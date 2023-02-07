<?php

class Issued_cheque extends MY_Controller {
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
		
		$data['list']['heading'] = array('ID', 'Company', 'Bank', 'Cheque Date', 'Cheque No', 'Favor', 'Amount', 'Realization', 'Remarks', 'Cancelled');
		$data['list']['class'] = array(
				'id'               => 'ID',
				'company_code'     => 'Text',
				'bank_name'        => 'Text',
				'cheque_date'      => 'Date',
				'cheque_no'        => 'Numeric',
				'favor'            => 'Text',
				'amount'           => 'Numeric',
				'realization_date' => 'Date',
				'remarks'          => 'Text',
				'cancelled'        => 'Label',
				);
		$data['list']['link_col'] = "id";
		$data['list']['link_url'] = $this->_clspath.$this->_class."/edit/";
		$data['label_class']      = $this->kaabar->getLabelClass();

		$this->load->library('pagination');
		$config['base_url']    = site_url($this->_clspath.$this->_class.'/index');
		$config['uri_segment'] = (strlen($this->_clspath) > 0 ? (3+substr_count($this->_clspath, '/')) : 3);
		$config['total_rows']  = $this->office->countIssuedCheques($search);
		$config['per_page']    = Settings::get('rows_per_page');
		$this->pagination->initialize($config);
		
		$data['list']['data'] = $this->office->getIssuedCheques($search, $starting_row, $config['per_page']);
		
		$data['buttons'] = array(anchor($this->_clspath.$this->_class.'/edit/0', '<i class="fa fa-plus"></i> <u>A</u>dd', 'class="btn btn-success" id="AddNew"'));
		$data['page_title'] = humanize($this->_class);
		$data['page'] = 'list';
		$this->load->view('index', $data);
	}
	
	function edit($id = 0) {
		$id = intval($id);
		
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('cheque_date', 'Cheque Date', 'trim|required');
		$this->form_validation->set_rules('cheque_no', 'Cheque No', 'trim|required');
		$this->form_validation->set_rules('favor', 'Favor', 'trim|required');
		$this->form_validation->set_rules('amount', 'Amount', 'trim|required');
		
		$row = $this->kaabar->getRow($this->_table, $id);
		if($row == false) {
			$row = array(
				'id'               => 0, 
				'company_id'       => $this->accounting->getCompanyID(),
				'bank_ledger_id'   => 0,
				'cheque_date'      => '',
				'cheque_no'        => '',
				'favor_ledger_id'  => 0,
				'favor'            => '',
				'amount'           => '',
				'realization_date' => '', 
				'cancelled'        => 'No',
				'remarks'          => ''
			);
		}
		
		$data['id'] = array('id' => $id);
		$data['row'] = $row;
		$data['bank_name'] = $this->kaabar->getField('ledgers', $row['bank_ledger_id']);

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

				$data = array(
					'company_id'       => $this->input->post('company_id'),
					'bank_ledger_id'   => $this->input->post('bank_ledger_id'),
					'cheque_date'      => $this->input->post('cheque_date'),
					'cheque_no'        => $this->input->post('cheque_no'),
					'favor'            => $this->input->post('favor'),
					'favor_ledger_id'  => $this->input->post('favor_ledger_id'),
					'amount'           => $this->input->post('amount'),
					'realization_date' => $this->input->post('realization_date'),
					'cancelled'        => $this->input->post('cancelled'),
					'remarks'          => $this->input->post('remarks')
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

	function ajaxBank($company_id) {
		if ($this->_is_ajax) {
			$search = strtolower($this->input->get('term'));		
			$sql = "SELECT L.id, L.name
			FROM ledgers L 
			WHERE L.company_id = $company_id AND L.category = 'Bank' AND L.name LIKE '%$search%' 
			ORDER BY L.name";
			$this->kaabar->getJson($sql);
		}
		else {
			echo "Access Denied";
		}
	}

	function ajaxFavor($company_id) {
		if ($this->_is_ajax) {
			$search = strtolower($this->input->get('term'));		
			$sql = "SELECT L.id, L.name
			FROM ledgers L 
			WHERE company_id = $company_id AND L.name LIKE '%$search%' 
			ORDER BY L.name
			LIMIT 0, 50";
			$this->kaabar->getJson($sql);
		}
		else {
			echo "Access Denied";
		}
	}
	
}
