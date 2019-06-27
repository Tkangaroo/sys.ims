<?php
namespace App\HttpController\Api;

use App\Utility\ESTools;
use Lib\Exception\ESException;
use App\Base\BaseController;
use App\Model\IpWhiteListModel;
use App\Utility\Pool\Mysql\MysqlObject;
use App\Utility\Pool\Mysql\MysqlPool;
use App\Validate\IpWhiteValidate;
use Lib\Logistic;

class IpWhiteList extends BaseController
{
    private $generalFieldsName = [
        'id', 'ip_addr', 'is_enable', 'comments', 'create_at'
    ];

    /**
     * to get a page data
     * @return bool
     * @throws \EasySwoole\Component\Pool\Exception\PoolEmpty
     * @throws \EasySwoole\Component\Pool\Exception\PoolException
     * @throws \Throwable
     */
    public function list():bool
    {
        $page = ESTools::getPageParams($this->request());
        $totalAndList = MysqlPool::invoke(function (MysqlObject $db) use ($page) {
            return (new IpWhiteListModel($db))->queryDataOfPagination($page, $this->generalFieldsName);
        });
        if ($totalAndList && isset($totalAndList['list']) && !empty($totalAndList['list'])) {
            foreach ($totalAndList['list'] as &$v) {
                $v['ip_addr'] = $v['ip_addr']?long2ip($v['ip_addr']):$v['ip_addr'];
                $v['create_at'] = date('Y-m-d H:i:s', $v['create_at']);
            }
        }
        unset($v);
        $this->logisticCode = Logistic::L_OK;
        $this->data = $totalAndList;
        $this->message = Logistic::getMsg(Logistic::L_OK);
        ESTools::writeJsonByResponse(
            $this->response(),
            $this->logisticCode,
            $this->message,
            $this->data
        );
        unset($esTools, $page, $whereParamsIdx, $where, $totalAndList);
        return false;
    }

    /**
     * to get a ip white
     * @return bool
     * @throws \EasySwoole\Component\Pool\Exception\PoolEmpty
     * @throws \EasySwoole\Component\Pool\Exception\PoolException
     * @throws \Throwable
     */
    public function get():bool
    {
        $paramsIdx = ['id'];
        $params = ESTools::getArgFromRequest($this->request(), $paramsIdx);
        $ipWhite = MysqlPool::invoke(function (MysqlObject $db) use ($params) {
            return (new IpWhiteListModel($db))->getOne($this->generalFieldsName, $params);
        });
        if ($ipWhite) {
            $ipWhite['ip_addr'] = $ipWhite['ip_addr']?long2ip($ipWhite['ip_addr']):$ipWhite['ip_addr'];
            $ipWhite['create_at'] = date('Y-m-d H:i:s', $ipWhite['create_at']);
        }
        $this->logisticCode = Logistic::L_OK;
        $this->data = $ipWhite;
        unset($paramsIdx, $params, $ipWhite);
        $this->message = Logistic::getMsg(Logistic::L_OK);
        ESTools::writeJsonByResponse(
            $this->response(),
            $this->logisticCode,
            $this->message,
            $this->data
        );
        return false;
    }

    /**
     * @return bool
     * @throws \Throwable
     */
	public function save():bool
	{
	    $paramsIdx = ['ip_addr', 'is_enable', 'comments'];
        $data = ESTools::getArgFromRequest($this->request(), $paramsIdx, 'getBody');
        try {
            (new IpWhiteValidate())->check($data, $paramsIdx);
            $saveResult = MysqlPool::invoke(function (MysqlObject $db) use ($data) {
                return (new IpWhiteListModel($db))->createIpWhiteSingle($data);
            });
            if ($saveResult) {
                $this->logisticCode = Logistic::L_OK;
                $this->message = Logistic::getMsg(Logistic::L_OK);
            } else {
                throw new ESException(
                    Logistic::getMsg(Logistic::L_RECORD_SAVE_ERROR),
                    Logistic::L_RECORD_SAVE_ERROR
                );
            }
        } catch (ESException $e) {
            $this->message = $e->report();
            $this->logisticCode = $e->getCode();
        } catch (\Throwable $e) {
            $this->message = $e->getMessage();
            $this->logisticCode = $e->getCode();
        }
        ESTools::writeJsonByResponse($this->response(), $this->logisticCode, $this->message);
        unset($data, $conf, $saveResult, $esResponse);
        return false;
	}
}