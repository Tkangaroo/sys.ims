<?php
/**
 * Created by PhpStorm.
 * User: speauty
 * Date: 2019/6/21
 * Time: 11:51
 */

namespace App\HttpController\Api;

use App\Utility\ESTools;
use Lib\Exception\ESException;
use App\Base\BaseController;
use App\Model\SystemManagersModel;
use App\Utility\Pool\Mysql\MysqlObject;
use App\Utility\Pool\Mysql\MysqlPool;
use App\Validate\SystemManagersValidate;
use Lib\Logistic;

/**
 * Class SystemManagers
 * @package App\HttpController\Admin
 */
class SystemManagers extends BaseController
{
    /* 查询输出字段 */
    private $generalFieldsName = [
        'id', 'account', 'phone', 'latest_login_ip', 'latest_login_at', 'create_at'
    ];

    /**
     * to get a page data
     * @return bool
     * @throws \EasySwoole\Component\Pool\Exception\PoolEmpty
     * @throws \EasySwoole\Component\Pool\Exception\PoolException
     * @throws \Throwable
     */
    public function list():bool
    {
        $page = ESTools::getPageParams($this->request());
        $whereParamsIdx = ['account', 'phone'];
        $where = ESTools::getArgFromRequest($this->request(), $whereParamsIdx);
        $totalAndList = MysqlPool::invoke(function (MysqlObject $db) use ($page, $where) {
            return (new SystemManagersModel($db))->queryDataOfPagination($page, $this->generalFieldsName, $where);
        });

        if ($totalAndList && isset($totalAndList['list']) && !empty($totalAndList['list'])) {
            foreach ($totalAndList['list'] as &$v) {
                $v['latest_login_ip'] = $v['latest_login_ip']?long2ip($v['latest_login_ip']):$v['latest_login_ip'];
                $v['latest_login_at'] = date('Y-m-d H:i:s', $v['latest_login_at']);
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
     * to get a system manager
     * @return bool
     * @throws \EasySwoole\Component\Pool\Exception\PoolEmpty
     * @throws \EasySwoole\Component\Pool\Exception\PoolException
     * @throws \Throwable
     */
    public function get():bool
    {
        $paramsIdx = ['id'];
        $params = ESTools::getArgFromRequest($this->request(), $paramsIdx);
        $systemManage = MysqlPool::invoke(function (MysqlObject $db) use ($params) {
            return (new SystemManagersModel($db))->getOne($this->generalFieldsName, $params);
        });
        $this->logisticCode = Logistic::L_OK;
        $this->data = $systemManage;
        unset($paramsIdx, $params, $systemManage);
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
     * to save a new system manager
     * @return bool
     * @throws \Throwable
     */
    public function save():bool
    {
        $paramsIdx = ['account', 'password', 'phone'];
        $data = ESTools::getArgFromRequest($this->request(), $paramsIdx, 'getBody');
        try {
            (new SystemManagersValidate())->check($data, $paramsIdx);
            $result = MysqlPool::invoke(function (MysqlObject $db) use ($data) {
                return (new SystemManagersModel($db))->createManagerSingle($data);
            });
            if ($result) {
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
        unset($paramsIdx, $data, $result);
        return false;
    }

    /**
     * to update a manager(only password)
     * @return bool
     */
    public function update()
    {
        $paramsIdx = ['id', 'old_password', 'password'];
        $data = ESTools::getArgFromRequest($this->request(), $paramsIdx, 'getBody');
        try {
            unset($paramsIdx[1]);
            (new SystemManagersValidate())->check($data, $paramsIdx);
            $result = MysqlPool::invoke(function (MysqlObject $db) use ($data) {
                return (new SystemManagersModel($db))->updateManager($data);
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
     * to delete a manager
     * @return bool
     */
    public function delete():bool
    {
        $paramsIdx = ['id'];
        $data = ESTools::getArgFromRequest($this->request(), $paramsIdx, 'getBody');
        try {
            (new SystemManagersValidate())->check($data, $paramsIdx);
            $result = MysqlPool::invoke(function (MysqlObject $db) use ($data) {
                return (new SystemManagersModel($db))->deleteManager($data);
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

    /**
     * to login
     * @return bool
     */
    public function login():bool
    {
        $paramsIdx = ['account', 'password'];
        $data = ESTools::getArgFromRequest($this->request(), $paramsIdx, 'getBody');
        try {
            (new SystemManagersValidate())->check($data, $paramsIdx);
            $data['current_ip'] = ESTools::getClientIp($this->request());
            $result = MysqlPool::invoke(function (MysqlObject $db) use ($data) {
                return (new SystemManagersModel($db))->login($data);
            });
            if ($result) {
                $this->logisticCode = Logistic::L_OK;
                $this->message = Logistic::getMsg(Logistic::L_OK);
                $this->data = [
                    'es_token' => $result
                ];
            } else {
                throw new ESException(
                    Logistic::getMsg(Logistic::L_LOGIN_ERROR),
                    Logistic::L_LOGIN_ERROR
                );
            }
        } catch (ESException $e) {
            $this->logisticCode = $e->getCode();
            $this->message = $e->report();
        } catch (\Throwable $e) {
            $this->logisticCode = $e->getCode();
            $this->message = $e->getMessage();
        }
        ESTools::writeJsonByResponse($this->response(), $this->logisticCode, $this->message, $this->data);
        unset($paramsIdx, $data, $result);
        return false;
    }
}