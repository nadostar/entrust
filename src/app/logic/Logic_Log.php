<?php

/**
* 
*/
class Logic_Log extends _Logic_App {
	
	public static function getAdminLogDataLimited(_DatabaseAccess $dao, $limit, $offset) {
		$ret = array();

		$sql = "SELECT * FROM `admin_log` ORDER BY `id` DESC LIMIT ? OFFSET ?";


		$param = array($limit, $offset);

		$ret['list'] = $dao->selectArrayFoundRows($sql, $param);
		$ret['count'] = $dao->getFoundRows();

		return $ret;
	}

	public static function getAdminLogDataById(_DatabaseAccess $dao, $id) {
		$sql = "SELECT * FROM `admin_log` WHERE `id` = ?";

		$param = array($id);

		return $dao->selectOne($sql, $param);
	}

	public static function getAccessLogDataLimited(_DatabaseAccess $dao, $limit, $offset) {
		$ret = array();

		$sql = "SELECT * FROM `access_log` ORDER BY `id` DESC LIMIT ? OFFSET ?";


		$param = array($limit, $offset);

		$ret['list'] = $dao->selectArrayFoundRows($sql, $param);
		$ret['count'] = $dao->getFoundRows();

		return $ret;
	}

	public static function getAccessLogDataById(_DatabaseAccess $dao, $id) {
		$sql = "SELECT * FROM `access_log` WHERE `id` = ?";

		$param = array($id);

		return $dao->selectOne($sql, $param);
	}

	public static function getErrorLogDataLimited(_DatabaseAccess $dao, $limit, $offset) {
		$ret = array();

		$sql = "SELECT * FROM `error_report` ORDER BY `id` DESC LIMIT ? OFFSET ?";


		$param = array($limit, $offset);

		$ret['list'] = $dao->selectArrayFoundRows($sql, $param);
		$ret['count'] = $dao->getFoundRows();

		return $ret;
	}

	public static function getErrorLogDataById(_DatabaseAccess $dao, $id) {
		$sql = "SELECT * FROM `error_report` WHERE `id` = ?";

		$param = array($id);

		return $dao->selectOne($sql, $param);
	}

	public static function getBlockLogDataLimited(_DatabaseAccess $dao) {

	}


	public static function getBlockLogDataById(_DatabaseAccess $dao) {

	} 
	
	public static function adminlog(_DatabaseAccess $dao, $admin_id, $category, $data, $ip_address) {
		try {
			$params = array(
				'day' => date('d'),
				'admin_id' => $admin_id,
				'category' => $category,
				'data' => empty($data) ? '' : json_encode($data),
				'ip_address' => $ip_address
			);

			$dao->insert('admin_log', $params, false);
		} catch (Exception $e) {
			LogManager::error($e->getMessage());
			return false;
		}

		return true;
	}

	public static function accesslog(_DatabaseAccess $dao, $accesskey, $kind, $data, $ip_address) {
		try {
			$dao->startTransaction();
			
			$params = array(
				'day' => date('d'),
				'accesskey' => $accesskey,
				'kind' => $kind,
				'data' => empty($data) ? '' : json_encode($data),
				'ip_address' => $ip_address
			);
			LogManager::debug($params);
			$dao->insert('access_log', $params, false);
			$dao->commit();
		} catch (Exception $e) {
			LogManager::error($e->getMessage());
			$dao->rollback();
			return false;
		}

		return true;
	}

	public static function errorlog(_DatabaseAccess $dao, $params) {
		try {
			$dao->startTransaction();

			$dao->insert('error_report', $params, false);
			$dao->commit();
		} catch (Exception $e) {
			LogManager::error($e->getMessage());
			$dao->rollback();
			return false;
		}

		return true;
	}
}