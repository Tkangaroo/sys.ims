<?php
/**
 * Created by PhpStorm.
 * User: speauty
 * Date: 2019/6/21
 * Time: 14:50
 */

namespace App\Utility\Tools;

use EasySwoole\Http\Request;

class ESTools
{
    /**
     * 从请求中获取指定数据
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


    public function get(string $toolClassName, string $prefix = '')
    {
        if ($prefix) $toolClassName = $prefix.'/'.$toolClassName;
        return class_exists($toolClassName)?new $toolClassName():null;
    }
}