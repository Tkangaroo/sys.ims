<?php
namespace App\HttpController;

use EasySwoole\Http\AbstractInterface\AbstractRouter;
use FastRoute\RouteCollector;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use EasySwoole\Component\Di;

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
            Di::getInstance()->get('ESTools')->writeJsonByResponse($response, 500, null, 'the method not found!');
            return false;//结束此次响应
        });

        $this->setRouterNotFoundCallBack(function (Request $request,Response $response){
            Di::getInstance()->get('ESTools')->writeJsonByResponse($response, 500, null, 'the route not found!');
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
            $this->initAdminRoute()
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
    private function initAdminRoute():array
    {
        return [
            ['POST', '/system_manager_save', '/Admin/SystemManagers/save'],
            ['POST', '/admin_ip_white_save', '/Admin/IpWhiteList/save'],
        ];
    }

    /**
     * API路由
     * @return array
     */
    private function initApiRoute():array
    {
        return [
            ['GET', '/api_test_index', '/Api/Test/index']
        ];
    }
}