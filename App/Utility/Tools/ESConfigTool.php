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
     * @return string
     */
    public function get(string $name):string
    {
        return ESConfig::getInstance()->getConf($name)??$name;
    }

    /**
     * 获取语言包
     * @param string $name
     * @return string
     */
    public function lang(string $name):string
    {
        return $this->get('lang'.$name);
    }

}