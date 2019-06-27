<?php
/**
 * Created by PhpStorm.
 * User: speauty
 * Date: 2019/6/25
 * Time: 14:26
 */

namespace App\Base;

use App\Utility\ESTools;
use EasySwoole\Validate\Validate;
use Lib\Logistic;


class BaseValidate extends Validate
{

    public function __construct()
    {
    }

    /**
     * @param array $columnNamesArr
     * @throws \Throwable
     */
    public function setColumn(array $columnNamesArr):void
    {
        if (!$columnNamesArr) {
            throw new \Exception('the validate needs more column', Logistic::L_FAIL);
        }

        foreach ($columnNamesArr as $v) {
            $methodName = 'set'.ESTools::convertUnderline2Pascal($v).'Column';
            if (method_exists($this, $methodName)) {
                $this->$methodName();
            } else {
                throw new \Exception(Logistic::getMsg(Logistic::L_HANDLE_NOT_FOUND), Logistic::L_HANDLE_NOT_FOUND);
            }
        }
        return ;
    }

    /**
     * @param array $data
     * @param array $columnNames2Check
     * @return bool
     * @throws \Throwable
     */
    public function check(array $data, array $columnNames2Check):bool
    {
        $this->setColumn($columnNames2Check);
        if (!$flag = $this->validate($data)) {
            ESTools::throwValidateException($this);
        }
        return $flag;
    }
}