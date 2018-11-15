<?php
namespace Common\Model;

class SystemMenuModel extends DataBaseModel
{
    protected $tableName = 'system_menu';
	
	protected $_validate=array(
	   array('menu_level',array(0,1,2),'请选择正确的菜单层级!',0,'in',3),
	   array('menu_pid',array(0,999999999),'请选择正确的上级菜单!',0,'between',3),
	   array('menu_title','1,50','请输入正确的菜单简称!',0,'length',3),
	   array('menu_full_title','1,100','请输入正确的菜单全称!',0,'length',3),
	   array('menu_url','1,1024','请输入正确的菜单URL!',0,'length',3),
	   array('menu_icon','1,1024','请输入正确的图标地址!',0,'length',3),
	   array('menu_order',array(0,999999999),'请输入正确的菜单序号!',0,'between',3),
	);
	
	protected $_auto = array(
	   array('state',1,1,'string')
	);
	
	protected function initShowMap()
	{
		
		$this->setShowMap('menuLevelList',array(0=>'顶级',1=>'一级',2=>'二级'));
		
		$this->setShowMap('stateList',array(0=>'禁用',1=>'启用'));
		
		$level0DataList=$this->where(array('menu_level'=>0))->select();
				
		$menuLevel0List=array();
		
		foreach($level0DataList as $item)
		{
			$menuLevel0List[$item['id']]=$item['menu_title'];
		}
		
		$this->setShowMap('menuLevel0List',$menuLevel0List);
	}
	
	
	public function getSubMenuList($menu_pid,$menu_level=0)
	{	
		$dataList=array();
		
		$subDataList=$this->where(array('menu_pid'=>$menu_pid,'menu_level'=>$menu_level))->select();
		
		foreach($subDataList as $item)
		{
			$dataList[$item['id']]=$item['menu_title'];
		}
		
		return $dataList;
	}
	
	
	public function addData($data)
	{
		
		if(!$this->create($data))
		{
			$this->setErrorCode(250);
			return false;
		}

		$id=$this->add();
		
		return $id;
		
	}
	
	public function editData($data)
	{
		$id=$data['id'];
		$nCount=$this->where(array('id'=>intval($id)))->count();
		if($nCount<1)
		{
			$this->setErrorMsg(250,'当前保存的菜单信息不存在,操作失败!');
			return false;
		}
		
		if(!$this->create($data))
		{
			$this->setErrorCode(250);
			return false;
		}
		
		$this->save();
		
		return true;
		
	}
	
	public function delData($data)
	{
		$id=$data['id'];
		
		$nCount=$this->where(array('menu_pid'=>intval($id)))->count();
		if($nCount>0)
		{
			$this->setErrorMsg(250,'当前菜单下还存在子菜单,无法删除!');
			return false;
		}
		
		$this->where(array('id'=>intval($id)))->delete();
		
		return true;
	}

	
	public function getAllShowMenuList()
	{
		$dataList=$this->where(array('state'=>1))->order('menu_level asc,menu_order asc')->select();
		return $dataList;
		
	}
	
	public function getPageShowList($option=array())
	{
		
		$order='menu_level asc,menu_order asc';
		$page=intval($option['page']);
		$pageSize=intval($option['pageSize']);
		if($pageSize<1)
		{
			$pageSize=50;
		}
		
		$where=array('menu_pid'=>0);
		
		
		
		if($option['menu_level0']>0)
		{
			$where['menu_pid']=$option['menu_level0'];
		}
		
		if($option['menu_level1']>0)
		{
			$where['menu_pid']=$option['menu_level1'];
		}
		
		if($option['state']>-1)
		{
			$where['state']=$option['state'];
		}
		
		if($option['kw'])
		{
			$where['menu_title|menu_full_title']=array('like','%'.$option['kw'].'%');
		}
		
		$dataReturn=$this->getPageList($where,$order,$page,$pageSize);
		
		
		$showMap=$this->getShowMap();
		

		foreach($dataReturn['dataList'] as &$item)
		{
			$item['state_name']=$showMap['stateList'][$item['state']];
			
			$item['menu_level_name']=$showMap['menuLevelList'][$item['menu_level']];
			
			if($item['menu_level']==0)
			{
				$item['menu_pname']='--';
			}
			
			if($item['menu_level']==1)
			{
				$item['menu_pname']=$showMap['menuLevel0List'][$item['menu_pid']];
			}
			
			if($item['menu_level']==2)
			{
				$item['menu_pname']=$showMap['menuLevel1List'][$item['menu_pid']];
			}
		}
		
		
		return $dataReturn;
		
	}
	
}