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
        $esTool = new ESTools();
        if (!$columnNamesArr) {
            throw new \Exception($esTool->lang('validate_column_empty_limit'));
        }

        foreach ($columnNamesArr as $v) {
            $methodName = 'set'.$esTool->convertUnderline2Pascal($v).'Column';
            if (method_exists($this, $methodName)) {
                $this->$methodName();
            } else {
                throw new \Exception($esTool->lang('validate_column_handle_not_found'));
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
        $flag = false;
        $this->setColumn($columnNames2Check);
        if (!$flag = $this->validate($data)) {
            (new ESTools())->throwValidateException($this);
        }
        return $flag;
    }
}