<?php


/* * 返回状态码枚举类型* */
class StatusCode {
    const msgSucessStatus = 0;  //响应状态码成功
    const msgFailStatus = 1;    //响应状态码失败
    const msgCheckFail=2;  //校验不通过
    const msgTokenFail=3;  //token校验不通过
    const msgDBFail=4;  //数据库异常
}

 class result{
     public $header;
     public $body;
     public $type;
 }
 class header{
     public $stats;
     public $msg;
 }
 class body{
     public $list;
 }


class Route{

	private static $route_lists;		//已注册的路由列表
	public $url;			//原始url
	public $requestType;		//请求类型
	public $module;
	public $controller;
	public $action;	

	public static function __callStatic($module, $arg){			
		$module=strtolower($module);		//模块目录只能为小写
		$regulars=$arg[0];
		$requestType=isset($arg[1])? $arg[1]: 'get' ;		//默认为get
		foreach ($regulars as $url => &$controllerAction) {
			if (isset( self::$route_lists[$requestType. ':'. $url] )) {
				myerror(StatusCode::msgFailStatus, "路由存在冲突，请检查重新注册");
			}else{
				self::registeRoute($requestType, $url, $module, $controllerAction);
				// dump($module);dump($regulars);
			}			
		}


		
	}

	public function __construct($url, $requestParam){			//$requestParam是json字符串
		if ( strlen($url)>1 )  {						//去掉右端的目录符号；根目录则跳过
			$this->url=rtrim($url, '/');	

		}
		$this->url=$url;
    	// 路由检查是否写入请求参数

		// if ( isset($requestParam['_post']) ){		
		// 	$this->requestType='post';

		// 	unset($requestParam['_post']);

		// }elseif( isset($requestParam['_put']) ){
		// 	$this->requestType='put';
		// 	unset($requestParam['_put']);

		// }elseif(isset($requestParam['_delete']) ){
		// 	$this->requestType='delete';
		// 	unset($requestParam['_delete']);

		// }else{									//默认为get空间
			$this->requestType='get';
			// unset($requestParam['_get']);
		// }
		_request('set', $requestParam);			//设置全局请求参数
		unset($requestParam);
	    if ($this->checkUrl()) {			
	    	$this->_run();							//运行路由
	    }else{
	        myerror(StatusCode::msgFailStatus, "无效的请求", $this->url);
	    }
		
	}
	public static function getLists(){		//获取全部路由表
		/**
		* 	形如   ['get:/user/login'=>'module.controller.action'  ]; 
		*/ 

		return self::$route_lists;
	}

	public function get($requestType, $url){			//获取单个注册路由
		return  self::$route_lists[ $requestType. ':'. $url ];
	}

	private static function registeRoute($requestType, $url, $module, $controllerAction){			//注册路由
		self::$route_lists[ $requestType. ':'. $url ]=$module. '.'. $controllerAction;

	}	

	private function set(){		//设置模块，控制器，方法
		$tmp=explode('.', self::$route_lists[ $this->requestType. ':'. $this->url ]);
		$this->module=$tmp[0];
		$this->controller=$tmp[1];
		$this->action=$tmp[2];

	}
	private function checkUrl(){		//检查路由是否注册
		if ( isset(self::$route_lists[ $this->requestType. ':'. $this->url ]) ){
			return true;
		}else{
			return false;
		}
	}


	private function _run(){		//运行路由器, 实现控制器动作
		$this->set();
		define('__CONTROLLER__', $this->controller);			//设置当前url常量
		define('__ACTION__', $this->action);
		define('__MODULE__', $this->module);


		require_once __API__. $this->module. '/controllers/'. $this->controller. 'Controller.php';
        $ctl_name = 'api\\'. $this->module.'\\'. $this->controller . 'Controller';
        $controller = new $ctl_name;
        $controller->_init();				//运行路由初始化方法
        $actionName = $this->action;
        $controller->$actionName();		


	}

}
class MODEL {
	public static function __callStatic($model, $args){
		$path=__API__. '/'. __MODULE__. '/models/'. "${model}Model.php";
		require_once($path);

		$modelname= 'api\\'. __MODULE__. '\\'. $model. 'Model';


		$class = new ReflectionClass($modelname);
		$a=$class->newInstanceArgs($args);


		return $a;
	}

}

class DAO{
	public static function __callStatic($dao, $args){
		$path=__API__. '/'. __MODULE__. '/daos/'. "${dao}Dao.php";
		require_once($path);

		$daoname= 'api\\'. __MODULE__. '\\'. $dao. 'Dao';
		$class = new ReflectionClass($daoname);
		$a=$class->newInstanceArgs($args);
		// array_push($GLOBALS['DAO'] ,serialize($a));
		return $a;
	}	


}

class Valid{		// 特殊校验类，不允许实例化
	private $info;
	private static $valid;
	private $instance;
	private function __construct(){

	}
	public static function __callStatic($func, $args){
		if (!call_user_func_array($func, $args)) {
		 	self::$valid=false;
		}else{
			self::$valid=true;
		}
		$instance=new Valid();
		return $instance;
	}
	public function withError($info){
		if (!self::$valid) {
			myerror(StatusCode::msgCheckFail, $info);
		}else{
			return true;
		}
	}

}




/**
*获取配置文件
*author：林澜叶
*/

class Config{

    /**
    *@param array $config  	存储配置文件的数组
    *@param string $file    配置文件名
    */

    private static $config=[];
    private static $file;



    /**
    *获取或设置配置文件，获取型如：config::file('key')； 设置类型如config::file(['key'=>'value'])
    */
    public static function __callStatic($file, $arg){
        $arg=$arg[0];                   //只接受单参数;
        $file=strtolower($file);        

        if ( !isset(self::$config[$file]) ) {
            self::$config[$file]=include(__CONFIG__. $file. '.php');
        }
      
        if ( is_array($arg) ) {         //设置配置,支持批量设置
            foreach ($arg as $key => $value) {
                self::set($key, $value, $file);
            }
        }else{                      //获取配置
            return self::get($arg, $file);
        }        

    }


    /**
    *返回已加载的全部配置
    */    
    public static function getAll(){
    	return self::$config;
    }


    /**
    *设置配置，可链式设置
    */
    private static function set($key, $value, $file){     
        $nodes=explode('.', $key);

		self::recursiveSet(self::$config[$file], $nodes, $value);
    }


    /**
    *设置链式调用的配置量
    *@param array $conf   某个配置文件的内容
    *@param array $nodes    链式调用配置的节点
    */
    private static function recursiveSet(&$conf, &$nodes, &$value){            
        $k=current($nodes);
        if ( next($nodes) ){			//递归进入最底层节点
            $conf[$k]=self::recursiveSet($conf[$k], $nodes, $value);
        }else{
        	$conf[$k]=$value;		//进入最底层节点后，赋值
        }
        return $conf;
    }



    /**
    *设置配置，可链式设置，支持批量设置
    */
    private static function get($key, $file){
        if ( is_null($key) ) {           //没有设置key时候返回全部
            return self::$config[$file];
        }
        $nodes=explode('.', $key);			
        return self::recursiveGet(self::$config[$file], $nodes);
    }


    /**
    *获取链式调用的配置量
    *@param array $conf   某个配置文件的内容
    *@param array $nodes    链式调用配置的数组
    *@return mixed
    */
    private static function recursiveGet(&$conf, &$nodes){            
        $k=current($nodes);			
        if ( isset($conf[$k]) ) {   		//节点有配置  
            if ( next($nodes) ){				//如果还需调用子节点，否则返回当前节点配置
                return self::recursiveGet($conf[$k], $nodes);
            }
            return $conf[$k];
        }else{                           //节点没有配置信息
            return null;
        }
    }


}
