<?php
namespace Addon\ajax\Controller;
use Common\Controller\CommonController;

class WafController extends AuthController
{
	public function _initialize()
	{
		parent::_initialize();
	}
	
	
	public function site_add_pop()
	{
		$data=array(
		    'rule_site_name'=>I('post.rule_site_name','','trim'),
			'http_host'=>I('post.http_host','','trim'),
			'http_host_type'=>I('post.http_host_type','','trim'),
		);
		
		
		$dWafRuleSite=D('WafRuleSite');
		
		$nId=$dWafRuleSite->addData($data);
		
		if(!$nId)
		{
			$msgData=$dWafRuleSite->getErrorMsg();
			$this->ajaxCallMsg($msgData['error_code'],$msgData['msg']);
		}
		
		$this->ajaxCallMsg(0,'操作成功!');
	}
	
	public function site_edit_pop()
	{
		$data=array(
			'id'=>I('post.id',0,'intval'),
		    'rule_site_name'=>I('post.rule_site_name','','trim'),
			'http_host'=>I('post.http_host','','trim'),
			'http_host_type'=>I('post.http_host_type','','trim'),
			'state'=>I('post.state',0,'intval'),
		);
		
		
		$dWafRuleSite=D('WafRuleSite');
		
		$nId=$dWafRuleSite->editData($data);
		
		if(!$nId)
		{
			$msgData=$dWafRuleSite->getErrorMsg();
			$this->ajaxCallMsg($msgData['error_code'],$msgData['msg']);
		}
		
		$this->ajaxCallMsg(0,'操作成功!');
	}
	
	public function site_pub()
	{
		$data=array(
			'id'=>I('get.id',0,'intval'),
		);
		
		
		$dWafRuleSite=D('WafRuleSite');
		
		$nId=$dWafRuleSite->pubData($data);
		
		if(!$nId)
		{
			$msgData=$dWafRuleSite->getErrorMsg();
			$this->ajaxCallMsg($msgData['error_code'],$msgData['msg']);
		}
		
		$this->ajaxCallMsg(0,'操作成功!');
	}
	
	public function site_del()
	{
		$data=array(
			'id'=>I('get.id',0,'intval'),
		);
		
		
		$dWafRuleSite=D('WafRuleSite');
		
		$nId=$dWafRuleSite->delData($data);
		
		if(!$nId)
		{
			$msgData=$dWafRuleSite->getErrorMsg();
			$this->ajaxCallMsg($msgData['error_code'],$msgData['msg']);
		}
		
		$this->ajaxCallMsg(0,'操作成功!');
	}
	
	public function rule_add_pop()
	{
		$data=array(
		    'rule_node_name'=>I('post.rule_node_name','','trim'),
			'rule_site_id'=>I('post.rule_site_id',0,'intval'),
			'action_type'=>I('post.action_type','','trim'),
			'action_target'=>I('post.action_target','','trim'),
			'action_value'=>I('post.action_value','','trim'),
			'rule_order'=>I('post.rule_order',0,'intval'),
		);
		
		
		$dWafRuleNode=D('WafRuleNode');
		
		$nId=$dWafRuleNode->addData($data);
		
		if(!$nId)
		{
			$msgData=$dWafRuleNode->getErrorMsg();
			$this->ajaxCallMsg($msgData['error_code'],$msgData['msg']);
		}
		
		$this->ajaxCallMsg(0,'操作成功!');
	}
	
	public function rule_edit_pop()
	{
		$data=array(
			'id'=>I('post.id',0,'intval'),
		    'rule_node_name'=>I('post.rule_node_name','','trim'),
			'rule_site_id'=>I('post.rule_site_id',0,'intval'),
			'action_type'=>I('post.action_type','','trim'),
			'action_target'=>I('post.action_target','','trim'),
			'action_value'=>I('post.action_value','','trim'),
			'rule_order'=>I('post.rule_order',0,'intval'),
		);
		
		
		$dWafRuleNode=D('WafRuleNode');
		
		$nId=$dWafRuleNode->editData($data);
		
		if(!$nId)
		{
			$msgData=$dWafRuleNode->getErrorMsg();
			$this->ajaxCallMsg($msgData['error_code'],$msgData['msg']);
		}
		
		$this->ajaxCallMsg(0,'操作成功!');
	}
	
	public function rule_del()
	{
		$data=array(
			'id'=>I('get.id',0,'intval'),
		);
		
		
		$dWafRuleNode=D('WafRuleNode');
		
		$nId=$dWafRuleNode->delData($data);
		
		if(!$nId)
		{
			$msgData=$dWafRuleNode->getErrorMsg();
			$this->ajaxCallMsg($msgData['error_code'],$msgData['msg']);
		}
		
		$this->ajaxCallMsg(0,'操作成功!');
	}
	
	public function rule_ip_pop()
	{
		$data=array(
			'ip'=>I('post.ip',''),
			'action_type'=>I('post.action_type',''),
		);
		
		$dWafRuleIp=D('WafRuleIp');
		
		$nId=$dWafRuleIp->actionIp($data);
		
		if(!$nId)
		{
			$msgData=$dWafRuleIp->getErrorMsg();
			$this->ajaxCallMsg($msgData['error_code'],$msgData['msg']);
		}
		
		$this->ajaxCallMsg(0,'操作成功!');
		
	}
	
	public function logic_add_pop()
	{
		$data=array(
		    'rule_logic_name'=>I('post.rule_logic_name','','trim'),
			'rule_logic_type'=>I('post.rule_logic_type','','trim'),
			'left_logic_id'=>I('post.left_logic_id',0,'intval'),
			'left_logic_type'=>I('post.left_logic_type','','trim'),
			'current_logic_key'=>I('post.current_logic_key','','trim'),
			'current_logic_type'=>I('post.current_logic_type','','trim'),
			'current_logic_value'=>I('post.current_logic_value','','trim'),
			'rule_node_id'=>I('post.rule_node_id',0,'intval'),
		);
		
		
		$dWafRuleLogic=D('WafRuleLogic');
		
		$nId=$dWafRuleLogic->addData($data);
		
		if(!$nId)
		{
			$msgData=$dWafRuleLogic->getErrorMsg();
			$this->ajaxCallMsg($msgData['error_code'],$msgData['msg']);
		}
		
		$this->ajaxCallMsg(0,'操作成功!');
	}
	
	public function logic_edit_pop()
	{
		$data=array(
		    'rule_logic_name'=>I('post.rule_logic_name','','trim'),
			'rule_logic_type'=>I('post.rule_logic_type','','trim'),
			'left_logic_id'=>I('post.left_logic_id',0,'intval'),
			'left_logic_type'=>I('post.left_logic_type','','trim'),
			'current_logic_key'=>I('post.current_logic_key','','trim'),
			'current_logic_type'=>I('post.current_logic_type','','trim'),
			'current_logic_value'=>I('post.current_logic_value','','trim'),
			'id'=>I('post.id',0,'intval'),
		);
		
		
		$dWafRuleLogic=D('WafRuleLogic');
		
		$nId=$dWafRuleLogic->editData($data);
		
		if(!$nId)
		{
			$msgData=$dWafRuleLogic->getErrorMsg();
			$this->ajaxCallMsg($msgData['error_code'],$msgData['msg']);
		}
		
		$this->ajaxCallMsg(0,'操作成功!');
	}
	
	public function logic_del()
	{
		$data=array(
			'id'=>I('get.id',0,'intval'),
		);
		
		
		$dWafRuleLogic=D('WafRuleLogic');
		
		$nId=$dWafRuleLogic->delData($data);
		
		if(!$nId)
		{
			$msgData=$dWafRuleLogic->getErrorMsg();
			$this->ajaxCallMsg($msgData['error_code'],$msgData['msg']);
		}
		
		$this->ajaxCallMsg(0,'操作成功!');
	}

}