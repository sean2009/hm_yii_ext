<?php

/**
 * 记录日志类
 *
 * @author wander
 * @version 1.0
 **/
class cls_logger {
    /**
     * @var string 日志文件后缀名
     **/
    private static $LOG_EXTENSION = array(
        'notice' => '.log',
        'warning' => '.warn',
        'error' => '.err',
    );

    /**
     * 向文件中记录日志
     *
     * @param string 日志类型
     * @param string 日志消息
     * @param string 文件名
     * @param string 文件行号
     * @param string 日志文件名
     **/
    public static function log($type, $msg, $file, $line) {
        $path = ROOT_PATH. DIRECTORY_SEPARATOR . 'log' . DIRECTORY_SEPARATOR;
        if (!file_exists($path)) {
            @mkdir($path);
        }
        chmod($path, 0777);
        if (!is_writable($path))
            exit('LOG_PATH is not writeable !');
        //		if (!(defined('DEBUG') && DEBUG) && $type != 'error')
        //		{
        //			return;
        //		}
        $arrLogLevels = array_keys(self::$LOG_EXTENSION);
        if (!in_array($type, $arrLogLevels)) {
            $type = 'error';
        }
        $filename = $path . date('YmdH') . self::$LOG_EXTENSION[$type];
        $date = date('Y-m-d H:i:s');
        file_put_contents($filename, "[$date] [$msg] [$file:$line]\r\n", FILE_APPEND);
    }

}

function testCLogger() {
    cls_logger::log('error', 'error test', __FILE__, __LINE__);
    cls_logger::log('notice', 'notice test', __FILE__, __LINE__);
    cls_logger::log('unknown', 'unknown test', __FILE__, __LINE__);
}

//testCLogger();
?>