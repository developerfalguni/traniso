<?php

class Location extends MY_Controller {
	function __construct() {
		parent::__construct();

		$this->load->model('transport');
	}
		
	function index($starting_row = 0) {
			if (! Auth::hasAccess()) {
			setSessionError('NO_PERMISSION');
			redirect('main');
		}

		$starting_row = intval($starting_row);
		
		$search = addslashes($this->input->post('search'));
		if($search == false && $this->input->post('search_form')) {
			$search = '';
			$this->session->set_userdata($this->_class.'_search', $search);
			//$this->session->unset_userdata($this->_class.'_search');
			//redirect($this->_class);
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
		
		$data['list']['heading'] = array('ID', 'Name');
		$data['list']['class'] = array(
			'id'   => 'ID',
			'name' => 'Name');
		$data['list']['link_col'] = 'id';
		$data['list']['link_url'] = $this->_clspath.$this->_class."/edit/";
		$data['label_class'] = $this->transport->getLabelClass();

		$this->load->library('pagination');
		$config['base_url']    = site_url($this->_clspath.$this->_class."/index");
		$config['uri_segment'] = (strlen($this->_clspath) > 0 ? (3+substr_count($this->_clspath, '/')) : 3);
		$config['total_rows']  = $this->transport->countLocations($search);
		$config['per_page']    = Settings::get('rows_per_page');
		$this->pagination->initialize($config);
		
		$data['list']['data'] = $this->transport->getLocations($search, $starting_row, $config['per_page']);

		$data['buttons'] = array(anchor($this->_clspath.$this->_class."/edit/0", '<i class="icon-white icon-plus"></i> Add', 'class="btn btn-success"'));

		$data['page_title'] = humanize($this->_class);
		$data['hide_title'] = true;
		$data['page']       = 'list';
		$data['docs_url']   = $this->_docs;
		$this->load->view('index', $data);
	}

	function edit($id) {
		$this->load->library('form_validation');
		
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('name', 'Location Name', 'trim|required');

		$row = $this->kaabar->getRow($this->_table, $id);
		if($row == false) {
			$row = array(
				'id'       => 0,
				'name'     => '',
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
			checkDuplicateFormSubmit($this->_clspath.$this->_class);
			
			if (Auth::hasAccess($id > 0 ? Auth::UPDATE : Auth::CREATE)) {
				$data = array(
					'name'     => $this->input->post('name'),
				);
				$id = $this->kaabar->save($this->_table, $data, $row);
				setSessionAlert('SAVED', 'success');
			}
			else
				setSessionError('NO_PERMISSION');

			redirect($this->_clspath.$this->_class);
		}
	}
}
