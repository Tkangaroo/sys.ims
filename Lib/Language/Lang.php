<?php
/**
 * Created by PhpStorm.
 * User: speauty
 * Date: 2019/6/20
 * Time: 15:13
 */

namespace Lib\Lanuage;


class Lang
{

    private $notFoundLang = '未知异常';
    private $chLangList = [
        'ip_white.not_unique' => 'IP已存在',
        'ip_white.save_success' => 'IP白名单保存成功',
        'ip_white.save_error' => 'IP白名单保存失败',

    ];

    private $enLangList = [
        'ip_white.not_unique' => 'the ip is already exists',
    ];
    private $lang = 'ch';
    private $langList = null;

    /**
     * Lang constructor.
     * @param string $lang
     */
    public function __construct(string $lang = 'ch')
    {
        $this->setLang($lang);
        $this->switchLang();
    }

    /**
     * 设置语言包类型
     * @param string $lang
     */
    public function setLang(string $lang = 'ch'):void
    {
        $this->lang = $lang;
    }

    /**
     * 切换语言包
     */
    public function switchLang():void
    {
        if ($this->lang === 'ch') {
            $this->langList = $this->chLangList;
        } else {
            $this->langList = $this->enLangList;
        }
    }

    /**
     * 获取语言包
     * @param string $name
     * @return string
     */
    public function get(string $name):string
    {
        $msg = $this->notFound();
        if (isset($this->langList[$name])) {
            $msg = $this->langList[$name];
        }
        return $msg;
    }

    public function notFound():string
    {
        return $this->notFoundLang;
    }
}