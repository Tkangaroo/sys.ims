<?php
/**
 * Created by PhpStorm.
 * User: speauty
 * Date: 2019/6/20
 * Time: 0:54
 */

namespace App\Utility\Tools;
use App\Utility\Tools\ESResponseTool;

class ESValidateTool
{

    /**
     * 输出验证类错误
     * @param \EasySwoole\Http\Response $response
     * @param \EasySwoole\Validate\Validate $validate
     */
    public function printValidateError(\EasySwoole\Http\Response $response, \EasySwoole\Validate\Validate $validate)
    {
        $msg = $validate->getError()->getErrorRuleMsg()?:$validate->getError()->getColumnErrorMsg();
        (new ESResponseTool())->writeJsonByResponse($response, 0, null, $msg);
        return ;
    }
}