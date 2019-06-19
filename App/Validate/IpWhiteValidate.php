<?php
namespace App\Validate;

use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Validate\Validate;
use App\Common;


class IpWhiteValidate
{
	public function check(array $data)
	{
		$flag = false;
		$valitor = new Validate();
		if (isset($data['id'])) {
			$valitor->addColumn('id','主键')->required('不能为空')->numeric('只能为数字类型');
		}
		$valitor->addColumn('ip_addr','IP地址')->required('不能为空')->isIp('无效格式');
		$valitor->addColumn('is_enable','是否激活')->required('不能为空')->inArray([0,1], true, '无效格式');
		$valitor->addColumn('comments', '备注')->lengthMax(50, '不能超过50字');
		$flag = $valitor->validate($data);
		var_dump($flag);
		$msg = $valitor->getError()->getErrorRuleMsg()?:$valitor->getError()->getColumnErrorMsg();
		(new Common())->writeJson(0, null, $msg);
		return $flag;
	}
}