<?php
namespace App\Model;

use App\Model\BaseModel;
use EasySwoole\EasySwoole\ServerManager;
use EasySwoole\Utility\SnowFlake;


class IpWhiteListModel extends BaseModel
{
	protected $table = 't_ip_whitelist';

	public function __construct()
	{
		parent::__construct();
	}

	public function createIpAddrSingle(array $form):int
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
		unset($v,$form);
		return $this->db->insert($this->table, $data);
	}

	/**
	 * 根據IP查詢數據
	 */
	public function queryByIpAddr(int $ipAddr = 0):array
	{
		$whiteIp = [];
		if ($ipAddr) {
			$this->db->where('ip_addr', $ipAddr);
			$this->db->where('delete_at', 0);
			$whiteIp = $this->db->getOne($this->table, 'id,is_enable');
		}

		return (array)$whiteIp;
	}
}