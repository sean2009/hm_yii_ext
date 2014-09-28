<?php

require_once dirname(__FILE__) . '/SQSBaseService.php';

/**
 * 日记记录服务
 */
class LogService extends SQSBaseService {

    const QUEUE_NAME_LOG = 'logtest';
    
    private static $logName = '';
    
    /**
     * 
     * @param string $logName 设置日志名
     */
    public static function setLogName($logName) {
        self::$logName = $logName;
    }

    /**
     * 记录日志到服务器上
     * 
     * @param mix $data 日志的内容，如果是数组类型，则数组的键就是mongodb的列名，值就是相应列对应的值
     * @param string $logName 日志名字，对应到mongodb的文档名，必须是拉丁字母和数字下划线
     */
    public static function log($data, $logName = '') {
        $conn = self::getSQSConnection();
        $logName = $logName ? $logName : self::$logName;
        
        if (!$logName || preg_match('/^[A-Z0-9_\-]+$/', $logName)) {
            throw new Exception("请指定日志名，日志名将做为mongodb的文档名");
        }
        
        $data = array(
            'data' => is_array($data) ? $data : array('msg' => $data),
            'table' => $logName
        );
        
        return $conn->put(self::QUEUE_NAME_LOG, json_encode($data));
    }

}
