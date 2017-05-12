<?php

/**
* 
*/
class Logic_AccessKeys extends _Logic_App {
	
	public static function getAccessKeyDataArray(_DatabaseAccess $dao, $pid, $link_id, $limit, $offset) {
		$ret = array();

		$sql = "SELECT * FROM `accesskeys` WHERE `pid` = ?";
		if(empty($link_id)) {
			$sql .= sprintf(" AND `link_id` = '%s'", $link_id);
		}
		$sql = " ORDER BY `link_id`, `link_key` LIMIT ? OFFSET ?";

		$param = array($pid, $limit, $offset);

		
		$ret['list'] = $dao->selectArrayFoundRows($sql, $param);
		$ret['count'] = $dao->getFoundRows();

		return $ret;
	}

	public static function getAccessKeyDataByLinkId(_DatabaseAccess $dao, $link_id) {
		$sql = "SELECT * FROM `accesskeys` WHERE `link_id` = ?";
		$param = array($link_id);

		return $dao->selectArray($sql, $param);
	}

	public static function getAccessKeyDataByPid(_DatabaseAccess $dao, $pid) {
		$sql = "SELECT * FROM accesskeys WHERE `link_id` = ?";
		$param = array($link_id);

		return $dao->selectArray($sql, $param);
	} 

	public static function getAccessKeyDataByKey(_DatabaseAccess $dao, $key) {
		$sql = "SELECT * FROM accesskeys WHERE `accesskey` = ? AND `disable` = 0";
		$param = array($key);

		return $dao->selectOne($sql, $param);
	}

	public static function insertAccessKeysData(_DatabaseAccess $dao, $params) {
		try {
			$dao->insert_bulk('accesskeys', $params, false);
		} catch (Exception $e) {
			LogManager::error($e->getMessage());
			return false;
		}

		return true;
	}
}