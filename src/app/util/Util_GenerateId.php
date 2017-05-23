<?php

class Util_GenerateId {
	public static function generateId($length) {
		$key = '';
		$pattern = '1234567890abcdefghijklmnopqrstuvwxyz';
		for($i = 0; $i < $length; $i ++) {
			$key .= $pattern {mt_rand ( 0, 35 )};
		}
		return $key;
	}
	
	public static function generateKey($id) {
		return crypt($id, md5(time() . self::generateId(5)));
	}

	public static function getMillisecond() {
		list($t1, $t2) = explode(' ', microtime());
		return (float)sprintf('%.0f',(floatval($t1)+floatval($t2))*1000);
	}
}