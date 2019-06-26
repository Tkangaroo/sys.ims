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

class ESTools
{
    /**
     * to get data for request
     * @param Request $request
     * @param array $argIdxArr
     * @param string $callBackName
     * @return array
     */
    public function getArgFromRequest(Request $request, array $argIdxArr, string $callBackName = 'getRequestParam'):array
    {
        $data = [];
        if (method_exists($request, $callBackName)) {
            if ($callBackName === 'getBody') {
                $argArr = json_decode($request->getBody()->__toString(), true);
            } else {
                $argArr = $request->$callBackName();
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
    public function getClientIp(Request $request):?int
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
    public function parseRequestTarget(Request $request):array
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
    public function getConfByName(string $name):string
    {
        return Config::getInstance()->getConf($name)??$name;
    }

    /**
     * to get lang message from conf file
     * @param string $name
     * @return string
     */
    public function lang(string $name):string
    {
        return $this->getConfByName('lang.'.$name);
    }


    /**
     * to format a json string prepare for output
     * @param int|null $statusCode
     * @param array|null $data
     * @param string|null $msg
     * @return string
     */
    public function outputJsonFormat(?int $statusCode, ?array $data, ?string $msg):string
    {
        $arr = [
            "code" => is_null($statusCode)?500:$statusCode,
            "data" => $data,
            "msg" => $msg,
            "time" => time()
        ];
        return json_encode($arr, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }

    /**
     * to flush the output stream
     * @param \EasySwoole\Http\Response $response
     */
    public function clear(\EasySwoole\Http\Response $response):void
    {
        $response->getBody()->truncate();
    }

    /**
     * return json
     * @param \EasySwoole\Http\Response $response
     * @param int $statusCode
     * @param null $data
     * @param string $msg
     */
    public function writeJsonByResponse(
        \EasySwoole\Http\Response $response,
        $statusCode = 500, $data = null, $msg = ''
    ):void
    {
        $this->clear($response);
        $response->write($this->outputJsonFormat($statusCode, $data, $msg));
        $response->withHeader('Content-type', 'application/json;charset=utf-8');
        $response->withStatus($statusCode);
        return ;
    }

    /**
     * throw validate exception
     * @param \EasySwoole\Validate\Validate $validate
     * @throws \Exception
     */
    public function throwValidateException(\EasySwoole\Validate\Validate $validate):void
    {
        throw new ESException($validate->getError()->getErrorRuleMsg()?:$validate->getError()->getColumnErrorMsg());
    }

    /**
     * to check the string is a valid dbs name
     * @param string $str
     * @return bool
     */
    public function checkStrIsAValidFieldName(string $str):bool
    {
        return $str && preg_match("/^[a-z|\_]+$/",$str);
    }

    /**
     * to convert array to where map quickly
     * @param MysqlObject $db
     * @param array $arr
     * @param int $ensureWhereNotEmptyFlag
     * @return bool
     */
    public function quickParseArr2WhereMap(MysqlObject $db, array $arr, int $ensureWhereNotEmptyFlag = 0):bool
    {
        $parseFlag = false; // 响应数据
        $buildNum = 0;      // 构建where次数
        if ($arr) {
            foreach ($arr as $k => $v) {
                // 保证字符串为有效的字段格式
                if ($this->checkStrIsAValidFieldName($k)) {
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
    public function checkUniqueByAField(MysqlObject $db, string $tableName, array $uniqueFilterWhereArr):bool
    {
        $this->quickParseArr2WhereMap($db, $uniqueFilterWhereArr, 1);
        return (bool)$db->getValue($tableName, 'id', 1);
    }

    /**
     * to convert underline style to pascal
     * @param string $str
     * @return string
     */
    public function convertUnderline2Pascal(string $str):string
    {
        return empty($str)?$str:str_replace(' ', '', ucwords(str_replace('_', ' ', $str)));
    }

    /**
     * to get page params from response
     * @param Request $request
     * @return array
     */
    public function getPageParams(Request $request):array
    {
        $params = $request->getQueryParams();
        return [
            'page' => ($tmp = $params['page']??1)>0?$tmp:1,
            'limit' => ($tmp = $params['limit']??1)>0?$tmp:1,
        ];

    }
}