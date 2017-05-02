<?php

/**
* 
*/
class Logic_Partner extends _Logic_App {
	public static function getPartnerDataLimited(_DatabaseAccess $dao, $pid, $limit, $offset) {
		$ret = array();

		$sql = "select 	`id`, 
						`name`, 
						`country`, 
						`complate_url`, 
						`screenout_url`, 
						`quotafull_url`, 
						`status`, 
						`sample_size`, 
						`request_limit`, 
						`updated_at`, 
						`pid`, 
						`link_id`,
						(select distinct `name` from `link` where `id` = `link_id`) AS link_name,
						(select `name` from `project` where `id` = `pid`) AS project_name
  				 from `partner` where `pid` = ?";

		$sql .= " limit ? offset ?";

		$param = array($pid, $limit, $offset);

		$ret['list'] = $dao->selectArrayFoundRows($sql, $param);
		$ret['count'] = $dao->getFoundRows();
		return $ret;
	}

	public static function getPartnerDataById(_DatabaseAccess $dao, $id) {
		$sql = "SELECT * FROM `partner` WHERE `id` = ?";
		$param = array($id);

		return $dao->selectOne($sql, $param);
	}

	public static function insertPartnerData(_DatabaseAccess $dao, $data) {
		try {
			$dao->insert('partner', $data);
		} catch (Exception $e) {
			LogManager::error($e->getMessage());
			return false;
		}

		return true;
	}

	public static function updatePartnerData(_DatabaseAccess $dao, $data) {
		try {
			$condition = "id = ?";
			$condition_param = array($data['id']);

			$dao->update('partner', $data, $condition, $condition_param);
		} catch (Exception $e) {
			LogManager::error($e->getMessage());
			return false;
		}

		return true;
	}
}