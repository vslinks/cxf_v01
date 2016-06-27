<?php	
 
abstract class DbDriver {

	protected $dbms;
	protected $dbhost;
    protected $dbname;
    protected $dbuser;
    protected $dbport;
    protected $dbpwd;
    protected $pdo;

    /**
     * 构造函数初始化
     */
    function __construct() {
        $this->dbms = Config('db.type');       //数据库类型，oracle用ODI,对于开发者来说，使用不同的数据库，只要改这个，不用记住那么多的函数了
        $this->dbhost = Config('db.host'); 		// $config['host']; 	//数据库主机名
        $this->dbname = Config('db.name');  //$config['dbname'];  	//使用的数据库名称
        $this->dbuser = Config('db.user');  //$config['user'];      //数据库连接用户名
        $this->dbport = Config('db.port');  //$config['port'];      //数据库连接端口
        $this->dbpwd = Config('db.pwd');  //$config['pass'];        //数据库连接密码     

        $dsn = "$this->dbms:host=$this->dbhost;dbname=$this->dbname";
        $this->pdo = new PDO("$this->dbms:host=$this->dbhost;port=$this->dbport;dbname=$this->dbname", $this->dbuser, $this->dbpwd); //初始化一个PDO对象
        $this->pdo->query("set names 'utf8'");

        
    }



}