<?php
namespace App\HttpController\Api;

use App\Utility\ESTools;
use Lib\Exception\ESException;
use App\Base\BaseController;
use App\Model\IpWhiteListModel;
use App\Utility\Pool\Mysql\MysqlObject;
use App\Utility\Pool\Mysql\MysqlPool;
use App\Validate\IpWhiteValidate;
use Lib\Logistic;

class IpWhiteList extends BaseController
{
    /**
     * @return bool
     * @throws \Throwable
     */
	public function save():bool
	{
	    $paramsIdx = ['ip_addr', 'is_enable', 'comments'];
        $data = ESTools::getArgFromRequest($this->request(), $paramsIdx, 'getBody');
        try {
            (new IpWhiteValidate())->check($data, $paramsIdx);
            $saveResult = MysqlPool::invoke(function (MysqlObject $db) use ($data) {
                return (new IpWhiteListModel($db))->createIpWhiteSingle($data);
            });
            if ($saveResult) {
                $this->logisticCode = Logistic::L_OK;
                $this->message = Logistic::getMsg(Logistic::L_OK);
            } else {
                throw new ESException(
                    Logistic::getMsg(Logistic::L_RECORD_SAVE_ERROR),
                    Logistic::L_RECORD_SAVE_ERROR
                );
            }
        } catch (ESException $e) {
            $this->message = $e->report();
            $this->logisticCode = $e->getCode();
        } catch (\Throwable $e) {
            $this->message = $e->getMessage();
            $this->logisticCode = $e->getCode();
        }
        ESTools::writeJsonByResponse($this->response(), $this->logisticCode, $this->message);
        unset($data, $conf, $saveResult, $esResponse);
        return false;
	}
}