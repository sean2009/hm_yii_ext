<?php

/**
 * HTTP请求类
 * @author xiaopeng <xp_go@qq.com>
 * @version 2.0 2012-04-20
 */
class HttpCurl
{
	/**
	 * 发送GET请求
	 * @param string $url	请求地址
	 * @param array $params	请求参数
	 * @return array
	 */
	public static function get($url , $params = array()){
		$params['t'] = time();
		$params['sign'] = self::getSign($params);
		$data = HttpRequest::encodeUrl($params);
		$return = self::request($url, array('response'=>$data), 'GET');
		return json_decode($return,true);
	}
	
	/**
	 * 发送POST请求
	 * @param string $url	请求地址
	 * @param array $params	请求参数
	 * @return array
	 */
	public static function post($url , $params = array()){
		$params['t'] = time();
		$params['sign'] = self::getSign($params);
		$data = HttpRequest::encodeUrl($params);
		$return = self::request($url, array('response'=>$data), 'POST');
		return json_decode($return,true);
	}
	
	
	
	/**
	 * 服务器端验证签名
	 */
	public static function signValidation($request){
		$token = self::getSign($request);
		if(empty($request['sign']) || $request['sign'] != $token){
			return false;
		}
		if(time() - $request['t'] > 3){
			return false;
		}
		return true;
	}
	
	/**
	 * 客户端根据参数生成验证签名
	 * @param array $params
	 * @return string
	 */
	public static function getSign($params = array()){
		if(isset($params['sign'])){
			unset($params['sign']);
		}
		$webservice_token = Yii::app()->params['webservice_token'];
		$params['webservice_token'] = $webservice_token;
		ksort($params);
		$params = http_build_query ( $params );
		return md5($params);
	}
	
    /**
     * 发起一个HTTP/HTTPS的请求
     * @param $url 接口的URL 
     * @param $params 接口参数   array('content'=>'test', 'format'=>'json');
     * @param $method 请求类型    GET|POST
     * @param $multi 图片信息
     * @param $extheaders 扩展的包头信息
     * @return string
     */
    public static function request( $url , $params = array(), $method = 'GET' , $multi = false, $extheaders = array())
    {
        if(!function_exists('curl_init')) exit('Need to open the curl extension');
        $method = strtoupper($method);
        $ci = curl_init();
        curl_setopt($ci, CURLOPT_USERAGENT, 'PHP-SDK OAuth2.0');
        curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, 3);
        curl_setopt($ci, CURLOPT_TIMEOUT, 3);
        curl_setopt($ci, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ci, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ci, CURLOPT_HEADER, false);
        $headers = (array)$extheaders;
        switch ($method)
        {
            case 'POST':
                curl_setopt($ci, CURLOPT_POST, TRUE);
                if (!empty($params))
                {
                    if($multi)
                    {
                        foreach($multi as $key => $file)
                        {
                            $params[$key] = '@' . $file;
                        }
                        curl_setopt($ci, CURLOPT_POSTFIELDS, $params);
                        $headers[] = 'Expect: ';
                    }
                    else
                    {
                        curl_setopt($ci, CURLOPT_POSTFIELDS, http_build_query($params));
                    }
                }
                break;
            case 'DELETE':
            case 'GET':
                $method == 'DELETE' && curl_setopt($ci, CURLOPT_CUSTOMREQUEST, 'DELETE');
                if (!empty($params))
                {
                    $url = $url . (strpos($url, '?') ? '&' : '?')
                        . (is_array($params) ? http_build_query($params) : $params);
                }
                break;
        }
//		echo $url;
        curl_setopt($ci, CURLINFO_HEADER_OUT, TRUE );
        curl_setopt($ci, CURLOPT_URL, $url);
        if($headers)
        {
            curl_setopt($ci, CURLOPT_HTTPHEADER, $headers );
        }

        $response = curl_exec($ci);
    	if (curl_errno ( $ci )) {
			print_r(curl_error($ci));die;
			throw new CHttpException(500,curl_error($ci));
		}
        curl_close ($ci);
        return $response;
    }
}