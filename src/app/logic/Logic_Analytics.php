<?php

/**
* 
*/
class Logic_Analytics extends _Logic_App {

	public static function getProjectData(_DatabaseAccess $dao, $limit, $offset) {
		$ret = array();

		$sql = "SELECT 	`id`,
						`name`,
						`client`,
						`country`,
						`sales`,
						`type`,
						CONCAT(`c`, '/', `sample`) AS `sample`,
						`free`,
						`disable` AS `status`,
						`c`,
						`s`,
						`q`,
						FORMAT((`c` / (`c` + `s`) * 100), 1)  AS `IR_A`,
						`ir` AS `IR_Q`,
						`cpi`,
						FORMAT((`cpi` * (`sample` + `free`)), 2) AS `final`
				  FROM `project`, 
				  		(SELECT `pid`, 
				  				SUM(`complate_count`) AS `c`, 
				  				SUM(`screenout_count`) AS `s`, 
				  				SUM(`quotafull_count`) AS `q` 
				  		   FROM `stat` GROUP BY `pid`) `stat`
				 WHERE `id` = `pid` LIMIT ? OFFSET ? ";

		$param = array($limit, $offset);

		$ret['list'] = $dao->selectArrayFoundRows($sql, $param);
		$ret['count'] = $dao->getFoundRows();

		return $ret;
	}

	public static function getPartnerData(_DatabaseAccess $dao, $id) {
		$sql = "SELECT	`id`,
						`name`,
						`country`,
						(SELECT `name` FROM `link` WHERE `id` = `partner`.`link_id`) AS `link`,
						`partner`.`status` AS `status`,
						CONCAT(`complate_count`, '/', `sample_size`) AS `sample_size`,
						CONCAT((SELECT COUNT(1) FROM `link_history` WHERE `accesskey` = (SELECT `accesskey` FROM `accesskeys` WHERE `pid` = `stat`.`pid` and `link_id` = `stat`.`link_id`)), '/', `request_limit`) AS `request_size`,
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

		$param = array($id);

		return $dao->selectArray($sql, $param);
	}

	public static function getLinkHistoryDataByAccesskey(_DatabaseAccess $dao, $pid, $link_id, $limit, $offset) {
		$ret = array();
		
		$sql = "SELECT 	`id`,
						`accesskey`,
						`progress`,
						`uid`,
						`created_at`
				  FROM `link_history`, `accesskeys`
				 WHERE `link_history`.`accesskey` = `accesskeys`.`accesskey`
				   AND `pid` = ?
				   AND `link_id` = ?
				 ORDER BY `id` DESC LIMIT ? OFFSET ?";

		$param = array($pid, $link_id, $limit, $offset);

		$ret['list'] = $dao->selectArrayFoundRows($sql, $param);
		$ret['count'] = $dao->getFoundRows();

		return $ret;
	}

	public static function getBlockDataByAccesskey(_DatabaseAccess $dao, $pid, $link_id, $limit, $offset) {
		$ret = array();

		$sql = "SELECT  `id`,
						`accesskey`,
						`ip_address`,
						`data`,
						`created_at`
				  FROM  `block`, `accesskeys`
				 WHERE  `block`.`accesskey` = `accesskeys`.`accesskey`
				   AND  `pid` = ?
				   AND  `link_id` = ?
				 ORDER BY `id` DESC LIMIT ? OFFSET ?";

		$param = array($pid, $link_id, $limit, $offset);
		
		$ret['list'] = $dao->selectArrayFoundRows($sql, $param);
		$ret['count'] = $dao->getFoundRows();

		return $ret;
	}
}