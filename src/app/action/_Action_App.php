<?php
/**
 * Base Action.
 *
 * @package    app.action
 * @author     xing
 */

require_once __DIR__.'/../../fw/core/view/ViewHelper.php';
require_once __DIR__.'/../../fw/core/view/Pager.php';

require_once __DIR__.'/../../fw/core/action/_Action.php';

require_once __DIR__.'/../../fw/core/util/Util_Session.php';
require_once __DIR__.'/../../fw/core/util/Util_validator.php';

require_once __DIR__.'/../output/Output_HTML_App.php';
require_once __DIR__.'/../output/Output_TWIG_App.php';

require_once __DIR__.'/../view/ViewHelper_App.php';

abstract class _Action_App extends _Action {
	protected $is_system_db = true;
	
    protected $system_db = null;
    protected $master_db = null;
    protected $slave_db = null;
    protected $log_db = null;
    
    /**
     * @see _Action::initialize()
     */
    protected  function initialize() {
        $this->trace(__METHOD__);
        Util_Session::start();
        parent::initialize();
    }

    /**
     * @see _Action::preAction()
     */
    protected function preAction() {
        parent::preAction();
        
        if($this->is_system_db) {
        	$this->system_db = $this->getDB('system');
        	$this->system_db->startTransaction();
        }
        
        
        $this->master_db = $this->getDB('master');
        $this->slave_db = $this->getDB('slave');
        $this->master_db->startTransaction();
        
        $this->log_db = $this->getDB('log');
        $this->log_db->startTransaction();
		
        //MasterDataAccess::configure($this->slave_db);
    }

    /**
     * @see _Action::preAction()
     */
    protected function postAction() {
        parent::postAction();
        if (!is_null($this->system_db)) {
            $this->system_db->commit();
        }
        
        if (!is_null($this->master_db)) {
        	$this->master_db->commit();
        }
        
        if (!is_null($this->log_db)) {
        	$this->log_db->commit();
        }

        $this->trace(__METHOD__);
    }

    /**
     * @see _Action::onError()
     */
    protected function onError(Exception $e) {
        if (!is_null($this->system_db)) {
            $this->system_db->rollback();
        }
        
        if (!is_null($this->master_db)) {
        	$this->master_db->rollback();
        }
        
        if (!is_null($this->log_db)) {
        	$this->log_db->rollback();
        }
        
        $this->error($e->__toString());
        $this->output = new Output_HTML_App();
        $this->output->setTmpl('error.php');
        $this->output->assign('message', $e->__toString());
        $this->output->output();
    }

    /**
     * curl로 HTTP 요청
     *
     * @param	$url		
     * @param	$timeout	
     */
    protected function curl_get_contents( $url, $timeout = 60 ){

        /*
         $ch = curl_init();
        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_HEADER, false );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_TIMEOUT, $timeout );
        $result = curl_exec( $ch );
        curl_close( $ch );
        */
        //windows에서 실행시 file_get_contents 수정이 필요하다.

        $result = file_get_contents($url);
        // 상태 코드 체크
        list($version, $status_code, $msg) = explode(' ',$http_response_header[0], 3);
        if (!preg_match('/^2[0-9]{2}/', $status_code)) {
            throw new Exception_Action("request failed : status_code=$status_code msg=$msg url=$url");
        }

        return $result;
    }
}
