<?php

require_once __DIR__.'/../../fw/core/view/ViewHelper.php';
require_once __DIR__.'/../../fw/core/view/Pager.php';

require_once __DIR__.'/../../fw/core/action/_Action.php';

require_once __DIR__.'/../../fw/core/util/Util_Session.php';
require_once __DIR__.'/../../fw/core/util/Util_validator.php';

require_once __DIR__.'/../output/Output_HTML_App.php';

require_once __DIR__.'/../view/ViewHelper_App.php';

abstract class _Action_Api extends _Action {

	protected $master_db = null;
	protected $slave_db = null;
	protected $log_db = null;
	protected $ip_address = null;
	
	/**
	 * @see _Action::initialize()
	 */
	protected function initialize() {
		LogManager::debug("_Action_API");
		
		$this->trace(__METHOD__);
		Util_Session::start();
		parent::initialize();
		$this->ip_address = $_SERVER['REMOTE_ADDR'];
	}
	
	/**
	 * @see _Action::preAction()
	 */
	protected function preAction() {
		parent::preAction();
		$this->output = new Output_HTML_App();
	
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
    
    protected function sendJsonResult($result_map) {
    	$this->output = null;
    	$json_output = json_encode($result_map);
    	echo $json_output;
    }
    
    protected function setErrorMessage($message) {
    	$result_map = array();
    	if(strlen($message) > 0) {
    		$result_map['status'] = false;
    		$result_map['message'] = $message;
    
    		$this->resultPut($result_map);
    		exit();
    	}
    }
    
    /**
     * Env에 정의 된 패턴을 사용하여 허용 된 IP 주소에서 액세스 여부를 조사
     */
    protected function checkAllowedServerAccess($remote_addr)
    {
    	foreach( Env::$ALLOWED_SERVER_PTN_ARRAY as $ptn ) {
    		if( preg_match($ptn,$remote_addr)===1 ) {
    			return true;
    		}
    	}
    
    	return false;
    }
}