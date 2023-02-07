<?php

class Pending extends MY_Controller {
	var $_allowedFileds, $_getFields;
	var $_fy_year;
	var $_parsed_search;

	function __construct() {
		parent::__construct();

		$this->_company_id = 0;
		$default_company = $this->session->userdata('default_company');
		if ($default_company != false) {
			$this->_fy_year    = $default_company['financial_year'];
		}
		unset($default_company);

		$this->_table = 'export_details';
		$this->load->model('export');
		$this->load->helper('datefn');

		$this->_allowedFileds = $this->export->getAllowedFileds();
		$this->_fields = $this->export->getFields();
		
	}
	
	public function index($type = 'Export', $starting_row = 0)
	{
		$default_company = $this->session->userdata("default_company");
		$data['company'] = $this->kaabar->getRow('companies', $default_company['id']);
		$data['years']   = explode('_', $default_company['financial_year']);

		$starting_row = intval($starting_row);
		$from_date = null;
		$to_date   = null;
		$search    = null;
		$advance_form = null;
		$advance_filter_form = null;

		if ($this->input->post('from_date')) {
			$from_date = $this->input->post('from_date');
			$to_date   = $this->input->post('to_date');
			$search    = $this->input->post('search');
			$advance_form    = $this->input->post('advance_form');
			$advance_filter_form    = $this->input->post('advance_filter_form');

			$this->session->set_userdata($this->_class.'_from_date', $from_date);
			$this->session->set_userdata($this->_class.'_to_date', $to_date);
			$this->session->set_userdata($this->_class.'_search', $search);
			$this->session->set_userdata($this->_class.'_advance_form', $advance_form);
			$this->session->set_userdata($this->_class.'_advance_filter_form', $advance_filter_form);
		}
		
		if ($from_date == null) {

			$from_date = $this->session->userdata($this->_class.'_from_date');
			$to_date   = $this->session->userdata($this->_class.'_to_date');
			$search = $this->session->userdata($this->_class.'_search');
			$advance_form = $this->session->userdata($this->_class.'_advance_form');
			$advance_filter_form = $this->session->userdata($this->_class.'_advance_filter_form');
		}

		$data['from_date']     = $from_date ? $from_date : date('01-04-'.$data['years'][0]);
		$data['to_date']       = $to_date ? $to_date : date('d-m-Y');
		$data['search']        = $search;

		$this->_parsed_form 	 = $this->kaabar->parseSearch($advance_form);
		$this->_parsed_filter  	 = $this->kaabar->parseSearch($advance_filter_form);

		$data['parsed_search'] = $this->_parsed_form;

		if (is_array($this->_parsed_form)) {
			$advance_form = '';
			foreach ($this->_parsed_form as $key => $value) {
				$advance_form .= $key.':'.$value.' ';
			}
			$data['advance_form'] = $advance_form;
		}

		$data['parsed_filter'] = $this->_parsed_filter;

		if (is_array($this->_parsed_filter)) {
			$advance_filter_form = '';
			foreach ($this->_parsed_filter as $key => $value) {
				$advance_filter_form .= $key.':'.$value.' ';
			}
			$data['advance_filter_form'] = $advance_filter_form;
		}

		$data['list']['search_fields'] = $this->_fields;
		$this->_parsed_search 	 = $this->kaabar->parseFilterSearch($this->_parsed_form , $this->_parsed_filter);

		$data['show_search'] = false;
		$data['advance_search'] = true;
		
		$headername = $this->export->getHeaderName();
		foreach ($headername as $key => $value) {
			$header[$key] = $headername[$key];
		}

		$data['list']['heading'] = $header;
		$data['list']['class'] = array(
			'job_no' 			=> array('class' => 'tiny', 'link' => 'id'),
	    	'job_date' 			=> '',
	    	'billing_party' 	=> '',
	    	'amount' 			=> '',
	    	'shipper' 			=> '',
	    	'sb_no' 			=> '',
	    	'consignee' 		=> '',
	    	'shipment_type' 	=> '',
	    	'custom_port' 		=> '',
	    	'pod' 				=> '',
	    	'document' 			=> '',
	    	'project_costsheet' => '',
	    	'actual_costsheet' 	=> '',
			'quotation' 		=> '',
			'reason_not_billed' => '',
			'upload' 			=> '');

		$data['list']['link_col'] = 'job_no';
		$data['list']['link_url'] = $this->_clspath.$this->_class."/edit/";
		$data['list']['preload_page'] = 'advance_form';

		$data['label_class'] = $this->export->getLabelClass();
		
		$this->load->library('pagination');
		$config['base_url']    = site_url($this->_clspath.$this->_class.'/index/'.$type);
		$config['uri_segment'] = (strlen($this->_clspath) > 0 ? (4+substr_count($this->_clspath, '/')) : 4);
		$config['total_rows']  = $this->export->countJobs($search);
		$config['per_page']    = Settings::get('rows_per_page');
		$this->pagination->initialize($config);

		$data['list']['data'] = $this->export->getPedningJobs($data['from_date'], $data['to_date'], $data['search'], $this->_parsed_search, $starting_row, $config['per_page']);

		$data['buttons'] = array(
			anchor($this->_clspath.$this->_class."/preview", '<i class="icon-file"></i> View', 'class="btn btn-secondary Popup"').
			anchor($this->_clspath.$this->_class."/preview/1", '<i class="icon-file-pdf"></i> PDF', 'class="btn btn-secondary ml-1 Popup"').
			anchor($this->_clspath.$this->_class."/excel", '<i class="icon-file-excel"></i> Excel', 'class="btn btn-secondary ml-1"')
		);
			
		$data['page_title'] = $type. ' - Pending Billing';
		$data['page']       = 'list';
		$data['docs_url']   = $this->_docs;
		$this->load->view('index', $data);

	}

	function json($table = FALSE, $key = 'id', $value = 'name') {
		if ($this->input->is_ajax_request()) {

			$default_company = $this->session->userdata("default_company");
			$data['company'] = $this->kaabar->getRow('companies', $default_company['id']);
			$data['years']   = explode('_', $default_company['financial_year']);

			$from_date = null;
			$to_date   = null;
			
			if ($this->input->post('from_date')) {
				$from_date = $this->input->post('from_date');
				$to_date   = $this->input->post('to_date');
				$search    = $this->input->post('search');
				$advance_form    = $this->input->post('advance_form');
				$advance_filter_form    = $this->input->post('advance_filter_form');

				$this->session->set_userdata($this->_class.'_from_date', $from_date);
				$this->session->set_userdata($this->_class.'_to_date', $to_date);
				$this->session->set_userdata($this->_class.'_search', $search);
				$this->session->set_userdata($this->_class.'_advance_form', $advance_form);
				$this->session->set_userdata($this->_class.'_advance_filter_form', $advance_filter_form);
			}
			
			if ($from_date == null) {
				$from_date = $this->session->userdata($this->_class.'_from_date');
				$to_date   = $this->session->userdata($this->_class.'_to_date');
			}

			$from_date     = $from_date ? $from_date : date('01-04-'.$data['years'][0]);
			$to_date       = $to_date ? $to_date : date('d-m-Y');
			$search = strtolower($this->input->post_get('term'));
			$args   = $this->uri->segment_array();

			$index    = array_search('json', $args); 							$index++;
			$table    = (isset($args[$index]) ? $args[$index] : $this->_table); $index++;
			$key      = (isset($args[$index]) ? $args[$index] : 'id'); 			$index++;
			$field[]  = (isset($args[$index]) ? $args[$index] : 'name'); 		$index++;

			$this->db->select($this->_fields[$value] .' as '. $value)
				->join('parties P', 'J.party_id = P.id', 'LEFT')
	    		->join('parties A', 'J.shipper_id = A.id', 'LEFT')
	    		->join('parties AA', 'J.consignee_id = AA.id', 'LEFT')
	    		->join('indian_ports PPO', 'J.custom_port_id = PPO.id', 'LEFT')
	    		->join('ports PP', 'J.discharge_port_id = PP.id', 'LEFT')
	    		->where_in('J.status', ['Pending', 'Program', 'Delivery', 'Bills', 'Completed'])
				->where('J.date >= ', convDate($from_date))
		    	->where('J.date <= ', convDate($to_date))
		    	->group_by($this->_fields[$value]);

			foreach ($field as $f) {
				$this->db->like($this->_fields[$f], $search, 'both');
			}

			$this->db->limit(50, 0);
			$rows = $this->db->get($table.' J')->result_array();

			foreach ($rows as $k => $v) {

				$v['name'] = $v[$value];
				unset($v[$value]);
				$rows[$k] = $v;
			}
			
			header('Content-type: application/json; charset=utf-8');
			echo json_encode($rows, JSON_UNESCAPED_UNICODE);
		}
		else
			echo "Access Denied";
	}

	function edit($type = 'Booking') {
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
			$sortby = '';
			$this->session->set_userdata($this->_class.'_sortby', $sortby);
		}
		$data['search'] = $search;
		$data['sortby'] = $sortby;
		$parsed_search  = $this->kaabar->parseSearch($search);
		$data['parsed_search'] = $parsed_search;

		if (is_array($this->_parsed_search)) {
			$search = '';
			foreach ($this->_parsed_search as $key => $value) {
				$search .= $key.':'.$value.' ';
			}
			$data['search'] = $search;
		}
		
		if ($type == 'Booking') {
			$data['search_fields']  = $this->_b_fields;
			$data['export_details'] = $this->_bookingPendings($search, $parsed_search, $this->_b_fields);
		}
		else if ($type == 'Custom') {
			$data['search_fields']  = $this->_c_fields;
			$data['export_details'] = $this->_customPendings($search, $parsed_search, $this->_c_fields);
		}
		else if ($type == 'Shipment') {
			$status = $this->input->post('job_status');
			if ($status) {
				foreach ($status as $job_id => $s) {
					$this->kaabar->save('jobs', array('status' => $s), array('id' => $job_id));
				}
			}

			$data['search_fields']  = $this->_s_fields;
			$data['rows'] = $this->_shipmentPendings($search, $parsed_search, $this->_s_fields);
		}
		
		$data['docs_url'] = $this->_docs;
		$data['page_title'] = humanize($this->_class);
		$data['page_desc'] = "Export - Container";

		$data['hide_footer'] = true;
		$data['page'] = $this->_clspath.$this->_class.'_'.strtolower($type);
		$this->load->view('index', $data);
	}
	
	function _bookingPendings($search, $parsed_search, $fields) {
		$years     = explode('_', $this->_fy_year);
		$from_date = $years[0].'-04-01';
		$to_date   = $years[1].'-03-31';

		$sql = "SELECT J.id, J.id AS job_id, CJ.id AS child_job_id, J.id2_format, PL.name AS party_name, EI.invoice_no, 
			DATE_FORMAT(EI.invoice_date, '%d-%m-%Y') AS invoice_date, IF(LENGTH(TRIM(CJ.bl_no)) = 0, 'Missing BL No', CJ.bl_no) AS bl_no, 
			DATE_FORMAT(CJ.bl_date, '%d-%m-%Y') AS bl_date, CARGO.name AS cargo_name,
			GROUP_CONCAT(DISTINCT CONCAT(PC.containers, 'x', CT.size, CT.code) SEPARATOR ', ') AS containers,
			L.name AS line_name, CFS.name AS cfs_name, V.name AS vessel_name, DATE_FORMAT(V.etd_date, '%d-%m-%Y') AS etd_date, 
			IP.name AS pol, PRT.name AS pod
		FROM jobs J
			INNER JOIN parties PL ON J.party_id = PL.id
			LEFT OUTER JOIN agents L ON J.line_id = L.id
			LEFT OUTER JOIN agents CFS ON J.cfs_id = CFS.id
			LEFT OUTER JOIN child_jobs CJ ON J.id = CJ.job_id
			LEFT OUTER JOIN vessels V ON J.vessel_id = V.id
			LEFT OUTER JOIN job_invoices EI ON CJ.id = EI.child_job_id
			LEFT OUTER JOIN indian_ports IP ON J.custom_port_id = IP.id
			LEFT OUTER JOIN ports PRT ON J.discharge_port_id = PRT.id
			LEFT OUTER JOIN products CARGO ON J.product_id = CARGO.id
			LEFT OUTER JOIN job_containers PC ON J.id = PC.job_id
			LEFT OUTER JOIN container_types CT ON PC.container_type_id = CT.id
		WHERE (J.type = 'Export' AND J.cargo_type = 'Container' AND (ISNULL(CJ.sb_no) OR LENGTH(TRIM(CJ.sb_no)) = 0)) AND J.status != 'Completed'";
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

	function _customPendings($search, $parsed_search, $fields) {
		$years     = explode('_', $this->_fy_year);
		$from_date = $years[0].'-04-01';
		$to_date   = $years[1].'-03-31';

		$sql = "SELECT CJ.job_id, CJ.id AS child_job_id, J.id2_format, PL.name AS party_name, EI.invoice_no, DATE_FORMAT(EI.invoice_date, '%d-%m-%Y') AS invoice_date, 
			CJ.sb_no, DATE_FORMAT(CJ.sb_date, '%d-%m-%Y') AS sb_date, IF(LENGTH(TRIM(CJ.bl_no)) = 0, 'Missing BL No', CJ.bl_no) AS bl_no, 
			DATE_FORMAT(CJ.bl_date, '%d-%m-%Y') AS bl_date, CARGO.name AS cargo_name, 
			GROUP_CONCAT(DISTINCT CONCAT(PC.containers, 'x', CT.size, CT.code) SEPARATOR ', ') AS containers,
			L.name AS line_name, V.name AS vessel_name, DATE_FORMAT(V.etd_date, '%d-%m-%Y') AS etd_date, 
			IP.name AS pol, PRT.name AS pod, CFS.name AS cfs_name,
			IC.last_fetched, IC.status, IC.last_status, DATE_FORMAT(IC.leo_date, '%d-%m-%Y') AS leo_date, IC.ep_copy_print_status, IC.print_status
		FROM icegate_sb IC
			INNER JOIN child_jobs CJ ON IC.child_job_id = CJ.id
			INNER JOIN jobs J ON J.id = CJ.job_id
			INNER JOIN parties PL ON J.party_id = PL.id
			LEFT OUTER JOIN agents L ON J.line_id = L.id
			LEFT OUTER JOIN agents CFS ON J.cfs_id = CFS.id
			LEFT OUTER JOIN vessels V ON J.vessel_id = V.id
			LEFT OUTER JOIN job_invoices EI ON CJ.id = EI.child_job_id
			LEFT OUTER JOIN indian_ports IP ON J.custom_port_id = IP.id
			LEFT OUTER JOIN ports PRT ON J.discharge_port_id = PRT.id
			LEFT OUTER JOIN products CARGO ON J.product_id = CARGO.id
			LEFT OUTER JOIN job_containers PC ON J.id = PC.job_id
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

	function _shipmentPendings($search, $parsed_search, $fields) {
		$years     = explode('_', $this->_fy_year);
		$from_date = $years[0].'-04-01';
		$to_date   = $years[1].'-03-31';

		$sql = "SELECT CJ.job_id, CJ.id AS child_job_id, J.status AS job_status, J.id2_format, PL.name AS party_name, 
			EI.invoice_no, DATE_FORMAT(EI.invoice_date, '%d-%m-%Y') AS invoice_date, 
			CJ.sb_no, DATE_FORMAT(CJ.sb_date, '%d-%m-%Y') AS sb_date, 
			IF(LENGTH(TRIM(CJ.bl_no)) = 0, 'Missing BL No', CJ.bl_no) AS bl_no, DATE_FORMAT(CJ.bl_date, '%d-%m-%Y') AS bl_date, 
			GROUP_CONCAT(DISTINCT CONCAT(PC.containers, 'x', CT.size, CT.code) SEPARATOR ', ') AS containers,
			L.name AS line_name, V.name AS vessel_name, DATE_FORMAT(V.etd_date, '%d-%m-%Y') AS etd_date, 
			IP.name AS pol, PRT.name AS pod, CFS.name AS cfs_name,
			IC.last_fetched, IC.status, IC.last_status, DATE_FORMAT(IC.leo_date, '%d-%m-%Y') AS leo_date, IC.ep_copy_print_status, IC.print_status
		FROM icegate_sb IC
			INNER JOIN child_jobs CJ ON IC.child_job_id = CJ.id
			INNER JOIN jobs J ON J.id = CJ.job_id
			INNER JOIN parties PL ON J.party_id = PL.id
			LEFT OUTER JOIN agents L ON J.line_id = L.id
			LEFT OUTER JOIN agents CFS ON J.cfs_id = CFS.id
			LEFT OUTER JOIN vessels V ON J.vessel_id = V.id
			LEFT OUTER JOIN job_invoices EI ON CJ.id = EI.child_job_id
			LEFT OUTER JOIN indian_ports IP ON J.custom_port_id = IP.id
			LEFT OUTER JOIN ports PRT ON J.discharge_port_id = PRT.id
			LEFT OUTER JOIN job_containers PC ON J.id = PC.job_id
			LEFT OUTER JOIN container_types CT ON PC.container_type_id = CT.id
		WHERE (J.type = 'Export' AND J.cargo_type = 'Container' AND LENGTH(TRIM(CJ.sb_no)) > 0 AND 
			LENGTH(TRIM(IC.leo_date)) > 8) ";
		$where = ' AND (';
		if (is_array($parsed_search)) {
			foreach($parsed_search as $key => $value)
				if (isset($fields[$key]))
					$where .= $fields[$key] . " LIKE '%$value%' AND ";
			if (strlen($where) > 6)
				$sql .= substr($where, 0, strlen($where) - 5) . ')';
		}
		$sql .= ((isset($parsed_search['status']) && $parsed_search['status'] == 'Completed') ? '' : " AND (J.status != 'Completed')") . "
		GROUP BY J.id, CJ.id
		ORDER BY J.id2 DESC";
		$query = $this->db->query($sql);
		return $query->result_array();
	}
}
