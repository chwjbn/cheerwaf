<?php
namespace Home\Controller;

class SystemController extends AuthController 
{
    
	public function menu()
	{
		$option=array(
			'page'=>I('post.page',1,'intval'),
			'pageSize'=>I('post.pageSize',50,'intval'),
			'menu_level0'=>I('post.menu_level0',0,'intval'),
		    'menu_level1'=>I('post.menu_level1',0,'intval'),
			'state'=>I('post.state',-1,'intval'),
			'kw'=>I('post.kw')
		);
		
		$this->assign('option',$option);
		
		
		$dSystemMenu=D('SystemMenu');
		
		$pageOption=$dSystemMenu->getShowMap();
		
		
		$menuLevel1List=$dSystemMenu->getSubMenuList($option['menu_level0'],1);
		$pageOption['menuLevel1List']=$menuLevel1List;
		
		
		$this->assign('pageOption',$pageOption);

		$pageData=$dSystemMenu->getPageShowList($option);	
		$this->assign('pageData',$pageData);
				
		$this->display('menu');
	}
	
	public function menu_add_pop()
	{
		$option=array(
		   'menu_level'=>I('get.menu_level',0,'intval')
		);
		
		$this->assign('option',$option);
		
		$dSystemMenu=D('SystemMenu');
		
		$pageOption=$dSystemMenu->getShowMap();
		$this->assign('pageOption',$pageOption);
		
		$this->display('menu_add_pop');
	}
	
	public function menu_edit_pop()
	{
		$option=array(
		   'menu_level'=>I('get.menu_level',0,'intval'),
		   'id'=>I('get.id',0,'intval'),
		);
		
		
		
		$dSystemMenu=D('SystemMenu');
		
		$pageOption=$dSystemMenu->getShowMap();
		$this->assign('pageOption',$pageOption);
		
		$data=$dSystemMenu->getData($option['id']);
		$this->assign('data',$data);
		
		if(!$option['menu_level']&&$data)
		{
			$option['menu_level']=$data['menu_level'];
		}
		
		$this->assign('option',$option);
		
		$this->display('menu_edit_pop');
	}

}