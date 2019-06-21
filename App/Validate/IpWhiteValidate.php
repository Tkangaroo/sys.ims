<?php
namespace App\Validate;

use App\Utility\Tools\ESValidateTool;
use EasySwoole\Validate\Validate;
use EasySwoole\Http\Response;


/**
 * Class IpWhiteValidate
 * 白名单验证类
 * @package App\Validate
 */
class IpWhiteValidate extends Validate
{

    private function setIdColumn():void
    {
        $this->addColumn('id','主键')->required()->notEmpty()->integer();
        return ;
    }

    private function setIpAddrColumn():void
    {
        $this->addColumn('ip_addr','IP地址')->required()->notEmpty()->isIp();
        return ;
    }

    private function setIsEnableColumn():void
    {
        $this->addColumn('is_enable','是否激活')->required()->inArray([0,1], false, '不在可选值[0,1]内');
        return ;
    }

    private function setCommentsColumn():void
    {
        $this->addColumn('comments', '备注')->lengthMax(50, '不能超过50字');
        return ;
    }


    /**
     * @param array $data
     * @return bool
     * @throws \Exception
     */
	public function check(array $data)
	{
		$flag = false;
		if (isset($data['id'])) {
            $this->setIdColumn();
		}
		$this->setIpAddrColumn();
		$this->setIsEnableColumn();
		$this->setCommentsColumn();
		$flag = $this->validate($data);
		if (!$flag) {
            (new ESValidateTool())->printValidateError($this);
		}
		return $flag;
	}
}