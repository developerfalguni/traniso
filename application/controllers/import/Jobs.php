<?php

class Jobs extends MY_Controller {
	function __construct() {
		parent::__construct();
		
		$this->_table2 = 'child_jobs';
		$this->load->model('import');
		$this->load->model('export');

		$this->_company_id = $this->import->getCompanyID();
		$this->_type = 'Import';
	}
	
	function index() {
		redirect($this->_clspath.$this->_class.'/edit');
	}

	function getJob($job_id = 0){

		$response = [];
		
		if($job_id > 0){

			$job = $this->import->getJob($job_id);
			//print_r($job);exit();
			///////// Find Billing 1 Name using Category
			if($job['billing_party_category'] == 'PARTY')
				$job['party_name'] = $this->kaabar->getField('parties', $job['billing_party_id'], 'id', 'name');
			elseif($job['billing_party_category'] == 'AGENT')
				$job['party_name'] = $this->kaabar->getField('new_agents', $job['billing_party_id'], 'id', 'name');

			///////// Find Shipper Name using Category
			if($job['shipper_category'] == 'PARTY')
				$job['shipper_name'] = $this->kaabar->getField('parties', $job['shipper_id'], 'id', 'name');
			elseif($job['shipper_category'] == 'AGENT')
				$job['shipper_name'] = $this->kaabar->getField('new_agents', $job['shipper_id'], 'id', 'name');
			elseif($job['shipper_category'] == 'CONSIGNEE')
				$job['shipper_name'] = $this->kaabar->getField('consignees', $job['shipper_id'], 'id', 'name');
			elseif($job['shipper_category'] == 'VENDOR')
				$job['shipper_name'] = $this->kaabar->getField('vendors', $job['shipper_id'], 'id', 'name');

			///////// Find Billing 2 Name using Category
			if($job['billing_party1_category'] == 'PARTY')
				$job['party_name2'] = $this->kaabar->getField('parties', $job['billing_party1_id'], 'id', 'name');
			elseif($job['billing_party1_category'] == 'AGENT')
				$job['party_name2'] = $this->kaabar->getField('new_agents', $job['billing_party1_id'], 'id', 'name');
			elseif($job['billing_party1_category'] == 'CONSIGNEE')
				$job['party_name2'] = $this->kaabar->getField('consignees', $job['billing_party1_id'], 'id', 'name');
			elseif($job['billing_party1_category'] == 'VENDOR')
				$job['party_name2'] = $this->kaabar->getField('vendors', $job['billing_party1_id'], 'id', 'name');

			$job['sub_type'] = explode(',', $job['sub_type']);

			$response['success'] = true;
			$response['job'] = $job;
			
		}
		else
		{
			$response['success'] = false;
			$response['messages'] = 'Plaese select Party';
		}

		header('Content-type: application/json; charset=utf-8');
		echo json_encode($response, JSON_UNESCAPED_UNICODE);
	}

	function edit() {

		$data['company_id'] = $this->_company_id;
		$data['sub_type'] = getEnumSetOptions($this->_table, 'sub_type');

		$data['page_title'] = "Import - Jobs Master";
		$data['page']       = $this->_clspath.'index';
		$data['job_page']   = $this->_clspath.$this->_class.'_edit';
		$data['docs_url']   = $this->_docs;
		
		$this->load->view('index', $data);

	}

	function ajaxEdit() {
		
		$response = [];
		$years = $this->kaabar->getFinancialYear($this->input->post('date'));

		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('date', 'Date', 'trim|required|min_length[10]|callback__date_in_financial_year['.$years.']');
		$this->form_validation->set_rules('party_id', 'Party', 'required|is_natural_no_zero');
		$this->form_validation->set_rules('shipper_id', 'Shipper', 'required|is_natural_no_zero');
		
		
		if ($this->form_validation->run() == false) {
			
			//setSessionError(validation_errors());
			$response['success'] = false;
        	foreach ($_POST as $key => $value) {
        		$response['messages'][$key] = form_error($key);
    		}

		}
		else {

			$row_id = $this->input->post('id');

			if ($this->input->post('sub_type'))
				$sub_type = join(',', $this->input->post('sub_type'));
			else
				$sub_type = $this->input->post('sub_type');
			
			$data = array(
				'branch_id'         => $this->input->post('branch_id'),
				'date'              => $this->input->post('date'),
				'type'              => $this->_type,
				'shipment_type'     => $this->input->post('shipment_type'),
				'sub_type'          => $sub_type,
				'job_reference'		=> $this->input->post('job_reference'),
				'billing_party_id'          => $this->input->post('party_id'),
				'billing_party_category'          => $this->input->post('party_category'),
				'shipper_id'        => $this->input->post('shipper_id'),
				'shipper_category'   => $this->input->post('shipper_category'),	
				'consignee_id'      => $this->input->post('consignee_id'),
				'buyer_id'			=> $this->input->post('buyer_id'),
				'notify_id'       	=> $this->input->post('notify_id'),
				'clearance_port_id' => $this->input->post('clearance_port_id'),
				'invoice_no'        => $this->input->post('invoice_no'),
				'invoice_date'      => $this->input->post('invoice_date'),
				'invoice_types'		=> $this->input->post('invoice_types'),
				'por_id'			=> $this->input->post('por_id'),
				'pol_id'			=> $this->input->post('pol_id'),
				'pod_id'			=> $this->input->post('pod_id'),
				'fpod_id'			=> $this->input->post('fpod_id'),
				'cha_id'		 	=> $this->input->post('cha_id'),
				'line_id'			=> $this->input->post('line_id'),
				'forwarder_id'		=> $this->input->post('forwarder_id'),
				'delivery_type'     => $this->input->post('delivery_type'),
				'packages'          => $this->input->post('packages'), 	 	// No Of Packages
				'package_type'   => $this->input->post('package_type'),		// Packages Unit
				'net_weight'        => $this->input->post('net_weight'),
				'gross_weight'      => $this->input->post('gross_weight'),
				'unit_id'           => $this->input->post('unit_id'),
				'incoterms'			=> $this->input->post('incoterms'),
				'sb_no'             => trim($this->input->post('sb_no')),
				'sb_date'           => $this->input->post('sb_date'),
				'remarks'           => $this->input->post('remarks'),
				'item_description'           => $this->input->post('item_description'),
				'billing_party1_id'          => $this->input->post('party_id1'),
				'billing_party1_category'          => $this->input->post('party_category1'),
				'booking_no'        => $this->input->post('booking_no'),
				'booking_date'      => $this->input->post('booking_date'),
				'booking_validity'      => $this->input->post('booking_validity'),
				'mbl_no'            => $this->input->post('mbl_no'),
				'mbl_type'          => $this->input->post('mbl_type'),
				'mbl_date'          => $this->input->post('mbl_date'),
				'hbl_no'            => $this->input->post('hbl_no'),
				'hbl_type'          => $this->input->post('hbl_type'),
				'hbl_date'          => $this->input->post('hbl_date'),
			);

			// if (Auth::isAdmin() OR $this->input->post('status') == 'Completed') {
			//  	$data['status'] = ($this->input->post('status') ? $this->input->post('status') : $row['status']);
			// }
			
			$id = $this->kaabar->save($this->_table, $data, ['id' => $row_id]);
			if($row_id == 0)
				$this->import->createJobNo($id, $data['date']);

			//$this->_updateTranshipment($id);
			//$this->_updateContainer($id);
			//$this->_updatePickup($id);

			if($id){
				$response['success'] = true;
	        	$response['messages'] = 'Succesfully Saved';	
			}
			else
			{
				$response['success'] = false;
				$response['messages'] = 'Somathing Went Wrong';	
			}

			//redirect($this->_clspath.$this->_class."/edit/$id");
		}


		header('Content-type: application/json; charset=utf-8');
		echo json_encode($response, JSON_UNESCAPED_UNICODE);
	}

	function delete($id = 0, $field = 'id') {
		$response = [];
		if ($this->input->is_ajax_request()) {
			if($id){
				
				///// Check in JOB table
				
				$this->db->where(array('id' => $id));
				$result = $this->db->get('jobs')->num_rows();
				
				if($result = 0)
				{
					$response['status'] = 'error';
					$response['msg'] = 'Jon not found';			
				}
				
				else
				{
					$this->db->delete($this->_table, ['id' => $id]);
					$this->db->delete('costsheets', ['job_id' => $id]);
					$this->db->delete('containers', ['job_id' => $id]);
					
					$hbl = $this->kaabar->getRow('hbl_jobs', ['job_id' => $id]);
					
					$this->db->delete('hbl_jobs', ['job_id' => $id]);
					$this->db->delete('hbl_details', ['parent_id' => $hbl['id']]);

					$attachments = $this->kaabar->getRows('attachments', ['parent_id' => $id, 'type' => 'JOB']);
					$unlink = '';

					if(count($attachments) > 0){
						foreach ($attachments as $key => $value) {
							$unlink = $value['path'].$value['name'];
							if($this->db->delete('attachments', ['id' => $value['id']]))
								if($unlink) unlink($unlink);
						}	
					}	
						
					$response['status'] = 'success';
					$response['msg'] = 'Successfully Deleted';			
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

	function ajaxShipmentType($cargo_type) {
		if ($this->input->is_ajax_request()) {
			$search = strtolower($this->input->post_get('term'));

			if ($cargo_type == 'Container')
				echo json_encode($this->import->getContainerShipmentTypes());
			else
				echo json_encode($this->import->getBulkShipmentTypes());
		}
		else
			echo "Access Denied";
	}

	function jobsList() {
		$json = [];
		if ($this->input->is_ajax_request()) {

			if(!empty($this->input->get("q"))){
				$this->db->like('idkaabar_code', $this->input->get("q"));
			}
			
			$query = $this->db->select('id,idkaabar_code as text')
							->where('type', 'Import')
							->limit(10)
							->get('jobs');
			//echo $this->db->last_query(); exit();  
			$json = $query->result_array();
			
			// $json[] = ['id' => 0, 'text' => 'New Job'];

			echo json_encode($json);
		}
		else
			echo "Access Denied";
   }

   
}
