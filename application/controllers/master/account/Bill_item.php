<?php

class Bill_item extends MY_Controller {
	function __construct() {
		parent::__construct();
		
		$this->_table = 'ledgers';
	}
	
	function index($starting_row = 0) {
		// $starting_row = intval($starting_row);
		
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
		// $data['search'] = $search;
		// $data['show_search'] = true;
		
		// $data['list']['heading'] = array('ID', 'Type', 'Code', 'Name', 'S.Tax Category', 'Reimbursment', 'Job Required', 'Active');
		// $data['list']['class'] = array(
		// 	'id'            => 'ID', 
		// 	'type'          => 'Code',
		// 	'code'          => 'Code',
		// 	'name'          => 'Text',
		// 	'stax_category' => 'Text',
		// 	'reimbursement' => 'Label',
		// 	'job_required'  => 'Label',
		// 	'active'        => 'Label');
		// $data['list']['link_col'] = "id";
		// $data['list']['link_url'] = $this->_clspath.$this->_class."/edit/";
		// $data['label_class'] = $this->accounting->getLabelClass();
		
		// $this->load->library('pagination');
		// $config['base_url']    = site_url($this->_clspath.$this->_class.'/index');
		// $config['uri_segment'] = (strlen($this->_clspath) > 0 ? (3+substr_count($this->_clspath, '/')) : 3);
		// $config['total_rows']  = $this->_count($search);
		// $config['per_page']    = Settings::get('rows_per_page');
		// $this->pagination->initialize($config);
		
		// $data['list']['data'] = $this->_get($search, $starting_row, $config['per_page']);
		
		// $data['buttons'] = array(anchor($this->_clspath.$this->_class.'/edit/0', '<i class="fa fa-plus"></i><span class="hidden-xs"> Add</span>', 'class="btn btn-success" id="AddNew"'));
		// $data['page_title'] = humanize($this->_class);
		// $data['page'] = 'list';
		// $this->load->view('index', $data);
		redirect($this->_clspath.$this->_class.'/edit/0');
	}

	function getBill($bill_id = 0){

		$response = [];
		
		if($bill_id > 0){

			$row = $this->kaabar->getRow('ledgers', $bill_id);
			
			$response['success'] = true;
			$response['row'] = $row;
		}
		else
		{
			$response['success'] = false;
			$response['messages'] = 'Plaese select BillItem';
		}

		header('Content-type: application/json; charset=utf-8');
		echo json_encode($response, JSON_UNESCAPED_UNICODE);
	}

	// function _count($search = '') {
	// 	$sql = "SELECT COUNT(L.id) AS numrows
	// 	FROM ledgers L
	// 		INNER JOIN companies C ON (L.company_id = ? AND L.category = 'Bill Items' AND L.company_id = C.id)
	// 		LEFT OUTER JOIN stax_categories STAX ON L.stax_category_id = STAX.id
	// 	WHERE L.code LIKE '%$search%' OR
	// 		L.name LIKE '%$search%' OR
	// 		C.code LIKE '%$search%' OR
	// 		C.name LIKE '%$search%' OR
	// 		STAX.name LIKE '%$search%'";
	// 	$query = $this->db->query($sql, [$this->_company['id']]);
	// 	$row = $query->row_array();
	// 	if (! $row)
	// 		$row['numrows'] = 0;
	// 	return $row['numrows'];
	// }

	// function _get($search = '', $offset = 0, $limit = 25) {
	// 	$sql = "SELECT L.id, C.code AS company_code, L.type, L.code, L.name, 
	// 		STAX.name AS stax_category, L.reimbursement, L.job_required, L.active
	// 	FROM ledgers L
	// 		INNER JOIN companies C ON (L.company_id = ? AND L.category = 'Bill Items' AND L.company_id = C.id)
	// 		LEFT OUTER JOIN stax_categories STAX ON L.stax_category_id = STAX.id
	// 	WHERE L.code LIKE '%$search%' OR
	// 		L.name LIKE '%$search%' OR
	// 		C.code LIKE '%$search%' OR
	// 		C.name LIKE '%$search%' OR
	// 		STAX.name LIKE '%$search%'
	// 	ORDER BY L.category, L.name
	// 	LIMIT $offset, $limit";
	// 	$query = $this->db->query($sql, [$this->_company['id']]);
	// 	return $query->result_array();
	// }

	function edit_old($id = 0) {
		$id = intval($id);
		
		$this->load->library('form_validation');
		
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('code', 'Code', 'trim|required');
		$this->form_validation->set_rules('name', 'Name', 'trim|required');
		
		$row = $this->kaabar->getRow($this->_table, $id);
		if ($row == false) {
			$row = array(
				'id'               => 0,
				'company_id'       => 0,
				'type'             => 'Services',
				'code'             => '',
				'name'             => '',
				'stax_category_id' => 0,
				'sac_hsn'          => 0,
				'billing_type'     => 'NA',
				'cgst'             => 0,
				'sgst'             => 0,
				'igst'             => 0,
				'reimbursement'    => 'No', 
				'job_required'     => 'No', 
				'active'           => 'Yes',
				'remarks'          => ''
			);

			if ($this->input->method() == 'post') {
				$post = $this->input->post(array_keys($row));
				foreach($post as $f => $v) {
					if ($v) $row[$f] = $v;
				}
			}
		}

		$data['row'] = $row;
		$data['id']  = $id;
				
		if ($this->form_validation->run() == false) {
			setSessionError(validation_errors());
			
			$data['page_title'] = humanize($this->_class);
			$data['page']       = $this->_clspath.$this->_class.'_edit';
			$data['docs_url']   = $this->_docs;
			$this->load->view('index', $data);
		}
		else {
			checkDuplicateFormSubmit($this->_clspath.$this->_class."/edit/$id");
			
			$data = array(
				'company_id'       => $this->accounting->getCompanyID(),
				'category'         => 'Bill Items',
				'type'             => $this->input->post('type'),
				'code'             => $this->input->post('code'),
				'name'             => $this->input->post('name'),
				'stax_category_id' => $this->input->post('stax_category_id'),
				'sac_hsn'          => $this->input->post('sac_hsn'),
				'billing_type'     => $this->input->post('billing_type'),
				'cgst'             => $this->input->post('cgst'),
				'sgst'             => $this->input->post('sgst'),
				'igst'             => $this->input->post('igst'),
				'reimbursement'    => ($this->input->post('reimbursement') == 'Yes' ? 'Yes' : 'No'),
				'job_required'     => ($this->input->post('job_required') == 'Yes' ? 'Yes' : 'No'),
				'active'           => ($this->input->post('active') == 'Yes' ? 'Yes' : 'No'),
				'remarks'          => $this->input->post('remarks')
			);
			$id = $this->kaabar->save($this->_table, $data, ['id' => $id]);

			setSessionAlert('Changes saved successfully', 'success');

			redirect($this->_clspath.$this->_class."/edit/$id");
		}
	}

	function edit($id = 0) {
		
		$data['id'] = array('id' => 0);
		$query = $this->db->get($this->_table);
		$data['count'] = $query->num_rows();
		$data['page_title'] = humanize($this->_class);
		$data['page']       = $this->_clspath.$this->_class.'_edit';
		$data['docs_url']   = $this->_docs;
		$this->load->view('index', $data);
	}

	function ajaxEdit() {
		
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('code', 'Code', 'trim|required');
		$this->form_validation->set_rules('name', 'Name', 'trim|required');
		
		$this->form_validation->set_error_delimiters('<p class="text-danger">','</p>');
		if ($this->form_validation->run() == false) {
			
			$response['success'] = false;
        	foreach ($_POST as $key => $value) {
        		$response['messages'][$key] = form_error($key);
        	}
				
		}
		else {
			
			$row_id = $this->input->post('id');

			if (Auth::hasAccess($row_id > 0 ? Auth::UPDATE : Auth::CREATE)) {
			
			$data = array(
				'company_id'       => $this->accounting->getCompanyID(),
				'category'         => 'Bill Items',
				'type'             => $this->input->post('type'),
				'code'             => $this->input->post('code'),
				'name'             => $this->input->post('name'),
				'stax_category_id' => $this->input->post('stax_category_id'),
				'sac_hsn'          => $this->input->post('sac_hsn'),
				//'billing_type'     => $this->input->post('billing_type'),
				'cgst'             => $this->input->post('cgst'),
				'sgst'             => $this->input->post('sgst'),
				'igst'             => $this->input->post('igst'),
				'reimbursement'    => ($this->input->post('reimbursement') == 'Yes' ? 'Yes' : 'No'),
				'job_required'     => ($this->input->post('job_required') == 'Yes' ? 'Yes' : 'No'),
				'active'           => ($this->input->post('active') == 'Yes' ? 'Yes' : 'No'),
				'remarks'          => $this->input->post('remarks')
			);
			$id = $this->kaabar->save($this->_table, $data, ['id' => $row_id]);

			if($id){
					$response['success'] = true;
		        	$response['messages'] = 'Succesfully Saved';	
				}
				else
				{
					$response['success'] = false;
					$response['messages'] = 'Somathing Went Wrong';	
				}
			}
		}
		header('Content-type: application/json; charset=utf-8');
		echo json_encode($response, JSON_UNESCAPED_UNICODE);
	}

	function delete($id = 0, $field = 'id') {
		$response = [];
		if ($this->input->is_ajax_request()) {
			if($id){
				
				$this->db->where(array('bill_item_id' => $id));
				$result = $this->db->get('costsheets')->num_rows();
				
				if($result > 0)
				{
					$response['status'] = 'error';
					$response['msg'] = 'You can not delete Bill Item is used in COSTSHEET';			
				}
				else
				{
					$this->db->delete($this->_table, ['id' => $id]);
	
					$response['status'] = 'success';
					$response['msg'] = 'Successfully Deleted Bill Item';			
				}
			}
			else
			{
				$response['status'] = 'error';
				$response['msg'] = 'Please Select Bill Item First then delete';		
			}
			header('Content-type: application/json; charset=utf-8');
			echo json_encode($response, JSON_UNESCAPED_UNICODE);
		}
		else
			echo "Access Denied";
	}

	function getJSON($table = FALSE, $field = 'name', $field2 = false) {
		if ($this->input->is_ajax_request()) {
			$search = strtolower($this->input->post_get('term'));
			$sql = "SELECT DISTINCT id, $field FROM $table WHERE category = 'Bill Items' AND $field LIKE '%$search%' ORDER BY $field LIMIT 0, 50";
			$query  = $this->db->query($sql);
			$result = $query->result_array();

			header('Content-type: application/json; charset=utf-8');
			echo json_encode($result, JSON_UNESCAPED_UNICODE);
		}
		else
			echo "Access Denied";
	}

	function billList() {
		$json = [];
		if ($this->input->is_ajax_request()) {

			if(!empty($this->input->get("q"))){
				$this->db->like('name', $this->input->get("q"));
			}
			
			$query = $this->db->select('id, name as text')
							->limit(10)
							->get('ledgers');
							
			$json = $query->result_array();
			
			// $json[] = ['id' => 0, 'text' => 'New BillItem'];

			echo json_encode($json);
		}
		else
			echo "Access Denied";
   }
}
