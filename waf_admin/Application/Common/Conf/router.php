<?php
$returnConfig = array();

$returnConfig['APP_SUB_DOMAIN_DEPLOY'] = 1;  //开启子域名部署
$returnConfig['URL_ROUTER_ON']         = true;  //开启路由
$returnConfig['MULTI_MODULE']          = true;
$returnConfig['URL_DENY_SUFFIX']='asp|jsp|conf';
$returnConfig['VAR_ADDON']='wapi';

//子域名和模块之间的映射
$returnConfig['APP_SUB_DOMAIN_RULES'] = array(
	get_current_domain() => 'Home',
);

$returnConfig['COOKIE_DOMAIN']=get_current_cookie_domain();
$returnConfig['COOKIE_PATH']='/';

return $returnConfig;