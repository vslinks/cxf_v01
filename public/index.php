<?php
//class A{
//    var $a;
//    var $b;
//    
//    static function  call(){
//        echo '414141';
//    }
//}
//
//$test=new A();
//$test->a="xiaolin";
//$rr= json_encode($test); //serialize($test);
//die($rr);
////die("标准json:".$rr."searial:".serialize($test));
//
//$result= json_decode($rr);
////var_dump($result);
//echo $result::call();
//exit();




/*
*author:陈清, 林澜叶
*入口文件，这里可以定义一些系统常量，
*/

date_default_timezone_set('PRC');
error_reporting(E_ALL);
ini_set('max_execution_time', '600');


define('DS',DIRECTORY_SEPARATOR);//>>定义斜杠
define('__BASE_PATH__',str_replace('\\','/',realpath(dirname(__FILE__).'/'))."/../");
define('__PUBLIC__', __BASE_PATH__. 'pubilc/');		//定义可访问目录
define('__CONFIG__', __BASE_PATH__. 'config/');		//定义配置目录
define('__LOG__', __BASE_PATH__. 'logs/');			//定义日志目录
define('__API__', __BASE_PATH__. 'api/');				//定义api目录，也即开发目录
define('__CORE__', __BASE_PATH__. 'core/');			//定义核心目录
define('__LIB__', __BASE_PATH__. 'lib/');			//定义自定义库目录
define('__DRIVER__', __CORE__. 'driver/');			//定义驱动目录
define('__EXT__',__LIB__ . 'ext' . DS);             //定义扩展类文件目录



//**如有需要，在此定义系统常量, 其他常量请到配置文件定义，**//

//define(name, value)

//*************************//

require  __CORE__. 'Start.php';


