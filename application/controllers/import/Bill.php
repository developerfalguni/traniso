<?php

class Bill extends MY_Controller {
	function __construct() {
		parent::__construct();
	
		$this->load->model('import');
	}
	
	function index($job_id = 0) {
		if (! Auth::hasAccess()) {
			setSessionError('NO_PERMISSION');
			redirect('main');
		}
	
		$job_id = intval($job_id);
		if ($job_id <= 0 OR $this->import->jobsExists($job_id) == 0) {
			setSessionError('SELECT_JOB');
			redirect($this->_clspath."jobs");
		}
		
		$data['list'] = array(
			'heading' => array('Company', 'Date', 'Voucher', 'Amount'),
			'class' => array(
				'company'    => 'Code',
				'date'       => 'Date',
				'id2_format' => array('class' => 'Code nowrap', 'link' => 'id'),
				'amount'     => 'Number'
			),
			'link_col' => "id2_format",
			'link_url' => "/accounting/"
		);
		
		$data['list']['data'] = $this->import->getBills($job_id);
		$data['jobs'] = $this->import->getJobsInfo($job_id, true, site_url($this->_clspath));
		
		//$data['buttons'] = array(anchor($this->_clspath.$this->_class."/generate/$job_id", '<i class="fa fa-plus"></i> Generate', 'class="btn btn-success"'));
		
		$data['page_title'] = humanize($this->_class);
		$data['hide_title'] = true;
		$data['page']       = $this->_clspath.'index';
		$data['job_page']   = 'list';
		$data['docs_url']   = $this->_docs;
		$this->load->view('index', $data);
	}
}
