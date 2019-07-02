<?php
/**
 * Created by PhpStorm.
 * User: speauty
 * Date: 2019/7/2
 * Time: 16:39
 */

namespace App\Task;
use EasySwoole\EasySwoole\Swoole\Task\AbstractAsyncTask;


class ESReStartTask extends AbstractAsyncTask
{

    /**
     * @param $taskData
     * @param $taskId
     * @param $fromWorkerId
     * @param null $flags
     * @return bool
     */
    function run($taskData, $taskId, $fromWorkerId, $flags = null)
    {
        sleep(3);
        exec('cd /www/wwwroot/ims.billeslook.com && php easyswoole stop && php easyswoole start');
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