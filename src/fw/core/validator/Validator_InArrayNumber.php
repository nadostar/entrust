<?php

require_once __DIR__.'/_Validator.php';

class Validator_InArrayNumber extends _Validator {
	/**
	 * @see _Validator::valid()
	 */
	public function valid($value, $param=null, $message='') {
		if (is_null($value) || $value=='') return;
		if (!is_array($value)) throw new Exception_Validation($message);
		foreach ($value as $val) {
			if (!Util_validator::checkNumberWithMinus($val)) throw new Exception_Validation($message);
		}
	}
}