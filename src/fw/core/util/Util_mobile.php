<?php

if ( !defined('CARRIER_IMODE')) define('CARRIER_IMODE', 1);
if ( !defined('CARRIER_DOCOMO')) define('CARRIER_DOCOMO', CARRIER_IMODE);

if ( !defined('CARRIER_EZWEB')) define('CARRIER_EZWEB', 2);
if ( !defined('CARRIER_AU')) define('CARRIER_AU', CARRIER_EZWEB);

if ( !defined('CARRIER_SB')) define('CARRIER_SB', 3);
if ( !defined('CARRIER_VODA')) define('CARRIER_VODA', CARRIER_SB);
if ( !defined('CARRIER_JPHONE')) define('CARRIER_JPHONE', CARRIER_SB);

if ( !defined('CARRIER_CRAWLER')) define('CARRIER_CRAWLER', 50);


if ( !defined('CARRIER_DEBUG')) define('CARRIER_DEBUG', 4);

if ( !defined('CARRIER_IPHONE')) define('CARRIER_IPHONE', 10);
if ( !defined('CARRIER_ANDROID')) define('CARRIER_ANDROID', 20);

if ( !defined('CARRIER_PC')) define('CARRIER_PC', 0);

class Util_mobile {
	private static $is_feature_phone = NULL;
	private static $is_smart_phone = NULL;

	private static $is_iphone = NULL;
	private static $is_android = NULL;

	private static $is_PC = NULL;

	private static $carrier = NULL;

	/**
	 *
	 * キャリア判別
	 */
	private static function ua_checker(){
		$carrier = self::getCarrier();

		switch($carrier){
			case CARRIER_IMODE:
			case CARRIER_EZWEB:
			case CARRIER_SB:
				self::$carrier = $carrier;
				self::$is_feature_phone = TRUE;
				self::$is_smart_phone = FALSE;
				self::$is_iphone = FALSE;
				self::$is_android = FALSE;
				self::$is_PC = FALSE;
				break;

			case CARRIER_ANDROID:
				self::$carrier = $carrier;
				self::$is_feature_phone = FALSE;
				self::$is_smart_phone = TRUE;
				self::$is_iphone = FALSE;
				self::$is_android = TRUE;
				self::$is_PC = FALSE;
				break;

			case CARRIER_IPHONE:
				self::$carrier = $carrier;
				self::$is_feature_phone = FALSE;
				self::$is_smart_phone = TRUE;
				self::$is_iphone = TRUE;
				self::$is_android = FALSE;
				self::$is_PC = FALSE;
				break;

			default:
				self::$carrier = $carrier;
				self::$is_feature_phone = FALSE;
				self::$is_smart_phone = FALSE;
				self::$is_iphone = FALSE;
				self::$is_android = FALSE;
				self::$is_PC = TRUE;
				break;
		}
	}

	/**
	 * スマホかどうかの判定
	 *
	 * @return true/false=スマホ/スマホでない
	 */
	public static function is_smart_phone(){
		if ( self::$is_smart_phone === NULL ){
			self::ua_checker();
		}
		return self::$is_smart_phone;
	}

	/**
	 * フィーチャーフォンかどうかの判定
	 *
	 * @return true/false=フィーチャーフォン/フィーチャーフォンでない
	 */
	public static function is_feature_phone(){
		if ( self::$is_feature_phone === NULL ){
			self::ua_checker();
		}
		return self::$is_feature_phone;
	}

	/**
	 * iOSかどうかの判定
	 *
	 * @return true/false=iOS/iOSでない
	 */
	public static function is_iphone(){
		if (  self::$is_iphone === NULL ){
			self::ua_checker();
		}
		return self::$is_iphone ;
	}

	/**
	 * Androidかどうかの判定
	 *
	 * @return true/false=Android/Androidでない
	 */
	public static function is_android(){
		if (  self::$is_android === NULL ){
			self::ua_checker();
		}
		return self::$is_android ;
	}

	/**
	 * PCかどうかの判定
	 *
	 * @return true/false=PC/PCでない
	 */
	public static function is_PC(){
		if (  self::$is_PC === NULL ){
			self::ua_checker();
		}
		return self::$is_PC ;
	}

	/**
	 * キャリア・スマホの判断
	 *
	 * @return	キャリア
	 */
	static public function getCarrier(){
		static $carrier = NULL; //判断結果を保持する

		if ( $carrier !== NULL ){
			return $carrier;
		}

		$ret = CARRIER_PC;
		if (!isset($_SERVER['HTTP_USER_AGENT'])){
			return $ret;
		}

		$userAgent = $_SERVER['HTTP_USER_AGENT'];
		if (preg_match('/^DoCoMo/', $userAgent)) {
			$ret = CARRIER_IMODE;
		}
		else if (preg_match("/^KDDI\-/i", $userAgent) || preg_match("/UP\.Browser/i", $userAgent)) {
			$ret = CARRIER_EZWEB;
		}
		else if (preg_match("/^(J\-PHONE|Vodafone|SoftBank|MOT\-[CV])/i", $userAgent)) {
			$ret = CARRIER_SB;
		}
		else if (preg_match('#^Mozilla/5\.0 \((?:iPhone|iPod|iPad);#', $userAgent)) {
			$ret = CARRIER_IPHONE;
		}
		else if (strpos($userAgent, 'Android') !== FALSE) {
		 	$ret = CARRIER_ANDROID;
		}
		else {
			$ret = CARRIER_PC;
		}

		return $ret;

	}

}