<?php

/**
* 
*/
class Logic_Admin extends _Logic_App {

	public static function getAdminDataLimited(_DatabaseAccess $dao, $search, $limit, $offset) {
		$ret = array();
		
		$sql = "select 	a.id as admin_id,
						a.email as email,
						a.name as name,
						p.id as permission_id,
						p.name as permission_name,
						a.updated_at as updated_at
				from admin a
				inner join admin_permission ap on (ap.admin_id = a.id)
				inner join permission p on (p.id = ap.permission_id)
				WHERE 1 = 1";
		
		if(!empty($search)) {
			$sql .= sprintf(" and a.email = '%s' or a.name = '%s'", $search, $search);
		}
		
		$sql .= " limit ? offset ?";
		
		$param = array($limit, $offset);
		
		$ret['list'] = $dao->selectArrayFoundRows($sql, $param);
		$ret['count'] = $dao->getFoundRows();
		
		return $ret;
	}

	public static function verifyPassword(_DatabaseAccess $dao, $id, $password) {
		$sql = "SELECT COUNT(1) AS 'found' FROM `admin` WHERE `id` = ? AND `password` = ?";
		
		$param = array($id, $password);

		return $dao->selectOne($sql, $param);
	}

	public static function getAdminDataById(_DatabaseAccess $dao, $id) {
		$sql = "select 	a.id,
						a.email,
						a.name,
						p.id as permission_id,
						a.updated_at as updated_at
				from admin a
				inner join admin_permission ap on (ap.admin_id = a.id)
				inner join permission p on (p.id = ap.permission_id)
				WHERE a.id = ?";
		
		$param = array($id);

		return $dao->selectOne($sql, $param);
	}

	public static function login(_DatabaseAccess $dao, $email, $password) {
		$sql = "select a.id as admin_id,
				       a.email as email,
				       a.name as name,
				       p.id as permission_id,
				       p.name as permission_name 
				  from admin a
				  inner join admin_permission ap on (ap.admin_id = a.id)
				  inner join permission p on (p.id = ap.permission_id)
				  where a.email = ? and a.password = ?";

		$param = array($email, $password);
		
		return $dao->selectOne($sql, $param);
	}

	public static function insertAdminData(_DatabaseAccess $dao, $data) {
		try {
			$dao->startTransaction();

			$param = array(
				'id' => $data['id'],
				'email' => $data['email'],
				'password' => $data['password'],
				'name' => $data['name']
			);

			$dao->insert('admin', $param);

			$param = array(
				'admin_id' => $data['id'],
				'permission_id' => $data['permission_id']
			);

			$dao->insert('admin_permission', $param);

			$dao->commit();
		} catch (Exception $e) {
			LogManager::error($e->getMessage());
			$dao->rollback();
			return false;
		}

		return true;
	}

	public static function updateAdminData(_DatabaseAccess $dao, $data) {
		try {
			$dao->startTransaction();

			$param = array(
				'name' => $data['name'],
			);

			$condition = "id = ?";
			$condition_param = array($data['id']);

			$dao->update('admin', $param, $condition, $condition_param);

			$param = array(
				'admin_id' => $data['id'],
				'permission_id' => $data['permission_id']
			);
			
			$condition = "admin_id = ?";
			$condition_param = array($data['id']);
			
			$dao->update('admin_permission', $param, $condition, $condition_param);

			$dao->commit();
		} catch (Exception $e) {
			LogManager::error($e->getMessage());
			$dao->rollback();
			return false;
		}

		return true;
	}

	public static function changePermission(_DatabaseAccess $dao, $data) {
		try {

			$param = array(
				'id' => $data['permission_id']
			);

			$condition = "admin_id = ?";
			$condition_param = array($data['id']);

			$dao->update('admin_permission', $param, $condition, $condition_param);

		} catch (Exception $e) {
			LogManager::error($e->getMessage());

			return false;
		}

		return true;
	}

	public static function changePassword(_DatabaseAccess $dao, $data) {
		try {
			$param = array('password' => $data['password']);
		
			$condition = "id  = ?";
			$condition_param = array($data['id']);

			$dao->update('admin', $param, $condition, $condition_param);
		} catch (Exception $e) {
			LogManager::error($e->getMessage());
			return false;
		}

		return true;
	}
}