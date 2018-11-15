<?php
namespace Home\Controller;

class MainController extends AuthController 
{
    
	public function index()
	{
		$dUserInfo=D('UserInfo');
		$userInfo=$dUserInfo->getCurrentUserInfo();
		
		$this->assign('userInfo',$userInfo);
		
		
		$this->display('index');		
	}
	
	public function get_nav_list()
	{
		$dSystemMenu=D('SystemMenu');
		
		$dataList=$dSystemMenu->getAllShowMenuList();
		
		$this->assign('dataList',$dataList);
		
		$this->display('get_nav_list');
	}
	
	public function main()
	{
		$this->display('main');
	}
}