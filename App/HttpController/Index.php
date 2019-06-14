<?php
namespace App\HttpController;
use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Http\Message\Status;

class Index extends Controller
{
    protected function onRequest(string $action): ?bool
    {
        $token = $this->request()->getRequestParam('token');

        if ($token == '123') {
            return true;
        } else {
            $this->response()->withStatus(Status::CODE_FORBIDDEN);
            $this->response()->write('action forbid');
        }
    }

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