<?php

class Party extends MY_Controller {
	var $_fields;
	var $_parsed_search;

	function __construct() {
		parent::__construct();
	
		$this->_fields = array(
			'account_group' => 'AG.name',
			'group'   => 'LP.group_name',
			'name'    => 'P.name',
			'address' => 'P.address',
			'city'    => 'C.name',
			'contact' => 'P.contact',
			'email'   => 'P.email',
			'pan'     => 'P.pan_no',
			'tan'     => 'P.tan_no',
			'stax'    => 'P.service_tax_no',
			'iec'     => 'P.iec_no',
			'tin'     => 'P.tin_no',
			'cst'     => 'P.cst_no',
			'excise'  => 'P.excise_no'
		);
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

		$data['search']        = $search;
		$this->_parsed_search  = $this->kaabar->parseSearch($search);
		$data['parsed_search'] = $this->_parsed_search;
		$data['search_fields'] = $this->_fields;

		if (is_array($this->_parsed_search)) {
			$search = '';
			foreach ($this->_parsed_search as $key => $value) {
				$search .= $key.':'.$value.' ';
			}
			$data['search'] = $search;
		}
		$default_company = $this->session->userdata("default_company");

		$data['rows'] = $this->_getPending($search, $data['parsed_search'], $this->_fields);

		$data['javascript'] = array('backbonejs/underscore-min.js', 'backbonejs/backbone-min.js');

		$data['page_title'] = humanize($this->_class);
		$data['hide_title'] = true;
		$data['page'] = $this->_clspath.$this->_class;
		$data['docs_url'] = $this->_docs;
		$this->load->view('index', $data);
	}

	function _getPending($search, $parsed_search, $fields, $query_only = false) {
		$sql = "SELECT DISTINCT P.*, LP.group_name, CONCAT(C.name, ' - ', C.pincode, ' ', S.name) AS city, AG.name AS account_group, 
			DI.id AS dgft_id
		FROM parties P LEFT OUTER JOIN cities C ON P.city_id = C.id
			LEFT OUTER JOIN states S ON C.state_id = S.id
			LEFT OUTER JOIN ledgers LP ON P.id = LP.party_id
			LEFT OUTER JOIN account_groups AG ON LP.account_group_id = AG.id
			LEFT OUTER JOIN dgft_iecs DI ON P.iec_no = DI.iec_no
		";
		$where = 'WHERE ';
		if (is_array($parsed_search)) {
			foreach($parsed_search as $key => $value)
				if (isset($fields[$key])) {
					if (strtolower($value) == 'empty')
						$where .= "LENGTH(TRIM(" . $fields[$key] . ")) = 0 AND ";
					else
						$where .= $fields[$key] . " LIKE '%$value%' AND ";
				}
			if (strlen($where) > 6)
				$sql .= substr($where, 0, strlen($where) - 5);
		}
		$sql .= " 
		ORDER BY LP.group_name, P.name";
		$query = $this->db->query($sql);

		if ($query_only)
			return $query;

		return $query->result_array();
	}

	function preview($pdf = 0) {
		$data['page_title'] = humanize($this->_class);
		$data['rows'] = $this->_getPending();

		if ($pdf) {
			$filename = $data['page_title'].".pdf";
			$html = $this->load->view($this->_clspath.$this->_class.'_preview', $data, true);
			$this->load->library('wkpdf');
			$this->wkpdf->set_html($html);
			$this->wkpdf->set_orientation('Landscape');
			$this->wkpdf->render();
			$this->wkpdf->output('D', "$filename.pdf");
			echo closeWindow();
		}
		else {
			$this->load->view($this->_clspath.$this->_class.'_preview', $data);
		}
	}

	
	function excel() {
		$search = $this->session->userdata($this->_class.'_search');
		$parsed_search = $this->kaabar->parseSearch($search);
		$query = $this->_getPending($search, $parsed_search, $this->_fields, true);

		$this->load->helper('excel');
		to_excel($query, $this->_class . '_' . date('d-m-Y'));
	}
}
