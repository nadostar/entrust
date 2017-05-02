<?php

/**
* 
*/
class Logic_LinkHistory extends _Logic_App {

	public static function getLinkHistoryById(_DatabaseAccess $dao, $accesskey, $uid) {
		$sql = "SELECT `id`, `progress` FROM `link_history` WHERE `accesskey` = ? AND `uid` = ?";
		$param = array($accesskey, $uid);

		return $dao->selectOne($sql, $param);
	}

	public static function getLinkHistoryByAccessKey(_DatabaseAccess $dao, $accesskey) {
		$sql = "SELECT count(`id`) AS `joinin` FROM `link_history` WHERE `accesskey` = ?";
		$param = array($accesskey);

		return $dao->selectOne($sql, $param);
	}

	public static function insertLinkHistoryData(_DatabaseAccess $dao, $data) {
		try {
			$dao->startTransaction();

			$dao->insert('link_history', $data, false);

			$dao->commit();
		} catch (Exception $e) {
			LogManager::error($e->getMessage());
			$dao->rollback();
			return false;
		}

		return true;
	}

	public static function updateLinkHistoryByProgress(_DatabaseAccess $dao, $id, $progress) {
		try {
			$dao->startTransaction();

			$param = array(
				'progress' => $progress
			);

			$condition = "id = ?";
			$condition_param = array($id);

			$dao->update('link_history', $param, $condition, $condition_param, false);
			$dao->commit();
		} catch (Exception $e) {
			LogManager::error($e->getMessage());
			$dao->rollback();
			return false;
		}

		return true;
	}
}