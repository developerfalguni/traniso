<?php

class Bank extends MY_Controller {
	function __construct() {
		parent::__construct();
	}
		
	function index($starting_row = 0) {
		// if (! Auth::hasAccess()) {
		// 	setSessionError('NO_PERMISSION');
		// 	redirect('main');
		// }
		// $starting_row = intval($starting_row);
		
		// $this->_fields = array('id', 'name');
		// $this->_search = array('name');
		
		// $this->_data['list'] = array(
		// 	'heading' => array('ID', 'Name'),
		// 	'class' => array(
		// 		'id' => 'ID', 
		// 		'name' => 'Text'),
		// 	'link_col' => "id",
		// 	'link_url' => $this->_clspath.$this->_class."/edit/");
		
		// $this->_index($starting_row);
		redirect($this->_clspath.$this->_class.'/edit/0');
	}

	function getBank($bank_id = 0){

		$response = [];
		
		if($bank_id > 0){

			$row = $this->kaabar->getRow('banks', $bank_id);
			
			$response['success'] = true;
			$response['row'] = $row;
		}
		else
		{
			$response['success'] = false;
			$response['messages'] = 'Plaese select Bank';
		}

		header('Content-type: application/json; charset=utf-8');
		echo json_encode($response, JSON_UNESCAPED_UNICODE);
	}

	function edit_old($id) {
		$this->load->library('form_validation');
		
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('name', 'Bank Name', 'trim|required');

		$row = $this->kaabar->getRow($this->_table, $id);
		if($row == false) {
			$row = array(
				'id' => 0,
				'name' => ''
			);
		}
		
		$data['id'] = array('id' => $id);
		$data['row'] = $row;

		if ($this->form_validation->run() == false) {
			setSessionError(validation_errors());
			
			$data['page_title'] = humanize($this->_class);
			$data['hide_title'] = truer;
			$data['page'] = 'simple_fom'; //$this->_clspath.$this->_class.'_edit';
			$data['docs_url'] = $this->_docs;
			$this->load->view('index', $data);
		}
		else {
			checkDuplicateFormSubmit($this->_clspath.$this->_class."/edit/$id");
			
			if (Auth::hasAccess($id > 0 ? Auth::UPDATE : Auth::CREATE)) {
				$data = array(
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

	function edit($id = 0) {
		
		$data['id'] = array('id' => 0);	
		$query = $this->db->get($this->_table);
		$data['count'] = $query->num_rows();
		$data['page_title'] = humanize($this->_class);
		$data['hide_title'] = true;
		$data['page'] = $this->_clspath.$this->_class.'_edit';
		$this->load->view('index', $data);
	}

	function ajaxEdit() {
		$this->load->library('form_validation');
		$this->form_validation->set_rules('name', 'Bank Name', 'trim|required');

		$this->form_validation->set_error_delimiters('<p class="text-danger">','</p>');
		if ($this->form_validation->run() == false) {
			
			$response['success'] = false;
        	foreach ($_POST as $key => $value) {
        		$response['messages'][$key] = form_error($key);
        	}
				
		}
		else {
			
			$row_id = $this->input->post('id');

			if (Auth::hasAccess($row_id > 0 ? Auth::UPDATE : Auth::CREATE)) {
			
				$data = array(
					'name'   => $this->input->post('name')
					);

				
				$id = $this->kaabar->save($this->_table, $data, ['id' => $row_id]);
				
				if($id){
					$response['success'] = true;
		        	$response['messages'] = 'Succesfully Saved';	
				}
				else
				{
					$response['success'] = false;
					$response['messages'] = 'Somathing Went Wrong';	
				}
			}
		}
		header('Content-type: application/json; charset=utf-8');
		echo json_encode($response, JSON_UNESCAPED_UNICODE);
	}

	function bankList() {
		$json = [];
		if ($this->input->is_ajax_request()) {

			if(!empty($this->input->get("q"))){
				$this->db->like('name', $this->input->get("q"));
			}
			
			$query = $this->db->select('id, name as text')
							->limit(10)
							->get('banks');
							
			$json = $query->result_array();
			
			// $json[] = ['id' => 0, 'text' => 'New Bank'];

			echo json_encode($json);
		}
		else
			echo "Access Denied";
   }
}
