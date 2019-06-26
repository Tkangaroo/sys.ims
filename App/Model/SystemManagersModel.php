<?php
/**
 * Created by PhpStorm.
 * User: speauty
 * Date: 2019/6/21
 * Time: 11:01
 */

namespace App\Model;
use App\Base\BaseModel;
use App\Utility\ESTools;
use Lib\Exception\ESException;


class SystemManagersModel extends BaseModel
{
    protected $table = 't_system_managers';

    public function setPasswordAttr($password):string
    {
        return MD5(MD5($password.'ims').'ims');
    }

    /**
     * to create new manager
     * @param array $manager
     * @return bool
     * @throws ESException
     * @throws \EasySwoole\Mysqli\Exceptions\ConnectFail
     * @throws \EasySwoole\Mysqli\Exceptions\PrepareQueryFail
     * @throws \Throwable
     */
    public function createManagerSingle(array $manager):bool
    {
        $esTools = new ESTools();
        $data = [
            'account' => '',
            'password' => '',
            'phone' => '',
            'create_at' => time(),
            'update_at' => time()
        ];

        foreach ($data as $k => &$v) {
            if (isset($manager[$k])) $v = $manager[$k];
        }
        // 查重
        $uniqueFilterWhere = [
            'account' => [$data['account'], '=', 'OR'],
            'phone' => [$data['phone'], '=', 'OR'],
        ];

        if ($esTools->checkUniqueByAField($this->getDb(), $this->table, $uniqueFilterWhere)) {
            throw new ESException($esTools->lang('system_manager_not_unique'));
        }
        unset($k, $v,$manager, $uniqueFilterWhere);
        $data['password'] = $this->setPasswordAttr($data['password']);
        return $this->getDb()->insert($this->table, $data);

    }

    /**
     * to update the manager(only password)
     * @param array $manager
     * @return bool
     * @throws ESException
     * @throws \Throwable
     */
    public function updateManager(array $manager):bool
    {
        $esTools = new ESTools();
        $where = [
            'id' => $manager['id']
        ];
        $oldManager = $this->getOne(['password'], $where);
        if (!$oldManager) throw new ESException($esTools->lang('query_system_manager_success'));
        $manager['password'] = $this->setPasswordAttr($manager['password']);
        if ($manager['password'] !== $oldManager['password']) throw new ESException($esTools->lang('old_password_not_match'));
        $esTools->quickParseArr2WhereMap($this->db, $where, true);
        return $this->db->setValue($this->table, 'password', $manager['password']);
    }
}