<?php

class Pending_bill extends MY_Controller {
	function __construct() {
		parent::__construct();
		
		$this->_table = 'ledgers';

		$this->_fields = array(
			'shipment' => 'J.type',
			'type'     => 'J.cargo_type',
			'group'    => 'PL.group_name',
			'party'    => 'P.name',
			'category' => 'PRD.category',
			'product'  => 'PRD.name',
			'cha'      => 'CHA.name',
			'vessel'   => "CONCAT(V.prefix, ' ', V.name, ' ', V.voyage_no)"
		);
	}
	
	function index($starting_row = 0) {
		$starting_row = intval($starting_row);
		
		// $search = addslashes($this->input->post('search'));
		// if ($search == false && $this->input->post('search_form')) {
		// 	$this->session->unset_userdata($this->_class.'_search'); 
		// 	$search = false;
		// 	redirect($this->_clspath.$this->_class);
		// }
		// if ($search && $search != $this->session->userdata($this->_class.'_search')) {
		// 	$this->session->set_userdata($this->_class.'_search', $search);
		// 	redirect($this->_clspath.$this->_class);
		// }
		// else {
		// 	$search = $this->session->userdata($this->_class.'_search');
		// }
		
		$default_company = $this->session->userdata("default_company");
		$data['company'] = $this->kaabar->getRow('companies', $default_company['id']);
		$data['years']   = explode('_', $default_company['financial_year']);

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

		$data['from_date'] = $from_date ? $from_date : date('01-m-Y');
		$data['to_date']   = $to_date ? $to_date : date('d-m-Y');
		$data['search']    = $search;
		$this->_parsed_form 	 = $this->kaabar->parseSearch($advance_form);
		$this->_parsed_filter  	 = $this->kaabar->parseSearch($advance_filter_form);
		
		$data['parsed_search']	= $this->_parsed_form;
		if (is_array($this->_parsed_form)) {
			$advance_form = '';
			foreach ($this->_parsed_form as $key => $value) {
				$advance_form .= $key.':'.$value.' ';
			}
			$data['advance_form'] = $advance_form;
		}

		$data['parsed_filter']	= $this->_parsed_filter;
		if (is_array($this->_parsed_filter)) {
			$advance_filter_form = '';
			foreach ($this->_parsed_filter as $key => $value) {
				$advance_filter_form .= $key.':'.$value.' ';
			}
			$data['advance_filter_form'] = $advance_filter_form;
		}

		$data['search_fields']	= $this->_fields;
		$this->_parsed_search 	 = $this->kaabar->parseFilterSearch($this->_parsed_form , $this->_parsed_filter);

		$data['show_search'] = false;
		$data['advance_search'] = true;

		$data['list']['heading'] = array('Job', 'Type', 'Job Date', 'Billing Party', 'Amount');
		$data['list']['class'] = array(
			'id2_format'    => array('class' => 'tiny', 'link' => 'id'), 
			'cargo_type'    => 'Text',
			'date'          => 'Text',
			'party_name'    => 'Text',
			'amount' 		=> 'Text');
		$data['list']['link_col'] = "id2_format";
		$data['list']['link_url'] = $this->_clspath.$this->_class."/edit/";
		$data['label_class'] = $this->accounting->getLabelClass();
		
		$this->load->library('pagination');
		$config['base_url']    = site_url($this->_clspath.$this->_class.'/index');
		$config['uri_segment'] = (strlen($this->_clspath) > 0 ? (3+substr_count($this->_clspath, '/')) : 3);
		$config['total_rows']  = $this->_count($search);
		$config['per_page']    = Settings::get('rows_per_page');
		$this->pagination->initialize($config);
		
		$data['list']['data'] = $this->_get($search, $starting_row, $config['per_page']);

		$data['from_date'] = '';
		$data['to_date'] = '';
		$data['search_list'] = '';
		$data['search_in'] = '';

		$data['list']['preload_page'] = 'advance_search';
		// $data['list']['search_form'] = 'search_form';
		
		// $data['buttons'] = array(anchor($this->_clspath.$this->_class.'/edit/0', '<i class="fa fa-plus"></i><span class="hidden-xs"> Add</span>', 'class="btn btn-success" id="AddNew"'));
		$data['page_title'] = humanize($this->_class);
		$data['page'] = 'list';
		$this->load->view('index', $data);
	}

	function _count($search = '') {
		$sql = "SELECT COUNT(C.id) AS numrows
		FROM costsheets C
			INNER JOIN jobs J ON C.job_id = J.id
		WHERE J.cargo_type LIKE '%$search%' OR			
			C.particulars LIKE '%$search%'";
		$query = $this->db->query($sql);
		$row = $query->row_array();
		if (! $row)
			$row['numrows'] = 0;
		return $row['numrows'];
	}

	function _get($search = '', $offset = 0, $limit = 25) {
		$sql = "SELECT J.id, J.id2_format, J.cargo_type, J.date, P.name as party_name, (select sum(C.sell_amount) from costsheets C where C.job_id = J.id GROUP BY C.job_id ) as amount
		FROM jobs J
			LEFT OUTER JOIN parties P ON P.id = J.party_id
		WHERE J.cargo_type LIKE '%$search%'
		group BY j.id
		ORDER BY J.id, J.date
		
		LIMIT $offset, $limit";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	// function edit($id = 0) {
	// 	$id = intval($id);
		
	// 	$this->load->library('form_validation');
		
	// 	$this->form_validation->set_error_delimiters('', '');
	// 	$this->form_validation->set_rules('code', 'Code', 'trim|required');
	// 	$this->form_validation->set_rules('name', 'Name', 'trim|required');
		
	// 	$row = $this->kaabar->getRow($this->_table, $id);
	// 	if ($row == false) {
	// 		$row = array(
	// 			'id'               => 0,
	// 			'company_id'       => 0,
	// 			'type'             => 'Services',
	// 			'code'             => '',
	// 			'name'             => '',
	// 			'stax_category_id' => 0,
	// 			'sac_hsn'          => 0,
	// 			'cgst'             => 0,
	// 			'sgst'             => 0,
	// 			'igst'             => 0,
	// 			'reimbursement'    => 'No', 
	// 			'job_required'     => 'No', 
	// 			'active'           => 'Yes',
	// 			'remarks'          => ''
	// 		);

	// 		if ($this->input->method() == 'post') {
	// 			$post = $this->input->post(array_keys($row));
	// 			foreach($post as $f => $v) {
	// 				if ($v) $row[$f] = $v;
	// 			}
	// 		}
	// 	}

	// 	$data['row'] = $row;
	// 	$data['id']  = $id;
				
	// 	if ($this->form_validation->run() == false) {
	// 		setSessionError(validation_errors());
			
	// 		$data['page_title'] = humanize($this->_class);
	// 		$data['page']       = $this->_clspath.$this->_class.'_edit';
	// 		$data['docs_url']   = $this->_docs;
	// 		$this->load->view('index', $data);
	// 	}
	// 	else {
	// 		checkDuplicateFormSubmit($this->_clspath.$this->_class."/edit/$id");
			
	// 		$data = array(
	// 			'company_id'       => $this->accounting->getCompanyID(),
	// 			'category'         => 'Bill Items',
	// 			'type'             => $this->input->post('type'),
	// 			'code'             => $this->input->post('code'),
	// 			'name'             => $this->input->post('name'),
	// 			'stax_category_id' => $this->input->post('stax_category_id'),
	// 			'sac_hsn'          => $this->input->post('sac_hsn'),
	// 			'cgst'             => $this->input->post('cgst'),
	// 			'sgst'             => $this->input->post('sgst'),
	// 			'igst'             => $this->input->post('igst'),
	// 			'reimbursement'    => ($this->input->post('reimbursement') == 'Yes' ? 'Yes' : 'No'),
	// 			'job_required'     => ($this->input->post('job_required') == 'Yes' ? 'Yes' : 'No'),
	// 			'active'           => ($this->input->post('active') == 'Yes' ? 'Yes' : 'No'),
	// 			'remarks'          => $this->input->post('remarks')
	// 		);
	// 		$id = $this->kaabar->save($this->_table, $data, ['id' => $id]);

	// 		setSessionAlert('Changes saved successfully', 'success');

	// 		redirect($this->_clspath.$this->_class."/edit/$id");
	// 	}
	// }

	// function delete($id = 0, $field = 'id') {
	// 	$can_delete = TRUE;
	// 	$query = $this->db->query("SELECT id FROM voucher_details WHERE bill_item_id = $id");
	// 	if ($query->num_rows() > 0)
	// 		$can_delete = FALSE;

	// 	$query = $this->db->query("SELECT id FROM bill_templates WHERE bill_item_id = $id");
	// 	if ($query->num_rows() > 0)
	// 		$can_delete = FALSE;

	// 	if ($can_delete) {
	// 		$this->kaabar->delete($this->_table, $id);
	// 		setSessionAlert('Bill Item Deleted Successfully', 'success');
	// 	}
	// 	else
	// 		setSessionError('Cannot Delete. Bill Item is currently in Use.');

	// 	redirect($this->_clspath.$this->_class.'/index');
	// }

	// function getJSON($table = FALSE, $field = 'name', $field2 = false) {
	// 	if ($this->input->is_ajax_request()) {
	// 		$search = strtolower($this->input->post_get('term'));
	// 		$sql = "SELECT DISTINCT id, $field FROM $table WHERE category = 'Bill Items' AND $field LIKE '%$search%' ORDER BY $field LIMIT 0, 50";
	// 		$query  = $this->db->query($sql);
	// 		$result = $query->result_array();

	// 		header('Content-type: application/json; charset=utf-8');
	// 		echo json_encode($result, JSON_UNESCAPED_UNICODE);
	// 	}
	// 	else
	// 		echo "Access Denied";
	// }
}
