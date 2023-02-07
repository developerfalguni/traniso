<?php

class Goods_service extends MY_Controller {
	function __construct() {
		parent::__construct();
	}
	
	function index($starting_row = 0) {
		$starting_row = intval($starting_row);
		
		$this->_fields = array('id', 'type', 'sac_hsn', 'name', 'cgst', 'sgst', 'igst', 'cess_condition');
		$this->_search = array('id', 'type', 'sac_hsn', 'name', 'cgst', 'sgst', 'igst', 'cess_condition');
		
		$this->_data['list'] = array(
			'heading' => array('ID', 'Type', 'SAC / HSN', 'Name', 'CGST', 'SGST', 'IGST', 'Cess'),
			'class' => array(
				'id'             => 'ID', 
				'type'           => 'Code', 
				'sac_hsn'        => 'Text', 
				'name'           => 'Text',
				'cgst'           => 'Numeric',
				'sgst'           => 'Numeric',
				'igst'           => 'Numeric',
				'cess_condition' => 'Text',
				),
			'link_col' => "id",
			'link_url' => $this->_clspath.$this->_class."/edit/");
		
		$this->_index($starting_row);
	}

	function edit($id) {
		$this->load->library('form_validation');
		
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('name', 'GST Service Name', 'trim|required');

		$row = $this->kaabar->getRow($this->_table, $id);
		if ($row == false) {
			$row = array(
				'id'             => 0,
				'type'           => 'Services',
				'sac_hsn'        => '',
				'name'           => '',
				'cgst'           => 0,
				'sgst'           => 0,
				'igst'           => 0,
				'cess_condition' => '',
			);
		}
		
		$data['id'] = ['id' => $id];
		$data['row'] = $row;

		if ($this->form_validation->run() == false) {
			setSessionError(validation_errors());
			
			$data['page_title'] = humanize($this->_class);
			$data['page']       = 'simple_form'; //$this->_clspath.$this->_class.'_edit';
			$data['docs_url']   = $this->_docs;
			$this->load->view('index', $data);
		}
		else {
			checkDuplicateFormSubmit($this->_clspath.$this->_class);
			
			$data = array(
				'id'             => $this->input->post('id'),
				'type'           => $this->input->post('type'),
				'sac_hsn'        => $this->input->post('sac_hsn'),
				'name'           => $this->input->post('name'),
				'cgst'           => $this->input->post('cgst'),
				'sgst'           => $this->input->post('sgst'),
				'igst'           => $this->input->post('igst'),
				'cess_condition' => $this->input->post('cess_condition'),
			);
			$id = $this->kaabar->save($this->_table, $data, ['id' => $id]);
			setSessionAlert('Changes saved successfully', 'success');
			
			redirect($this->_clspath.$this->_class);
		}
	}

	function json() {
		if ($this->input->is_ajax_request()) {
			$search = strtolower($this->input->post_get('term'));
			$sql   = "SELECT *
			FROM $this->_table
			WHERE sac_hsn LIKE '%$search%' OR
				name LIKE '%$search%' OR
				cess_condition LIKE '%$search%'
			ORDER BY sac_hsn
			LIMIT 0, 50";
			$rows = $this->db->query($sql)->result_array();
			header('Content-type: application/json; charset=utf-8');
			echo json_encode($rows, JSON_UNESCAPED_UNICODE);
		}
		else {
			echo "Access Denied";
		}
	}
}
