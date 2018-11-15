<?php
namespace Common\Model;

class UserInfoModel extends DataBaseModel
{
    protected $tableName = 'user_info';

	
	public function checkLogin($username,$password)
	{
		
		if(!$username||!$password)
		{
			$this->setErrorMsg(200,'你输入的登录名或者登录密码不能为空!');
			return false;
		}
		
		$data=$this->where(array('user_name'=>$username))->find();
		
		if(!$data)
		{
			$this->setErrorMsg(201,'你输入的登录名不存在!');
			return false;
		}
		
		$checkPass=md5(sprintf('%s%s',$password,$data['user_pass_salt']));
		
		if($data['user_password']!=$checkPass)
		{
			$this->setErrorMsg(202,'你输入的登录密码错误!');
			return false;
		}
		
		session('admin_uid',$data['id']);
		
		return true;
	}
	
	public function getUserInfo($uid)
	{
		$data=$this->where(array('id'=>intval($uid)))->find();
		
		if(!$data)
		{
			return $data;
		}
		
		unset($data['user_password']);
		unset($data['user_pass_salt']);
		
		return $data;
	}
	
	public function getCurrentUserInfo()
	{
		$data=array();
		
		$uid=session('admin_uid');
		
		if(!$uid)
		{
			return $data;
		}
		
		$data=$this->getUserInfo($uid);
		return $data;
	}
	
}