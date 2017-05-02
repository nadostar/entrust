<?php

class SimplePager {
	
	const NUM_PER_PAGE = 10;
	const PAGER_ARM_LENGTH = 1;

	private $cur_page;
	private $pre_page;
	private $next_page;
	private $num_per_page;

	private $min_page;
	private $max_page;

	private $offset;
	private $limit;
	private $total;
	private $no;

	public function __construct($page, $num_per_page = self::NUM_PER_PAGE) {
		$this->cur_page = ($page > 0) ? $page : 1;
		$this->num_per_page = $num_per_page;

		$this->limit = ($num_per_page > 0) ? $num_per_page : 1;
		$this->offset = $this->limit * ($this->cur_page - 1);
	}

	public function setPager($total, $pager_arm_len = self::PAGER_ARM_LENGTH) {
		$this->total		= $total;
		$this->max_page		= ceil($this->total / $this->num_per_page);
		$this->min_page		= 1;
		$this->pre_page		= ($this->cur_page > $this->min_page) ? $this->cur_page - 1 : 0;
    	$this->next_page	= ($this->cur_page < $this->max_page) ? $this->cur_page + 1 : 0;
	}

	public function total(){
		return $this->total;
	}

	public function prev() {
		return $this->pre_page;
	}

	public function next() {
		return $this->next_page;
	}

  	public function offset() {
    	return $this->offset;
  	}

  	public function limit() {
    	return $this->limit;
  	}

  	public function output($params) {
  		$prev = $params;
  		$prev['page'] = $this->pre_page;

  		$next = $params;
  		$next['page'] = $this->next_page;

  		$pager = $params;
  		$pager['total'] = $this->total;
  		$pager['prev'] = ($this->pre_page == 0) ? '' : json_encode($prev);
  		$pager['next'] = ($this->next_page == 0) ? '' : json_encode($next);

  		return $pager;
  	}
}