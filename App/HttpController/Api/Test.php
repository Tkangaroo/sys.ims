<?php
namespace App\HttpController\Api;

use App\Base\BaseController;
use App\Model\IpWhiteListModel;


class Test extends BaseController
{
    /**
     * @return bool|void
     * @throws \Exception
     */
    public function index()
    {
    	$ip = $this->OSDi->get('ESTools')->getClientIp($this->request());
        $this->response()->write('index action for api:'.$ip);
    }
}