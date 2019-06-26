<?php
namespace App\HttpController\Api;

use App\Base\BaseController;
use App\Model\IpWhiteListModel;


class Test extends BaseController
{
    /**
     * @return bool|void
     * @throws \Throwable
     */
    public function index()
    {
    	$ip = $this->Di->get('ESTools')->getClientIp($this->request());
        $this->response()->write('index action for api:'.$ip);
    }
}