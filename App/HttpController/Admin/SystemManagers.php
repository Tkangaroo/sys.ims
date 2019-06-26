<?php
/**
 * Created by PhpStorm.
 * User: speauty
 * Date: 2019/6/21
 * Time: 11:51
 */

namespace App\HttpController\Admin;

use App\Utility\ESTools;
use Lib\Exception\ESException;
use App\Base\BaseController;
use App\Model\SystemManagersModel;
use App\Utility\Pool\Mysql\MysqlObject;
use App\Utility\Pool\Mysql\MysqlPool;
use App\Validate\SystemManagersValidate;

/**
 * Class SystemManagers
 * @package App\HttpController\Admin
 */
class SystemManagers extends BaseController
{
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
        $esTools = new ESTools();
        $page = $esTools->getPageParams($this->request());
        $totalAndList = MysqlPool::invoke(function (MysqlObject $db) use ($page) {
            return (new SystemManagersModel($db))->queryDataOfPagination($page, $this->generalFieldsName);
        });
        $this->code = 200;
        $this->data = $totalAndList;
        $this->message = $esTools->lang('query_system_manager_success');
        $esTools->writeJsonByResponse(
            $this->response(),
            $this->code,
            $this->data,
            $this->message
        );
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
        $esTools = new ESTools();
        $paramsIdx = ['id'];
        $params = $esTools->getArgFromRequest($this->request(), $paramsIdx);
        $systemManage = MysqlPool::invoke(function (MysqlObject $db) use ($params) {
            return (new SystemManagersModel($db))->getOne($this->generalFieldsName, $params);
        });
        $this->code = 200;
        $this->data = $systemManage;
        unset($paramsIdx, $params, $systemManage);
        $this->message = $esTools->lang('query_system_manager_success');
        $esTools->writeJsonByResponse(
            $this->response(),
            $this->code,
            $this->data,
            $this->message
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
        $esTools = new ESTools();
        $data = $esTools->getArgFromRequest($this->request(), $paramsIdx, 'getBody');
        try {
            (new SystemManagersValidate())->check($data, $paramsIdx);
            $saveResult = MysqlPool::invoke(function (MysqlObject $db) use ($data) {
                return (new SystemManagersModel($db))->createManagerSingle($data);
            });
            if ($saveResult) {
                $this->code = 200;
                $this->message = $esTools->lang('system_manager_save_success');
            } else {
                throw new ESException($esTools->lang('system_manager_save_error'));
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

    public function update()
    {
        $paramsIdx = ['id', 'old_password', 'password'];
        $esTools = new ESTools();
        $data = $esTools->getArgFromRequest($this->request(), $paramsIdx, 'getBody');
        try {
            unset($paramsIdx['old_password']);
            (new SystemManagersValidate())->check($data, $paramsIdx);
            $saveResult = MysqlPool::invoke(function (MysqlObject $db) use ($data) {
                return (new SystemManagersModel($db))->updateManager($data);
            });
            if ($saveResult) {
                $this->code = 200;
                $this->message = $esTools->lang('system_manager_save_success');
            } else {
                throw new ESException($esTools->lang('system_manager_save_error'));
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