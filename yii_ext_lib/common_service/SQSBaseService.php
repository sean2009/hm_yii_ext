<?php

require_once dirname(__FILE__) . '/thirdparty/Httpsqs_Client.php';

class SQSBaseService {

    /**
     *
     * @var array 测试环境的配置
     */
    public static $sqsConfigDev = array(
        'host' => '192.168.0.218',
        'port' => 1218,
        'auth' => 'httpsqsmmall.com'
    );

    /**
     *
     * @var array 线上环境的配置
     */
    public static $sqsConfigPro = array(
        'host' => '10.0.2.51',
        'port' => 1218,
        'auth' => 'httpsqsmmall.com'
    );

    /**
     * 取得一个sqs的连接
     * 
     * @param array $config
     * @return Httpsqs_Client
     */
    protected static function getSQSConnection($config = null) {
        if (!$config) {
            if ((defined('DEV_ENVIRONMENT') && DEV_ENVIRONMENT == 'pro')) {
                $config = self::$sqsConfigPro;
            } else {
                $config = self::$sqsConfigDev;
            }
        }

        $sqsClient = new Httpsqs_Client($config['host'], $config['port'], $config['auth']);

        return $sqsClient;
    }

}
