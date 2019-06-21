<?php
namespace App\Model;

use App\Utility\Pool\Mysql\MysqlObject;


class BaseModel
{

	// private   $conf = 'MYSQL';
	protected $db;
	protected $softDeleteFieldName = 'delete_at';

	public function __construct(MysqlObject $dbObject)
	{
		if (is_null($this->db) || !$this->db instanceof MysqlObject) {
			$this->setDb($dbObject);
		}
	}

	private function setDb(MysqlObject $dbObject):void
    {
        $this->db = $dbObject;
    }

    /**
     * 获取Db连接
     * @return MysqlObject
     */
    protected function getDb():MysqlObject
    {
        return $this->db;
    }

    /**
     * 获取Db连接
     * @return MysqlObject
     */
    function getDbConnection():MysqlObject
    {
        return $this->db;
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