<?php

/**
* 
*/
class Logic_Generate extends _Logic_App {
	static $prefix_key = array(
		'admin' => 'ES',
		'project' => 'P',
		'link' => 'L',
		'partner' => 'T'
	);
	
	public static function generateId(_DatabaseAccess $dao, $kind, $prefix = '', $key = 1000000) {
		$sql = "select count(distinct id) + 1 as id from ".$kind." limit 1";

		$data = $dao->selectOne($sql);

		if(empty($prefix)) {
			$id = self::$prefix_key[$kind].($key + $data['id']);	
		} else {
			$id = $prefix.($key + $data['id']);	
		}

		return $id;
	}
}