<?php

class Icegate_be extends MY_Controller {
	var $_fields, $_group_fields;

	function __construct() {
		parent::__construct();
		
		$this->_fields = array(
			'party'             => 'P.name',
			'type'              => 'J.cargo_type',
			'product'           => 'PRD.name',
			'category'          => 'PRD.category',
			'vessel'            => 'CONCAT(V.prefix, " ", V.name, " ", V.voyage_no)',
			'bl_no'             => 'J.bl_no',
			'bl_date'           => "DATE_FORMAT(J.bl_date, '%d-%m-%Y')",
			'be_no'             => 'J.be_no',
			'be_date'           => "DATE_FORMAT(J.be_date, '%d-%m-%Y')",
			'iec_no'            => 'IT.iec_no',
			'port'              => 'IP.name',
			'cha'               => 'IT.cha_no',
			'cfs'               => 'CFS.name',
			'prior_be'          => 'IT.prior_be',
			'section_48'        => 'IT.section_48',
			'wbe_no'            => 'IT.wbe_no',
			'appraisement'      => 'IT.appraisement',
			'appraisement_date' => 'IT.appraisement_date',
			'assessment_date'   => 'IT.assessment_date',
			'payment_date'      => 'IT.payment_date',
			'exam_date'         => 'IT.exam_date',
			'ooc_date'          => 'IT.ooc_date',
			'duty_paid'         => 'IT.duty_paid',
			'payment_mode'      => 'IT.payment_mode'
		);

		$this->_group_fields = array(
			'party'  => 'P.name',
			'vessel' => 'CONCAT(V.prefix, " ", V.name, " ", V.voyage_no)',
			'cha'    => 'IT.cha_no',
			'port'   => 'IP.port'
		);
	}
	
	function index() {
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

		$data['icegate'] = $this->_getIcegateOOC($data['from_date'], $data['to_date'], $search, $parsed_search);

		$default_company = $this->session->userdata("default_company");
		$data['years']   = explode('_', $default_company['financial_year']);

		$this->load->helper('datefn');
		$data['javascript'] = ['bootstrap-daterangepicker/daterangepicker.js'];
		$data['stylesheet'] = ['bootstrap-daterangepicker/daterangepicker.css'];

		$data['page_title'] = "Icegate Register";
		$data['page'] = $this->_clspath.$this->_class;
		$data['docs_url'] = $this->_docs;
		$this->load->view('index', $data);
	}

	function _getIcegateOOC($from_date, $to_date, $search, $parsed_search) {
		$sql = "SELECT J.id, P.name AS party_name, J.cargo_type, PRD.name AS product, PRD.category,
			CONCAT(V.prefix, ' ', V.name, ' ', V.voyage_no) AS vessel_name, J.packages, 
			IF(J.cbm > 0, J.cbm, J.net_weight) AS net_weight, container_20, container_40, J.bl_no, 
			DATE_FORMAT(J.bl_date, '%d-%m-%Y') AS bl_date, J.be_no, DATE_FORMAT(J.be_date, '%d-%m-%Y') AS be_date, 
			IP.name AS indian_port, IT.iec_no, IT.cha_no, IF(ISNULL(A.name), IT.cha_no, A.name) AS cha_name, CFS.name AS cfs_name,
			IT.prior_be, IT.section_48, IT.wbe_no, IT.appraisement, IT.appraisement_date, IT.assessment_date, 
			IT.payment_date, IT.exam_date, IT.ooc_date, IT.duty_amount
		FROM (((((icegate_be IT INNER JOIN jobs J ON IT.job_id = J.id)
		 	INNER JOIN parties P ON J.party_id = P.id) 
		 	INNER JOIN vessels V ON J.vessel_id = V.id) 
		 	LEFT OUTER JOIN indian_ports IP ON J.indian_port_id = IP.id) 
		 	LEFT OUTER JOIN products PRD ON J.product_id = PRD.id) 
		 	LEFT OUTER JOIN agents A ON IT.cha_no = A.cha_no
		 	LEFT OUTER JOIN agents CFS ON J.cfs_id = CFS.id
		WHERE ((DATE_FORMAT(STR_TO_DATE(IT.ooc_date, '%Y-%m-%d'), '%Y-%m-%d') >= ? AND 
			   DATE_FORMAT(STR_TO_DATE(IT.ooc_date, '%Y-%m-%d'), '%Y-%m-%d') <= ?) OR
			  (DATE_FORMAT(STR_TO_DATE(IT.ooc_date, '%m/%d/%Y'), '%Y-%m-%d') >= ? AND 
			   DATE_FORMAT(STR_TO_DATE(IT.ooc_date, '%m/%d/%Y'), '%Y-%m-%d') <= ?))";
		$where = ' AND (';
		if (is_array($parsed_search)) {
			foreach($parsed_search as $key => $value)
				if (isset($this->_fields[$key]))
					$where .= $this->_fields[$key] . " LIKE '%$value%' AND ";
			if (strlen($where) > 6)
				$sql .= substr($where, 0, strlen($where) - 5) . ') ';
		}
		$sql .= 'ORDER BY IT.ooc_date';
		$query = $this->db->query($sql, array(
			convDate($from_date), convDate($to_date),
			convDate($from_date), convDate($to_date)
		));
		return $query->result_array();
	}


	function index_be() {
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

		$data['icegate'] = $this->_getIcegateBE($data['from_date'], $data['to_date'], $search, $parsed_search);

		$default_company = $this->session->userdata("default_company");
		$data['years']   = explode('_', $default_company['financial_year']);

		$this->load->helper('datefn');
		$data['javascript'] = ['bootstrap-daterangepicker/daterangepicker.js'];
		$data['stylesheet'] = ['bootstrap-daterangepicker/daterangepicker.css'];

		$data['page_title'] = "Icegate Register";
		$data['page'] = $this->_clspath.$this->_class;
		$data['docs_url'] = $this->_docs;
		$this->load->view('index', $data);
	}

	function _getIcegateBE($from_date, $to_date, $search, $parsed_search) {
		$sql = "SELECT J.id, P.name AS party_name, J.cargo_type, PRD.name AS product, PRD.category,
			CONCAT(V.prefix, ' ', V.name, ' ', V.voyage_no) AS vessel_name, J.packages, 
			IF(J.cbm > 0, J.cbm, J.net_weight) AS net_weight, container_20, container_40, J.bl_no, 
			DATE_FORMAT(J.bl_date, '%d-%m-%Y') AS bl_date, J.be_no, DATE_FORMAT(J.be_date, '%d-%m-%Y') AS be_date, 
			IP.name AS indian_port, IT.iec_no, IT.cha_no, IF(ISNULL(A.name), IT.cha_no, A.name) AS cha_name, CFS.name AS cfs_name,
			IT.prior_be, IT.section_48, IT.wbe_no, IT.appraisement, IT.appraisement_date, IT.assessment_date, 
			IT.payment_date, IT.exam_date, IT.ooc_date, IT.duty_amount
		FROM icegate_be IT INNER JOIN jobs J ON IT.job_id = J.id
		 	INNER JOIN parties P ON J.party_id = P.id 
		 	INNER JOIN vessels V ON J.vessel_id = V.id 
		 	LEFT OUTER JOIN indian_ports IP ON J.indian_port_id = IP.id 
		 	LEFT OUTER JOIN products PRD ON J.product_id = PRD.id 
		 	LEFT OUTER JOIN agents A ON IT.cha_no = A.cha_no
		 	LEFT OUTER JOIN agents CFS ON J.cfs_id = CFS.id
		WHERE J.be_date >= ? AND J.be_date <= ? ";
		$where = ' AND (';
		if (is_array($parsed_search)) {
			foreach($parsed_search as $key => $value)
				if (isset($this->_fields[$key]))
					$where .= $this->_fields[$key] . " LIKE '%$value%' AND ";
			if (strlen($where) > 6)
				$sql .= substr($where, 0, strlen($where) - 5) . ') ';
		}
		$sql .= 'ORDER BY J.be_date';
		$query = $this->db->query($sql, array(
			convDate($from_date), convDate($to_date)
		));
		return $query->result_array();
	}

	function excel() {
		$from_date = $this->session->userdata($this->_class.'_from_date');
		$to_date   = $this->session->userdata($this->_class.'_to_date');
		$search    = $this->session->userdata($this->_class.'_search');
		$parsed_search = $this->kaabar->parseSearch($search);
		$rows          = $this->_getIcegateBE($from_date, $to_date, $search, $parsed_search);

		$this->_excel($rows);
	}


	function group() {
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

		$data['from_date'] = $from_date ? $from_date : date('01-m-Y');
		$data['to_date']   = $to_date ? $to_date : date('d-m-Y');
		$data['search']    = $search ? $search : '';
		$parsed_search     = $this->kaabar->parseSearch($search);
		$data['parsed_search'] = $parsed_search;
		$data['search_fields'] = $this->_group_fields;

		if (is_array($parsed_search)) {
			$search = '';
			foreach ($parsed_search as $key => $value) {
				$search .= $key.':'.$value.' ';
			}
			$data['search'] = $search;
		}

		$data['icegate'] = $this->_getIcegateGroup($from_date, $to_date, $search, $parsed_search);

		$default_company = $this->session->userdata("default_company");
		$data['years']   = explode('_', $default_company['financial_year']);

		$this->load->helper('datefn');
		$data['javascript'] = ['bootstrap-daterangepicker/daterangepicker.js'];
		$data['stylesheet'] = ['bootstrap-daterangepicker/daterangepicker.css'];

		$data['page_title'] = "Icegate Register";
		$data['page'] = $this->_clspath.$this->_class.'_group';
		$data['docs_url'] = $this->_docs;
		$this->load->view('index', $data);
	}

	function _getIcegateGroup($from_date, $to_date, $search, $parsed_search) {
		$sql = "SELECT IC.*, IF(ISNULL(A.name), IC.cha_no, A.name) AS cha_name
		FROM (
		SELECT J.id, J.type, P.name AS party_name, J.cargo_type, J.product_id, PRD.name AS product, PRD.category,
			CONCAT(V.prefix, ' ', V.name, ' ', V.voyage_no) AS vessel_name, IP.name AS indian_port, IT.cha_no, 
			ROUND(SUM(IF(IT.appraisement =  'SYSTEM', IF(J.cbm > 0, J.cbm, J.net_weight), 0)), 3) AS system_net_weight, 
			ROUND(SUM(IF(IT.appraisement != 'SYSTEM', IF(J.cbm > 0, J.cbm, J.net_weight), 0)), 3) AS officer_net_weight, 
			SUM(IF(IT.appraisement =  'SYSTEM', J.container_20, 0)) AS system_container_20,
			SUM(IF(IT.appraisement != 'SYSTEM', J.container_20, 0)) AS officer_container_20,
			SUM(IF(IT.appraisement =  'SYSTEM', J.container_40, 0)) AS system_container_40,
			SUM(IF(IT.appraisement != 'SYSTEM', J.container_40, 0)) AS officer_container_40
		FROM ((((icegate_be IT INNER JOIN jobs J ON IT.job_id = J.id)
			INNER JOIN parties P ON J.party_id = P.id)
			INNER JOIN vessels V ON J.vessel_id = V.id)
			INNER JOIN products PRD ON J.product_id = PRD.id)
			INNER JOIN indian_ports IP ON J.indian_port_id = IP.id
		WHERE ((DATE_FORMAT(STR_TO_DATE(IT.ooc_date, '%Y-%m-%d'), '%Y-%m-%d') >= ? AND 
			   DATE_FORMAT(STR_TO_DATE(IT.ooc_date, '%Y-%m-%d'), '%Y-%m-%d') <= ?) OR
			  (DATE_FORMAT(STR_TO_DATE(IT.ooc_date, '%m/%d/%Y'), '%Y-%m-%d') >= ? AND 
			   DATE_FORMAT(STR_TO_DATE(IT.ooc_date, '%m/%d/%Y'), '%Y-%m-%d') <= ?))";
		if (is_array($parsed_search)) {
			$where = ' AND (';
			foreach($parsed_search as $key => $value)
				if (isset($this->_fields[$key]))
					$where .= $this->_fields[$key] . " LIKE '%$value%' AND ";
			if (strlen($where) > 6)
				$sql .= substr($where, 0, strlen($where) - 5) . ') ';
		}
		$sql .= '
			GROUP BY J.vessel_id
		) IC LEFT OUTER JOIN agents A ON IC.cha_no = A.cha_no
		ORDER BY IC.type, IC.product_id';
		$query = $this->db->query($sql, array(
			convDate($from_date), convDate($to_date),
			convDate($from_date), convDate($to_date)
		));
		return $query->result_array();
	}
}
