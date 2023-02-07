<?php

class Pending extends MY_Controller {
	var $_b_map, $_c_map;
	var $_b_fields, $_c_fields;
	var $_parsed_search;

	function __construct() {
		parent::__construct();

		$this->_table = 'import_details';
		$this->load->model('import');
		$this->load->helper('datefn');

		$this->_b_map = array(
			'party'    => 'PL.name',
			'hss'      => 'HSL.name',
			'bl'       => 'J.bl_no',
			'category' => 'PRD.category',
			'product'  => 'PRD.name',
			'vessel'   => 'V.name',
			'port'     => 'IP.name',
			'shipper'  => 'J.vi_shipper_name',
			'status'   => 'IT.status'
		);
		$this->_b_fields = array(
			'party' 	=> 'Party Name',
			'hss'		=> 'High Seas Sale',
			'bl' 		=> 'BL No',
			'product'	=> 'Product',
			'vessel' 	=> 'Vessel Name',
			'port'		=> 'Port',
			'shipper' 	=> 'Shipper Name',
			'status' 	=> 'IceGate Status'
		);

		$this->_c_map = array(
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
			'status'  => 'J.status',
			'website' => 'WP.name',
		);
		$this->_c_fields = array(
			'party'   => 'Party Name',
			'hss'     => 'High Seas Sale',
			'bl'      => 'BL No',
			'vessel'  => 'Vessel Name',
			'line'    => 'Line Name',
			'cfs'     => 'CFS.name',
			'port'    => 'Port Name',
			'pod'     => 'Place of Delivery',
			'shipper' => 'Shipper Name',
			'remarks' => 'Remarks',
			'status'  => 'Status',
			'website' => 'Website Party Name',
		);
	}
	
	function index($cargo_type = 'Bulk') {
		$this->edit($cargo_type);
	}

	function edit($cargo_type = 'Bulk') {
		$starting_row = intval($starting_row);$search = $this->session->userdata($this->_class.'_search');
		$sortby = $this->session->userdata($this->_class.'_sortby');

		if($this->input->post('search_form')) {
			$search = addslashes($this->input->post('search'));
			$sortby = $this->input->post('sortby');
			$this->session->set_userdata($this->_class.'_search', $search);
			$this->session->set_userdata($this->_class.'_sortby', $sortby);
		}
		
		if ($search == null) {
			$search = '';
			$this->session->set_userdata($this->_class.'_search', $search);
		}

		if ($sortby == null) {
			$sortby = ($cargo_type == 'Bulk' ? 'V.name, PL.name' : 'V.eta_date, J.bl_date');
			$this->session->set_userdata($this->_class.'_sortby', $sortby);
		}
		$data['search'] = $search;
		$data['sortby'] = $sortby;
		$this->_parsed_search  = $this->kaabar->parseSearch($search);
		$data['parsed_search'] = $this->_parsed_search;

		if (is_array($this->_parsed_search)) {
			$search = '';
			foreach ($this->_parsed_search as $key => $value) {
				$search .= $key.':'.$value.' ';
			}
			$data['search'] = $search;
		}
		
		if ($this->input->post('bulk_form') == 1) {
			$this->_update_bulk();
			setSessionAlert('Changes saved successfully', 'success');
		}

		$data['label_class'] = array(
			'N.A.' 		=> 'label-default',
			'OOC' 		=> 'label-success',
			'EXAM'  	=> 'label-default',
			'PAYMENT' 	=> 'label-blue',
			'ASSESS'	=> 'label-warning',
			'APPRA' 	=> 'label-info',
		);

		if ($cargo_type == 'Bulk') {
			$this->load->helper('datefn');
			$data['search_fields']  = $this->_b_fields;
			$data['rows'] = $this->_bulkPendings($cargo_type, $search, $sortby);
		}
		else if ($cargo_type == 'Container') {
			$data['search_fields']  = $this->_c_fields;
			$data['rows'] = $this->_contPendings($cargo_type, $search, $sortby);
		}
		else { // if ($cargo_type == 'Tracking')
			$data['search_fields']  = $this->_c_fields;
			$data['rows'] = $this->_contTrackings($cargo_type, $search, $sortby);
		}

		$data['auto_refresh'] = 300;
		
		$data['cargo_type']  = $cargo_type;
		$data['docs_url']    = $this->_docs;
		$data['page_title']  = $this->_class . " - Import - $cargo_type";
		$data['hide_title']  = true;
		$data['hide_footer'] = true;
		$data['page']        = $this->_clspath.$this->_class.'_'.strtolower($cargo_type);
		$this->load->view('index', $data);
	}
	
	function _bulkPendings($cargo_type, $search = '', $sortby = '') {
		$sql = "SELECT P.id, P.job_id, J.id2_format, PL.name AS party_name, 
			GROUP_CONCAT(DISTINCT HSL.name ORDER BY HSL.id SEPARATOR '<br />') AS high_seas, PRD.category,
			PRD.name AS product, J.details, IP.name AS port, IP.name AS indian_port, J.packages, PK.code AS package_type, 
			IF(J.cbm > 0, J.cbm, J.net_weight) AS cbm, J.net_weight, J.net_weight_unit, J.house_bl,
			IF(LENGTH(TRIM(J.bl_no)) = 0, 'Missing BL No', J.bl_no) AS bl_no, DATE_FORMAT(J.bl_date, '%d-%m-%Y') AS bl_date, 
			J.be_no, IT.last_fetched, IT.last_status AS last_status, IT.status AS current_status, IT.section_48, IT.query_raised, 
			IC.id AS challan_id, J.vessel_id, V.name AS vessel_name, DATE_FORMAT(V.eta_date, '%d-%m-%Y') AS eta_date, V.voyage_no, 
			P.free_days, DATE_FORMAT(IF(ISNULL(V.eta_date), DATE_ADD(P.temp_eta, INTERVAL (P.free_days-1) DAY), DATE_ADD(V.eta_date, INTERVAL (P.free_days-1) DAY)), '%d-%m-%Y') AS free_days_upto,	IF(ISNULL(SL.id), J.vi_shipper_name, SL.name) AS shipper_name, J.status, DATE_FORMAT(P.original_bl_received, '%d-%m-%Y') AS original_bl_received, P.remarks,	SL.name AS line_name, CFS.name AS cfs_name, P.custom_duty, P.ppq, P.stamp_duty, P.wharfage,
			(P.custom_duty + P.ppq + P.stamp_duty + P.wharfage) AS total,
			CD.completed_documents, PD.pending_documents, DO.do_no, DATE_FORMAT(DO.date, '%d-%m-%Y') AS delivery_date, DATE_FORMAT(COALESCE(DO.validity, '0000-00-00'), '%d-%m-%Y') AS validity, DO.empty_return_park
		FROM import_details P
			INNER JOIN jobs J ON P.job_id = J.id
			INNER JOIN parties PL ON J.party_id = PL.id
			INNER JOIN products PRD ON J.product_id = PRD.id
			INNER JOIN indian_ports IP ON J.indian_port_id = IP.id
			INNER JOIN package_types PK ON J.package_type_id = PK.id
			LEFT OUTER JOIN agents SL ON J.shipper_id = SL.id
			LEFT OUTER JOIN agents CFS ON J.cfs_id = CFS.id
			LEFT OUTER JOIN vessels V ON J.vessel_id = V.id
			LEFT OUTER JOIN high_seas HS ON P.job_id = HS.job_id
			LEFT OUTER JOIN parties HSL ON HS.party_id = HSL.id
			LEFT OUTER JOIN icegate_be IT ON J.id = IT.job_id
			LEFT OUTER JOIN delivery_orders DO ON J.id = DO.job_id
			LEFT OUTER JOIN icegate_challans IC ON IT.challan_no = IC.challan_no
			LEFT OUTER JOIN (
				SELECT AD.job_id, GROUP_CONCAT(CONCAT('<strong>', DT.code, '</strong>', IF(LENGTH(AD.remarks) > 0, CONCAT(' (', AD.remarks, ')'), '')) ORDER BY DT.sr_no, AD.id SEPARATOR ', ') AS completed_documents 
				FROM (jobs J INNER JOIN attached_documents AD ON (J.type = 'Import' AND J.cargo_type = '$cargo_type' AND AD.job_id= J.id))
					INNER JOIN document_types DT ON AD.document_type_id = DT.id
				WHERE AD.is_pending = 'Yes' AND AD.received = 'Yes'
				GROUP BY AD.job_id
			) CD ON P.job_id = CD.job_id
			LEFT OUTER JOIN (
				SELECT AD.job_id, GROUP_CONCAT(CONCAT('<strong>', DT.code, '</strong>', IF(LENGTH(AD.remarks) > 0, CONCAT(' (', AD.remarks, ')'), '')) ORDER BY DT.sr_no, AD.id SEPARATOR ', ') AS pending_documents 
				FROM (jobs J INNER JOIN attached_documents AD ON (J.type = 'Import' AND J.cargo_type = '$cargo_type' AND AD.job_id= J.id))
					INNER JOIN document_types DT ON AD.document_type_id = DT.id
				WHERE AD.is_pending = 'Yes' AND AD.received = 'No'
				GROUP BY AD.job_id
			) PD ON P.job_id = PD.job_id
		WHERE (J.type = 'Import' AND J.cargo_type = '$cargo_type')";
				$where = ' AND (';
				if (is_array($this->_parsed_search)) {
					foreach($this->_parsed_search as $key => $value)
						if (isset($this->_b_map[$key]))
							$where .= $this->_b_map[$key] . " LIKE '%$value%' AND ";
					if (strlen($where) > 6)
						$sql .= substr($where, 0, strlen($where) - 5) . ')';
				}
				else {
					$sql .= $where . "PL.name LIKE '%$search%' OR
			HSL.name LIKE '%$search%' OR
			J.bl_no LIKE '%$search%' OR
			J.be_no LIKE '%$search%' OR
			V.name  LIKE '%$search%' OR
			SL.name LIKE '%$search%')";
				}
				$sql .= " AND (
				NOT ISNULL(PD.pending_documents) OR 
				ISNULL(IT.ooc_date) OR 
				LENGTH(TRIM(IT.ooc_date)) = 0 OR 
				TRIM(IT.ooc_date) = 'N.A.'
			)
		GROUP BY P.job_id
		ORDER BY J.be_no DESC, $sortby";
		$query  = $this->db->query($sql);
		$rows   = $query->result_array();
		$result = array(0);
		foreach ($rows as $r) {
			$result[$r['job_id']] = $r;
			$result[$r['job_id']]['delivery'] = array(
				'job_id'          => 0,
				'net_weight'      => 0,
				'dispatch_weight' => 0,
				'last_in_date'    => '',
			);
			$result[$r['job_id']]['expenses'] = 0;
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

		unset($result[0]);
		return $result;
	}
	

	function _contPendings($cargo_type, $search = '', $sortby = '') {
		$sql = "SELECT P.id, P.job_id, J.id2_format, PL.name AS party_name, GROUP_CONCAT(DISTINCT HSL.name ORDER BY HSL.id SEPARATOR '<br />') AS high_seas, 
			COALESCE(SHIPPER.name, J.vi_shipper_name) AS shipper_name, 
			CONCAT(IF(J.container_20 > 0, CONCAT(J.container_20, 'x20 '), ''), IF(J.container_40 > 0, CONCAT(J.container_40, 'x40 '), '')) AS containers,
			(J.container_20 + J.container_40) AS total_containers, J.net_weight, J.net_weight_unit,
			J.house_bl, IF(LENGTH(TRIM(J.bl_no)) = 0, 'Missing BL No', J.bl_no) AS bl_no, DATE_FORMAT(bl_date, '%d-%m-%Y') AS bl_date, 
			J.be_no, IT.section_48, IT.query_raised, IT.last_fetched, IT.last_status AS last_status, IT.status AS current_status, IC.id AS challan_id, 
			J.vessel_id, P.temp_vessel_name, DATE_FORMAT(temp_eta, '%d-%m-%Y') AS temp_eta,  V.name AS vessel_name, V.voyage_no, DATE_FORMAT(V.eta_date, '%d-%m-%Y') AS eta_date, 
			IP.name AS indian_port, P.place_of_delivery, CFS.name AS cfs_name,  SL.name AS line_name, P.custom_duty, 
			P.free_days, DATE_FORMAT(IF(ISNULL(V.eta_date), DATE_ADD(P.temp_eta, INTERVAL (P.free_days-1) DAY), DATE_ADD(V.eta_date, INTERVAL (P.free_days-1) DAY)), '%d-%m-%Y') AS free_days_upto, DATE_FORMAT(P.original_bl_received, '%d-%m-%Y') AS original_bl_received,
			CD.completed_documents, PD.pending_documents, P.remarks, J.status, 
			DO.do_no, DATE_FORMAT(DO.date, '%d-%m-%Y') AS delivery_date, DATE_FORMAT(COALESCE(DO.validity, '0000-00-00'), '%d-%m-%Y') AS validity, DO.empty_return_park
		FROM import_details P
			INNER JOIN jobs J ON P.job_id = J.id
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
			LEFT OUTER JOIN (
				SELECT AD.job_id, GROUP_CONCAT(CONCAT('<strong>', DT.code, '</strong>', IF(LENGTH(AD.remarks) > 0, CONCAT(' (', AD.remarks, ')'), '')) ORDER BY DT.sr_no, AD.id SEPARATOR ', ') AS completed_documents 
				FROM attached_documents AD INNER JOIN document_types DT ON AD.document_type_id = DT.id
				WHERE AD.is_pending = 'Yes' AND AD.received = 'Yes'
				GROUP BY AD.job_id
			) CD ON P.job_id = CD.job_id
			LEFT OUTER JOIN (
				SELECT AD.job_id, GROUP_CONCAT(CONCAT('<strong>', DT.code, '</strong>', IF(LENGTH(AD.remarks) > 0, CONCAT(' (', AD.remarks, ')'), '')) ORDER BY DT.sr_no, AD.id SEPARATOR ', ') AS pending_documents 
				FROM attached_documents AD INNER JOIN document_types DT ON AD.document_type_id = DT.id
				WHERE AD.is_pending = 'Yes' AND AD.received = 'No'
				GROUP BY AD.job_id
			) PD ON P.job_id = PD.job_id
		WHERE (J.type = 'Import' AND J.cargo_type = '$cargo_type' AND LENGTH(J.be_no) > 0)";
				$where = ' AND (';
				if (is_array($this->_parsed_search)) {
					foreach($this->_parsed_search as $key => $value)
						if (isset($this->_c_map[$key]))
							$where .= $this->_c_map[$key] . " LIKE '%$value%' AND ";
					if (strlen($where) > 6)
						$sql .= substr($where, 0, strlen($where) - 5) . ')';
				}
				else {
					$sql .= $where . "PL.name LIKE '%$search%' OR
			HSL.name LIKE '%$search%' OR
			J.bl_no LIKE '%$search%' OR
			J.be_no LIKE '%$search%' OR
			V.name LIKE '%$search%' OR
			P.temp_vessel_name LIKE '%$search%' OR
			IP.name LIKE '%$search%' OR
			SL.name LIKE '%$search%')";
				}
			
				$sql .= ((isset($this->_parsed_search['status']) && $this->_parsed_search['status'] == 'Completed') ? '' : " AND (J.status != 'Completed')") . "
		GROUP BY P.job_id
		ORDER BY IF(J.status = 'Program', 1, IF(J.status = 'Pending', 2, IF(J.status = 'Delivery', 3, 4))), $sortby " . 
		((isset($this->_parsed_search['status']) && $this->_parsed_search['status'] == 'Completed') ? 'LIMIT 0, 50' : '');
		$query  = $this->db->query($sql);
		$rows   = $query->result_array();
		$result = array(0);
		foreach ($rows as $r) {
			$result[$r['job_id']] = $r;
			$result[$r['job_id']]['delivery'] = array(
				'job_id'          => 0,
				'container_20'    => 0,
				'container_40'    => 0,
				'net_weight'      => 0,
				'dispatch_weight' => 0,
				'last_in_date'    => '',
			);
			$result[$r['job_id']]['expenses'] = 0;
		}

		// Fetch Deliveries
		$sql = "SELECT C.job_id, 
			COUNT(IF(CT.size = 20 AND D.cfs_in_date != '0000-00-00 00:00:00', C.id, NULL)) AS container_20, 
			COUNT(IF(CT.size = 40 AND D.cfs_in_date != '0000-00-00 00:00:00', C.id, NULL)) AS container_40,
			ROUND(SUM(C.net_weight), 2) AS net_weight, ROUND(SUM(D.dispatch_weight), 2) AS dispatch_weight,
			DATE_FORMAT(MAX(D.cfs_in_date), '%d-%m-%Y') AS last_in_date
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

		unset($result[0]);
		return $result;
	}


	function _contTrackings($cargo_type, $search = '', $sortby = '') {
		$sql = "SELECT P.id, P.job_id, J.id2_format, PL.name AS party_name, GROUP_CONCAT(DISTINCT HSL.name ORDER BY HSL.id SEPARATOR '<br />') AS high_seas, 
			COALESCE(SHIPPER.name, J.vi_shipper_name) AS shipper_name, CFS.name AS cfs_name, 
			IF(J.cbm > 0, J.cbm, J.net_weight) AS cbm, J.net_weight, J.net_weight_unit, 
			(J.container_20 + J.container_40) AS total_containers,
			CONCAT(IF(J.container_20 > 0, CONCAT(J.container_20, 'x20 '), ''), IF(J.container_40 > 0, CONCAT(J.container_40, 'x40 '), '')) AS containers,
			J.house_bl, IF(LENGTH(TRIM(J.bl_no)) = 0, 'Missing BL No', J.bl_no) AS bl_no, DATE_FORMAT(bl_date, '%d-%m-%Y') AS bl_date, 
			J.vessel_id, P.temp_vessel_name, DATE_FORMAT(P.temp_eta, '%d-%m-%Y') AS temp_eta,  V.name AS vessel_name, V.voyage_no, DATE_FORMAT(V.eta_date, '%d-%m-%Y') AS eta_date, 
			IP.name AS indian_port, P.place_of_delivery, SL.name AS line_name, CHA.name AS cha_name, 
			P.free_days, DATE_FORMAT(IF(ISNULL(V.eta_date), DATE_ADD(P.temp_eta, INTERVAL (P.free_days-1) DAY), DATE_ADD(V.eta_date, INTERVAL (P.free_days-1) DAY)), '%d-%m-%Y') AS free_days_upto, DATE_FORMAT(P.original_bl_received, '%d-%m-%Y') AS original_bl_received,
			P.remarks, J.status
		FROM import_details P
			INNER JOIN jobs J ON P.job_id = J.id
			INNER JOIN parties PL ON J.party_id = PL.id
			LEFT OUTER JOIN agents SHIPPER ON J.shipper_id = SHIPPER.id
			LEFT OUTER JOIN agents SL ON J.line_id = SL.id
			LEFT OUTER JOIN agents CFS ON J.cfs_id = CFS.id
			LEFT OUTER JOIN agents CHA ON J.cha_id = CHA.id
			LEFT OUTER JOIN vessels V ON J.vessel_id = V.id
			LEFT OUTER JOIN indian_ports IP ON J.indian_port_id = IP.id
			LEFT OUTER JOIN high_seas HS ON P.job_id = HS.job_id
			LEFT OUTER JOIN parties HSL ON HS.party_id = HSL.id
			LEFT OUTER JOIN parties WP ON J.web_party_id = WP.id
		WHERE (J.type = 'Import' AND J.cargo_type = 'Container' AND LENGTH(J.be_no) = 0)";
				$where = ' AND (';
				if (is_array($this->_parsed_search)) {
					foreach($this->_parsed_search as $key => $value)
						if (isset($this->_c_map[$key]))
							$where .= $this->_c_map[$key] . " LIKE '%$value%' AND ";
					if (strlen($where) > 6)
						$sql .= substr($where, 0, strlen($where) - 5) . ')';
				}
				else {
					$sql .= $where . "PL.name LIKE '%$search%' OR
			HSL.name LIKE '%$search%' OR
			J.bl_no LIKE '%$search%' OR
			V.name LIKE '%$search%' OR
			P.temp_vessel_name LIKE '%$search%' OR
			IP.name LIKE '%$search%' OR
			SL.name LIKE '%$search%')";
				}
			
				$sql .= " AND (
				J.status != 'Completed'
			)
		GROUP BY P.job_id
		ORDER BY (ISNULL(V.eta_date) OR V.eta_date = '0000-00-00'), V.eta_date, (P.temp_eta = '0000-00-00'), P.temp_eta, $sortby";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function _contTrackingExcel($cargo_type, $search = '', $sortby = '') {
		$sql = "SELECT P.id, P.job_id, J.id2_format, PL.name AS customer_name, GROUP_CONCAT(DISTINCT HSL.name ORDER BY HSL.id SEPARATOR ' >> ') AS high_seas, 
			COALESCE(SHIPPER.name, J.vi_shipper_name) AS shipper_name, CFS.name AS cfs_name, (J.container_20 + J.container_40) AS total_containers,
			J.bl_no, CONCAT(IF(J.container_20 > 0, CONCAT(J.container_20, 'x20 '), ''), IF(J.container_40 > 0, CONCAT(J.container_40, 'x40 '), '')) AS containers,
			J.net_weight, J.net_weight_unit, SL.name AS shipping_line, 
			IF(ISNULL(V.name), P.temp_vessel_name, V.name) AS vessel_name, 
			CONCAT(IP.name, IF(LENGTH(P.place_of_delivery) > 0, CONCAT(' >> ', P.place_of_delivery), '')) AS place_of_discharge, 	
			DATE_FORMAT(IF(ISNULL(V.eta_date), DATE_ADD(P.temp_eta, INTERVAL (P.free_days-1) DAY), DATE_ADD(V.eta_date, INTERVAL (P.free_days-1) DAY)), '%d-%b-%Y') AS free_days_upto,
			IF(ISNULL(V.eta_date), DATE_FORMAT(P.temp_eta, '%d-%b-%Y'), DATE_FORMAT(V.eta_date, '%d-%b-%Y')) AS eta_date, 
			IF(P.original_bl_received = '0000-00-00', 'No', 'Yes') AS original_doc_rcvd,
			P.remarks AS current_status
		FROM import_details P
			INNER JOIN jobs J ON P.job_id = J.id
			INNER JOIN parties PL ON J.party_id = PL.id
			LEFT OUTER JOIN agents SHIPPER ON J.shipper_id = SHIPPER.id
			LEFT OUTER JOIN agents SL ON J.line_id = SL.id
			LEFT OUTER JOIN agents CFS ON J.cfs_id = CFS.id
			LEFT OUTER JOIN vessels V ON J.vessel_id = V.id
			LEFT OUTER JOIN indian_ports IP ON J.indian_port_id = IP.id
			LEFT OUTER JOIN high_seas HS ON P.job_id = HS.job_id
			LEFT OUTER JOIN parties HSL ON HS.party_id = HSL.id
			LEFT OUTER JOIN parties WP ON J.web_party_id = WP.id
		WHERE (J.type = 'Import' AND J.cargo_type = 'Container' AND LENGTH(J.be_no) = 0)";
				$where = ' AND (';
				if (is_array($this->_parsed_search)) {
					foreach($this->_parsed_search as $key => $value)
						if (isset($this->_c_map[$key]))
							$where .= $this->_c_map[$key] . " LIKE '%$value%' AND ";
					if (strlen($where) > 6)
						$sql .= substr($where, 0, strlen($where) - 5) . ')';
				}
				else {
					$sql .= $where . "PL.name LIKE '%$search%' OR
			HSL.name LIKE '%$search%' OR
			J.bl_no LIKE '%$search%' OR
			V.name LIKE '%$search%' OR
			P.temp_vessel_name LIKE '%$search%' OR
			IP.name LIKE '%$search%' OR
			SL.name LIKE '%$search%')";
				}
			
				$sql .= " AND (
				J.status != 'Completed'
			)
		GROUP BY P.job_id
		ORDER BY (ISNULL(V.eta_date) OR V.eta_date = '0000-00-00'), V.eta_date, (P.temp_eta = '0000-00-00'), P.temp_eta, $sortby";

		$query = $this->db->query($sql);
		return $query;
	}

	function _contPendingExcel($cargo_type, $search = '', $sortby = '') {
		$sql = "SELECT J.id2_format, PL.name AS party_name, GROUP_CONCAT(DISTINCT HSL.name ORDER BY HSL.id SEPARATOR ', ') AS high_seas, 
			CONCAT(IF(J.container_20 > 0, CONCAT(J.container_20, 'x20 '), ''), IF(J.container_40 > 0, CONCAT(J.container_40, 'x40 '), '')) AS containers,
			J.house_bl, IF(LENGTH(TRIM(J.bl_no)) = 0, 'Missing BL No', J.bl_no) AS bl_no, DATE_FORMAT(bl_date, '%d-%m-%Y') AS bl_date, 
			J.be_no, IF(ISNULL(V.name), P.temp_vessel_name, CONCAT(V.prefix, ' ', V.name, ' ', V.voyage_no)) AS vessel_name, 
			CONCAT(IP.name, IF(LENGTH(P.place_of_delivery) > 0, CONCAT(' >> ', P.place_of_delivery), '')) AS place_of_discharge, 	
			IF(ISNULL(V.eta_date), DATE_FORMAT(P.temp_eta, '%d-%b-%Y'), DATE_FORMAT(V.eta_date, '%d-%b-%Y')) AS eta_date, 
			IP.name AS indian_port, SL.name AS line_name, CFS.name AS cfs_name,
			P.custom_duty, P.ppq, P.line_payment, P.cfs_payment, 
			(P.custom_duty + P.ppq + P.line_payment + P.cfs_payment) AS total,
			P.free_days, DATE_FORMAT(IF(ISNULL(V.eta_date), DATE_ADD(P.temp_eta, INTERVAL (P.free_days-1) DAY), DATE_ADD(V.eta_date, INTERVAL (P.free_days-1) DAY)), '%d-%m-%Y') AS free_days_upto, DATE_FORMAT(P.original_bl_received, '%d-%m-%Y') AS original_bl_received,
			CD.completed_documents, PD.pending_documents, P.remarks, J.status
		FROM import_details P
			INNER JOIN jobs J ON P.job_id = J.id
			INNER JOIN parties PL ON J.party_id = PL.id
			LEFT OUTER JOIN agents SL ON J.line_id = SL.id
			LEFT OUTER JOIN agents CFS ON J.cfs_id = CFS.id
			LEFT OUTER JOIN vessels V ON J.vessel_id = V.id
			LEFT OUTER JOIN indian_ports IP ON J.indian_port_id = IP.id
			LEFT OUTER JOIN high_seas HS ON P.job_id = HS.job_id
			LEFT OUTER JOIN parties HSL ON HS.party_id = HSL.id
			LEFT OUTER JOIN parties WP ON J.web_party_id = WP.id
			LEFT OUTER JOIN icegate_be IT ON J.id = IT.job_id
			LEFT OUTER JOIN icegate_challans IC ON IT.challan_no = IC.challan_no
			LEFT OUTER JOIN (
				SELECT AD.job_id, GROUP_CONCAT(CONCAT(DT.code, ':', IF(LENGTH(AD.remarks) > 0, CONCAT(' (', AD.remarks, ')'), '')) ORDER BY DT.sr_no, AD.id SEPARATOR ', ') AS completed_documents 
				FROM attached_documents AD INNER JOIN document_types DT ON AD.document_type_id = DT.id
				WHERE AD.is_pending = 'Yes' AND AD.received = 'Yes'
				GROUP BY AD.job_id
			) CD ON P.job_id = CD.job_id
			LEFT OUTER JOIN (
				SELECT AD.job_id, GROUP_CONCAT(CONCAT(DT.code, ':', IF(LENGTH(AD.remarks) > 0, CONCAT(' (', AD.remarks, ')'), '')) ORDER BY DT.sr_no, AD.id SEPARATOR ', ') AS pending_documents 
				FROM attached_documents AD INNER JOIN document_types DT ON AD.document_type_id = DT.id
				WHERE AD.is_pending = 'Yes' AND AD.received = 'No'
				GROUP BY AD.job_id
			) PD ON P.job_id = PD.job_id
		WHERE (J.type = 'Import' AND J.cargo_type = '$cargo_type' AND LENGTH(J.be_no) > 0)";
				$where = ' AND (';
				if (is_array($this->_parsed_search)) {
					foreach($this->_parsed_search as $key => $value)
						if (isset($this->_c_map[$key]))
							$where .= $this->_c_map[$key] . " LIKE '%$value%' AND ";
					if (strlen($where) > 6)
						$sql .= substr($where, 0, strlen($where) - 5) . ')';
				}
				else {
					$sql .= $where . "PL.name LIKE '%$search%' OR
			HSL.name LIKE '%$search%' OR
			J.bl_no LIKE '%$search%' OR
			J.be_no LIKE '%$search%' OR
			V.name LIKE '%$search%' OR
			P.temp_vessel_name LIKE '%$search%' OR
			IP.name LIKE '%$search%' OR
			SL.name LIKE '%$search%')";
		}
	
		$sql .= ((isset($this->_parsed_search['status']) && $this->_parsed_search['status'] == 'Completed') ? '' : " AND (J.status != 'Completed')") . "
			GROUP BY P.job_id
			ORDER BY (ISNULL(V.eta_date) OR V.eta_date = '0000-00-00'), V.eta_date, (P.temp_eta = '0000-00-00'), P.temp_eta, $sortby " . 
			((isset($this->_parsed_search['status']) && $this->_parsed_search['status'] == 'Completed') ? 'LIMIT 0, 50' : '');
		$query = $this->db->query($sql);
		return $query;
	}


	function _update_bulk() {
		$id = intval($this->input->post('do_payment_id'));
		if ($id > 0) {
			$row = $this->kaabar->getRow($this->_table, $id);
			$data = array();

			$percent = intval($this->input->post('do_percent'));
			if ($percent > 0) {
				if ($percent > 100)	$percent = 100;
				$this->kaabar->save('attached_documents', array(
					'job_id' => $row['job_id'], 
					'date' 	 => date('d-m-Y'),
					'doc_no' => $this->input->post('do_no'),
					'document_type_id' => 35, 
					'remarks' => $percent . '%, ' . $this->input->post('do_pieces') . ' Pcs, ' . $this->input->post('do_cbm') . ' CBM', 
					'received' => 'Yes',
					'received_date' => $this->input->post('do_date'),
					'is_pending' => 'Yes')
				);

				$do = array(
					'job_id'     => $row['job_id'], 
					'do_no'      => $this->input->post('do_no'),
					'date'       => date('d-m-Y'),
					'percentage' => $percent, 
					'pieces'     => $this->input->post('do_pieces'),
					'cbm'        => $this->input->post('do_cbm')
				);
				$this->kaabar->save('delivery_orders', $do);
			}
		}
	}


	function preview($cargo_type = 'Bulk', $pdf = 0) {
		$search = $this->session->userdata($this->_class.'_search');
		$sortby = $this->session->userdata($this->_class.'_sortby');
		$this->_parsed_search = $this->kaabar->parseSearch($search);

		$default_company = $this->session->userdata('default_company');
		$data['company'] = $this->kaabar->getRow('companies', $default_company['id']);

		if ($cargo_type == 'Bulk') {
			$data['rows'] = $this->_bulkPendings($cargo_type, $search, $sortby);
			$data['page'] = 'reports/pending_' . underscore($cargo_type) . '_preview';
		}
		if ($cargo_type == 'Container') {
			$data['rows'] = $this->_contPendings($cargo_type, $search, $sortby);
			$data['page'] = 'reports/pending_' . underscore($cargo_type) . '_preview';
		}
		else if ($cargo_type == 'Tracking') {
			$query        = $this->_contTrackingExcel($cargo_type, $search, $sortby);
			$data['rows'] = $query->result_array();
			$data['page'] = 'reports/' . underscore($cargo_type) . '_preview';
		}
		
		$data['page_title'] = humanize($this->_class . ' Report');
		$data['filename']   = strtolower($cargo_type . '_' . (strlen($search) > 0 ? $search . '_' : '') . date('d-m-Y'));
		
		$this->_preview($data, $pdf);
	}


	function excel($cargo_type = 'Bulk') {
		$search = $this->session->userdata($this->_class.'_search');
		$sortby = $this->session->userdata($this->_class.'_sortby');
		$this->_parsed_search = $this->kaabar->parseSearch($search);

		if ($cargo_type == 'Tracking') {
			$query = $this->_contTrackingExcel($cargo_type, $search, $sortby);
			$rows = $query->result_array();
			$header = array_keys($rows[0]);
		}
		else if ($cargo_type == 'Bulk') {
			$rows = $this->_bulkPendings($cargo_type, $search, $sortby);
			$header = array_keys(reset($rows));
		}
		else {
			$query = $this->_contPendingExcel($cargo_type, $search, $sortby);
			$rows = $query->result_array();
			$header = array_keys($rows[0]);
		}

		$filename = $cargo_type . '_' . (strlen($search) > 0 ? $search . '_' : '') . date('d-m-Y') . ".xlsx";
		$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
		$sheet       = $spreadsheet->getActiveSheet();
		
		$styleSheet = [
			'font' => [
				'name' => 'Times New Roman',
				'size' => 10
			],
		];

		$styleHeading = [
			'font' => [
				'bold' => true,
			],
			'alignment' => [
				'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
			]
		];

		$styleYellow = [
			'fill' => [
				'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
				'startColor' => [
					'argb' => 'FFFFFF88',
				],
			]
		];


		// Header
		$last_col = 'A';
		foreach ($header as $i => $h) {
			$sheet->setCellValue($last_col++.'1', humanize($h));
			$sheet->getColumnDimensionByColumn($i)->setAutoSize(true);
		}
		$last_col--;
		$sheet->getStyle('A1:' . $last_col . '1')->applyFromArray($styleHeading);
		
		// Data
		$i = 2;
		foreach ($rows as $row) {
			$j = 'A';
			foreach ($row as $f => $v) {
				if ($f == 'id2_format') {
					$sheet->setCellValueExplicit($j++.$i, $v, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
				}
				else {
					$sheet->setCellValue($j++.$i, html_entity_decode(str_replace('<strong>', '', str_replace('</strong>', '', $v))));
				}
				if ($f == 'eta_date' && strlen($v) != 0 && $v != '00-00-0000' && daysDiff(date('d-m-Y'), $v, 'd-m-Y') <= 1) {
					$sheet->getStyle('A'.$i.':'.$last_col.$i)->applyFromArray($styleYellow);
				}
			}
			$i++;
		}
		$sheet->getStyle('A1:' . $last_col.$i)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
		$sheet->getStyle('A1:' . $last_col.$i)->applyFromArray($styleSheet);

		// redirect output to client browser
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="' . $filename.'"');
		header('Cache-Control: max-age=0');
		$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
		$writer->save('php://output');
	}

	function email($cargo_type = 'Bulk') {
		$search = $this->session->userdata($this->_class.'_search');
		$sortby = $this->session->userdata($this->_class.'_sortby');
		$this->_parsed_search = $this->kaabar->parseSearch($search);

		$default_company = $this->session->userdata('default_company');
		$data['company'] = $this->kaabar->getRow('companies', $default_company['id']);

		if ($cargo_type == 'Bulk') {
			$data['rows'] = $this->_bulkPendings($cargo_type, $search, $sortby);
			$page = 'reports/pending_' . underscore($cargo_type) . '_preview';
		}
		if ($cargo_type == 'Container') {
			$data['rows'] = $this->_contPendings($cargo_type, $search, $sortby);
			$page = 'reports/pending_' . underscore($cargo_type) . '_preview';
		}
		else if ($cargo_type == 'Tracking') {
			$query        = $this->_contTrackingExcel($cargo_type, $search, $sortby);
			$data['rows'] = $query->result_array();
			$page = 'reports/' . underscore($cargo_type) . '_preview';
		}
		
		$data['page_title'] = humanize($this->_class . ' Report');
		$html = $this->load->view($page, $data, true);
		$this->load->helper(array('file', 'email'));

		$to      = $this->kaabar->checkEmail(explode(';', str_replace(' ', '', $this->input->post('to'))));
		$cc      = $this->kaabar->checkEmail(explode(';', str_replace(' ', '', $this->input->post('cc'))));
		$bcc     = $this->kaabar->checkEmail(explode(';', str_replace(' ', '', $this->input->post('bcc'))));
		$subject = $this->input->post('subject');
		$message = $this->input->post('message');
		$message .= '<hr />' . $html;

		if (count($to) > 0) {
			$config = array(
				'protocol'     => 'smtp',
				'smtp_timeout' => 30,
				'smtp_host'    => Settings::get('smtp_host'),
				'smtp_port'    => Settings::get('smtp_port'),
				'smtp_user'    => Settings::get('smtp_user'),
				'smtp_pass'    => Settings::get('smtp_password'),
				'newline'      => "\r\n",
				'crlf'         => "\r\n",
				'mailtype'     => "html"
			);

			$this->load->library('email', $config);
			$this->email->from(Settings::get('smtp_user'));
			$this->email->to($to);
			$this->email->cc($cc);
			$this->email->bcc($bcc);
			$this->email->subject($subject);
			$this->email->message($message);
			
			$this->email->send();
			//echo $this->email->print_debugger(); exit;
			setSessionAlert('Email has been sent To: &lt;' . implode(', ', $to) . '&gt;...', 'success');
		}
		redirect($this->agent->referrer());
	}

	function updateStatus() {
		$this->db->update('jobs', 
			array('status' => $this->input->post('status')),
			array('id'     => $this->input->post('row_id'))
		);

		redirect($this->agent->referrer());
	}

	function updateDO() {
		$id        = $this->input->post('row_id');
		$job_id    = $this->input->post('job_id');
		$do_no     = $this->input->post('do_no');
		$do_date   = $this->input->post('do_date');
		$do_upto   = $this->input->post('do_upto');
		$do_erpark = $this->input->post('do_empty_return_park');

		if ($do_upto != null) {
			if (strlen($do_upto) == 10 && $do_upto != '00-00-0000') {
				// Attach Document
				$do = $this->kaabar->getRow('attached_documents', array('job_id' => $job_id, 'document_type_id' => 35));
				if (! $do) {
					$do = array(
						'id'               => 0,
						'job_id'           => $job_id, 
						'date'             => date('d-m-Y'),
						'doc_no'           => $do_no,
						'document_type_id' => 35,
						'remarks'          => 'Upto: ' . str_replace('-', '/', $do_upto), 
						'received'         => 'Yes',
						'received_date'    => $do_date,
						'is_pending'       => 'Yes'
					);
				}
				else {
					$do['date']          = date('d-m-Y');
					$do['doc_no']        = $do_no;
					$do['remarks']       = 'Upto: ' . str_replace('-', '/', $do_upto);
					$do['received']      = 'Yes';
					$do['received_date'] = $do_date;
					$do['is_pending']    = 'Yes';
				}
				$this->kaabar->save('attached_documents', $do, array('id' => $do['id']));

				// Delivery Order
				if (strlen($do_upto) == 10 && $do_upto != '00-00-0000') {
					$do = $this->kaabar->getRow('delivery_orders', $job_id, 'job_id');
					if (! $do) {
						$do = array(
							'id'       => 0,
							'job_id'   => $job_id, 
							'do_no'    => $do_no,
							'date'     => date('d-m-Y'),
							'validity' => $do_upto, 
							'empty_return_park' => $do_erpark, 
						);
					}
					else {
						$do['do_no']    = $do_no;
						$do['date']     = date('d-m-Y');
						$do['validity'] = $do_upto;
						$do['empty_return_park'] = $do_erpark;
					}
					$this->kaabar->save('delivery_orders', $do, array('id' => $do['id']));
				}
			}
		}

		redirect($this->agent->referrer());
	}
	
	function updateETA() {
		$data = array();

		$data['temp_vessel_name'] = $this->input->post('vessel');
		$data['temp_eta']         = $this->input->post('eta');
		$data['free_days']        = $this->input->post('free_days');
		$data['remarks']          = $this->input->post('remarks');

		// if ($status[$id] == 'Program') {
		// 	$icegate = $this->kaabar->getField('icegate_be', $row['job_id'], 'job_id', 'status');
		// 	if ($icegate == 'OOC' && strlen($do_uptos[$id]) == 10 && $do_uptos[$id] != '00-00-0000')
		// 		$data['status'] = 'Delivery';
		// }

		// original_bl_received should not be 3 days old from current date.
		if (Auth::isAdmin())
			$data['original_bl_received'] 	= $this->input->post('original_bl_received');
		else if(daysDiff($original_bl_receiveds[$id], date('d-m-Y'), 'd-m-Y') <= 3)
			$data['original_bl_received'] 	= $this->input->post('original_bl_received');

		$data['id'] = $this->input->post('row_id');
		$this->kaabar->save($this->_table, $data, array('id' => $data['id']));

		redirect($this->agent->referrer());
	}
}
