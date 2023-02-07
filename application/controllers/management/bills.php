<?php

class Bills extends MY_Controller {
	function __construct() {
		parent::__construct();

		$this->load->helper('datefn');
	}

	function index() {
		$this->_fields = array(
			'party'   => 'PL.name',
		);

		$search = $this->session->userdata($this->_class.'_search');
		if($this->input->post('search_form')) {
			$search = addslashes($this->input->post('search'));
			$this->session->set_userdata($this->_class.'_search', $search);
		}
		
		if ($search == null) {
			$search = '';
			$this->session->set_userdata($this->_class.'_search', $search);
		}

		$data['search'] = $search;
		$parsed_search  = $this->kaabar->parseSearch($search);
		$data['parsed_search'] = $parsed_search;

		if (is_array($parsed_search)) {
			$search = '';
			foreach ($parsed_search as $key => $value) {
				$search .= $key.':'.$value.' ';
			}
			$data['search'] = $search;
		}
		$data['search_fields']  = $this->_fields;
		$data['rows'] = $this->_bills($search, $parsed_search);

		$data['page_title']  = $this->_class . " / Bills";
		$data['page']        = $this->_clspath.$this->_class;
		$data['docs_url']    = $this->_docs;
		$this->load->view('index', $data);
	}

	function _bills($search, $parsed_search, $fields) {
		$sql = "SELECT J.id AS job_id, J.type, J.id2_format, PL.name AS party_name,
		COALESCE(S.container_20, J.container_20) AS container_20, 
		COALESCE(S.container_40, J.container_40) AS container_40,
		COALESCE(TPT.amount, 0) AS transportation, 
		COALESCE(EXP.amount, 0) AS expenses,
		COALESCE(INV.amount, 0) AS invoice
		FROM jobs J INNER JOIN parties PL ON J.party_id = PL.id
			LEFT OUTER JOIN import_details ID ON J.id = ID.job_id
			LEFT OUTER JOIN (
				SELECT S.job_id,
					COUNT(IF(CT.size = 20, S.id, null)) AS container_20,
					COUNT(IF(CT.size = 40, S.id, null)) AS container_40
				FROM deliveries_stuffings S INNER JOIN container_types CT ON S.container_type_id = CT.id
				GROUP BY S.job_id
			) S ON J.id = S.job_id
			LEFT OUTER JOIN (
				SELECT T.job_id, ROUND(SUM(VD.amount), 2) AS amount
				FROM voucher_details VD 
					INNER JOIN trips T ON VD.trip_id = T.id
					INNER JOIN vouchers V ON VD.voucher_id = V.id
					INNER JOIN voucher_books VB ON (V.voucher_book_id = VB.id AND VB.job_type IN ('Transportation'))
					INNER JOIN voucher_types VT ON (VB.voucher_type_id = VT.id AND VT.name = 'Invoice')
				GROUP BY T.job_id
			) TPT ON TPT.job_id = J.id
			LEFT OUTER JOIN (
				SELECT VD.job_id, ROUND(SUM(VD.amount), 2) AS amount
				FROM voucher_details VD 
					INNER JOIN vouchers V ON VD.voucher_id = V.id
					INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id
					INNER JOIN voucher_types VT ON (VB.voucher_type_id = VT.id AND VT.name IN ('Journal', 'Payment'))
				GROUP BY VD.job_id
			) EXP ON EXP.job_id = J.id
			LEFT OUTER JOIN (
				SELECT COALESCE(V.job_id, VD.job_id) AS job_id, ROUND(SUM(VD.amount), 2) AS amount
				FROM voucher_details VD 
					INNER JOIN ledgers BI ON VD.bill_item_id = BI.id
					INNER JOIN vouchers V ON VD.voucher_id = V.id
					INNER JOIN voucher_books VB ON (V.voucher_book_id = VB.id AND VB.job_type IN ('Import', 'Export', 'Import-Export', 'Transportation'))
					INNER JOIN voucher_types VT ON (VB.voucher_type_id = VT.id AND VT.name IN ('Invoice', 'Debit Note'))
				GROUP BY V.job_id
			) INV ON INV.job_id = J.id
		WHERE J.cargo_type = 'Container' > 0 AND 
			COALESCE(J.status, J.status) != 'Completed'";
		$where = ' AND (';
		if (is_array($parsed_search)) {
			foreach($parsed_search as $key => $value)
				if (isset($this->_fields[$key]))
					$where .= $this->_fields[$key] . " LIKE '%$value%' AND ";
			if (strlen($where) > 6)
				$sql .= substr($where, 0, strlen($where) - 5) . ')';
		}
	
		$sql .= "
		GROUP BY J.id
		ORDER BY J.type, J.id2";
		$query = $this->db->query($sql);
		return $query->result_array();
	}
}