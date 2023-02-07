<?php

class Port extends MY_Controller {
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
		
		$data['list']['heading'] = array('ID', 'Code', 'Unece Code', 'Name', 'Country');
		$data['list']['class'] = array(
			'id' => 'ID',
			'code' => 'Code',
			'unece_code' => 'Code',
			'name' => 'Text',
			'country_name' => 'Text');
		$data['list']['link_col'] = "id";
		$data['list']['link_url'] = $this->_clspath.$this->_class."/edit/";
		
		$this->load->library('pagination');
		$config['base_url'] = site_url($this->_clspath.$this->_class.'/index');
		$config['uri_segment'] = (strlen($this->_clspath) > 0 ? (3+substr_count($this->_clspath, '/')) : 3);
		$config['total_rows'] = $this->office->countPorts($search);
		$config['per_page'] = Settings::get('rows_per_page');
		$this->pagination->initialize($config);
		
		$data['list']['data'] = $this->office->getPorts($search, $starting_row, $config['per_page']);
		
		$data['buttons'] = array(anchor($this->_clspath.$this->_class."/edit/0", '<i class="fa fa-plus"></i> <u>A</u>dd', 'class="btn btn-success" id="AddNew"'));

		$data['page_title'] = humanize($this->_class);
		$data['page'] = 'list';

		$data['docs_url'] = $this->_docs;
		$this->load->view('index', $data);
	}

	function edit($id) {
		$this->load->library('form_validation');
		
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('country_id', 'Country', 'trim');
		$this->form_validation->set_rules('code', 'Ports Code', 'trim|required');
		$this->form_validation->set_rules('unece_code', 'UNECE Code', 'trim');
		$this->form_validation->set_rules('name', 'Ports Name', 'trim|required');

		$row = $this->kaabar->getRow($this->_table, $id);
		if($row == false) {
			$row = array(
				'id' => 0,
				'country_id' => '',
				'code' => '',
				'unece_code' => '',
				'name' => ''
			);
		}
		
		$data['id'] = array('id' => $id);
		$data['row'] = $row;

		if ($this->form_validation->run() == false) {
			setSessionError(validation_errors());
			$data['countries'] = getSelectOptions('countries');
			$data['page_title'] = humanize($this->_class);
			$data['hide_title'] = true;
			$data['page'] = $this->_clspath.$this->_class.'_edit';
	
			$data['docs_url'] = $this->_docs;
			$this->load->view('index', $data);
		}
		else {
			checkDuplicateFormSubmit($this->_clspath.$this->_class."/edit/$id");
			
			if (Auth::hasAccess($id > 0 ? Auth::UPDATE : Auth::CREATE)) {
				$data = array(
					'country_id' => $this->input->post('country_id'),
					'code' => $this->input->post('code'),
					'unece_code' => $this->input->post('unece_code'),
					'name' => $this->input->post('name')
				);
				if($id == 0) {
					$id = $this->kaabar->save($this->_table, $data);
				}
				else {
					$id = $this->kaabar->save($this->_table, $data, $row);
				}
				setSessionAlert('SAVED', 'success');
			}
			else
				setSessionError('NO_PERMISSION');

			redirect($this->_clspath.$this->_class."/edit/$id");
		}
	}
	
	function ajaxEdit($id, $category = null) {

		$response = [];
		$this->load->library('form_validation');
		//$this->form_validation->set_rules('country_id', 'Country', 'trim');
		$this->form_validation->set_rules('code', 'Ports Code', 'trim|required');
		$this->form_validation->set_rules('unece_code', 'UNECE Code', 'trim');
		$this->form_validation->set_rules('name', 'Ports Name', 'trim|required');
		$this->form_validation->set_error_delimiters('<p class="text-danger">','</p>');
		
		if ($this->form_validation->run() == false) {
			//setSessionError(validation_errors());
			$response['success'] = false;
        	foreach ($_POST as $key => $value) {
        		$response['messages'][$key] = form_error($key);
        	}
        }
		else
		{
			$country_id = $this->input->post('country_id');
			$code = $this->input->post('code');
			$unece_code = $this->input->post('unece_code');
			$name = $this->input->post('name');
			if($name != null){

				$data = array(
	        		'country_id'	=> $country_id,
	        		'code'			=> $code,
	        		'unece_code'	=> $unece_code,
	        		'name'			=> $name,
	        	);

				$id = $this->kaabar->save($this->_table, $data);
				if($category == 'Indian'){
					unset($data['country_id']);
					$id = $this->kaabar->save('indian_ports', $data);
				}

				$response['id'] = $id;
				$response['name'] = $name;
		        $response['success'] = true;
	        	$response['messages'] = 'Succesfully Saved';	
			}
			else
			{
				$response['success'] = false;
				$response['messages'] = 'Somathing Went Wrong';	
			}

		}

		header('Content-type: application/json; charset=utf-8');
		echo json_encode($response, JSON_UNESCAPED_UNICODE);
	}

	function ajax($country_code = null) {
		if ($this->input->is_ajax_request()) {
			$search = strtolower($this->input->get('term'));
			$sql = "SELECT P.id, P.unece_code, P.name, P.country_id, C.name AS country
				FROM ports P INNER JOIN countries C ON P.country_id = C.id
				WHERE " . ($country_code != null ? " C.code = '$country_code' AND " : '') .
					" (P.name LIKE '%$search%' OR P.unece_code LIKE '%$search%')
				ORDER BY P.name
				LIMIT 0, 50";
			$this->kaabar->getJson($sql);
		}
		else {
			echo "Access Denied";
		}
	}
}
