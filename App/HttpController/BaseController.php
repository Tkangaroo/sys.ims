<?php
namespace App\HttpController;

use App\Exception\ESException;
use App\Utility\Tools\ESResponseTool;
use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Http\Message\Status;
use App\Model\IpWhiteListModel;
use App\Utility\Tools\ESConfigTool;


/**
 * Class BaseController
 * @package App\HttpController
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
	       try {
	           var_dump($this->request()->getUri()['path']);
               $this->checkClientIpHasAccessAuthority();
               //判断是否登录
               if (0/*伪代码*/) {
                   throw new ESException($this->confTool()->lang('login_expired'));
               }
               $this->code = 200;
           } catch (ESException $e) {
                $this->message = $e->report();
           } catch (\Throwable $e) {
                $this->message = $e->getMessage();
           } finally {
                if ($this->code == 200) {
                    return true;
                } else {
                    (new ESResponseTool())->writeJsonByResponse($this->response(), $this->code, $this->data, $this->message);
                    return false;
                }
           }
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

    /**
     * 获取配置类工具
     * @return ESConfigTool
     */
    protected function confTool():ESConfigTool
    {
        return new ESConfigTool();
    }

    public function index()
    {
        // TODO: Implement index() method.
        $this->response()->write("forbidden");
    }

    /**
     * 檢測客戶端IP是否具有權限訪問
     * @throws ESException
     * @throws \EasySwoole\Mysqli\Exceptions\ConnectFail
     * @throws \EasySwoole\Mysqli\Exceptions\PrepareQueryFail
     * @throws \Throwable
     */
    protected function checkClientIpHasAccessAuthority():void
    {
    	$ip = $this->getClientIp();
    	$whiteIp = (new IpWhiteListModel)->queryByIpAddr($ip);

    	if (!$whiteIp)
    	    throw new ESException($this->confTool()->get('ip_has_refused'));

    	if (!isset($whiteIp['is_enable']) || !$whiteIp['is_enable'])
    	    throw new ESException($this->confTool()->get('ip_has_disable'));
    	return ;

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
