<?php

/**
* 
*/
class Logic_Permission extends _Logic_App {
	
	public static function getPermissionDataLimited(_DatabaseAccess $dao, $search, $limit, $offset) {
		$ret = array();
		
		$sql = "select `id`, `name`, `description`, `updated_at` from `permission` where 1 = 1 ";

		if(!empty($search)) {
			$sql .= sprintf(" and `name` = '%s'", $search);
		}

		$sql .= " limit ? offset ?";

		$param = array($limit, $offset);
		
		$ret['list'] = $dao->selectArrayFoundRows($sql, $param);
		$ret['count'] = $dao->getFoundRows();

		return $ret;
	}
	
	public static function getPermissionDataArray(_DatabaseAccess $dao) {
		$sql = "select `id`, `name` from `permission`";

		return $dao->selectArray($sql);
	}

	public static function getPermissionDataById(_DatabaseAccess $dao, $id) {
		$sql = "select `id`, `name`, `description` from `permission` where `id` = ?";

		$param = array($id);

		return $dao->selectOne($sql, $param);
	}

	public static function getAlreadyAllowMap(_DatabaseAccess $dao, $id) {
		$sql = "select `id`, `allow_no`, `roles` from `permission_allow` where `permission_id` = ?";

		$param = array($id);

		return $dao->selectArray($sql, $param);
	}

	public static function insertPermissionData(_DatabaseAccess $dao, $data) {
		try {
			$dao->startTransaction();

			$param = array(
				'name' => $data['name'],
				'description' => $data['description']
			);

			$dao->insert('permission', $param);

			$permission_id = $dao->getLastInsertID();

			$param = array();
			foreach ($data['allow_no'] as $idx => $no) {
				$param[] = array(
					'permission_id' => $permission_id,
					'allow_no' => $no,
					'roles' => $data['roles'][$no]
				);
			}

			$dao->insert_bulk('permission_allow', $param);

			$dao->commit();
		} catch (Exception $e) {
			LogManager::error($e->getMessage());
			$dao->rollback();
			return false;
		}

		return true;
	}

	public static function updatePermissionData(_DatabaseAccess $dao, $data) {
		try {
			$dao->startTransaction();

			// change permission table
			$param = array(
				'name' => $data['name'],
				'description' => $data['description']
			);
			$condition = "id = ?";
			$condition_param = array($data['id']);

			$dao->update('permission', $param, $condition, $condition_param);
			
			// drop prev permission_allow data
			$condition = "permission_id = ?";
			$param = array($data['id']);
			$dao->delete('permission_allow', $condition, $param);

			// create new permission_allow data
			$param = array();
			foreach ($data['allow_no'] as $idx => $no) {
				$param[] = array(
					'permission_id' => $data['id'],
					'allow_no' => $no,
					'roles' => $data['roles'][$no]
				);
			}

			$dao->insert_bulk('permission_allow', $param);

			$dao->commit();
		} catch (Exception $e) {
			LogManager::error($e->getMessage());
			$dao->rollback();
			return false;
		}

		return true;
	}
}