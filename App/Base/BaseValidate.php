<?php
/**
 * Created by PhpStorm.
 * User: speauty
 * Date: 2019/6/25
 * Time: 14:26
 */

namespace App\Base;


use EasySwoole\Validate\Validate;

class BaseValidate extends Validate
{
    protected $OSDi;

    public function __construct()
    {
        if (!$this->OSDi || !$this->OSDi instanceof OSDi) {
            $this->OSDi = OSDi::getInstance();
        }
    }
}