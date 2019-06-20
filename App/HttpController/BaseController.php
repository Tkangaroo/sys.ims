<?php
namespace App\HttpController;

use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Http\Message\Status;
use App\Model\IpWhiteListModel;


/**
 * 基类控制器
 * 校验白名单和一些登录情况
 */
class BaseController Extends Controller
{
    protected $code = 0;
    protected $message = '';
    protected $data = null;


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
	   		// 判斷IP白名單
	   		if (!$this->checkClientIpHasAccessAuthority()) {
	   			return false;
	   		}
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
     * @param \Throwable $throwable
     */
    protected function onException(\Throwable $throwable): void
    {
    	// 清空之前输出缓存
    	$this->response()->getBody()->truncate();
    	$msg = $throwable->getMessage();
		$this->writeJson(200, null, $msg);
	 	return ;
    }

    public function index()
    {
        // TODO: Implement index() method.
        $this->response()->write("forbidden");
    }

    /**
     * 檢測客戶端IP是否具有權限訪問
     */
    protected function checkClientIpHasAccessAuthority():int
    {
    	$flag = 0;
    	$ip = $this->getClientIp();
    	$whiteIp = (new IpWhiteListModel)->queryByIpAddr($ip);

    	if ($whiteIp) {
    		if ($whiteIp['is_enable'] == 1) {
    			$flag = 1;
    		} else {
    			$this->writeJson(0, null, '访问受限, 您当前的IP为: '.long2ip($ip));
    		}
    	} else {
    		$this->writeJson(0, null, '尚未注册, 您当前的IP为: '.long2ip($ip));
    	}
    	return (int)$flag;

    }

    /**
     * 获取用户端真实IP
     */
    protected function getClientIp():int
    {
    	$ipNum = 0;
		$ip = $this->request()->getHeaders();
		if ($ip && isset($ip['x-real-ip']) && $ip['x-real-ip']) {
			$ip = array_pop($ip['x-real-ip']);
			$ipNum = ip2long($ip);
		}
		return (int)$ipNum;
    }

}
