<?php
namespace Addon\ajax\Controller;
use Common\Controller\CommonController;

class AccountController extends BaseController
{
	public function _initialize()
	{
		parent::_initialize();
	}
	
	
	public function login()
	{
		
		$userName=I('post.e_user');
		$passWord=I('post.e_pass');
		
		$dUserInfo=D('UserInfo');
		
		$bRet=$dUserInfo->checkLogin($userName,$passWord);
		
		if(!$bRet)
		{
			$msgData=$dUserInfo->getErrorMsg();
			$this->ajaxCallMsg($msgData['error_code'],$msgData['msg']);
		}
		
		$this->ajaxCallMsg(0,'登录成功!');
	}

}