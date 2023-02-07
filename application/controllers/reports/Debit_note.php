<?php

class Debit_note extends MY_Controller {
	var $_fields;
	var $_parsed_search;

	function __construct() {
		parent::__construct();
		
		$this->_fields = array(
			'job_no'  => 'J.id2_format',
			'bill_no' => "CONCAT(V.id2_format, '/', V.id3)",
			'debit'   => 'DL.name',
			'credit'  => 'CL.name',
			'balance' => ''
		);
	}
	
	function index() {
		if (! Auth::hasAccess()) {
			setSessionError('NO_PERMISSION');
			redirect('main');
		}

		$default_company = $this->session->userdata("default_company");
		$data['company'] = $this->kaabar->getRow('companies', $default_company['id']);
		$data['years']   = explode('_', $default_company['financial_year']);

		$from_date = null;
		$to_date   = null;
		$search    = null;

		if($this->input->post('from_date')) {
			$from_date = $this->input->post('from_date');
			$to_date   = $this->input->post('to_date');
			$search    = $this->input->post('search');
			$this->session->set_userdata($this->_class.'_from_date', $from_date);
			$this->session->set_userdata($this->_class.'_to_date', $to_date);
			$this->session->set_userdata($this->_class.'_search', $search);
		}
		
		if($from_date == null) {
			$from_date = $this->session->userdata($this->_class.'_from_date');
			$to_date   = $this->session->userdata($this->_class.'_to_date');
			$search = $this->session->userdata($this->_class.'_search');
		}

		$data['from_date']     = $from_date ? $from_date : date('01-m-Y');
		$data['to_date']       = $to_date ? $to_date : date('d-m-Y');
		$data['search']        = $search;
		$this->_parsed_search  = $this->kaabar->parseSearch($search);
		$data['parsed_search'] = $this->_parsed_search;
		$data['search_fields'] = $this->_fields;

		if (is_array($this->_parsed_search)) {
			$search = '';
			foreach ($this->_parsed_search as $key => $value) {
				$search .= $key.':'.$value.' ';
			}
			$data['search'] = $search;
		}

		$data['rows'] = $this->_get($data['from_date'], $data['to_date'], $search);

		$this->load->helper('datefn');
		$data['javascript'] = array('daterangepicker/daterangepicker.js');
		$data['stylesheet'] = array('daterangepicker/daterangepicker-bs3.css');

		$data['page_title'] = "Invoice Report";
		$data['hide_title'] = true;
		$data['page']       = $this->_clspath.$this->_class;
		$data['docs_url']   = $this->_docs;
		$this->load->view('index', $data);
	}

	function excel() {
		$from_date     = $this->session->userdata($this->_class.'_from_date');
		$to_date       = $this->session->userdata($this->_class.'_to_date');
		$search        = $this->session->userdata($this->_class.'_search');
		$rows         = $this->_get($from_date, $to_date, $search);

		$this->_excel($rows, array('id', 'voucher_book_id', 'id2', 'id3', 'job_id'));
	}

	function _get($from_date, $to_date, $search) {
		$company = $this->session->userdata('default_company');

		$sql = "SELECT V.id, V.voucher_book_id, V.id2, V.id3, V.id2_format, DATE_FORMAT(V.date, '%d-%m-%Y') AS date, 
			J.id AS job_id, J.type, J.cargo_type, J.id2_format AS job_no, DL.name AS debit_name, CL.name AS credit_name, V.amount
		FROM vouchers V 
			INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id
			INNER JOIN ledgers DL ON V.dr_ledger_id = DL.id
			INNER JOIN ledgers CL ON V.cr_ledger_id = CL.id
			LEFT OUTER JOIN jobs J ON V.job_id = J.id
		WHERE (VB.company_id = ? AND V.date >= ? AND V.date <= ? AND VB.voucher_type_id = 3)";
		$where = ' AND (';
		if (is_array($this->_parsed_search)) {
			foreach($this->_parsed_search as $key => $value)
				if (isset($this->_fields[$key]))
					$where .= $this->_fields[$key] . " LIKE '%$value%' AND ";
			if (strlen($where) > 6)
				$sql .= substr($where, 0, strlen($where) - 5) . ')';
		}
		$sql .= "
		ORDER BY VB.id, V.id2, V.date";
		$query = $this->db->query($sql, array(
			$company['id'], convDate($from_date), convDate($to_date),
		));
		return $query->result_array();
	}
}
