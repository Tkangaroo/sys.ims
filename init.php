<?php
/**
 * Created by PhpStorm.
 * User: speauty
 * Date: 2019/6/21
 * Time: 17:50
 */

defined('DS') or define('DS', '/');
defined('ROOT_PATH') or define('ROOT_PATH', IN_PHAR ? \Phar::running() : realpath(getcwd()));
defined('LIB_PATH') or define('LIB_PATH', ROOT_PATH.DS.'Lib');

// 注册类自动加载静态方法
require_once(ROOT_PATH.'/Lib/AutoLoad.php');
spl_autoload_register(['Lib\AutoLoad', 'load']);
