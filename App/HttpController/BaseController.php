<?php
namespace App\HttpController;

use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Http\Message\Status;
use EasySwoole\EasySwoole\ServerManager;


/**
 * 基类控制器
 * 校验白名单和一些登录情况
 */
class BaseController Extends Controller
{
    // 构造函数
    public function __constuct()
    {


    }


    /**
     * 在准备调用控制器方法处理请求时的事件,如果该方法返回false则不继续往下执行.
	 * 可用于做控制器基类权限验证等...
     */
    protected function onRequest(?string $action): ?bool
	{
	   if (parent::onRequest($action)) {
	        //判断是否登录
	        if (0/*伪代码*/) {
	            $this->writeJson(Status::CODE_UNAUTHORIZED, '', '登入已过期');
	            return false;
	        }
	        return true;
	    }
	    return false;
	}

	/**
	 * 当控制器逻辑抛出异常时将调用该方法进行处理异常(框架默认已经处理了异常)
	 * 可覆盖该方法,进行自定义的异常处理...
	 */
    protected function onException(\Throwable $throwable): void
    {
    	// 清空之前输出缓存
    	$this->response()->getBody()->truncate();
		$this->writeJson(200, null, 'connection too much,please wait a moment.');
	 	return ;
    }

    public function index()
    {
        // TODO: Implement index() method.
        $this->response()->write("forbidden");
    }

    public function getClientIp():int
    {
    	int $ipNum = 0;
		$ip = $this->request()->getHeaders();
		if ($ip && isset($ip['x-real-ip']) && $ip['x-real-ip']) {
			$ip = array_pop($ip['x-real-ip']);
			$ipNum = ip2long($ip);
		}
		return $ipNum;
    }

}
