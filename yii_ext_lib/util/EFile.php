<?php
/**
 * 文件处理相关
 * @author xiaopeng
 *
 */
class EFile{
	/**
	 * 远程下载文件到本地
	 * @param unknown_type $url
	 * @param unknown_type $file
	 * @param unknown_type $timeout
	 */
	public static function httpcopy($url, $file="", $timeout=60) {
	    $file = empty($file) ? pathinfo($url,PATHINFO_BASENAME) : $file;
	    $dir = pathinfo($file,PATHINFO_DIRNAME);
	    !is_dir($dir) && @mkdir($dir,0755,true);
	    $url = str_replace(" ","%20",$url);
	
	    if(function_exists('curl_init')) {
	        $ch = curl_init();
	        curl_setopt($ch, CURLOPT_URL, $url);
	        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	        $temp = curl_exec($ch);
	        if(@file_put_contents($file, $temp) && !curl_error($ch)) {
	            return $file;
	        } else {
	            return false;
	        }
	    } else {
	        $opts = array(
	            "http"=>array(
	            "method"=>"GET",
	            "header"=>"",
	            "timeout"=>$timeout)
	        );
	        $context = stream_context_create($opts);
	        if(@copy($url, $file, $context)) {
	            //$http_response_header
	            return $file;
	        } else {
	            return false;
	        }
	    }
	}
	
	/**
	 * 解压缩文件
	 * @param unknown_type $file
	 * @param unknown_type $destination
	 */
	public static function unzip_file($file, $destination){ 
		// create object 
		$zip = new ZipArchive() ; 
		// open archive 
		if ($zip->open($file) !== TRUE) { 
			return false;
//			die ('Could not open archive'); 
		} 
		// extract contents to destination directory 
		$return = $zip->extractTo($destination); 
//		for( $i = 0; $i < $zip->numFiles; $i++ ){ 
//		    $stat = $zip->statIndex( $i ); 
//		    print_r( basename( $stat['name'] ) . PHP_EOL ); 
//		}
		// close archive 
		$stat = $zip->statIndex(0);
		$zip->close();
		return basename( $stat['name'] ). PHP_EOL;
	}
	
	public static function read($filename){
		$fp = @fopen($filename, 'r');
		if($fp){
			$contents = fread($fp,1024*1024*2);
			fclose($fp);
			return $contents;
		}
		return false;
	}
}