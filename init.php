<?php
/**
 * Created by PhpStorm.
 * User: speauty
 * Date: 2019/6/21
 * Time: 17:50
 */
use Lib\OSDi;

define('DS', '/');
define('ROOT_PATH',str_replace('\\','/',realpath(dirname(__FILE__).'/')));

spl_autoload_register(['Lib\AutoLoad', 'load']);

(new OSDi)->test();
