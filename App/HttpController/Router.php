<?php
namespace App\HttpController;

use App\Utility\ESTools;
use EasySwoole\Http\AbstractInterface\AbstractRouter;
use FastRoute\RouteCollector;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use Lib\Logistic;

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
            ESTools::writeJsonByResponse($response, Logistic::L_METHOD_NOT_FOUND);
            return false;//结束此次响应
        });

        $this->setRouterNotFoundCallBack(function (Request $request,Response $response) {
            ESTools::writeJsonByResponse($response, Logistic::L_ROUTE_NOT_FOUND);
            return false;//结束此次响应
        });

        $this->buildRoute();
    }

    /**
     * 构建路由
     */
    private function buildRoute():void
    {
        $routeArr = array_merge(
            $this->initApiRoute(),
            $this->initOpenRoute()
        );
        foreach ($routeArr as $v) {
            $this->getRouteCollector()->addRoute($v[0], $v[1], $v[2]);
        }
    }

    /**
     * 后台路由
     * @return array
     *
     */
    private function initApiRoute():array
    {
        return [
            ['GET', '/api/system_managers', '/Api/SystemManagers/list'],
            ['GET', '/api/system_managers/{id:\d+}', '/Api/SystemManagers/get'],
            ['POST', '/api/system_managers', '/Api/SystemManagers/save'],
            ['PATCH', '/api/system_managers', '/Api/SystemManagers/update'],
            ['DELETE', '/api/system_managers', '/Api/SystemManagers/delete'],
            ['POST', '/api/system_managers/login', '/Api/SystemManagers/login'],
            ['GET', '/api/ip_white', '/Api/IpWhiteList/list'],
            ['GET', '/api/ip_white/{id:\d+}', '/Api/IpWhiteList/get'],
            ['POST', '/api/ip_white', '/Api/IpWhiteList/save'],
            ['PATCH', '/api/ip_white', '/Api/IpWhiteList/update'],
        ];
    }

    /**
     * API路由
     * @return array
     */
    private function initOpenRoute():array
    {
        return [
            ['GET', '/api_test_index', '/Api/Open/index']
        ];
    }
}