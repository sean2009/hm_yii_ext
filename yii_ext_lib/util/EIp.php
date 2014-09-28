<?php
/**
 * 和IP地址相关
 * @author xiaopeng
 *
 */
class EIp{
	/**
	 * Enter description here ...
	 * @return Ambigous <unknown, multitype:>
	 */
	public static function getIp(){
		if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            list($ip,) = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        } elseif (isset($_SERVER['HTTP_CLIENT_IP']) && !empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
	}
	
	/**
	 * Enter description here ...
	 * @param unknown_type $ip
	 * @return string
	 */
	public static function getIpAddress($ip = ''){
		$ip = !empty($ip) ? $ip : self::getIp();
		header("REMOTE_ADDR: {$_SERVER['REMOTE_ADDR']}");
        header("REAL_ADDR: $ip");
        header("FORWARDED_FOR: {$_SERVER['HTTP_X_FORWARDED_FOR']}");
		$addr = '';
		if(!extension_loaded('qqwry')){//直接用php查询QQWry数据
			throw new CHttpException(500,'qqwry扩展不正常');
		}
		else//加载qqwry的PHP扩展来查询
		{
			$filename =realpath(ROOT_PATH . '/data/qqwry.dat');
			$qqwry=new qqwry($filename);
			list($addr1,$addr2)=$qqwry->q($ip);
			$addr=iconv('GB2312','UTF-8',$addr1);
		}
		return $addr;
	}
}