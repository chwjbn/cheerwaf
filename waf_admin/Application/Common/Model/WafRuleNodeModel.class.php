<?php
namespace Common\Model;

class WafRuleNodeModel extends DataBaseModel
{
    protected $tableName = 'waf_rule_node';
	
	protected $_validate=array( 
	   array('rule_node_name','1,100','请输入正确的规则名称!',0,'length',3),
	   array('rule_order',array(0,999999999),'请输入正确的规则序号!',0,'between',3),
	   array('rule_site_id',array(1,999999999),'请选择正确的站点!',0,'between',3),
	   array('action_type','checkActionType','请选择正确的动作类别!',0,'callback',3),
	   array('action_target','checkActionTarget','请选择正确的动作目标!',0,'callback',3),
	   array('action_value','0,50','请输入正确的动作值!',0,'length',3),
	   array('action_target','checkActionValue','请输入正确的动作值!',0,'callback',3),
	);
	
	protected $_auto = array(
	   array('create_time','get_fulltime',1,'function'),
	   array('update_time','get_fulltime',3,'function'),
	   array('update_ip','get_client_addr',3,'function'),
	);
	
	protected function initShowMap()
	{
		
		$actionTypeList=array(
		  'white'=>'放行',
		  'black'=>'拦截',
		  'white_score'=>'可信加分',
		  'black_score'=>'可疑加分',
		);
		
		$actionTargetList=array(
			'session'=>'单次访问',
			'cookie_uuid'=>'访客',
			'cookie_uid'=>'注册用户',
			'ip'=>'访问IP'
		);
		
		$this->setShowMap('actionTypeList',$actionTypeList);
		$this->setShowMap('actionTargetList',$actionTargetList);	
		
		$dWafRuleSite=D('WafRuleSite');	
		$siteList=$dWafRuleSite->getAllDataKvList();
		$this->setShowMap('siteRuleList',$siteList);
	}
	
	protected function checkActionType($data)
	{
		
		$showMap=$this->getShowMap();
		$dataMap=$showMap['actionTypeList'];
		
		if(array_key_exists($data,$dataMap))
		{
			return true;
		}
		
		
		return false;
	}
	
	protected function checkActionTarget($data)
	{
		
		$showMap=$this->getShowMap();
		$dataMap=$showMap['actionTargetList'];
		
		if(array_key_exists($data,$dataMap))
		{
			return true;
		}
		
		return false;
	}
	
	protected function checkActionValue($data)
	{
		return true;
	}
	
	public function getCountSite($rule_site_id)
	{
		$nCount=$this->where(array('rule_site_id'=>intval($rule_site_id)))->count();
		return $nCount;
	}
	
	public function addData($data)
	{
		
		if(!$this->create($data))
		{
			$this->setErrorCode(250);
			return false;
		}

		$id=$this->add();
		
		$dWafRuleSite=D('WafRuleSite');
		$dWafRuleSite->editData(array('id'=>$data['rule_site_id']));
		
		return $id;
		
	}
	
	public function editData($data)
	{
		$id=$data['id'];
		$nCount=$this->where(array('id'=>intval($id)))->count();
		if($nCount<1)
		{
			$this->setErrorMsg(250,'当前保存的信息不存在,操作失败!');
			return false;
		}
		
		if(!$this->create($data))
		{
			$this->setErrorCode(250);
			return false;
		}
		
		$this->save();
		
		$dWafRuleSite=D('WafRuleSite');
		$dWafRuleSite->editData(array('id'=>$data['rule_site_id']));
		
		return true;
		
	}
	
	public function delData($data)
	{
		$id=$data['id'];
		
		$dWafRuleLogic=D('WafRuleLogic');	
		$nCount=$dWafRuleLogic->getCountRule($id);
		if($nCount>0)
		{
			$this->setErrorMsg(250,'当前规则还存在关联逻辑,无法删除!');
			return false;
		}
		
		$data=$this->where(array('id'=>intval($id)))->find();
		$this->where(array('id'=>intval($id)))->delete();
		
		$dWafRuleSite=D('WafRuleSite');
		$dWafRuleSite->editData(array('id'=>$data['rule_site_id']));
		
		return true;
	}

	
	public function getPageShowList($option=array())
	{
		
		$order='rule_site_id asc,rule_order asc,update_time desc';
		$page=intval($option['page']);
		$pageSize=intval($option['pageSize']);
		if($pageSize<1)
		{
			$pageSize=50;
		}
		
		$where=array('id'=>array('gt',0));
		
		if($option['rule_site_id']>0)
		{
			$where['rule_site_id']=$option['rule_site_id'];
		}
		
		if($option['kw'])
		{
			$where['rule_name']=array('like','%'.$option['kw'].'%');
		}
		
		$dataReturn=$this->getPageList($where,$order,$page,$pageSize);
		
		
		$showMap=$this->getShowMap();
		

		foreach($dataReturn['dataList'] as &$item)
		{
			$item['action_type_name']=$showMap['actionTypeList'][$item['action_type']];
			$item['action_target_name']=$showMap['actionTargetList'][$item['action_target']];
			$item['rule_site_name']=$showMap['siteRuleList'][$item['rule_site_id']];
		}
		
		
		return $dataReturn;
		
	}
	
}