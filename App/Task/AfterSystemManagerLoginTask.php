<?php
/**
 * Created by PhpStorm.
 * User: speauty
 * Date: 2019/7/2
 * Time: 16:09
 */

namespace App\Task;
use App\Model\SystemManagersModel;
use App\Utility\Pool\Mysql\MysqlObject;
use App\Utility\Pool\Mysql\MysqlPool;
use App\Utility\Pool\Redis\RedisObject;
use App\Utility\Pool\Redis\RedisPool;
use EasySwoole\EasySwoole\Swoole\Task\AbstractAsyncTask;


class AfterSystemManagerLoginTask extends AbstractAsyncTask
{
    /**
     * @param $taskData
     * @param $taskId
     * @param $fromWorkerId
     * @param null $flags
     * @return bool
     * @throws \EasySwoole\Component\Pool\Exception\PoolEmpty
     * @throws \EasySwoole\Component\Pool\Exception\PoolException
     * @throws \Throwable
     */
    function run($taskData, $taskId, $fromWorkerId, $flags = null)
    {
        MysqlPool::invoke(function (MysqlObject $db) use ($taskData) {
            $model = (new SystemManagersModel($db));
            // to clear the cache last login logged
            $signName = $model->getLoginSignName($taskData['id'], $taskData['old_ip'], $taskData['old_salt']);
            RedisPool::invoke(function (RedisObject $redis) use ($signName) {
                return $redis->delete($signName);
            });

            $model->setLoginLog($taskData['signName'], $taskData['managerId']);
            $model->afterLogin($taskData['managerId'], $taskData['ip'],$taskData['salt']);
            unset($model, $signName);
        });
        return true;
    }

    /**
     * @param $result
     * @param $task_id
     */
    function finish($result, $task_id)
    {
        // nothing to do
    }
}