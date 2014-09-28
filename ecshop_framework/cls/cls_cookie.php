<?php

class cls_cookie {
    var $session_id = '';
    var $_ip = '';

//    private $COOKIE_PATH = '';
//    private $COOKIE_DOMAIN = '';
//    private $session_name = 'ECS_ID';
//    private $cookieTimeOut = 3600;

    /**
     * 初始化
     * Enter description here ...
     */
    function __construct() {
    	$GLOBALS['_SESSION'] = array();
    	$userInfo = $this->getUser();
    	
    	if(!empty($userInfo))
    	{
    		$GLOBALS['_SESSION'] = $userInfo;
    	}
		$cookieid = $this -> get('SESSION_ID');
    	if (!empty($cookieid)) {
            $this -> session_id = $cookieid;
        } else {
            $this -> session_id = $this -> get_guid();
            $this -> add('SESSION_ID', $this -> session_id);
        }
    	/*
        $GLOBALS['_SESSION'] = array();
        $cookieid = $this -> get('ECS_ID');
        $_array = $this -> get('ECS');
        //$this->_ip = real_ip();

        if (!empty($cookieid)) {
            $this -> session_id = $cookieid;
        } else {
            $this -> session_id = $this -> get_guid();
            $this -> add($this -> session_name, $this -> session_id, $this -> cookieTimeOut);
        }
        if ($_array != '') {

            $GLOBALS['_SESSION']['user_id'] = !array_key_exists('user_id', $_array) ? 0 : intval($_array['user_id']);
            $GLOBALS['_SESSION']['admin_id'] = !array_key_exists('admin_id', $_array) ? 0 : intval($_array['admin_id']);
            $GLOBALS['_SESSION']['user_name'] = !array_key_exists('user_name', $_array) ? '' : trim($_array['user_name']);
            $GLOBALS['_SESSION']['discount'] = !array_key_exists('discount', $_array) ? 1.00 : round($_array['discount']);
            $GLOBALS['_SESSION']['auto_login'] = !array_key_exists('auto_login', $_array) ? '' : round($_array['auto_login']);
            
            $GLOBALS['_SESSION']['email'] = !array_key_exists('email', $_array) ? '' : ($_array['email']);
            $GLOBALS['_SESSION']['mobile_phone'] = !array_key_exists('mobile_phone', $_array) ? '' : ($_array['mobile_phone']);
            $GLOBALS['_SESSION']['user_real_name'] = !array_key_exists('user_real_name', $_array) ? '' : ($_array['user_real_name']);


        } else {
            $GLOBALS['_SESSION']['user_id'] = 0;
            $GLOBALS['_SESSION']['admin_id'] = 0;
            $GLOBALS['_SESSION']['user_name'] = '';
            //$GLOBALS['_SESSION']['user_rank'] = 0;
            $GLOBALS['_SESSION']['discount'] = 1.00;
            $GLOBALS['_SESSION']['auto_login'] = '';
            
        }
        $GLOBALS['_SESSION']['user_rank'] = $this -> get('user_rank') == '' ? 0 : $this -> get('user_rank');
        //$GLOBALS['_SESSION']['email'] = $this -> get('email') == '' ? '' : trim($this -> get('email'));
        $GLOBALS['_SESSION']['last_time'] = $this -> get('last_time') == '' ? 0 : $this -> get('last_time');
        $GLOBALS['_SESSION']['from_ad'] = $this -> get('from_ad') == '' ? '' : trim($this -> get('from_ad'));
        $GLOBALS['_SESSION']['referer'] = $this -> get('referer') == '' ? '' : trim($this -> get('referer'));
        $GLOBALS['_SESSION']['login_fail'] = $this -> get('login_fail') == '' ? 0 : $this -> get('login_fail');
        $GLOBALS['_SESSION']['temp_user'] = $this -> get('temp_user') == '' ? '' : trim($this -> get('temp_user'));
        $GLOBALS['_SESSION']['temp_user_name'] = $this -> get('temp_user_name') == '' ? '' : trim($this -> get('temp_user_name'));
        $GLOBALS['_SESSION']['passwd_answer'] = $this -> get('passwd_answer') == '' ? '' : trim($this -> get('passwd_answer'));
        $GLOBALS['_SESSION']['last_email_query'] = $this -> get('last_email_query') == '' ? 0 : $this -> get('last_email_query');
        $GLOBALS['_SESSION']['last_order_query'] = $this -> get('last_order_query') == '' ? 0 : $this -> get('last_order_query');
        $GLOBALS['_SESSION']['display_search'] = $this -> get('display_search') == '' ? '' : trim($this -> get('display_search'));
        $GLOBALS['_SESSION']['send_time'] = $this -> get('send_time') == '' ? 0 : $this -> get('send_time');
        $GLOBALS['_SESSION']['tryLoginCount'] = $this -> get('tryLoginCount') == '' ? 0 : $this -> get('tryLoginCount');
	*/
    }
    
    public function init(){
    	
    }

    /**
     * 获取用户登录信息
     * array ('user_id' => $userInfo [0], 'user_name' => $userInfo [1], 'login_name' => $userInfo [2], 'email' => $userInfo [3], 'mobile_phone' => $userInfo [4], 'user_real_name' => $userInfo [5], 'autologin' => $userInfo [6] );
     */
    public function getUser()
    {
    	$user = array();
    	$userCookie = $this->get(USER_KEY);
    	if(!empty($userCookie))
    	{
    		$userInfo = explode("\r\n", $userCookie);
    		$user = array(	'user_id'=>$userInfo[0],
	    					'user_name'=>$userInfo[1],
	    					'login_name'=>$userInfo[2],
	    					'email'=>$userInfo[3],
				    		'mobile_phone'=>$userInfo[4],
				    		'user_real_name'=>$userInfo[5],
				    		'auto_login'=>$userInfo[6]
    					);
    	}
    	return $user;
    }
    /**
     * 设置cookie
     * Enter description here ...
     * @param string $name
     * @param string $value
     * @param int $expirytime 过期时间
     */
    public function add($name, $value, $expirytime = 0) 
    {
    	$value = $this->_encrypt($value);
        $cookie_path = COOKIE_PATH;
        $cookie_domain = COOKIE_DOMAIN;
        setcookie($name, $value, $expirytime, $cookie_path, $cookie_domain);
    }

    /**
     * 获取cookie
     * Enter description here ...
     * @param $name
     */
    public function get($name) 
    {
       $value = isset($_COOKIE[$name]) ? $_COOKIE[$name]: '';
       if(is_array($value))
       {
       		$ret = array();
       		foreach ($value as $key => $val)
       		{
       			$ret[$key] = $this->_decrypt($val);
       		}
       		return $ret;
       }
       else
       {
	       $value = $this->_decrypt($value);
	       return $value;
       }
    }

    /**
     * GUID
     * Enter description here ...
     */
    public function get_guid() {
        return md5(uniqid(mt_rand(), true));
    }

    /**
     * 返回id
     * Enter description here ...
     */
    public function get_session_id() {
        return $this -> session_id;
    }

    function gen_session_id() {
        $this -> session_id = $this -> get_guid();
    }

    function gen_session_key($session_id) {
        static $ip = '';

        if ($ip == '') {
            $ip = substr($this -> _ip, 0, strrpos($this -> _ip, '.'));
        }

        return sprintf('%08x', crc32(ROOT_PATH . $ip . $session_id));
    }
    
	/**
	 * 解密已经加密了的cookie
	 * 
	 * @param string $encryptedText
	 * @return string
	 */
	public function _decrypt($encryptedText)
	{
		return ECommon::decrypt($encryptedText);
	}

	/**
	 * 加密cookie
	 *
	 * @param string $plainText
	 * @return string
	 */
	public function _encrypt($plainText)
	{
		return ECommon::encrypt($plainText);
	}
	
}
