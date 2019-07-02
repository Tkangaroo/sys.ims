<?php
/**
 * Created by PhpStorm.
 * User: speauty
 * Date: 2019/7/2
 * Time: 16:09
 */

namespace App\Task;
use EasySwoole\EasySwoole\Swoole\Task\AbstractAsyncTask;


class AfterSystemManagerLoginTask extends AbstractAsyncTask
{
    /**
     * 执行任务的内容
     * @param mixed $taskData     任务数据
     * @param int   $taskId       执行任务的task编号
     * @param int   $fromWorkerId 派发任务的worker进程号
     * @param mixed   $flags        to
     */
    function run($taskData, $taskId, $fromWorkerId, $flags = null)
    {
        var_dump($taskData);
        var_dump($taskId);
        var_dump($fromWorkerId);
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