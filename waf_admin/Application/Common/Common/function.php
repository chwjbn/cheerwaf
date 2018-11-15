<?php

function SVC($svcName)
{
    return D($svcName, 'Service');
}

//获取完整时间
function get_fulltime($time = false)
{
    if(!$time)
    {
        $now  = time();
    }
    else
    {
        $now = $time;
    }

    $time = is_numeric($now) ? $now : strtotime($now);

    return strftime("%Y-%m-%d %H:%M:%S", $time);
}

//获取含有代理的IP地址
function get_client_addr()
{
    return get_client_ip(0, true);
}

//获取当前域名
function get_current_domain()
{
    $srvName = $_SERVER['HTTP_HOST'];
    $srvPort = $_SERVER['SERVER_PORT'];

    if ($srvPort == 80)
    {
        $srvPort = '';
    }
    else
    {
        $srvPort = sprintf(':%s', $srvPort);
    }

    $resultDomain = sprintf('%s%s',$srvName, $srvPort);

    return $resultDomain;
}

//获取当前cookie域
function get_current_cookie_domain()
{
	$srvName = $_SERVER['HTTP_HOST'];
    $srvPort = $_SERVER['SERVER_PORT'];

    $domainParts = explode('.', $srvName);
    if (count($domainParts) > 2)
    {
        unset($domainParts[0]);
    }

    if ($srvPort == 80)
    {
        $srvPort = '';
    }
    else
    {
        $srvPort = sprintf(':%s', $srvPort);
    }

    $resultDomain = sprintf('.%s%s', join('.', $domainParts), $srvPort);

    return $resultDomain;
}

//获取二维数据某项值列表
function get_array_item_list($arrayList,$keyname)
{
	$list=array();
	
	foreach($arrayList as $item)
	{
	  $list[]=$item[$keyname];
	}
	
	return $list;
}

function check_user_uuid()
{
    $ycj_uuid = cookie('ycj_uuid');

    $uuidCheckRule = '/^[0-9a-f]{32}$/i';
    if(!preg_match($uuidCheckRule, $ycj_uuid))
    {
        return false;
    }
    return true;
}

function get_user_uuid()
{
    $ycj_uuid = cookie('ycj_uuid');

    $uuidCheckRule = '/^[0-9a-f]{32}$/i';
    if(!preg_match($uuidCheckRule, $ycj_uuid))
    {
        $ycj_uuid = sprintf('%s_%s_%s_%s', get_client_addr(), time(), uniqid(), __SELF__);
        $ycj_uuid = md5($ycj_uuid);
    }
    return $ycj_uuid;
}