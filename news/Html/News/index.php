<?php
define('IN_PRODUCTION', true);
define('PRODUCTION_ROOT', dirname(dirname(__DIR__)));
//应用配置
define('APP_NAME', substr(strrchr(__DIR__, DIRECTORY_SEPARATOR), 1));// 配置是哪个实
require_once('/www/QFramework/init.php');
Q_Controller_Front::run();
