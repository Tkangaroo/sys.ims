<?php
/**
 * Created by PhpStorm.
 * User: speauty
 * Date: 2019/6/21
 * Time: 9:21
 */

namespace App\Model;

use App\Base\BaseModel;
use App\Utility\ESTools;
use Lib\Exception\ESException;
use Lib\Logistic;


class IpWhiteListModel extends BaseModel
{
    protected $table = 't_ip_white_list';

    /**
     * 创建IP白名单记录
     * @param array $form
     * @return bool
     * @throws ESException
     * @throws \EasySwoole\Mysqli\Exceptions\ConnectFail
     * @throws \EasySwoole\Mysqli\Exceptions\PrepareQueryFail
     * @throws \Throwable
     */
    public function createIpWhiteSingle(array $form):bool
    {
        $data = [
            'ip_addr' => 0,
            'is_enable' => 0,
            'comments' => '',
            'create_at' => time(),
            'update_at' => time()
        ];

        foreach ($data as $k => &$v) {
            if (isset($form[$k])) $v = $form[$k];
            if ($k == 'ip_addr') $v = ip2long($v);
        }
        // 查重
        $uniqueFilterWhere = [
            'ip_addr' => $data['ip_addr']
        ];

        if (ESTools::checkUniqueByAField($this->getDb(), $this->table, $uniqueFilterWhere)) {
            throw new ESException(Logistic::getMsg(Logistic::L_RECORD_NOT_UNIQUE), Logistic::L_RECORD_NOT_UNIQUE);
        }
        unset($k, $v,$form, $uniqueFilterWhere);
        return $this->getDb()->insert($this->table, $data);
    }

    /**
     * @param $ipAddr
     * @return array|null
     * @throws \EasySwoole\Mysqli\Exceptions\ConnectFail
     * @throws \EasySwoole\Mysqli\Exceptions\PrepareQueryFail
     * @throws \Throwable
     */
    public function queryByIpAddr($ipAddr):?array
    {
        $whiteIp = [];
        if ($ipAddr) {
            $this->getDb()->where('ip_addr', $ipAddr, '=');
            $this->setSoftDeleteWhere();
            $whiteIp = $this->getDb()->getOne($this->table, 'id,is_enable');
        }
        return $whiteIp;
    }

}