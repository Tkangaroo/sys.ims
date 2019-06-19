<?php
namespace App\HttpController;

use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Http\Message\Status;


/**
 * 基类控制器
 * 校验白名单
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
	        if (1/*伪代码*/) {
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
		$this->writeJson(200, null, 'connection too much,please wait a moment.');
	 	return ;
    }
}
