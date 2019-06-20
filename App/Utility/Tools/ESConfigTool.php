<?php
/**
 * Created by PhpStorm.
 * User: speauty
 * Date: 2019/6/20
 * Time: 15:55
 */

namespace App\Utility\Tools;
use EasySwoole\EasySwoole\Config as ESConfig;

class ESConfigTool
{
    /**
     * 获取配置项
     * @param string $name
     * @return string|null
     */
    public function get(string $name):?string
    {

        $conf = ESConfig::getInstance()->getConf($name);
        var_dump($conf);
        return $conf;
    }

}