<?php

/**
* 
*/
class Logic_Link extends _Logic_App {

	public static function getLinkDataLimited(_DatabaseAccess $dao, $pid, $limit, $offset) {
		$ret = array();

		$sql = "SELECT `link`.`id`, `link`.`name`, `link`.`type`, `link`.`updated_at`, `link`.`pid`, `p`.`name` AS `project_name`,
  				(SELECT COUNT(1) FROM `useful_link` WHERE link_id = `link`.`id`) AS `urls`,
  				(SELECT COUNT(1) from `useful_link` WHERE `link_id` = `link`.`id` AND `useful` = 1) AS `used_urls`
  				FROM `link`, `project` `p`
 				WHERE 1 = 1
   				AND `link`.`pid` = `p`.`id`
   				AND `p`.`id` = ?";

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