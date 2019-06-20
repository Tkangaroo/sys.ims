<?php
/**
 * Created by PhpStorm.
 * User: speauty
 * Date: 2019/6/19
 * Time: 23:18
 */

namespace App\Traits;

/**
 * Trait SqlTrait
 * Sql 片段
 * @package App\Traits
 */
trait SqlTrait
{
    /**
     * 检测字符串是否为有效的数据库字段格式
     * @param string $str
     * @return bool
     */
    public function checkStrIsAValidFieldName(string $str):bool
    {
        return $str && preg_match("/^[a-z|\_]+$/",$str);
    }
}