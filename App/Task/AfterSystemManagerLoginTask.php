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
     * 任务执行完的回调
     * @param mixed $result  任务执行完成返回的结果
     * @param int   $task_id 执行任务的task编号
     */
    function finish($result, $task_id)
    {
        var_dump('the task '.$task_id.' finished!');
        var_dump($result);
    }
}