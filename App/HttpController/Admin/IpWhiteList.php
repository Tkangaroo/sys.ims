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
		$this->writeJson(200, null, '数据通过验证...');
	}
}