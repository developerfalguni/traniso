<?php

class Scanned_pending extends MY_Controller {
	var $_fields;
	var $_parsed_search;
	var $_company_id;
	var $_fy_year;

	function __construct() {
		parent::__construct();
	
		$this->_fields = array(
			'voucher' => 'V.id2_format',
			'dr_code' => 'DL.code',
			'dr_name' => 'DL.name',
			'cr_code' => 'CL.code',
			'cr_name' => 'CL.name',
		);

		$default_company   = $this->session->userdata("default_company");
		$this->_company_id = $default_company['id'];
		$this->_fy_year    = $default_company['financial_year'];
	}
	
	function index() {
		if (! Auth::hasAccess()) {
			setSessionError('NO_PERMISSION');
			redirect('main');
		}

		$search = $this->session->userdata($this->_class.'_search');

		if($this->input->post('search_form')) {
			$search = addslashes($this->input->post('search'));
			$this->session->set_userdata($this->_class.'_search', $search);
		}
		
		if ($search == null) {
			$search = '';
			$this->session->set_userdata($this->_class.'_search', $search);
		}

		$data['hide_id']       = $this->input->post('hide_id');
		$data['search']        = $search ? $search : '';
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

		$data['rows'] = $this->_getPending($search, $data['parsed_search'], $this->_fields);

		$data['page_title'] = humanize($this->_class);
		$data['hide_title'] = true;
		$data['page'] = $this->_clspath.$this->_class;
		$data['docs_url'] = $this->_docs;
		$this->load->view('index', $data);
	}

	function _getPending($search, $parsed_search, $fields) {
		$years     = explode('_', $this->_fy_year);
		$from_date = $years[0] . '-04-01';
		$to_date   = $years[1] . '-03-31';
		$hide_id   = $this->input->post('hide_id');

		$sql = "SELECT V.id, CONCAT(VT.name,'/edit/',VB.id,'/',V.id,'/',V.id3) AS url, 
			V.id2_format, DATE_FORMAT(V.date, '%d-%m-%Y') AS date,
			V.dr_ledger_id, DL.code AS dr_code, DL.name AS dr_name,
			V.cr_ledger_id, CL.code AS cr_code, CL.name AS cr_name,
			V.amount, IF(ISNULL(VD.file), 'No', 'Yes') AS document
		FROM ((((vouchers V INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id)
			INNER JOIN voucher_types VT ON VB.voucher_type_id = VT.id)
			INNER JOIN ledgers DL ON V.dr_ledger_id = DL.id)
			INNER JOIN ledgers CL ON V.cr_ledger_id = CL.id)
			LEFT OUTER JOIN voucher_documents VD ON V.id = VD.voucher_id
		WHERE (VB.voucher_type_id = 5 AND VB.company_id = ? AND V.date >= ? AND V.date <= ? AND V.id3 = 1 AND ISNULL(VD.file))";
		if (is_array($hide_id))
			$sql .= " AND V.dr_ledger_id NOT IN (" . implode(",", $hide_id) . ") AND V.cr_ledger_id NOT IN (" . implode(",", $hide_id) . ")";
		$where = ' AND (';
		if (is_array($parsed_search)) {
			foreach($parsed_search as $key => $value)
				if (isset($this->_fields[$key]))
					$where .= $this->_fields[$key] . " LIKE '%$value%' AND ";
			if (strlen($where) > 6)
				$sql .= substr($where, 0, strlen($where) - 5) . ')';
		}
		$sql .= "
		ORDER BY VB.id, V.date, V.id2";
		$query = $this->db->query($sql, array($this->_company_id, $from_date, $to_date));
		return $query->result_array();
	}
}
