<?php

class Group extends MY_Controller {
	function __construct() {
		parent::__construct();
	}
	
	function index($starting_row = 0) {
		if (! Auth::isAdmin()) {
			setSessionError('You don&rsquo;t have enough permission');
			redirect('main');
		}

		$starting_row = intval($starting_row);
		
		$search = addslashes($this->input->post('search'));
		if ($search == false && $this->input->post('search_form')) {
			$this->session->unset_userdata($this->_class.'_search'); 
			$search = false;
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
		
		$data['list']['heading'] = array('GID', 'Name');
		$data['list']['class'] = array(
			'id'   => 'ID', 
			'name' => 'Text');
		$data['list']['link_col'] = "id";
		$data['list']['link_url'] = $this->_clspath.$this->_class."/edit/";
		
		$this->load->library('pagination');
		$config['base_url']    = site_url($this->_clspath.$this->_class.'/index');
		$config['uri_segment'] = 4;
		$config['total_rows']  = $this->auth->countGroups($search);
		$config['per_page']    = Settings::get('rows_per_page');
		$this->pagination->initialize($config);
	
		$data['list']['data'] = $this->auth->getGroups($search, $config['per_page'], $starting_row);
		
		$data['buttons'] = array(anchor($this->_clspath.$this->_class."/edit/0", '<i class="fa fa-plus"></i><span class="hidden-xs"> Add</span>', 'class="btn btn-success" id="AddNew"'));
		
		$data['page_title'] = humanize($this->_class);
		$data['page']       = 'list';
		$data['docs_url']   = $this->_docs;
		$this->load->view('index', $data);
	}

	function edit($id) {
		if (! Auth::isAdmin()) {
			setSessionError('You don&rsquo;t have enough permission');
			redirect('main');
		}

		$id = intval($id);
		
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('name', 'Name', 'trim|required|callback__is_unique');
	
		$row = $this->auth->getGroup($id);
		if ($row == FALSE) {
			$row = [
				'id'   => '0',
				'name' => '',
			];
		}
		
		$data['id']  = ['id' => $id];
		$data['row'] = $row;
		
		if ($this->form_validation->run() == false) {
			setSessionError(validation_errors());
			
			$data['list']['data'] = $this->auth->groupPermissions($id);
			
			if ($this->auth->isAdmin()) {
				$data['formselect']['title'] = "Available Content";
				$data['formselect']['url'] = $this->_clspath.$this->_class.'/addpermission';
				$data['formselect']['postvar'] = "availablePermissions[]";
				$data['formselect']['data'] = $this->auth->getAvailablePermissions($id);
			}
			
			$data['page_title'] = humanize($this->_class);
			$data['page'] = $this->_clspath.$this->_class.'_edit';
			$this->load->view('index', $data);
		}
		else {
			$this->auth->saveGroup($this->input->post('name'), $this->input->post('id'));
			setSessionAlert('Changes saved successfully', 'success');
			
			redirect($this->_clspath.$this->_class);
		}
	}
	
	function addpermission() {
		if (! Auth::isAdmin()) {
			setSessionError('You don&rsquo;t have enough permission');
			redirect('main');
		}

		$this->auth->addPermission($this->input->post('id'), $this->input->post('availablePermissions'));
		setSessionAlert('Changes saved successfully', 'success');

		redirect($this->_clspath.$this->_class.'/edit/'.$this->input->post('id'));
	}
	
	function permission() {
		if (! Auth::isAdmin()) {
			setSessionError('You don&rsquo;t have enough permission');
			redirect('main');
		}

		$this->auth->delPermission($this->input->post('delete_id'));
		$this->auth->updatePermission($this->input->post('id'), $this->input->post('perm'));
		setSessionAlert('Changes saved successfully', 'success');
		
		redirect($this->_clspath.$this->_class.'/edit/'.$this->input->post('id'));
	}
	
	function _is_unique($name) {
		$this->form_validation->set_message('_is_unique', 
			"The <b>" . $name . "</b> group name already exists.");
		return ! $this->auth->groupExists($name);
	}
}
