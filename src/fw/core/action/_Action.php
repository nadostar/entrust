<?php

abstract class _Action {

    protected $args = null;
    protected $output = null;
    protected $redirect_url = null;
    protected $current_url = null;
    protected $validator_map = array();
    protected $skip_do_action = false;

    /**
     * コンストラクタ
     */
    function __construct() {
        $this->setCurrentURL();
        $this->initialize();
    }

    protected function initialize(){}

    /**
     * 검증 설정
     *
     * @param	$name		パラメータ名
     * @param	$validator	Validatorクラス
     * @param	$message	メッセージ
     * @param	$param		Validatorパラメータ
     */
    protected function registValidatorMap($name, $validator=null, $message=null, $param=null) {
        $this->validator_map[$name][] = array('class' => $validator, 'message' => $message, 'param' => $param);
    }

    public function doAct() {
        try {
            $this->validParam();

            // 前処理
            $this->preAction();

            // preActionでリダイレクトが指定された場合、以降の処理はスキップ
            // 実処理
            if (is_null($this->redirect_url)) {
                if ($this->skip_do_action === false) {
                    $this->doAction();
                }

                // 後処理
                $this->postAction();

                if (!is_null($this->output)) {
                    $this->output->output();
                }
            }

            if (!is_null($this->redirect_url)) {
                $this->redirect();
            }

        } catch (Exception $e) {
            $this->onError($e);
        }
    }

    /**
     * 매개 변수의 검증 및 취득
     */
    protected function validParam() {
        $tmp_args = array_merge($_GET, $_POST);
        // 全てチェックしてから例外をthrowする。
        $exception_array = array();
        foreach ($this->validator_map as $name => $valid_array) {

            $value = set( $tmp_args[$name] );

            foreach ($valid_array as $valid_setting) {
                $class = $valid_setting['class'];
                if (!is_null($class)) {

                    require_once __DIR__.'/../validator/'.$class.'.php';
                    
                    $validator = new $class();

                    try {
                        $validator->valid($value, $valid_setting['param'], $valid_setting['message']);
                    } catch (Exception_Validation $e) {
                        $exception_array[] = $e;
                    }
                }
            }
            $this->args[$name] = $value;
        }
        if (count($exception_array) > 0) {
            throw $exception_array[0];
        }
    }

    /**
     * 데이터베이스 액세스 클래스의 취득
     */
    protected function getDB($key) {
        return DatabaseManager::getDB($key);
    }

    /**
     * trace log.
     */
    protected function trace($var, $key=null) {
        LogManager::trace($var, $key);
    }

    /**
     * 디버깅 로그
     */
    protected function debug($var, $key=null) {
        LogManager::debug($var, $key);
    }

    /**
     * info log
     */
    protected function info($var, $key=null) {
        LogManager::info($var, $key);
    }

    protected function warn($var, $key=null) {
        LogManager::warn($var, $key);
    }

    /**
     * 에러 로그
     */
    protected function error($var, $key=null) {
        LogManager::error($var, $key);
    }

    /**
     * 로그 남기기
     */
    protected function log($var, $msg, $key=null) {
        LogManager::outputLog($var, $msg, $key);
    }

    /**
     * 선처리 액션
     */
    protected function preAction() {}

    /**
     * 액션 실행
     */
    protected function doAction() {}

    /**
     * 후처리 액션
     */
    protected function postAction() {}

    /**
     * 예외 처리
     */
    protected function onError(Exception $e) {
        throw $e;
    }

    protected function getQuery($key) {
        return set($this->args[$key]);
    }

    /**
     * 리디렉션 처리
     */
    protected function redirect() {
        header('location:'.$this->redirect_url );
    }

    protected function getQueryListByStartStr($str, $replace=true) {
        $ret = array();
        foreach ($this->args as $key => $value) {
            if (substr($key, 0, strlen($str)) == $str) {
                if ($replace) {
                    $ret[str_replace($str, '', $key)] = $value;
                } else {
                    $ret[$key] = $value;
                }
            }
        }
        return $ret;
    }

    private static function getTemplateDir() {
        return BASE_DIR.'tmpl/';
    }

    protected function setCurrentURL() {
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
            $protocol = 'https://';
        } else {
            $protocol = 'http://';
        }
        $this->current_url = $protocol.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    }
}
