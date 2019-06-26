<?php
/**
 * Created by PhpStorm.
 * User: speauty
 * Date: 2019/6/21
 * Time: 14:39
 */

namespace App\Validate;

use App\Base\BaseValidate;


class SystemManagersValidate extends BaseValidate
{
    protected function setAccountColumn():void
    {
        $this->addColumn('account', '账号')->required()->notEmpty()->alphaDash()->betweenLen(2, 10);
        return ;
    }

    protected function setPasswordColumn():void
    {
        $this->addColumn('password', '密码')->required()->notEmpty()->alphaNum();
        return ;
    }

    protected function setPhoneColumn():void
    {
        $this->addColumn('phone', '手机号')->required()->notEmpty()->length(11)->phone();
        return ;
    }
}