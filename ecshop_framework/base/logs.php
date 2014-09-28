<?php
/**
 * 日志类
 * @author wander
 *
 */
class logs{
	public $START_TIME;
	public $cache;
	public function __construct(){
		global $cache;
		$this->cache=$cache;
		$this->START_TIME = $this->get_microtime();
		register_shutdown_function(array($this, 'log'));
	}
	/**
	 * 获取当前时间（微秒级）
	 */
	public function get_microtime()
    { 
        list($usec, $sec) = explode(' ', microtime()); 
        return ((float)$usec + (float)$sec); 
    }
    /**
     * 计算程序执行时间(毫秒级)
     */
    public function speed() 
    { 
    	$STOP_TIME = $this->get_microtime();
        return round(($STOP_TIME - $this->START_TIME) * 1000, 1); 
    }
	/**
     * 写日志
     */
	public function log() {
        // $path = ROOT_PATH. DIRECTORY_SEPARATOR . 'log' . DIRECTORY_SEPARATOR;
        //线上要改为下面的
        $path = '/data/log/sql/';
        $time = $this->speed();
        if (!file_exists($path)) {
            @mkdir($path);
            //chmod($path, 0777);
        }
        if (!is_writable($path)){
        	return;
        }
        $url = $this->getLocationURL();
        $filename = $path . 'zx_front_' . date('YmdH') . '.log';
        $date = date('Y-m-d H:i:s');
        global $QUERY_SQL;
        $msg = '';
        
	 
        $cache_sql=$this->cache->fetch("internal_page_view_sql",false);
        if(empty($cache_sql)){
        	$cache_sql=array();
        }
        if(count($cache_sql)>=500){
        	$cache_sql=array();
        }
        
        $sessid = SESS_ID;
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        
		global $auth_view_sql_userids;// config配置文件中
		if(!empty($_SESSION['user_id']) && !empty($auth_view_sql_userids) && in_array($_SESSION['user_id'], $auth_view_sql_userids)){
	        if(count($QUERY_SQL)>0){
	        	$view_url=$this->getViewSqlURL();
	        	if($view_url!="/index.php?con=shop&act=sql" && $view_url!="/index.php?con=shop&act=clear"){
		        	$cache_sql[]=$url;
		        	foreach ($QUERY_SQL as $sql){
		        		$sqlstr = str_replace("\r\n","",$sql[1]);
		        		$cache_sql[]=$sqlstr;
		        	}
		        	if(count($QUERY_SQL)>0){//头部ajax请求总会有两条sql
		        		$this->cache->save("internal_page_view_sql",$cache_sql,MEM_DEFAULT_TIMEOUT,false);
		        	}
		        	unset($cache_sql);
	        	}
	        }
		}
        
        if(count($QUERY_SQL)>0){
        	foreach ($QUERY_SQL as $sql){
        		$sqlstr = str_replace("\r\n","",$sql[1]);
        		$msg .= "{sessionid:$sessid,date:$date,sql:\"{$sqlstr}\",sqlspeed:{$sql[0]},url:\"$url\",pagespeed:$time,IP:\"$ip\"},\r\n";
        	}
        }
        else 
        {
        	$msg .= '{sessionid:'.SESS_ID.',date:"'.$date.'",sql:"",sqlspeed:0,url:"'.$url.'",pagespeed:'.$time.",IP:\"$ip\"},\r\n";
        }
        file_put_contents($filename,$msg, FILE_APPEND);
        $QUERY_SQL = array();
        return true;
    }
	/**
     * 获取当前请求的URL地址
     */
    public function getLocationURL(){
    	$query_string = strlen($_SERVER['QUERY_STRING'])>0?'?'.$_SERVER['QUERY_STRING']:'';
    	return 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].$query_string;
    }
    
 
    public function getViewSqlURL(){
    	$query_string = strlen($_SERVER['QUERY_STRING'])>0?'?'.$_SERVER['QUERY_STRING']:'';
    	return $_SERVER['PHP_SELF'].$query_string;
    }
}