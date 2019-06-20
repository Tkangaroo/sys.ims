<?php
namespace App\HttpController;

use EasySwoole\Http\AbstractInterface\AbstractRouter;
use FastRoute\RouteCollector;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use App\Utility\Tools\ESResponseTool;


/**
 * Class Router
 * 路由类
 * @package App\HttpController
 */
class Router extends AbstractRouter
{
  public function initialize(RouteCollector $routeCollector)
  {
    // 开启全局拦截
    $this->setGlobalMode(true);

    $this->setMethodNotAllowCallBack(function (Request $request,Response $response) {
        (new ESResponseTool)->writeJsonByResponse($response, 500, null, 'the method not found!');
        return false;//结束此次响应
    });

    $this->setRouterNotFoundCallBack(function (Request $request,Response $response){
        (new ESResponseTool)->writeJsonByResponse($response, 500, null, 'the route not found!');
        return false;//结束此次响应
    });

    // 拦截GET方法
    $routeCollector->addRoute('GET', '/api_index', '/Api/Index/index');

    // 后台方法
    $routeCollector->addRoute('POST', '/admin_ip_white_save', '/Admin/IpWhiteList/save');
  }
}