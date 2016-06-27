<?php
require __CORE__. 'BaseFunction.php';	//载入基础函数
require __CORE__. 'BaseClass.php';	//载入基础类

spl_autoload_register(function($name){				//类自动加载
    $file = __BASE_PATH__. str_replace('\\', '/', $name);
    $file .= '.php';
    if (file_exists($file)) {
        include_once $file;
    }
});			

require __LIB__. 'CommonClass.php';
require __LIB__. 'CommonFunction.php';
require __API__. 'route.php';		//读取路由配置

require_once __CORE__. 'BaseController.php';
require_once __DRIVER__.'MysqlDriver.php';
require_once __CORE__. 'BaseModel.php';
require_once __CORE__. 'BaseDao.php';




$requestParam=!empty(file_get_contents('php://input', 'r')) ? file_get_contents('php://input', 'r') :$_REQUEST;		//请求的参数
$url=explode('?', $_SERVER['REQUEST_URI'])[0];		//请求的url


$t=new Route($url, $requestParam);			//实例化路由

