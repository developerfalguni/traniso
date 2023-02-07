<?php

class Invoice extends MY_Controller {
	function __construct() {
		parent::__construct();
		
		$this->_fields = array(
			'book'    => 'VT.name',
			'type'    => 'VB.job_type',
			'job_no'  => 'COALESCE(J.id2_format, JT.id2_format)',
			'bill_no' => "CONCAT(V.id2_format, '/', V.id3)",
			'debit'   => 'DL.name',
			'credit'  => 'CL.name',
			'balance' => ''
		);
	}
	
	function index() {
		$default_company = $this->session->userdata("default_company");
		$data['company'] = $this->kaabar->getRow('companies', $default_company['id']);
		$data['years']   = explode('_', $default_company['financial_year']);

		$from_date = null;
		$to_date   = null;
		$search    = null;

		if ($this->input->post('from_date')) {
			$from_date = $this->input->post('from_date');
			$to_date   = $this->input->post('to_date');
			$search    = $this->input->post('search');
			$this->session->set_userdata($this->_class.'_from_date', $from_date);
			$this->session->set_userdata($this->_class.'_to_date', $to_date);
			$this->session->set_userdata($this->_class.'_search', $search);
		}
		
		if ($from_date == null) {
			$from_date = $this->session->userdata($this->_class.'_from_date');
			$to_date   = $this->session->userdata($this->_class.'_to_date');
			$search    = $this->session->userdata($this->_class.'_search');
		}

		$data['from_date']     = $from_date ? $from_date : date('01-m-Y');
		$data['to_date']       = $to_date ? $to_date : date('d-m-Y');
		$data['search']        = $search;
		$parsed_search         = $this->kaabar->parseSearch($search);
		$data['parsed_search'] = $parsed_search;
		$data['search_fields'] = $this->_fields;

		if (is_array($parsed_search)) {
			$search = '';
			foreach ($parsed_search as $key => $value) {
				$search .= $key.':'.$value.' ';
			}
			$data['search'] = $search;
		}

		$data['rows'] = $this->_get($data['from_date'], $data['to_date'], $search, $parsed_search);

		$this->load->helper('datefn');
		$data['javascript'] = ['bootstrap-daterangepicker/daterangepicker.js'];
		$data['stylesheet'] = ['bootstrap-daterangepicker/daterangepicker.css'];

		$data['page_title'] = "Invoice Report";
		$data['page']       = $this->_clspath.$this->_class;
		$data['docs_url']   = $this->_docs;
		$this->load->view('index', $data);
	}

	function excel() {
		$from_date     = $this->session->userdata($this->_class.'_from_date');
		$to_date       = $this->session->userdata($this->_class.'_to_date');
		$search        = $this->session->userdata($this->_class.'_search');
		$parsed_search = $this->kaabar->parseSearch($search);
		$rows          = $this->_get($from_date, $to_date, $search, $parsed_search);

		$this->_excel($rows, array('voucher_book_id', 'id2', 'id3'));
	}

	function _get($from_date, $to_date, $search, $parsed_search) {
		$company = $this->session->userdata('default_company');

		$sql = "SELECT V.id, V.voucher_book_id, V.id2, V.id3, V.id2_format, DATE_FORMAT(V.date, '%d-%m-%Y') AS date, 
			COALESCE(J.id, JT.id) AS job_id, COALESCE(J.type, JT.type) AS type, COALESCE(J.cargo_type, JT.cargo_type) cargo_type, 
			COALESCE(J.id2_format, JT.id2_format) AS job_no, 
			IF(CT.size=20, PC.containers, COALESCE(J.container_20, JT.container_20)) AS c20, 
			IF(CT.size=40, PC.containers, COALESCE(J.container_40, JT.container_40)) AS c40, 
			DL.name AS debit_name, CL.name AS credit_name, 
			IF(VB.job_type = 'Transportation', SUM(COALESCE(TA.advance, 0) + COALESCE(VT.advance, 0)), '') AS advance, V.amount AS net_amount,
			SUM(IF(LENGTH(VJD.tax_calculation) = 0, VJD.amount, 0)) AS amount, SUM(IF(LENGTH(VJD.tax_calculation) = 0, 0, VJD.amount)) as stax_amount
		FROM vouchers V 
			INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id
			INNER JOIN voucher_types VT ON VB.voucher_type_id = VT.id
			INNER JOIN ledgers DL ON V.dr_ledger_id = DL.id
			INNER JOIN ledgers CL ON V.cr_ledger_id = CL.id
			INNER JOIN voucher_details VJD ON V.id = VJD.voucher_id
			LEFT OUTER JOIN jobs J ON V.job_id = J.id
			LEFT OUTER JOIN trips T ON VJD.trip_id = T.id
			LEFT OUTER JOIN jobs JT ON T.job_id = JT.id
			LEFT OUTER JOIN job_containers PC ON COALESCE(J.id, JT.id) = PC.job_id
			LEFT OUTER JOIN container_types CT ON PC.container_type_id = CT.id
			LEFT OUTER JOIN (
				SELECT TA.trip_id, SUM(TA.amount) AS advance
				FROM trip_advances TA INNER JOIN trips T ON TA.trip_id = T.id
				WHERE T.company_id = ? AND TA.advance_by = 'Party'
				GROUP BY TA.trip_id
			) TA ON T.id = TA.trip_id
			LEFT OUTER JOIN (
				SELECT VT.trip_id, SUM(VT.advance) AS advance
				FROM voucher_trips VT INNER JOIN vouchers V ON VT.voucher_id = V.id
					INNER JOIN voucher_books VB ON (VB.company_id = ? AND V.voucher_book_id = VB.id)
					INNER JOIN voucher_types VTS ON (VB.voucher_type_id = VTS.id AND VTS.name = 'Receipt')
				GROUP BY VT.trip_id
			) VT ON T.id = VT.trip_id
		WHERE (VB.company_id = ? AND V.date >= ? AND V.date <= ? AND VB.voucher_type_id IN (3,4)) "; //  AND VJD.particulars LIKE 'Transportation%'
		$where = ' AND (';
		if (is_array($parsed_search)) {
			foreach($parsed_search as $key => $value)
				if (isset($this->_fields[$key]))
					$where .= $this->_fields[$key] . " LIKE '%$value%' AND ";
			if (strlen($where) > 6)
				$sql .= substr($where, 0, strlen($where) - 5) . ')';
		}
		$sql .= "
		GROUP BY V.id
		ORDER BY VB.id, V.id2, V.date";
		$query = $this->db->query($sql, array(
			$company['id'], $company['id'], $company['id'], convDate($from_date), convDate($to_date),
		));
		return $query->result_array();
	}
}
