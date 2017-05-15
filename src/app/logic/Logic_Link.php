<?php

/**
* 
*/
class Logic_Link extends _Logic_App {

	public static function getLinkDataLimited(_DatabaseAccess $dao, $pid, $limit, $offset) {
		$ret = array();

		$sql = "SELECT `link`.`id`, `link`.`name`, `link`.`type`, `link`.`updated_at`, `project`.`name` AS `project_name`, `pid`
				FROM `link` LEFT JOIN `project` ON `project`.`id` = `pid` 
				WHERE `pid` = ?";

		$sql.= " LIMIT ? OFFSET ?";

		$param = array($pid, $limit, $offset);
		
		$ret['list'] = $dao->selectArrayFoundRows($sql, $param);
		$ret['count'] = $dao->getFoundRows();

		return $ret;
	}

	public static function getLinkDataByProjectId(_DatabaseAccess $dao, $project_id) {
		$sql = "SELECT DISTINCT `id`, `name` FROM `link` WHERE `pid` = ?";
		$param = array($project_id);

		return $dao->selectArray($sql, $param);
	}
 
	public static function getLinkDataById(_DatabaseAccess $dao, $id) {
		$sql = "SELECT * FROM `link` WHERE `id` = ?";

		$param = array($id);

		return $dao->selectOne($sql, $param);
	}

	public static function insertLinkData(_DatabaseAccess $dao, $data) {
		try {
			$dao->insert('link', $data);
		} catch (Exception $e) {
			LogManager::error($e->getMessage());
			return false;
		}

		return true;
	}

	public static function updateLinkData(_DatabaseAccess $dao, $id, $data) {
		try {
			$condition = "id = ?";
			$condition_param = array($data['id']);

			$dao->update('link', $data, $condition, $condition_param);
		} catch (Exception $e) {
			LogManager::error($e->getMessage());
			return false;
		}

		return true;
	}

	public static function insertUsefulLinkData(_DatabaseAccess $dao, $data) {
		try {

			$idx = $data['link_no'];
			foreach ($data['url'] as $url) {
				$param[] = array(
					'link_id' => $data['link_id'],
					'link_no' => ++$idx,
					'url' => $url,
				);
			}

			$dao->insert_bulk('useful_link', $param);
		} catch (Exception $e) {
			LogManager::error($e->getMessage());
			return false;
		}

		return true;
	}

	public static function updateSingleUsefulLinkData(_DatabaseAccess $dao, $link_id, $link_no, $url) {
		try {

			$condition = "link_id = ? AND link_no = ?";
			$condition_param = array($link_id, $link_no);

			$dao->update('useful_link', array('url'  => $url), $condition, $condition_param);

		} catch (Exception $e) {
			LogManager::debug($e->getMessage());
			return false;
		}

		return true;
	}

	public static function findUsefulLink(_DatabaseAccess $dao, $accesskey, $link_id) {
		$sql = "SELECT `useful_link`.`link_id`, `link_no`, `url`, `link`.`type` AS `link_type` FROM `useful_link` 
				LEFT JOIN `snapshot` ON `useful_link`.`link_id` = `snapshot`.`link_id`
				LEFT JOIN `link` ON `useful_link`.`link_id` = `link`.`id`
				WHERE `useful_link`.`link_id` = ?
				AND `snapshot`.`accesskey` = ?
				AND `useful` = 0 LIMIT 5";

		$param = array($link_id, $accesskey);

		return $dao->selectArray($sql, $param);
	}

	public static function alreadyUseLink(_DatabaseAccess $dao, $link_id, $link_no) {
		try {
			$condition = "link_id = ? AND link_no = ?";
			$condition_param = array($link_id, $link_no);

			$dao->update('useful_link', array('useful' => 1), $condition, $condition_param);
		} catch (Exception $e) {
			LogManager::error($e->getMessage());
			return false;
		}

		return true;
	}

	public static function getMultiLinkDataLimited(_DatabaseAccess $dao, $link_id, $limit, $offset) {
		$ret = array();

		$sql = "SELECT * FROM `useful_link` WHERE `link_id` = ? AND `useful` = 0 LIMIT ? OFFSET ?";

		$param = array($link_id, $limit, $offset);
		
		$ret['list'] = $dao->selectArrayFoundRows($sql, $param);
		$ret['count'] = $dao->getFoundRows();

		return $ret;
	}

	public static function getSingleLinkData(_DatabaseAccess $dao, $link_id) {
		$sql = "SELECT * FROM `useful_link` WHERE `link_id` = ?";
		
		$param = array($link_id);

		return $dao->selectOne($sql, $param);
	}

	public static function getUsefulLinkMaxNo(_DatabaseAccess $dao, $link_id) {
		$sql = "SELECT MAX(`link_no`) AS `no` FROM `useful_link` WHERE `link_id` = ?";

		$param = array($link_id);

		return $dao->selectOne($sql, $param);
	}
}