<?php
/**
 * Enter description here ...
 *
 * the last known user to change this file in the repository  <$LastChangedBy: libra $>
 * @author 
 * @version $Id: BaseService.php
 * @package 
 */
class BaseWebService {
	
	/**
	 * @var array 记录错误信息
	 */
	protected $errors = array();
	public static $_models = array();
	/**
	 * 单例模式
	 * @param unknown_type $className
	 * @return obj
	 */
	public static function model($className=__CLASS__)
	{
		if(isset(self::$_models[$className]))
			return self::$_models[$className];
		else
		{
			$model=self::$_models[$className]=new $className(null);
			return $model;
		}
	}
	
	/**
	 * 设置错误信息,并返回相应的错误类型状态，一般为false,0,'',null,array()
	 * @param string $error
	 * @return mixed
	 */
	public function setError($error,$returnValue = false){
		if(!empty($error)){
			if(is_array($error)){
				foreach ($error as $key=>$_error){
					is_int($key) ? $this->errors[] = $_error : $this->errors[$key] = $_error;
				}
			}else{
				$this->errors[] = $error;
			}
		}
		return $returnValue;
	}
	
	/**
	 * 取得单条错误信息
	 * @return string
	 */
	public function getError(){
		if($this->errors)
			return array_pop($this->errors);
	}
	
	/**
	 * 获取所有错误类型
	 * @return array
	 */
	public function getErrors(){
		return $this->errors;
	}
	
	/**
	 * 是否有错误
	 * @return boolean
	 */
	public function hasErrors(){
		if(empty($this->errors))
			return false;
		else
			return true;
	}
	
	/**
	 * WebService返回数据处理
	 * @param type $return
	 * @return boolean
	 */
	public function valiCode($return){
		if($return['code'] < 0){
			$this->setError($return['msg']);
			return false;
		}
		return $return['response'];
	}

}

