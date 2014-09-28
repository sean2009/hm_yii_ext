<?php

/**
 * 扩展控制器基类
 * @author Xiaopeng <xp_go@qq.com> 2013-7-25
 * @link http://www.mmall.com
 * @copyright Copyright &copy; 2003-2011 mmall.com
 * @license
 */
class BaseController extends CController {
	public $layout = 'main';
	public $pageTitle = '';
	public $pageKeywords = '';
	public $pageDescription = '';
	/**
	 * 面包屑
	 * @var array
	 */
	public $breadcrumbs = array();
	
	public function __construct($id,$module){
		parent::__construct($id,$module);
	}
    
    public function beforeAction($action) {
        $errorAction = Yii::app()->errorHandler->errorAction;
        if ($errorAction == $this->getId() . '/' . $this->getAction()->getId()) {
            $error = Yii::app()->errorHandler->error;
            
            MonitoringService::getInstance()->catchException(array(
                'class' => $error['type'],
                'file' => $error['file'],
                'code' => $error['errorCode'],
                'content' => $error['message'],
                'line' => $error['line']
            ));
        }
        
        return parent::beforeAction($action);
    }
    
	/**
	 * 重写 CController::actions() 并且注册一个继承于 CCaptchaAction 类ID为 'captcha' 的方法.
	 * 并配置其他相关属性
	 * @see CController::actions()
	 */
	public function actions() {
		return array (	//验证码
			'captcha' => array (//加载外部的action class
				'class' => 'CCaptchaAction', //设置验证码图片背景色属性
				'backColor' => 0xffffff, 'maxLength' => 5 )
		);
	}
    
    public function responseJSON($data) {
        header("Content-Type: text/json");
        echo json_encode($data);
        Yii::app()->end();
    }
	
	/**
	 * 
	 * 返回字符串错误信息。
	 * @param CModel $model
	 */
	public function getErrorHtml($model){
		if(!$model->hasErrors()){
			return true;
		}
		$html = array();
		$errors = $model->getErrors();
		foreach($errors as $val){
			$html[] = $val[0];
		}
		return implode('
',$html);
	}
}
