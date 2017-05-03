<?php

/**
* 
*/
class Action_AccessLog extends _Action_Support {
	
	protected $menu_allow_no = MenuData::LOG_ACCESS_ID;

	protected function initialize() {
		parent::initialize();
		
		// module
		$this->registValidatorMap('m');
	}

	protected function preAction() {
		parent::preAction();

		$this->action = $this->getQuery('m');

		$this->registValidatorMap('page');
		$this->registValidatorMap('accesskey');
		$this->registValidatorMap('category');
		$this->registValidatorMap('start_time');
		$this->registValidatorMap('end_time');
		$this->registValidatorMap('limit');

		$this->registValidatorMap('id');

		try {
			$this->validParam();
		} catch (Exception $e) {
			$this->error_msg = $e->getMessage();
			return;
		}
	}


	protected function doAction() {
		LogManager::debug($this->action);

		switch ($this->action) {
			case 'search':
				$this->search();
				break;
			case 'viewer':
				$this->viewer();
				break;
			default:
				$target = array(
					'tree' => 'Logs',
					'menu' => $this->menu_allow_no
				);
				
				$this->output->assign('target', $target);
				
				$this->output->setTmpl('support/accesslog.php');		
				break;
		}		
	}

	private function search() {
		$accesskey 	= $this->getQuery('accesskey');
		$category 	= $this->getQuery('category');
		$start_time = $this->getQuery('start_time');
		$end_time 	= $this->getQuery('end_time');
		$limit 		= $this->getQuery('limit');

		$limit = empty($limit) ? 100 : Env::PAGE_LIST;

		$page = $this->getQuery('page');
		empty($page) ? $page = 1 : '';

		$pager = new SimplePager($page, $limit);
		
		$data = Logic_Log::getAccessLogDataLimited($this->log_db, $accesskey, $category, $start_time, $end_time, $pager->limit(), $pager->offset());

		$this->output->assign('data', $data['list']);

		$pager->setPager($data['count'], self::PAGER_ARM_LENGTH);

		$params = array(
			'accesskey' => $accesskey,
			'category' => $category,
			'start_time' => $start_time,
			'end_time' => $end_time,
			'limit' => $limit
		);

		LogManager::debug($pager->output($params));
		$this->output->assign('pager', $pager->output($params));

		$this->output->setTmpl('support/_accesslog_list.php');
	}

	private function viewer() {
		$id = $this->getQuery('id');

		$log = Logic_Log::getAccessLogDataById($this->log_db, $id);

		$this->output->assign('log', $log);
		$this->output->setTmpl('support/_accesslog_viewer.php');
	}
}