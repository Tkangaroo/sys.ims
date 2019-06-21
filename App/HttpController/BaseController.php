<?php
namespace App\HttpController;

use App\Exception\ESException;
use App\Model\IpWhiteList\IpWhiteListBean;
use App\Utility\Tools\ESResponseTool;
use EasySwoole\Http\AbstractInterface\Controller;
use App\Utility\Tools\ESConfigTool;
use App\Utility\Pool\Mysql\MysqlObject;
use App\Utility\Pool\Mysql\MysqlPool;
use App\Model\IpWhiteList\IpWhiteListModel;
use EasySwoole\Http\UrlParser;

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
     * @param string|null $action
     * @return bool|null
     */
    protected function onRequest(?string $action): ?bool
	{

        var_dump(UrlParser::pathInfo($this->request()->getUri()->getPath()));
	   if (parent::onRequest($action)) {
	       try {
	           // 均需要验证白名单
               $this->checkClientIpHasAccessAuthority();
               // 根据这个做登录什么的限制
               $target = $this->parseRequestTarget();
               var_dump($target);
               if ($target['module'] === 'Admin') {
                   // 后台模块 除登录模块外，均需验证是否处于登录状态
               } else if ($target['module'] === 'Api') {
                   // API模块
               } else {
                   throw new ESException($this->confTool()->lang('module_not_found'));
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
        return false;
    }

    /**
     * 檢測客戶端IP是否具有權限訪問
     * @throws ESException
     * @throws \EasySwoole\Component\Pool\Exception\PoolEmpty
     * @throws \EasySwoole\Component\Pool\Exception\PoolException
     * @throws \Throwable
     */
    protected function checkClientIpHasAccessAuthority():void
    {
        $whiteIp = MysqlPool::invoke(function (MysqlObject $db) {
            $ipWhiteListBean = new IpWhiteListBean();
            $ipWhiteListBean->setIpAddr($this->getClientIp());
            return (new IpWhiteListModel($db))->queryByIpAddr($ipWhiteListBean);
        });

    	if (is_null($whiteIp))
    	    throw new ESException($this->confTool()->get('ip_has_refused'));

    	if (!$whiteIp->getIsEnable())
    	    throw new ESException($this->confTool()->get('ip_has_disable'));
    	return ;

    }

    /**
     * 获取用户端真实IP
     * @return string|null
     */
    protected function getClientIp():?string
    {
    	$ipAddr = '';
		$ip = $this->request()->getHeaders();
		if ($ip && isset($ip['x-real-ip']) && $ip['x-real-ip']) {
			$ip = array_pop($ip['x-real-ip']);
			$ipAddr = $ip;
		}
		unset($ip);
		return $ipAddr;
    }

    /**
     * 格式化请求方法等
     * @return array
     */
    protected function parseRequestTarget():array
    {
        $targetStr = $this->request()->getRequestTarget();
        if (strstr('?', $targetStr)) {
            $targetStr = substr($targetStr,0,strpos($targetStr, '?'));
        }
        substr($targetStr,0,1) === '/' && ($targetStr = substr($targetStr,1));
        $target = explode('/', $targetStr);
        $target = array_filter($target);
        $arr = [
            'module'        => $target[0]??'',
            'controller'    => $target[1]??'',
            'action'        => $target[2]??'index',
        ];
        unset($target);
        return $arr;
    }

}
