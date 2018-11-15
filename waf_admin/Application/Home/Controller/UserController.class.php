<?php
namespace Home\Controller;
class UserController extends BaseController 
{
    public function check_code_page()
	{
		$this->display('check_code_page');
	}
	
	
	public function check_code_task()
	{
		if($this->checkVerify())
		{		
			redirect('/');
			exit();		
		}
		
		$this->display('check_code_task');
	}
	
	public function get_verify()
	{
		$uuid=get_user_uuid();
		
		if(!check_user_uuid())
		{
			cookie('ycj_uuid',$uuid,86400 * 30);
		}
		
		
		$seKey='cheerwaf_';
		$dataKey=sprintf('%s%s',$seKey,$uuid);
		
		$config=array('seKey'=>$seKey,'imageH'=>50,'imageW'=>200,'useNoise'=>false,'codeSet'=>'1234567890','useCurve'=>false,'length'=>4);
		$dVerify=new \Think\Verify($config);
		
		$seCodeData=$dVerify->entry($id);
		
		$code=$seCodeData;
		
		if($code)
		{
			$redisHandle=\Com\Chw\RedisLib::getInstance('REDIS_DEFAULT');
			$redisHandle->select(2);
			$redisHandle->setex($dataKey,300,$code);
		}	
	}
	
	private function checkVerify()
	{
	    $bRet=false;
		
		$uuid=get_user_uuid();	
		if(!check_user_uuid())
		{
			return $bRet;
		}
		
		$code=I('post.code_val','');
		if(!$code)
		{
			return $bRet;
		}
		
		$seKey='cheerwaf_';
		$dataKey=sprintf('%s%s',$seKey,$uuid);

		$redisHandle=\Com\Chw\RedisLib::getInstance('REDIS_DEFAULT');
		$redisHandle->select(2);
		$dataCode=$redisHandle->get($dataKey);
		
		if(!$dataCode)
		{
			return $bRet;
		}
		
		$redisHandle->del($dataKey);
		
		$dataCode=strtolower($dataCode);
		$code=strtolower($code);
		
		if($dataCode==$code)
		{
			$bRet=true;
		}
		
		if($bRet)
		{
			$whiteKey=sprintf('white_uuid_%s',$uuid);
			
			$redisHandle->select(1);
			$redisHandle->setex($whiteKey,3600*24*7,100);
		}
		
		return $bRet;
	}
}