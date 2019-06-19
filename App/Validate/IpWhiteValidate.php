<?php
namespace App\Validate;

use EasySwoole\Validate\Validate;
use EasySwoole\Http\Response;
use App\Common;


class IpWhiteValidate
{
	public function check(Response $response, array $data)
	{
		$flag = false;
		$valitor = new Validate();
		if (isset($data['id'])) {
			$valitor->addColumn('id','主键')->required('主键不能为空')->numeric('主键只能为数字类型');
		}
		$valitor->addColumn('ip_addr','IP地址')->required('IP地址不能为空')->isIp('IP地址无效格式');
		$valitor->addColumn('is_enable','是否激活')->required('是否激活不能为空')->inArray([0,1], true, '是否激活无效格式');
		$valitor->addColumn('comments', '备注')->lengthMax(50, '备注不能超过50字');
		$flag = $valitor->validate($data);
		if (!$flag) {
			$msg = $valitor->getError()->getErrorRuleMsg()?:$valitor->getError()->getColumnErrorMsg();
			(new Common())->writeJsonByResponse($response, 0, null, $msg);
		}
		return $flag;
	}
}