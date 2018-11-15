<?php
namespace Common\Model;

class WafRuleSiteModel extends DataBaseModel
{
    protected $tableName = 'waf_rule_site';
	
	protected $_validate=array( 
	   array('rule_site_name','1,100','请输入正确的站点名称!',0,'length',3),
	   array('http_host','1,250','请输入正确的站点规则!',0,'length',3),
	   array('http_host_type',array('string','regex'),'请选择正确的规则类别!',0,'in',3),
	);
	
	protected $_auto = array(
	   array('state',1,1,'string'),
	   array('create_time','get_fulltime',1,'function'),
	   array('update_time','get_fulltime',3,'function'),
	   array('update_ip','get_client_addr',3,'function'),
	);
	
	protected function initShowMap()
	{
		$this->setShowMap('httpHostTypeList',array('string'=>'字符匹配','regex'=>'正则匹配'));
		$this->setShowMap('stateList',array(0=>'禁用',1=>'启用'));	
	}
	
	public function getAllDataKvList()
	{
		$data=array();
		
		$tempData=$this->select();
		
		foreach($tempData as $item)
		{
			$data[$item['id']]=$item['rule_site_name'];
		}
		
		return $data;

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
			$this->setErrorMsg(250,'当前保存的信息不存在,操作失败!');
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
		
		$dWafRuleNode=D('WafRuleNode');
		$nCount=$dWafRuleNode->getCountSite($id);
		if($nCount>0)
		{
			$this->setErrorMsg(250,'当前站点还存在规则集合,无法删除!');
			return false;
		}
		
		$this->where(array('id'=>intval($id)))->delete();
		
		return true;
	}
	
	public function pubData($data)
	{
		$id=$data['id'];
		
		$siteData=$this->field('id,http_host,http_host_type,update_time')->where(array('id'=>$id))->find();
		
		if(!$siteData)
		{
			$this->setErrorMsg(251,'站点信息已经被删除,操作失败!');
			return false;
		}
		
		$updateTime=$siteData['update_time'];
		unset($siteData['update_time']);
		
		$dWafRuleNode=D('WafRuleNode');
		$ruleNodeList=$dWafRuleNode->field('id,rule_order,action_type,action_target,action_value')->where(array('rule_site_id'=>$id))->order('rule_order asc')->select();
		
		if(!$ruleNodeList)
		{
			$this->setErrorMsg(252,'当前站点下没有任何规则,操作失败!');
			return false;
		}
		
		
		$nodeIdList=get_array_item_list($ruleNodeList,'id');
		
		$dWafRuleLogic=D('WafRuleLogic');
		$ruleLogicList=$dWafRuleLogic->field('id,rule_node_id,rule_logic_type,left_logic_id,left_logic_type,current_logic_key,current_logic_type,current_logic_value')->where(array('rule_node_id'=>array('in',$nodeIdList)))->select();
		
		if(!$ruleLogicList)
		{
			$this->setErrorMsg(253,'当前站点下所有规则没有任何触发条件,操作失败!');
			return false;
		}
		
		//所有站点信息
		$siteDataList=$this->field('id,http_host,http_host_type')->where(array('state'=>1))->select();
		
		$redisHandle=\Com\Chw\RedisLib::getInstance('REDIS_DEFAULT');
		
		$redisHandle->set('waf_site',json_encode($siteDataList));
		$redisHandle->set(sprintf('waf_site_node_%s',$siteData['id']),json_encode($ruleNodeList));
		$redisHandle->set(sprintf('waf_site_logic_%s',$siteData['id']),json_encode($ruleLogicList));
		
        $this->where(array('id'=>$id))->save(array('update_cache_time'=>$updateTime));
		
		return true;
	}

	
	public function getPageShowList($option=array())
	{
		
		$order='update_time desc';
		$page=intval($option['page']);
		$pageSize=intval($option['pageSize']);
		if($pageSize<1)
		{
			$pageSize=50;
		}
		
		$where=array('id'=>array('gt',0));
		
		if($option['state']>-1)
		{
			$where['state']=$option['state'];
		}
		
		if($option['kw'])
		{
			$where['site_rule_name']=array('like','%'.$option['kw'].'%');
		}
		
		$dataReturn=$this->getPageList($where,$order,$page,$pageSize);
		
		
		$showMap=$this->getShowMap();
		

		foreach($dataReturn['dataList'] as &$item)
		{
			$item['http_host_type_name']=$showMap['httpHostTypeList'][$item['http_host_type']];
			$item['state_name']=$showMap['stateList'][$item['state']];
		}
		
		
		return $dataReturn;
		
	}
	
}