<?php
/**
 * Created by PhpStorm.
 * User: speauty
 * Date: 2019/6/28
 * Time: 9:19
 */

namespace App\HttpController\Api;
use App\Base\BaseController;
use App\Model\IpWhiteListModel;
use App\Model\ServiceCustomersModel;
use App\Utility\ESTools;
use App\Utility\Pool\Mysql\MysqlObject;
use App\Utility\Pool\Mysql\MysqlPool;
use App\Validate\ServiceCustomersValidate;
use Lib\Exception\ESException;
use Lib\Logistic;


class ServiceCustomers extends BaseController
{
    private $generalFieldsName = [
        'id', 'customer_name', 'customer_contact_phone', 'customer_company_name',
        'customer_id', 'customer_es_key', 'is_enable', 'stock_update_callback_url',
        'comments', 'create_at'
    ];

    /**
     * to save a service customer
     * @return bool
     */
    public function save():bool
    {
        $paramsIdx = [
            'customer_name', 'customer_contact_phone', 'customer_company_name',
            'is_enable', 'ip_addr', 'stock_update_callback_url', 'comments'
        ];
        $data = ESTools::getArgFromRequest($this->request(), $paramsIdx, 'getBody');
        try {
            var_dump($data);
            (new ServiceCustomersValidate())->check($data, $paramsIdx);
            $saveResult = MysqlPool::invoke(function (MysqlObject $db) use ($data) {
                $db->startTransaction();
                $customerResult = (new ServiceCustomersModel($db))->createServiceCustomerSingle($data);

                $whiteIp = [
                    'ip_addr' => $data['ip_addr'],
                    'is_enable' => 1,
                    'comments' => 'belonged to '.$data['customer_name']
                ];
                $whiteIpResult = (new IpWhiteListModel($db))->createIpWhiteSingle($whiteIp);
                if ($customerResult && $whiteIpResult) {
                    $db->commit();
                } else {
                    $db->rollback();
                }
                return $customerResult && $whiteIpResult;
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