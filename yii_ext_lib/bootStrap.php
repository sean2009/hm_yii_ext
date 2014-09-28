<?php
/**
 * 启动脚本
 * @author xiaopeng <zhangzenglun@163.com> 2013-7-25
 * @link http://www.mmall.com
 * @copyright Copyright &copy;
 * @license
 */

if(YII_DEBUG === true){
	error_reporting(E_ERROR | E_WARNING);
}else{
	error_reporting(0);
}


/**
 * @var string 网站根目录定义
 */
if (!defined("YII_ROOT_PATH")) {
	define('YII_ROOT_PATH',dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR);
}
/**
 * @var string 网站运行环境
 * dev 开发环境
 * test 测试环境
 * pro 生产环境
 */
if (!defined("DEV_ENVIRONMENT")) {
    define('DEV_ENVIRONMENT', isset($_SERVER['APP_ENV'])? $_SERVER['APP_ENV'] : 'dev');
}

//监控系统开始

defined("YII_IS_CLIENT") or define('YII_IS_CLIENT',true);
	
require dirname(__FILE__) . '/common_service/MonitoringService.php';
register_shutdown_function(array(MonitoringService::getInstance(), 'end'));

//监控系统结束

require(YII_ROOT_PATH.'config/'.DEV_ENVIRONMENT.'/public_config.php');	
// include Yii bootstrap file
require(YII_ROOT_PATH.'framework/yii.php');

Yii::setPathOfAlias('yii_ext_lib', YII_ROOT_PATH.'yii_ext_lib');
yii::import('yii_ext_lib.base.*');
yii::import('yii_ext_lib.db.*');
yii::import('yii_ext_lib.library.*');
yii::import('yii_ext_lib.util.*');


//是否需要兼容老的ecshop框架
if(defined('IF_ECSHOP') && IF_ECSHOP === true){
	Yii::setPathOfAlias('ecshop_lib', YII_ROOT_PATH.'ecshop_framework');
	yii::import('ecshop_lib.base.*');
	yii::import('ecshop_lib.cls.*');
}