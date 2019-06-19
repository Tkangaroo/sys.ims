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

	public function getTest():array
	{
		return $this->db->get($this->table);
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