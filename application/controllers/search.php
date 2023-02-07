<?php

class Search extends MY_Controller {
	function __construct() {
		parent::__construct();
		
		$this->load->model('report');
	}
	
	function index($starting_row = 0) {
		$starting_row = intval($starting_row);
		
		$from_date = null;
		$to_date   = null;
		$search    = null;
		$search_in = null;
		
		if($this->input->post('Submit')) {
			$starting_row = 0;
			$from_date = $this->input->post('from_date');
			$to_date   = $this->input->post('to_date');
			$search    = $this->input->post('search');
			$search_in = $this->input->post('search_in');
			$this->session->set_userdata($this->_class.'_from_date', $from_date);
			$this->session->set_userdata($this->_class.'_to_date', $to_date);
			$this->session->set_userdata($this->_class.'_search', $search);
			$this->session->set_userdata($this->_class.'_search_in', $search_in);
		}
		
		if ($from_date == null) {
			$from_date = $this->session->userdata($this->_class.'_from_date');
			$to_date   = $this->session->userdata($this->_class.'_to_date');
			$search    = $this->session->userdata($this->_class.'_search');
			$search_in = $this->session->userdata($this->_class.'_search_in');
		}

		$data['from_date'] = $from_date ? $from_date : date('01-m-Y');
		$data['to_date']   = $to_date ? $to_date : date('d-m-Y');
		$data['search']    = $search ? $search : '';
		$data['search_in'] = $search_in ? $search_in : 'ij';
		$data['search_list'] = array(
			'ij' => 'Import Jobs',
			'ej' => 'Export Jobs',
		);
				
		$this->load->library('pagination');
		$config['base_url'] = site_url($this->_class."/index");
		$config['uri_segment'] = 4;
		$config['per_page'] = Settings::get('rows_per_page');
		if ($search_in == 'ij') {
			$data['list'] = $this->_jobs();
			$config['total_rows'] = $this->report->countJobs($from_date, $to_date, $search);
			$data['list']['data'] = $this->report->getJobs($from_date, $to_date, $search, 0, $starting_row, $config['per_page']);
			$data['page_title'] = 'Import Jobs Search';
			$data['label_class'] = $this->import->getLabelClass();
		}
		else {
			$data['list'] = $this->_empty();
			$config['total_rows'] = 0;
			$data['list']['data'] = array();
			$data['page_title'] = 'Search';
		}
		$this->pagination->initialize($config);
		
		$data['page'] = 'list';
		$data['docs_url'] = $this->_docs;
		$this->load->view('index', $data);
	}
	
	function preview() {
		$from_date = $this->session->userdata($this->_class.'_from_date');
		$to_date   = $this->session->userdata($this->_class.'_to_date');
		$search    = $this->session->userdata($this->_class.'_search');
		$search_in = $this->session->userdata($this->_class.'_search_in');
		
		if ($search_in == 'ij') {
			$data['list'] = $this->_jobs();
			$total_rows = $this->report->countJobs($from_date, $to_date, $search);
			$data['list']['data'] = $this->report->getJobs($from_date, $to_date, $search, 0, 0, $total_rows);
			$data['page_title'] = 'Jobs Register';
			$data['label_class'] = $this->import->getLabelClass();
		}
		
		unset($data['list']['search_form']);
		$data['page_desc'] = "Period From: $from_date - To: $to_date, Searched Criteria: $search";
		$data['hide_menu'] = true;
		$data['hide_footer'] = true;
		$data['page'] = 'list';
		$this->load->view('index', $data);
	}
	
	function excel() {
		$from_date = $this->session->userdata($this->_class.'_from_date');
		$to_date   = $this->session->userdata($this->_class.'_to_date');
		$search    = $this->session->userdata($this->_class.'_search');
		$search_in = $this->session->userdata($this->_class.'_search_in');
		
		if ($type == 'ij') {
			$query = $this->report->getJobs($from_date, $to_date, $search, 1);
		}

		$this->load->helper('excel');
		to_excel($query, $this->_class);
	}
	
	function _empty() {
		return array(
			'heading' => array('ID'),
			'class' => array('id' => 'Code'),
			'search_form' => 'search');
	}
	
	function _jobs() {
		return array(
			'heading' => array('Job No', 'Party Name', 'HSS Party Name', 'Port', 'Be Type', 'BL No', 'BL Date', 'Vessel', 'Containers', 'Number', 'Size',
				'Line Name', 'Line Payments', 'Line Total', 'CFS Name', 'CFS Payments', 'CFS Total', 'Stamp Duty', 'Bills', 'Status'),
			'class' => array(
				'id2_format' => 'Code',
				'party_name' => 'Text',
				'hss_party_name' => 'Text',
				'indian_port' => 'Code',
				'be_type' => 'Code',
				'bl_no' => 'Code',
				'bl_date' => 'Date',
				'vessel_name' => 'Code',
				'containers' => 'Numeric',
				'container_nos' => 'Text',
				'size' => 'Code',
				'line_name' => 'Text',
				'line_payment' => 'Text',
				'line_total' => 'Numeric',
				'cfs_name' => 'Text',
				'cfs_payment' => 'Text',
				'cfs_total' => 'Numeric',
				'stamp_duty' => 'Numeric',
				'bill_detail' => 'Text',
				'status' => 'Label'),
			'search_form' => 'search');
	}
}
