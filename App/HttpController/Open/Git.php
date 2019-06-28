<?php
/**
 * Created by PhpStorm.
 * User: speauty
 * Date: 2019/6/28
 * Time: 9:23
 */

namespace App\HttpController\Open;


use App\Base\BaseController;
use App\Utility\ESTools;

class Git extends BaseController
{
    public function pull()
    {
        var_dump($this->request()->getHeaders());
        var_dump($this->request()->getRequestParam());
    }
}