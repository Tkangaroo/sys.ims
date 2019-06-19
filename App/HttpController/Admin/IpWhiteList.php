<?php
namespace App\HttpController\Admin;

use App\HttpController\BaseController;
use App\Model\IpWhiteListModel;
use App\Validate\IpWhiteValidate;

class IpWhiteList extends BaseController
{
	/**
	 * 註冊白名單
	 */
	public function save()
	{
		$request = $this->request()；
		$data = $request->getRequestParam('ip_addr', 'is_enable', 'comments');
		$flag = (new IpWhiteValidate())->check($data);
		if (!$flag) {
			return false;
		}
		$this->writeJson(200, null, '数据通过验证...');
	}
}