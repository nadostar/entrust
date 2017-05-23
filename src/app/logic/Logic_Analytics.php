<?php

/**
* 
*/
class Logic_Analytics extends _Logic_App {
	public static function getMainAnalyticsData(_DatabaseAccess $dao, $limit, $offset) {
		$ret = array();

		$sql = "SELECT 	`id`,
						`name`,
						`client`,
						`country`,
						`sales`,
						`type`,
						CONCAT(`c`, '/', `sample`) AS `sample`,
						`free`,
						`status`,
						`c`,
						`s`,
						`q`,
						FORMAT((`c` / (`c` + `s`) * 100), 1)  AS `IR_A`,
						`ir` AS `IR_Q`,
						`cpi`,
						FORMAT((`cpi` * (`c` + `free`)), 2) AS `final`
				  FROM `project`, 
				  		(SELECT `pid`, 
				  				SUM(`complate_count`) AS `c`, 
				  				SUM(`screenout_count`) AS `s`, 
				  				SUM(`quotafull_count`) AS `q` 
				  		   FROM `stat` GROUP BY `pid`) `stat`
				 WHERE `id` = `pid` LIMIT ? OFFSET ? ";

		$params = array($limit, $offset);

		$ret['list'] = $dao->selectArrayFoundRows($sql, $params);
		$ret['count'] = $dao->getFoundRows();

		return $ret;
	}

	public static function getSubAnalyticsData(_DatabaseAccess $dao, $project_id) {
		$sql = "SELECT	`id`,
						`name`,
						`country`,
						(SELECT `name` FROM `link` WHERE `id` = `partner`.`link_id`) AS `link`,
						`partner`.`status` AS `status`,
						CONCAT(`complate_count`, '/', `sample_size`) AS `sample_size`,
						CONCAT((SELECT COUNT(1) FROM `history` WHERE `accesskey` = (SELECT `accesskey` FROM `snapshot` WHERE `partner_id` = `id`)), '/', `hits_limit`) AS `hits`,
						`complate_count` AS `c`,
						`screenout_count` AS `s`,
						`quotafull_count` AS `q`,
						FORMAT((complate_count / (complate_count + screenout_count) * 100), 1) AS `IR`,
						`partner`.`pid`,
						`partner`.`link_id`
				  FROM `partner`, 
				  		`stat`
				  WHERE 1 = 1 
				    AND `partner`.`pid` = `stat`.`pid`
				    AND `partner`.`link_id` = `stat`.`link_id`
				    AND `stat`.`pid` = ?";

		$params = array($project_id);

		return $dao->selectArray($sql, $params);
	}

	public static function getHistoryDataByPartnerId(_DatabaseAccess $dao, $partner_id, $limit, $offset) {
		$ret = array();
		
		$sql = "SELECT `history`.* FROM `history`, `snapshot`
				WHERE `history`.`accesskey` = `snapshot`.`accesskey`
				AND `snapshot`.`partner_id` = ? LIMIT ? OFFSET ?";

		$param = array($partner_id, $limit, $offset);

		$ret['list'] = $dao->selectArrayFoundRows($sql, $param);
		$ret['count'] = $dao->getFoundRows();

		return $ret;
	}

	public static function getBlockDataByProjectId(_DatabaseAccess $dao, $pid, $limit, $offset) {
		$ret = array();

		$sql = "SELECT  * FROM  `block` WHERE `pid` = ? ORDER BY `id` DESC LIMIT ? OFFSET ?";

		$param = array($pid, $limit, $offset);
		
		$ret['list'] = $dao->selectArrayFoundRows($sql, $param);
		$ret['count'] = $dao->getFoundRows();

		return $ret;
	}

	public static function exportDataByAccesskey(_DatabaseAccess $dao, $accesskey) {
		$sql = "SELECT `accessid` AS 'ESID', `accesskey` AS 'Partner Key', `uid` AS 'Partner UID', `url` AS 'Survey URLs', `progress` AS 'Progress', `created_at` AS 'Issue Date' FROM history WHERE `accesskey` = ?";
		$param = array($accesskey);

		return $dao->selectArray($sql, $param);
	}
}