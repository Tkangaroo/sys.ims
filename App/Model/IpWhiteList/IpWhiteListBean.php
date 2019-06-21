<?php
/**
 * Created by PhpStorm.
 * User: speauty
 * Date: 2019/6/21
 * Time: 9:22
 */

namespace App\Model\IpWhiteList;

use EasySwoole\Spl\SplBean;


class IpWhiteListBean extends SplBean
{
    protected $id;
    protected $ip_addr;
    protected $is_enable;
    protected $comments;
    protected $create_at;
    protected $update_at;
    protected $delete_at;

    public function getId():int
    {
        return (int)$this->id;
    }

    public function setIpAddr(string $ipAddr):void
    {
        if ($ipAddr) $this->ip_addr = ip2long($ipAddr);
    }

    public function getIpAddr(bool $isConvert = true):?string
    {
        if ($isConvert) {
            return long2ip($this->ip_addr);
        } else {
            return $this->ip_addr;
        }
    }

    public function setIsEnable(int $isEnable):void
    {
        $this->is_enable = $isEnable;
    }

    public function getIsEnable():?int
    {
        return $this->is_enable;
    }

    public function setComments($comments):void
    {
        $this->comments = $comments;
    }

    public function getComments():?string
    {
        return $this->comments;
    }

    public function setCreateAt():void
    {
        $this->create_at = time();
    }

    public function getCreateAt():int
    {
        return (int)$this->create_at;
    }

    public function setUpdateAt():void
    {
        $this->update_at = time();
    }

    public function getUpdateAt():int
    {
        return (int)$this->update_at;
    }

    public function setDeleteAt():void
    {
        $this->delete_at = time();
    }

    public function getDeleteAt():int
    {
        return (int)$this->delete_at;
    }
}