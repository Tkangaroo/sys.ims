<?php
namespace App\HttpController\Admin;

use App\Exception\ESException;
use App\Exception\IpWhiteException;
use App\HttpController\BaseController;
use App\Model\IpWhiteListModel;
use App\Validate\IpWhiteValidate;
use App\Utility\Tools\ESResponseTool;

class IpWhiteList extends BaseController
{
	/**
	 * 注册白名单
	 */
	public function save()
	{
		$data = $this->request()->getRequestParam('ip_addr', 'is_enable', 'comments');
        $esResponse = new ESResponseTool();
        try {

            (new IpWhiteValidate())->check($this->response(), $data);

            $saveRes = (new IpWhiteListModel())->createIpAddrSingle($data);
            if ($saveRes) {
                $esResponse->writeJsonByResponse($this->response(), 200, null, '添加IP成功');
            } else {
                throw new IpWhiteException('IP_OPERATE_FAIL');
            }
        } catch (IpWhiteException $e) {
            $esResponse->writeJsonByResponse($this->response(), 0, null, $e->report(1));
        } catch (ESException $e) {
            $esResponse->writeJsonByResponse($this->response(), 0, null, $e->report(1));
        } catch (\Throwable $e) {
            $esResponse->writeJsonByResponse($this->response(), 0, null, $e->getMessage());
        }
		return false;
	}
}