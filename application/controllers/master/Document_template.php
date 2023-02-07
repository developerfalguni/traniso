<?php

class Document_template extends MY_Controller {
	function __construct() {
		parent::__construct();

		$this->load->model('office');
	}
	
	function index() {
		if (! Auth::hasAccess()) {
			setSessionError('NO_PERMISSION');
			redirect('main');
		}

		$data['show_search'] = false;
		
		$data['list']['heading'] = array('ID', 'Name');
		$data['list']['class'] = array(
			'id'   => 'ID',
			'name' => 'Text');
		$data['list']['link_col'] = 'id';
		$data['list']['link_url'] = $this->_clspath.$this->_class."/edit/";
		
		$data['list']['data'] = $this->_getDocumentTemplates();

		$data['buttons'] = array(anchor($this->_clspath.$this->_class."/edit/0", '<i class="fa fa-plus"></i> <u>A</u>dd', 'class="btn btn-success" id="AddNew"'));

		$data['page_title'] = humanize($this->_class);
		$data['hide_title'] = true;
		$data['page']       = 'list';
		$data['docs_url']   = $this->_docs;
		$this->load->view('index', $data);		
	}

	function edit($id) {
		$this->load->library('form_validation');

		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('name', 'Name', 'trim|required');
		$this->form_validation->set_rules('template', 'Template', 'trim');

		$row = $this->kaabar->getRow($this->_table, $id);
		if($row == false) {
			$row = array(
				'id'         => 0,
				'name'       => '',
				'template'   => ''
			);
		}
		$data['id'] = array('id' => $id);
		$data['row'] = $row;

		if ($this->form_validation->run() == false) {
			setSessionError(validation_errors());
			
			$data['javascript'] = array('js/jquery.base64.js');

			$data['page_title'] = humanize($this->_class);
			$data['hide_title'] = true;
			$data['page']       = $this->_clspath.$this->_class.'_edit';
			$data['docs_url']   = $this->_docs;
			$this->load->view('index', $data);
		}
		else {
			checkDuplicateFormSubmit($this->_clspath.$this->_class."/edit/$id");

			if (Auth::hasAccess(($id > 0 ? Auth::UPDATE : Auth::CREATE))) {
				$data = array(
					'name'       => $this->input->post('name'),
					'template'   => base64_decode($this->input->post('template'))
				);
				$id = $this->kaabar->save($this->_table, $data, $row);
				setSessionAlert('SAVED', 'success');
			}
			else
				setSessionError('NO_PERMISSION');

			redirect($this->_clspath.$this->_class."/edit/$id");
		}
	}

	function _getDocumentTemplates() {
		$sql   = "SELECT DT.id, DT.name FROM document_templates DT WHERE company_id = 0 ORDER BY DT.name";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function getTemplateListJS($type = 'Import') {
		$result = '';
		$rows   = $this->kaabar->getNameValuePair($this->_table, $type, 'type', 'id', 'name');
		foreach ($rows as $id => $name) {
			$result[] = array('title' => $name, 'description' => $name, 'url' => base_url('master/document_template/getTemplate/'.$id));
		}
		echo json_encode($result);
	}

	function getTemplate($id) {
		echo $this->kaabar->getField($this->_table, $id, 'id', 'template');
	}
}
