<?php

if (!defined('YII_IS_MONITORING')) {
    define('YII_IS_MONITORING', DEV_ENVIRONMENT == 'dev' || substr(PHP_SAPI, 0, 3) == 'cli' ? false : true);
}
defined('YII_IS_CLIENT') or define('YII_IS_CLIENT', true);

if (!function_exists('fastcgi_finish_request')) {

    function fastcgi_finish_request() {
        
    }

}

require dirname(__FILE__) . '/SQSBaseService.php';

/**
 * 日记记录服务
 */
class MonitoringService extends SQSBaseService {
    
    
    
    const QUEUE_NAME_LOG = 'monitoring_log1';
    const LOG_TYPE_PAGE = 1;
    const LOG_TYPE_SERVICE_SERVER = 2;
    const LOG_TYPE_MEMORY = 3;
    const LOG_TYPE_DB = 4;
    const LOG_TYPE_MEMCACHE = 5;
    const LOG_TYPE_SERVICE_CLIENT = 6;
    const LOG_TYPE_EXCEPTION = 7;
    const LOG_TYPE_SERVICE_EXCEPTION = 8;
    const LOG_TYPE_MONGO = 9;

    private static $instance;

    private $requestId;
    private $urlId;
    private $startTime;
    private $_logs = array();
    // 当前要监控的页面的类型，可以是网页类型的，也可以是服务类型
    private $pageType;
    // 开始内存使用量
    private $startMemory;

    /**
     * 
     * @return MonitoringService
     */
    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    /*
     * 监控队列使用不同的配置
     */
    public function getConfig(){
       $sqsConfigPro = array(
           'host' => '10.0.1.35',
           'port' => 1218,
           'auth' => 'httpsqsmmall.com'
       );
       if (isset($_SERVER['APP_ENV']) && $_SERVER['APP_ENV'] == 'pro') {
           return $sqsConfigPro;
       }
       return null;
    }

    private function __construct() {
        $this->urlId = uniqid(hash('ripemd128', $_SERVER['SCRIPT_FILENAME']), true);
        $this->startMemory = memory_get_usage();

        if (YII_IS_MONITORING) {
            if (defined('DEV_ENVIRONMENT') && !YII_IS_CLIENT) {
                define('YII_ENABLE_EXCEPTION_HANDLER', false);
                set_exception_handler(array($this, 'catchException'));
            }
            define('YII_ENABLE_ERROR_HANDLER', false);
            set_error_handler(array($this, 'catchError'));
        }

        $this->startTime = self::getMillisecond();
        $this->pageType = YII_IS_CLIENT ? self::LOG_TYPE_PAGE : self::LOG_TYPE_SERVICE_SERVER;
        $this->requestId = $this->pageType == self::LOG_TYPE_PAGE ? $this->urlId : $_GET['monitoring_request_id'];
        
        header("M_______________ID: {$this->requestId}");

        $this->log($this->pageType, array(
            'content' => $this->getFullUrl(),
            'type' => 'start',
            'hash' => md5($this->getFullUrl()),
            'server_addr' => $_SERVER['SERVER_ADDR']
        ));
    }

    public function end() {
        $this->log(self::LOG_TYPE_MEMORY, array('normal' => memory_get_usage() - $this->startMemory));
        $this->log(self::LOG_TYPE_MEMORY, array('peak' => memory_get_peak_usage() - $this->startMemory));

        $this->log($this->pageType, array(
            'et' => self::diffMillisecond(self::getMillisecond(), $this->startTime),
            'type' => 'end',
            'hash' => md5($this->getFullUrl())
            )
        );

        fastcgi_finish_request();

        if (YII_IS_MONITORING) {
            $config = $this->getConfig();
            $httpsqs = self::getSQSConnection($config);

            if (!empty($this->_logs)) {
                $data = array();
                foreach ($this->_logs as $group) {
                    foreach ($group as $v) {
                        $data[] = $v;
                    }
                }
                $httpsqs->put(self::QUEUE_NAME_LOG, json_encode($data));
            }
        }
    }

    /**
     * 
     * @param int $type 日志类型，可选值是LOG_TYPE_*的类常量
     * @param array $data 数据
     */
    public function log($type, array $data) {
        if (YII_IS_MONITORING) {
            $this->_logs[$type][] = array_merge(array(
                'request_id' => $this->requestId,
                'url_id' => $this->urlId,
                'dateline' => self::getMillisecond(),
                'monitor_type' => &$type,
                'sequence' => $this->getSequence()
                ), $data);
        }
    }

    /**
     * 捕获异常信息
     * 
     * @params mix $exception Exception or array
     */
    public function catchException($exception) {
        if ($exception instanceof Exception) {
            $data = array(
                'class' => get_class($exception),
                'file' => $exception->getFile(),
                'code' => $exception->getCode(),
                'content' => $exception->getMessage(),
                'line' => $exception->getLine()
            );
        } else {
            $data = $exception;
        }
        $this->log(self::LOG_TYPE_EXCEPTION, $data);
    }

    public function catchError($errno, $errstr, $errfile, $errline, $errcontent) {
        if($errno <= 1){
            $this->log(self::LOG_TYPE_EXCEPTION, array(
                'file' => $errfile,
                'content' => $errstr,
                'line' => $errline,
                'level' => $errno,
            ));
        }
    }

    private function getFullUrl() {
        return $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }

    private function getSequence() {
        static $i = 0;
        $i += 1;

        return $i;
    }

    private function __clone() {
        
    }

    public function getRequestId() {
        return $this->requestId;
    }

    public function getUrlId() {
        return $this->urlId;
    }

    /**
     * 设置当前的页面类型
     * 
     * @param int $type
     */
    public function setPageType($type) {
        $this->pageType = $type;
    }

    /*
     * 获取精确到毫秒的时间
     */

    public static function getMillisecond() {
        list($s1, $s2) = explode(' ', microtime());
        return (float) sprintf('%.0f', (floatval($s1) + floatval($s2)) * 1000);
    }

    /*
     * 获取时间差
     */

    public static function diffMillisecond($t1, $t2) {
        return (float) $t1 - (float) $t2;
    }

}
