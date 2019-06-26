<?php
/**
 * Created by PhpStorm.
 * User: speauty
 * Date: 2019/6/25
 * Time: 14:26
 */

namespace App\Base;

use EasySwoole\Component\Di;
use EasySwoole\Validate\Validate;


class BaseValidate extends Validate
{
    protected $Di;

    public function __construct()
    {
        if (is_null($this->Di) || !$this->Di instanceof Di) {
            $this->Di = Di::getInstance();
        }
    }
}