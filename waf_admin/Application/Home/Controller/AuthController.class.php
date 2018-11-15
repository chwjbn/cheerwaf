<?php
namespace Home\Controller;
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
			$this->show('<script language="javascript">window.top.location.href="/index/login.html";</script>','utf-8');
			exit();
		}
	}
}