<?php
namespace App\HttpController;
use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Http\Message\Status;

class Index extends Controller
{
    public function onException(\Throwable $throwable): void
    {
        $this->response()->write('waiting...');
        return ;
    }

    public function index()
    {
        // TODO: Implement index() method.
        $this->response()->write("hello world");
        new fdsafsa();
    }

    public function test()
    {
        $this->response()->write('this is a test');
    }
}