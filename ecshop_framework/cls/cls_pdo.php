<?php
/**
 * pdo_mysql连接类
 * @author xiaopeng
 *
 */

class cls_pdo{
	const PARAM_PREFIX=':yp';
	public $connected = FALSE;
	protected $host,$port,$username,$password,$charset,$connectionString,$dbname;
	protected $persistent = 0;
	private $_active=false;
	private $_pdo,$_statement;
	private $_attributes=array();
	public $services;
	
	public function __construct(){
		
	}
	
	public function init(){
		$this->setService($this->services);
	}
	
	//对象序列化前调用,不接收任何参数，但会返回数组，这里可以放置哪些属性需要序列化
	public function __sleep()
	{
		$this->close();
		return array_keys(get_object_vars($this));
	}
	//析构函数是在对象销毁时调用的代码
	public function __destruct(){
		$this->close();
	}
	
	public function setService($config){
		foreach($config as $key => $val){
			$this->$key = $val;
		}
		$this->connectionString = "mysql:host={$this->host};dbname={$this->dbname};port={$this->port}";
	}
	
	//当前连接的状态
	public function getActive()
	{
		return $this->_active;
	}
	
	public function setActive($value)
	{
		if($value!=$this->_active)
		{
			if($value)
				$this->open();
			else
				$this->close();
		}
	}
	//执行sql
	public function query($sql,$params = array()){
		$this->setActive(true);
		try{
			$this->_statement = $this->_pdo->prepare($sql);
			$this->_statement->execute($params);
			return $this->_statement;
		}catch(Exception $e){
			$errorInfo = $e instanceof PDOException ? $e->errorInfo : null;
			$message = $e->getMessage();
			$this->show($message);
		}
	}
	
	public function getOne($sql,$params = array()){
		return $this->query($sql,$params)->fetchColumn();
	}
	
	public function getRow($sql,$params = array()){
		return $this->query($sql,$params)->fetch();
	}
	
	public function getList($sql,$params = array()){
		return $this->query($sql,$params)->fetchAll();
	}
	
	public function getLimitList($sql,$params = array(),$page_size = 10,$page = 1){
		$sql .= ' limit '.($page-1)*$page_size.','.$page_size;
		return $this->getList($sql,$params);
	}
	
	public function insert($table,$data){
		$fields=array();
		$values=array();
		$placeholders=array();
		$i=0;
		foreach($data as $name=>$value){
			$fields[] = '`'.$name.'`';
			$placeholders[] = self::PARAM_PREFIX.$i;
			$values[self::PARAM_PREFIX.$i] = $value;
			$i++;
		}
		$sql="INSERT INTO `{$table}` (".implode(', ',$fields).') VALUES ('.implode(', ',$placeholders).')';
		return $this->query($sql,$values)->rowCount();
	}
	
	public function update($table,$data,$condition,$params = array()){
		$fields=array();
		$values=array();
		$i=0;
		foreach($data as $name=>$value)
		{
			$fields[]='`'.$name.'`='.self::PARAM_PREFIX.$i;
			$values[self::PARAM_PREFIX.$i] = $value;
			$i++;
		}
		$sql="UPDATE `{$table}` SET ".implode(', ',$fields);
		$sql .= ' WHERE '.$condition;
		return $this->query($sql,array_merge($values,$params))->rowCount();
	}
	
	//影响行数
	public function getRowCount(){
		return $this->_statement->rowCount();
	}
	//查询最后一次插入的序列值
	public function getLastInsertID($sequenceName='')
	{
		$this->setActive(true);
		return $this->_pdo->lastInsertId($sequenceName);
	}
	
	public function setAttribute($name,$value)
	{
		if($this->_pdo instanceof PDO)
			$this->_pdo->setAttribute($name,$value);
		else
			$this->_attributes[$name]=$value;
	}
	
	public function getTimeout()
	{
		return $this->getAttribute(PDO::ATTR_TIMEOUT);
	}
	
//事务操作******************************************************************************	
	//开始事务
	public function beginTransaction()
	{
		$this->setActive(true);
		$this->_pdo->beginTransaction();
	}
	//提交事务
	public function commit(){
		if($this->_active){
			$this->_pdo->commit();
		}
	}
	//事务回滚
	public function rollback(){
		if($this->_active)
		{
			$this->_pdo->rollBack();
			$this->_active=false;
		}
		else{
			$this->show('rollback error');
		}
	}
	
	public function quoteValue($str)
	{
		if(is_int($str) || is_float($str))
			return $str;

		$this->setActive(true);
		if(($value=$this->_pdo->quote($str))!==false)
			return $value;
		else
			return "'" . addcslashes(str_replace("'", "''", $str), "\000\n\r\\\032") . "'";
	}
	
	/**
	 * 打开PDO连接
	 * @throws XDbException
	 */
	protected function open()
	{
		if($this->_pdo===null)
		{
			if(empty($this->connectionString))
				$this->show('connectionString cannot be empty.');
			try
			{
				$this->_pdo=$this->createPdoInstance();
				$this->_active=true;
				if($this->charset!==null){
					$this->_pdo->exec('set names '.$this->charset);
				}
			}
			catch(PDOException $e)
			{
				$errorInfo = $e instanceof PDOException ? $e->errorInfo : null;
				$message = $e->getMessage();
				$this->show($message);
			}
		}
	}
	//实例化pdo连接对象
	protected function createPdoInstance(){
		return new PDO($this->connectionString,$this->username,
									$this->password,array(
				PDO::ATTR_PERSISTENT => $this->persistent,
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
				PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
				PDO::ATTR_CASE => PDO::CASE_LOWER,
				PDO::MYSQL_ATTR_USE_BUFFERED_QUERY =>TRUE,
				PDO::ATTR_AUTOCOMMIT => TRUE
			));
	}
	
	/**
	 * 关闭连接
	 */
	protected function close()
	{
		$this->_pdo=null;
		$this->_active=false;
		$this->_schema=null;
	}
	//打印错误
	protected function show($message){
		if(DEBUG_MODE){
			echo $message;exit;
		}
	}
}