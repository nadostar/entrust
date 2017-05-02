<?php

require_once __DIR__ . '/_DatabaseAccess.php';

/**
 * MySQL Database Access Class.
 *
 * @package    fw.core.database
 */
class DatabaseAccessMySQL extends _DatabaseAccess
{
    protected $found_rows = NULL;

    public function selectArrayFoundRows($query, $param = array(), _KVSAccess $KVSAccess = null)
    {
        $query = trim($query);
        if (!preg_match('/^SELECT SQL_CALC_FOUND_ROWS/is', $query)) {
            $query = preg_replace('/^SELECT/is', 'SELECT SQL_CALC_FOUND_ROWS', $query);
        }

        $ret = $this->selectArray($query, $param, $KVSAccess);

        ///////////// 件数を保持
        $query = 'SELECT FOUND_ROWS() as found_rows';
        $this->sendQuery($query);
        $data = $this->fetch();
        $this->found_rows = $data[0]['found_rows'];
        //////////////////////////
        return $ret;
    }

    public function insert($table, $param = array(), $isDate = true)
    {
        $param_array = array();
        $param_array[] = $param;
        $this->insert_bulk($table, $param_array, $isDate);
    }

    public function insert_bulk($table, $param_array, $isDate = true)
    {
        if (!(is_array($param_array) && is_array($param_array[0]) && count($param_array) > 0)) {
            return;
        }

        $c = "";
        foreach ($param_array[0] as $column => $value) {
            $c .= $column . ',';
        }
        if($isDate) {
	        if (!isset($param_array[0]["created_at"])) {
	            $c .= "created_at,";
	        }
	        if (!isset($param_array[0]["updated_at"])) {
	            $c .= "updated_at,";
	        }
        }
        $c = rtrim($c, ',');

        $v = "";
        $bind_value = array();
        foreach ($param_array as $param) {
            $v .= "(";
            foreach ($param as $column => $value) {
                if (is_array($value) && isset($value[0])) {
                    $v .= $value[0];
                } else {
                    $v .= '?';
                    $bind_value[] = $value;
                }
                $v .= ",";
            }
            if($isDate) {
	            if (!isset($param_array[0]["created_at"])) {
	                $v .= "now(),";
	            }
	            if (!isset($param_array[0]["updated_at"])) {
	                $v .= "now(),";
	            }
            }
            $v = rtrim($v, ',');
            $v .= "),";
        }
        $v = rtrim($v, ',');
        $query = "INSERT INTO $table ($c) VALUES $v";
        LogManager::debug($query);
        $this->prepare($query);
        $this->bindValue($bind_value);
        $this->execute();
    }

    public function lockRow($table, $condition = '', $param = array())
    {
        try {
            $query = "SELECT * FROM $table";
            if ($condition !== '') {
                $query .= " WHERE $condition";
            }
            $query .= " FOR UPDATE ";
            LogManager::debug($query);
            $this->prepare($query);
            $this->bindValue($param);
            $this->execute();

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function lockTable($table_array, $mode_array = null)
    {
        try {
            $table = '';
            $i = 0;
            foreach ($table_array as $key => $each) {
                if ($i++ > 0) {
                    $table .= ',';
                }
                $table .= $each;
                if (!is_null($mode_array) && isset($mode_array[$key])) {
                    $table .= ' '.$mode_array[$key];
                } else {
                    $table .= ' WRITE';
                }
            }
            $query = 'LOCK TABLES '.$table;
            $this->sendQuery($query);

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function unlockTable()
    {
        $query = 'UNLOCK TABLES';
        $this->sendQuery($query);
    }

    public function getFoundRows()
    {
        return $this->found_rows;
    }

    public function getLastInsertIDArray($insert_count)
    {
        $insert_id = $this->con->lastInsertId();
        $ret = array();
        for ($i = 0; $i < $insert_count; $i++) {
            $ret[] = intval($insert_id++);
        }

        return $ret;
    }

    public function insertOnDuplicateKeyUpdate($table, $insert_param, $update_param)
    {
        if (!is_array($insert_param) && count($insert_param) === 0) {
            return;
        }
        if (!is_array($update_param) && count($update_param) === 0) {
            return;
        }

        $bind_value = array();
        $value_arry = array();
        $column_arry = array();

        $query = '';

        // 値をプレースホルダ化し、別にまとめる
        foreach ($insert_param as $column => $value) {
            $column_arry[] = $column;
            if (is_array($value) && isset($value[0])) {
                //リテラル
                $value_arry[] = $value[0];
            } else {
                $value_arry[] = '?';
                $bind_value[] = $value;
            }
        }
		
        /*
        if (!isset($insert_param["create_date"])) {
            $column_arry[] = "create_date";
            $value_arry[] = "now()";
        }
        if (!isset($insert_param["update_date"])) {
            $column_arry[] = "update_date";
            $value_arry[] = "now()";
        }
        */
		
        $query = "INSERT INTO $table (" . implode(',', $column_arry) . ") VALUES (" . implode(',', $value_arry) . ")";
        /////////////////////////////////////////

        $set_arry = array();

        // 値をプレースホルダ化し、別にまとめる
        foreach ($update_param as $column => $value) {
            if (is_array($value) && isset($value[0])) {
                $set_arry[] = "$column = " . $value[0];
            } else {
                $set_arry[] = "$column = ?";
                $bind_value[] = $value;
            }
        }

        //$set_arry[] = "update_date = now()";

        $query .= " ON DUPLICATE KEY UPDATE " . implode(",", $set_arry);
        $this->prepare($query);
        $this->bindValue($bind_value);
        $this->execute();

    }

}
