<?php

class Vendor extends MY_Controller {
	function __construct() {
		parent::__construct();
		
		$this->load->model('office');
		$this->load->model('export');

		$this->_folder = 'documents/vendor/';
		$this->_share  = FCPATH . 'share/';
		$this->_path   = FCPATH . $this->_folder;
		$this->_share_url  = base_url('share');
		$this->_path_url   = base_url($this->_folder);
	}
		
	function index() {

		redirect($this->_clspath.$this->_class.'/edit/0');
	}

	function getVendor($vendor_id = 0){

		$response = [];
		
		if($vendor_id > 0){

			$row = $this->kaabar->getRow('vendors', $vendor_id);
			$row['state_name'] = $this->kaabar->getField('states', $row['state'], 'id', 'name');
			$row['country_name'] = $this->kaabar->getField('countries', $row['country'], 'id', 'name');
			$vendor_upload = $this->kaabar->getRows('attachments', ['parent_id' => $vendor_id, 'type' => 'VENDOR']);
			$uploads = [];
			foreach ($vendor_upload as $key => $value) {
				$file = $value['path'].$value['name'];	
				$docdir = $this->office->getDocFolder($this->_path, $vendor_id);
				if(file_exists($file) AND $value['name'] != null){
					$download = $this->_path_url.$docdir.$value['name'];
					$uploads[$key]['doc_name'] = $value['doc_name'];
					$uploads[$key]['doc_path'] = $value['path'].$value['name'];
					$uploads[$key]['filepath'] = $download;
					$uploads[$key]['filename'] = $value['name'];
					$uploads[$key]['id'] = $value['id'];
				}
			}

			$type = explode(',', $row['type']);
			if(!empty(array_filter($type)))
				$row['type'] = $type;
			else
				$row['type'] = null;

			$response['success'] = true;
			$response['row'] = $row;
			$response['files'] = $uploads;

		}
		else
		{
			$response['success'] = false;
			$response['messages'] = 'Plaese select Vendor';
		}

		header('Content-type: application/json; charset=utf-8');
		echo json_encode($response, JSON_UNESCAPED_UNICODE);
	}
	
	function edit($id = 0) {

		$data['id'] = array('id' => 0);
		$query = $this->db->get($this->_table);
		$data['count'] = $query->num_rows();
		$data['types'] = getEnumSetOptions($this->_table, 'type');
		$data['page_title'] = humanize($this->_class);
		$data['hide_title'] = true;
		$data['page']       = $this->_clspath.$this->_class.'_edit';
		$this->load->view('index', $data);		
	}

	function ajaxEdit() {

		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('name', 'Name', 'trim|required');
		$this->form_validation->set_rules('address1', 'Address', 'trim');
		$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
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
			
				if ($this->input->post('type'))
					$types = join(',', $this->input->post('type'));
				else
					$types = $this->input->post('type');

				$data = array(
					'name'            => $this->input->post('name'),
					'address1'        => $this->input->post('address1'),
					'address2'        => $this->input->post('address2'),
					'address3'        => $this->input->post('address3'),
					'city'         	   => $this->input->post('city'),
					'state'            => $this->input->post('state'),
					'pincode'          => $this->input->post('pincode'),
					'country'          => $this->input->post('country'),
					'contact_person'  => $this->input->post('contact_person'),
					'mobile_no'       => $this->input->post('mobile_no'),
					'email'           => $this->input->post('email'),
					'gst_no'          => $this->input->post('gst_no'),
					'pan_no'          => $this->input->post('pan_no'),
					'remarks'         => $this->input->post('remarks'),
					'type'            => $types,
					'business_started_date'            => $this->input->post('business_started_date')
				);
				
				$id = $this->kaabar->save($this->_table, $data, ['id' => $row_id]);
				$this->_do_upload($id);

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

	function delete($id = 0, $field = 'id') {
		$response = [];
		if ($this->input->is_ajax_request()) {
			if($id){
				
				///// Check in JOB table
				$this->db->group_start();
				$this->db->where(array('shipper_category' => 'VENDOR', 'shipper_id' => $id));
				$this->db->group_end();
				$this->db->or_group_start();
				$this->db->where(array('billing_party1_category' => 'VENDOR', 'billing_party1_id' => $id));
				$this->db->group_end();
				$this->db->or_where(array('cha_id' => $id, 'line_id' => $id, 'forwarder_id' => $id));
				$result = $this->db->get('jobs')->num_rows();

				//// Check in Costsheet Table
				$this->db->where(array('vendor_id' => $id));
				$result1 = $this->db->get('costsheets')->num_rows();
				
				if($result > 0)
				{
					$response['status'] = 'error';
					$response['msg'] = 'You can not delete vendor is used in JOBS';			
				}
				elseif($result1 > 0)
				{
					$response['status'] = 'error';
					$response['msg'] = 'You can not delete vendor is used in COSTSHEET';			
				}
				else
				{
					$this->db->delete($this->_table, ['id' => $id]);

					$attachments = $this->kaabar->getRows('attachments', ['parent_id' => $id, 'type' => 'VENDOR']);
					$unlink = '';

					if(count($attachments) > 0){
						foreach ($attachments as $key => $value) {
							$unlink = $value['path'].$value['name'];
							if($this->db->delete('attachments', ['id' => $value['id']]))
								if($unlink) unlink($unlink);
						}	
					}	
						
					$response['status'] = 'success';
					$response['msg'] = 'Successfully Deleted Vendor';			
				}
				
			}
			else
			{
				$response['status'] = 'error';
				$response['msg'] = 'Please Select Vendor First then delete';		
			}
			header('Content-type: application/json; charset=utf-8');
			echo json_encode($response, JSON_UNESCAPED_UNICODE);
		}
		else
			echo "Access Denied";
	}

	public function _do_upload($id)
	{       
	    $this->load->library('upload');
	    $dataInfo = array();
	    $files = $_FILES;

	    $names = $this->input->post('userfilename');

	    foreach ($names as $key => $value) {

	    	$attach = $this->kaabar->getRow('attachments', $key);

	    	if(isset($attach['name']) AND empty($_FILES['userfile']['name'][$key])){
	    		$data = array(
			        'doc_name' => $value,
			        'updated_by' => Auth::getCurrUID(),
			    );
			    $this->kaabar->save('attachments', $data, ['id' => $key]);
	    	}
	    	else
	    	{
	    		$_FILES['userfile']['name']= $files['userfile']['name'][$key];
		        $_FILES['userfile']['type']= $files['userfile']['type'][$key];
		        $_FILES['userfile']['tmp_name']= $files['userfile']['tmp_name'][$key];
		        $_FILES['userfile']['error']= $files['userfile']['error'][$key];
		        $_FILES['userfile']['size']= $files['userfile']['size'][$key];    

		        $this->upload->initialize($this->set_upload_options($id));

		        if($this->upload->do_upload('userfile')){

		        	$dataInfo[$key]['info'] = $this->upload->data();
		        	$dataInfo[$key]['status'] = true;

		        	$data = array(
				        'parent_id' => $id,
				        'doc_name' => $value,
				        'name' => $dataInfo[$key]['info']['file_name'],
				        'path' => $dataInfo[$key]['info']['file_path'],
				        'size' => $dataInfo[$key]['info']['file_size'],
				        'mime' => $dataInfo[$key]['info']['file_type'],
				        'created_by' => Auth::getCurrUID(),
				        'type' => 'VENDOR',
				    );
				    $this->kaabar->save('attachments', $data);
		        }
				else
		        {
		        	$dataInfo[$key]['info'] = $this->upload->display_errors();
		        	$dataInfo[$key]['status'] = false;
		        }

	    	}
	    }
	    return $dataInfo;
	}

	private function set_upload_options($id)
	{   
	    //upload an image options
	    $docdir = $this->office->getDocFolder($this->_path, $id);
	    $config = array();
	    $config['upload_path'] = $this->_path.$docdir; 
		$config['allowed_types'] = 'jpg|jpeg|png|gif|zip|pdf'; 
	    $config['max_size']      = '0';
	    $config['overwrite']     = FALSE;

	    return $config;
	}

	// function _ajaxEdit($id, $category = null) {

	// 	$response = [];
	// 	$this->load->library('form_validation');
	// 	$this->form_validation->set_rules('name', humanize($category).' Name', 'trim|required');
	// 	$this->form_validation->set_error_delimiters('<p class="text-danger">','</p>');
		
	// 	if ($this->form_validation->run() == false) {
	// 		//setSessionError(validation_errors());
	// 		$response['success'] = false;
 //        	foreach ($_POST as $key => $value) {
 //        		$response['messages'][$key] = form_error($key);
 //        	}
 //        }
	// 	else
	// 	{

	// 		$name = $this->input->post('name');
	// 		if($name != null){
	// 			$data = array(
	// 				'type'	=> $category,
	//         		'name'	=> $name,
	//         	);

	// 			$id = $this->kaabar->save($this->_table, $data);

	// 			$response['id'] = $id;
	// 			$response['name'] = $name;
	// 	        $response['success'] = true;
	//         	$response['messages'] = 'Succesfully Saved';	
	// 		}
	// 		else
	// 		{
	// 			$response['success'] = false;
	// 			$response['messages'] = 'Somathing Went Wrong';	
	// 		}

	// 	}

	// 	header('Content-type: application/json; charset=utf-8');
	// 	echo json_encode($response, JSON_UNESCAPED_UNICODE);
	// }

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

	function vendorList() {
		$json['data'] = [];
		if ($this->input->is_ajax_request()) {

			if(!empty($this->input->get("searchTerm"))){
				$this->db->like('name', $this->input->get("searchTerm"));
			}
			
			$query = $this->db->select('id, name as text')
							->limit(10)
							->get('vendors');
			$json = $query->result_array();
			
			echo json_encode($json);
		}
		else
			echo "Access Denied";
   	}

   	function delDetails($table, $custID, $id) {
    	$unlink = '';
		$json = [];
		if ($this->input->is_ajax_request()) {

			if($custID AND $id){

				if($table == 'attachments'){
					$docdir = $this->office->getDocFolder($this->_path, $custID);
					$file = $this->kaabar->getField($table, $id, 'id', 'name');
					$unlink = $this->_path.$docdir.$file;
				}
				if($this->db->delete($table, ['id' => $id])){
					if($unlink) unlink($unlink);
					$json = ['success' => true, 'messages' => 'Successfully Deleted'];
				}
				else
					$json = ['success' => false, 'messages' => 'Something Wrong on Database, Try Again'];	
			}
			else
				$json = ['success' => false, 'messages' => 'Something Wrong, Try Again'];

			echo json_encode($json);
		}
		else
			echo "Access Denied";
    }
}

