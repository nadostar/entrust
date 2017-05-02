<?php
/**
 * JS演出出力クラス
 *
 * @package    fw.core.output
 * @author     Xing
 */

require_once __DIR__ . '/Output_HTML.php';

require_once __DIR__ . '/../exception/Exception_Output.php';

/**
 * JS演出出力クラス
 *
 * @package    fw.core.output
 */
class Output_JS extends Output_HTML
{
    /** Effect_JS */
    protected $Effect = null;
    /** js展開文字列 */
    protected $js_extract_str = null;

    /**
     * Effect_JSクラスの設定
     *
     * @param Effect_JS $Effect Effect_JSクラス
     */
    public function setEffect(_Effect_JS $Effect)
    {
        $this->Effect = $Effect;
    }

    /**
     * js展開文字列の設定
     *
     * @param   $str    js展開文字列
     */
    public function setJSExtractStr($str)
    {
        $this->js_extract_str = $str;
    }

    /**
     * JS演出出力
     */
    public function output()
    {
        $this->Effect->generate();
        $this->assign($this->js_extract_str, $this->Effect->output(false));
        parent::output();
    }
}
