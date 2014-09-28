<?php
/*
 * ORACLE数据库操作类
 */
class cls_oracle {
	public $Debug = 0;
	public $sqoe = 1;
	// sqoe= show query on error

	public $DBDatabase = "";
	public $DBUser = "";
	public $DBPassword = "";
	public $Persistent = false;
	public $Uppercase = false;

	public $Record = array();
	public $Row;

	public $Binds = array();

	public $Link_ID = 0;
	public $Query_ID = 0;
	public $Connected = false;

	public $Encoding = "UTF8";

	public $Error = "";

	var $queryCount = 0;
	var $queryTime = '';
	var $queryLog = array();

	/**
	 * 是否开启事务
	 * @var boolen 默认是不开启
	 */
	public $Transaction = FALSE;
	
	public function __construct()
	{
		
	}
	
	public function init(){
		
	}
	
	/**
	 * 执行事务(必须Transaction==TRUE时才有效)
	 */
	function commit() {
		if ($this -> Transaction) {
			$committed = oci_commit($this -> Link_ID);
			$this -> Transaction = FALSE;
			// Test whether commit was successful. If error occurred, return error message
			if (!$committed) {
				oci_rollback($this -> Link_ID);
				$this -> Error = oci_error($this -> Query_ID);
				if ($this -> Error["code"] != 1403 && $this -> Error["code"] != 0 && $this -> sqoe) {
					$this->show("<BR><FONT color=red><B>" . $this -> Error["message"] . "<br/></B></FONT>");
				}
				return false;
			}
			return true;
		}
		$this -> Transaction = FALSE;
		return false;
	}

	function show($message)
	{
		$this->Binds = array();
		$this->close();		
		if(DEBUG_MODE)
		{
			echo $message;
		}
		else
		{
			throw new Exception($message, 500);
		}		
	}
	/**
	 * @var object memcached对象
	 */
	public static $MEMCACHED = null;

	/* public: constructor */
	function DB_Sql($query = "") {
	}

	function try_connect() {
		$this -> Query_ID = 0;
		if ($this -> Persistent)
			$this -> Link_ID = @oci_pconnect("$this->DBUser", "$this->DBPassword", "$this->DBDatabase");
		else
			$this -> Link_ID = @oci_connect("$this->DBUser", "$this->DBPassword", "$this->DBDatabase");

		$this -> Connected = $this -> Link_ID ? true : false;
		return $this -> Connected;
	}

	function connect() {
		if (!$this -> Connected) {
			$this -> Query_ID = 0;
			if ($this -> Debug) {
				printf("<br>Connecting to $this->DBDatabase...<br>\n");
			}
            $t1 = MonitoringService::getMillisecond();

            if ($this -> Persistent)
				$this -> Link_ID = oci_pconnect("$this->DBUser", "$this->DBPassword", "$this->DBDatabase", $this -> Encoding);
			else
				$this -> Link_ID = oci_connect("$this->DBUser", "$this->DBPassword", "$this->DBDatabase", $this -> Encoding);
            
            $monitor = MonitoringService::getInstance();
            $monitor->log(MonitoringService::LOG_TYPE_DB, array(
                'et' => MonitoringService::diffMillisecond(MonitoringService::getMillisecond(), $t1),
                'type' => 'CONNECT',
                'content' => $this->DBDatabase
            ));
            
			if (!$this -> Link_ID) {
				$this -> Error = oci_error(!$this -> Link_ID);
				$this -> Halt("Cannot connect to Database: " . $this -> Error["message"]);
				return 0;
			}

			if ($this -> Debug) {
				printf("<br>Obtained the Link_ID: $this->Link_ID<br>\n");
			}
			$this -> Connected = true;
		}
	}

	public function bind($parameter_name, $parameter_value, $parameter_length = -1, $parameter_type = 0) {
		if ($parameter_length == -1 && $parameter_type == 0)
			$parameter_length = strlen($parameter_value);
		$this -> Binds[$parameter_name] = array(
			$parameter_value,
			$parameter_length,
			$parameter_type
		);
	}

	/**
	 * 获取所有数据
	 * @param string $Query_String 查询的SQL语句
	 * @param boolen $usecache 是否缓存数据(默认不缓存)
	 * @param int $time 缓存时间默认60秒
	 * @param string $cachegroup 缓存分组前缀(默认"sql")
	 * @param boolen $change 是否将数组的所有的 KEY 都转换为大写或小写
	 * @return array 数组
	 * @author wander 2012-4-12
	 */
	public function getAll($Query_String, $usecache = 0, $time = MEM_ORACLE_TIMEOUT, $cachegroup = 'sql', $change = 1, $mod = "OCI_ASSOC") {
		if ($usecache && MEM_OPEN) {
			return $this -> getAllWithCache($Query_String, $time, $cachegroup, $change, $mod);
		} else {
			return $this -> getAllWithNoCache($Query_String, $change, $mod);
		}
	}

	/**
	 * 直接从数据库取数据
	 * @param string $Query_String 查询的SQL语句
	 * @param boolen $change 是否将数组的所有的 KEY 都转换为大写或小写
	 * @return array 数组
	 * @author wander 2012-4-16
	 */
	public function getAllWithNoCache($Query_String, $change = 1, $mod) {
		$res = $this -> query($Query_String);
		if ($res !== false) {
			$arr = array();
			while ($this -> next_record($mod)) {
				if ($change) {
					$arr[] = $this -> changekey($this -> Record);
				} else {
					$arr[] = $this -> Record;
				}
			}
			return $arr;
		} else {
			return false;
		}
	}

	/**
	 * 从缓存获取所有数据
	 * @param string $Query_String 查询的SQL语句
	 * @param int $time 缓存时间默认60秒
	 * @param string $cachegroup 缓存分组前缀(默认"sql")
	 * @param boolen $change 是否将数组的所有的 KEY 都转换为大写或小写
	 * @return array 数组
	 * @author wander 2012-4-12
	 */
	private function getAllWithCache($Query_String, $time = MEM_ORACLE_TIMEOUT, $cachegroup = 'sql', $change = 1, $mod) {
		$key = MEM_ROOT . ':' . $cachegroup . ':' . md5($Query_String);
		$rows = array();
		if (null === self::$MEMCACHED) {
			$cache = $GLOBALS['cache'];
			if (FALSE === $cache) {
				$this -> Halt("Cannot connect to MEMCACHE");
				return 0;
			}
			self::$MEMCACHED = $cache;
		}
		$res = self::$MEMCACHED -> get($key);

		if (FALSE !== $res) {
			$this -> Binds = array();
			return json_decode($res, 1);
		} else {
			$rows = $this -> getAllWithNoCache($Query_String, $change, $mod);
			if (FALSE !== $rows) {
				self::$MEMCACHED -> set($key, json_encode($rows, JSON_NUMERIC_CHECK), 0, $time);
			}
		}
		return $rows;
	}

	/**
	 * 获取一行数据
	 * @param string $Query_String 查询的SQL语句
	 * @param boolen $usecache 是否缓存数据(默认不缓存)
	 * @param int $time 缓存时间默认60秒
	 * @param string $cachegroup 缓存分组前缀(默认"sql_")
	 * @param boolen $change 是否将数组的所有的 KEY 都转换为大写或小写
	 * @return array 数组
	 * @author wander 2012-4-12
	 */
	public function getRow($Query_String, $usecache = 0, $time = MEM_ORACLE_TIMEOUT, $cachegroup = 'sql', $change = 1) {
		if ($usecache && MEM_OPEN) {
			return $this -> getRowWithCache($Query_String, $time, $cachegroup, $change);
		} else {
			return $this -> getRowWithNoCache($Query_String, $change);
		}
	}

	/**
	 * 获取一行数据
	 * @param string $Query_String 查询的SQL语句
	 * @param int $time 缓存时间默认60秒
	 * @param string $cachegroup 缓存分组前缀(默认"sql_")
	 * @param boolen $change 是否将数组的所有的 KEY 都转换为大写或小写(默认转为小写)
	 */
	public function getRowWithCache($Query_String, $time = MEM_ORACLE_TIMEOUT, $cachegroup = 'sql', $change = 1) {
		$key = MEM_ROOT . ':' . $cachegroup . ':' . md5($Query_String);
		$row = array();
		if (null === self::$MEMCACHED) {
			$cache = $GLOBALS['cache'];
			if (FALSE === $cache) {
				$this -> Halt("Cannot connect to MEMCACHE");
				return 0;
			}
			self::$MEMCACHED = $cache;
		}
		$res = self::$MEMCACHED -> get($key);
		if (FALSE !== $res) {
			$this -> Binds = array();
			return json_decode($res, 1);
		} else {
			$row = $this -> getRowWithNoCache($Query_String, $change);
			if (FALSE !== $row) {
				self::$MEMCACHED -> set($key, json_encode($row, JSON_NUMERIC_CHECK), 0, $time);
			}
		}
		return $row;
	}

	/**
	 * 直接从数据库中取第一行数据
	 * @param string $Query_String
	 * @param boolen $change  是否将数组的所有的 KEY 都转换为大写或小写(默认转为小写)
	 */
	public function getRowWithNoCache($Query_String, $change = 1) {
		$res = $this -> query($Query_String);
		if ($res) {
			$row = $this -> next_record();
			if ($row) {
				if ($change) {
					return $this -> changekey($this -> Record);
				} else {
					return array();
				}
			} else {
				return array();
			}
		} else {
			return false;
		}
	}

	/**
	 * 获取返回值
	 * @param string $Query_String 查询字符串
	 * @param boolen $usecache 是否使用缓存，默认不使用
	 * @param int $time 缓存时间默认60秒
	 * @param string $cachegroup 缓存分组前缀(默认"sql")
	 */
	public function getOne($Query_String, $usecache = 0, $time = MEM_ORACLE_TIMEOUT, $cachegroup = 'sql') {
		if ($usecache && MEM_OPEN) {
			return $this -> getOneWithCache($Query_String, $time, $cachegroup);
		} else {
			return $this -> getOneWithNoCache($Query_String);
		}
	}

	/**
	 * 从缓存取数据第一个字段
	 * @param string $Query_String
	 * @param int $time 缓存时间默认60秒
	 * @param string $cachegroup 缓存分组前缀(默认"sql")
	 */
	public function getOneWithCache($Query_String, $time = MEM_ORACLE_TIMEOUT, $cachegroup = 'sql') {
		$key = MEM_ROOT . ':' . $cachegroup . ':' . md5($Query_String);
		if (null === self::$MEMCACHED) {
			$cache = $GLOBALS['cache'];
			if (FALSE === $cache) {
				$this -> Halt("Cannot connect to MEMCACHE");
				return 0;
			}
			self::$MEMCACHED = $cache;
		}
		$res = self::$MEMCACHED -> get($key);
		if (FALSE !== $res) {
			$this -> Binds = array();
			return $res;
		} else {
			$res = $this -> getOneWithNoCache($Query_String);
			if (FALSE !== $res) {
				self::$MEMCACHED -> set($key, $res, 0, $time);
				return $res;
			}
			return false;
		}
	}

	/**
	 * 从数据库取数据
	 * @param string $Query_String
	 */
	public function getOneWithNoCache($Query_String) {
		$res = $this -> query($Query_String);
		if ($res !== false) {
			$row = $this -> next_record('');
			if ($row) {
				return $this -> f(0);
			} else {
				return '';
			}
		} else {
			return false;
		}
	}

	/**
	 * 执行SQL语句 仿limit
	 * @param string $sql
	 * @param int $count 多少条数据
	 * @param int $start 开始记录
	 * @return object $this->Query_ID;
	 */
	public function queryLimit($sql, $count, $start = 0) {
		return $this -> selectLimit($sql, $count, $start);
	}

	/*
	 * 执行SQL语句
	 */
	public function query($Query_String,$clear = true) {
		$starttime = $this->get_microtime();
		$this -> Record = array();
//		if (OCI_QUERY_LOG)
//			cls_logger::log('notice', $Query_String, __FILE__, __LINE__);
		/* No empty queries, please, since PHP4 chokes on them. */
		if ($Query_String == "")
			/* The empty query string is passed on from the constructor,
			 * when calling the class without a query, e.g. in situations
			 * like these: '$db = new DB_Sql_Subclass;'
			 */
			return 0;

		$this -> connect();
		if(YII_DEBUG === true){
			$Bound = array();
			foreach($this -> Binds as $key => $val){
				$Bound[] = $key.':'.$val[0];
			}
			$par='. Bound with '.implode(', ',$Bound);
			Yii::log($Query_String.$par,'trace','cls_oracle:begin');
		}
        $t1 = MonitoringService::getMillisecond();
        $monitor = MonitoringService::getInstance();
        
		$this -> Query_ID = oci_parse($this -> Link_ID, $Query_String);
		if (!$this -> Query_ID) {
			$this -> Error = oci_error($this -> Query_ID);
			exit ;
		} else {
            $queryType = oci_statement_type($this->Query_ID);
			if (sizeof($this -> Binds) > 0) {
				foreach ($this->Binds as $parameter_name => $parameter_values) {
					if ($parameter_values[2] == OCI_B_CURSOR) {
						$this -> Binds[$parameter_name][0] = oci_new_cursor($this -> Link_ID);
                    } elseif ($parameter_values[2] == 0) {
                        oci_bind_by_name($this -> Query_ID, ":" . $parameter_name, $this -> Binds[$parameter_name][0], $parameter_values[1]);
                    } elseif ($parameter_values[2] == OCI_B_CLOB) {
                        $this->Binds[$parameter_name][3] = oci_new_descriptor($this->Link_ID, OCI_D_LOB);
                        oci_bind_by_name($this -> Query_ID, ":" . $parameter_name, $this -> Binds[$parameter_name][3], $parameter_values[1], $parameter_values[2]);
                    } else {
                        oci_bind_by_name($this -> Query_ID, ":" . $parameter_name, $this -> Binds[$parameter_name][0], $parameter_values[1], $parameter_values[2]);   
                    }
				}
			}
			if ($this -> Transaction === FALSE) {
				oci_execute($this -> Query_ID);
			} else {
				oci_execute($this -> Query_ID, OCI_DEFAULT);
			}
            $monitor->log(\MonitoringService::LOG_TYPE_DB, array(
                'et' => \MonitoringService::diffMillisecond(\MonitoringService::getMillisecond(), $t1),
                'type' => $queryType,
                'content' => $Query_String
            ));
            
			$this -> Error = oci_error($this -> Query_ID);
		}

		$this -> Row = 0;

		if ($this -> Debug) {
			printf("Debug: query = %s<br>\n", $Query_String);
		}

		if ($this -> Error["code"] != 1403 && $this -> Error["code"] != 0 && $this -> sqoe) {
			$this->show("<BR><FONT color=red><B>" . $this -> Error["message"] . "<BR>Query :\"$Query_String\"</B></FONT>");
		}

		if (sizeof($this -> Binds) > 0) {
			$bi = 0;
			foreach ($this->Binds as $parameter_name => $parameter_values) {
				if ($parameter_values[2] == OCI_B_CURSOR) {
					if ($this -> Transaction === FALSE) {
						oci_execute($this -> Binds[$parameter_name][0]);
					} else {
						oci_execute($this -> Binds[$parameter_name][0], OCI_DEFAULT);
					}
					$this -> Error = oci_error($this -> Binds[$parameter_name][0]);
					$this -> Query_ID = $this -> Binds[$parameter_name][0];
                } elseif ($parameter_values[2] == OCI_B_CLOB) {
                    $this->Binds[$parameter_name][3]->truncate();
                    $r = $this->Binds[$parameter_name][3]->save($this->Binds[$parameter_name][0]);
                }
				$this -> Record[$parameter_name] = $parameter_values[0];
//				$this -> Record[$bi++] = $parameter_values[0];
			}
		}
		global $QUERY_SQL;
		$speed = $this->speed($starttime);
		$QUERY_SQL[] = array($speed,$Query_String);
		if($clear){$this -> Binds = array();}
		return $this -> Query_ID;
	}
	/**
	 * 获取当前时间（微秒级）
	 */
	public function get_microtime()
    { 
        list($usec, $sec) = explode(' ', microtime()); 
        return ((float)$usec + (float)$sec); 
    }
	/**
     * 计算程序执行时间(毫秒级)
     */
    public function speed($starttime) 
    { 
    	$STOP_TIME = $this->get_microtime();
        return round(($STOP_TIME - $starttime) * 1000, 1); 
    }

	/**
	 * 返回执行oracle语句的错误代码
	 *
	 */
	public function errno() {
		return $this -> Error["code"];
	}

	/**
	 * 返回错误信息
	 *
	 */
	public function errorMsg() {
		return $this -> Error["message"];
	}

	/**
	 * ORACLE limit查询 仿mysql limit
	 * @param string $sql 查询的sql语句
	 * @param int $num 到第几条数据
	 * @param int $start 起始记录数，默认是从0开始
	 * 如第10条到第20条，$start=10 $num = 20
	 * @return object $this->Query_ID
	 */
	public function selectLimit($sql, $num, $start = 0) {
		$start = $start < 0 ? 1 : $start + 1;

		$querysql = 'SELECT * FROM 
					(
						SELECT TEMPSELECT.*, ROWNUM as ROW_NUMBER FROM ( ' . $sql . ' ) TEMPSELECT
						WHERE ROWNUM < ' . ($num + $start) . ')';

		$querysql .= ' WHERE  ' . $start . '<= ROW_NUMBER';

		return $this -> query($querysql);
	}

	/**
	 * 仿mysql limit
	 *
	 * @param string $sql
	 * @param int $num
	 * @param int $start
	 * @param boolen $usecache 是否从缓存取数据(默认直接从数据库取)
	 * @param boolen $change 是否转换大小写，默认转成小写
	 * @return array 返回查询到的数据
	 */
	public function selectLimitEx($sql, $num, $start = 0, $usecache = 0, $change = 1) {
		$start = $start < 0 ? 1 : $start + 1;
		$querysql = 'SELECT * FROM 
					(
						SELECT TEMPSELECT.*, ROWNUM as ROW_NUMBER FROM ( ' . $sql . ' ) TEMPSELECT
						WHERE ROWNUM < ' . ($num + $start) . ')';

		$querysql .= ' WHERE  ' . $start . '<= ROW_NUMBER';
		return $this -> getAll($querysql, $usecache, $change);
	}

	/**
	 * 分页取数据
	 * @param string $sql
	 * @param int $page_index 当前页，默认第一页
	 * @param int $page_size 一页条数，默认20条
	 */
	public function selectPage($sql, $page_index = 1, $page_size = 20, $changeKey = 1) {
		if ($page_index <= 0) {
			$page_index = 1;
		}
		$start = $page_size * ($page_index - 1) + 1;
		$num = $page_size * $page_index;

		$res = $this -> selectLimit($sql, $num, $start);
		if ($res !== false) {
			$arr = array();
			while ($this -> next_record()) {
				if ($changeKey) {
					$arr[] = $this -> changekey($this -> Record);
				} else {
					$arr[] = $this -> Record;
				}
			}
			return $arr;
		} else {
			return false;
		}
	}

	/**
	 * 获取下一条数据
	 * @param object $query 数据库对象
	 */
	public function fetchRow($query, $changeKey = 1) {
		if ($this -> next_record()) {
			if ($changeKey) {
				return $this -> changekey($this -> Record);
			} else {
				return $this -> Record;
			}
		} else {
			return false;
		}
	}

	/**
	 * 方法同fetchRow
	 * Enter description here ...
	 * @param $query
	 * @param $changeKey
	 */
	function fetch_array($query, $changeKey = 1) {
		return $this -> fetchRow($query, $changeKey);
	}

	function next_record($mode = "OCI_ASSOC") {
		if (!$this -> Query_ID)
			return 0;
		switch($mode) {
			case "OCI_ASSOC" :
				$result = @oci_fetch_array($this -> Query_ID, OCI_ASSOC + OCI_RETURN_NULLS);
				break;
			case "OCI_NUM" :
				$result = @oci_fetch_array($this -> Query_ID, OCI_NUM + OCI_RETURN_NULLS);
				break;
			default :
				$result = @oci_fetch_array($this -> Query_ID, OCI_BOTH + OCI_RETURN_NULLS);
				break;
		}
		// $result = @oci_fetch_array($this->Query_ID,OCI_ASSOC);
		//if (0 == @OCIFetchInto($this->Query_ID, $result, OCI_ASSOC + OCI_RETURN_NULLS))
		if (!$result) {
			if ($this -> Debug) {
				printf("<br>ID: %d,Rows: %d<br>\n", $this -> Link_ID, $this -> num_rows());
			}
			$this -> Row += 1;

			$errno = oci_error($this -> Query_ID);
			if (1403 == $errno) {# 1043 means no more records found
				$this -> Error = "";
				$this -> disconnect();
				$stat = 0;
			} else {
				$this -> Error = oci_error($this -> Query_ID);
				if ($this -> Debug) {
					printf("<br>Error: %s", $this -> Error["message"]);
				}
				$stat = 0;
			}
		} else {
			for ($ix = 1; $ix <= oci_num_fields($this -> Query_ID); $ix++) {
				$col = oci_field_name($this -> Query_ID, $ix);
				$colreturn = $col;
				if(array_key_exists($col,$result))//判断键值是否存在
				{
					$result["$col"] = is_object($result["$col"]) ? $result["$col"] -> load() : $result["$col"];
					$this -> Record["$colreturn"] = $result["$col"];

					if ($mode != "OCI_ASSOC")
						$this -> Record[$ix - 1] = $result["$col"];
				}
				if ($this -> Debug)
					echo "<b>[$col]</b>:" . $result["$col"] . "<br>\n";
			}
			$stat = 1;
		}
		return $stat;
	}

	function seek($pos) {
		$i = 0;
		//while ($i < $pos && @OCIFetchInto($this->Query_ID, $result, OCI_ASSOC + OCI_RETURN_NULLS))
		while ($i < $pos && @oci_fetch_array($this -> Query_ID)) {
			$i++;
		}
		$this -> Row += $i;
		return true;
	}

	function metadata($table, $full = false) {
		$count = 0;
		$id = 0;
		$res = array();

		/*
		 * Due to compatibility problems with Table we changed the behavior
		 * of metadata();
		 * depending on $full, metadata returns the following values:
		 *
		 * - full is false (default):
		 * $result[]:
		 *   [0]["table"] table name
		 *   [0]["name"]   field name
		 *   [0]["type"]   field type
		 *   [0]["len"]    field length
		 *   [0]["flags"] field flags ("NOT NULL", "INDEX")
		 *   [0]["format"] precision and scale of number (eg. "10,2") or empty
		 *   [0]["index"] name of index (if has one)
		 *   [0]["chars"] number of chars (if any char-type)
		 *
		 * - full is true
		 * $result[]:
		 *   ["num_fields"] number of metadata records
		 *   [0]["table"] table name
		 *   [0]["name"]   field name
		 *   [0]["type"]   field type
		 *   [0]["len"]    field length
		 *   [0]["flags"] field flags ("NOT NULL", "INDEX")
		 *   [0]["format"] precision and scale of number (eg. "10,2") or empty
		 *   [0]["index"] name of index (if has one)
		 *   [0]["chars"] number of chars (if any char-type)
		 *   ["meta"][field name] index of field named "field name"
		 *   The last one is used, if you have a field name, but no index.
		 *   Test: if (isset($result['meta']['myfield'])) {} ...
		 */

		$this -> connect();

		## This is a RIGHT OUTER JOIN: "(+)", if you want to see, what
		## this query results try the following:
		## $table = new Table; $db = new my_DB_Sql; # you have to make
		##                                          # your own class
		## $table->show_results($db->query(see query vvvvvv))
		##
		$this -> query("SELECT T.table_name,T.column_name,T.data_type," . "T.data_length,T.data_precision,T.data_scale,T.nullable," . "T.char_col_decl_length,I.index_name" . " FROM ALL_TAB_COLUMNS T,ALL_IND_COLUMNS I" . " WHERE T.column_name=I.column_name (+)" . " AND T.table_name=I.table_name (+)" . " AND T.table_name=UPPER('$table') ORDER BY T.column_id");

		$i = 0;
		while ($this -> next_record()) {
			$res[$i]["table"] = $this -> Record["table_name"];
			$res[$i]["name"] = strtolower($this -> Record["column_name"]);
			$res[$i]["type"] = $this -> Record['data_type'];
			$res[$i]["len"] = $this -> Record['data_length'];
			$res[$i]["flags"] = '';
			if ($this -> Record['index_name']) {
				$res[$i]["flags"] = "INDEX ";
			}
			$res[$i]["flags"] .= ($this -> Record['nullable'] == 'N') ? '' : 'NOT NULL';
			$res[$i]["format"] = (int)$this -> Record['data_precision'] . "," . (int)$this -> Record['data_scale'];
			if ("0,0" == $res[$i]["format"])
				$res[$i]["format"] = '';
			$res[$i]["index"] = $this -> Record['index_name'];
			$res[$i]["chars"] = $this -> Record['char_col_decl_length'];
			if ($full) {
				$j = $res[$i]["name"];
				$res["meta"][$j] = $i;
				$res["meta"][strtoupper($j)] = $i;
			}
			if ($full)
				$res["meta"][$res[$i]["name"]] = $i;
			$i++;
		}
		if ($full)
			$res["num_fields"] = $i;
		#$this->disconnect();
		return $res;
	}

	function affected_rows() {
		return $this -> num_rows();
	}

	function num_rows() {
		return oci_num_rows($this -> Query_ID);
	}

	function num_fields() {
		return oci_num_fields($this -> Query_ID);
	}

	function nf() {
		return $this -> num_rows();
	}

	function np() {
		print $this -> num_rows();
	}

	function f($Name) {
		if ($this -> Uppercase)
			$Name = strtoupper($Name);
		if (array_key_exists($Name, $this -> Record) && is_object($this -> Record[$Name])) {
			return $this -> Record[$Name] -> load();
		} else {
			return $this -> Record && array_key_exists($Name, $this -> Record) ? $this -> Record[$Name] : "";
		}
	}

	function p($Name) {
		if ($this -> Uppercase)
			$Name = strtoupper($Name);
		print $this -> f($Name);
	}

	/**
	 * 返回刚插入记录的自增字段值 该值只有在同一次会话中，发生$seqname.nextval后有效
	 * currval返回序列的当前值
	 *
	 * @param string $seqname 序列名称
	 * @return number 序列的当前值
	 */
	public function insert_id($seqname) {
		$curr_id = 0;
		$this -> connect();

		$Query_ID = @oci_parse($this -> Link_ID, "SELECT $seqname.CURRVAL FROM DUAL");

		if (!@oci_execute($Query_ID)) {
			$this -> Error = @oci_error($Query_ID);
			if ($this -> Error["code"] == 2289) {
				$Query_ID = oci_parse($this -> Link_ID, "CREATE SEQUENCE $seqname");
				if (!oci_execute($Query_ID)) {
					$this -> Error = oci_error($Query_ID);
					$this -> Errors -> addError("Database error: " . $this -> Error["message"]);
					return 0;
				} else {
					$Query_ID = oci_parse($this -> Link_ID, "SELECT $seqname.CURRVAL FROM DUAL");
					oci_execute($Query_ID);
				}
			}
		}

		if (oci_fetch($Query_ID)) {
			$curr_id = oci_result($Query_ID, "CURRVAL");
		} else {
			$curr_id = 0;
		}
		oci_free_statement($Query_ID);
		return $curr_id;
	}

	public function nextid($seqname) {
		$this -> connect();

		$Query_ID = @oci_parse($this -> Link_ID, "SELECT $seqname.NEXTVAL FROM DUAL");

		if (!@oci_execute($Query_ID)) {
			$this -> Error = @oci_error($Query_ID);
			if ($this -> Error["code"] == 2289) {
				$Query_ID = oci_parse($this -> Link_ID, "CREATE SEQUENCE $seqname");
				if (!oci_execute($Query_ID)) {
					$this -> Error = oci_error($Query_ID);
					$this -> Errors -> addError("Database error: " . $this -> Error["message"]);
					return 0;
				} else {
					$Query_ID = oci_parse($this -> Link_ID, "SELECT $seqname.NEXTVAL FROM DUAL");
					oci_execute($Query_ID);
				}
			}
		}

		if (ocifetch($Query_ID)) {
			$next_id = ociresult($Query_ID, "NEXTVAL");
		} else {
			$next_id = 0;
		}
		oci_free_statement($Query_ID);
		return $next_id;
	}

	function disconnect() {
		if ($this -> Debug) {
			printf("Disconnecting...<br>\n");
		}
		oci_close($this -> Link_ID);
	}

	function free_result() {
		@oci_free_statement($this -> Query_ID);
		$this -> Query_ID = 0;
	}

	function close() {
		if ($this -> Query_ID) {
			$this -> free_result();
		}
		if ($this -> Connected && !$this -> Persistent) {
			oci_close($this -> Link_ID);
			$this -> Connected = false;
		}
	}

	function halt($msg) {
		$this->Binds = array();
		$this->close();
		if(DEBUG_MODE)
		{
			printf("</td></tr></table><b>Database error:</b> %s<br>\n", $msg);
			printf("<b>ORACLE Error</b>: %s<br>\n", $this -> Error["message"]);
			die("Session halted.");
		}
		else
		{
			throw new Exception($msg, 500);
		}
	}

	/**
	 * 析构函数
	 */
	public function __destruct()
	{
		$this->close();
	}
	function lock($table, $mode = "write") {
		$this -> connect();
		if ($mode == "write") {
			$Parse = oci_parse($this -> Link_ID, "lock table $table in row exclusive mode");
			oci_execute($Parse);
		} else {
			$result = 1;
		}
		return $result;
	}

	function unlock() {
		return $this -> query("commit");
	}

	function table_names() {
		$this -> connect();
		$this -> query("
   SELECT table_name,tablespace_name
     FROM user_tables");
		$i = 0;
		while ($this -> next_record()) {
			$info[$i]["table_name"] = $this -> Record["table_name"];
			$info[$i]["tablespace_name"] = $this -> Record["tablespace_name"];
			$i++;
		}
		return $info;
	}

	function add_specialcharacters($query) {
		return str_replace("'", "''", $query);
	}

	function split_specialcharacters($query) {
		return str_replace("''", "'", $query);
	}

	function esc($value) {
		return str_replace("'", "''", $value);
	}

	function bindsblob($sql, $string) {
		$this -> connect();
		$stmt = oci_parse($this -> Link_ID, $sql);

		// Creates an "empty" OCI-Lob object to bind to the locator
		$myLOB = oci_new_descriptor($this -> Link_ID, OCI_D_LOB);

		// Bind the returned Oracle LOB locator to the PHP LOB object
		oci_bind_by_name($stmt, ":CONTENT", $myLOB, -1, OCI_B_BLOB);

		// Execute the statement using , OCI_DEFAULT - as a transaction
		oci_execute($stmt, OCI_DEFAULT) or die("Unable to execute query\n");

		// Now save a value to the LOB
		if (!$myLOB -> save($string)) {

			// On error, rollback the transaction
			oci_rollback($this -> Link_ID);
			$result = 0;

		} else {

			// On success, commit the transaction
			oci_commit($this -> Link_ID);
			$result = 1;
		}

		// Free resources
		oci_free_statement($stmt);
		$myLOB -> free();
		return $result;
	}

	/**
	 * 应对mysql 基类下的 getCol 方法
	 * @author  Joanh.Fu
	 * @date    2102-03-21
	 * @return  boolean/int
	 */
	public function getCol($sql, $change = 1) {
		/*SELECT * FROM user_tab_columns WHERE table_name = '表名'
		 $res = $this ->query($sql);
		 if ($res !== false) {
		 $arr = array();
		 while ($row = oci_fetch_assoc($res)) {
		 $arr[] = $row[0];
		 }

		 return $arr;
		 } else {
		 return false;
		 }
		 */
		$res = $this -> query($sql);
		if ($res !== false) {
			$arr = array();
			while ($this -> next_record()) {
				$arr[] = $this -> f(0);
			}
			return $arr;
		} else {
			return false;
		}
	}

	/**
	 * 把key该成小写
	 * @author  Joanh.Fu
	 * @date    2102-03-21
	 * @return  boolean/int
	 */
	private function changekey($arr) {
		$arr = array_change_key_case($arr, CASE_LOWER);
		return $arr;
	}

	/**
	 * 传入数组自动执行SQL语句，包括INSERT和UPDATE(完全兼容日期型和自增长型字段序列)
	 * 日期型的请传入日期型字符串，格式 为yyyy-mm-dd hh24:mi:ss
	 * 序列请传入序列名。如：SEQ_ECS_ADMIN_USER_USER_ID.NEXTVAL 注：要带上.NEXTVAL
	 * @param string $table 要操作的表名
	 * @param array $field_values 要插入或者更新的字段 键/值
	 * @param string $mode INSERT是插入新数据，其它为更新操作。
	 * @param string $where 更新的时候要加入条件。例：$where='user_id=1'
	 * @author wander
	 */
	public function autoExecute($table, $field_values, $mode = 'INSERT', $where = '') {
		$table = trim($table);
		//去除表名空格
		$field_values = array_change_key_case($field_values, CASE_UPPER);
		//键名换大写
		$sql = "SELECT COLUMN_NAME,DATA_TYPE FROM USER_TAB_COLUMNS WHERE TABLE_NAME = '$table'";
		$tableinfo = $this -> getAll($sql, 0, MEM_ORACLE_TIMEOUT, "sql", 1, "both");
		//查询用户表结构，取【字段名、字段类型】防止传入的数组有多余元素并为以后不同字段类型做处理。
		$field_names = array();
		//二维数组转为一维数组
		foreach ($tableinfo as $row) {
			$field_names[strtoupper($row[0])] = strtoupper($row[1]);
		}
		$sql = '';
		$this -> Binds = array();
		if ($mode == 'INSERT') {
			$fields = $values = array();
			foreach ($field_names as $key => $value) {
				if (array_key_exists($key, $field_values) == true) {
					$fields[] = $key;
					//要插入的字段。

					//处理自增字段
					if (strtoupper(substr($field_values[$key], -8)) == '.NEXTVAL') {
						$values[] = $field_values[$key];
					} else {
						//判断是不是日期型的。如果是日期型的要进行转换。
						if ($value == 'DATE') {
							//TO_DATE(:$key,'yyyy-mm-dd hh24:mi:ss')
							$values[] = "TO_DATE(:$key,'yyyy-mm-dd hh24:mi:ss')";
						} else {
							$values[] = ":$key";
						}
						$this -> bind($key, $field_values[$key]);
						//绑定参数
					}
				}
			}
			if (!empty($fields)) {
				$sql = 'INSERT INTO ' . $table . ' (' . implode(', ', $fields) . ') VALUES (' . implode(', ', $values) . ')';
			}
		} else {
			$sets = array();
			foreach ($field_names AS $key => $value) {
				if (array_key_exists($key, $field_values) == true) {
					if ($value == 'DATE') {
						$sets[] = "$key = TO_DATE(:$key,'yyyy-mm-dd hh24:mi:ss')";
					} else {
						$sets[] = "$key = :$key";
					}
					$this -> bind($key, $field_values[$key]);
					//绑定参数
				}
			}

			if (!empty($sets)) {
				$sql = 'UPDATE ' . $table . ' SET ' . implode(', ', $sets) . ' WHERE ' . $where;
			}
		}
		if ($sql) {
			return $this -> query($sql);
		} else {
			$this -> Binds = array();
			return false;
		}
	}

}

//End DB OCI8 Class
?>