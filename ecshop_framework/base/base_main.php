<?php
/**
 * base_common 系统方法类
 *
 * @access  public
 * @return  object
 * @package default
 * @author  Jonah.Fu
 * @date    2012-03-20
 */
class base_main {
	private function __construct() {
		// echo "default";
	}
    
      /**
     * 格式化400电话
     */
    public static function format_400($phone) {
        // 默认电话
        if (empty($phone) || $phone == '4000-213-213') {
            return '4000-213-213';
        }
        list($master, $redirect) = explode('转', $phone);
        $master = str_replace('-', '', $master);
        
        $first = substr($master, 0, 4);
        $last = implode('-', str_split(substr($master, 4), 3));
        
        return "$first-$last" . ($redirect ? "<span>转</span>$redirect" : '');
    }
    
     /**
     * 
     * @param int $time timestamp
     * @param boolean $human 更好读的形式
     * @param boolean $showDetail 是否显示小时分钟秒
     * @return string
     */
    public static function date_format($time, $human = false, $showDetail = false) {
        if ($human) {
            $now = time();
            if ($now > $time) {
                $diff = $now - $time;

                if ($diff > 0 && $diff < 60) {
                    return sprintf('%d秒前', $diff);
                } elseif ($diff >= 60 && $diff < 3600) {
                    return sprintf('%d分钟前', $diff / 60);
                } elseif ($diff >= 3600 && $diff < 3600 * 24) {
                    return sprintf('%d小时前', $diff / 3600);
                } elseif ($diff >= 3600 * 24) {
                    return date('Y-m-d' . ($showDetail ? ' H:i:s' : ''), $time);
                }
            }
        }
        return date('Y-m-d' . ($showDetail ? ' H:i:s' : ''), $time);
    }
    
	//截取utf8字符串 
	public static function utf8Substr($str, $from, $len) 
	{ 
		return preg_replace('#^(?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,'.$from.'}'. 
				'((?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,'.$len.'}).*#s', 
				'$1',$str); 
	}
    
    /**
	 * 获取用户IP对应的城市ID(省、市)
	 * @return array array('province'=>-1,'city'=>-1);都是-1代表没有查出来
     * @deprecated since version yii
	 */
	public static function getIp_Region(){
		$province = Yii::app()->cookie -> get('citys_province');
		$city = Yii::app()->cookie -> get('citys_city');
		$cityInfo = array('province'=>$province,'city'=>$city);
        if (DEBUG_MODE && empty($city)) {
            $_SERVER['HTTP_X_FORWARDED_FOR'] = '27.115.87.142,192.168.0.1';
            $city = '';
        }
        
		if($province=='' || $city == '')
		{
			$addr = self::get_ip();
			$cityInfo = self::getCity($addr);
			Yii::app()->cookie -> add('citys_province', $cityInfo['province'], (time()+3600 * 24 * 15));
			Yii::app()->cookie -> add('citys_city', $cityInfo['city'], (time()+3600 * 24 * 15));
        }
		return $cityInfo;
	}
    
    /**
	 * 获取IP对应的地址，纯真数据
     * @deprecated since version yii
	 */
	public static function get_ip(){
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            list($ip,) = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        } elseif (isset($_SERVER['HTTP_CLIENT_IP']) && !empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
		$addr = '';
		if(!extension_loaded('qqwry')){//直接用php查询QQWry数据
			$ipaddr = base_main::get_Location($ip);
			$addr = isset($ipaddr['country'])?$ipaddr['country']:'';
		}
		else//加载qqwry的PHP扩展来查询
		{
			$filename =realpath(dirname(__FILE__) . '/qqwry.dat');
			$qqwry=new qqwry($filename);
			list($addr1,$addr2)=$qqwry->q($ip);
			$addr=iconv('GB2312','UTF-8',$addr1);
		}
		return $addr;
	}
    
	/**
	 * 根据qqwry查出来的地区数据,对应到省、市，返回ecs_region中的ID
	 * @param string $str qqwry查出来的地区数据,到省、市
	 * @return array array('province'=>-1,'city'=>-1);都是-1代表没有查出来
     * @deprecated since version yii
	 */
	public static function getCity($str){
		$regions = self::getRegions();
		$res = array('province'=>-1,'city'=>-1);
		
		//先取直辖市和特别行政区的情况(台湾除外)
		$zhixiashi = array('上海','北京','天津','重庆','香港','台湾','澳门');//直辖市
		$city = self::utf8Substr($str, 0,2);
		if(in_array($city, $zhixiashi)){
			if(key_exists($city, $regions['p'])){
				$res['province'] = $regions['p'][$city];//找到后直接返回
				$res['city'] = $regions['c'][$city];//找到后直接返回
				return $res;
			}
		}
		//处理正常的带省/市的地区
		//吉林省长春市
		$arr = explode('省', $str);
		if(count($arr)>=2)
		{
			if(key_exists($arr[0], $regions['p'])){
				$res['province'] = $regions['p'][$arr[0]];
			}
			$arr1 = explode('市', $arr[1]);
			if(count($arr1)>=2)
			{
				if(key_exists($arr1[0], $regions['c'])){
					$res['city'] = $regions['c'][$arr1[0]];
					return $res;
				}
			}
			$arr1 = explode('州', $arr[1]);
			if(count($arr1)>=2)
			{
				if(key_exists($arr1[0], $regions['c'])){
					$res['city'] = $regions['c'][$arr1[0]];
					return $res;
				}
			}
			$arr1 = explode('地区', $arr[1]);
			if(count($arr1)>=2)
			{
				if(key_exists($arr1[0], $regions['c'])){
					$res['city'] = $regions['c'][$arr1[0]];
					return $res;
				}
			}
			return $res;
		}
		else 
		{
			//处理自治区的情况
			$zizhiqu = array('广西','宁夏','新疆','西藏');//自治区
			$ishave = false;
			if(in_array($city, $zizhiqu)){
				if(key_exists($city, $regions['p'])){
					$res['province'] = $regions['p'][$city];//找到后直接返回
					$ishave = true;
				}
			}
			if(!$ishave)
			{
				$city = self::utf8Substr($str, 0,3);
				if($city=='内蒙古'){
					$res['province'] = $regions['p'][$city];//找到后直接返回
					$ishave = true;
				}
			}
			if($ishave)
			{
				$str = str_replace($city, '', $str);
				$arr = explode('市', $str);
				if(count($arr)>=2)
				{
					$city = $arr[0];
					if(key_exists($city, $regions['c']))
					{
						$res['city'] = $regions['c'][$city]; 
						return $res;
					}
				}
				$arr = explode('州', $str);//地区
				if(count($arr)>=2)
				{
					$city = $arr[0];
					if(key_exists($city, $regions['c']))
					{
						$res['city'] = $regions['c'][$city]; 
						return $res;
					}
				}
				$arr = explode('地区', $str);
				if(count($arr)>=2)
				{
					$city = $arr[0];
					if(key_exists($city, $regions['c']))
					{
						$res['city'] = $regions['c'][$city]; 
						return $res;
					}
				}
				return $res;//找到后返回，不循环了
			}
		}
		return $res;
	}
	/**
	 * 获取省、市、县信息（带缓存）
     * @deprecated since version yii
	 */
	public static function getRegions()
	{
		$keyCache = '---getRegions---';
		$res = Yii::app()->cache->fetch($keyCache);
		if($res!==FALSE)
		{
			return $res;
		}
		else 
		{
			$sql = 'SELECT REGION_ID,REGION_NAME,PARENT_ID,REGION_TYPE FROM ECS_REGION';	
			$res = Yii::app()->dbc->getAll($sql);
			$result =array();
			foreach ($res as $key=>$v){
				switch ($v['region_type']){
					case 1:
						$result['p'][$v['region_name']] = $v['region_id'];
						break;
					case 2:
						$result['c'][$v['region_name']] = $v['region_id'];
						break;
				}
			}
			Yii::app()->cache->save($keyCache,$result,600);
			return $result;
		}
	}
	/**
     * 根据所给 IP 地址或域名返回所在地区信息
     *
     * @access public
     * @param string $ip 可为空，为空时自动获取本机IP
     * @return array
     * @deprecated since version yii
     */
	public static function get_Location($ip='')
	{
		$ipaddress = new cls_iplocation();
		return $ipaddress->getlocation($ip);
	}

	/**
	 * 获取暂存盒的HTML
	 * @param int $id
	 * @param int $cachetime
     * @deprecated since version yii
	 */
	public static function get_cache_html($id,$usecache = 0, $time = MEM_ORACLE_TIMEOUT){
		$sql = 'SELECT CACHE_OUT_HTML FROM CMS_CACHE WHERE ID=:ID';
		Yii::app()->dbc-> bind('ID', $id);
		$html = Yii::app()->dbc->getOne($sql,$usecache,$time);
//		$html = Yii::app()->dbc->getOne($sql);
		return $html;
	}
	/**
	 * 根据暂存盒生成html代码
	 * @param int $id 暂存盒ID
     * @deprecated since version yii
	 */
	public static function create_html($id){
		$sql = 'SELECT COUNT_NUM,CACHE_HTML FROM CMS_CACHE WHERE ID=:ID AND IS_DELETED=0';
		Yii::app()->dbc-> bind('ID', $id);
		$source = Yii::app()->dbc->getRow($sql);
		if(empty($source)){
			return false;
		}
		$count = $source['count_num'];
				
		$sql = 'SELECT * FROM(
					SELECT A.*,ROWNUM AS ROW_NUMBER FROM (
						SELECT * FROM CMS_CACHE_CONTENT WHERE CACHE_ID=:ID AND IS_DELETED=0
						ORDER BY SORT_ORDER DESC
					  ) A
					)
					';
		if($count>0){
			$sql .=' WHERE ROW_NUMBER<=:ROW_NUMBER';
			Yii::app()->dbc-> bind('ROW_NUMBER', $count);
		}
		Yii::app()->dbc-> bind('ID', $id);
		$list = Yii::app()->dbc->getAll($sql);
		
		$GLOBALS['smarty']->assign('list',$list);
		$source = $GLOBALS['smarty']->create_html($source['cache_html']);
		$sql = 'UPDATE CMS_CACHE SET CACHE_OUT_HTML=:HTML WHERE ID=:ID';
		Yii::app()->dbc-> bind('ID', $id);
		Yii::app()->dbc-> bind('HTML', $source);
		Yii::app()->dbc->query($sql);
		
		return true;		
	}
	
	/**
	 * 短信发送
	 * @param array $mobiles 手机号码数组
	 * @param string $content 手机内容
     * @param string $type 发送短信的类型
     * 
	 * @return array 返回发送结果[status]=true为成功 $result= array("status" => false,"msg" => "发送失败");
     * @deprecated since version yii
	 */
	public static function send_sms($mobiles,$content,$type='团购后台',$max=2)
	{
		return ECommon::sendSms($mobiles, $content, $type, $max);
	}
	
	/**
	 * 发送邮件
	 * $mail_title:邮件主题
	 * $mail_content:邮件内容
	 * $mail_to:收件人
	 * Enter description here ...
	 * @param $mail_title
     * 
     * @deprecated since version yii
	 */
	public static function send_email($mail_title,$mail_content,$mail_to){
        return ECommon::sendMail($mail_title, $mail_content, $mail_to);
	}

	/**
	 * 记录管理员的操作内容
	 *
	 * @access  public
	 * @param   string      $sn         数据的唯一值
	 * @param   string      $action     操作的类型
	 * @param   string      $content    操作的内容
	 * @return  void
     * 
     * @deprecated since version yii
	 */
	public static function admin_log($sn = '', $action, $content) {
		$log_info = $GLOBALS['_LANG']['log_action'][$action] . $GLOBALS['_LANG']['log_action'][$content] . ': ' . addslashes($sn);
		// modified by huangyu :  改用Oracle
		/*$sql = 'INSERT INTO ' . Yii::app()->ecs->table('admin_log') . ' (log_time, user_id, log_info, ip_address) ' .
		 " VALUES ('" . gmtime() . "', $_SESSION[admin_id], '" . stripslashes($log_info) . "', '" . real_ip() . "')";
		 Yii::app()->db->query($sql);*/
		$sql = 'INSERT INTO ' . Yii::app()->ecs -> table_oci('admin_log') . ' (log_id,log_time, user_id, log_info, ip_address) ' . " VALUES (seq_ecs_admin_log_log_id.nextval," . gmtime() . ", $_SESSION[admin_id], '" . stripslashes($log_info) . "', '" . real_ip() . "')";
		Yii::app()->dbc -> query($sql);
	}

	/**
	 * 判断管理员对某一个操作是否有权限。
	 *
	 * 根据当前对应的action_code，然后再和用户session里面的action_list做匹配，以此来决定是否可以继续执行。
	 * @param     string    $priv_str    操作对应的priv_str
	 * @param     string    $msg_type       返回的类型
	 * @return true/false
     * @deprecated since version yii
	 */
	public static function admin_priv($priv_str, $msg_type = '', $msg_output = true) {
		return true;
	}
	
    /**
     * 
     * @global type $_LANG
     * @param type $priv_str
     * @param type $msg_type
     * @param type $msg_output
     * @return boolean
     * @deprecated since version yii
     */
	public static function admin_priv_new($priv_str, $msg_type = '', $msg_output = true) {
		global $_LANG;

		if ($_SESSION['action_list'] == 'all') {
			return true;
		}
		if (strpos(',' . $_SESSION['action_list'] . ',', ',' . $priv_str . ',') === false) {
			$link[] = array(
				'text' => $_LANG['go_back'],
				'href' => 'javascript:history.back(-1)'
			);
			if ($msg_output) {
				self::sys_msg($_LANG['priv_error'], 0, $link);
			}
			return false;
		} else {
			return true;
		}
	}

	/**
	 * 判断管理员有无此权限(返回JSON)
	 * @param string $authz
     * @deprecated since version yii
	 */
	public static function check_authz_json($authz) {
		return true;
		if (!self::check_authz($authz)) {
			$res = array(
				'error' => $error,
				'message' => $GLOBALS['_LANG']['priv_error'],
				'content' => $content
			);
			if (!empty($append)) {
				foreach ($append AS $key => $val) {
					$res[$key] = $val;
				}
			}
			$val = json_encode($res);

			exit($val);
		}
	}

	/**
	 * 检查管理员权限
	 *
	 * @access  public
	 * @param   string  $authz
	 * @return  boolean
     * @deprecated since version yii
	 */
	public static function check_authz($authz) {
		return (preg_match('/,*' . $authz . ',*/', $_SESSION['action_list']) || $_SESSION['action_list'] == 'all');
	}

	/**
	 * 获取指定主题某个模板的主题的动态模块
	 *
	 * @access  public
	 * @param   string       $theme    模板主题
	 * @param   string       $tmp      模板名称
	 *
	 * @return array()
     * @deprecated since version yii
	 */
	public static function get_dyna_libs($theme, $tmp) {
		$cache = Yii::app()->cache;
		$ext = end(explode('.', $tmp));
		$tmp = basename($tmp, ".$ext");
		// $sql = 'SELECT region, library, sort_order, id, number, type' .
		// ' FROM ' . Yii::app()->ecs->table('template') .
		// " WHERE theme = '$theme' AND filename = '" . $tmp . "' AND type > 0 AND remarks=''".
		// ' ORDER BY region, library, sort_order';
		// $res = Yii::app()->db->getAll($sql);
		// oracle @author jonah.fu
		$sql = "
		SELECT region, template_library, sort_order, id, template_number, template_type
		FROM " . Yii::app()->ecs -> table_oci('template') . "
		WHERE theme = '$theme' AND filename = '$tmp'  AND template_type > 0 AND remarks=''
		ORDER BY region, template_library, sort_order";
		// 打开缓存
		$res = Yii::app()->dbc -> getAll(strtoupper($sql), 1);

		$dyna_libs = array();
		foreach ($res AS $row) {
			$dyna_libs[$row['region']][$row['template_library']][] = array(
				'id' => $row['id'],
				'number' => $row['template_number'],
				'type' => $row['template_type']
			);
		}

		return $dyna_libs;
	}

	/**
	 * 取得上次的过滤条件
	 * @param   string  $param_str  参数字符串，由list函数的参数组成
	 * @return  如果有，返回array('filter' => $filter, 'sql' => $sql)；否则返回false
	 */
	public static function get_filter($param_str = '') {
		$filterfile = basename(PHP_SELF, '.php');
		if ($param_str) {
			$filterfile .= $param_str;
		}
		if (isset($_GET['uselastfilter']) && isset($_COOKIE['TUANECSCP']['lastfilterfile']) && $_COOKIE['TUANECSCP']['lastfilterfile'] == sprintf('%X', crc32($filterfile))) {
			return array(
				'filter' => unserialize(urldecode($_COOKIE['TUANECSCP']['lastfilter'])),
				'sql' => base64_decode($_COOKIE['TUANECSCP']['lastfiltersql'])
			);
		} else {
			return false;
		}
	}

	/**
	 * 取得某模板某库设置的数量
	 * @param   string      $template   模板名，如index
	 * @param   string      $library    库名，如recommend_best
	 * @param   int         $def_num    默认数量：如果没有设置模板，显示的数量
	 * @return  int         数量
     * @deprecated since version yii
     */
	public static function get_library_number($library, $template = null) {
		global $page_libs;

		if (empty($template)) {
			$template = basename(PHP_SELF);
			$template = substr($template, 0, strrpos($template, '.'));
		}
		$template = addslashes($template);

		static $lib_list = array();

		/* 如果没有该模板的信息，取得该模板的信息 */
		if (!isset($lib_list[$template])) {
			$lib_list[$template] = array();

			/*
			 $sql = "SELECT library, number FROM " . Yii::app()->ecs->table('template') .
			 " WHERE theme = '" . $GLOBALS['_CFG']['template'] . "'" .
			 " AND filename = '$template' AND remarks='' ";
			 $res = Yii::app()->db->query($sql);*/
			// oracle   @author Jonah.Fu    @date   2012-03-21

			$sql = "SELECT TEMPLATE_LIBRARY,TEMPLATE_NUMBER FROM " . Yii::app()->ecs -> table_oci('template') . "
        WHERE THEME = '" . $GLOBALS['_CFG']['template'] . "'" . " AND FILENAME = '$template' AND REMARKS='' ";

			$res = Yii::app()->dbc -> query($sql);
			// while ($row = Yii::app()->db->fetchRow($res))
			while ($row = Yii::app()->dbc -> fetchRow($res)) {
				// $lib = basename(strtolower(substr($row['library'], 0, strpos($row['library'], '.'))));
				// $lib_list[$template][$lib] = $row['number'];
				$lib = basename(strtolower(substr($row['LIBRARY'], 0, strpos($row['LIBRARY'], '.'))));
				$lib_list[$template][$lib] = $row['NUMBER'];
			}
		}

		$num = 0;
		if (isset($lib_list[$template][$library])) {
			$num = intval($lib_list[$template][$library]);
		} else {
			/* 模板设置文件查找默认值 */
			// include_once (ROOT_PATH . ADMIN_PATH . '/includes/lib_template.php');
			static $static_page_libs = null;
			if ($static_page_libs == null) {
				$static_page_libs = $page_libs;
			}
			$lib = '/library/' . $library . '.lbi';

			$num = isset($static_page_libs[$template][$lib]) ? $static_page_libs[$template][$lib] : 3;
		}

		return $num;
	}

	/**
	 * 取得用户等级数组,按用户级别排序
	 * @param   bool      $is_special      是否只显示特殊会员组
	 * @return  array     rank_id=>rank_name
     * @deprecated since version yii
	 */
	public static function get_rank_list($is_special = false) {
		$rank_list = array();
		// modified by huangyu :  改用Oracle
		//$sql = 'SELECT rank_id, rank_name, min_points FROM ' . Yii::app()->ecs->table('user_rank');
		$sql = 'SELECT rank_id, rank_name, min_points FROM ' . Yii::app()->ecs -> table_oci('user_rank');
		if ($is_special) {
			$sql .= ' WHERE special_rank = 1';
		}
		$sql .= ' ORDER BY min_points';
		// modified by huangyu :  改用Oracle
		//$res = Yii::app()->db->query($sql);
		$res = Yii::app()->dbc -> query($sql);
		// modified by huangyu :  改用Oracle
		/*
		 while ($row = Yii::app()->db->fetchRow($res))
		 {
		 $rank_list[$row['rank_id']] = $row['rank_name'];
		 }*/
		while ($row = Yii::app()->dbc -> fetchRow($res)) {
			$rank_list[$row['rank_id']] = $row['rank_name'];
		}
		return $rank_list;
	}

	/**
	 * 分配帮助信息
	 *
	 * @access  public
	 * @return  array
     * @deprecated since version yii
	 */
	public static function get_shop_help() {
		$sql = '
		SELECT c.cat_id, c.cat_name, c.sort_order, a.article_id, a.title, a.file_url, a.open_type
		FROM ' . Yii::app()->ecs -> table_oci('article') . ' a
		LEFT JOIN ' . Yii::app()->ecs -> table_oci('article_cat') . ' c ON a.cat_id = c.cat_id
		WHERE c.cat_type = 5 AND a.is_open = 1
		ORDER BY c.sort_order ASC, a.article_id';
		$res = Yii::app()->dbc -> getAll($sql, 1);

		$arr = array();
		foreach ($res AS $key => $row) {
			$arr[$row['cat_id']]['cat_id'] = base_common::build_uri('article_cat', array('acid' => $row['cat_id']), $row['cat_name']);
			$arr[$row['cat_id']]['cat_name'] = $row['cat_name'];
			$arr[$row['cat_id']]['article'][$key]['article_id'] = $row['article_id'];
			$arr[$row['cat_id']]['article'][$key]['title'] = $row['title'];
			$arr[$row['cat_id']]['article'][$key]['short_title'] = $GLOBALS['_CFG']['article_title_length'] > 0 ? static_base::sub_str($row['title'], $GLOBALS['_CFG']['article_title_length']) : $row['title'];
			$arr[$row['cat_id']]['article'][$key]['url'] = $row['open_type'] != 1 ? base_common::build_uri('article', array('aid' => $row['article_id']), $row['title']) : trim($row['file_url']);
		}

		return $arr;
	}

	/**
	 * 查询会员的红包金额
	 *
	 * @access  public
	 * @param   integer     $user_id
	 * @return  void
     * @deprecated since version yii
	 */
	public static function get_user_bonus($user_id = 0) {
		if ($user_id == 0) {
			$user_id = $_SESSION['user_id'];
		}
		Yii::app()->dbc -> Binds = array();
		Yii::app()->dbc -> bind('user_id', $user_id * 1);
		$sql = "
		SELECT SUM(bt.type_money) AS bonus_value, COUNT(*) AS bonus_count
		FROM " . Yii::app()->ecs -> table_oci('user_bonus') . " ub, " . Yii::app()->ecs -> table_oci('bonus_type') . " bt " . "
		WHERE ub.user_id = :user_id AND ub.bonus_type_id = bt.type_id AND ub.order_id = 0";
		$row = Yii::app()->dbc -> getRow($sql);

		return $row;
	}

	/**
	 *  获取用户信息数组
	 *
	 * @access  public
	 * @param
	 *
	 * @return array        $user       用户信息数组
     * @deprecated since version yii
	 */
	public static function get_user_info_oci($id = 0) {
		if ($id == 0) {
			$id = $_SESSION['user_id'];
		}
		$time = date('Y-m-d');
		$sql = 'SELECT u.user_id, u.email, u.user_name,u.mobile_phone, u.user_money, u.pay_points' . ' FROM ' . Yii::app()->ecs -> table_oci('users') . ' u ' . " WHERE u.user_id = '$id'";
		$user = Yii::app()->dbc -> getRow($sql);
		$bonus = self::get_user_bonus($id);

		if (!empty($user['user_name'])) {
			$user['username'] = $user['user_name'];
		} else if (!empty($user['mobile_phone'])) {
			$user['username'] = $user['mobile_phone'];
		} else {
			$user['username'] = $user['email'];
		}
		//$user['username']    = $user['user_name'];
		$user['user_points'] = $user['pay_points'] . $GLOBALS['_CFG']['integral_name'];
		$user['user_money'] = base_common::price_format($user['user_money'], false);
		$user['user_bonus'] = base_common::price_format($bonus['BONUS_VALUE'], false);

		return $user;
	}

	/**
	 * 调用调查内容
	 *
	 * @access  public
	 * @param   integer $id   调查的编号
	 * @return  array
     * @deprecated since version yii
	 */
	public static function get_vote($id = '') {
		/* 随机取得一个调查的主题 */
		if (empty($id)) {
			$time = static_time::gmtime();
			/*
			 $sql = 'SELECT vote_id, vote_name, can_multi, vote_count, RAND() AS rnd' .
			 ' FROM ' . Yii::app()->ecs->table('vote') .
			 " WHERE start_time <= '$time' AND end_time >= '$time' ".
			 ' ORDER BY rnd LIMIT 1';*/
			// oracle @author Jonah.Fu @date 2012-03-22
			$sql = 'select vote_id, vote_name, can_multi, vote_count from (SELECT vote_id, vote_name, can_multi, vote_count' . ' FROM ' . Yii::app()->ecs -> table_oci('vote') . " WHERE start_time <= $time AND end_time >= $time" . ' order by dbms_random.value) where rownum=1';
		} else {
			$sql = 'SELECT vote_id, vote_name, can_multi, vote_count' . ' FROM ' . Yii::app()->ecs -> table('vote') . " WHERE vote_id = '$id'";
		}
		// $vote_arr = Yii::app()->db->getRow($sql);
		$vote_arr = Yii::app()->dbc -> getRow(strtoupper($sql));

		if ($vote_arr !== false && !empty($vote_arr)) {
			/* 通过调查的ID,查询调查选项 */
			$sql_option = 'SELECT v.*, o.option_id, o.vote_id, o.option_name, o.option_count ' . 'FROM ' . Yii::app()->ecs -> table('vote') . '  v, ' . Yii::app()->ecs -> table('vote_option') . '  o ' . "WHERE o.vote_id = v.vote_id AND o.vote_id = '$vote_arr[vote_id]' ORDER BY o.option_order ASC, o.option_id DESC";
			$res = Yii::app()->db -> getAll($sql_option);

			/* 总票数 */
			$sql = 'SELECT SUM(option_count) AS all_option FROM ' . Yii::app()->ecs -> table('vote_option') . " WHERE vote_id = '" . $vote_arr['vote_id'] . "' GROUP BY vote_id";
			$option_num = Yii::app()->db -> getOne($sql);

			$arr = array();
			$count = 100;
			foreach ($res AS $idx => $row) {
				if ($option_num > 0 && $idx == count($res) - 1) {
					$percent = $count;
				} else {
					$percent = ($row['vote_count'] > 0 && $option_num > 0) ? round(($row['option_count'] / $option_num) * 100) : 0;

					$count -= $percent;
				}
				$arr[$row['vote_id']]['options'][$row['option_id']]['percent'] = $percent;

				$arr[$row['vote_id']]['vote_id'] = $row['vote_id'];
				$arr[$row['vote_id']]['vote_name'] = $row['vote_name'];
				$arr[$row['vote_id']]['can_multi'] = $row['can_multi'];
				$arr[$row['vote_id']]['vote_count'] = $row['vote_count'];

				$arr[$row['vote_id']]['options'][$row['option_id']]['option_id'] = $row['option_id'];
				$arr[$row['vote_id']]['options'][$row['option_id']]['option_name'] = $row['option_name'];
				$arr[$row['vote_id']]['options'][$row['option_id']]['option_count'] = $row['option_count'];
			}

			$vote_arr['vote_id'] = (!empty($vote_arr['vote_id'])) ? $vote_arr['vote_id'] : '';

			$vote = array(
				'id' => $vote_arr['vote_id'],
				'content' => $arr
			);

			return $vote;
		}
	}

	/**
	 * 自动动态地将后台添加的图片地址添加上域名路径
	 * @param string $str
     * @deprecated since version yii
	 */
	public static function img_url($str) {
		return ECommon::imgUrl($str);
	}
    /**
     * 获取图片的小图地址
     * 
     * @param string $url
     * @return string
     * @deprecated since version yii
     */
    public static function img_flash_url($url) {
        if (empty($url)) {
            return '';
        }
        if (strpos($url, 'http://') !== false) {
            $url_info = parse_url($url);
            $path = $url_info['path'];
            $server = "{$url_info['scheme']}://{$url_info['host']}";
        } else {
            $server = $GLOBALS['img_servers'][array_rand($GLOBALS['img_servers'])];
        }
        
        $info = pathinfo($path);
        return "$server/{$info['dirname']}/{$info['filename']}_300225.{$info['extension']}";
    }

	/**
	 * 授权信息内容
	 *
	 * @return  str
     * @deprecated since version yii
	 */
	public static function license_info() {
		/*
		 if ($GLOBALS['_CFG']['licensed'] > 0) {
		 // 获取HOST
		 if (isset($_SERVER['HTTP_X_FORWARDED_HOST'])) {
		 $host = $_SERVER['HTTP_X_FORWARDED_HOST'];
		 } elseif (isset($_SERVER['HTTP_HOST'])) {
		 $host = $_SERVER['HTTP_HOST'];
		 }
		 //$license = '<a href="http://license.comsenz.com/?pid=4&host='. $host .'">Licensed</a>';
		 $host = 'http://' . $host . '/';
		 $license = '<a href="http://service.shopex.cn/auth.php?product=ecshop&url=' . urlencode($host) . '">Licensed</a>';
		 return $license;
		 } else {
		 return '';
		 }*/
		return '';
	}

	/**
	 * 创建一个JSON格式的数据
	 *
	 * @access  public
	 * @param   string      $content
	 * @param   integer     $error
	 * @param   string      $message
	 * @param   array       $append
	 * @return  void
	 */
	public static function make_json_response($content = '', $error = "0", $message = '', $append = array()) {
		// include_once(ROOT_PATH . 'includes/cls_json.php');

		// $json = new JSON;

		$res = array(
			'error' => $error,
			'message' => $message,
			'content' => $content
		);

		if (!empty($append)) {
			foreach ($append AS $key => $val) {
				$res[$key] = $val;
			}
		}

		// $val = $json->encode($res);
		// @author jonah.fu @date 2012-03-30
		$val = json_encode($res);

		exit($val);
	}

	/**
	 *
	 *
	 * @access  public
	 * @param
	 * @return  void
	 */
	public static function make_json_result($content, $message = '', $append = array()) {
		base_main::make_json_response($content, 0, $message, $append);
	}

	/**
	 * 显示一个提示信息
	 *
	 * @access  public
	 * @param   string  $content
	 * @param   string  $link
	 * @param   string  $href
	 * @param   string  $type               信息类型：warning, error, info
	 * @param   string  $auto_redirect      是否自动跳转
	 * @return  void
     * @deprecated since version yii
	 */
	public static function show_message($content, $links = '', $hrefs = '', $type = 'info', $auto_redirect = true) {
		self::assign_template();

		$msg['content'] = $content;
		if (is_array($links) && is_array($hrefs)) {
			if (!empty($links) && count($links) == count($hrefs)) {
				foreach ($links as $key => $val) {
					$msg['url_info'][$val] = $hrefs[$key];
				}
				$msg['back_url'] = $hrefs['0'];
			}
		} else {
			$link = empty($links) ? $GLOBALS['_LANG']['back_up_page'] : $links;
			$href = empty($hrefs) ? 'javascript:history.back()' : $hrefs;
			$msg['url_info'][$link] = $href;
			$msg['back_url'] = $href;
		}

		$msg['type'] = $type;
		$position = self::assign_ur_here(0, $GLOBALS['_LANG']['sys_msg']);
		$GLOBALS['smarty'] -> assign('page_title', $position['title']);
		// 页面标题
		$GLOBALS['smarty'] -> assign('ur_here', $position['ur_here']);
		// 当前位置

		if (is_null($GLOBALS['smarty'] -> get_template_vars('helps'))) {
			$GLOBALS['smarty'] -> assign('helps', self::get_shop_help());
			// 网店帮助
		}

		$GLOBALS['smarty'] -> assign('auto_redirect', $auto_redirect);
		$GLOBALS['smarty'] -> assign('message', $msg);
		$GLOBALS['smarty'] -> display('message.dwt');

		exit ;
	}
    
    public static function strlen($str) {
        return ECommon::utf8Strlen($str);
    }
    
    /**
     * 计算字符串的字节数
     * 
     * @param string $str
     * @return int
     */
    public static function str_bytes($str) {
        return mb_strlen($str, '8bit');
    }
    
    /**
     * 字符串截取
     * @param string $str
     * @param int $start
     * @param int $length
     * @param boolean $ellipsis 显示省略号
     * 
     * @return string
     */
    public static function substr($str, $start, $length = null, $ellipsis = false) {
        return ECommon::substr($str, $start, $length, $ellipsis);
    }
    
    /**
     * 生成随机字符串
     * @param int $count
     */
    public static function str_rand($count = 4) {
        $range = array_merge(range('a', 'z'), range('A', 'Z'), range(0, 9));
        $str = '';
        $indexes = array_rand($range, $count);
        
        foreach ($indexes as $item) {
            $str = chr($range[$item]);
        }
        return $str;
    }

}
?>