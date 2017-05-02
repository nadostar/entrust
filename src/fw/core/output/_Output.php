<?php

require_once __DIR__.'/../exception/Exception_Output.php';

/**
 * 出力基底クラス
 *
 * @package    fw.core.output
 */
abstract class _Output {
	/** 出力データ */
	protected $data = array();

	/**
	 * コンストラクタ
	 */
	function __construct() {
		$this->initialize();
	}

	/**
	 * 初期処理
	 */
	protected function initialize(){

	}

	/**
	 * 出力データを格納する。
	 *
	 * @param	$key	格納キー
	 * @param	$value	格納オブジェクト
	 */
	public function assign($key, $value) {
		$this->data[$key] = $value;
	}

	/**
	 * 出力データを削除する。
	 *
	 * @param	$key	格納キー
	 */
	public function remove($key) {
		if (isset($this->data[$key])) {
			unset($this->data[$key]);
		}
	}

	/**
	 * 出力データを取得する。
	 *
	 * @param	$key	格納キー
	 * @return	出力データ
	 */
	public function get($key) {
		return $this->data[$key];
	}

	/**
	 * データを出力する。<br/>
	 * 派生先で実装必須
	 */
	abstract public function output();

}