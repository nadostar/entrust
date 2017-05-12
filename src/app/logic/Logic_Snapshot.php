<?php

/**
* 
*/
class Logic_Snapshot extends _Logic_App {

	public static function getSnapshotDataByPartnerId(_DatabaseAccess $dao, $partner_id) {
		$sql = "SELECT * FROM `snapshot` WHERE `partner_id` = ?";
		$param = array($partner_id);

		return $dao->selectOne($sql, $param);
	}

	public static function getSnapshotDataByLinkId(_DatabaseAccess $dao, $link_id) {
		$sql = "SELECT * FROM `snapshot` WHERE `link_id` = ?";
		$param = array($link_id);

		return $dao->selectOne($sql, $param);
	}

	public static function getSnapshotDataByProjectId(_DatabaseAccess $dao, $project_id) {
		$sql = "SELECT * FROM `snapshot` WHERE `pid` = ?";
		$param = array($project_id);

		return $dao->selectOne($sql, $param);
	}

	public static function getSnapshotDataByAccesskey(_DatabaseAccess $dao, $accesskey) {
		$sql = "SELECT * FROM `snapshot` WHERE `accesskey` = ?";
		$param = array($accesskey);

		return $dao->selectOne($sql, $param);
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
	
}