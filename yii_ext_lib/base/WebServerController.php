<?php
/**
 * WebServer服务端Controller基类
 */
class WebServerController extends CController{
	public $serverParams = array();
	public function init(){
		$this->serverParams = HttpRequest::getRequest();
		if(HttpCurl::signValidation($this->serverParams) === false){
			HttpRequest::setReponse(-1, '签名错误或已过期');
		}
	}
}