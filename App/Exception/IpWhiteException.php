<?php
/**
 * Created by PhpStorm.
 * User: speauty
 * Date: 2019/6/20
 * Time: 14:46
 */

namespace App\Exception;


class IpWhiteException extends ESException
{
    protected $code = 200;
    // notice message
    protected $message = 'ip white exception broken, please contact us to report the error.';

    private $defaultLanguage = [
        'IP_ALREADY_EXISTS' => 'IP已存在',
        'IP_OPERATE_FAIL' => 'IP操作失败'
    ];

    /**
     * IpWhiteException constructor.
     * @param string $languageName
     */
    public function __construct(string $languageName = '')
    {
        if ($languageName && isset($this->defaultLanguage[strtoupper($languageName)])) {
            $this->setMessage($this->defaultLanguage[strtoupper($languageName)]);
        }
        parent::__construct($this->message, $this->code);
    }
}