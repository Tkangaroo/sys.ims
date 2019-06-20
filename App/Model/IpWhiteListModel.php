<?php
namespace App\Model;

use App\Exception\ESException;
use App\Utility\Tools\ESMysqliTool;
use Lib\Language\Lang;

class IpWhiteListModel extends BaseModel
{
    // 当前表名
    private $table = 't_ip_whitelist';
    // 默认字段数据
    private $defaultFieldData = [
        'ip_addr' => 0,
        'is_enable' => 0,
        'comments' => '',
        'create_at' => 0,
        'update_at' => 0
    ];

	public function __construct()
	{
		parent::__construct();
	}

    /**
     * @param array $form
     * @return int
     * @throws \EasySwoole\Mysqli\Exceptions\ConnectFail
     * @throws \EasySwoole\Mysqli\Exceptions\PrepareQueryFail
     * @throws \Throwable
     */
	public function createIpAddrSingle(array $form):int
	{
		$data =  $this->defaultFieldData;

		foreach ($data as $k => &$v) {
			if (isset($form[$k])) $v = $form[$k];
			if ($k == 'ip_addr') $v = ip2long($v);
		}
		// 查重
        $uniqueFilterWhere = [
            'ip_addr' => $data['ip_addr']
        ];
        if ((new ESMysqliTool())->checkUniqueByAField($this->db, $this->table, $uniqueFilterWhere)) {
            throw new ESException((new Lang())->get('ip_white.not_unique'));
        }
        // 设置时间戳
        $data['create_at'] = time();
        $data['update_at'] = time();

        $saveFlag = $this->db->insert($this->table, $data);
        unset($v,$form, $data, $uniqueFilterWhere);
        return $saveFlag;
	}

    /**
     * 根据IP查询数据
     * @param int $ipAddr IP地址
     * @return array
     * @throws \EasySwoole\Mysqli\Exceptions\ConnectFail
     * @throws \EasySwoole\Mysqli\Exceptions\PrepareQueryFail
     * @throws \Throwable
     */
	public function queryByIpAddr(int $ipAddr = 0):array
	{
		$whiteIp = [];
		if ($ipAddr) {
            $this->db->where('ip_addr', $ipAddr);
            $this->setSoftDeleteWhere();
            $whiteIp = $this->db->getOne($this->table, 'id,is_enable');
		}

		return (array)$whiteIp;
	}
}