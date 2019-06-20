<?php
namespace App\HttpController\Admin;

use App\HttpController\BaseController;
use App\Model\IpWhiteListModel;
use App\Validate\IpWhiteValidate;

class IpWhiteList extends BaseController
{
	/**
	 * 注册白名单
	 */
	public function save()
	{
		$data = $this->request()->getRequestParam('ip_addr', 'is_enable', 'comments');
		$flag = (new IpWhiteValidate())->check($this->response(), $data);
		if (!$flag) {
			return false;
		}
		try {
            $saveRes = (new IpWhiteListModel())->createIpAddrSingle($data);
            if ($saveRes) {
                $this->writeJson(200, null, '添加IP成功');
            } else {
                $this->writeJson(0, null, '添加IP失败');
            }
        } catch (\Throwable $throwable) {
            $this->writeJson(0, null, $throwable->getMessage());
        }
		return false;
	}
}