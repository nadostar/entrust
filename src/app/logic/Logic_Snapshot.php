<?php

/**
* 
*/
class Logic_Snapshot extends _Logic_App {

	public static function getSnapshotDataByPartnerId(_DatabaseAccess $dao, $partner_id) {
		$sql = "SELECT * FROM `snapshot` WHERE `partner_id` = ?";
		$params = array($partner_id);

		return $dao->selectOne($sql, $params);
	}

	public static function getSnapshotDataByLinkId(_DatabaseAccess $dao, $link_id) {
		$sql = "SELECT * FROM `snapshot` WHERE `link_id` = ?";
		$params = array($link_id);

		return $dao->selectOne($sql, $params);
	}

	public static function getSnapshotDataByProjectId(_DatabaseAccess $dao, $project_id) {
		$sql = "SELECT * FROM `snapshot` WHERE `pid` = ?";
		$params = array($project_id);

		return $dao->selectArray($sql, $params);
	}

	public static function getSnapshotDataByAccesskey(_DatabaseAccess $dao, $accesskey) {
		$sql = "SELECT * FROM `snapshot` WHERE `accesskey` = ?";
		$params = array($accesskey);

		return $dao->selectOne($sql, $params);
	}

	public static function insertSnapshotData(_DatabaseAccess $dao, $data) {
		try {
			$dao->insert('snapshot', $data, false);
		} catch (Exception $e) {
			LogManager::error($e->getMessage());

			return false;
		}

		return true;
	}

	public static function updateSnapshotExtra(_DatabaseAccess $dao, $accesskey, $extra) {
		try {
			$condition = "`accesskey` = ?";
			$condition_params = array($accesskey);

			$dao->update('snapshot', array('extra' => json_encode($extra)), $condition, $condition_params, false);
		} catch (Exception $e) {
			LogManager::error($e->getMessage());
			return false;
		}

		return true;
	}
}