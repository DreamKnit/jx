<?php

namespace Admin\Logic;

/**
 * Description of DbMySqlLogic
 *
 * @author qingf
 */
class DbMySqlLogic implements DbMysqlIntface {

    public function connect() {
        echo __METHOD__ . '<br />';
    }

    public function disconnect() {
        echo __METHOD__ . '<br />';
    }

    public function free($result) {
        echo __METHOD__ . '<br />';
    }

    public function getAll($sql, array $args = array()) {
        echo __METHOD__ . '<br />';
    }

    public function getAssoc($sql, array $args = array()) {
        echo __METHOD__ . '<br />';
    }

    public function getCol($sql, array $args = array()) {
        echo __METHOD__ . '<br />';
    }

    /**
     * @param string $sql
     * @param array $args
     * @return mixed
     */
    public function getOne($sql, array $args = array()) {
        $args  = func_get_args();
        $sql   = array_shift($args);
        $parms = $args;
        $sqls  = preg_split('/\?[NTF]/', $sql);
        array_pop($sqls);
        $sql   = '';
        foreach ($sqls as $key => $value) {
            $sql .= $value . $parms[$key];
        }
        $rows = M()->query($sql);
        if ($rows) {
            $row = array_shift($rows);
            return array_shift($row);
        }
    }

    /**
     * 得到一行数据
     * @param string $sql
     * @param array $args
     * @return array
     */
    public function getRow($sql, array $args = array()) {
        $args  = func_get_args();
        $sql   = array_shift($args);
        $parms = $args;
        $sqls  = preg_split('/\?[NTF]/', $sql);
        array_pop($sqls);
        $sql   = '';
        foreach ($sqls as $key => $value) {
            $sql .= $value . $parms[$key];
        }
        $rows = M()->query($sql); // 查询方法执行sql
        if ($rows) {
            return array_shift($rows);
        }
    }

    /**
     * 重写接口的插入方法，拼接sql语句并执行
     * @param string $sql
     * @param array $args
     * @return bool|string
     */
    public function insert($sql, array $args = array()) {
        $args= func_get_args();
        $sql        = array_shift($args);
        $table_name = $args[0];
        $params     = $args[1];
        unset($params['id']);
        $sql        = str_replace('?T', '`' . $table_name . '`', $sql);
        $fields     = array();
        foreach ($params as $key => $value) {
            $fields[] = '`' . $key . '`="' . $value . '"';
        }
        $field_str = implode(',', $fields);
        $sql       = str_replace('?%', $field_str, $sql);
        if(M()->execute($sql)!==false){
            return M()->getLastInsID();
        }else{
            return false;
        }
    }

    /**
     * @param string $sql
     * @param array $args
     * @return false|int
     */
    public function query($sql, array $args = array()) {
        $args  = func_get_args();
        $sql   = array_shift($args);
        $parms = $args;
        $sqls  = preg_split('/\?[NTF]/', $sql);
        array_pop($sqls);
        $sql   = '';
        foreach ($sqls as $key => $value) {
            $sql .= $value . $parms[$key];
        }
        return M()->execute($sql);
    }

    /**
     * @param string $sql
     * @param array $args
     */
    public function update($sql, array $args = array()) {
        echo __METHOD__ . '<br />';
    }
}
