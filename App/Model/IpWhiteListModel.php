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
}