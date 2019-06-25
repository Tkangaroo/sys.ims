<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/5/28
 * Time: 下午6:33
 */

namespace EasySwoole\EasySwoole;

// 引入自定义初始化文件
require_once '/init.php';
use App\Utility\Pool\Mysql\MysqlPool;
use EasySwoole\Component\Pool\PoolManager;
use EasySwoole\EasySwoole\Swoole\EventRegister;
use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use Lib\OSDi;

class EasySwooleEvent implements Event
{

    /**
     * @throws \EasySwoole\Component\Pool\Exception\PoolException
     */
    public static function initialize()
    {
        // TODO: Implement initialize() method.
        date_default_timezone_set('Asia/Shanghai');

        // 加载语言包配置文件
        Config::getInstance()->loadFile(EASYSWOOLE_ROOT.'/lang.php', true);

        $mysqlConf = PoolManager::getInstance()->register(MysqlPool::class, Config::getInstance()->getConf('MYSQL.POOL_MAX_NUM'));
        if ($mysqlConf === null) {
            throw new \Exception('注册失败!');
        }
        //设置其他参数
        $mysqlConf->setMaxObjectNum(20)->setMinObjectNum(5);
        OSDi::getInstance()->test();
    }

    public static function mainServerCreate(EventRegister $register)
    {
        ################### mysql 热启动   #######################
        $register->add($register::onWorkerStart, function (\swoole_server $server, int $workerId) {
            if ($server->taskworker == false) {
                //每个worker进程都预创建连接
                PoolManager::getInstance()->getPool(MysqlPool::class)->preLoad(5);//最小创建数量
            }
        });
        // TODO: Implement mainServerCreate() method.
    }

    public static function onRequest(Request $request, Response $response): bool
    {
        // TODO: Implement onRequest() method.
        return true;
    }

    public static function afterRequest(Request $request, Response $response): void
    {
        // TODO: Implement afterAction() method.
    }
}
