<?php
namespace App\HttpController\Admin;

use App\Exception\ESException;
use App\HttpController\BaseController;
use App\Model\IpWhiteListModel;
use App\Validate\IpWhiteValidate;
use App\Utility\Tools\ESResponseTool;
use App\Utility\Tools\ESConfigTool;

class IpWhiteList extends BaseController
{
	/**
	 * 注册白名单
	 */
	public function save()
	{
		$data = $this->request()->getRequestParam('ip_addr', 'is_enable', 'comments');
        $esResponse = new ESResponseTool();
        $conf = new ESConfigTool();
        try {
            (new IpWhiteValidate())->check($this->response(), $data);
            $saveRes = (new IpWhiteListModel())->createIpAddrSingle($data);
            if ($saveRes) {
                $this->code = 200;
                $this->message = $conf->get('lang.ch.ip_white_save_success');
            } else {
                throw new ESException($conf->get('lang.ch.ip_white_save_error'));
            }
        } catch (ESException $e) {
            $this->message = $e->report();
        } catch (\Throwable $e) {
            $this->message = $e->getMessage();
        }
        $esResponse->writeJsonByResponse($this->response(), $this->code, $this->data, $this->message);
        return false;
	}
}