<?php
/**
 * Created by PhpStorm.
 * User: speauty
 * Date: 2019/6/28
 * Time: 9:23
 */

namespace App\HttpController\Open;


use App\Utility\ESTools;

class Git
{
    public function pull()
    {
        $params = ESTools::getArgFromRequest();
        var_dump($params);
    }
}