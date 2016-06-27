<?php
 /*author:陈清 
   desc:控制器基类
   date:2016-06-20
 */
class BaseController{
	/**构造函数*/
	public $request;
	public $response;

	public function __construct() {

	}

	/**返回json数据*/
	public function response($obj=null) {	
		if (is_null($obj)) {
			die( json_encode($this->request)); 
		}else{
			die( json_encode($obj)); 
		}
	}
	/**stdClass to Object Model*/
	public function objectToObject($instance, $className) {
		return unserialize(sprintf('O:%d:"%s"%s', strlen($className), $className, strstr(strstr(serialize($instance), '"'), ':')));
	}


	/**
	*初始化控制器
	*1.返回类型
	*2.token检查
	*/
	public function _init(){		
		$this->_start();

	}

	protected function _start(){
		$this->_checkRequest();				//检查请求是否合法
		$this->_setHeader();
		header('Access-Control-Allow-Origin: *');
		$this->request = _request();
		
		$file=__API__. __MODULE__.'/Functions.php';	

		if (file_exists($file)) {		//调用模块内的自定义函数	
        	include $file;
    	}
    	// require_once __API__. __MODULE__.'/Dao.php';		
	}


	protected function _setHeader(){
		switch (Config('return_type')) {
			case 'xml':
				header("Content-Type:application/xml;charset=utf-8");
				break;
			case 'html':
				header("Content-Type:text/html;charset=utf-8");
				break;
			case 'text':
				header("Content-Type:text/plain;charset=utf-8");
				break;										
			default:
				header("Content-Type:text/json;charset=utf-8");
				break;
		}		
	}

	protected function _checkRequest(){
		
	}


	protected function _checkToken(){
		$request=$this->request;
		if (!empty($request)) {
			ksort($request);
		}
		




		return true;

		// myerror(StatusCode::msgTokenFail, "token校验失败");
	}



	protected function _guid(){
	    $charid = strtoupper(md5(uniqid(mt_rand(), true)));
	    $hyphen = chr(45); // "-"
	    $uuid = chr(123)// "{"
	            . substr($charid, 0, 8) . $hyphen
	            . substr($charid, 8, 4) . $hyphen
	            . substr($charid, 12, 4) . $hyphen
	            . substr($charid, 16, 4) . $hyphen
	            . substr($charid, 20, 12)
	            . chr(125); // "}"
	    return $uuid;
	}

	protected function _newToken(){

	}




}
