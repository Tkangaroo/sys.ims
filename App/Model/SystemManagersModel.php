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
use App\Utility\Pool\Redis\RedisObject;
use App\Utility\Pool\Redis\RedisPool;
use EasySwoole\EasySwoole\Swoole\Task\TaskManager;
use Lib\Exception\ESException;
use Lib\Logistic;


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

        if (ESTools::checkUniqueByAField($this->getDb(), $this->table, $uniqueFilterWhere)) {
            throw new ESException(Logistic::getMsg(Logistic::L_RECORD_NOT_UNIQUE), Logistic::L_RECORD_NOT_UNIQUE);
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
        $where = [
            'id' => $manager['id']
        ];
        $oldManager = $this->getOne(['password'], $where);
        if (!$oldManager) {
            throw new ESException(
                Logistic::getMsg(Logistic::L_RECORD_NOT_FOUND),
                Logistic::L_RECORD_NOT_FOUND
            );
        }
        $manager['old_password'] = $this->setPasswordAttr($manager['old_password']);
        if ($manager['old_password'] !== $oldManager['password']) {
            throw new ESException(
                Logistic::getMsg(Logistic::L_PASSWORD_NOT_MATCH),
                Logistic::L_PASSWORD_NOT_MATCH
            );
        }

        ESTools::quickParseArr2WhereMap($this->db, $where, true);
        return $this->db->setValue($this->table, 'password', $manager['password']);
    }

    /**
     * to delete a manager by the id
     * @param array $manager
     * @return bool
     * @throws ESException
     * @throws \EasySwoole\Mysqli\Exceptions\ConnectFail
     * @throws \EasySwoole\Mysqli\Exceptions\PrepareQueryFail
     * @throws \Throwable
     */
    public function deleteManager(array $manager):bool
    {
        if (!ESTools::checkUniqueByAField($this->db, $this->table, $manager)) {
            throw new ESException(
                Logistic::getMsg(Logistic::L_RECORD_NOT_FOUND),
                Logistic::L_RECORD_NOT_FOUND
            );
        }

        return $this->db->where('id', $manager['id'])->delete($this->table, 1);
    }

    /**
     * @param array $login
     * @return mixed
     * @throws \Throwable
     */
    public function login(array $login)
    {
        $where = [
            'account' => $login['account']
        ];
        $manager = $this->getOne(['id', 'password', 'latest_login_ip'], $where);

        if (is_null($manager)) {
            throw new ESException(
                Logistic::getMsg(Logistic::L_RECORD_NOT_FOUND),
                Logistic::L_RECORD_NOT_FOUND
            );
        }

        if ($manager['password'] !== $this->setPasswordAttr($login['password'])) {
            throw new ESException(
                Logistic::getMsg(Logistic::L_PASSWORD_NOT_MATCH),
                Logistic::L_PASSWORD_NOT_MATCH
            );
        }

        $salt = ESTools::buildRandomStr('4');
        $signName = $this->getLoginSignName($manager['id'], $manager['latest_login_ip'], $salt);

        $as = 1;
        TaskManager::async(function() use ($as) {
            var_dump($as);
        });
        RedisPool::invoke(function (RedisObject $redis) use ($signName, $manager) {
            $redis->set($signName, $manager['id'], 12*60*60);
        });
        return $signName;
    }

    /**
     * to get a login sign name for redis name
     * @param int $id
     * @param int $latestLoginIp
     * @param string $salt
     * @return string
     */
    public function getLoginSignName(int $id, int $latestLoginIp, string $salt):string
    {
        return MD5(time().$id.$salt.$latestLoginIp);
    }

    public function afterLogin($managerId, $salt):void
    {

    }
}