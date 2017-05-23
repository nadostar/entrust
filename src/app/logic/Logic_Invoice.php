<?php

/**
* 
*/
class Logic_Invoice extends _Logic_App {
	public static function insertInvoice(_DatabaseAccess $dao, $keys, $values) {
		$insert_param = array_combine($keys, $values);
		$update_param = array_combine($keys, $values);
		
		try {
			$dao->insertOnDuplicateKeyUpdate('invoice', $insert_param, $update_param);
		} catch (Exception $e) {
			LogManager::error($e->getMessage());
			return false;
		}
		
		return true;
	}

	public static function getInvoiceDataByPartnerId(_DatabaseAccess $dao, $tid) {
		$sql = "SELECT * FROM `invoice` WHERE `tid` = ?";

		$param = array($tid);

		return $dao->selectOne($sql, $param);
	}
}