<?php
if (!defined("DEV_ENVIRONMENT")) {
    define('DEV_ENVIRONMENT', isset($_SERVER['APP_ENV'])? $_SERVER['APP_ENV'] : 'dev');
}
switch(DEV_ENVIRONMENT){
	case 'dev':
		defined('DOMAIN_WWW') || define('DOMAIN_WWW', 'http://www.ec.com/');
		defined('DOMAIN_USER') || define('DOMAIN_USER', 'http://home.ec.com/');
		defined('DOMAIN_CART') || define('DOMAIN_CART', 'http://cart.ec.com/');
		defined('DOMAIN_TUAN') || define('DOMAIN_TUAN', 'http://tg.ec.com/');
		defined('DOMAIN_SEARCH') || define("DOMAIN_SEARCH", 'http://search.ec.com/');
		defined('DOMAIN_MAI') || define("DOMAIN_MAI", 'http://mai.ec.com/');
		defined('DOMAIN_ZIXUN') || define("DOMAIN_ZIXUN", 'http://zixun.ec.com/');
		defined('DOMAIN_PASSPORT') || define("DOMAIN_PASSPORT", 'http://passport.ec.com/');
		defined('DATAJS_DOMAIN1') || define('DATAJS_DOMAIN1', 'http://datajs01.rscdn.com');
		defined('CSS_DOMAIN1') || define('CSS_DOMAIN1', 'http://csstest01.rscdn.com');
		defined('CSS_DOMAIN2') || define('CSS_DOMAIN2', 'http://csstest02.rscdn.com');
		defined('CSS_DOMAIN3') || define('CSS_DOMAIN3', 'http://csstest03.rscdn.com');
		defined('JS_DOMAIN1') || define('JS_DOMAIN1', 'http://jstest01.rscdn.com');
		defined('CSS_VERSION') || define('CSS_VERSION', '20140520');
		defined('JS_VERSION') || define('JS_VERSION', '20140520');
		break;
	case 'test':
		defined('DOMAIN_WWW') || define('DOMAIN_WWW', 'http://wwwtest.mmall.com/');
		defined('DOMAIN_USER') || define('DOMAIN_USER', 'http://hometest.mmall.com/');
		defined('DOMAIN_CART') || define('DOMAIN_CART', 'http://carttest.mmall.com/');
		defined('DOMAIN_TUAN') || define('DOMAIN_TUAN', 'http://tgtest.mmall.com/');
		defined('DOMAIN_SEARCH') || define("DOMAIN_SEARCH", 'http://searchtest.mmall.com/');
		defined('DOMAIN_MAI') || define("DOMAIN_MAI", 'http://maitest.mmall.com/');
		defined('DOMAIN_ZIXUN') || define("DOMAIN_ZIXUN", 'http://zixuntest.mmall.com/');
		defined('DOMAIN_PASSPORT') || define("DOMAIN_PASSPORT", 'http://passporttest.mmall.com/');
		defined('DATAJS_DOMAIN1') || define('DATAJS_DOMAIN1', 'http://datajs01.rscdn.com');
		defined('CSS_DOMAIN1') || define('CSS_DOMAIN1', 'http://csstest01.rscdn.com');
		defined('CSS_DOMAIN2') || define('CSS_DOMAIN2', 'http://csstest02.rscdn.com');
		defined('CSS_DOMAIN3') || define('CSS_DOMAIN3', 'http://csstest03.rscdn.com');
		defined('JS_DOMAIN1') || define('JS_DOMAIN1', 'http://jstest01.rscdn.com');
		defined('CSS_VERSION') || define('CSS_VERSION', '20140520');
		defined('JS_VERSION') || define('JS_VERSION', '20140520');
		break;
	case 'pro':
		defined('DOMAIN_WWW') || define('DOMAIN_WWW', 'http://www.mmall.com/');
		defined('DOMAIN_USER') || define('DOMAIN_USER', 'http://home.mmall.com/');
		defined('DOMAIN_CART') || define('DOMAIN_CART', 'http://cart.mmall.com/');
		defined('DOMAIN_TUAN') || define('DOMAIN_TUAN', 'http://tg.mmall.com/');
		defined('DOMAIN_SEARCH') || define("DOMAIN_SEARCH", 'http://search.mmall.com/');
		defined('DOMAIN_MAI') || define("DOMAIN_MAI", 'http://mai.mmall.com/');
		defined('DOMAIN_ZIXUN') || define("DOMAIN_ZIXUN", 'http://zixun.mmall.com/');
		defined('DOMAIN_PASSPORT') || define("DOMAIN_PASSPORT", 'http://passport.mmall.com/');
		defined('DATAJS_DOMAIN1') || define('DATAJS_DOMAIN1', 'http://js10.homemall.com.cn');
		defined('CSS_DOMAIN1') || define('CSS_DOMAIN1', 'http://css01.homemall.com.cn');
		defined('CSS_DOMAIN2') || define('CSS_DOMAIN2', 'http://css02.homemall.com.cn');
		defined('CSS_DOMAIN3') || define('CSS_DOMAIN3', 'http://css03.homemall.com.cn');
		defined('JS_DOMAIN1') || define('JS_DOMAIN1', 'http://js01.homemall.com.cn');
		defined('CSS_VERSION') || define('CSS_VERSION', '20140520');
		defined('JS_VERSION') || define('JS_VERSION', '20140520');
}
?>
<meta property="qc:admins" content="15535460516551446375" />
<meta property="wb:webmaster" content="e70aa9edb6c2ce88" />
<meta http-equiv="window-target" content="_top" />
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta http-equiv="content-language" content="zh-cn"/>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1,requiresActiveX=true" />
<meta http-equiv="imagetoolbar" content="no" />
<meta name="xcode-display" content="render" /> 
<!-- <meta name="viewport" content="width=1200,initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable=no,target-densitydpi=high-dpi" /> -->
<meta name="format-detection" content="telephone=no" />
<meta name="MSSmartTagsPreventParsing" content="true" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta http-equiv="MSThemeCompatible" content="no" />
<meta http-equiv="x-dns-prefetch-control" content="on" />
<link rel="search" type="application/opensearchdescription+xml" href="" title="" />
<link rel="icon" href="/favicon.ico" type="image/x-icon" />
<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
<link rel="apple-touch-icon" href="/favicon_ipad.png" type="image/png" />
<link rel="apple-touch-icon-precomposed" href="/favicon_ipad.png" type="image/png" />
<link rel="canonical" href="" />
<link rel="stylesheet" href="<?php echo CSS_DOMAIN1;?>??/css/global/reset.css,/css/global/base.css?<?php echo CSS_VERSION;?>"/>
<script type="text/javascript" src="<?php echo JS_DOMAIN1;?>??/js/jq-min.js,/js/init.min.js??<?php echo JS_VERSION;?>"></script>
<script type="text/javascript" src="<?php echo JS_DOMAIN1;?>??/js/jquery.plugins/jquery.datalazyload.min.js,/js/jquery.plugins/jquery.cookie.min.js??<?php echo JS_VERSION;?>"></script>
