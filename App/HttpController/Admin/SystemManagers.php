<?php
/**
 * Created by PhpStorm.
 * User: speauty
 * Date: 2019/6/21
 * Time: 11:51
 */

namespace App\HttpController\Admin;

use App\Exception\ESException;
use App\HttpController\BaseController;
use App\Model\SystemManagersModel;
use App\Utility\Pool\Mysql\MysqlObject;
use App\Utility\Pool\Mysql\MysqlPool;
use App\Utility\Tools\ESConfigTool;
use App\Utility\Tools\ESResponseTool;
use App\Utility\Tools\ESTools;
use App\Validate\SystemManagersValidate;


class SystemManagers extends BaseController
{
    public function save()
    {
        $argIdx = ['account', 'password', 'phone'];
        $data = (new ESTools())->getArgFromRequest($this->request(), $argIdx);
        $esResponse = new ESResponseTool();
        $conf = new ESConfigTool();
        try {
            (new SystemManagersValidate())->check($data);
            $saveResult = MysqlPool::invoke(function (MysqlObject $db) use ($data) {
                return (new SystemManagersModel($db))->createManagerSingle($data);
            });
            if ($saveResult) {
                $this->code = 200;
                $this->message = $conf->lang('system_manager_save_success');
            } else {
                throw new ESException($conf->lang('system_manager_save_error'));
            }
        } catch (ESException $e) {
            $this->message = $e->report();
        } catch (\Throwable $e) {
            $this->message = $e->getMessage();
        }
        $esResponse->writeJsonByResponse($this->response(), $this->code, $this->data, $this->message);
        unset($data, $conf, $saveResult, $esResponse);
        return false;
    }
}