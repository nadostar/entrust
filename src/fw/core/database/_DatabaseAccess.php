<?php

require_once __DIR__.'/../exception/Exception_Database.php';

class _DatabaseAccess {

  protected $param;

  protected $driver;

  protected $con = null;

  protected $last_query = '';

  protected $bind_param = array();

  protected $choice_param;

  protected $result_set;

  protected $is_master = true;

  protected $ready_start_transaction = false;
  protected $exec_start_transaction = false;

  function __construct($param) {
    $this->param = $param['param'];
    $this->driver = $param['driver'];
  }

  public function connect() {

    if (is_null($this->con)) {
      $param_array = array();

      $this->choice_param = $this->choiceDB();

      if (!empty($this->driver)) {
        $driver = $this->driver;
      } else {
        throw new Exception_Database('invalid driver');
      }

      if (isset($this->choice_param['master'])) {
        $this->is_master = $this->choice_param['master'];
      }

      if (isset($this->choice_param['host'])) {
        $host = $this->choice_param['host'];
      } else {
        throw new Exception_Database('invalid host');
      }

      if (isset($this->choice_param['port'])) {
        $port = $this->choice_param['port'];
      } else {
        throw new Exception_Database('invalid port');
      }

      if (isset($this->choice_param['user'])) {
        $user = $this->choice_param['user'];
      } else {
        throw new Exception_Database('invalid user');
      }

      if (isset($this->choice_param['password'])) {
        $password = $this->choice_param['password'];
      } else {
        $password = '';
      }

      if (isset($this->choice_param['database'])) {
        $database = $this->choice_param['database'];
      } else {
        throw new Exception_Database('invalid database');
      }
	
      try {
       if ($this->driver_options_array === null) {
        $this->con = new PDO("${driver}:host=${host}; port=${port}; dbname=${database}", $user, $password);
      } else {
       $this->con = new PDO("${driver}:host=${host}; port=${port}; dbname=${database}", $user, $password, $this->driver_options_array);
      }

      $this->con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

      } catch (Exception_Database $e) {
       throw new Exception_Database('faild database connect');
      }

      return null;
    }
  }

  protected function choiceDB() {
    if (is_hash($this->param)) {
      return $this->param;
    } else {
      return $this->param[mt_rand(0, count($this->param)-1)];
    }
  }

  public function sendQuery($query) {
    $this->connect();
    $this->last_query = $query;

    if ($this->isUpdateQuery($query)) {
      if ($this->is_master) {
        if ($this->ready_start_transaction && !$this->exec_start_transaction) {
          $this->startTransaction(true);
        }
      } else {
        throw new Exception_Database('except select query send to slave:'.$query);
      }
    }
    
    $this->result_set = $this->con->query($query);
    if (!$this->result_set) {
      throw new Exception_Database($this->getLastErrorNumber() . ': ' . $this->getLastErrorString());
    }
  }

  public function prepare($query) {
    $this->connect();
    $this->last_query = $query;

    if ($this->isUpdateQuery($query)) {
      if ($this->is_master) {
        if ($this->ready_start_transaction && !$this->exec_start_transaction) {
          $this->startTransaction(true);
        }
      } else {
        throw new Exception_Database('except select query send to slave:'.$query);
      }
    }
    $this->result_set = $this->con->prepare($query);
    if (!$this->result_set) {
      throw new Exception_Database($this->getLastErrorNumber() . ': ' . $this->getLastErrorString());
    }
  }

  public function bindValue($param=array()) {
    if (empty($param)) {
      return;
    }
    $i = 1;
    $this->bind_param = array();
    foreach ($param as $val) {
      if (is_null($val)) {
        $type = PDO::PARAM_NULL;
      } elseif (is_bool($val)) {
        $type = PDO::PARAM_BOOL;
      } elseif (is_float($val)) {
        $type = PDO::PARAM_INT;
      } elseif (is_int($val)) {
        $type = PDO::PARAM_INT;
      } elseif (is_string($val)) {
        $type = PDO::PARAM_STR;
      }
      $this->result_set->bindValue($i, $val, $type);
      $this->bind_param[] = $val;
      $i++;
    }
  }

  public function execute() {
    LogManager::trace($this->getLastQuery());
    if ($this->result_set->execute() === false) {
      throw new Exception_Database($this->getLastErrorNumber() . ': ' . $this->getLastErrorString());
    }
  }

  public function executeQuey($query, $param) {
    $this->prepare($query);
    $this->bindValue($param);
    $this->execute();
  }

  public function isUpdateQuery($query) {
    $tmp = trim(strtoupper($query));
    return substr($tmp, 0, strlen('SELECT')) !== 'SELECT' || strpos($tmp, ' FOR UPDATE');
  }

  public function selectOne($query, $param=array()) {
    $data = $this->selectArray($query, $param);
    if (count($data) == 0) {
      return false;
    }
    return $data[0];
  }

  public function selectArray($query, $param=array()) {
      $ret = array();

      $this->prepare($query);
      $this->bindValue($param);
      $this->execute();
      $ret = $this->fetch();

      return $ret;
  }

  public function selectMap($query, $key, $param=array()) {
    $record_array = self::selectArray($query, $param);
    $record_map = array();
    foreach ($record_array as $record) {
      $record_map[$record[$key]] = $record;
    }
    return $record_map;
  }

  public function selectMMap($query, $key, $param=array()) {
    $record_array = self::selectArray($query, $param);
    $record_map = array();
    foreach ($record_array as $record) {
      $record_map[$record[$key]][] = $record;
    }
    return $record_map;
  }

  public function selectArrayFoundRows($query, $param = array()) {
    $query = trim($query);
    if (!preg_match('/^SELECT SQL_CALC_FOUND_ROWS/is', $query)) {
      $query = preg_replace('/^SELECT/is', 'SELECT SQL_CALC_FOUND_ROWS', $query);
    }
    return $this->selectArray($query, $param);
  }

  public function insert($table, $param, $isDate = true) {
    $param_array = array();
    $param_array[] = $param;
    $this->insert_bulk($table, $param_array, $isDate);
  }

  public function insert_bulk($table, $param_array, $isDate = true) {
    if (!(is_array($param_array) && is_array($param_array[0]) && count($param_array) > 0 && count($param_array[0]) > 0)) {
      return;
    }

    $cnt = 0;
    $c = "";
    foreach ($param_array[0] as $column => $value) {
      if($cnt++ > 0) {
        $c .= ",";
      }
      $c .= $column;
    }
    if($isDate) {
    	if (!isset($param_array[0]["created_at"])) {
    		$c .= ",created_at";
    	}
    	if (!isset($param_array[0]["updated_at"])) {
    		$c .= ",updated_at";
    	}
    }

    $cnt = 0;
    $v = "";
    $bind_value = array();
    foreach ($param_array as $param) {
      $cnt_2 = 0;
      if($cnt++ > 0) {
        $v .= ",";
      }
      $v .= "(";
      foreach ($param as $column => $value) {
        if($cnt_2++ > 0) {
          $v .= ",";
        }
        if (is_array($value) && isset($value[0])) {
          $v .= $value[0];
        } else {
          $v .= '?';
          $bind_value[] = $value;
        }
      }
      if($isDate) {
      	if (!isset($param_array[0]["created_at"])) {
      		$v .= ",now()";
      	}
      	if (!isset($param_array[0]["updated_at"])) {
      		$v .= ",now()";
      	}
      }
      $v .= ")";
    }
    $query = "INSERT INTO $table ($c) VALUES $v";
    
    LogManager::debug($query);

    $this->prepare($query);
    $this->bindValue($bind_value);
    $this->execute();
  }

  public function update($table, $update_param, $condition='', $condition_param=array(), $isDate = true) {
    $cnt = 0;
    $c = "";

    $bind_value = array();
    foreach($update_param as $column => $value) {
      if($cnt++ > 0) {
        $c .= ",";
      }
      if (is_array($value) && isset($value[0])) {
        $c .= "$column = ".$value[0];
      } else {
        $c .= "$column = ?";
        $bind_value[] = $value;
      }
    }
    if($isDate) {
	    if (!isset($param["updated_at"])) {
	      $c .= ",updated_at = now()";
	    }
    }
    foreach ($condition_param as $con_value) {
      $bind_value[] = $con_value;
    }

    $query = "UPDATE $table SET $c";
    if ($condition !== '') {
      $query .= " WHERE $condition";
    }

    LogManager::debug($query);

    $this->prepare($query);
    $this->bindValue($bind_value);
    $this->execute();
  }

  public function delete($table, $condition='', $param=array()) {
    $query = "DELETE FROM $table";
    if ($condition !== '') {
      $query .= " WHERE $condition";
    }
    $this->prepare($query);
    $this->bindValue($param);
    $this->execute();
  }

  public function startTransaction($exec_flg=false) {
    if ($exec_flg) {
      $this->exec_start_transaction = true;
      $this->con->beginTransaction();
    } else {
      $this->ready_start_transaction = true;
    }
  }

  /**
   * ロールバック発行
   */
  public function rollback() {
    if ($this->exec_start_transaction) {
      $this->con->rollBack();
      $this->exec_start_transaction = false;
    }
  }

  /**
   * コミット発行
   */
  public function commit() {
    if ($this->exec_start_transaction) {
      $this->con->commit();
      $this->exec_start_transaction = false;
    }
  }

  public function lockRow($table, $condition='', $param=array()) {
    try {
      $query = "SELECT * FROM $table";
      if ($condition !== '') {
        $query .= " WHERE $condition";
      }
      $query .= " FOR UPDATE ";
            
      $this->prepare($query);
      $this->bindValue($param);
      $this->execute();
      return true;
    } catch (Exception $e) {
      return false;
    }
  }

  public function lockTable($table_array, $mode_array=null) {
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

  public function unlockTable() {
    $query = 'UNLOCK TABLES';
    $this->sendQuery($query);
  }

  public function getFoundRows() {
    $query = 'SELECT FOUND_ROWS() as found_row';
    $this->sendQuery($query);
    $data = $this->fetch();
    return $data[0]['found_row'];
  }

  public function getLastInsertID() {
    $insert_id = $this->con->lastInsertId();
    return $insert_id;
  }

  public function getLastAffectedRows() {
    return $this->result_set->rowCount();
  }

  public function getLastErrorString() {
    $error = $this->con->errorInfo();
    return $error[2];
  }

  public function getLastErrorNumber() {
    $error = $this->con->errorInfo();
    return $error[1];
  }

  public function escapeLike($str) {
    return $this->escape(str_replace(array('\\', '%', '_'), array('\\\\', '\\%', '\\_'), $str));
  }

  public function fetch($result_type=PDO::FETCH_ASSOC) {
    if ($this->result_set == null) {
      return null;
    }
    return $this->result_set->fetchAll($result_type);
  }

  public function getLastQuery() {
    $sql = $this->last_query;
    foreach ($this->bind_param as $val) {
      $tmp = '';
      if (is_null($val)) {
        $tmp = 'null';
      } elseif (is_bool($val)) {
        if ($val === true) {
          $tmp = 'true';
        } else {
          $tmp = 'false';
        }
      } elseif (is_float($val)) {
        $tmp = $val;
      } elseif (is_int($val)) {
        $tmp = $val;
      } elseif (is_string($val)) {
        $tmp = "'$val'";
      }
      $sql = preg_replace('/\?/', $tmp, $sql, 1);
    }
    return $sql;
  }
  
  public function close()
  {
  	$this->con = null;
  }
}