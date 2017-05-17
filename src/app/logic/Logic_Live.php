<?php

/**
* 
*/
class Logic_Live extends _Logic_App {
	public static function findPartnerUrls(_DatabaseAccess $dao, $id) {
		$sql = "SELECT `complate_url`, `screenout_url`, `quotafull_url` FROM `partner` WHERE `id` = ?";
		
		$params = array($id);

		return $dao->selectOne($sql, $params);
	}

	public static function findSnapshotByAccesskey(_DatabaseAccess $dao, $accesskey) {
		$sql = "SELECT * FROM `snapshot` WHERE `accesskey` = ?";

		$params=  array($accesskey);

		return $dao->selectOne($sql, $params);
	}

	public static function findSnapshotArrayById(_DatabaseAccess $dao, $project_id) {
		$sql = "SELECT * FROM `snapshot` WHERE `pid` = ?";

		$params=  array($project_id);

		return $dao->selectArray($sql, $params);
	}

	public static function findHistoryById(_DatabaseAccess $dao, $accessid) {
		$sql = "SELECT `accessid`, `url`, `progress` FROM `history` WHERE `accessid` = ?";

		$params = array($accessid);

		return $dao->selectOne($sql, $params);
	}

	public static function findStatisticsById(_DatabaseAccess $dao, $snapshotObject) {
		$sql = "SELECT `complate_count`, `screenout_count`, `quotafull_count`, 
						(SELECT SUM(`complate_count`) FROM `stat` b WHERE `b`.`pid` = `pid`) AS `complate_total`
					FROM `stat` WHERE `pid` = ? AND `link_id` = ? AND `partner_id` = ?";

		$params = array($snapshotObject['pid'], $snapshotObject['link_id'], $snapshotObject['partner_id']);

		return $dao->selectOne($sql, $params);
	}

	public static function findBlockDataByIP(_DatabaseAccess $dao, $project_id, $ip_address) {
		$sql = "SELECT COUNT(1) AS `found` FROM `block` WHERE `pid` = ? AND `ip_address` = ?";

		$params = array($project_id, $ip_address);

		return $dao->selectOne($sql, $params);
	}

	public static function findUsefulLinkById(_DatabaseAccess $dao, $id) {
		$sql = "SELECT `link_id`, `link_no`, `url`, 
					(SELECT `type` FROM `link` WHERE `id` = `link_id`) AS `type` 
				FROM `useful_link` WHERE `link_id` = ? AND `useful` = 0 LIMIT 5";

		$params = array($id);

		return $dao->selectArray($sql, $params);
	}

	public static function closeStatusOfPartner(_DatabaseAccess $dao, $id) {
		try {
			$condition = "`id` = ?";
			$condition_params = array($id);

			$dao->update('partner', array('status' => 1), $condition, $condition_params);
		} catch (Exception $e) {
			LogManager::error($e->getMessage());
			return false;
		}

		return true;
	}

	public static function closeStatusOfProject(_DatabaseAccess $dao, $id) {
		try {
			$condition = "`id` = ?";
			$condition_params = array($id);

			$dao->update('project', array('status' => 1), $condition, $condition_params);
		} catch (Exception $e) {
			LogManager::error($e->getMessage());
			return false;
		}

		return true;
	}

	public static function getHistoryCountByAccesskey(_DatabaseAccess $dao, $accesskey) {
		$sql = "SELECT COUNT(`accessid`) AS `found` FROM `history` WHERE `accesskey` = ?";

		$params = array($accesskey);

		return $dao->selectOne($sql, $params);
	}

	public static function writeHistory(_DatabaseAccess $dao, $object) {
		try {
			$dao->insert('history', $object, false);
		} catch (Exception $e) {
			LogManager::error($e->getMessage());
			return false;
		}

		return true;
	}

	public static function writeBlockOfRemoteAddr(_DatabaseAccess $dao, $object) {
		try {
			$dao->insert('block', $object, false);
		} catch (Exception $e) {
			LogManager::error($e->getMessage());
			return false;
		}

		return true;
	}

	public static function changeProgressOfLink(_DatabaseAccess $dao, $accessid) {
		try {
			$condition = "accessid = ?";
			$condition_params = array($accessid);

			$params = array('progress' => 1);

			$dao->update('history', $params, $condition, $condition_params, false);
		} catch (Exception $e) {
			LogManager::error($e->getMessage());
			return false;
		}

		return true;
	}

	public static function changeSnapshotExtra(_DatabaseAccess $dao, $accesskey, $extra) {
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

	public static function alreadyUseLink(_DatabaseAccess $dao, $link_id, $link_no) {
		try {
			$condition = "`link_id` = ? AND `link_no` = ?";
			$condition_params = array($link_id, $link_no);

			$dao->update('useful_link', array('useful' => 1), $condition, $condition_params);
		} catch (Exception $e) {
			LogManager::error($e->getMessage());
			return false;
		}

		return true;
	}

	public static function statisticsCounting(_DatabaseAccess $dao, $status, $snapshotObject) {
		try {
			$dao->startTransaction();
			$subsql = "";

			switch ($status) {
				case 'complete':
					$subsql = "`complate_count` = `complate_count` + 1";
					break;
				case 'screenout':
					$subsql = "`screenout_count` = `screenout_count` + 1";
					break;
				case 'quotafull':
					$subsql = "`quotafull_count` = `quotafull_count` + 1";
					break;
			}

			$sql = sprintf("UPDATE `stat` SET ".$subsql." WHERE `pid` = '%s' AND `link_id` = '%s' AND `partner_id` = '%s'", $snapshotObject['pid'], $snapshotObject['link_id'], $snapshotObject['partner_id']);

			$dao->sendQuery($sql);
			$dao->commit();
		} catch (Exception $e) {
			LogManager::error($e->getMessage());
			$dao->rollback();
			return false;
		}

		return true;
	}
}