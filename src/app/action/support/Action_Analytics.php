<?php

/**
* 
*/
class Action_Analytics extends _Action_Support {
	protected $menu_allow_no = MenuData::SURVEY_ANALYTICS_ID;

	protected $error_msg = '';

	private $action = null;

	protected function initialize() {
		parent::initialize();

		$this->registValidatorMap('m');
	}

	protected function preAction() {
		parent::preAction();

		$this->action = $this->getQuery('m');

		if(!empty($this->action)) {
			$this->registValidatorMap('page');
			$this->registValidatorMap('id');

			$this->registValidatorMap('pid');
			$this->registValidatorMap('link_id');
		}

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
			case 'blocklog':
				$this->blocklog();
				break;
			case 'historylog':
				$this->historylog();
				break;
			default:
				$target = array(
					'tree' => 'Survey',
					'menu' => $this->menu_allow_no
				);
				
				$this->output->assign('target', $target);

				$this->output->setTmpl('support/analytics.php');
				break;
		}
	}

	private function search() {
		$page = $this->getQuery('page');
		empty($page) ? $page = 1 : '';

		$pager = new SimplePager($page, Env::PAGE_LIST);

		$data = Logic_Analytics::getProjectData($this->slave_db, $pager->limit(), $pager->offset());

		$this->output->assign('data', $data['list']);

		$pager->setPager($data['count'], self::PAGER_ARM_LENGTH);

		$params = array();

		$this->output->assign('pager', $pager->output($params));

		$this->output->setTmpl('support/_analytics_list.php');
	}

	private function viewer() {
		$id = $this->getQuery('id');
		
		$project = Logic_Project::getProjectDataById($this->slave_db, $id);

		$partner = Logic_Analytics::getPartnerData($this->slave_db, $id);

		//$history = Logic_Analytics::getLinkHistoryDataByAccesskey($this->slave_db, '', '', '');

		//$block = Logic_Analytics::getBlockDataByAccesskey($this->slave_db, '', '', '');

		$data = array(
			'project' => $project,
			'partner' => $partner,
		//	'history' => $history,
		//	'block' => $block
		);

		$this->output->assign('data', $data);

		$this->output->setTmpl('support/_analytics_viewer.php');
	}
	
	private function blocklog() {
		$pid = $this->getQuery('pid');
		$link_id = $this->getQuery('link_id');

		$page = $this->getQuery('page');
		empty($page) ? $page = 1 : '';

		$pager = new SimplePager($page, Env::PAGE_LIST);

		$data = Logic_Analytics::getBlockDataByAccesskey($this->slave_db, $pid, $link_id, $pager->limit(), $pager->offset());

		$this->output->assign('data', $data['list']);
		$pager->setPager($data['count'], self::PAGER_ARM_LENGTH);

		$params = array(
			'pid' => $pid,
			'link_id' => $link_id
		);

		LogManager::debug($pager->output($params));
		$this->output->assign('pager', $pager->output($params));

		$this->output->setTmpl('support/_analytics_ip_viewer.php');
	}

	private function historylog() {
		$pid = $this->getQuery('pid');
		$link_id = $this->getQuery('link_id');

		$page = $this->getQuery('page');
		empty($page) ? $page = 1 : '';

		$pager = new SimplePager($page, Env::PAGE_LIST);

		$data = Logic_Analytics::getLinkHistoryDataByAccesskey($this->slave_db, $pid, $link_id, $pager->limit(), $pager->offset());

		$this->output->assign('data', $data['list']);
		$pager->setPager($data['count'], self::PAGER_ARM_LENGTH);

		$params = array(
			'pid' => $pid,
			'link_id' => $link_id
		);

		LogManager::debug($pager->output($params));
		$this->output->assign('pager', $pager->output($params));

		$this->output->setTmpl('support/_analytics_history_viewer.php');
	}
}