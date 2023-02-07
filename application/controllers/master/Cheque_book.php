<?php

class Cheque_book extends MY_Controller {
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
		
		$data['list']['heading'] = array('ID', 'Bank', 'Account No', 'Starting No', 'Ending No');
		$data['list']['class'] = array(
				'id'                 => 'ID',
				'bank'               => 'Text',
				'account_no'         => 'Numeric',
				'starting_cheque_no' => 'Numeric',
				'ending_cheque_no'   => 'Numeric');
		$data['list']['link_col'] = "id";
		$data['list']['link_url'] = $this->_clspath.$this->_class."/edit/";
		
		$this->load->library('pagination');
		$config['base_url']    = site_url($this->_clspath.$this->_class.'/index');
		$config['uri_segment'] = (strlen($this->_clspath) > 0 ? (3+substr_count($this->_clspath, '/')) : 3);
		$config['total_rows']  = $this->office->countChequeBooks($search);
		$config['per_page']    = Settings::get('rows_per_page');
		$this->pagination->initialize($config);
		
		$data['list']['data'] = $this->office->getChequeBooks($search, $starting_row, $config['per_page']);
		
		$data['buttons'] = array(anchor($this->_clspath.$this->_class.'/edit/0', '<i class="fa fa-plus"></i> <u>A</u>dd', 'class="btn btn-success" id="AddNew"'));
		$data['page_title'] = humanize($this->_class);
		$data['page'] = 'list';
		$this->load->view('index', $data);
	}
	
	function edit($id = 0) {
		$id = intval($id);
		
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('bank_name', 'Bank Name', 'trim|required');
		$this->form_validation->set_rules('starting_cheque_no', 'Starting Chque No', 'trim|required|exact_length[6]');
		$this->form_validation->set_rules('ending_cheque_no', 'Ending Chque No', 'trim|required|exact_length[6]');
		$this->form_validation->set_rules('account_no', 'Account No', 'trim|required');
		
		$row = $this->kaabar->getRow($this->_table, $id);
		if($row == false) {
			$row = array(
				'id' => 0,
				'bank_id' => 0,
				'starting_cheque_no' => '',
				'ending_cheque_no' => '',
				'account_no' => ''
			);
		}
		
		$data['id'] = array('id' => $id);
		$data['row'] = $row;

		$data['bank_name']  = $this->kaabar->getField('banks', $row['bank_id']);
								
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
			
			$company_id = $this->session->userdata('default_company');
			$company = $company_id['id'];
			if (Auth::hasAccess($id > 0 ? Auth::UPDATE : Auth::CREATE)) {
				$data = array(
					'company_id' => $company,
					'bank_id' => $this->input->post('bank_id'),
					'starting_cheque_no' => $this->input->post('starting_cheque_no'),
					'ending_cheque_no' => $this->input->post('ending_cheque_no'),
					'account_no' => $this->input->post('account_no')
				);
				$id = $this->kaabar->save($this->_table, $data, $row);
				setSessionAlert('SAVED', 'success');
			}
			else {
				setSessionError('NO_PERMISSION');
			}
			
			redirect($this->_clspath.$this->_class."/edit/$id");
		}
	}

	function ajaxBanks() {
		if ($this->_is_ajax) {
			$search = strtolower($this->input->get('term'));		
			$sql = "SELECT id, name
			FROM banks
			WHERE name LIKE '%$search%' 
			ORDER BY name";
			$this->kaabar->getJson($sql);
		}
		else {
			echo "Access Denied";
		}
	}
	
}
