<?php
namespace App\HttpController\Admin;

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
		$data = $this->request()->getRequestParam('ip_addr', 'is_enable', 'comments');
        $esTools = $this->Di->get('ESTools');
        try {
            (new IpWhiteValidate())->check($data);
            $saveResult = MysqlPool::invoke(function (MysqlObject $db) use ($data) {
                return (new IpWhiteListModel($db))->createIpWhiteSingle($data);
            });
            if ($saveResult) {
                $this->code = 200;
                $this->message = $esTools->lang('ip_white_save_success');
            } else {
                throw new ESException($esTools->lang('ip_white_save_error'));
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