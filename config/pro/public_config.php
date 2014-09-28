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
define('COOKIE_DOMAIN', '.mmall.com');
define('COOKIE_KEY', 'REDSTARMALL');
define('SESSION_ID_NAME', 'ECS_ID');

/**
 * 定义COOKIE里面的user
 */
define('USER_KEY', 'USER');

/*JS服务器地址*/
define('JS_DOMAIN1', 'http://js01.homemall.com.cn');
define('JS_DOMAIN2', 'http://js02.homemall.com.cn');
define('JS_DOMAIN3', 'http://js01.homemall.com.cn');
define('JS_DOMAIN4', 'http://js02.homemall.com.cn');
define('JS_DOMAIN5', 'http://js01.homemall.com.cn');
/*CSS服务器地址*/
define('CSS_DOMAIN1', 'http://css01.homemall.com.cn');
define('CSS_DOMAIN2', 'http://css02.homemall.com.cn');
define('CSS_DOMAIN3', 'http://css03.homemall.com.cn');
define('CSS_DOMAIN4', 'http://css04.homemall.com.cn');
define('CSS_DOMAIN5', 'http://css05.homemall.com.cn');
define('SAFEIMG_DOMAIN', 'https://safeimg.mmall.com');

/**
 * 项目地址
 */
define('DOMAIN_USER', 'http://home.mmall.com/');
define('DOMAIN_TUAN', 'http://tg.mmall.com/');
define('DOMAIN_SHAN', 'http://shan.mmall.com/');
define('DOMAIN_WWW', 'http://www.mmall.com/');
define('DOMAIN_ZIXUN', 'http://zixun.mmall.com/');
define('DOMAIN_SEARCH','http://search.mmall.com/');
define('DOMAIN_GJ','http://guanjia.mmall.com/');
define('DOMAIN_TOOL', 'http://tools.mmall.com/');
define('DOMAIN_HUI', 'http://hui.mmall.com/');
define('DOMAIN_HY','http://hangye.mmall.com/');
/**
 * cms的JS域名
 */
define('DATAJS_DOMAIN1', 'http://js10.homemall.com.cn');
define('DATAJS_DOMAIN2', 'http://js11.homemall.com.cn');

define('DEBUG_MODE', false);

define('IMG_PATH', '/admin/images/static01/');
define('YAR_API_PROCOTOL', 'yar');

/**
 * 图片服务器配置信息
 * @var Array
 */
$img_servers = array(
	'http://img01.homemall.com.cn/',
	'http://img02.homemall.com.cn/'
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
$auth_view_sql_userids=array(144687);