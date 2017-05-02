<?php
/**
 * SWF出力クラス
 *
 * @package    fw.core.output
 * @author     Xing
 */

require_once __DIR__ . '/_Output.php';

require_once __DIR__ . '/../exception/Exception_Output.php';

/**
 * SWF出力クラス
 *
 * @package    fw.core.output
 */
class Output_SWF extends _Output
{
    /** Effect_SWF */
    protected $Effect = null;

    /**
     * Effect_SWFクラスの設定
     *
     * @param _Effect_SWF $effect Effect_SWFクラス
     */
    public function setEffect(_Effect_SWF $Effect)
    {
        $this->Effect = $Effect;
    }

    /**
     * SWF出力
     */
    public function output()
    {
        $this->Effect->generate();

        header('Content-type: application/x-shockwave-flash');
        $this->Effect->output();
    }
}
