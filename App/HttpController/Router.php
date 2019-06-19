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

    // 拦截GET方法
    $routeCollector->addRoute('GET', 'api_index', '/Api/Index/index');
  }


}