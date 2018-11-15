<?php
// 应用入口文件

// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');


define('ROOT_DIR', dirname(__FILE__) . '/');

define('APP_DEBUG',True);

function dev_show($data)
{
    print_r($data);
    exit();
}

//判断是否是本地开发模式
function in_local_mode()
{
    $debugFile = ROOT_DIR . 'debug.lock';
	
    if (file_exists($debugFile))
    {
        return true;
    }
    return false;
}



define('APP_PATH', ROOT_DIR . '../Application/');
define('RUNTIME_PATH', ROOT_DIR . '../Runtime/');
define('TLIB_PATH', ROOT_DIR . '../TLib');

require TLIB_PATH . '/ThinkPHP.php';