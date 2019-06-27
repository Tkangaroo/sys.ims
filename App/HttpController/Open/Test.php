<?php
namespace App\HttpController\Open;

use App\Base\BaseController;
use App\Utility\ESTools;


class Test extends BaseController
{
    /**
     * @return bool|void
     * @throws \Throwable
     */
    public function index()
    {
    	$ip = ESTools::getClientIp($this->request());
        $this->response()->write('index action for api:'.$ip);
    }
}