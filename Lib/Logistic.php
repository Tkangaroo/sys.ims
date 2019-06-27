<?php
/**
 * Created by PhpStorm.
 * User: speauty
 * Date: 2019/6/27
 * Time: 10:04
 */

namespace Lib;


/**
 * Class Logistic
 * 逻辑代码类
 * @package Lib
 */
class Logistic
{
    const L_FAIL = 0;
    const L_OK = 200;
    const L_EXCEPTION = 500;

    const L_NOT_FOUND = 10000;
    const L_ROUTE_NOT_FOUND = 10001;
    const L_METHOD_NOT_FOUND = 10002;
    const L_MODULE_NOT_FOUND = 10003;
    const L_HANDLE_NOT_FOUND = 10004;

    const L_VALIDATE_ERROR   = 10005;

    const L_IP_NOT_REGISTER = 10006;
    const L_IP_DISABLE = 10007;

    const L_RECORD_NOT_UNIQUE = 10008;
    const L_RECORD_NOT_FOUND  = 10009;

    const L_PASSWORD_NOT_MATCH = 10010;

    const L_RECORD_SAVE_ERROR = 10011;
    const L_RECORD_UPDATE_ERROR = 10012;
    const L_RECORD_DELETE_ERROR = 10013;



    private static $msg = [
        0   => 'fail',
        200 => 'ok',
        500 => 'exception',

        10000 => 'something not found',
        10001 => 'the route not found',
        10002 => 'the method not found',
        10003 => 'the module not found',
        10004 => 'the handle not found',

        10005 => '',

        10006 => 'the ip not register',
        10007 => 'the ip has refused',

        10008 => 'the record not unique',
        10009 => 'the record not found',
        10010 => 'the password not matched',
        10011 => 'the record save fail',
        10012 => 'the record update fail',
        10013 => 'the record delete fail'
    ];

    static public function getMsg(int $code):?string
    {
        if(isset(self::$msg[$code])){
            return self::$msg[$code];
        }else{
            return null;
        }
    }
}