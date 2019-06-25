<?php
/**
 * Created by PhpStorm.
 * User: speauty
 * Date: 2019/6/21
 * Time: 17:50
 */

defined('ROOT_PATH') or define('ROOT_PATH', IN_PHAR ? \Phar::running() : realpath(getcwd()));
require_once(ROOT_PATH.'/Lib/AutoLoad.php');
spl_autoload_register(['Lib\AutoLoad', 'load']);
