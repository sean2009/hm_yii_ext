<?php
/**
 * 时间统计类
 * @package default
 * @author	Jonah.Fu
 * @date	2011-11-21
 */
class base_timestatistcs {
    private $timer;
    //初始函数
    function __construct() {
        $this -> timer = array();
    }

    //析构函数
    function __destruct() {
        unset($this -> timer);
    }

    //记录开始
    public function start($timerName = "start") {
        $this -> pushTime($timerName);
    }

    //记录结束
    public function stop($timerName = "stop") {
        $this -> pushTime($timerName);
    }

    //添加标记
    public function setMarker($timerName) {
        $this -> pushTime($timerName);
    }

    //显示数据
    public function display($type = "string", $filePath = "", $fileTopStr = "") {
        $retrun = NULL;
        switch($type) {
            case "file" :
                if (defined('TIMER_LOG')) {
                    $arr = explode("/", $_SERVER['PHP_SELF']);
                    $filePath = TIMER_LOG . "/" . $arr[count($arr) - 1] . ".timer.log";
                    $fileTopStr = $arr[count($arr)-1] . " LogTime:" . date("Y-m-d H:i:s", time());
                    $this -> saveFiles($this -> displayStr(), $filePath, $fileTopStr);
                }
                break;
            default :
                echo str_replace("\r\n", "<br />", $this -> displayStr());
        }
    }

    //时间记录
    private function pushTime($timerName) {
        $temp = $this -> timer;
        $temp[$timerName] = $this -> getTime();
        $this -> timer = $temp;
    }

    //添加时间数据
    private function getTime() {
        $return = NULL;
        $return = explode(" ", microtime(0));
        $return = $return[1] . substr($return[0], 1, 9);
        return $return;
    }

    //处理输出
    private function displayStr() {
        $str = "";
        $i = 0;
        $upItem = NULL;
        $tempName = array_keys($this -> timer);
        $allTime = $this -> timer[$tempName[count($tempName) - 1]] - $this -> timer[$tempName[0]];
        foreach ($this->timer as $k => $v) {
            $str .= "--" . $k . "--" . $v . "--";
            switch($i) {
                case 0 :
                    $str .= "0.000000--0.00%--\r\n";
                    $startItem = $v;
                    break;
                default :
                    $tempTime = number_format(($this -> timer[$k] * 1 - $upItem * 1), 6);
                    $tempPercent = number_format(($tempTime / $allTime), 2) * 100;
                    $str .= "$tempTime--$tempPercent%--\r\n";
            }

            $upItem = $this -> timer[$k];
            $i++;
        }
        $allTime = number_format($allTime, 6);
        $str .= "--total--0000000000.00000000--$allTime--100%--\r\n";
        return $str;
    }

    //保存到文件
    private function saveFiles($str, $filePath, $fileTopStr = "") {
        // if (!file_exists($filePath))
            // file_put_contents($filePath,'');
        $fp = fopen($filePath, "a");
        flock($fp, LOCK_EX);
        if (!$fileTopStr == "") {
            fwrite($fp, $fileTopStr . "\r\n");
        }
        fwrite($fp, $str);
        flock($fp, LOCK_UN);
        fclose($fp);
    }

}
?>