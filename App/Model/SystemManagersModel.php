<?php
/**
 * Created by PhpStorm.
 * User: speauty
 * Date: 2019/6/21
 * Time: 11:01
 */

namespace App\Model;
use App\Base\BaseModel;
use Lib\Exception\ESException;


class SystemManagersModel extends BaseModel
{
    protected $table = 't_system_managers';

    public function setPasswordAttr($password):string
    {
        return MD5(MD5($password.'ims').'ims');
    }

    /**
     * @param array $form
     * @return bool
     * @throws ESException
     * @throws \EasySwoole\Mysqli\Exceptions\ConnectFail
     * @throws \EasySwoole\Mysqli\Exceptions\PrepareQueryFail
     * @throws \Throwable
     */
    public function createManagerSingle(array $form):bool
    {
        $data = [
            'account' => '',
            'password' => '',
            'phone' => '',
            'create_at' => time(),
            'update_at' => time()
        ];

        foreach ($data as $k => &$v) {
            if (isset($form[$k])) $v = $form[$k];
        }
        // 查重
        $uniqueFilterWhere = [
            'account' => [$data['account'], '=', 'OR'],
            'phone' => [$data['phone'], '=', 'OR'],
        ];

        if ($this->Di->get('ESTools')->checkUniqueByAField($this->getDb(), $this->table, $uniqueFilterWhere)) {
            throw new ESException($this->Di->get('ESTools')->lang('system_manager_not_unique'));
        }
        unset($k, $v,$form, $uniqueFilterWhere);
        $data['password'] = $this->setPasswordAttr($data['password']);
        return $this->getDb()->insert($this->table, $data);

    }
}