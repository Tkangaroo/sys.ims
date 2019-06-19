<?php
namespace App\Model;

use App\Model\BaseModel;
use EasySwoole\EasySwoole\ServerManager;
use EasySwoole\Utility\SnowFlake;


class IpWhiteListModel extends BaseModel
{
	protected $table = 't_ip_whitelist';

	protected function __construct()
	{
		parent::__construct();
	}

	public function getTest()
	{
		dump($this->db->get($this->table));
	}
}