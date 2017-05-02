<?php

/**
* 
*/
class Logic_Stat extends _Logic_App {

	public static function getStatDataByPid(_DatabaseAccess $dao, $pid) {
		
	}

	public static function getStatData(_DatabaseAccess $dao) {
		
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

			$sql = sprintf("UPDATE stat SET ".$update_sql." WHERE `pid` = '%s' AND `link_id` = '%s'", $data['pid'], $data['link_id']);

			$dao->sendQuery($sql);

		} catch (Exception $e) {
			LogManager::error($e->getMessage());
			return false;
		}

		return true;
	}
}