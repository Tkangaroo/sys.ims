<?php
namespace App\HttpController\Api;

use App\Model\IpWhiteListModel;
use App\HttpController\BaseController;


class Index extends BaseController
{
    public function index()
    {
    	$this->getClientIp();
    	$res = (new IpWhiteListModel)->getTest();
        $this->response()->write('index action for api');
        $this->writeJson(200, $res, 'test');
    }
}