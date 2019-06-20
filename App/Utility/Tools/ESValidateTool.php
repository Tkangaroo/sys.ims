<?php
/**
 * Created by PhpStorm.
 * User: speauty
 * Date: 2019/6/20
 * Time: 0:54
 */

namespace App\Utility\Tools;

use App\Exception\ESException;


class ESValidateTool
{

    /**
     * 抛出验证类错误异常
     * @param \EasySwoole\Validate\Validate $validate
     * @throws \Exception
     */
    public function printValidateError(\EasySwoole\Validate\Validate $validate):void
    {
        throw new ESException($validate->getError()->getErrorRuleMsg()?:$validate->getError()->getColumnErrorMsg());
    }
}