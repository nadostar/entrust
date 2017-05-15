<?php

/**
* 
*/
class Logic_Project extends _Logic_App {
	
	public static function getProjectDataLimited(_DatabaseAccess $dao, $pid, $status, $sales, $limit, $offset) {
		$ret = array();

		$sql = "SELECT * FROM `project` WHERE 1 = 1";
		
		// search zone
		if(!empty($pid)) {
			$sql .= sprintf(" AND `id` = '%s'", $pid);
		}

		if(strlen($status) > 0) {
			$sql .= sprintf(" AND `status` = %d", $status);
		}

		if(!empty($sales)) {
			$sql .= sprintf(" AND `sales` = '%s'", $sales);
		}
		// search zone
		$sql.= " ORDER BY `id` DESC LIMIT ? OFFSET ?";

		$param = array($limit, $offset);
		
		$ret['list'] = $dao->selectArrayFoundRows($sql, $param);
		$ret['count'] = $dao->getFoundRows();

		return $ret;
	}

	public static function getProjectDataMap(_DatabaseAccess $dao) {
		$sql = "SELECT `id`, `name` FROM project WHERE `status` < 2";
		
		return $dao->selectArray($sql);
	}

	public static function getProjectDataById(_DatabaseAccess $dao, $id) {
		$sql = "SELECT * FROM `project` WHERE id = ?";
		$param = array($id);

		return $dao->selectOne($sql, $param);
	}

	public static function insertProjectData(_DatabaseAccess $dao, $data) {
		try {
			$dao->insert('project', $data);
		} catch (Exception $e) {
			LogManager::error($e->getMessage());
			return false;
		}

		return true;
	}

	public static function updateProjectData(_DatabaseAccess $dao, $data) {
		try {
			$condition = "id = ?";
			$condition_param = array($data['id']);

			$dao->update('project', $data, $condition, $condition_param);
		} catch (Exception $e) {
			LogManager::error($e->getMessage());
			return false;
		}

		return true;
	}

	public static function changeProjectStatus(_DatabaseAccess $dao, $id, $status) {
		try {
			$param = array(
				'status' => $status
			);

			$condition = "id = ?";
			$condition_param = array($id);

			$dao->update('project', $param, $condition, $condition_param);
		} catch (Exception $e) {
			LogManager::error($e->getMessage());
			return false;
		}

		return true;
	}
}