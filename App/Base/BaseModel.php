<?php
/**
 * Created by PhpStorm.
 * User: speauty
 * Date: 2019/6/25
 * Time: 14:26
 */
namespace App\Base;

use App\Utility\Pool\Mysql\MysqlObject;
use EasySwoole\Component\Di;

/**
 * Class BaseModel
 * the basic model
 * @package App\Base
 */
class BaseModel
{
    protected $Di;
    protected $db;
    protected $softDeleteFieldName = 'delete_at';

    public function __construct(MysqlObject $dbObject)
    {
        if (is_null($this->db) || !$this->db instanceof MysqlObject) {
            $this->setDb($dbObject);
        }
        if (is_null($this->Di) || !$this->Di instanceof Di) {
            $this->Di = Di::getInstance();
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