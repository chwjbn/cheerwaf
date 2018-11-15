<?php
namespace Addon\ajax\Controller;
use Common\Controller\CommonController;

class SystemController extends AuthController
{
	public function _initialize()
	{
		parent::_initialize();
	}
	
	
	public function menu_add_pop()
	{
		$data=array(
		    'menu_level'=>I('post.menu_level',0,'intval'),
			'menu_pid'=>I('post.menu_pid',0,'intval'),
			'menu_title'=>I('post.menu_title','','trim'),
			'menu_full_title'=>I('post.menu_full_title','','trim'),
			'menu_order'=>I('post.menu_order',0,'intval'),
			'menu_url'=>I('post.menu_url','#'),
			'menu_icon'=>I('post.menu_icon','#'),
		);
		
		if(!$data['menu_url'])
		{
			$data['menu_url']='#';
		}
		
		if(!$data['menu_icon'])
		{
			$data['menu_icon']='#';
		}
		
		if(!$data['menu_full_title'])
		{
			$data['menu_full_title']=$data['menu_title'];
		}
		
		$dSystemMenu=D('SystemMenu');
		
		$nId=$dSystemMenu->addData($data);
		
		if(!$nId)
		{
			$msgData=$dSystemMenu->getErrorMsg();
			$this->ajaxCallMsg($msgData['error_code'],$msgData['msg']);
		}
		
		$this->ajaxCallMsg(0,'操作成功!');
	}
	
	public function menu_edit_pop()
	{
		$data=array(
			'id'=>I('post.id',0,'intval'),
		    'menu_level'=>I('post.menu_level',0,'intval'),
			'menu_pid'=>I('post.menu_pid',0,'intval'),
			'menu_title'=>I('post.menu_title',''),
			'menu_full_title'=>I('post.menu_full_title',''),
			'menu_order'=>I('post.menu_order',0,'intval'),
			'menu_url'=>I('post.menu_url','#'),
			'menu_icon'=>I('post.menu_icon','#')
		);
		
		if(!$data['menu_full_title'])
		{
			$data['menu_full_title']=$data['menu_title'];
		}
		
		$dSystemMenu=D('SystemMenu');
		
		$nId=$dSystemMenu->editData($data);
		
		if(!$nId)
		{
			$msgData=$dSystemMenu->getErrorMsg();
			$this->ajaxCallMsg($msgData['error_code'],$msgData['msg']);
		}
		
		$this->ajaxCallMsg(0,'操作成功!');
	}
	
	public function menu_del()
	{
		$data=array(
			'id'=>I('get.id',0,'intval'),
		);
		
		
		$dSystemMenu=D('SystemMenu');
		
		$nId=$dSystemMenu->delData($data);
		
		if(!$nId)
		{
			$msgData=$dSystemMenu->getErrorMsg();
			$this->ajaxCallMsg($msgData['error_code'],$msgData['msg']);
		}
		
		$this->ajaxCallMsg(0,'操作成功!');
	}

}