<?php

class Agent extends MY_Controller {
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
		
		$data['list'] = array(
			'heading' => array('ID', 'Type', 'Code', 'Name', 'Address', 'Person', 'Contact', 'PAN No', 'CHA No', 'Ledger A/c'),
			'class' => array(
				'id'          => 'ID',
				'type'        => 'Code',
				'code'        => 'Code bold',
				'name'        => 'Text bold',
				'address'     => 'Text',
				'person'      => 'Text',
				'contact'     => 'Text',
				'pan_no'      => 'Text',
				'cha_no'      => 'Text',
				'ledger_name' => 'Text'),
			'link_col' => "id",
			'link_url' => $this->_clspath.$this->_class."/edit/");
		$data['label_class'] = $this->office->getLabelClass();
		
		$this->load->library('pagination');
		$config['base_url'] = site_url($this->_clspath.$this->_class.'/index');
		$config['uri_segment'] = (strlen($this->_clspath) > 0 ? (3+substr_count($this->_clspath, '/')) : 3);
		$config['total_rows'] = $this->office->countAgents($search);
		$config['per_page'] = Settings::get('rows_per_page');
		$this->pagination->initialize($config);
		
		$data['list']['data'] = $this->office->getAgents($search, $starting_row, $config['per_page']);
		
		$data['buttons'] = array(
			anchor($this->_clspath.$this->_class."/edit/0", '<i class="fa fa-plus"></i> <u>A</u>dd', 'class="btn btn-success btn-sm" id="AddNew"'),
			anchor("/tracking/traces/captcha/agents", 'PAN Login', 'class="btn btn-info  btn-sm Popup"'),
			anchor("/tracking/traces/track/agents", 'Fetch PAN', 'class="btn btn-info  btn-sm Popup"'),
			anchor($this->_clspath.$this->_class."/excel", 'Excel', 'class="btn  btn-sm btn-warning"')
		);

		$data['page_title'] = humanize($this->_class);
		$data['page'] = 'list';

		$data['docs_url'] = $this->_docs;
		$this->load->view('index', $data);
	}
	
	function edit($id) {
		$this->load->library('form_validation');
		
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('name', 'Agent Name', 'trim|required');
		$this->form_validation->set_rules('address', 'Address', 'trim');
		$this->form_validation->set_rules('contact', 'Contact', 'trim');
		$this->form_validation->set_rules('email', 'Email', 'trim');
		$this->form_validation->set_rules('remarks', 'Remarks', 'trim');
		
		$row = $this->kaabar->getRow($this->_table, $id);
		if($row == false) {
			$row = array(
				'id'              => 0,
				'type'            => '',
				'code'            => '',
				'name'            => '', 
				'address'         => '',
				'city_id'         => 0,
				'person'          => '',
				'contact'         => '',
				'fax'             => '',
				'traces_name'	  => '',
				'email'           => '',
				'gst_no'          => '',
				'gst_no_verified' => 'No',
				'pan_no'          => '',
				'pan_no_verified' => 'No',
				'tan_no'          => '',
				'tan_no_verified' => 'No',
				'cha_no'          => '',
				'service_tax_no'  => '',
				'tin_no'          => '',
				'cst_no'          => '',
				'remarks'         => ''
			);
		}
		
		$data['id'] = array('id' => $id);
		$data['row'] = $row;

		if ($this->form_validation->run() == false) {
			setSessionError(validation_errors());
	
			$data['tariffs'] = $this->office->getAgentRate($id);
			
			$data['page_title'] = humanize($this->_class);
			$data['hide_title'] = true;
			$data['page']       = $this->_clspath.$this->_class.'_edit';
			$data['docs_url']   = $this->_docs;
			$this->load->view('index', $data);
		}
		else {
			checkDuplicateFormSubmit($this->_clspath.$this->_class."/edit/$id");
			
			if (Auth::hasAccess($id > 0 ? Auth::UPDATE : Auth::CREATE)) {
				if ($this->input->post('type'))
					$type = join(',', $this->input->post('type'));
				else
					$type = $this->input->post('type');

				$data = array(
					'type'            => $type,
					'code'            => $this->input->post('code'),
					'name'            => $this->input->post('name'),
					'address'         => $this->input->post('address'),
					'city_id'         => $this->input->post('city_id'),
					'person'          => $this->input->post('person'),
					'contact'         => $this->input->post('contact'),
					'fax'             => $this->input->post('fax'),
					'email'           => $this->input->post('email'),
					'pan_no'          => $this->input->post('pan_no'),
					'pan_no_verified' => ($this->input->post('pan_no_verified') ? 'Yes' : 'No'),
					'tan_no'          => $this->input->post('tan_no'),
					'tan_no_verified' => ($this->input->post('tan_no_verified') ? 'Yes' : 'No'),
					'cha_no'          => $this->input->post('cha_no'),
					'service_tax_no'  => $this->input->post('service_tax_no'),
					'tin_no'          => $this->input->post('tin_no'),
					'cst_no'          => $this->input->post('cst_no'),
					'remarks'         => $this->input->post('remarks')
				);
				$id = $this->kaabar->save($this->_table, $data, $row);
				
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
		$this->form_validation->set_rules('name', humanize($category).' Name', 'trim|required');
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

			$name = $this->input->post('name');
			if($name != null){
				$data = array(
					'type'	=> $category,
	        		'name'	=> $name,
	        	);

				$id = $this->kaabar->save($this->_table, $data);

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

	function ajax($type = false) {
		if ($this->input->is_ajax_request()) {
			$search = strtolower($this->input->get('term'));

			$sql = "SELECT id, name FROM " . $this->_table . "
			WHERE " . ($type ? "FIND_IN_SET('$type', type) AND" : NULL) . " name LIKE '%$search%' 
			ORDER BY name
			LIMIT 0, 50";
			$this->kaabar->getJson($sql);
		}
		else {
			echo "Access Denied";
		}
	}

	function ajaxLine() {
		if ($this->input->is_ajax_request()) {
			$search = strtolower($this->input->get('term'));

			$sql = "SELECT id, code, name FROM $this->_table
			WHERE (FIND_IN_SET('Line', type) OR FIND_IN_SET('Agents', type) OR FIND_IN_SET('NVOCC', type)) AND 
				(code LIKE '%$search%' OR name LIKE '%$search%')
			ORDER BY name
			LIMIT 0, 50";
			$this->kaabar->getJson($sql);
		}
		else {
			echo "Access Denied";
		}
	}

	function excel() {
		$query = $this->db->query("SELECT * FROM " . $this->_table . " ORDER BY name");

		$this->load->helper('excel');
		to_excel($query, $this->_class . '_' . date('d-m-Y'));
	}
}
