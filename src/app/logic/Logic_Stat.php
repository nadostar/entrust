<?php

/**
* 
*/
class Logic_Stat extends _Logic_App {

	public static function getStatDataByProjectId(_DatabaseAccess $dao, $pid) {
		$ret = null;

		$sql = "SELECT `pid`, SUM(`complate_count`) AS c, SUM(`screenout_count`) AS s, SUM(`quotafull_count`) AS q FROM `stat` WHERE `pid` = ?";
		
		$param = array($pid);
		$ret = $dao->selectOne($sql, $param);

		return $ret;
	}

	public static function getStatDataByIds(_DatabaseAccess $dao, $snapshot) {
		$sql = "SELECT * FROM `stat` WHERE `pid` = ? AND `link_id` = ? AND `partner_id` = ?";

		$param = array($snapshot['pid'], $snapshot['link_id'], $snapshot['partner_id']);

		return $dao->selectOne($sql, $param);
	}

	public static function insertStatData(_DatabaseAccess $dao, $data) {
		try {
			$dao->insert("stat", $data, false);
		} catch (Exception $e) {
			LogManager::error($e->getMessage());
			return false;
		}

		return true;
	}

	public static function recordStatData(_DatabaseAccess $dao, $kind, $data) {
		try {
			$dao->startTransaction();

			$update_sql = "";

			switch ($kind) {
				case 'c':
					$update_sql = "`complate_count` = `complate_count` + 1";
					break;
				case 's':
					$update_sql = "`screenout_count` = `screenout_count` + 1";
					break;
				case 'q':
					$update_sql = "`quotafull_count` = `quotafull_count` + 1";
					break;
			}

			$sql = sprintf("UPDATE stat SET ".$update_sql." WHERE `pid` = '%s' AND `link_id` = '%s' AND `partner_id` = '%s'", $data['pid'], $data['link_id'], $data['partner_id']);

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