<?php
namespace App\HttpController\Api;

use EasySwoole\Http\AbstractInterface\Controller;
use App\Model\IpWhiteListModel;


class Index extends Controller
{
    public function index()
    {
    	(new IpWhiteListModel)->getTest();
        $this->response()->write('index action for api');
    }

    public function test()
    {
        $this->response()->write('this a test for api');
    }
}