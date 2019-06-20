<?php
/**
 * Created by PhpStorm.
 * User: speauty
 * Date: 2019/6/19
 * Time: 23:08
 */

namespace App\Utility\Tools;

use App\Traits\SqlTrait;


/**
 * Class ESMysqliTool
 * Mysqli 工具类(结合ES框架使用)
 * @package App\Utility\Tools
 */
class ESMysqliTool
{
    use SqlTrait;


    /**
     * 快速将数组转化成查询条件
     * @param \EasySwoole\Mysqli\Mysqli $db
     * @param array $arr
     * @param int $ensureWhereNotEmptyFlag
     * @return bool
     */
    public function quickParseArr2WhereMap(\EasySwoole\Mysqli\Mysqli $db, array $arr, int $ensureWhereNotEmptyFlag = 0):bool
    {
        $parseFlag = false; // 响应数据
        $buildNum = 0;      // 构建where次数
        if ($arr) {
            foreach ($arr as $k => $v) {
                // 保证字符串为有效的字段格式
                if ($this->checkStrIsAValidFieldName($k)) {
                    $db->where($k, $v[0], $v[1]??'=');
                    $buildNum++;
                }
                continue;
            }
        }
        // 设置响应数据 根据是否保证查询条件为空和构建条件次数
        if (!$ensureWhereNotEmptyFlag || ($ensureWhereNotEmptyFlag && $buildNum)) $parseFlag = true;
        return $parseFlag;
    }

    /**
     * 检测数据唯一性
     * @param \EasySwoole\Mysqli\Mysqli $db
     * @param string $tableName 检测表明
     * @param array $uniqueFilterWhereArr 过滤数组
     * @return bool
     * @throws \EasySwoole\Mysqli\Exceptions\ConnectFail
     * @throws \EasySwoole\Mysqli\Exceptions\PrepareQueryFail
     * @throws \Throwable
     *
     */
    public function checkUniqueByAField(\EasySwoole\Mysqli\Mysqli $db, string $tableName, array $uniqueFilterWhereArr):bool
    {
        $uniqueFlag = false;
        $this->quickParseArr2WhereMap($db, $uniqueFilterWhereArr);
        $uniqueFlag = (bool)$db->getValue($tableName, 'id', 1);
        return $uniqueFlag;
    }
}