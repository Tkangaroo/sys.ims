<?php
/**
 * Created by PhpStorm.
 * User: speauty
 * Date: 2019/6/21
 * Time: 17:50
 */

define('DS', '/');
define('ROOT_PATH',str_replace('\\','/',realpath(dirname(__FILE__).'/')));

var_dump(ROOT_PATH);
var_dump(file_exists(ROOT_PATH.'/Lib/Autoload.php'));
die();
require_once ROOT_PATH.'/Lib/Autoload.php';
spl_autoload_register(['Lib\AutoLoad', 'load']);
