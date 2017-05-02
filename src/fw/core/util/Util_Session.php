<?php

require_once __DIR__.'/../exception/Exception_SessionTimeOut.php';

class Util_Session {

	/**
	 * セッション開始
	 */
	public static function start() {
		session_start();
	}

	/**
	 * セッションからの値取得
	 *
	 * @param	$key				キー文字列
	 * @param	$throw_exception	存在しない場合の例外throw
	 * @throws						Exception_SessionTimeOut
	 */
	public static function get($key=null, $throw_exception=true) {
		if (is_null($key)) {
			return $_SESSION;
		} else {
			if (isset($_SESSION[$key])) {
				return $_SESSION[$key];
			} else {
				if ($throw_exception) {
					throw new Exception_SessionTimeOut("session time out : key=$key");
				} else {
					return null;
				}
			}
		}
	}

	/**
	 * セッションへの値設定
	 *
	 * @param	$key	キー文字列
	 * @param	$value	値
	 */
	public static function set($key, $value) {
		$_SESSION[$key] = $value;
	}

	/**
	 * セッションからの値削除
	 *
	 * @param	$key	キー文字列
	 */
	public static function remove($key) {
		if (self::exist($key)) {
			unset($_SESSION[$key]);
		}
	}

	/**
	 * セッションデータの存在確認
	 *
	 * @param	$key	キー文字列
	 * @return	true/false=存在/存在しない
	 */
	public static function exist($key) {
		return isset($_SESSION[$key]);
	}
}
