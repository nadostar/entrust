<?php

/**
* 
*/
class Action_Analytics extends _Action_Support {
	protected $menu_allow_no = MenuData::SURVEY_ANALYTICS_ID;

	protected $error_msg = '';

	private $action = null;

	private $export_opt_disp_colum_name = true;

	/** CSV 컬럼명 */
	private $exportColumNameArray = array(
		"ESID", "Partner Key", "Partner UID", "Survey URL", "Progress", "Issue Date"
	);

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
			$this->registValidatorMap('export_id');
		}

		try {
			$this->validParam();
		} catch (Exception $e) {
			$this->error_msg = $e->getMessage();
			return;
		}
	}

	protected function doAction() {
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
			case 'export':
				$this->export();
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

		$data = Logic_Analytics::getMainAnalyticsData($this->slave_db, $pager->limit(), $pager->offset());

		$this->output->assign('data', $data['list']);

		$pager->setPager($data['count'], self::PAGER_ARM_LENGTH);

		$params = array();

		$this->output->assign('pager', $pager->output($params));

		$this->output->setTmpl('support/_analytics_list.php');
	}

	private function viewer() {
		$id = $this->getQuery('id');
		
		$project = Logic_Project::getProjectDataById($this->slave_db, $id);

		$partner = Logic_Analytics::getSubAnalyticsData($this->slave_db, $id);

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

		$page = $this->getQuery('page');
		empty($page) ? $page = 1 : '';

		$pager = new SimplePager($page, Env::PAGE_LIST);

		$data = Logic_Analytics::getBlockDataByProjectId($this->slave_db, $pid, $pager->limit(), $pager->offset());

		$this->output->assign('data', $data['list']);
		$pager->setPager($data['count'], self::PAGER_ARM_LENGTH);

		$params = array(
			'pid' => $pid,
		);

		LogManager::debug($pager->output($params));
		$this->output->assign('pager', $pager->output($params));

		$this->output->setTmpl('support/_analytics_ip_viewer.php');
	}

	private function historylog() {
		$id = $this->getQuery('id');

		$page = $this->getQuery('page');
		empty($page) ? $page = 1 : '';

		$pager = new SimplePager($page, Env::PAGE_LIST);

		$data = Logic_Analytics::getHistoryDataByPartnerId($this->slave_db, $id, $pager->limit(), $pager->offset());

		$this->output->assign('data', $data['list']);
		$pager->setPager($data['count'], self::PAGER_ARM_LENGTH);

		$params = array(
			'id' => $id
		);

		LogManager::debug($pager->output($params));
		$this->output->assign('pager', $pager->output($params));

		$this->output->setTmpl('support/_analytics_history_viewer.php');
	}

	private function export() {
		$this->output = new Output_CSV();
		set_time_limit(0);

		$date = date('ymdHis');
		$file = $date.'.csv';
		$this->output->setExportFileName($file);
		$this->output->realtimeOutput();

		if($this->export_opt_disp_colum_name) {
			$this->output->assignRow($this->exportColumNameArray);
		}
		$id = $this->getQuery('export_id');

		$snapshot = Logic_Snapshot::getSnapshotDataByPartnerId($this->slave_db, $id);
		$data = Logic_Analytics::exportDataByAccesskey($this->slave_db, $snapshot['accesskey']);

		foreach ($data as $k => $v) {
			$v['Progress'] = MasterData::getAccessLogCategory($v['Progress']);
			$this->output->assignRow($v);
		}
	}
}