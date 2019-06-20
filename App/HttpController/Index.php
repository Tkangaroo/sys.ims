<?php
namespace App\HttpController;

use App\HttpController\BaseController;

class Index extends BaseController
{

    public function index()
    {
        // TODO: Implement index() method.
        $this->response()->write("hello world");
    }
}