<?php
/**
 * Created by PhpStorm.
 * User: speauty
 * Date: 2019/6/21
 * Time: 17:50
 */

define('DS', '/');
define('ROOT_PATH',dirname(__FILE__));
require_once(ROOT_PATH.'/Lib/Autoload.php');
spl_autoload_register(['Lib\AutoLoad', 'load']);
