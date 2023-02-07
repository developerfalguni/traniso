<?php

class Main extends MY_Controller {
	var $_criteria;

	function __construct() {
		parent::__construct();

		$this->_map = array(
			'party' 	=> 'PL.name',
			'hss' 		=> 'HSL.name',
			'bl' 		=> 'J.bl_no',
			'vessel' 	=> 'V.name',
			'shipper'	=> 'J.shipper_name',
			'status'	=> 'IT.status',
			'appra' 	=> 'IC.appraisement'
		);

		$this->_criteria = array(
			'Summary' => array(
				'PL.name'         => 'Party Name',
				'HSS.name'        => 'High Seas Party',
				'SP.name'         => 'Shipper Name',
				'V.name'          => 'Vessel Name',
				'IP.name'         => 'Indian Port',
				'J.packages'      => 'Pieces',
				'J.net_weight'    => 'Quantity',
				'IC.appraisement' => 'Appraisement'
			),
			'Detail'  => array(
				'PL.name'         => 'Party Name',
				'HSS.name'        => 'High Seas Party',
				'SP.name'         => 'Shipper Name',
				'V.name'          => 'Vessel Name',
				'IP.name'         => 'Indian Port',
				'J.bl_no'         => 'BL No',
				'J.be_no'         => 'BE No',
				'J.packages'      => 'Pieces',
				'J.net_weight'    => 'Quantity',
				'IC.prior_be'          => 'Prior BE',
				'IC.section_48'        => 'Section 48',
				'IC.appraising_group'  => 'Appraising Group',
				'IC.accessible_value'  => 'Accessible Value',
				'IC.appraisement'      => 'Appraisement',
				'IC.appraisement_date' => 'Appraisement Date',
				'IC.assessment_date'   => 'Assessment Date',
				'IC.payment_date'      => 'Payment Date',
				'IC.exam_date'         => 'Exam Date',
				'IC.ooc_date'          => 'OOC Date'
			)
		);

		$this->load->model('report');
	}

	function _parseSearch($search) {
		if (strpos($search, ':') == 0)
			return $search;

		$parsed = array();
		$parts = explode(' ', str_replace(': ', ':', $search));
		foreach($parts as $p) {
			$subparts = explode(':', $p);
			if (count($subparts) == 2)
				$parsed[trim($subparts[0])] = trim($subparts[1]);
		}
		return $parsed;
	}
	
	function index() {
		
//ChromePhp::info($_POST); die();
		$from_date = null;
		$to_date   = null;
		$search    = null;
		
		if ($this->input->post('from_date')) {
			$from_date = $this->input->post('from_date');
			$to_date   = $this->input->post('to_date');
			$search = $this->input->post('search');
			$this->session->set_userdata($this->_class.'_from_date', $from_date);
			$this->session->set_userdata($this->_class.'_to_date', $to_date);
			$this->session->set_userdata($this->_class.'_search', $search);
		}
		
		if ($from_date == null) {
			$from_date = $this->session->userdata($this->_class.'_from_date');
			$to_date   = $this->session->userdata($this->_class.'_to_date');
			$search    = $this->session->userdata($this->_class.'_search');
		}

		$data['from_date'] = $from_date ? $from_date : date('01-m-Y');
		$data['to_date']   = $to_date ? $to_date : date('d-m-Y');
		$data['search']    = $search ? $search : '';
		$data['criteria']  = $this->_criteria;
		$data['search_fields'] = $this->_map;
		
		if ($this->input->post('from_date')) {
			$data['list']['heading'] = array('ID', 'Party Name', 'Port', 'Product', 'Vessel', 'BL', 'BE', 'Pieces', 'Quantity', 'Shipper', 'Appraisement');
			$data['list']['class'] = array(
					'id' => 'ID',
					'party_name' => 'Text',
					'indian_port' => 'Text',
					'product_name' => 'Text',
					'vessel_name' => 'Text',
					'bl_no' => 'Code',
					'be_no' => 'Code',
					'packages' => 'Text',
					'net_weight' => 'Text',
					'shipper_name' => 'Text',
					'appraisement' => 'Text');
			
			$data['list']['data'] = $this->report->getAppraisement(convDate($from_date), convDate($to_date), $search);
		}

		$default_company = $this->session->userdata("default_company");
		$data['years'] = explode('_', $default_company['financial_year']);

		$this->load->helper('datefn');
		$data['javascript'] = ['bootstrap-daterangepicker/daterangepicker.js'];
		$data['stylesheet'] = ['bootstrap-daterangepicker/daterangepicker.css'];

		$data['page_title'] = "Job Register";
		$data['page'] = $this->_clspath.$this->_class;
		$data['docs_url'] = $this->_docs;
		$this->load->view('index', $data);
	}
}
