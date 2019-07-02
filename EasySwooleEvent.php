<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/5/28
 * Time: 下午6:33
 */

namespace EasySwoole\EasySwoole;

use App\Model\IpWhiteListModel;
use App\Process\HotReload;
use App\Utility\ESTools;
use App\Utility\Pool\Mysql\MysqlObject;
use App\Utility\Pool\Mysql\MysqlPool;
use App\Utility\Pool\Redis\RedisPool;
use EasySwoole\Component\Pool\PoolManager;
use EasySwoole\EasySwoole\Swoole\EventRegister;
use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\Http\Message\Status;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;


class EasySwooleEvent implements Event
{

    /**
     * @throws \EasySwoole\Component\Pool\Exception\PoolException
     * @throws \EasySwoole\Component\Pool\Exception\PoolObjectNumError
     * @throws \Exception
     */
    public static function initialize()
    {
        // TODO: Implement initialize() method.
        date_default_timezone_set('Asia/Shanghai');

        /* mysql service register */
        $mysqlConf = PoolManager::getInstance()->register(MysqlPool::class, Config::getInstance()->getConf('MYSQL.POOL_MAX_NUM'));
        if ($mysqlConf === null) throw new \Exception('注册失败!');
        //设置其他参数
        $mysqlConf->setMaxObjectNum(20)->setMinObjectNum(5);


        /* redis service register */
        PoolManager::getInstance()->register(RedisPool::class);
    }

    /**
     * @param EventRegister $register
     * @throws \EasySwoole\Component\Pool\Exception\PoolException
     */
    public static function mainServerCreate(EventRegister $register)
    {
        ################### mysql 热启动   #######################
        $register->add($register::onWorkerStart, function (\swoole_server $server, int $workerId) {
            if ($server->taskworker == false) {
                //每个worker进程都预创建连接
                PoolManager::getInstance()->getPool(MysqlPool::class)->preLoad(5);//最小创建数量
            }
        });

        $swooleServer = ServerManager::getInstance()->getSwooleServer();
        $swooleServer->addProcess((new HotReload('HotReload', ['disableInotify' => false]))->getProcess());
        // TODO: Implement mainServerCreate() method.
    }


    /**
     * @param Request $request
     * @param Response $response
     * @return bool
     * @throws \EasySwoole\Component\Pool\Exception\PoolEmpty
     * @throws \EasySwoole\Component\Pool\Exception\PoolException
     * @throws \Throwable
     */
    public static function onRequest(Request $request, Response $response): bool
    {
        // TODO: Implement onRequest() method.
        // to check the client ip in white list
        $clientIp = ESTools::getClientIp($request);
        $whiteIp = MysqlPool::invoke(function (MysqlObject $db) use ($clientIp) {
            return (new IpWhiteListModel($db))->queryByIpAddr($clientIp);
        });
        $headers = $request->getHeaders();
        $origin = '';
        if ($whiteIp && $whiteIp['is_enable']) {
            if (isset($headers['origin']) && isset($headers['origin'][0])) {
                $origin = $headers['origin'][0];
            }
            if (!$origin) {
                $origin = 'http://'.long2ip($clientIp);
            }
        } else {
            $origin = 'null';
        }
        $response->withHeader('Access-Control-Allow-Origin', $origin);
        unset($clientIp, $whiteIp, $headers, $origin);
        $response->withHeader('Access-Control-Allow-Methods', 'GET, DELETE, PATCH, POST, OPTIONS');
        $response->withHeader('Access-Control-Allow-Credentials', 'true');
        $response->withHeader('Access-Control-Allow-Headers', 'Content-Type, Es-Token, X-Requested-With');
        if ($request->getMethod() === 'OPTIONS') {
            $response->withStatus(Status::CODE_OK);
            return false;
        }
        return true;
    }

    public static function afterRequest(Request $request, Response $response): void
    {
        // TODO: Implement afterAction() method.
    }
}
