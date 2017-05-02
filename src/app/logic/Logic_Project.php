<?php

/**
* 
*/
class Logic_Project extends _Logic_App {
	
	public static function getProjectDataLimited(_DatabaseAccess $dao, $search, $limit, $offset) {
		$ret = array();

		$sql = "select * from `project` where 1 = 1";

		$sql.= " limit ? offset ?";

		$param = array($limit, $offset);
		
		$ret['list'] = $dao->selectArrayFoundRows($sql, $param);
		$ret['count'] = $dao->getFoundRows();

		return $ret;
	}

	public static function getProjectDataMap(_DatabaseAccess $dao) {
		$sql = "select `id`, `name` from project where `disable` < 2";
		
		return $dao->selectArray($sql);
	}

	public static function getProjectDataById(_DatabaseAccess $dao, $id) {
		$sql = "select * from `project` where id = ?";
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
				'disable' => $status
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