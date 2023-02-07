<?php

class Export_process extends MY_Controller {
	function __construct() {
		parent::__construct();

		$this->load->helper('datefn');
	}

	function index() {
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
		$data['page']        = $this->_clspath.$this->_class;
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
			LEFT OUTER JOIN job_invoices EI ON CJ.id = EI.child_job_id
			LEFT OUTER JOIN indian_ports IP ON J.custom_port_id = IP.id
			LEFT OUTER JOIN ports PRT ON J.discharge_port_id = PRT.id
			LEFT OUTER JOIN products CARGO ON J.product_id = CARGO.id
			LEFT OUTER JOIN job_containers PC ON J.id = PC.job_id
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
}