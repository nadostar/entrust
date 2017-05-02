<?php

class Util_validator {

	/**
	 * 数値チェック
	 *
	 * @param	$val	チェック対象
	 * @return	true/false=OK/NG
	 */
	static public function checkNumber($val) {
		$ret = false;
		if (!isset($val)) return $ret;
		if (preg_match('/^[0-9]+$/', (string)$val)) {
			$ret = true;
		}
		return $ret;
	}

	/**
	 * 数値チェック(マイナスも考慮する)
	 *
	 * @param	$val	チェック対象
	 * @return	true/false=OK/NG
	 */
	static public function checkNumberWithMinus($val) {
		$ret = false;
		if (preg_match('/^\-*[0-9]+$/', (string)$val)) {
			$ret = true;
		}
		return $ret;
	}


	/**
	 * 数値範囲チェック
	 *
	 * @param	$val	チェック対象
	 * @param	$min	最小値
	 * @param	$max	最大値
	 * @return	true/false=OK/NG
	 */
	static public function checkNumberRange($val, $min, $max) {
		$ret = false;
		if (self::checkNumber($val) && $min<=$val && $val<=$max) {
			$ret = true;
		}
		return $ret;
	}

	/**
	 * 文字列長チェック（文字列長）
	 *
	 * @param	$val	チェック対象
	 * @param	$min	最小値
	 * @param	$max	最大値
	 * @param	$encode	エンコード
	 * @return	true/false=OK/NG
	 */
	static public function checkStrWidth($val, $min, $max, $encode) {
		$ret = false;
		$len = mb_strwidth($val, $encode);
		if ($len >= $min && $len <= $max) {
			$ret = true;
		}
		return $ret;
	}


	/**
	 * 文字列長チェック（バイト）
	 *
	 * @param	$val	チェック対象
	 * @param	$min	最小値
	 * @param	$max	最大値
	 * @return	true/false=OK/NG
	 */
	static public function checkStrByte($val, $min, $max) {
		$ret = false;
		$len = strlen($val);
		if ($len >= $min && $len <= $max) {
			$ret = true;
		}
		return $ret;
	}

	/**
	 * 文字列長チェック（マルチバイト対応文字数）
	 *
	 * @param	$val	チェック対象
	 * @param	$min	最小値
	 * @param	$max	最大値
	 * @param	$encode	エンコード
	 * @return	true/false=OK/NG
	 */
	static public function checkMbStrlen($val, $min, $max, $encode) {
		$ret = false;
		$len = mb_strlen($val, $encode);
		if ($len >= $min && $len <= $max) {
			$ret = true;
		}
		return $ret;
	}

	/**
	 * 正規表現チェック
	 *
	 * @param	$val	チェック対象
	 * @param	$ptn	正規表現
	 * @return	true/false=OK/NG
	 */
	static public function checkRegex($val, $ptn) {
		$ret = false;
		if ($val !== "" && preg_match($ptn, $val)) {
			$ret = true;
		}
		return $ret;
	}

	/**
	 * 配列要素チェック
	 *
	 * @param	$val	チェック対象
	 * @param	$ary	配列
	 * @return	true/false=有/無
	 */
	static public function checkArrayVal($val, $ary) {
		return in_array($val, $ary);
	}

	/**
	 * 入力有無チェック
	 *
	 * @param	$val	チェック対象
	 * @return	true/false=有/無
	 */
	static public function checkInput($val) {
		$ret = false;
		if (isset($val) && !is_null($val) && $val !== "") {
			$ret = true;
		}
		return $ret;
	}

	/**
	 * 文字列のエンコードをチェックする。
	 *
	 * @param	$str	チェック対象
	 * @param	$encode	エンコードタイプ
	 */
	static public function checkEncoding($str, $encode) {
		$ret = true;
		if (!mb_check_encoding($str, $encode)) {
			$ret = false;
		}
		return $ret;
	}

}
