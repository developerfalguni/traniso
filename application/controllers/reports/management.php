<?php

class Management extends MY_Controller {
	var $_fields;

	function __construct() {
		parent::__construct();

		$this->load->helper('datefn');
	}

	function index() {

	}

	function import_jobs() {
		if (! Auth::hasAccess()) {
			setSessionError('NO_PERMISSION');
			redirect('main');
		}

		$this->_fields = array(
			'party'   => 'PL.name',
			'hss'     => 'HSL.name',
			'bl'      => 'J.bl_no',
			'vessel'  => 'V.name',
			'line'    => 'SL.name',
			'port'    => 'IP.name',
			'remarks' => 'P.remarks',
			'status'  => 'P.status',
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
		$data['rows'] = $this->_importJobs($search, $parsed_search);

		$data['page_title']  = $this->_class . " / Import / Container";
		$data['page']        = $this->_clspath.$this->_class.'_import_jobs';
		$data['docs_url']    = $this->_docs;
		$this->load->view('index', $data);
	}
	
	function _importJobs($search, $parsed_search) {
		$sql = "SELECT P.id, P.job_id, J.id2_format, PL.name AS party_name, GROUP_CONCAT(DISTINCT HSL.name ORDER BY HSL.id SEPARATOR '<br />') AS high_seas, 
			IF(J.cbm > 0, J.cbm, J.net_weight) AS cbm, J.net_weight, J.net_weight_unit, 
			(J.container_20 + J.container_40) AS total_containers,
			CONCAT(IF(J.container_20 > 0, CONCAT(J.container_20, 'x20 '), ''), IF(J.container_40 > 0, CONCAT(J.container_40, 'x40 '), '')) AS containers,
			J.house_bl, IF(LENGTH(TRIM(J.bl_no)) = 0, 'Missing BL No', J.bl_no) AS bl_no, DATE_FORMAT(bl_date, '%d-%m-%Y') AS bl_date, 
			J.vessel_id, P.temp_vessel_name, DATE_FORMAT(P.temp_eta, '%d-%m-%Y') AS temp_eta,  V.name AS vessel_name, V.voyage_no, DATE_FORMAT(V.eta_date, '%d-%m-%Y') AS eta_date, 
			IP.name AS indian_port, SL.name AS line_name, CHA.name AS cha_name,
			P.free_days, DATE_FORMAT(IF(ISNULL(V.eta_date), DATE_ADD(P.temp_eta, INTERVAL (P.free_days-1) DAY), DATE_ADD(V.eta_date, INTERVAL (P.free_days-1) DAY)), '%d-%m-%Y') AS free_days_upto, DATE_FORMAT(P.original_bl_received, '%d-%m-%Y') AS original_bl_received,
			P.remarks, P.status
		FROM import_details P INNER JOIN jobs J ON P.job_id = J.id
			INNER JOIN parties PL ON J.party_id = PL.id
			LEFT OUTER JOIN agents SL ON J.line_id = SL.id
			LEFT OUTER JOIN agents CHA ON J.cha_id = CHA.id
			LEFT OUTER JOIN vessels V ON J.vessel_id = V.id
			LEFT OUTER JOIN indian_ports IP ON J.indian_port_id = IP.id
			LEFT OUTER JOIN high_seas HS ON P.job_id = HS.job_id
			LEFT OUTER JOIN parties HSL ON HS.party_id = HSL.id
		WHERE (J.type = 'Import' AND J.cargo_type = 'Container' AND LENGTH(J.be_no) = 0 AND P.status != 'Completed')";
		$where = ' AND (';
		if (is_array($parsed_search)) {
			foreach($parsed_search as $key => $value)
				if (isset($this->_fields[$key]))
					$where .= $this->_fields[$key] . " LIKE '%$value%' AND ";
			if (strlen($where) > 6)
				$sql .= substr($where, 0, strlen($where) - 5) . ')';
		}
		$sql .= "
		GROUP BY P.job_id
		ORDER BY IF(ISNULL(V.eta_date) OR V.eta_date = '0000-00-00', P.temp_eta, V.eta_date), J.bl_date";
		$query = $this->db->query($sql);
		$rows  = $query->result_array();
		return $rows;
	}


	function import_process() {
		if (! Auth::hasAccess()) {
			setSessionError('NO_PERMISSION');
			redirect('main');
		}

		$this->_fields = array(
			'party'   => 'PL.name',
			'hss'     => 'HSL.name',
			'bl'      => 'J.bl_no',
			'vessel'  => 'V.name',
			'line'    => 'SL.name',
			'cfs'     => 'CFS.name',
			'port'    => 'IP.name',
			'pod'     => 'P.place_of_delivery',
			'shipper' => 'J.vi_shipper_name',
			'remarks' => 'P.remarks',
			'status'  => 'P.status',
			'icegate' => 'IT.status',
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
		$data['rows'] = $this->_importProcess($search, $parsed_search);

		$data['label_class'] = array(
			'N.A.' 		=> 'label-default',
			'OOC' 		=> 'label-success',
			'EXAM'  	=> 'label-default',
			'PAYMENT' 	=> 'label-blue',
			'ASSESS'	=> 'label-warning',
			'APPRA' 	=> 'label-info',
		);

		$data['page_title']  = $this->_class . " / Import / Container";
		$data['page']        = $this->_clspath.$this->_class.'_import_process';
		$data['docs_url']    = $this->_docs;
		$this->load->view('index', $data);
	}
	
	function _importProcess($search, $parsed_search) {
		$sql = "SELECT P.id, P.job_id, J.id2_format, PL.name AS party_name, GROUP_CONCAT(DISTINCT HSL.name ORDER BY HSL.id SEPARATOR '<br />') AS high_seas, 
			COALESCE(SHIPPER.name, J.vi_shipper_name) AS shipper_name, 
			CONCAT(IF(J.container_20 > 0, CONCAT(J.container_20, 'x20 '), ''), IF(J.container_40 > 0, CONCAT(J.container_40, 'x40 '), '')) AS containers,
			(J.container_20 + J.container_40) AS total_containers, J.net_weight, J.net_weight_unit,
			J.house_bl, IF(LENGTH(TRIM(J.bl_no)) = 0, 'Missing BL No', J.bl_no) AS bl_no, DATE_FORMAT(bl_date, '%d-%m-%Y') AS bl_date, 
			J.be_no, IT.section_48, IT.query_raised, IT.last_fetched, IT.last_status AS last_status, IT.status AS current_status, IC.id AS challan_id, 
			J.vessel_id, P.temp_vessel_name, DATE_FORMAT(temp_eta, '%d-%m-%Y') AS temp_eta,  V.name AS vessel_name, V.voyage_no, DATE_FORMAT(V.eta_date, '%d-%m-%Y') AS eta_date, 
			IP.name AS indian_port, P.place_of_delivery, CFS.name AS cfs_name,  SL.name AS line_name, P.custom_duty, 
			P.free_days, DATE_FORMAT(IF(ISNULL(V.eta_date), DATE_ADD(P.temp_eta, INTERVAL (P.free_days-1) DAY), DATE_ADD(V.eta_date, INTERVAL (P.free_days-1) DAY)), '%d-%m-%Y') AS free_days_upto, DATE_FORMAT(P.original_bl_received, '%d-%m-%Y') AS original_bl_received,
			P.remarks, P.status, 
			DO.do_no, DATE_FORMAT(DO.date, '%d-%m-%Y') AS delivery_date, DATE_FORMAT(COALESCE(DO.validity, '0000-00-00'), '%d-%m-%Y') AS validity, DO.empty_return_park
		FROM import_details P INNER JOIN jobs J ON P.job_id = J.id
			INNER JOIN parties PL ON J.party_id = PL.id
			LEFT OUTER JOIN agents SHIPPER ON J.shipper_id = SHIPPER.id
			LEFT OUTER JOIN agents SL ON J.line_id = SL.id
			LEFT OUTER JOIN agents CFS ON J.cfs_id = CFS.id
			LEFT OUTER JOIN vessels V ON J.vessel_id = V.id
			LEFT OUTER JOIN indian_ports IP ON J.indian_port_id = IP.id
			LEFT OUTER JOIN high_seas HS ON P.job_id = HS.job_id
			LEFT OUTER JOIN parties HSL ON HS.party_id = HSL.id
			LEFT OUTER JOIN parties WP ON J.web_party_id = WP.id
			LEFT OUTER JOIN icegate_be IT ON J.id = IT.job_id
			LEFT OUTER JOIN icegate_challans IC ON IT.challan_no = IC.challan_no
			LEFT OUTER JOIN delivery_orders DO ON J.id = DO.job_id
		WHERE (J.type = 'Import' AND J.cargo_type = 'Container' AND LENGTH(J.be_no) > 0)";
		$where = ' AND (';
		if (is_array($parsed_search)) {
			foreach($parsed_search as $key => $value)
				if (isset($this->_fields[$key]))
					$where .= $this->_fields[$key] . " LIKE '%$value%' AND ";
			if (strlen($where) > 6)
				$sql .= substr($where, 0, strlen($where) - 5) . ')';
		}
		$sql .= ((isset($parsed_search['status']) && $parsed_search['status'] == 'Completed') ? '' : " AND (P.status != 'Completed')") . "
		GROUP BY P.job_id
		ORDER BY IF(P.status = 'Program', 1, IF(P.status = 'Pending', 2, IF(P.status = 'Delivery', 3, 4))), V.eta_date " . 
		((isset($parsed_search['status']) && $parsed_search['status'] == 'Completed') ? 'LIMIT 0, 50' : '');
		$query  = $this->db->query($sql);
		$rows   = $query->result_array();
		$result = array(0);
		foreach ($rows as $r) {
			$result[$r['job_id']] = $r;
			$result[$r['job_id']]['expenses'] = 0;
			$result[$r['job_id']]['receipts'] = 0;
		}

		// Fetch Deliveries
		$sql = "SELECT C.job_id, 
			COUNT(IF(CT.size = 20 AND D.cfs_in_date != '0000-00-00 00:00:00', C.id, NULL)) AS container_20, 
			COUNT(IF(CT.size = 40 AND D.cfs_in_date != '0000-00-00 00:00:00', C.id, NULL)) AS container_40,
			ROUND(SUM(C.net_weight), 2) AS net_weight, ROUND(SUM(D.dispatch_weight), 2) AS dispatch_weight,
			IF(MAX(D.gatepass_date) = '0000-00-00', NOW(), MAX(D.gatepass_date)) AS last_delivery
		FROM deliveries_stuffings D LEFT OUTER JOIN containers C ON D.container_id = C.id
			LEFT OUTER JOIN container_types CT ON C.container_type_id = CT.id
		WHERE D.job_id IN (" . implode(',', array_keys($result)) . ")
		GROUP BY D.job_id";
		$query = $this->db->query($sql);
		$rows  = $query->result_array();
		foreach ($rows as $r) {
			if (isset($result[$r['job_id']]))
				$result[$r['job_id']]['delivery'] = $r;
		}

		// Fetch Journal Vouchers
		$sql = "SELECT VJD.job_id, SUM(VJD.amount) AS expenses
		FROM voucher_details VJD
			INNER JOIN vouchers V ON VJD.voucher_id = V.id
			INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id
		WHERE VB.voucher_type_id = 5 AND VJD.job_id > 0 AND VJD.job_id IN (" . implode(',', array_keys($result)) . ")
		GROUP BY VJD.job_id";
		$query  = $this->db->query($sql);
		$rows = $query->result_array();
		foreach ($rows as $r) {
			$result[$r['job_id']]['expenses'] = $r['expenses'];
		}

		// Fetch Receipt Vouchers
		$sql = "SELECT VJD.job_id, SUM(VJD.amount) AS receipts
		FROM voucher_details VJD
			INNER JOIN vouchers V ON VJD.voucher_id = V.id
			INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id
		WHERE VB.voucher_type_id = 11 AND VJD.job_id > 0 AND VJD.job_id IN (" . implode(',', array_keys($result)) . ")
		GROUP BY VJD.job_id";
		$query  = $this->db->query($sql);
		$rows = $query->result_array();
		foreach ($rows as $r) {
			$result[$r['job_id']]['receipts'] = $r['receipts'];
		}

		unset($result[0]);
		return $result;
	}



	function export_process() {
		if (! Auth::hasAccess()) {
			setSessionError('NO_PERMISSION');
			redirect('main');
		}

		$this->_fields = array(
			'status'  => 'J.status',
			'party'   => 'PL.name',
			'bl'      => 'BL.bl_no',
			'sb'      => 'J.sb_no',
			'invoice' => 'EI.invoice_no',
			'vessel'  => 'CONCAT(V.name, " ", V.voyage_no)',
			'line'    => 'L.name',
			'pol'     => 'IP.name',
			'pod'     => 'PRT.name',
			'cfs'     => 'CFS.name',
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
		$data['rows'] = $this->_export_process($search, $parsed_search);

		$data['page_title']  = $this->_class . " / Export / Container";
		$data['page']        = $this->_clspath.$this->_class.'_export_process';
		$data['docs_url']    = $this->_docs;
		$this->load->view('index', $data);
	}

	function _export_process($search, $parsed_search) {
		$sql = "SELECT CJ.job_id, CJ.id AS child_job_id, J.id2_format, PL.name AS party_name, EI.invoice_no, DATE_FORMAT(EI.invoice_date, '%d-%m-%Y') AS invoice_date, 
			CJ.sb_no, DATE_FORMAT(CJ.sb_date, '%d-%m-%Y') AS sb_date, IF(LENGTH(TRIM(CJ.bl_no)) = 0, 'Missing BL No', CJ.bl_no) AS bl_no, 
			DATE_FORMAT(CJ.bl_date, '%d-%m-%Y') AS bl_date, CARGO.name AS cargo_name, 
			GROUP_CONCAT(DISTINCT CONCAT(PC.containers, 'x', CT.size, CT.code) SEPARATOR ', ') AS containers,
			L.name AS line_name, V.name AS vessel_name, DATE_FORMAT(V.etd_date, '%d-%m-%Y') AS etd_date, 
			IP.name AS pol, PRT.name AS pod, 
			IC.last_fetched, IC.status AS icegate_status, IC.last_status, DATE_FORMAT(IC.leo_date, '%d-%m-%Y') AS leo_date, IC.ep_copy_print_status, IC.print_status
		FROM icegate_sb IC INNER JOIN child_jobs CJ ON IC.child_job_id = CJ.id
			INNER JOIN jobs J ON J.id = CJ.job_id
			INNER JOIN parties PL ON J.party_id = PL.id
			LEFT OUTER JOIN agents L ON J.line_id = L.id
			LEFT OUTER JOIN vessels V ON J.vessel_id = V.id
			LEFT OUTER JOIN indian_ports IP ON J.custom_port_id = IP.id
			LEFT OUTER JOIN ports PRT ON J.discharge_port_id = PRT.id
			LEFT OUTER JOIN products CARGO ON J.product_id = CARGO.id
			LEFT OUTER JOIN planned_containers PC ON J.id = PC.job_id
			LEFT OUTER JOIN container_types CT ON PC.container_type_id = CT.id
		WHERE (J.type = 'Export' AND J.cargo_type = 'Container' AND LENGTH(TRIM(CJ.sb_no)) > 0 AND J.status != 'Completed') ";
		$where = ' AND (';
		if (is_array($parsed_search)) {
			foreach($parsed_search as $key => $value)
				if (isset($this->_fields[$key]))
					$where .= $this->_fields[$key] . " LIKE '%$value%' AND ";
			if (strlen($where) > 6)
				$sql .= substr($where, 0, strlen($where) - 5) . ')';
		}
	
		$sql .= "
		GROUP BY J.id, CJ.id
		ORDER BY id2 DESC";
		$query = $this->db->query($sql);
		return $query->result_array();
	}



	function bills() {
		if (! Auth::hasAccess()) {
			setSessionError('NO_PERMISSION');
			redirect('main');
		}

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
		$data['page']        = $this->_clspath.$this->_class.'_bills';
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
			COALESCE(ID.status, J.status) != 'Completed'";
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



	function payment() {
		if (! Auth::hasAccess()) {
			setSessionError('NO_PERMISSION');
			redirect('main');
		}

		$this->_fields = array(
			'job_no'  => 'J.id2_format',
			'bill_no' => "CONCAT(V.id2_format, '/', V.id3)",
			'debit'   => 'DL.name',
			'credit'  => 'CL.name',
			'balance' => ''
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
		$data['rows'] = $this->_payment($search, $parsed_search);

		$data['page_title']  = $this->_class . " / Export / Container";
		$data['page']        = $this->_clspath.$this->_class.'_payment';
		$data['docs_url']    = $this->_docs;
		$this->load->view('index', $data);
	}

	function _payment($search, $parsed_search, $fields) {
		$sql = "SELECT CJ.job_id, CJ.id AS child_job_id, J.id2_format, PL.name AS party_name, EI.invoice_no, DATE_FORMAT(EI.invoice_date, '%d-%m-%Y') AS invoice_date, 
			CJ.sb_no, DATE_FORMAT(CJ.sb_date, '%d-%m-%Y') AS sb_date, IF(LENGTH(TRIM(CJ.bl_no)) = 0, 'Missing BL No', CJ.bl_no) AS bl_no, 
			DATE_FORMAT(CJ.bl_date, '%d-%m-%Y') AS bl_date, CARGO.name AS cargo_name, 
			GROUP_CONCAT(DISTINCT CONCAT(PC.containers, 'x', CT.size, CT.code) SEPARATOR ', ') AS containers,
			L.name AS line_name, V.name AS vessel_name, DATE_FORMAT(V.etd_date, '%d-%m-%Y') AS etd_date, 
			IP.name AS pol, PRT.name AS pod, CFS.name AS cfs_name,
			IC.last_fetched, IC.status AS icegate_status, IC.last_status, DATE_FORMAT(IC.leo_date, '%d-%m-%Y') AS leo_date, IC.ep_copy_print_status, IC.print_status
		FROM icegate_sb IC INNER JOIN child_jobs CJ ON IC.child_job_id = CJ.id
			INNER JOIN jobs J ON J.id = CJ.job_id
			INNER JOIN parties PL ON J.party_id = PL.id
			LEFT OUTER JOIN agents L ON J.line_id = L.id
			LEFT OUTER JOIN agents CFS ON J.cfs_id = CFS.id
			LEFT OUTER JOIN vessels V ON J.vessel_id = V.id
			LEFT OUTER JOIN indian_ports IP ON J.custom_port_id = IP.id
			LEFT OUTER JOIN ports PRT ON J.discharge_port_id = PRT.id
			LEFT OUTER JOIN products CARGO ON J.product_id = CARGO.id
			LEFT OUTER JOIN planned_containers PC ON J.id = PC.job_id
			LEFT OUTER JOIN container_types CT ON PC.container_type_id = CT.id
		WHERE (J.type = 'Export' AND J.cargo_type = 'Container' AND LENGTH(TRIM(CJ.sb_no)) > 0 AND 
			(LENGTH(TRIM(IC.leo_date)) = 0 OR TRIM(IC.leo_date) = 'N.A.')) 
			AND J.status != 'Completed'";
		$where = ' AND (';
		if (is_array($parsed_search)) {
			foreach($parsed_search as $key => $value)
				if (isset($fields[$key]))
					$where .= $fields[$key] . " LIKE '%$value%' AND ";
			if (strlen($where) > 6)
				$sql .= substr($where, 0, strlen($where) - 5) . ')';
		}
	
		$sql .= "
		GROUP BY J.id, CJ.id
		ORDER BY id2 DESC";
		$query = $this->db->query($sql);
		return $query->result_array();
	}
}
