<?php

require_once dirname(__FILE__) . '/API_Base.php';

use yii_ext_lib\yar\YarException;

class API_Client {

    private static $isAsync = false;
    private static $tasks = array();
    private static $exception;

    public static function async($isAsync) {
        self::$isAsync = $isAsync;
        self::$tasks = array();
    }

    /**
     * 调用一个API，当给定了回调函数，则api掉完后会调用回调函数
     * 当没有指定回调函数时，会返回调用的结果
     * 
     * @param string $url API地址
     * @param string $apiName 要调用的API名称，以controller/action的形式
     * @param array $params 参数
     * 
     * @return mix 如果是异步的不会有返回的内容
     */
    public static function call($url, $apiName, $params = '') {
        $t1 = MonitoringService::getMillisecond();
        $monitor = MonitoringService::getInstance();
        $sequence = uniqid();

        $monitor->log(MonitoringService::LOG_TYPE_SERVICE_CLIENT, array(
            'type' => 'start',
            'content' => json_encode($params),
            'url' => $url,
            'api_name' => $apiName,
        ));

        $params = array($apiName, array($params));
        $return = self::$exception = null;
        $apiUrl = $url . (strpos($url, '?') === false ? '?' : '&') . 'monitoring_request_id=' . urlencode($monitor->getUrlId()) . "&sequence=$sequence";

        if (class_exists('Yar_Client', false)) {
            if (self::$isAsync) {
                Yar_Concurrent_Client::call($apiUrl, "request", $params);
            } else {
                try {
                    $client = new Yar_Client($apiUrl);
                    $return = $client->request($params);
                } catch (Exception $ex) {
                    self::$exception = new yii_ext_lib\yar\YarException($ex->getMessage(), $ex->getCode());
                }
            }
        } else {
            try {
                if (!self::$isAsync) {
                    $ch = curl_init($apiUrl);
                    curl_setopt_array($ch, array(
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_HEADER => false,
                        CURLOPT_CONNECTTIMEOUT => 1,
                        CURLOPT_POST => true,
                        CURLOPT_POSTFIELDS => json_encode($params)
                    ));
                    
                    $rs = curl_exec($ch);
                    $curlInfo = curl_getinfo($ch);
                    
                    if ($rs === false) {
                        self::$exception = new yii_ext_lib\yar\YarException(curl_error($ch), curl_errno($ch));
                    } elseif ($curlInfo['http_code'] != 200) {
                        self::$exception = new yii_ext_lib\yar\YarException($rs, $curlInfo['http_code']);
                    }
                    curl_close($ch);
                    $return = json_decode($rs, true);
                } else {
                    self::$tasks[] = $ch;
                }
            } catch (Exception $ex) {
                self::$exception = new yii_ext_lib\yar\YarException($ex->getMessage(), $ex->getCode());
            }
        }
        if ($return) {
            self::$exception = unserialize($return['exception']);
            if (self::$exception != null) {
                $monitor->log(MonitoringService::LOG_TYPE_SERVICE_EXCEPTION, array(
                    'class' => get_class(self::$exception),
                    'file' => self::$exception->getFile(),
                    'code' => self::$exception->getCode(),
                    'content' => self::$exception->getMessage(),
                    'line' => self::$exception->getLine()
                ));
            }

            $return = $return['return'];
        }

        $apiType = self::$exception ? 'error' : 'end';
        $monitor->log(MonitoringService::LOG_TYPE_SERVICE_CLIENT, array(
            'type' => $apiType,
            'url' => $url,
            'api_name' => $apiName,
            'et' => MonitoringService::getMillisecond() - $t1,
        ));

        return $return;
    }

    /**
     * 执行批处理的请求
     * 参数：
     * $retval => 返回的内容
     * $callInfo => 当等于null的时候说明所有的请求都结束了
     * function callback($retval, $callInfo) {}
     * 
     * @param function $callback 每次执行成功后的回调函数
     * @param function $errorCallback 执行失败的回调函数，主要是指返回的http状态码不是200的情况
     */
    public static function loop($callback = null, $errorCallback = null) {
        if (self::$isAsync) {
            if (class_exists('Yar_Client', false)) {
                Yar_Concurrent_Client::loop($callback, $errorCallback);
            } else {
                $mh = curl_multi_init();
                foreach (self::$tasks as $ch) {
                    curl_multi_add_handle($mh, $ch);
                }
                $still_running = false;
                $previousActive = -1;
                do {
                    $cur = 0;
                    curl_multi_exec($mh, $still_running);
                    if ($still_running != $previousActive) {
                        $cur++;
                        $info = curl_multi_info_read($mh);
                        if ($info['handle']) {
                            if ($callback) {
                                $callInfo = $cur == count(self::$tasks) ? new stdClass() : null;

                                call_user_func($callback, json_decode(curl_multi_getcontent($info['handle']), true), $callInfo);
                            }
                            curl_multi_remove_handle($mh, $info['handle']);
                            curl_close($info['handle']);
                        }
                    }
                } while ($still_running);
                curl_multi_close($mh);
            }
        }
        self::$tasks = array();
        self::$isAsync = false;
    }

    /**
     * 返回最后一个异常信息，如果没有出错，则返回null
     * 这个方法只有当服务端打开了YII_DEBUG才回有值
     * 
     * @return yii_ext_lib\yar\YarException
     */
    public static function getLastException() {
        return self::$exception;
    }

}
