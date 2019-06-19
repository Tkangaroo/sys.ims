<?php
namespace App;


class Common
{
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

	 public function writeJsonByResponse(
	 	Reponse $response,
	 	$statusCode = 500, $data = null, $msg = null
	 ):void
    {
        $response->write($this->backJsonStr($statusCode, $data, $msg));
        $response->withHeader('Content-type', 'application/json;charset=utf-8');
        $response->withStatus($statusCode);
        return ;
    }
}