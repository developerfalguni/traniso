<?php

class Vi extends MY_Controller {
	function __construct() {
		parent::__construct();
		
		$this->load->model('visualimpex');
	}
		
	function index() {
		if (! Auth::hasAccess()) {
			setSessionError('NO_PERMISSION');
			redirect('main');
		}
		
		$data['financial_year'] = $this->visualimpex->getFinancialYear();
		$data['page_title']     = "Import From Visual Impex";
		$data['hide_title']     = true;
		$data['page']           = $this->_clspath.$this->_class;
		$this->load->view('index', $data);
	}

	function importJobs($financial_year) {
		if ($this->_is_ajax && Auth::hasAccess(Auth::CREATE | Auth::UPDATE))
			$this->visualimpex->import($financial_year);
		echo "OK";
	}

	function ajaxExportJobs() {
		if ($this->_is_ajax) {
			$search = strtolower($this->input->get('term'));
			$this->visualimpex->ajaxExportJobs($search);
		}
		else {
			echo "Access Denied";
		}
	}
}
