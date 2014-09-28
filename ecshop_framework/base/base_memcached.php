<?php
/**
 * Memcache的封闭类
 */
class base_memcached extends Memcache {
	public $servers;
	/*
	 * 构造函数
	 */
	public function __construct() {
		
	}
	
	public function init(){
		foreach ($this->servers as $item)
		{
			$this -> addServer($item['host'], $item['port'], 1);
		}
	}
	
	/**
	 * 析构函数
	 */
	function __destruct()
	{
		$this->close();
	}
	
	/**
	 * 设置'key'对应存储的值
	 * @param string $key
	 * @param object $value
	 * @param int $time
	 */
	public function save($key,$value,$time=MEM_DEFAULT_TIMEOUT,$include_root = true)
	{
		if(MEM_OPEN)
		{
			if($include_root)
				return parent::set(MEM_ROOT.':'.$key,json_encode($value, JSON_NUMERIC_CHECK),0,$time);
			else
				return parent::set($key,json_encode($value, JSON_NUMERIC_CHECK),0,$time);
		}
		return FALSE;
	}
	/**
	 * 获取memcache的值
	 * @param string $key
	 */
	public function fetch($key,$include_root = true)
	{
		if(MEM_OPEN)
		{
			if($include_root)
				$res = parent::get(MEM_ROOT.':'.$key);
			else
				$res = parent::get($key);
			if (FALSE !== $res)
			{
				return json_decode($res, 1);
			}
			return FALSE;
		}
		return FALSE;
	}
}
?>