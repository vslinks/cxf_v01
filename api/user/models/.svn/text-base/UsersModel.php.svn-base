<?php
namespace api\user;
class UsersModel extends Model {

    function __construct($id = null, $name = null, $password = null) {

        $this->id = $id;
        $this->name = $name;
        $this->password = $password;
        $this->table = 'users';
    }


    static $init_valid_array = array("id" => array('int', '', 'NO', '用户ID'), "name" => array('varchar', '255', 'YES', '用户名称'), "password" => array('varchar', '255', 'YES', '用户密码'));
    public $id;
    public $name;
    public $password;
}