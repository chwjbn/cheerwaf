<?php
namespace Home\Controller;

class WafController extends AuthController 
{
	public function site_list()
	{
		
		$option=array(
			'page'=>I('post.page',1,'intval'),
			'pageSize'=>I('post.pageSize',50,'intval'),
			'state'=>I('post.state',-1,'intval'),
			'kw'=>I('post.kw')
		);
		
		$this->assign('option',$option);
		
		
		$dWafRuleSite=D('WafRuleSite');
		
		$pageOption=$dWafRuleSite->getShowMap();
		$this->assign('pageOption',$pageOption);

		$pageData=$dWafRuleSite->getPageShowList($option);	
		$this->assign('pageData',$pageData);
		
		$this->display('site_list');
	}
	
	public function site_add_pop()
	{
		$dWafRuleSite=D('WafRuleSite');
		
		$pageOption=$dWafRuleSite->getShowMap();
		$this->assign('pageOption',$pageOption);
		
		$this->display('site_add_pop');
	}
	
	public function site_edit_pop()
	{
		$option=array(
			'id'=>I('get.id',0,'intval'),
		);
		
		$dWafRuleSite=D('WafRuleSite');
		
		$pageOption=$dWafRuleSite->getShowMap();
		$this->assign('pageOption',$pageOption);
		
		$data=$dWafRuleSite->getData($option['id']);
		$this->assign('data',$data);
		
		$this->display('site_edit_pop');
	}
	
	public function rule_list()
	{
		$option=array(
			'page'=>I('post.page',1,'intval'),
			'pageSize'=>I('post.pageSize',50,'intval'),
			'rule_site_id'=>I('post.rule_site_id',0,'intval'),
			'kw'=>I('post.kw')
		);
		
		$this->assign('option',$option);
		
		
		$dWafRuleNode=D('WafRuleNode');
		
		$pageOption=$dWafRuleNode->getShowMap();
		$this->assign('pageOption',$pageOption);

		$pageData=$dWafRuleNode->getPageShowList($option);	
		$this->assign('pageData',$pageData);
		
		$this->display('rule_list');
	}
	
	public function rule_add_pop()
	{
		$dWafRuleNode=D('WafRuleNode');
		
		$pageOption=$dWafRuleNode->getShowMap();
		$this->assign('pageOption',$pageOption);
		
		$this->display('rule_add_pop');
	}
	
	public function rule_edit_pop()
	{
		$option=array(
			'id'=>I('get.id',0,'intval'),
		);
		
		$dWafRuleNode=D('WafRuleNode');
		
		$pageOption=$dWafRuleNode->getShowMap();
		$this->assign('pageOption',$pageOption);
		
		$data=$dWafRuleNode->getData($option['id']);
		$this->assign('data',$data);
		
		
		$dWafRuleLogic=D('WafRuleLogic');
		$logicDataList=$dWafRuleLogic->getShowList($option['id']);
		
		
		$this->assign('logicDataList',$logicDataList);
		
		$this->display('rule_edit_pop');
	}
	
	
	public function rule_ip_pop()
	{
		$this->display('rule_ip_pop');
	}
	
	public function logic_add_pop()
	{
		
		$dWafRuleLogic=D('WafRuleLogic');
		
		$pageOption=$dWafRuleLogic->getShowMap();
		
		$pageOption['rule_node_id']=I('get.rule_node_id',0,'intval');
		
		$leftLogicIdList=$dWafRuleLogic->getLeftLogicIdKvList($pageOption['rule_node_id']);
		$pageOption['leftLogicIdList']=$leftLogicIdList;
		
		$this->assign('pageOption',$pageOption);
		
		$this->display('logic_add_pop');
	}
	
	public function logic_edit_pop()
	{
		$option=array(
			'id'=>I('get.id',0,'intval'),
		);
		
		$dWafRuleLogic=D('WafRuleLogic');
		
		$data=$dWafRuleLogic->getData($option['id']);
		$this->assign('data',$data);
		
		$pageOption=$dWafRuleLogic->getShowMap();
		
		$leftLogicIdList=$dWafRuleLogic->getLeftLogicIdKvList($data['rule_node_id']);
		
		unset($leftLogicIdList[$data['id']]);
		
		$pageOption['leftLogicIdList']=$leftLogicIdList;
		
		$this->assign('pageOption',$pageOption);
		
		
	
		
		$this->display('logic_edit_pop');
	}
}