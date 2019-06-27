<?php
/**
 * Created by PhpStorm.
 * User: speauty
 * Date: 2019/6/21
 * Time: 14:50
 */

namespace App\Utility;

use App\Utility\Pool\Mysql\MysqlObject;
use EasySwoole\EasySwoole\Config;
use EasySwoole\Http\Request;
use EasySwoole\Http\UrlParser;
use Lib\Exception\ESException;
use Lib\Logistic;

/**
 * Class ESTools
 * @package App\Utility
 */
class ESTools
{
    /**
     * to get data for request
     * @param Request $request
     * @param array $argIdxArr
     * @param string $callBackName
     * @return array
     */
    static public function getArgFromRequest(Request $request, array $argIdxArr, string $callBackName = 'getRequestParam'):array
    {
        $data = [];
        if (method_exists($request, $callBackName)) {
            if ($callBackName === 'getBody') {
                $argArr = json_decode($request->getBody()->__toString(), true);
            } else {
                $argArr = $request->$callBackName();
                var_dump($argArr);
            }
            if ($argArr) {
                foreach ($argArr as $k => $v) {
                    if (in_array($k, $argIdxArr, true)) {
                        $data[$k] = $v;
                    }
                }
            }
        }
        return $data;
    }

    /**
     * to get the real ip of client
     * @param Request $request
     * @return int|null
     */
    static public function getClientIp(Request $request):?int
    {
        $ipAddr = 0;
        $ip = $request->getHeaders();
        if ($ip && isset($ip['x-real-ip']) && $ip['x-real-ip']) {
            $ip = array_pop($ip['x-real-ip']);
            $ipAddr = ip2long($ip);
        }
        unset($ip);
        return $ipAddr;
    }

    /**
     * to format the uri of request
     * @param Request $request
     * @return array
     */
    static public function parseRequestTarget(Request $request):array
    {
        $targetStr = UrlParser::pathInfo($request->getUri()->getPath());
        // 如果第一位字符是/，则从第二位开始截取到最后，以免出现数组首位不齐的现象，导致模块不通过
        substr($targetStr,0,1) === '/' && ($targetStr = substr($targetStr,1));
        $target = explode('/', $targetStr);
        $arr = [
            'module'        => $target[0]??'',
            'controller'    => $target[1]??'',
            'action'        => $target[2]??'index',
        ];
        unset($target);
        return $arr;
    }

    /**
     * to get conf from conf file
     * @param string $name
     * @return string
     */
    static public function getConfByName(string $name):string
    {
        return Config::getInstance()->getConf($name)??$name;
    }


    /**
     * to format a json string prepare for output
     * @param int $logisticCode
     * @param string|null $msg
     * @param array|null $data
     * @return string
     */
    static public function outputJsonFormat(int $logisticCode, ?string $msg, ?array $data):string
    {
        $arr = [
            'code' => $logisticCode,
            "msg" => $msg,
            "data" => $data
        ];
        return json_encode($arr, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }

    /**
     * @param \EasySwoole\Http\Response $response
     * @param int $logisticCode
     * @param string $msg
     * @param null $data
     * @param int $httpCode
     */
    static public function writeJsonByResponse(
        \EasySwoole\Http\Response $response,
        $logisticCode = 500, $msg = '', $data = null, $httpCode = 200
    ):void
    {
        self::clear($response);
        if (empty($msg) && !($msg = Logistic::getMsg($logisticCode))) {
            $msg = 'something wrong';
        }
        $response->write(self::outputJsonFormat($logisticCode, $msg, $data));
        $response->withHeader('Content-type', 'application/json;charset=utf-8');
        $response->withStatus($httpCode);
        return ;
    }

    /**
     * to flush the output stream
     * @param \EasySwoole\Http\Response $response
     */
    static public function clear(\EasySwoole\Http\Response $response):void
    {
        $response->getBody()->truncate();
    }

    /**
     * throw validate exception
     * @param \EasySwoole\Validate\Validate $validate
     * @throws \Exception
     */
    static public function throwValidateException(\EasySwoole\Validate\Validate $validate):void
    {
        throw new ESException($validate->getError()->getErrorRuleMsg()?:$validate->getError()->getColumnErrorMsg(), Logistic::L_VALIDATE_ERROR);
    }

    /**
     * to check the string is a valid dbs name
     * @param string $str
     * @return bool
     */
    static public function checkStrIsAValidFieldName(string $str):bool
    {
        return $str && preg_match("/^[a-z|\_]+$/",$str);
    }

    /**
     * to convert array to where map quickly
     * @param MysqlObject $db
     * @param array|null $arr
     * @param int $ensureWhereNotEmptyFlag
     * @return bool
     */
    static public function quickParseArr2WhereMap(MysqlObject $db, ?array $arr, int $ensureWhereNotEmptyFlag = 0):bool
    {
        $parseFlag = false; // 响应数据
        $buildNum = 0;      // 构建where次数
        if ($arr) {
            foreach ($arr as $k => $v) {
                // 保证字符串为有效的字段格式
                if (self::checkStrIsAValidFieldName($k)) {
                    // 根据VAL值类型分开处理
                    if (is_array($v)) {
                        $db->where($k, $v[0], $v[1]??'=', $v[2]??'AND');
                    } else {
                        $db->where($k, $v, '=');
                    }
                    $buildNum++;
                }
                continue;
            }
        }
        // 设置响应数据 根据是否保证查询条件为空和构建条件次数
        if (!$ensureWhereNotEmptyFlag || ($ensureWhereNotEmptyFlag && $buildNum)) $parseFlag = true;
        return $parseFlag;
    }

    /**
     * to ensure the unique of record will insert or update
     * @param MysqlObject $db
     * @param string $tableName
     * @param array $uniqueFilterWhereArr
     * @return bool
     * @throws \EasySwoole\Mysqli\Exceptions\ConnectFail
     * @throws \EasySwoole\Mysqli\Exceptions\PrepareQueryFail
     * @throws \Throwable
     *
     */
    static public function checkUniqueByAField(MysqlObject $db, string $tableName, array $uniqueFilterWhereArr):bool
    {
        self::quickParseArr2WhereMap($db, $uniqueFilterWhereArr, 1);
        return (bool)$db->getValue($tableName, 'id', 1);
    }

    /**
     * to convert underline style to pascal
     * @param string $str
     * @return string
     */
    static public function convertUnderline2Pascal(string $str):string
    {
        return empty($str)?$str:str_replace(' ', '', ucwords(str_replace('_', ' ', $str)));
    }

    /**
     * to get page params from response
     * @param Request $request
     * @return array
     */
    static public function getPageParams(Request $request):array
    {
        $params = $request->getQueryParams();
        return [
            'page' => ($tmp = $params['page']??1)>0?$tmp:1,
            'limit' => ($tmp = $params['limit']??10)>0?$tmp:10,
        ];

    }

    /**
     * to get a random str
     * @param int $len 字符串长度
     * @param string $chars 字符串随机源
     * @return string
     */
    static public function buildRandomStr($len = 6, $chars = '')
    {
        if (!$chars) {
            $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        }
        for ($i = 0, $str = '', $lc = strlen($chars) - 1; $i < $len; $i++) {
            $str .= $chars[mt_rand(0, $lc)];
        }
        return $str;
    }
}