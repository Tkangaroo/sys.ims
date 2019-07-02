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
            (new SystemManagersModel($db))->setLoginLog($taskData['signName'], $taskData['managerId']);
            (new SystemManagersModel($db))->afterLogin($taskData['managerId'], $taskData['ip'],$taskData['salt']);
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