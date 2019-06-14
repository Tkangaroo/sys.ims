<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/14 0014
 * Time: 14:23
 */

namespace App\HttpController\Api;

use EasySwoole\Http\AbstractInterface\Controller;
class Index extends Controller
{
    public function index()
    {
        $this->response()->write('index action for api');
    }

    public function test()
    {
        $this->response()->write('this a test for api');
    }
}