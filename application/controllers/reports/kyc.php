<?php

class Kyc extends MY_Controller {
	var $_fields;
	var $_parsed_search;

	function __construct() {
		parent::__construct();
	
		$this->_fields = array(
			'account_group' => 'AG.name',
			'group' => 'L.group_name',
			'name'  => 'P.name',
			'doc'   => 'KDT.name'
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

		$data['page_title'] = humanize($this->_class);
		$data['hide_title'] = true;
		$data['page'] = $this->_clspath.$this->_class;
		$data['docs_url'] = $this->_docs;
		$this->load->view('index', $data);
	}

	function _getPending($search, $parsed_search, $fields, $query_only = false) {
		$default_company = $this->session->userdata('default_company');

		$sql = "SELECT P.id, TDS.name AS type, P.name, L.id AS ledger_id, CONCAT(L.code, ' - ', L.name) AS ledger, L.group_name, AG.name AS account_group, 
			GROUP_CONCAT(IF(LENGTH(TRIM(KYC.file)) > 0, CONCAT('<a href=\'/master/kyc/index/', P.id, '/', KYC.id, '\' class=\'Popup\'>', KDT.name, '</a>'), NULL) ORDER BY KDT.name SEPARATOR ', ') AS attached_docs,
			GROUP_CONCAT(IF(ISNULL(KYC.file) OR LENGTH(TRIM(KYC.file)) = 0, KDT.name, NULL) ORDER BY KDT.name SEPARATOR ', ') AS pending_docs
		FROM ((((parties P LEFT OUTER JOIN ledgers L ON (L.company_id = ? AND P.id = L.party_id))
			LEFT OUTER JOIN account_groups AG ON L.account_group_id = AG.id)
			LEFT OUTER JOIN tds_classes TDS ON L.tds_class_id = TDS.id)
			LEFT OUTER JOIN kyc_document_types KDT ON L.tds_class_id = KDT.deductee_id)
			LEFT OUTER JOIN kyc_documents KYC ON (P.id = KYC.party_id AND KYC.kyc_document_type_id = KDT.id)
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
		GROUP BY P.id
		ORDER BY L.group_name, P.name";
		$query = $this->db->query($sql, array($default_company['id']));

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
