<?php
namespace App\HttpController;

use EasySwoole\Http\AbstractInterface\AbstractRouter;
use FastRoute\RouteCollector;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;


class Router extends AbstractRouter
{
  public function initialize(RouteCollector $routeCollector)
  {
    // 开启全局拦截
    $this->setGlobalMode(true);

    $this->setMethodNotAllowCallBack(function (Request $request,Response $response){
        $data = Array(
            "code" => 500,
            "data" => null,
            "msg" => 'the method not found!',
            "time" => time()
        );
        $response->write(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        $response->withHeader('Content-type', 'application/json;charset=utf-8');
        $response->withStatus(500);
        return false;//结束此次响应
    });

    $this->setRouterNotFoundCallBack(function (Request $request,Response $response){
        $data = Array(
            "code" => 500,
            "data" => null,
            "msg" => 'the route not found!',
            "time" => time()
        );
        $response->write(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        $response->withHeader('Content-type', 'application/json;charset=utf-8');
        $response->withStatus(500);
        return false;//结束此次响应
    });

    // 拦截GET方法
    $routeCollector->addRoute('GET', '/api_index', '/Api/Index/index');
    
  }


}