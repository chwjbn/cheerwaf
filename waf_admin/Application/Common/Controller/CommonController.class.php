<?php
namespace Common\Controller;

use Think\Controller;

class CommonController extends Controller
{
	public function __construct()
    {
        parent::__construct();
    }
	
	protected function ajaxCallMsg($error_code,$msg,$data=null)
	{
	   $dataReturn=array('error_code'=>$error_code,'msg'=>$msg);
	   if(!is_null($data))
	   {
		   $dataReturn['data']=$data;
	   }
	   
	   $this->ajaxReturn($dataReturn);
	}
}