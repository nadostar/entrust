<?php

abstract class _LogOutput {

	/** ログ出力設定 */
	protected $setting;

	/**
	 * コンストラクタ
	 *
	 * @param	$setting	ログ出力設定
	 */
	function __construct($setting) {
		$this->setting = $setting;
	}

	/**
	 * ログ出力
	 *
	 * @param	$var	出力オブジェクト
	 * @param	$msg	メッセージ
	 * @param	$level	出力レベル
	 */
	public abstract function output($var, $msg, $level);

}