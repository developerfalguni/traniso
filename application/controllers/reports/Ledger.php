<?php

class Ledger extends MY_Controller {
	function __construct() {
		parent::__construct();
		
		$this->load->model('import');
		$this->load->model('accounting');
	}
	
	function index($starting_row = 0) {
		if (! Auth::hasAccess()) {
			setSessionError('NO_PERMISSION');
			redirect('main');
		}
		
		$starting_row = intval($starting_row);

		$date   = $this->session->userdata($this->_class.'_date');
		$type = $this->session->userdata($this->_class.'_type');
		$days = $this->session->userdata($this->_class.'_days');
		$amount = $this->session->userdata($this->_class.'_amount');

		if ($this->input->post('date')) {
			$starting_row = 0;
			$date   = $this->input->post('date');
			$type = $this->input->post('type');
			$days = $this->input->post('days');
			$amount = $this->input->post('amount');
			$this->session->set_userdata($this->_class.'_date', $date);
			$this->session->set_userdata($this->_class.'_type', $type);
			$this->session->set_userdata($this->_class.'_days', $days);
			$this->session->set_userdata($this->_class.'_amount', $amount);
		}
		
		if($date == null) {
			$date   = date("d-m-Y");
			$type = 'Bulk';
			$days = 60;
			$amount = 10000;
			$this->session->set_userdata($this->_class.'_date', $date);
			$this->session->set_userdata($this->_class.'_type', $type);
			$this->session->set_userdata($this->_class.'_days', $days);
			$this->session->set_userdata($this->_class.'_amount', $amount);
		}

		$data['list'] = array(
			'heading' => array('Group', 'Code', 'Name', 'Debit', 'Credit'),
			'class' => array(
				'group_name' => 'Text',
				'code'       => 'Code',
				'name'       => 'Text',
				'debit'      => 'Numeric',
				'credit'     => 'Numeric'),
			'search_form' => $this->_clspath.$this->_class
		);

		if ($open_close) {
			$total_rows = $this->accounting->countTrialBalanceOpening($search);
			$data['list']['data'] = $this->accounting->getTrialBalanceOpening($search, 0, 0, $total_rows);
		}
		else {
			$total_rows = $this->accounting->countTrialBalanceClosing($search);
			$data['list']['data'] = $this->accounting->getTrialBalanceClosing($search, 0, 0, $total_rows);
		}
		
		$default_company = $this->session->userdata("default_company");
		$data['years']   = explode('_', $default_company['financial_year']);

		$this->load->helper('datefn');
		$data['javascript'] = array('bootstrap-daterangepicker/daterangepicker.js');
		$data['stylesheet'] = array('bootstrap-daterangepicker/daterangepicker.css');


		$data['date'] = $date;
		$data['type'] = $type;
		$data['days'] = $days;
		$data['amount'] = $amount;
		$data['docs_url'] = $this->_docs;
		$data['page_title'] = humanize($this->_class);

		$data['page'] = 'list';
		$this->load->view('index', $data);
	}


	function clearSearch() {
		$this->session->unset_userdata($this->_class.'_date');
		$this->session->unset_userdata($this->_class.'_type');
		$this->session->unset_userdata($this->_class.'_days');
		$this->session->unset_userdata($this->_class.'_amount');

		redirect($this->_clspath.$this->_class);
	}
	
	function preview($open_close = 0) {
		$search    = $this->session->userdata($this->_class.'_search');
		
		$data['list'] = array(
			'heading' => array('Group', 'Code', 'Name', 'Debit', 'Credit'),
			'class' => array(
				'group_name' => 'Text',
				'code' => 'Code',
				'name' => 'Text',
				'debit' => 'Numeric',
				'credit' => 'Numeric'),
			'show_total' => array('debit' => 0, 'credit' => 0)
		);
		if ($open_close) {
			$total_rows = $this->accounting->countTrialBalanceOpening($search);
			$data['list']['data'] = $this->accounting->getTrialBalanceOpening($search, 0, 0, $total_rows);
		}
		else {
			$total_rows = $this->accounting->countTrialBalanceClosing($search);
			$data['list']['data'] = $this->accounting->getTrialBalanceClosing($search, 0, 0, $total_rows);
		}
		$data['page_title'] = humanize($this->_class);


		$data['page_desc'] = "Search Criteria [ $search ]";
		$data['hide_menu'] = true;
		$data['hide_footer'] = true;
		$data['page'] = 'list';
		$this->load->view('index', $data);
	}
	
	function excel($open_close = 0) {
		$search    = $this->session->userdata($this->_class.'_search');
		if ($open_close)
			$query = $this->accounting->getTrialBalanceOpening($search, 1);
		else
			$query = $this->accounting->getTrialBalanceClosing($search, 1);
		$this->load->helper('excel');
		to_excel($query, $this->_class);
	}
}
