<?php
return array(
	'DEFAULT_MODULE' => 'Home',
    'MODULE_ALLOW_LIST' => array('Home'),
	'SHOW_ERROR_MSG'=>true,
	'LOG_LEVEL'=>'ERR',
	'LOG_RECORD'=> true,
    'LOG_EXCEPTION_RECORD' => true,
	'LOAD_EXT_CONFIG'=> 'db,router',  //router一定要放到最后
);