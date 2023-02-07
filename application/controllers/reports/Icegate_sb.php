<?php

class Icegate_sb extends MY_Controller {
	var $_fields;

	function __construct() {
		parent::__construct();
		
		$this->_fields = array(
			'party'    => 'P.name',
			'type'     => 'J.cargo_type',
			'product'  => 'PRD.name',
			'vessel'   => 'CONCAT(V.prefix, " ", V.name, " ", V.voyage_no)',
			'bl_no'    => 'CJ.bl_no',
			'sb_no'    => 'CJ.sb_no',
			'iec_no'   => 'IT.iec_no',
			'port'     => 'IP.name',
			'cha'      => 'IT.cha_no',
		);
	}
	
	function index() {
		if (! Auth::hasAccess()) {
			setSessionError('NO_PERMISSION');
			redirect('main');
		}

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
			$search    = $this->session->userdata($this->_class.'_search');
		}

		$data['from_date'] = $from_date ? $from_date : date('01-m-Y');
		$data['to_date']   = $to_date ? $to_date : date('d-m-Y');
		$data['search']    = $search ? $search : '';
		$parsed_search     = $this->kaabar->parseSearch($search);
		$data['parsed_search'] = $parsed_search;
		$data['search_fields'] = $this->_fields;

		if (is_array($parsed_search)) {
			$search = '';
			foreach ($parsed_search as $key => $value) {
				$search .= $key.':'.$value.' ';
			}
			$data['search'] = $search;
		}

		$data['icegate'] = $this->_getIcegate($data['from_date'], $data['to_date'], $search, $parsed_search);

		$default_company = $this->session->userdata("default_company");
		$data['years']   = explode('_', $default_company['financial_year']);

		$this->load->helper('datefn');
		$data['javascript'] = array('daterangepicker/daterangepicker.js');
		$data['stylesheet'] = array('daterangepicker/daterangepicker-bs3.css');

		$data['page_title'] = "Icegate Register";
		$data['hide_title'] = true;
		$data['page']       = $this->_clspath.$this->_class;
		$data['docs_url']   = $this->_docs;
		$this->load->view('index', $data);
	}

	function excel() {
		$from_date = $this->session->userdata($this->_class.'_from_date');
		$to_date   = $this->session->userdata($this->_class.'_to_date');
		$search    = $this->session->userdata($this->_class.'_search');
		$parsed_search = $this->kaabar->parseSearch($search);
		$rows          = $this->_getIcegate($from_date, $to_date, $search, $parsed_search);

		$this->_excel($rows);
	}

	function _getIcegate($from_date, $to_date, $search, $parsed_search) {
		$sql = "SELECT J.id, J.id2_format, J.party_id, P.name AS party_name, J.cargo_type, PRD.name AS product, 
			CONCAT(V.prefix, ' ', V.name, ' ', V.voyage_no) AS vessel_name, 
			SUM(CJ.packages) AS packages, SUM(CJ.net_weight) AS net_weight, SUM(CJ.gross_weight) AS gross_weight,
			S.container_20, S.container_40, 
			GROUP_CONCAT(S.containers SEPARATOR ', ') AS containers, 
			GROUP_CONCAT(DISTINCT CONCAT(CJ.bl_no, ' ', DATE_FORMAT(CJ.bl_date, '%d-%m-%Y')) SEPARATOR ', ') AS bl_no_date, 
			GROUP_CONCAT(DISTINCT CONCAT(CJ.sb_no, ' ', DATE_FORMAT(CJ.sb_date, '%d-%m-%Y')) SEPARATOR ', ') AS sb_no_date, 
			IP.name AS indian_port, IT.iec_no, IT.cha_no, IF(ISNULL(A.name), IT.cha_no, A.name) AS cha_name
		FROM icegate_sb IT INNER JOIN child_jobs CJ ON IT.child_job_id = CJ.id
			INNER JOIN jobs J ON CJ.job_id = J.id
			INNER JOIN parties P ON J.party_id = P.id
		 	LEFT OUTER JOIN vessels V ON J.vessel_id = V.id 
		 	LEFT OUTER JOIN indian_ports IP ON J.loading_port_id = IP.id 
		 	LEFT OUTER JOIN products PRD ON J.product_id = PRD.id 
		 	LEFT OUTER JOIN agents A ON IT.cha_no = A.cha_no
		 	LEFT OUTER JOIN (
				SELECT S.job_id, COUNT(IF(CT.size = 20, S.id, NULL)) AS container_20, COUNT(IF(CT.size = 40, S.id, NULL)) AS container_40,
					CONCAT(COUNT(S.id), 'x', CT.size, CT.code) AS containers
				FROM deliveries_stuffings S INNER JOIN container_types CT ON S.container_type_id = CT.id
					INNER JOIN jobs J ON S.job_id = J.id
				GROUP BY J.id, CT.id
			) S ON J.id = S.job_id
		WHERE CJ.sb_date >= ? AND CJ.sb_date <= ? ";
		$where = ' AND (';
		if (is_array($parsed_search)) {
			foreach($parsed_search as $key => $value)
				if (isset($this->_fields[$key]))
					$where .= $this->_fields[$key] . " LIKE '%$value%' AND ";
			if (strlen($where) > 6)
				$sql .= substr($where, 0, strlen($where) - 5) . ') ';
		}
		$sql .= '
		GROUP BY J.id
		ORDER BY CJ.sb_date';
		$query = $this->db->query($sql, array(
			convDate($from_date), convDate($to_date),
			convDate($from_date), convDate($to_date)
		));
		return $query->result_array();
	}
}
