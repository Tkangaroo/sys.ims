<?php
namespace App\HttpController;
use EasySwoole\Http\AbstractInterface\Controller;
class Base Extends Controller
{
    // 构造函数
    public function __constuct()
    {

    }

    public function onException(\Throwable $throwable): void
    {
	$this->response()->write('connection too much,please wait a moment.');
 	return ;
    }
}
