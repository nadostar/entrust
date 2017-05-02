<?php

require_once __DIR__.'/../exception/Exception_Log.php';

class LogManager {

	const LEVEL_TRACE = 1;
	const LEVEL_DEBUG = 2;
	const LEVEL_INFO = 3;
	const LEVEL_WARN = 4;
	const LEVEL_ERROR = 5;

	private static $log_setting = array();

	private static $log_output_table = array();

	private function __construct() {}

	public static function configure($log_setting) {
		self::$log_setting = $log_setting;
		foreach (self::$log_setting as $key => $setting) {
			if (isset($setting['class_file']) && isset($setting['class_name'])) {
				// クラスが指定されている場合はクラス優先
				$class_name = $setting['class_name'];
				$class_file = BASE_DIR.$setting['class_file'];

			} elseif (isset($setting['driver'])) {
				// driver毎のKVSアクセスクラス取得
				switch (strtolower($setting['driver'])) {
					case 'html':
						$class_name = 'LogOutput_HTML';
						break;

					case 'file':
						$class_name = 'LogOutput_File';
						break;

					case 'mail';
						$class_name = 'LogOutput_Mail';
						break;

					default:
						throw new Exception('not supported driver : '.$setting['driver']);
				}
				$class_file = __DIR__.'/'.$class_name.'.php';

			} else {
				throw new Exception_Log('invalid log config');
			}

			require_once $class_file;
			self::$log_output_table[$key] = new $class_name(set($setting['param']));
		}
	}

	/**
	 * トレースログ出力
	 *
	 * @param	$var	出力オブジェクト
	 * @param	$key	ログ出力オブジェクト識別子
	 */
	public static function trace($var, $key=null) {
		self::outputLog($var, 'trace', $key, self::LEVEL_TRACE);
	}

	/**
	 * デバッグログ出力
	 *
	 * @param	$var	出力オブジェクト
	 * @param	$key	ログ出力オブジェクト識別子
	 */
	public static function debug($var, $key=null) {
		self::outputLog($var, 'debug', $key, self::LEVEL_DEBUG);
	}

	/**
	 * インフォメーションログ出力
	 *
	 * @param	$var	出力オブジェクト
	 * @param	$key	ログ出力オブジェクト識別子
	 */
	public static function info($var, $key=null) {
		self::outputLog($var, 'info', $key, self::LEVEL_INFO);
	}

	/**
	 * ワーニングログ出力
	 *
	 * @param	$var	出力オブジェクト
	 * @param	$key	ログ出力オブジェクト識別子
	 */
	public static function warn($var, $key=null) {
		self::outputLog($var, 'warn', $key, self::LEVEL_WARN);
	}

	/**
	 * エラーログ出力
	 *
	 * @param	$var	出力オブジェクト
	 * @param	$key	ログ出力オブジェクト識別子
	 */
	public static function error($var, $key=null) {
		self::outputLog($var, 'error', $key, self::LEVEL_ERROR);
	}

	public static function outputLog($var, $msg, $key=null, $level=self::LEVEL_TRACE) {
		 $LogOutputArray = array();

		// キーが指定されていればそのログだけ出力
		// 出力レベルは指定出力レベルのみ、指定出力レベル以上で出力
		if (is_null($key)) {
			foreach (self::$log_output_table as $key => $LogOutput) {
				if (!isset(self::$log_setting[$key]['level']) ||
				((isset(self::$log_setting[$key]['greater']) && self::$log_setting[$key]['greater'] == true && $level >= self::$log_setting[$key]['level']) ||
				((!isset(self::$log_setting[$key]['greater']) || self::$log_setting[$key]['greater'] == false) && $level == self::$log_setting[$key]['level']))) {
					$LogOutputArray[] = $LogOutput;
				}
			}

		} else {
			if (isset(self::$log_output_table[$key])) {
				if (!isset(self::$log_setting[$key]['level']) ||
					 ((isset(self::$log_setting[$key]['greater']) && self::$log_setting[$key]['greater'] == true && $level >= self::$log_setting[$key]['level']) ||
					 ((!isset(self::$log_setting[$key]['greater']) || self::$log_setting[$key]['greater'] == false) && $level == self::$log_setting[$key]['level']))) {
					$LogOutputArray[] = self::$log_output_table[$key];
				}
			} else {
				throw new Exception('invalid log setting : '.$key);
			}
		}

		foreach ($LogOutputArray as $LogOutput) {
			$LogOutput->output($var, $msg, $level);
		}
	}

}