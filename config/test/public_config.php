<?php
define('EC_CHARSET', 'utf-8');

/**
 * memcached Oracle 数据默认缓存时间
 */
define('MEM_ORACLE_TIMEOUT', 2 * 60);
define('MEM_DEFAULT_TIMEOUT', 2 * 60);

/**
 * cookie time out 10 分钟
 */
define('COOKIE_TIME_OUT', 0);
define('COOKIE_PATH', '/');
define('COOKIE_DOMAIN', '.shop.com');
define('COOKIE_KEY', 'REDSTARMALL');
define('SESSION_ID_NAME', 'ECS_ID');

/**
 * 定义COOKIE里面的user
 */
define('USER_KEY', 'USER');

/*JS服务器地址*/
define('JS_DOMAIN1', 'http://jstest01.rscdn.com');
define('JS_DOMAIN2', 'http://jstest02.rscdn.com');
define('JS_DOMAIN3', 'http://jstest03.rscdn.com');
define('JS_DOMAIN4', 'http://jstest04.rscdn.com');
define('JS_DOMAIN5', 'http://jstest05.rscdn.com');
/*CSS服务器地址*/
define('CSS_DOMAIN1', 'http://csstest01.rscdn.com');
define('CSS_DOMAIN2', 'http://csstest02.rscdn.com');
define('CSS_DOMAIN3', 'http://csstest03.rscdn.com');
define('CSS_DOMAIN4', 'http://csstest04.rscdn.com');
define('CSS_DOMAIN5', 'http://csstest05.rscdn.com');
/**
 * 项目地址
 */
define('DOMAIN_USER', 'http://user.shop.com/');
define('DOMAIN_TUAN', 'http://tuan.ec.com/');
define('DOMAIN_SHAN', 'http://shan.ec.com/');
define('DOMAIN_WWW', 'http://mall.shop.com/');
define('DOMAIN_ZIXUN', 'http://front.shop.com/');
define('DOMAIN_SEARCH','http://localhost:80/mallsearch/');
define('DOMAIN_GJ','http://guanjia.ec.com/');
define('DOMAIN_ZX', 'http://front.shop.com/');
define('DOMAIN_TOOL', 'http://tools.mmall.com/');
define('DOMAIN_HUI', 'http://hui.ec.com/');
define('DOMAIN_HY','http://hangye.shop.com/');
define("DOMAIN_PASSPORT",'http://passport.shop.com/');
define("API_COUPON",'http://servicecoupons.shop.com/api.php');
/**
 * cms的JS域名
 */
define('DATAJS_DOMAIN1', 'http://datajs01.rscdn.com');
define('DATAJS_DOMAIN2', 'http://datajs02.rscdn.com');

define('DEBUG_MODE', false);

define('IMG_PATH', '/admin/images/static01/');
define('YAR_API_PROCOTOL', 'yar');

/**
 * 图片服务器配置信息
 * @var Array
 */
$img_servers = array(
	'http://imgtest01.rscdn.com/',
	'http://imgtest02.rscdn.com/'
);
$zx_img_servers = array(
	'http://img06.homemall.com.cn/infoimages/',
	'http://img07.homemall.com.cn/infoimages/'
);
$dfs_servers = array(
	"group1"=>array("http://img10.homemall.com.cn","http://img11.homemall.com.cn","http://img12.homemall.com.cn"),
	"group2"=>array("http://img10.homemall.com.cn","http://img11.homemall.com.cn","http://img12.homemall.com.cn"),
);
//有权限查看sql页面的user_ids,线上配置成array(144687);
$auth_view_sql_userids=array(1428);