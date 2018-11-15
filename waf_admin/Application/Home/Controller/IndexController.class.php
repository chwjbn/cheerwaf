<?php
namespace Home\Controller;
class IndexController extends BaseController 
{
    
	public function index()
	{
		redirect('/index/login.html');
	}
	
	public function login()
	{
		$this->assign('jump_url','/main/index.html');
		$this->display('login');
	}
}