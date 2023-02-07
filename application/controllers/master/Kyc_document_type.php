<?php

class Kyc_document_type extends MY_Controller {
	function __construct() {
		parent::__construct();
		
		$this->load->model('office');
	}
		
	function index() {
		if (! Auth::hasAccess()) {
			setSessionError('NO_PERMISSION');
			redirect('main');
		}

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
		
		$data['list'] = array(
			'heading' => array('ID', 'Company Type'),
			'class' => array(
				'id'           => 'ID',
				'company_type' => 'Text'),
			'link_col' => "id",
			'link_url' => $this->_clspath.$this->_class."/edit/");
		
		$data['list']['data'] = $this->office->getKycDocumentTypes($search);
		
		$data['buttons'] = array(anchor($this->_clspath.$this->_class."/edit/0", '<i class="fa fa-plus"></i> <u>A</u>dd', 'class="btn btn-success" id="AddNew"'));

		$data['page_title'] = humanize($this->_class);
		$data['page'] = 'list';

		$data['docs_url'] = $this->_docs;
		$this->load->view('index', $data);
	}
	
	function edit($id) {
		$this->load->library('form_validation');
		
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('deductee_id', 'Company Type', 'trim|required');
		
		$row = $this->office->getKycDocumentType($id);
		if($row == false) {
			$row = array(
				'deductee_id' => 0,
				'documents'   => array()
			);
		}
		
		$data['id'] = array('id' => $id);
		$data['row'] = $row;

		if ($this->form_validation->run() == false) {
			setSessionError(validation_errors());
			
			$data['page_title'] = humanize($this->_class);
			$data['hide_title'] = true;
			$data['page']       = $this->_clspath.$this->_class.'_edit';
			$data['docs_url']   = $this->_docs;
			$this->load->view('index', $data);
		}
		else {
			checkDuplicateFormSubmit($this->_clspath.$this->_class."/edit/$id");
			
			if (Auth::hasAccess($id > 0 ? Auth::UPDATE : Auth::CREATE)) {
				$delete_ids = $this->input->post('delete_id') == false ? array("0" => "0") : $this->input->post('delete_id');
				$codes = $this->input->post('code');
				$new_codes = $this->input->post('new_code');

				if ($codes != null) {
					$names = $this->input->post('name');
					foreach ($codes as $index => $code) {
						if (! in_array("$index", $delete_ids)) {
							$row = array(
								'deductee_id' => $this->input->post('deductee_id'),
								'code'        => strtoupper($code),
								'name'        => $names[$index]
							);
							$id = $this->kaabar->save($this->_table, $row, array('id' => $index));
						}
					}
				}

				if ($delete_ids != null) {
					foreach ($delete_ids as $index) {
						if ($index > 0) {
							$this->db->delete($this->_table, array('id' => $index));
						}
					}
				}

				if ($new_codes != null) {
					$names = $this->input->post('new_name');
					foreach ($new_codes as $index => $code) {
						if (strlen(trim($names[$index])) > 0) {
							$row = array(
								'deductee_id' => $this->input->post('deductee_id'),
								'code'        => strtoupper($code),
								'name'        => $names[$index]
							);
							$id = $this->kaabar->save($this->_table, $row);
						}
					}
				}
				setSessionAlert('SAVED', 'success');
			}
			else
				setSessionError('NO_PERMISSION');
			
			redirect($this->_clspath.$this->_class."/edit/$id");
		}
	}
}
