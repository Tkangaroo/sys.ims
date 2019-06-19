<?php
namespace App\HttpController\Api;

use EasySwoole\Http\AbstractInterface\Controller;
use App\Model\IpWhiteListModel;


class Index extends Controller
{
    public function index()
    {
    	$res = (new IpWhiteListModel)->getTest();
        $this->response()->write('index action for api');
        $this->response()->writeJson(200, $res, 'test');
    }

    public function test()
    {
        $this->response()->write('this a test for api');
    }
}