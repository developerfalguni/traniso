<?php

class Bill_item extends MY_Controller {
	function __construct() {
		parent::__construct();
		
		$this->_table = 'ledgers';
	}
	
	function index($starting_row = 0) {
		$starting_row = intval($starting_row);
		
		$search = addslashes($this->input->post('search'));
		if ($search == false && $this->input->post('search_form')) {
			$this->session->unset_userdata($this->_class.'_search'); 
			$search = false;
			redirect($this->_clspath.$this->_class);
		}
		if ($search && $search != $this->session->userdata($this->_class.'_search')) {
			$this->session->set_userdata($this->_class.'_search', $search);
			redirect($this->_clspath.$this->_class);
		}
		else {
			$search = $this->session->userdata($this->_class.'_search');
		}
		$data['search'] = $search;
		$data['show_search'] = true;
		
		$data['list']['heading'] = array('ID', 'Type', 'Code', 'Name', 'S.Tax Category', 'Reimbursment', 'Job Required', 'Active');
		$data['list']['class'] = array(
			'id'            => 'ID', 
			'type'          => 'Code',
			'code'          => 'Code',
			'name'          => 'Text',
			'stax_category' => 'Text',
			'reimbursement' => 'Label',
			'job_required'  => 'Label',
			'active'        => 'Label');
		$data['list']['link_col'] = "id";
		$data['list']['link_url'] = $this->_clspath.$this->_class."/edit/";
		$data['label_class'] = $this->accounting->getLabelClass();
		
		$this->load->library('pagination');
		$config['base_url']    = site_url($this->_clspath.$this->_class.'/index');
		$config['uri_segment'] = (strlen($this->_clspath) > 0 ? (3+substr_count($this->_clspath, '/')) : 3);
		$config['total_rows']  = $this->_count($search);
		$config['per_page']    = Settings::get('rows_per_page');
		$this->pagination->initialize($config);
		
		$data['list']['data'] = $this->_get($search, $starting_row, $config['per_page']);
		
		$data['buttons'] = array(anchor($this->_clspath.$this->_class.'/edit/0', '<i class="fa fa-plus"></i><span class="hidden-xs"> Add</span>', 'class="btn btn-success" id="AddNew"'));
		$data['page_title'] = humanize($this->_class);
		$data['page'] = 'list';
		$this->load->view('index', $data);
	}

	function _count($search = '') {
		$sql = "SELECT COUNT(L.id) AS numrows
		FROM ledgers L
			INNER JOIN companies C ON (L.company_id = ? AND L.category = 'Bill Items' AND L.company_id = C.id)
			LEFT OUTER JOIN stax_categories STAX ON L.stax_category_id = STAX.id
		WHERE L.code LIKE '%$search%' OR
			L.name LIKE '%$search%' OR
			C.code LIKE '%$search%' OR
			C.name LIKE '%$search%' OR
			STAX.name LIKE '%$search%'";
		$query = $this->db->query($sql, [$this->_company['id']]);
		$row = $query->row_array();
		if (! $row)
			$row['numrows'] = 0;
		return $row['numrows'];
	}

	function _get($search = '', $offset = 0, $limit = 25) {
		$sql = "SELECT L.id, C.code AS company_code, L.type, L.code, L.name, 
			STAX.name AS stax_category, L.reimbursement, L.job_required, L.active
		FROM ledgers L
			INNER JOIN companies C ON (L.company_id = ? AND L.category = 'Bill Items' AND L.company_id = C.id)
			LEFT OUTER JOIN stax_categories STAX ON L.stax_category_id = STAX.id
		WHERE L.code LIKE '%$search%' OR
			L.name LIKE '%$search%' OR
			C.code LIKE '%$search%' OR
			C.name LIKE '%$search%' OR
			STAX.name LIKE '%$search%'
		ORDER BY L.category, L.name
		LIMIT $offset, $limit";
		$query = $this->db->query($sql, [$this->_company['id']]);
		return $query->result_array();
	}

	function edit($id = 0) {
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

	function delete($id = 0, $field = 'id') {
		$can_delete = TRUE;
		$query = $this->db->query("SELECT id FROM voucher_details WHERE bill_item_id = $id");
		if ($query->num_rows() > 0)
			$can_delete = FALSE;

		$query = $this->db->query("SELECT id FROM bill_templates WHERE bill_item_id = $id");
		if ($query->num_rows() > 0)
			$can_delete = FALSE;

		if ($can_delete) {
			$this->kaabar->delete($this->_table, $id);
			setSessionAlert('Bill Item Deleted Successfully', 'success');
		}
		else
			setSessionError('Cannot Delete. Bill Item is currently in Use.');

		redirect($this->_clspath.$this->_class.'/index');
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
}
