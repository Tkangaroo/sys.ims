<?php
/**
 * Created by PhpStorm.
 * User: speauty
 * Date: 2019/6/21
 * Time: 9:21
 */

namespace App\Model\IpWhiteList;

use App\Model\BaseModel;
use EasySwoole\EasySwoole\ServerManager;
use EasySwoole\Utility\SnowFlake;


class IpWhiteListModel extends BaseModel
{
    protected $table = 't_ip_white_list';

    /**
     * 根据IP查询白名单记录
     * @param IpWhiteListBean $ipWhiteListBean
     * @return IpWhiteListBean|null
     * @throws \EasySwoole\Mysqli\Exceptions\ConnectFail
     * @throws \EasySwoole\Mysqli\Exceptions\PrepareQueryFail
     * @throws \Throwable
     */
    public function queryByIpAddr(IpWhiteListBean $ipWhiteListBean):?IpWhiteListBean
    {
        $whiteIp = [];
        if ($ipAddr = $ipWhiteListBean->getIpAddr(false)) {
            var_dump($ipAddr);
            $this->getDb()->where('ip_addr', '=', $ipAddr);
            $this->setSoftDeleteWhere();
            $whiteIp = $this->getDb()->getOne($this->table, 'id,is_enable');
        }
        return $whiteIp?new IpWhiteListBean($whiteIp):null;
    }

}