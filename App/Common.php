<?php
namespace App;

use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use EasySwoole\EasySwoole\ServerManager;

class Common
{
	$this->response;
	$this->request;

	protected function request(): Request
    {
        return $this->request;
    }

    protected function response(): Response
    {
        return $this->response;
    }

	public function writeJson($statusCode = 200, $result = null, $msg = null)
	{
		$data = Array(
            "code" => $statusCode,
            "data" => $result,
            "msg" => $msg,
            "time" => time()
        );
        $this->response()->write(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        $this->response()->withHeader('Content-type', 'application/json;charset=utf-8');
        $this->response()->withStatus($statusCode);
	}
}