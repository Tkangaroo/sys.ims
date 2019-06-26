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
use Lib\Exception\ESException;


class BaseValidate extends Validate
{
    protected $Di;

    public function __construct()
    {
        if (is_null($this->Di) || !$this->Di instanceof Di) {
            $this->Di = Di::getInstance();
        }
    }

    /**
     * @param array $columnNamesArr
     * @throws \Throwable
     */
    public function setColumn(array $columnNamesArr):void
    {
        if (!$columnNamesArr) {
            throw new \Exception($this->Di->get('ESTools')->lang('validate_column_empty_limit'));
        }

        foreach ($columnNamesArr as $v) {
            $methodName = 'set'.$this->Di->get('ESTools')->convertUnderline2Pascal($v).'Column';
            if (method_exists($this, $methodName)) {
                $this->$methodName();
            } else {
                throw new \Exception($this->Di->get('ESTools')->lang('validate_column_handle_not_found'));
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
            $this->Di->get('ESTools')->throwValidateException($this);
        }
        return $flag;
    }
}