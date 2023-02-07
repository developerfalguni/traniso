<?php

class Import_jobs extends MY_Controller {
	function __construct() {
		parent::__construct();

		$this->load->helper('datefn');
	}

	function index() {
		$this->_fields = array(
			'party'   => 'PL.name',
			'hss'     => 'HSL.name',
			'bl'      => 'J.bl_no',
			'vessel'  => 'V.name',
			'line'    => 'SL.name',
			'port'    => 'IP.name',
			'remarks' => 'P.remarks',
			'status'  => 'J.status',
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
		$data['page']        = $this->_clspath.$this->_class;
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
			P.remarks, J.status
		FROM import_details P INNER JOIN jobs J ON P.job_id = J.id
			INNER JOIN parties PL ON J.party_id = PL.id
			LEFT OUTER JOIN agents SL ON J.line_id = SL.id
			LEFT OUTER JOIN agents CHA ON J.cha_id = CHA.id
			LEFT OUTER JOIN vessels V ON J.vessel_id = V.id
			LEFT OUTER JOIN indian_ports IP ON J.indian_port_id = IP.id
			LEFT OUTER JOIN high_seas HS ON P.job_id = HS.job_id
			LEFT OUTER JOIN parties HSL ON HS.party_id = HSL.id
		WHERE (J.type = 'Import' AND J.cargo_type = 'Container' AND LENGTH(J.be_no) = 0 AND J.status != 'Completed')";
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
}