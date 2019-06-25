<?php
/**
 * Created by PhpStorm.
 * User: speauty
 * Date: 2019/6/21
 * Time: 18:00
 */

namespace Lib;

use EasySwoole\Spl\Exception\Exception;

class AutoLoad
{

    /**
     * @param $class
     * @throws \Exception
     */
    static public function load($class)
    {
        require_once ROOT_PATH.DS.str_replace('\\', DS, $class).'.php';
        unset($class);
    }
}