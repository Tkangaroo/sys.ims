<?php
namespace App\Model;

class BaseModel
{
	
	private   $conf = 'MYSQL';
	protected $db   = NULL;

	public function __construct()
	{
		if (is_null($this->db)) {
			$this->db = $this->getDb();
		}
	}

    protected function getDb()
    {
    	$conf = new \EasySwoole\Mysqli\Config(\EasySwoole\EasySwoole\Config::getInstance()->getConf($this->conf));
		return new \Mysqli($conf);
    }
}