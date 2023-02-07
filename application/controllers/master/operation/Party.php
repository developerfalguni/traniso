<?php

class Party extends MY_Controller {
	function __construct() {
		parent::__construct();
		
		$this->load->model('office');
		$this->load->model('export');

		$this->_folder = 'documents/customers/';
		$this->_share  = FCPATH . 'share/';
		$this->_path   = FCPATH . $this->_folder;
		$this->_share_url  = base_url('share');
		$this->_path_url   = base_url($this->_folder);
	}
	
	function index($starting_row = 0) {
		redirect($this->_clspath.$this->_class.'/edit/0');
	}

	function getParty($party_id = 0){

		$response = [];
		
		if($party_id > 0){

			$party = $this->kaabar->getRow('parties', $party_id);


			$party['state_name'] = $this->kaabar->getField('states', $party['state'], 'id', 'name');
			$party['country_name'] = $this->kaabar->getField('countries', $party['country'], 'id', 'name');

			$parties_add = $this->kaabar->getRows('party_addresses', ['party_id' => $party_id]);
			$parties_con = $this->kaabar->getRows('party_contacts', ['party_id' => $party_id]);
			$parties_upload = $this->kaabar->getRows('attachments', ['parent_id' => $party_id, 'type' => 'CUSTOMER']);
			$uploads = [];
			foreach ($parties_upload as $key => $value) {
				$file = $value['path'].$value['name'];	
				$docdir = $this->office->getDocFolder($this->_path, $party_id);
				if(file_exists($file) AND $value['name'] != null){
					$download = $this->_path_url.$docdir.$value['name'];
					$uploads[$key]['doc_name'] = $value['doc_name'];
					$uploads[$key]['doc_path'] = $value['path'].$value['name'];
					$uploads[$key]['filepath'] = $download;
					$uploads[$key]['filename'] = $value['name'];
					$uploads[$key]['id'] = $value['id'];
				}
			}
				
			$response['success'] = true;
			$response['party'] = $party;
			$response['files'] = $uploads;
			$response['parties_add'] = $parties_add;
			$response['parties_con'] = $parties_con;
		}
		else
		{
			$response['success'] = false;
			$response['messages'] = 'Plaese select Party';
		}

		header('Content-type: application/json; charset=utf-8');
		echo json_encode($response, JSON_UNESCAPED_UNICODE);
	}
	
	function edit($id = 0) {

			$data['id'] = array('id' => 0);

			$data['kyc_documents']  = $this->office->getAttachedKycs($id, 1, 0);
			$data['ledger_id'] 		= $this->kaabar->getField('ledgers', $id, 'party_id', 'id');
			$data['iec_details']    = $this->kaabar->getRow('dgft_iecs', $id, 'party_id');
			$data['file']       	= $this->kaabar->getRows('parties', ['id' => $id]);
			$data['contacts']       = $this->kaabar->getRows('party_contacts', ['party_id' => $id, 'party_site_id' => 0]);
			$data['addresses']      = $this->kaabar->getRows('party_addresses', ['party_id' => $id, 'party_site_id' => 0]);
			$data['bill_templates'] = $this->office->getPartyBillTemplates($id);
			$query 					= $this->db->get($this->_table);
			$data['count'] 			= $query->num_rows();
			$data['page_title'] 	= 'Customer';
			$data['page']       	= $this->_clspath.$this->_class.'_edit';
			$data['docs_url']   	= $this->_docs;
			$this->load->view('index', $data);
		
	}

	function ajaxEdit() {

		$response = [];
		$this->load->library('form_validation');
		$this->form_validation->set_rules('name', 'Party Name', 'trim|required');
		$this->form_validation->set_rules('main_address', 'Address', 'trim');
		$this->form_validation->set_rules('contact', 'Contact', 'trim');
		$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
		$this->form_validation->set_rules('remarks', 'Remarks', 'trim');
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

			$row_id = $this->input->post('id');

			if (Auth::hasAccess($row_id > 0 ? Auth::UPDATE : Auth::CREATE)) {

				if(isset($_FILES['new_upload']['name']))
					$file = $_FILES['new_upload']['name'];
				

				$data = array(
					'name'            => $this->input->post('name'),
					'address'         => $this->input->post('main_address'),
					'city_id'         => $this->input->post('city_id'),
					'state'            => $this->input->post('state_id'),
					'pincode'          => $this->input->post('pincode'),
					'country'          => $this->input->post('country'),
					'contact'         => $this->input->post('contact'),
					'email'           => $this->input->post('email'),
					'pan_no'          => $this->input->post('pan_no'),
					'pan_no_verified' => ($this->input->post('pan_no_verified') ? 'Yes' : 'No'),
					'tan_no'          => $this->input->post('tan_no'),
					'tan_no_verified' => ($this->input->post('tan_no_verified') ? 'Yes' : 'No'),
					'iec_no'          => $this->input->post('iec_no'),
					'tin_no'          => $this->input->post('tin_no'),
					'customer_type'   => $this->input->post('customer_type'),
					'commodity'   	  => $this->input->post('commodity'),
					'excise_no'       => $this->input->post('excise_no'),
					'gst_nos'         => $this->input->post('gst_nos'),
					'tds_class_id'    => $this->input->post('tds_class_id'),
					'username'        => $this->input->post('username'),
					'password'        => $this->input->post('password'),
					'active'          => $this->input->post('active'),
					'remarks'         => $this->input->post('remarks'),
					'business_started_date' => $this->input->post('business_started_date'),
					//'file'			  => $file ? $file : NULL
				);
				if ($this->input->post('password')) {
					$data['password'] = Auth::_addSalt($this->input->post('password'));
				}


				
				$id = $this->kaabar->save($this->_table, $data, ['id' => $row_id]);
				
				$this->_do_upload($id);

				$this->_updateContacts($id, 0);
				$this->_updateAddress($id, 0);
				
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
	    		$_FILES['userfile']['name'] = $files['userfile']['name'][$key];
		        $_FILES['userfile']['type'] = $files['userfile']['type'][$key];
		        $_FILES['userfile']['tmp_name']= $files['userfile']['tmp_name'][$key];
		        $_FILES['userfile']['error'] = $files['userfile']['error'][$key];
		        $_FILES['userfile']['size'] = $files['userfile']['size'][$key];    

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
				        'type' => 'CUSTOMER',
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
	    $config['upload_path'] 	 = $this->_path.$docdir; 
		$config['allowed_types'] = 'jpg|jpeg|png|gif|zip|pdf'; 
	    $config['max_size']      = '0';
	    $config['overwrite']     = FALSE;

	    return $config;
	}

	function _updateContacts($party_id, $id) {
		$delete_ids = $this->input->post('delete_id') == false? ['0' => 0] : $this->input->post('delete_id');
		$designations = $this->input->post('designation');
		if ($designations != null) {
			$person_names = $this->input->post('person_name');
			$mobiles      = $this->input->post('mobile');
			$email        = $this->input->post('con_email');

			foreach ($designations as $index => $designation) {
				
					$data = array(
						'designation' => $designation,
						'person_name' => $person_names[$index],
						'mobile'      => $mobiles[$index],
						'email'       => $email[$index],
					);
					$this->kaabar->save('party_contacts', $data, ['id' => $index]);
				}
			
		}

		if ($delete_ids != null) {
			foreach ($delete_ids as $index => $tmp) {
				$this->kaabar->delete('party_contacts', ['id' => $index]);
			}
		}

		$new_designations = $this->input->post('new_designation');
		if($new_designations != null) {
			$person_names = $this->input->post('new_person_name');
			$mobiles      = $this->input->post('new_mobile');
			$email        = $this->input->post('new_con_email');

			foreach ($new_designations as $index => $designation) {
				if (strlen(trim($mobiles[$index])) > 0 OR strlen(trim($email[$index])) > 0) {
					$data = array(
						'party_id'      => $party_id,
						'party_site_id' => $id,
						'designation'   => $designation,
						'person_name'   => $person_names[$index],
						'mobile'        => $mobiles[$index],
						'email'         => $email[$index],
					);
					$this->kaabar->save('party_contacts', $data);
				}
			}
		}
	}

	function _updateAddress($party_id, $id) {
		$delete_ids = $this->input->post('delete_id') == false? ['0' => 0] : $this->input->post('delete_id');
		$branch_codes = $this->input->post('branch_code');
		if ($branch_codes != null) {
			$address1 		= $this->input->post('address1');
			$address2       = $this->input->post('address2');
			$gst_no         = $this->input->post('gst_no');
			$state          = $this->input->post('state');
 
			foreach ($branch_codes as $index => $branch_code) {
				if (strlen(trim($gst_no[$index])) > 0 OR strlen(trim($state[$index])) > 0) {
					$data = array(
						'branch_code' 	=> $branch_code,
						'address1' 		=> $address1[$index],
						'address2'      => $address2[$index],
						'gst_no'       	=> $gst_no[$index],
						'state'       	=> $state[$index],
					);
					$this->kaabar->save('party_addresses', $data, ['id' => $index]);
				}
			}
		}

		if ($delete_ids != null) {
			foreach ($delete_ids as $index => $tmp) {
				$this->kaabar->delete('party_addresses', ['id' => $index]);
			}
		}

		$new_branch_codes = $this->input->post('new_branch_code');
		if($new_branch_codes != null) {
			$address1 	   = $this->input->post('new_address1');
			$address2      = $this->input->post('new_address2');
			$gst_no        = $this->input->post('new_gst_no');
			$state         = $this->input->post('new_state');

			foreach ($new_branch_codes as $index => $branch_code) {
				if (strlen(trim($gst_no[$index])) > 0 OR strlen(trim($state[$index])) > 0) {
					$data = array(
						'party_id'      => $party_id,
						'party_site_id' => $id,
						'branch_code' 	=> $branch_code,
						'address1' 		=> $address1[$index],
						'address2'      => $address2[$index],
						'gst_no'       	=> $gst_no[$index],
						'state'       	=> $state[$index],

					);
					$this->kaabar->save('party_addresses', $data);
				}
			}
		}
	}

	function ajaxSite($party_id) {
		if ($this->input->is_ajax_request()) {
			$search = strtolower($this->input->post_get('term'));

			$sql = "SELECT id, name, address FROM party_sites WHERE party_id = $party_id AND name LIKE '%$search%' ORDER BY name";
			$rows = $this->db->query($sql)->result_array();
			header('Content-type: application/json; charset=utf-8');
			echo json_encode($rows, JSON_UNESCAPED_UNICODE);
		}
		else
			echo "Access Denied";
	}

	function ajaxEmail($party_id = 0, $party_name = null) {
		if ($this->input->is_ajax_request()) {
			$search = strtolower($this->input->post_get('term'));

			if ($party_id > 0) {
				$sql = "SELECT email, name FROM parties 
				WHERE id = $party_id AND (name LIKE '%$search%' OR email LIKE '%$search%')
				UNION
				SELECT DISTINCT email, person_name FROM party_contacts 
				WHERE party_id = $party_id AND (person_name LIKE '%$search%' OR email LIKE '%$search%')
				UNION
				SELECT DISTINCT email, fullname FROM users 
				WHERE (fullname LIKE '%$search%' OR email LIKE '%$search%')
				ORDER BY email";
				$rows = $this->db->query($sql)->result_array();
			header('Content-type: application/json; charset=utf-8');
			echo json_encode($rows, JSON_UNESCAPED_UNICODE);
			}
			else {
				$sql = "SELECT P.email, P.name FROM parties P
				WHERE P.name LIKE '%" . urldecode($party_name) . "%' AND LENGTH(P.email) > 0
				UNION
				SELECT DISTINCT PC.email, PC.person_name 
				FROM party_contacts PC INNER JOIN parties P ON PC.party_id = P.id
				WHERE P.name LIKE '%" . urldecode($party_name) . "%' AND LENGTH(PC.email) > 0
				UNION
				SELECT DISTINCT email, fullname FROM users 
				WHERE (fullname LIKE '%$search%' OR email LIKE '%$search%')
				ORDER BY email";
				$rows = $this->db->query($sql)->result_array();
			header('Content-type: application/json; charset=utf-8');
			echo json_encode($rows, JSON_UNESCAPED_UNICODE);
			}
		}
		else
			echo "Access Denied";
	}

	function delete($id = 0, $field = 'id') {
		$response = [];
		if ($this->input->is_ajax_request()) {
			if($id){
				$this->db->group_start();
				$this->db->where(array('billing_party_category' => 'PARTY', 'billing_party_id' => $id));
				$this->db->group_end();
				$this->db->or_group_start();
				$this->db->where(array('shipper_category' => 'PARTY', 'shipper_id' => $id));
				$this->db->group_end();
				$this->db->or_group_start();
				$this->db->where(array('billing_party1_category' => 'PARTY', 'billing_party1_id' => $id));
				$this->db->group_end();
				$result = $this->db->get('jobs')->num_rows();
				
				$this->db->where(array('ledger_id' => $id));
				$result1 = $this->db->get('invoices')->num_rows();

				if($result > 0)
				{
					$response['status'] = 'error';
					$response['msg'] = 'You can not delete party is used in JOBS';			
				}
				elseif($result1 > 0)
				{
					$response['status'] = 'error';
					$response['msg'] = 'You can not delete party is used in Invoice';			
				}
				else
				{
					$this->db->delete($this->_table, ['id' => $id]);

					$this->db->delete('party_addresses', ['party_id' => $id]);
					$this->db->delete('party_contacts', ['party_id' => $id]);


					$attachments = $this->kaabar->getRows('attachments', ['parent_id' => $id, 'type' => 'CUSTOMER']);
					$unlink = '';

					if(count($attachments) > 0){
						foreach ($attachments as $key => $value) {
							$unlink = $value['path'].$value['name'];
							if($this->db->delete('attachments', ['id' => $value['id']]))
								if($unlink) unlink($unlink);
						}	
					}	
						
					$response['status'] = 'success';
					$response['msg'] = 'Successfully Deleted Party';			
				}
			}
			else
			{
				$response['status'] = 'error';
				$response['msg'] = 'Please Select Party First then delete';		
			}
			header('Content-type: application/json; charset=utf-8');
			echo json_encode($response, JSON_UNESCAPED_UNICODE);
		}
		else
			echo "Access Denied";
	}

	function ajax1($table = FALSE, $field = 'name') {
		if ($this->input->is_ajax_request()) {
			$search = strtolower($this->input->post_get('term'));

			$sql = "SELECT id, name, category
			FROM (
			  SELECT id, name, 'PARTY' as category FROM $this->_table WHERE name LIKE '%$search%' 
			  UNION
			  SELECT id, company_name as name, 'AGENT' as category FROM new_agents WHERE company_name LIKE '%$search%' OR person_name LIKE '%$search%'   
			) as sub
			GROUP BY name
			ORDER BY name DESC
			LIMIT 0, 50";

			$rows = $this->db->query($sql)->result_array();

			header('Content-type: application/json; charset=utf-8');
			echo json_encode($rows, JSON_UNESCAPED_UNICODE);
		}
		else
			echo "Access Denied";
	}

	function ajax2($table = FALSE, $field = 'name') {
		if ($this->input->is_ajax_request()) {
			$search = strtolower($this->input->post_get('term'));

			$sql = "SELECT id, name, category
			FROM (
			  SELECT id, name, 'PARTY' as category FROM $this->_table WHERE name LIKE '%$search%' 
			  UNION
			  SELECT id, company_name as name, 'AGENT' as category FROM new_agents WHERE company_name LIKE '%$search%' OR person_name LIKE '%$search%'
			   UNION
			  SELECT id, consignee_name as name, 'CONSIGNEE' as category FROM consignees WHERE consignee_name LIKE '%$search%'
			   UNION
			  SELECT id, name as name, 'VENDOR' as category FROM vendors WHERE name LIKE '%$search%'   
			) as sub
			GROUP BY name
			ORDER BY name DESC
			LIMIT 0, 50";

			$rows = $this->db->query($sql)->result_array();

			header('Content-type: application/json; charset=utf-8');
			echo json_encode($rows, JSON_UNESCAPED_UNICODE);
		}
		else
			echo "Access Denied";
	}

	function excel() {
		$query = $this->db->query("SELECT * FROM $this->_table ORDER BY name");
		$rows  = $query->result_array();

		$this->_excel($rows);
	}

	function partyList() {
		$json = [];
		if ($this->input->is_ajax_request()) {

			if(!empty($this->input->get("q"))){
				$this->db->like('name', $this->input->get("q"));
			}
			
			$query = $this->db->select('id, name as text')
							->limit(10)
							->get('parties');
							
			$json = $query->result_array();
			
			// $json[] = ['id' => 0, 'text' => 'New Customer'];

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
