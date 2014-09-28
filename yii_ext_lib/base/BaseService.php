<?php
/**
 * Enter description here ...
 *
 * the last known user to change this file in the repository  <$LastChangedBy: libra $>
 * @author 
 * @version $Id: BaseService.php
 * @package 
 */
class BaseService {
	
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
	 * 取得缓存对象
	 * @return CCache
	 */
	public function getCacheObj(){
		return Yii::app()->cache;
	}
	
	/**
	 * 将AR集合对象转换成数组格式
	 * @param array $arCollection
	 * @return array
	 */
	public function arToArray(array $arCollection){
		$_array = array();
		foreach($arCollection as $ar){
			$_array[] = $ar->attributes;
		}
		return $_array;
	}
	
	/**
	 * 按指定数组中已经存在的字段名做为索引（key）重建数组
	 * @param array $data    数组
	 * @param string $resultIndex    指定的字段
	 * @param string $returnData     返回的字段
	 * @author su qian
	 * @return array   返回重建后的数组
	 */
	public function buildDataByIndex(array $data, $resultIndex, $returnData = null) {
		if (empty($resultIndex)) return $data;
		$_data = array();
		foreach ($data as $key => $value) {
			if (!isset($_data[$value[$resultIndex]]))
				$_data[$value[$resultIndex]] = $returnData ? $value[$returnData] : $value;
			else {
				$_tmp = $_data[$value[$resultIndex]];
				$_data[$value[$resultIndex]] = (!is_array($_tmp) || $_tmp[$resultIndex]) ? array($_tmp) : $_tmp;
				array_push($_data[$value[$resultIndex]], $returnData ? $value[$returnData] : $value);
			}
		}
		return $_data;
	}


}

