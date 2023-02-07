<?php

class Container_type extends MY_Controller {
	function __construct() {
		parent::__construct();
	}
		
	function index($starting_row = 0) {
		if (! Auth::hasAccess()) {
			setSessionError('NO_PERMISSION');
			redirect('main');
		}
		$starting_row = intval($starting_row);
		
		$this->_fields = array('id', 'code', 'size', 'name');
		$this->_search = array('code', 'size', 'name');
		
		$this->_data['list'] = array(
			'heading' => array('ID', 'Code', 'Size', 'Name'),
			'class' => array(
				'id' => 'ID', 
				'code' => 'Code',
				'size' => 'Code',
				'name' => 'Text'),
			'link_col' => "id",
			'link_url' => $this->_clspath.$this->_class."/edit/");
		
		$this->_index($starting_row);
	}

	function edit($id) {
		$this->load->library('form_validation');
		
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('code', 'Container Type Code', 'trim|required');
		$this->form_validation->set_rules('size', 'Container Type Size', 'trim|required');
		$this->form_validation->set_rules('name', 'Container Type Name', 'trim|required');

		$row = $this->kaabar->getRow($this->_table, $id);
		if($row == false) {
			$row = array(
				'id'   => 0,
				'code' => '',
				'size' => 0,
				'name' => ''
			);
		}
		
		$data['id'] = array('id' => $id);
		$data['row'] = $row;

		if ($this->form_validation->run() == false) {
			setSessionError(validation_errors());
			
			$data['page_title'] = humanize($this->_class);
			$data['hide_title'] = true;
			$data['page']       = 'simple_form'; //$this->_clspath.$this->_class.'_edit';
			$data['docs_url']   = $this->_docs;
			$this->load->view('index', $data);
		}
		else {
			checkDuplicateFormSubmit($this->_clspath.$this->_class."/edit/$id");
			
			if (Auth::hasAccess($id > 0 ? Auth::UPDATE : Auth::CREATE)) {
				$data = array(
					'code' => $this->input->post('code'),
					'size' => $this->input->post('size'),
					'name' => $this->input->post('name')
				);
				$id = $this->kaabar->save($this->_table, $data, $row);
				setSessionAlert('SAVED', 'success');
			}
			else
				setSessionError('NO_PERMISSION');

			redirect($this->_clspath.$this->_class."/edit/$id");
		}
	}

	function ajax() {
		if ($this->input->is_ajax_request()) {
			$search = strtolower($this->input->get('term'));
			$sql = "SELECT id, CONCAT(size, ' ', name) AS name
				FROM " . $this->_table . "
				WHERE size LIKE '%$search%' OR name LIKE '%$search%'
				ORDER BY size";
			$this->kaabar->getJson($sql);
		}
		else {
			echo "Access Denied";
		}
	}

	function ajaxSize() {
		if ($this->input->is_ajax_request()) {
			$search = strtolower($this->input->get('term'));
			$sql = "SELECT id, size as name
				FROM " . $this->_table . "
				WHERE size LIKE '%$search%'
				 GROUP BY size ORDER BY size";
			$this->kaabar->getJson($sql);
		}
		else {
			echo "Access Denied";
		}
	}
}
