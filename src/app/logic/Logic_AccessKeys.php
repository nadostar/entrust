<?php

/**
* 
*/
class Logic_AccessKeys extends _Logic_App {
	
	public static function getAccessKeyDataArray(_DatabaseAccess $dao, $pid, $link_id, $limit, $offset) {
		$ret = array();

		$sql = "select * from access_keys where pid = ?";
		if(empty($link_id)) {
			$sql .= sprintf(" and link_id = '%s'", $link_id);
		}
		$sql = " order by link_id, link_key limit ? offset ?";

		$param = array($pid, $limit, $offset);

		
		$ret['list'] = $dao->selectArrayFoundRows($sql, $param);
		$ret['count'] = $dao->getFoundRows();

		return $ret;
	}

	public static function getAccessKeyDataByLinkId(_DatabaseAccess $dao, $link_id) {
		$sql = "select * from access_keys where link_id = ?";
		$param = array($link_id);

		return $dao->selectArray($sql, $param);
	}

	public static function getAccessKeyDataByPid(_DatabaseAccess $dao, $pid) {
		$sql = "select * from access_keys where link_id = ?";
		$param = array($link_id);

		return $dao->selectArray($sql, $param);
	} 

	public static function getAccessKeyDataByKey(_DatabaseAccess $dao, $key) {
		$sql = "select * from access_keys where access_key = ? and disable = 0";
		$param = array($key);

		return $dao->selectOne($sql, $param);
	}

	public static function insertAccessKeysData(_DatabaseAccess $dao, $params) {
		try {
			$dao->insert_bulk('access_keys', $params, false);
		} catch (Exception $e) {
			LogManager::error($e->getMessage());
			return false;
		}

		return true;
	}
}