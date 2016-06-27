<?php
/* author:陈清 
  desc:数据模型实体类
  date:2016-06-20
  author:陈清
 */

class BaseModel {

    public $table;

    static $init_valid_array;
    function __construct($tablename) {
        $this->table = $tablename;
    }

    //验证实体数据的准确性和完整性

    public function valid() {

        $obj_value= get_object_vars($this);
        unset($obj_value["db"]);
        unset($obj_value["table"]);
        foreach ($obj_value as $key => $value) {
            $type = self::$init_valid_array[$key][0];
            $len = self::$init_valid_array[$key][1];
            $isnull = self::$init_valid_array[$key][2];
            if ($type == "int") {
                if ($isnull != "NO" && !is_int($value)) {
                    myerror(StatusCode::msgFailStatus, '请检查你的输入，数' . $key . '要求整形');
                }
            }
            if ($type == "float") {
                if ($isnull != "NO" && !is_float($value)) {
                    myerror(StatusCode::msgFailStatus, '请检查你的输入，数' . $key . '要求float');
                }
            }
            if ($isnull != "NO" && strlen($value) < 1) {
                myerror(StatusCode::msgFailStatus, '请检查你的输入参数' . $key . '要求不能为空');
            }
            if ($isnull != "NO" && !empty($len) && strlen($len)>0 &&  strlen($value) > $len) {
                myerror(StatusCode::msgFailStatus, '请检查你的输入参数' . $key . '的长度不能超过' . $len);
            }
        }
    }



}
