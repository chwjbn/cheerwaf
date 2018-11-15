<?php
namespace Addon\ajax\Controller;
use Common\Controller\CommonController;

class AuthController extends BaseController
{
	public function _initialize()
	{
		parent::_initialize();
		$this->checkLogin();
	}
	
	private function checkLogin()
	{
		$uid=session('admin_uid');
		
		if(!$uid)
		{
			$this->ajaxCallMsg(301,'你没有登录或者登录状态过期,请重新登录!');
		}
	}
}