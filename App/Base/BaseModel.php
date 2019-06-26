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
    protected $table;

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
     * to get connection of db
     * @return MysqlObject
     */
    protected function getDb():MysqlObject
    {
        return $this->db;
    }

    /**
     * to get connection of db
     * @return MysqlObject
     */
    public function getDbConnection():MysqlObject
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

    /**
     * @param array $pageConf
     * @param array|null $fieldsName
     * @param array|null $where
     * @param string|null $orderFieldName
     * @param string|null $orderType
     * @return array|null
     * @throws \Throwable
     */
    public function queryDataOfPagination(array $pageConf, array $fieldsName = null, array $where = null, string $orderFieldName = null, string $orderType = null):?array
    {
        if (is_null($orderFieldName)) $orderFieldName = 'id';
        if (is_null($orderType)) $orderType = 'DESC';
        $this->Di->get('ESTools')->quickParseArr2WhereMap($this->db, $where);
        $this->db->orderBy($orderFieldName, $orderType);
        if ($this->table) {
            $total = $this->db->count($this->table, '1');
            if ($total > 0) {
                $list = $this->db->get(
                    $this->table,
                    [($pageConf['page']-1)*$pageConf['limit'],$pageConf['limit']],
                    is_null($fieldsName)?implode(',',$fieldsName):'*'
                );
                var_dump($this->db->getLastQuery());
            } else {
                $list = null;
            }

        } else {
            $total = 0;
            $list = null;
        }
        return [
            'total' => $total,
            'list' => $list
        ];
    }
}