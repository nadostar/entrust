<?php
/**
 * HTML출력 클래스
 *
 * @package    fw.core.output
 */

require_once __DIR__.'/_Output.php';
require_once __DIR__.'/../exception/Exception_Output.php';

class Output_HTML extends _Output {

	/** テンプレートファイル */
	protected $tmpl = null;

	/** コールバック */
	protected $callback = null;

	/**
	 * テンプレートファイルの設定
	 *
	 * @param	$tmpl		テンプレートファイル
	 */
	public function setTmpl($tmpl) {
		$tmpl = BASE_DIR.Env::TEMPLATE_DIR.'/'.$tmpl;

		if (is_file($tmpl)) {
			$this->tmpl = $tmpl;
		} else {
			throw new Exception_Output('invalid template file : '.$tmpl);
		}
	}

	/**
	 * HTML出力
	 */
	public function output() {
		try {
			set_include_path(get_include_path() . PATH_SEPARATOR . BASE_DIR.Env::TEMPLATE_DIR );

			if (!is_null($this->tmpl)) {
				extract($this->data);

				ob_start($this->callback);

				include $this->tmpl;
				ob_end_flush();
			}
		} catch (Exception $e) {
			prt( $e );
			throw new Exception_Output('failed output html');
		}
	}


	/**
	 * コールバック関数の設定
	 *
	 * @param	$callback		コールバック関数
	 */
	public function setObCallback($callback) {
		$this->callback = $callback;
	}

	/**
	 * キャッシュデータの読込
	 *
	 * @param	$key				キー
	 * @return	true/false=読込成功/読込失敗
	 */
	public function load($key) {
		if (parent::load($key)) {
			$this->tmpl = $this->get('_tmpl');
			return true;
		}
		return false;
	}

	/**
	 * @see _Output::save()
	 */
	public function save($key) {
		$this->assign('_tmpl', $this->tmpl);
		parent::save($key);
	}

}