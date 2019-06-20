<?php
namespace App\Model;

use EasySwoole\Mysqli\Mysqli;
use EasySwoole\Mysqli\Config as MysqliConfig;
use EasySwoole\EasySwoole\Config as ESConfig;

class BaseModel
{
	
	private   $conf = 'MYSQL';
	protected $db   = NULL;
	protected $softDeleteFieldName = 'delete_at';

	public function __construct()
	{
		if (is_null($this->db) || !$this->db instanceof Mysqli) {
			$this->db = $this->getDb();
		}
	}

    /**
     * 获取Mysql连接
     * @return Mysqli
     */
    protected function getDb():Mysqli
    {
    	$conf = new MysqliConfig(ESConfig::getInstance()->getConf($this->conf));
		return new Mysqli($conf);
    }

    /**
     * 设置过滤软删除数据条件
     * @param string $softDeleteFieldName 软删除字段名
     */
    protected function setSoftDeleteWhere(string $softDeleteFieldName = 'delete_at')
    {
        $this->db->where($this->softDeleteFieldName, 0, '=');
    }
}