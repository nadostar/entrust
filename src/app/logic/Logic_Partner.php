<?php

/**
* 
*/
class Logic_Partner extends _Logic_App {
	public static function getPartnerDataLimited(_DatabaseAccess $dao, $pid, $limit, $offset) {
		$ret = array();

		$sql = "SELECT partner.*,
				(SELECT `name` FROM `link` WHERE `id` = `link_id`) AS `link_name`,
				(SELECT `name` FROM `project` WHERE `id` = `pid`) AS `project_name`,
				(SELECT COUNT(1) FROM `snapshot` WHERE `partner_id` = `id`) AS 'found',
				(SELECT COUNT(1) FROM `history` WHERE `accesskey` = (SELECT `accesskey` FROM `snapshot` WHERE `partner_id` = `id`)) AS 'hits'
				FROM `partner`
				WHERE `pid` = ?";

		$sql .= " LIMIT ? OFFSET ?";

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

	public static function getHistoryCountByAccesskey(_DatabaseAccess $dao, $accesskey) {
		$sql = "SELECT COUNT(1) AS `found` FROM `history` WHERE `accesskey` = ?";

		$param = array($accesskey);

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

	public static function changePartnerStatus(_DatabaseAccess $dao, $id, $status) {
		try {
			$param = array(
				'status' => $status
			);

			$condition = "id = ?";
			$condition_param = array($id);

			$dao->update('partner', $param, $condition, $condition_param);
		} catch (Exception $e) {
			LogManager::error($e->getMessage());
			return false;
		}

		return true;
	}
}