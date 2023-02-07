<?php

class Import_process extends MY_Controller {
	var $_fields;

	function __construct() {
		parent::__construct();

		$this->load->helper('datefn');
	}

	function index() {
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
		$data['page']        = $this->_clspath.$this->_class;
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
}