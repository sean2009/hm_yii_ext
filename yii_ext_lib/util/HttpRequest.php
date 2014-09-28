<?php
/**
 * 
 * @author xiaopeng
 *
 */
class HttpRequest{
	
	/**
	 * 数据压缩方法 msgpack或json
	 * @var unknown_type
	 */
	public static $encode_type = 'json';
	
	/**
	 * 获取get或post或直接传入的引用处理
	 * @param unknown_type $data
	 * @return unknown
	 */
	public static function getRequest($data = NULL){
		if($data === NULL){
			if(isset($_REQUEST['response'])){
				$data = urldecode($_REQUEST['response']);
				$data = self::decodeUrl($data);
			}else{
				$data = $_REQUEST;
			}
		}else{
			$data = self::decodeUrl($data);
		}
		return $data;
	}
	
	/**
	 * 创建返回结果集
	 * @param unknown_type $code
	 * @param unknown_type $msg
	 * @param unknown_type $data
	 */
	public static function setReponse($code,$msg,$data = ''){
		$data = array(
			'code'	=> $code,
			'msg' => $msg,
			'response'	=> $data
		);
		if(self::$encode_type === 'msgpack'){
			$response = msgpack_serialize($data);
		}else{
			$response = json_encode($data);
		}
		if(isset(Yii::app()->request)){
			echo $response;
			exit;
		}else{
			return $response;
		}
	}
	
	
	/**
	 * 编码
	 * @param unknown_type $data
	 * @return string
	 */
	public static function encodeUrl($data){
		if(self::$encode_type === 'msgpack'){
			return msgpack_serialize($data);
		}else{
			return json_encode($data);
		}
	}
	
	/**
	 * 解码
	 * @param unknown_type $data
	 * @return mixed
	 */
	public static function decodeUrl($data){
		if(self::$encode_type === 'msgpack'){
			return msgpack_unserialize($data);
		}else{
			return json_decode($data,true);
		}
	}
	
	/**
	 * 创建表单进行提交
	 * @auther 秦家佳
	 * @param type $gateway
	 * @param type $prams_temp
	 * @param type $method
	 * @return string
	 */
	public static function request_by_form($gateway, $prams_temp, $method = 'post') {

		$sHtml = "<form id='formsubmit' name='formsubmit' action='" . $gateway . "' method='" . $method . "'>";
		while (list ( $key, $val ) = each($params)) {
				$sHtml .= "<input type='hidden' name='" . $key . "' value='" . $val . "'/>";
		}

		// submit按钮控件请不要含有name属性
		$sHtml = $sHtml . "<input type='submit' style='display:none;' value='" . $button_name . "'></form>";

		$sHtml = $sHtml . "<script type='text/javascript'>document.forms['formsubmit'].submit();</script>";

		return $sHtml;
	}
	
	/**
	 * 创建Cookie
	 * @param type $name
	 * @param type $value
	 * @param type $expire
	 */
	public static function setCookie($name,$value,$expire = COOKIE_TIME_OUT){
		$setCookie = new CHttpCookie($name, $value);
		$setCookie->expire = $expire;
		Yii::app()->request->cookies[$name]=$setCookie;
	}
	
	public static function getCookie($name){
		$cookie = Yii::app()->request->getCookies();
		return $cookie[$name]->value;
	}
}