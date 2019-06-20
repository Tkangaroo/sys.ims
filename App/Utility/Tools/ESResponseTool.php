<?php
/**
 * Created by PhpStorm.
 * User: speauty
 * Date: 2019/6/19
 * Time: 23:07
 */

namespace App\Utility\Tools;


/**
 * Class ESResponseTool
 * Response 工具类(结合ES框架使用)
 * @package App\Utility\Tools
 */
class ESResponseTool
{

    /**
     * 返回json字符串
     * @param int $statusCode 状态码
     * @param null $data 数据包
     * @param null $msg 提示信息
     * @return string
     */
    protected function backJsonStr($statusCode = 500, $data = null, $msg = null):string
    {
        $arr = Array(
            "code" => $statusCode,
            "data" => $data,
            "msg" => $msg,
            "time" => time()
        );
        return json_encode($arr, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }


    /**
     * 清空之前输出流
     * @param \EasySwoole\Http\Response $response
     */
    public function clear(\EasySwoole\Http\Response $response):void
    {
        $response->getBody()->truncate();
    }

    /**
     * 返回JSON数据(基于Response类)
     * @param \EasySwoole\Http\Response $response
     * @param int $statusCode 状态码
     * @param null $data 数据包
     * @param string $msg 提示信息
     */
    public function writeJsonByResponse(
        \EasySwoole\Http\Response $response,
        $statusCode = 500, $data = null, $msg = ''
    ):void
    {
        $this->clear($response);
        $response->write($this->backJsonStr($statusCode, $data, $msg));
        $response->withHeader('Content-type', 'application/json;charset=utf-8');
        $response->withStatus($statusCode);
        return ;
    }
}