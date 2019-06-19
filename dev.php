<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2019-01-01
 * Time: 20:06
 */

return [
    'SERVER_NAME' => "IMS DEV",
    'MAIN_SERVER' => [
        'LISTEN_ADDRESS' => '0.0.0.0',
        'PORT' => 9501,
        'SERVER_TYPE' => EASYSWOOLE_WEB_SERVER, //可选为 EASYSWOOLE_SERVER  EASYSWOOLE_WEB_SERVER EASYSWOOLE_WEB_SOCKET_SERVER,EASYSWOOLE_REDIS_SERVER
        'SOCK_TYPE' => SWOOLE_TCP,
        'RUN_MODEL' => SWOOLE_PROCESS,
        'SETTING' => [
            'worker_num' => 8,
            'task_worker_num' => 8,
            'reload_async' => true,
            'task_enable_coroutine' => true,
            'max_wait_time'=>3
        ],
    ],
    'TEMP_DIR' => null,
    'LOG_DIR' => null,
    'PHAR' => [
        'EXCLUDE' => ['.idea', 'Log', 'Temp', 'easyswoole', 'easyswoole.install']
    ],
    /*################ MYSQL CONFIG ##################*/
    'MYSQL' => [
    //数据库配置
       'host'                 => '119.23.191.131',//数据库连接ip
       'user'                 => 'ims',//数据库用户名
       'password'             => 'catbug',//数据库密码
       'database'             => 'ims',//数据库
       'port'                 => '3306',//端口
       'timeout'              => '30',//超时时间
       'connect_timeout'      => '5',//连接超时时间
       'charset'              => 'utf8mb4',//字符编码
       'strict_type'          => false, //开启严格模式，返回的字段将自动转为数字类型
       'fetch_mode'           => false,//开启fetch模式, 可与pdo一样使用fetch/fetchAll逐行或获取全部结果集(4.0版本以上)
       'alias'                => '',//子查询别名
       'isSubQuery'           => false,//是否为子查询
       'max_reconnect_times ' => '5',//最大重连次数
    ],
];
