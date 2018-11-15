<?php
$returnConfig = array();

$returnConfig['URL_MAP_RULES'] = array(
   'waf_auth/check_code_page'=>'User/check_code_page',
   'waf_auth/check_code_task'=>'User/check_code_task',
   'waf_auth/get_verify'=>'User/get_verify',
);

return $returnConfig;