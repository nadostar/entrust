<?php

/**
* 
*/
class Action_Invoice extends _Action_Support {
	protected $menu_allow_no = MenuData::SURVEY_PARTNER_ID;

	protected $error_msg = '';

	private $action = null;

	protected function initialize() {
		parent::initialize();

		$this->registValidatorMap('m');
	}

	protected function preAction() {
		parent::preAction();

		$this->action = $this->getQuery('m');

		$this->registValidatorMap('id');
		$this->registValidatorMap('pid');
		$this->registValidatorMap('tid');
		$this->registValidatorMap('sample');
		$this->registValidatorMap('q');
		$this->registValidatorMap('p');
		$this->registValidatorMap('o');
		$this->registValidatorMap('r');

		try {
			$this->validParam();
		} catch (Exception $e) {
			$this->error_msg = $e->getMessage();
			return;
		}
	}

	protected function doAction() {
		switch ($this->action) {
			case 'payment':
				$this->payment();
				break;
			case 'invoice':
				$this->invoice();
				break;
		}
	}
	
	private function payment() {
		
		$keys = array('id', 'pid', 'tid', 'sample', 'price', 'other_free', 'quantity', 'remark', 'last_updated_user');
		$values = array(
			$this->getQuery('id'),
			$this->getQuery('pid'),
			$this->getQuery('tid'),
			$this->getQuery('sample'),
			$this->getQuery('p'),
			$this->getQuery('o'),
			$this->getQuery('q'),
			$this->getQuery('r'),
			$this->login_session->getAdminId()
		);

		LogManager::debug($values);

		$result_map = array('status' => true, 'message' => 'The data has been save changed!');

		if(!Logic_Invoice::insertInvoice($this->master_db, $keys, $values)) {
			$result_map['status'] = false;
			$result_map['message'] = 'transaction fail!';
		}

		$this->sendJsonResult($result_map);
	}

	private function invoice() {
		$id = $this->getQuery('id');

		$invoice = Logic_Invoice::getInvoiceDataByPartnerId($this->slave_db, $id);
		$partner = Logic_Partner::getPartnerInvoiceDataById($this->slave_db, $id);

		$snapshot = array(
			'pid' 			=> $partner['pid'],
			'link_id' 		=> $partner['link_id'],
			'partner_id' 	=> $partner['id']
		);

		$statdata = Logic_Stat::getStatDataByIds($this->slave_db, $snapshot);

		$partner['c'] = $statdata['complate_count'];
		$partner['s'] = $statdata['screenout_count'];
		$partner['q'] = $statdata['quotafull_count'];

		$ir_a = 0;
		try {
			$ir_a = intval($statdata['complate_count']) / (intval($statdata['complate_count']) + intval($statdata['screenout_count'])) * 100;
		} catch (Exception $e) {
			$ir_a = 0;
		}

		$partner['ir_a'] = $ir_a;
		

		if($invoice === false) {
			$partner['invoice_no'] = 'INV-'.date("ymdH-is");
			$partner['invoice_date'] = date("Y-m-d");
			$partner['price'] = '0.00';
			$partner['other'] = '0.00';
			$partner['quantity'] = '0';
			$partner['remark'] = '';
			$partner['total'] = '0.00';
		} else {
			$partner['invoice_no'] = $invoice['id'];
			$partner['invoice_date'] = $invoice['updated_at'];
			$partner['price'] = $invoice['price'];
			$partner['other'] = $invoice['other_free'];
			$partner['quantity'] = $invoice['quantity'];
			$partner['remark'] = $invoice['remark'];
			$partner['total'] = round($invoice['price'] * $invoice['quantity'] + $invoice['other_free'] ,2);
		}

		$this->output->assign('partner', $partner);
		$this->output->setTmpl('support/_partner_invoice.php');
	}	
}