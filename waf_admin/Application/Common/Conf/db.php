<?php
	
	$returnConfig = array();
	
	$offlineConfig=array(

		//默认库
		'DB_MYSQL_DEFAULT'=>array(
			'DB_TYPE'               =>  'mysql',     // 数据库类型
			'DB_HOST'               =>  '127.0.0.1', // 服务器地址
			'DB_NAME'               =>  'db_waf',          // 数据库名
			'DB_USER'               =>  'root',      // 用户名
			'DB_PWD'                =>  'chwjbn',          // 密码
			'DB_PORT'               =>  '3306',        // 端口
			'DB_PREFIX'             =>  't_',    // 数据库表前缀
		),
	
	
		//默认redis
		'REDIS_DEFAULT'=>array(
			'REDIS_HOST'=>'127.0.0.1',
			'REDIS_PORT'=>6379,
			'REDIS_DB'=>0
		),

	);

	$onlineConfig=array(

		//默认库
		'DB_MYSQL_DEFAULT'=>array(
			'DB_TYPE'               =>  'mysql',     // 数据库类型
			'DB_HOST'               =>  '172.25.10.102', // 服务器地址
			'DB_NAME'               =>  'db_waf',          // 数据库名
			'DB_USER'               =>  'root',      // 用户名
			'DB_PWD'                =>  'YcjMysql1234!@#$',          // 密码
			'DB_PORT'               =>  '3306',        // 端口
			'DB_PREFIX'             =>  't_',    // 数据库表前缀
		),
	
	
		//默认redis
		'REDIS_DEFAULT'=>array(
			'REDIS_HOST'=>'172.25.10.98',
			'REDIS_PORT'=>6379,
			'REDIS_DB'=>0
		),

	);
	
	
	
	if(in_local_mode())
	{
		$returnConfig=$offlineConfig;
	}
	else
	{
		$returnConfig=$onlineConfig;
	}

	return $returnConfig;