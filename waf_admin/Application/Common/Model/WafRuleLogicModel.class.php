<?php
namespace Common\Model;

class WafRuleLogicModel extends DataBaseModel
{
    protected $tableName = 'waf_rule_logic';
	
	protected $_validate=array( 
	   array('rule_node_id',array(0,999999999),'请选择正确的规则!',0,'between',3),
	   array('rule_logic_name','1,100','请输入正确的逻辑名称!',0,'length',3),
	   array('left_logic_id',array(0,999999999),'请选择正确的左侧逻辑!',0,'between',3),
	   array('left_logic_type','checkLeftLogicType','请选择正确的左侧逻辑类别!',0,'callback',3),
	   array('current_logic_key','checkCurrentLogicKey','请选择正确的逻辑匹配字段!',0,'callback',3),
	   array('current_logic_type','checkCurrentLogicType','请选择正确的逻辑类别!',0,'callback',3),
	   array('current_logic_value','checkCurrentLogicValue','请输入正确的逻辑匹配值!',0,'callback',3),
	);
	
	protected $_auto = array(
	   array('create_time','get_fulltime',1,'function'),
	   array('update_time','get_fulltime',3,'function'),
	   array('update_ip','get_client_addr',3,'function'),
	);
	
	protected function initShowMap()
	{
		
		$ruleLogicTypeList=array(
			1=>'中间逻辑',
			2=>'最终逻辑'
		);
		
		$leftLogicTypeList=array(
		  'and'=>'与',
		  'or'=>'或',
		  'andnot'=>'与非',
		  'ornot'=>'或非',
		);
		
		//eq,lt,gt,lte,gte,neq,regex
		$currentLogicTypeList=array(
			'eq'=>'等于',
			'lt'=>'小于',
			'gt'=>'大于',
			'lte'=>'小于等于',
			'gte'=>'大于等于',
			'neq'=>'不等于',
			'regex'=>'正则匹配'
		);
		
		$currentLogickeyList=array(
			's_http_ip'=>'http_ip',
			's_http_header_method'=>'http_header_method',
			's_http_header_host'=>'http_header_host',
			's_http_header_useragent'=>'http_header_useragent',
			's_http_header_url'=>'http_header_url',
			's_http_header_referer'=>'http_header_referer',
			's_http_header_cookie'=>'http_header_cookie',
			's_http_header_x_requested_with'=>'http_header_x_requested_with',
			's_cookie_wafsid'=>'cookie_wafsid',
			's_cookie_uuid'=>'cookie_uuid',
			's_cookie_uid'=>'cookie_uid',
			's_cookie_token'=>'cookie_token',
			's_time'=>'time',
			'd_score_session_white'=>'score_session_white',
			'd_score_session_black'=>'score_session_black',
			'd_score_uuid_white'=>'score_uuid_white',
			'd_score_uuid_black'=>'score_uuid_black',
			'd_score_uid_white'=>'score_uid_white',
			'd_score_uid_black'=>'score_uid_black',
			'd_score_ip_white'=>'score_ip_white',
			'd_score_ip_black'=>'score_ip_black',
			'd_count_ip_min'=>'count_ip_min',
			'd_count_uuid_min'=>'count_uuid_min',
		);
		
		$this->setShowMap('ruleLogicTypeList',$ruleLogicTypeList);
		
		$this->setShowMap('leftLogicTypeList',$leftLogicTypeList);
		$this->setShowMap('currentLogicTypeList',$currentLogicTypeList);	
		
		$this->setShowMap('currentLogickeyList',$currentLogickeyList);
		
		$dWafRuleSite=D('WafRuleSite');	
		$siteList=$dWafRuleSite->getAllDataKvList();
		$this->setShowMap('siteRuleList',$siteList);
	}
	
	protected function checkLeftLogicType($data)
	{
		
		$showMap=$this->getShowMap();
		$dataMap=$showMap['leftLogicTypeList'];
		
		if(array_key_exists($data,$dataMap))
		{
			return true;
		}
		
		
		return false;
	}
	
	protected function checkCurrentLogicType($data)
	{
		
		$showMap=$this->getShowMap();
		$dataMap=$showMap['currentLogicTypeList'];
		
		if(array_key_exists($data,$dataMap))
		{
			return true;
		}
		
		return false;
	}
	
	protected function checkCurrentLogicKey($data)
	{
		
		$showMap=$this->getShowMap();
		$dataMap=$showMap['currentLogickeyList'];
		
		if(array_key_exists($data,$dataMap))
		{
			return true;
		}
		
		return false;
	}
	
	protected function checkCurrentLogicValue($data)
	{
		return true;
	}
	
	public function getCountRule($rule_node_id)
	{
		$nCount=$this->where(array('rule_node_id'=>intval($rule_node_id)))->count();
		return $nCount;
	}
	
	public function getLeftLogicIdKvList($rule_node_id)
	{
		$dataList=array();
		$tempDataList=$this->where(array('rule_node_id'=>intval($rule_node_id)))->select();
		
		foreach($tempDataList as $item)
		{
			$dataList[$item['id']]=$item['rule_logic_name'];
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
		
		if($data['current_logic_key']=='s_time')
		{
			$valRule='/^[0-9]{6}$/i';
			if(!preg_match($valRule,$data['current_logic_value']))
			{
				$this->setErrorMsg(251,'time的配置逻辑值应该是一个6位数字!');
				return false;
			}
		}

		$id=$this->add();
		
		
		$dWafRuleNode=D('WafRuleNode');
		$dWafRuleNode->editData(array('id'=>$data['rule_node_id']));
		
		
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
		
		if($data['current_logic_key']=='s_time')
		{
			$valRule='/^[0-9]{6}$/i';
			if(!preg_match($valRule,$data['current_logic_value']))
			{
				$this->setErrorMsg(251,'time的配置逻辑值应该是一个6位数字!');
				return false;
			}
		}
		
		if($data['id']==$data['left_logic_id'])
		{
			$this->setErrorMsg(252,'左侧逻辑不能为当前逻辑,操作失败!');
			return false;
		}
		
			
		$this->save();
		
		$dWafRuleNode=D('WafRuleNode');
		$dWafRuleNode->editData(array('id'=>$data['rule_node_id']));
		
		return true;
		
	}
	
	public function delData($data)
	{
		$id=$data['id'];
		
		$leftRefCount=$this->where(array('left_logic_id'=>$id))->count();
		
		if($leftRefCount>0)
		{
			$this->setErrorMsg(250,'当前逻辑被其他逻辑引用,无法删除!');
			return false;
		}
		
		
		$data=$this->where(array('id'=>intval($id)))->find();
		
		$this->where(array('id'=>intval($id)))->delete();
		
		$dWafRuleNode=D('WafRuleNode');
		$dWafRuleNode->editData(array('id'=>$data['rule_node_id']));
		
		return true;
	}

	
	public function getShowList($rule_node_id)
	{
		
		$order='rule_logic_type asc,left_logic_id asc';
		
		
		$where=array('rule_node_id'=>$rule_node_id);
		
		
		$dataList=$this->where($where)->order($order)->select();
		
		
		$leftLogicMap=$this->getLeftLogicIdKvList($rule_node_id);
		
		$showMap=$this->getShowMap();
		

		foreach($dataList as &$item)
		{
			$item['left_logic_type_name']=$showMap['leftLogicTypeList'][$item['left_logic_type']];
			$item['left_logic_id_name']=$leftLogicMap[$item['left_logic_id']];
			
			if(!$item['left_logic_id_name'])
			{
				$item['left_logic_id_name']='TRUE';
			}
			
			$item['current_logic_key_name']=$showMap['currentLogickeyList'][$item['current_logic_key']];	
			$item['current_logic_type_name']=$showMap['currentLogicTypeList'][$item['current_logic_type']];
			$item['rule_logic_type_name']=$showMap['ruleLogicTypeList'][$item['rule_logic_type']];
		}
			
		return $dataList;
		
	}
	
}