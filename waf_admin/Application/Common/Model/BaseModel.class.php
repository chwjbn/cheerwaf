<?php
namespace Common\Model;
use Think\Model;

class BaseModel extends Model
{
	//字段检测
	protected $autoCheckFields = false;
	
	//错误码
	private $errorCode=0;
	
	//自定义显示映射
	private $showMap=array();
	
	//构造函数
	public function __construct()
	{
		$this->_initialize();
	}
	
	//初始化
	protected function _initialize() 
	{
		$this->initDbConfig();
		$this->initShowMap();
	}
	
	//初始化db配置
	protected function initDbConfig()
	{
		
	}
	
	//初始化map
	protected function initShowMap()
	{
		
	}
	
	//设置数据库配置
	protected function setDbConfig($dbConfigKey)
	{
		$this->tablePrefix=C(sprintf('%s.DB_PREFIX',$dbConfigKey));
		$this->db(1,$dbConfigKey);
	}
	
	//设置map
	protected function setShowMap($mapKey,$mapKvList)
	{
		$this->showMap[$mapKey]=$mapKvList;
	}
	
	//扩充TP的getError/setError对
	protected function setError($msg)
	{
	   $this->error=$msg;
	}
	
	protected function setErrorCode($error_code)
	{
		$this->errorCode=$error_code;
	}

	protected function setErrorMsg($error_code,$msg)
	{
		$this->errorCode=$error_code;
		$this->error=$msg;
	}
	
	public function getShowMap()
	{
		return $this->showMap;
	}

	public function  getErrorMsg()
	{
		return array('error_code'=>$this->errorCode,'msg'=>$this->error);
	}


	//分页函数
	public function pagination($page,$allCount,$pageSize=30,$eachPage=10)
	{
		$pageCount=intval($allCount/$pageSize);
		if($allCount%$pageSize>0)
		{
	         $pageCount++;
		}
		
		if($page<1)
		{
		    $page=1;
		}
		
		if($page>$pageCount)
		{
		   $page=$pageCount;
		}
		
		$fromPage=intval(($page-1)/$eachPage)*$eachPage+1;	
		$toPage=$fromPage+$eachPage-1;
		
		$prevPage=$page-1;
		$nextPage=$page+1;
		
		if($fromPage<1)
		{
			$fromPage=1;
		}
		if($toPage>$pageCount)
		{
			$toPage=$pageCount;
		}
		
		if($prevPage<1)
		{
			$prevPage=1;
		}
		if($nextPage>$pageCount)
		{
			$nextPage=$pageCount;
		}
		
		$pageData=array(
		   'page'=>$page,
		   'allCount'=>$allCount,
		   'pageCount'=>$pageCount,
		   'pageSize'=>$pageSize,
		   'fromPage'=>$fromPage,
		   'toPage'=>$toPage,
		   'prevPage'=>$prevPage,
		   'nextPage'=>$nextPage
		);
		
		return $pageData;
	}
	
	//根据ID列表查
	public function getKvList($idList=array(),$keyName='id')
	{
	   $dataList=array();
	   if(!$idList)
	   {
	      return $dataList;
	   }
	   
	   $dataListTemp=$this->where(array($keyName=>array('in',$idList)))->select();
	   
	   foreach($dataListTemp as $item)
	   {
	      $dataList[$item[$keyName]]=$item;
	   }
	   
	   return $dataList;
	}
	
	//分页获取
	public function getPageList($where=array(),$order='',$page=1,$pageSize=100)
	{
	  
	   $dataReturn=array();

	   $allCount=$this->where($where)->count();

	   $pageInfo=$this->pagination($page,$allCount,$pageSize,10);
	   $dataReturn['pageInfo']=$pageInfo;

	   if($order)
	   {
		   $dataList=$this->where($where)->page($page,$pageSize)->order($order)->select();
	   }
	   else
	   {
		   $dataList=$this->where($where)->page($page,$pageSize)->select();
	   }

	   $dataReturn['dataList']=$dataList;
	   
	   return $dataReturn;
	}
	
	public function getData($keyVal=0,$keyName='id')
	{
		
		$where=array($keyName=>$keyVal);
		$data=$this->where($where)->find();
		return $data;
		
	}

}