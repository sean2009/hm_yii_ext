<?php
/**
 * oci_pdo 类
 * @author	jonah.fu
 * @date	2012-04-21
 */
class base_pdooci {

	/**
	 * 返回的连接状态
	 * @author	jonah.fu
	 * @date	2012-04-21
	 */
	public $connected = FALSE;

	/**
	 * 返回的pdo对象
	 * @author	jonah.fu
	 * @date	2012-04-21
	 */
	public $mConn;
	protected $DbUserName, $DbPassWord, $DbConnectStr, $DbCharset;

	public static $_instance = array();

	private function __construct($type = 0, $autoClose = 0, $ociDbServer = array(), $ociConnectStr = "") {
		if (empty($ociDbServer))
			$ociDbServer = $GLOBALS['ociPdoServer'];
		if (empty($ociConnectStr)) {
			$ociConnectStr = $GLOBALS["ociPdoConnectStr"];
		}
		$this -> DbCharset = $ociDbServer["charset"];
		$this -> DbUserName = $ociDbServer["username"];
		$this -> DbPassWord = $ociDbServer["password"];
		$this -> DbConnectStr = $ociConnectStr;

		try {
			$this -> mConn = new PDO("oci:dbname=" . $this -> DbConnectStr . ";charset=" . $this -> DbCharset, $this -> DbUserName, $this -> DbPassWord, array(
				PDO::ATTR_PERSISTENT => $type,
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
				PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
				PDO::ATTR_CASE => PDO::CASE_LOWER,
				PDO::ATTR_AUTOCOMMIT => TRUE
			));
			$this -> connected = TRUE;
		} catch ( Exception $e ) {
			printf($e -> getMessage());
			exit();
		}

		if ($autoClose) {
			register_shutdown_function(array(
				&$this,
				'close_pdooci'
			));
		}
	}

	/**
	 * 防止复制对象,因为单例模式要保证一个类实例的对象是唯一的.
	 */
	private function __clone() {
	}

	/**
	 * 返回的连接状态
	 * @author	jonah.fu
	 * @date	2012-04-21
	 * @param	int			type			是否长联接
	 * @param	int			autoClose		是否自动关闭连接
	 * @param	int			autoClose		是否自动关闭
	 * @param	array		ociDbServer		数据库信息，参考 config.php 文件
	 * @param	string		ociConnectStr	数据库连接字符串，参考 config.php 文件
	 *
	 * @return	object		返回pdo_oci_obj 对象
	 */
	public static function connect($type = 0, $autoClose = 0, $ociDbServer = array(), $ociConnectStr = "") {
		$arrStr = md5(json_encode((array)($ociDbServer)));
		if (empty(self::$_instance) || !(self::$_instance[$arrStr] instanceof self)) {

			// 如果是长联接则不自动关闭
			if ($type)
				$autoClose = 0;
			self::$_instance[$arrStr] = new self($type, $autoClose, $ociDbServer, $ociConnectStr);
		}
		return self::$_instance[$arrStr];
	}

	/**
	 * 关闭连接
	 * @author	jonah.fu
	 * @date	2012-04-21
	 */
	public function close_pdooci() {
		$this -> mConn = null;
	}

}
?>