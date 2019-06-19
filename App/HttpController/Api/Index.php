<?php
namespace App\HttpController\Api;

use App\Model\IpWhiteListModel;
use App\HttpController\BaseController;


class Index extends BaseController
{
    public function index()
    {
    	$ip = $this->getClientIp();
        $this->response()->write('index action for api:'.$ip);
    }
}