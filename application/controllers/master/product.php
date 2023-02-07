<?php

class Product extends MY_Controller {
	function __construct() {
		parent::__construct();
	}
	
	function index($starting_row = 0) {
		
		$starting_row = intval($starting_row);
		
		$this->_fields = array('id', 'code', 'name', 'category');
		$this->_search = array('code', 'name', 'category');
		
		$this->_data['list'] = array(
			'heading' => array('ID', 'Code', 'Name', 'Category'),
			'class' => array(
				'id'       => 'ID', 
				'code'     => 'Code',
				'name'     => 'Text',
				'category' => 'Text'),
			'link_col' => "id",
			'link_url' => $this->_clspath.$this->_class."/edit/");
		
		$this->_index($starting_row);
	}

	function edit($id) {
		$this->load->library('form_validation');
		
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('code', 'Product Code', 'trim');
		$this->form_validation->set_rules('name', 'Product Name', 'trim|required');

		$row = $this->kaabar->getRow($this->_table, $id);
		if ($row == false) {
			$row = array(
				'id'       => 0,
				'code'     => '',
				'name'     => '',
				'category' => '',
			);
		}
		
		$data['id'] = array('id' => $id);
		$data['row'] = $row;

		if ($this->form_validation->run() == false) {
			setSessionError(validation_errors());
			
			$data['page_title'] = humanize($this->_class);
			$data['hide_title'] = true;
			$data['page'] = 'simple_form'; //$this->_clspath.$this->_class.'_edit';
			$data['docs_url'] = $this->_docs;
			$this->load->view('index', $data);
		}
		else {
			checkDuplicateFormSubmit($this->_clspath.$this->_class."/edit/$id");
			
			$data = array(
				'code'     => $this->input->post('code'),
				'name'     => $this->input->post('name'),
				'category' => $this->input->post('category')
			);
			$id = $this->kaabar->save($this->_table, $data, ['id' => $id]);
			setSessionAlert('Changes saved successfully', 'success');

			redirect($this->_clspath.$this->_class."/edit/$id");
		}
	}
}
