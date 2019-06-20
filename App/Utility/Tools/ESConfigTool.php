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
    protected $esConf;

    /**
     * 设置ESConf类
     */
    protected function setEsConf():void
    {
        $this->esConf = new ESConfig();
    }

    /**
     * 获取ESConf类
     * @return ESConfig
     */
    public function getEsConf():ESConfig
    {
        if (!$this->esConf instanceof ESConfig) {
            $this->setEsConf();
        }
        return $this->esConf;
    }

    /**
     * 获取配置项
     * @param string $name
     * @return string|null
     */
    public function get(string $name):?string
    {

        return ESConfig::getInstance()->getConf($name);
    }

}