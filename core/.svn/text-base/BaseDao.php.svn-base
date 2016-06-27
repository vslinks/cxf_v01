<?php
/* author:陈清 林澜叶
  desc:数据访问层基类
  date:2016-06-20
 */

class BaseDao {
    /*     * 构造函数 */
    public $db;

    function __construct() {
        $this->db = new MysqlDriver();
    }

    /*     * *新增一条数据到数据库，返回整个实体或者自增id，默认返回id
     * @param $model 数据实体
     * @param $type 返回类型，为model时候返回model实体
     * @param return 返回实体
     */




    public function insert($model, $type='') {
        try {
            $result=$this->db->insert(get_object_vars($model), $type);
            if ($this->_checkTransON()) {                                                   //如果事务开启，
                $this->_saveTransData($result, $model, 1);                                     //存储事务数据
            }
            return $result;
        } catch (Exception $e) {                                                                    //异常处理
            if ($this->_checkTransON()) {                                                       //如果事务开启，
                $this->_terminateTrans();      
                $log=db_error_message($model,1, 1);                                       //立即终止事务
                myerror(StatusCode::msgDBFail, $log, $e->getMessage());                    
            }
            $log=db_error_message($model);   
            myerror(StatusCode::msgDBFail, $log, $e->getMessage());    
        }


    }


    /*     * * 保存多个对象
     * @param $arr_model 实体数组
     * @param return int 返回影响条数
     */

    public function insertMany($arr_model) {
        try {
            $result=$this->db->insertMany($arr_model);
            if ($this->_checkTransON()) {                                                       //如果事务开启，
                $this->_saveTransData($result, $arr_model, 4);                                     //存储事务数据
            }
            return $result;
        } catch (Exception $e) {                                                                    //异常处理
            if ($this->_checkTransON()) {                                                       //如果事务开启，
                $this->_terminateTrans();      
                $log=db_error_message($model,4, 1);                                       //立即终止事务
                myerror(StatusCode::msgDBFail, $log, $e->getMessage());                    
            }
            $log=db_error_message($model,4);   
            myerror(StatusCode::msgDBFail, $log, $e->getMessage());    

        }

        return $this->db->insertMany($arr_model);
    }

    /*     * 删除一个对象实体 */

    public function delete($model) {
        try {
            $result=$this->db->delete($model);
            if ($this->_checkTransON()) {                                                       //如果事务开启，
                $this->_saveTransData($result, $model, 1);                                     //存储事务数据
            }
            return $result;
        } catch (Exception $e) {                                                                    //异常处理
            if ($this->_checkTransON()) {                                                       //如果事务开启，
                $this->_terminateTrans();      
                $log=db_error_message($model,2, 1);                                       //立即终止事务
                myerror(StatusCode::msgDBFail, $log, $e->getMessage());                    
            }
            $log=db_error_message($model, 2);   
            myerror(StatusCode::msgDBFail, $log, $e->getMessage());    
        }
    }

    /*     * 修改一个对象实体 */

    public function update($model) {
        try {
            $result=$this->db->update(get_object_vars($model));
            if ($this->_checkTransON()) {                                                       //如果事务开启，
                $this->_saveTransData($result, $model, 1);                                     //存储事务数据
            }
            return $result;
        } catch (Exception $e) {                                                                    //异常处理
            if ($this->_checkTransON()) {                                                       //如果事务开启，
                $this->_terminateTrans();      
                $log=db_error_message($model,3, 1);                                       //立即终止事务
                myerror(StatusCode::msgDBFail, $log, $e->getMessage());                    
            }
            $log=db_error_message($model, 3);   
            myerror(StatusCode::msgDBFail, $log, $e->getMessage());    
        }
    }

    /*     * 获取总条数记录 */

    public function count($model) {
        $c = $this->db->count(get_object_vars($model));
        return $c;
    }

    /*     * 分页查询方法 */

    public function getList($model, $fields='', $paging=1, $order = 'asc') {
        $c = $this->db->getList(get_object_vars($model), $fields, $paging, $order);
        return $c;
    }
    
      /*
     * 开始事物
     */

    protected function begin_trans() {
        return $this->db->beginTrans();
    }

    /*
     * 回滚事物
     */

    protected function roll_back() {
        return $this->db->rollBack();
    }

    /*
     * 提交事物
     */

    protected function _commit() {
        return $this->db->commit();
    }

    public function excute($sql, $parmeters) {
        try {
            $result=$this->db->excute($sql, $parmeters);
            if ($this->_checkTransON()) {                                                       //如果事务开启，
                $this->_saveTransData($result, $model, 1);                                     //存储事务数据
            }
            return $result;
        } catch (Exception $e) {                                                                    //异常处理
            if ($this->_checkTransON()) {                                                       //如果事务开启，
                $this->_terminateTrans();      
                $log=db_error_message($model,'', 1);                                       //立即终止事务
                myerror(StatusCode::msgDBFail, $log, $e->getMessage());                    
            }
            $log=db_error_message($model, '');   
            myerror(StatusCode::msgDBFail, $log, $e->getMessage());    
        }


        // $sql = "update users set name=:name where password=:password";
        // //$parmeters = array(":name"=>'zengli',":password"=>'pwd');
    }

    /*     * 执行自定义查询语句，多表查询等等 */

    public function query($sql, $parmeters) {
        // $sql = "select * from users where name=:name";
        // //$parmeters = array(":name"=>'zengli',":password"=>'pwd');
        $result = $this->db->query($sql, $parmeters);
        return $result;
    }

    public function beginTrans(){               //事务开始标记
        $GLOBALS['_trans_']=isset($GLOBALS['_trans_'])? $GLOBALS['_trans_']: [];
        $GLOBALS['_trans_']['begin'] = true;
        $this->begin_trans();
    }
    public function endTrans(){                 //事务结束标记
        foreach ($GLOBALS['_trans_']['result'] as $key => &$v) {
            if (!$v['return']) {                                              //事务不通过
                $this->_terminateTrans();
                $log=db_error_message($v['model'],$v['type'], 1, 0);   
                myerror(StatusCode::msgDBFail, $log);  
            }
        }
        unset($GLOBALS['_trans_']);
        $this->_commit();

    }

    protected function _checkTransON(){                     //检查事务是否开启
        if ($GLOBALS['_trans_']['begin'] === true) {
            return true;
        }else{
            return false;
        }
        
    }
    protected function _saveTransData($data, &$model, $type){              //存储事务数据，
        if (is_object($data)) {
            $data=isset($data->id)? $data->id: 0;
        }
        $GLOBALS['_trans_']['result'][]=['return'=>$data, 'model'=>$model, 'type'=>$type];         
    }

    protected function _terminateTrans(){           //强制结束事务
        unset($GLOBALS['_trans_']);
        $this->roll_back();
    }

}
