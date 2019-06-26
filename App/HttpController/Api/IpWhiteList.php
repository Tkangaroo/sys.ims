<?php
namespace App\HttpController\Api;

use App\Utility\ESTools;
use Lib\Exception\ESException;
use App\Base\BaseController;
use App\Model\IpWhiteListModel;
use App\Utility\Pool\Mysql\MysqlObject;
use App\Utility\Pool\Mysql\MysqlPool;
use App\Validate\IpWhiteValidate;

class IpWhiteList extends BaseController
{
    /**
     * @return bool
     * @throws \Throwable
     */
	public function save():bool
	{
	    $paramsIdx = ['ip_addr', 'is_enable', 'comments'];
        $esTools = new ESTools();
        $data = $esTools->getArgFromRequest($this->request(), $paramsIdx, 'getBody');
        try {
            (new IpWhiteValidate())->check($data, $paramsIdx);
            $saveResult = MysqlPool::invoke(function (MysqlObject $db) use ($data) {
                return (new IpWhiteListModel($db))->createIpWhiteSingle($data);
            });
            if ($saveResult) {
                $this->code = 200;
                $this->message = $esTools->lang('ip_white_save_success');
            } else {
                throw new ESException($esTools->lang('ip_white_save_fail'));
            }
        } catch (ESException $e) {
            $this->message = $e->report();
        } catch (\Throwable $e) {
            $this->message = $e->getMessage();
        }
        $esTools->writeJsonByResponse($this->response(), $this->code, $this->data, $this->message);
        unset($data, $conf, $saveResult, $esResponse);
        return false;
	}
}