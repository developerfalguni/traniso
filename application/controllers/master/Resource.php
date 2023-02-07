<?php

class Resource extends MY_Controller {
	var $_table2;

	function __construct() {
		parent::__construct();
		
		$this->load->model('office');
	}
	
	function index($starting_row = 0) {
		if (! Auth::hasAccess()) {
			setSessionError('NO_PERMISSION');
			redirect('main');
		}
		
		$starting_row = intval($starting_row);
		
		$search = addslashes($this->input->post('search'));
		if($search == false && $this->input->post('search_form')) {
			$this->session->unset_userdata($this->_class.'_search'); 
			$search = false;
			redirect($this->_clspath.$this->_class);
		}
		if($search && $search != $this->session->userdata($this->_class.'_search')) {
			$this->session->set_userdata($this->_class.'_search', $search);
			redirect($this->_clspath.$this->_class);
		}
		else {
			$search = $this->session->userdata($this->_class.'_search');
		}
		$data['search'] = $search;
		$data['show_search'] = true;
		
		$data['list']['heading'] = array('ID', 'Company', 'Category', 'Type', 'Purchase Date', 'Model No', 'Active');
		$data['list']['class'] = array(
			'id'            => 'ID', 
			'company_code'  => 'Code',
			'category'      => 'Text',
			'type'          => 'Text',
			'purchase_date' => 'Date',
			'model_no'      => 'Text',
			'active'        => 'Label');
		$data['list']['link_col'] = "id";
		$data['list']['link_url'] = $this->_clspath.$this->_class."/edit/";
		$data['label_class'] = $this->office->getLabelClass();
		
		$this->load->library('pagination');
		$config['base_url']    = site_url($this->_clspath.$this->_class.'/index');
		$config['uri_segment'] = (strlen($this->_clspath) > 0 ? (3+substr_count($this->_clspath, '/')) : 3);
		$config['total_rows']  = $this->office->countResources($search);
		$config['per_page']    = Settings::get('rows_per_page');
		$this->pagination->initialize($config);
		
		$data['list']['data'] = $this->office->getResources($search, $starting_row, $config['per_page']);
		
		$data['buttons'] = array(anchor($this->_clspath.$this->_class.'/edit/0', '<i class="fa fa-plus"></i> <u>A</u>dd', 'class="btn btn-success" id="AddNew"'));
		$data['page_title'] = humanize($this->_class);
		$data['page'] = 'list';
		$this->load->view('index', $data);
	}

	function edit($id = 0) {
		$id = intval($id);
		
		$this->load->library('form_validation');
		
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('category', 'Category', 'trim|required');
		$this->form_validation->set_rules('type', 'Type', 'trim|required');
		$this->form_validation->set_rules('model_no', 'Name', 'trim|required');
		
		$row = $this->kaabar->getRow($this->_table, $id);
		if($row == false) {
			$row = array(
				'id'         => 0,
				'company_id' => 0,
				'category'   => '',
				'type'       => '',
				'purchase_date' => date('d-m-Y'),
				'model_no'   => '',
				'active'     => 'Yes'
			);
		}

		$data['id'] = array('id' => $id);
		$data['row'] = $row;
				
		if ($this->form_validation->run() == false) {
			setSessionError(validation_errors());
			
			$data['docs_url'] = $this->_docs;
			$data['page_title'] = humanize($this->_class);
			$data['hide_title'] = true;
			$data['page'] = $this->_clspath.$this->_class.'_edit';
			$this->load->view('index', $data);
		}
		else {
			checkDuplicateFormSubmit($this->_clspath.$this->_class."/edit/$id");
			
			if (Auth::hasAccess($id > 0 ? Auth::UPDATE : Auth::CREATE)) {
				$default_company = $this->session->userdata('default_company');
				$data = array(
					'company_id'=> $default_company['id'],
					'category' 	=> $this->input->post('category'),
					'type' 		=> $this->input->post('type'),
					'purchase_date' => $this->input->post('purchase_date'),
					'model_no' 	=> $this->input->post('model_no'),
					'active' 	=> ($this->input->post('active') == 'Yes' ? 'Yes' : 'No')
				);
				$id = $this->kaabar->save($this->_table, $data, $row);
				setSessionAlert('SAVED', 'success');
			}
			else
				setSessionError('NO_PERMISSION');
			
			redirect($this->_clspath.$this->_class."/edit/$id");
		}
	}
}
