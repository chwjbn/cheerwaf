<?php
namespace Common\Model;

class WafRuleIpModel extends BaseModel
{
	public function actionIp($data)
	{
		
		$ip=$data['ip'];
		$actionType=$data['action_type'];
		
		$ipRule='/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/i';
		if(!preg_match($ipRule,$ip))
		{
			$this->setErrorMsg(251,'请输入正确的IP地址!');
			return false;
		}
		
		$actionTypeList=array('act_remove_black','act_remove_white','act_add_black','act_add_white');
		if(!in_array($actionType,$actionTypeList))
		{
			$this->setErrorMsg(252,'请选择正确的操作类型!');
			return false;
		}
		
		
		
		
		$redisHandle=\Com\Chw\RedisLib::getInstance('REDIS_DEFAULT');
		$redisHandle->select(1);
		
		
		if($actionType=='act_remove_black')
		{
			$dataKey=sprintf('black_ip_%s',$ip);
		    $redisHandle->del($dataKey);
		}
		
		if($actionType=='act_remove_white')
		{
			$dataKey=sprintf('white_ip_%s',$ip);
		    $redisHandle->del($dataKey);
		}
		
		if($actionType=='act_add_black')
		{
			$dataKey=sprintf('black_ip_%s',$ip);
		    $redisHandle->set($dataKey,102,3600*24*7);
		}
		
		if($actionType=='act_add_white')
		{
			$dataKey=sprintf('white_ip_%s',$ip);
		    $redisHandle->set($dataKey,102,3600*24*7);
		}
		
		return true;
		
	}
}