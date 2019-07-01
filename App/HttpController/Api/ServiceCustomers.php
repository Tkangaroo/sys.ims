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
     * to get a page data
     * @return bool
     * @throws \EasySwoole\Component\Pool\Exception\PoolEmpty
     * @throws \EasySwoole\Component\Pool\Exception\PoolException
     * @throws \Throwable
     */
    public function list()
    {
        $page = ESTools::getPageParams($this->request());
        $totalAndList = MysqlPool::invoke(function (MysqlObject $db) use ($page) {
            return (new ServiceCustomersModel($db))->queryDataOfPagination($page, $this->generalFieldsName);
        });
        if ($totalAndList && isset($totalAndList['list']) && !empty($totalAndList['list'])) {
            foreach ($totalAndList['list'] as &$v) {
                $v['create_at'] = date('Y-m-d H:i:s', $v['create_at']);
            }
        }
        unset($v);
        $this->logisticCode = Logistic::L_OK;
        $this->data = $totalAndList;
        $this->message = Logistic::getMsg(Logistic::L_OK);
        ESTools::writeJsonByResponse(
            $this->response(),
            $this->logisticCode,
            $this->message,
            $this->data
        );
        unset($esTools, $page, $whereParamsIdx, $where, $totalAndList);
        return false;
    }

    /**
     * to get a white ip
     * @return bool
     * @throws \EasySwoole\Component\Pool\Exception\PoolEmpty
     * @throws \EasySwoole\Component\Pool\Exception\PoolException
     * @throws \Throwable
     */
    public function get():bool
    {
        $paramsIdx = ['id'];
        $params = ESTools::getArgFromRequest($this->request(), $paramsIdx);
        $ipWhite = MysqlPool::invoke(function (MysqlObject $db) use ($params) {
            return (new ServiceCustomersModel($db))->getOne($this->generalFieldsName, $params);
        });
        if ($ipWhite) {
            $ipWhite['create_at'] = date('Y-m-d H:i:s', $ipWhite['create_at']);
        }
        $this->logisticCode = Logistic::L_OK;
        $this->data = $ipWhite;
        unset($paramsIdx, $params, $ipWhite);
        $this->message = Logistic::getMsg(Logistic::L_OK);
        ESTools::writeJsonByResponse(
            $this->response(),
            $this->logisticCode,
            $this->message,
            $this->data
        );
        return false;
    }

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
            (new ServiceCustomersValidate())->check($data, $paramsIdx);
            $saveResult = MysqlPool::invoke(function (MysqlObject $db) use ($data) {
                $db->startTransaction();
                $customerResult = (new ServiceCustomersModel($db))->createServiceCustomerSingle($data);

                $whiteIp = [
                    'ip_addr' => $data['ip_addr'],
                    'is_enable' => 1,
                    'comments' => 'belonged to customer named '.$data['customer_name']
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

    /**
     * to update a customer
     * @return bool
     */
    public function update()
    {
        $paramsIdx = [
            'id', 'customer_contact_phone', 'customer_company_name', 'is_enable',
            'stock_update_callback_url', 'comments'
        ];
        $data = ESTools::getArgFromRequest($this->request(), $paramsIdx, 'getBody');
        try {
            unset($paramsIdx[1]);
            (new ServiceCustomersValidate())->check($data, $paramsIdx);
            $result = MysqlPool::invoke(function (MysqlObject $db) use ($data) {
                return (new ServiceCustomersModel($db))->update($data);
            });
            if ($result) {
                $this->logisticCode = Logistic::L_OK;
                $this->message = Logistic::getMsg(Logistic::L_OK);
            } else {
                throw new ESException(
                    Logistic::getMsg(Logistic::L_RECORD_UPDATE_ERROR),
                    Logistic::L_RECORD_UPDATE_ERROR
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
        unset($paramsIdx, $data, $result);
        return false;
    }

    /**
     * to delete a customer
     * @return bool
     */
    public function delete():bool
    {
        $paramsIdx = ['id'];
        $data = ESTools::getArgFromRequest($this->request(), $paramsIdx, 'getBody');
        try {
            (new ServiceCustomersValidate())->check($data, $paramsIdx);
            $result = MysqlPool::invoke(function (MysqlObject $db) use ($data) {
                return (new ServiceCustomersModel($db))->delete($data);
            });
            if ($result) {
                $this->logisticCode = Logistic::L_OK;
                $this->message = Logistic::getMsg(Logistic::L_OK);
            } else {
                throw new ESException(
                    Logistic::getMsg(Logistic::L_RECORD_DELETE_ERROR),
                    Logistic::L_RECORD_DELETE_ERROR
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
        unset($paramsIdx, $data, $result);
        return false;
    }
}