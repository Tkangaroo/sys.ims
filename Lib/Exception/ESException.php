<?php
/**
 * Created by PhpStorm.
 * User: speauty
 * Date: 2019/6/20
 * Time: 14:16
 */

namespace Lib\Exception;

/**
 * Class ESException
 * 重写Exception
 * @package App\Exception
 */
class ESException extends \Exception
{
    // Http Logistic
    protected $code = 500;
    // notice message
    protected $message = '';

    /**
     * ESException constructor.
     * @param string $message
     * @param int $code
     */
    public function __construct($message = '', $code = 500)
    {
        $this->setCode($code);
        $this->setMessage($message);
        parent::__construct($message, $code);
    }

    /**
     * 设置Code
     * @param int $code
     */
    public function setCode(int $code = 500):void
    {
        $this->code = $code;
    }

    /**
     * 设置Message
     * @param string $message
     */
    public function setMessage(string $message = ''):void
    {
        $this->message = $message;
    }

    /**
     * 报告错误
     * @param int $isShowDetail
     * @return string
     */
    public function report(int $isShowDetail = 0):string
    {
        $msg = $this->message;
        if ($isShowDetail) {
            $msg .= '[fileName:'.$this->getFile() .', lineNo:'.$this->getLine().']';
        }
        return $msg;
    }
}