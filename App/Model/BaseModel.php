<?php
namespace App\Model;

use EasySwoole\Mysqli\Mysqli;
use EasySwoole\Mysqli\Config as MysqliConfig;
use EasySwoole\EasySwoole\Config as ESConfig;

class BaseModel
{
	
	private   $conf = 'MYSQL';
	protected $db   = NULL;

	public function __construct()
	{
		if (is_null($this->db) || !$this->db instanceof Mysqli) {
			$this->db = $this->getDb();
		}
	}

    protected function getDb():Mysqli
    {
    	$conf = new MysqliConfig(ESConfig::getInstance()->getConf($this->conf));
		return new Mysqli($conf);
    }
}