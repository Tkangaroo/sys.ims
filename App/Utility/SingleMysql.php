<?php
namespace App\Utility;


class SingleMysql
{
	private $conf = 'MYSQL';

	protected function getDb()
	{
		$conf = new \EasySwoole\Mysqli\Config(\EasySwoole\EasySwoole\Config::getInstance()->getConf($this->conf));
		return new Mysqli($conf);
	}
}