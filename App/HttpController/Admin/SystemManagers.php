<?php
/**
 * Created by PhpStorm.
 * User: speauty
 * Date: 2019/6/21
 * Time: 11:51
 */

namespace App\HttpController\Admin;

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

    public function list()
    {
        $page = $this->Di->get('ESTools')->getPageParams($this->request());
        var_dump($page);
    }

    /**
     * @return bool
     * @throws \Throwable
     */
    public function save()
    {
        $paramsIdx = ['account', 'password', 'phone'];
        $esTools = $this->Di->get('ESTools');
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
}