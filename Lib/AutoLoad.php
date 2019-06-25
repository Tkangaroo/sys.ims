<?php
/**
 * Created by PhpStorm.
 * User: speauty
 * Date: 2019/6/21
 * Time: 18:00
 */

namespace Lib;


class AutoLoad
{

    static public function load($class)
    {
        var_dump($class);
        require_once ROOT_PATH.DS.str_replace('\\', '/', $class).'.php';
    }
}
