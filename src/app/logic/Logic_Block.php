<?php

/**
* 
*/
class Logic_Block extends _Logic_App {
	public static function findBlockDataByIp(_DatabaseAccess $dao, $pid, $ip) {
		$sql = "SELECT COUNT(1) AS `found` FROM `block` WHERE `pid` = ? AND `ip_address` = ?";

		$param = array($pid, $ip);

		return $dao->selectOne($sql, $param);
	}

	public static function insertBlockData(_DatabaseAccess $dao, $params) {
		try {
			$dao->insert('block', $params, false);
		} catch (Exception $e) {
			LogManager::error($e->getMessage());
			return false;
		}

		return true;
	} 
}