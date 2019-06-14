<?php
namespace App\HttpController;
use EasySwoole\Http\AbstractInterface\Controller;

class Index extends Controller
{
    public function index()
    {
        // TODO: Implement index() method.
        $this->response()->write("hello world");
    }

    public function test()
    {
        $this->response()->write('this is a test');
    }
}