<?php

class Hbl extends MY_Controller {
	function __construct() {
		parent::__construct();
		$this->load->model('export');
		$this->load->model('import');
		$this->load->model('kaabar');
	}
	
	function index() {
		redirect($this->_clspath.$this->_class.'/edit/0');
	}

	function edit($id = 0) {

		$data['id'] = array('id' => 0);

		$data['page_title'] = 'HBL';
		$data['page']       = $this->_clspath.$this->_class.'_edit';
		$data['docs_url']   = $this->_docs;
		$this->load->view('index', $data);
		
	}
	function get($job_id = 0) {

		$response = [];
		
		if($job_id){

			$jobs = $this->kaabar->getRow('jobs',['id' => $job_id]);
			$hblData = $this->kaabar->getRow('hbl_jobs',['job_id' => $job_id]);
			
			if($hblData){

				$jobs = $hblData;
				$job['job_id'] = $job_id;
				$containers = $this->kaabar->getRows('hbl_details', $jobs['id'], 'parent_id');
			}
			else
			{

				$job = $this->export->getJob($job_id);

				$job['job_id'] = $job_id;
				///////// Find Billing 1 Name using Category
				if($job['billing_party_category'] == 'PARTY')
					$party = $this->kaabar->getRow('parties', $job['billing_party_id'], 'id');
				elseif($job['billing_party_category'] == 'AGENT')
					$party = $this->kaabar->getRow('new_agents', $job['billing_party_id'], 'id');

				///////// Find Shipper Name using Category
				
				
				if($job['shipper_category'] == 'PARTY'){
					$shipper = $this->kaabar->getRow('parties', $job['shipper_id'], 'id');
				}
				elseif($job['shipper_category'] == 'AGENT'){
					$shipper = $this->kaabar->getRow('new_agents', $job['shipper_id'], 'id');

					$shipper['name']=$shipper['person_name'];
					$shipper['address']=$shipper['address1'];
					
				}
				elseif($job['shipper_category'] == 'CONSIGNEE')
					$shipper = $this->kaabar->getRow('consignees', $job['shipper_id'], 'id');
				elseif($job['shipper_category'] == 'VENDOR')
					$shipper = $this->kaabar->getRow('vendors', $job['shipper_id'], 'id');

				///////// Find Billing 2 Name using Category
				if($job['billing_party1_category'] == 'PARTY')
					$party2 = $this->kaabar->getRow('parties', $job['billing_party1_id'], 'id');
				elseif($job['billing_party1_category'] == 'AGENT')
					$party2 = $this->kaabar->getRow('new_agents', $job['billing_party1_id'], 'id');
				elseif($job['billing_party1_category'] == 'CONSIGNEE')
					$party2 = $this->kaabar->getRow('consignees', $job['billing_party1_id'], 'id');
				elseif($job['billing_party1_category'] == 'VENDOR')
					$party2 = $this->kaabar->getRow('vendors', $job['billing_party1_id'], 'id');


				$consignee = $this->kaabar->getRow('consignees',['id' => $job['consignee_id']]);
				$notify = $this->kaabar->getRow('consignees',['id' => $job['notify_id']]);
				$receipt_port  = $this->kaabar->getField('ports', ['id' => $job['por_id']]);
				$loading_port  = $this->kaabar->getField('ports', ['id' => $job['pol_id']]);
				$discharge  = $this->kaabar->getField('ports', ['id' => $job['pod_id']]);
				$delivery  = $this->kaabar->getField('ports', ['id' => $job['fpod_id']]);
				

				$jobs['id'] 			= 0;
				$jobs['job_id'] 		= $job['id'];
				$jobs['s_name'] 		= $shipper['name'];
				$jobs['s_address'] 		= $shipper['address'];
				$jobs['c_name'] 		= isset($consignee['consignee_name']) ? $consignee['consignee_name'] : '';
				$jobs['c_address'] 		= isset($consignee['address1']) ? $consignee['address1'] : '';
				$jobs['n_name'] 		= isset($notify['name']) ? $notify['name'] : '';
				$jobs['n_address'] 		= isset($notify['address1']) ? $notify['address1'] : '';
				$jobs['bl_no'] 			= $job['hbl_no'];
				$jobs['bl_type'] 		= $job['hbl_type'];
				$jobs['vessel'] 		= $job['vessel_name'];
				$jobs['voyage'] 		= $job['vessel_voyage'];
				$jobs['booking_no'] 	= $job['booking_no'];
				$jobs['delivery_agent'] = '';
				$jobs['receipt'] 		= $receipt_port ? $receipt_port : '';
				$jobs['loading'] 		= $loading_port ? $loading_port : '';
				$jobs['discharge'] 		= $discharge ? $discharge : '';
				$jobs['delivery'] 		= $delivery ? $delivery : '';
				$jobs['gross_weight'] 	= $job['gross_weight'];
				$jobs['charges_amount'] = '';
				$jobs['payable_at'] 	= '';
				$jobs['no_of_original'] = '';
				$jobs['date_issue'] 	= '';
				$jobs['remarks'] 		= $job['remarks'];

				$containers = $this->kaabar->getRows('containers', $job_id, 'job_id');

				foreach ($containers as $key => $value) {
					$containers[$key]['description'] = $jobs['remarks'];
				}

				$jobs['no_containers'] = count($containers);
				
			}

			
			$response['job'] = $jobs;
			// $container_count = count($containers);
			$response['containers'] = $containers;
			//$response['containers'] = $containers;

		}
		else
		{
			$response['code'] = 0;
			$response['success'] = false;
			$response['messages'] = 'Job No Required';
		}
		header('Content-type: application/json; charset=utf-8');
		echo json_encode($response, JSON_UNESCAPED_UNICODE);
	}

	function ajaxEdit() {

		$response = [];
		$this->load->library('form_validation');
		$this->form_validation->set_rules('loading', 'Loading Port', 'trim|required');
		$this->form_validation->set_rules('delivery', 'Delivery Port', 'required');
		$this->form_validation->set_rules('bl_no', 'BL No', 'required');
		
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
			// echo "<pre>";
			// print_r($this->input->post());exit;
			if (Auth::hasAccess($row_id > 0 ? Auth::UPDATE : Auth::CREATE)) {
				$data = array(
					'job_id'			=> $this->input->post('job_id'),
					's_name'            => $this->input->post('s_name'),
					's_address'        	=> $this->input->post('s_address'),
					'c_name'           	=> $this->input->post('c_name'),
					'c_address'        	=> $this->input->post('c_address'),
					'n_name'           	=> $this->input->post('n_name'),
					'n_address'         => $this->input->post('n_address'),
					'bl_no'         	=> $this->input->post('bl_no'),
					'bl_type'         	=> $this->input->post('bl_type'),
					'vessel'            => $this->input->post('vessel_name'),
					'voyage'           	=> $this->input->post('voyage'),
					'booking_no'        => $this->input->post('booking_no'),
					'delivery_agent'    => $this->input->post('delivery_agent'),
					'receipt'  			=> $this->input->post('receipt'),
					'loading'           => $this->input->post('loading'),
					'discharge'         => $this->input->post('discharge'),
					'delivery'   		=> $this->input->post('delivery'),
					'gross_weight'   	=> $this->input->post('gross_weight'),
					'no_containers'     => $this->input->post('no_of_original'),
					'charges_amount'    => $this->input->post('charges_amount'),
					'payable_at'    	=> $this->input->post('payable_at'),
					'no_of_original'    => $this->input->post('no_of_original'),
					'date_issue'        => $this->input->post('date_issue'),
					'remarks'           => $this->input->post('remarks')
				);
				
				$id = $this->kaabar->save('hbl_jobs', $data, ['id' => $row_id]);
				
				$this->_updateHbl($id, 0);
							
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

	function _updateHbl($job_id, $id) {
		// $delete_ids = $this->input->post('delete_id') == false? ['0' => 0] : $this->input->post('delete_id');
		$descriptions = $this->input->post('description');
		if ($descriptions != null) {
			$number 			= $this->input->post('number');
			$mark_number    = $this->input->post('mark_number');
			$measurement	= $this->input->post('measurement');

			foreach ($descriptions as $index => $description) {
				
					$data = array(
						'parent_id'	  	=> $job_id,
						'description' 	=> $description,
						'number' 		=> $number[$index],
						'mark_number'      	=> $mark_number[$index],
						'measurement'	=> $measurement[$index] ? $measurement[$index] : '-',
					);
					$containers = $this->kaabar->getRows('hbl_details', $job_id, 'parent_id');
					if($containers)
						$this->kaabar->save('hbl_details', $data, ['id' => $index]);
					else
						$this->kaabar->save('hbl_details', $data);
				}
			
		}
	}


	function jobsList() {
		$json = [];
		if ($this->input->is_ajax_request()) {

			if(!empty($this->input->get("q"))){
				$this->db->like('idkaabar_code', $this->input->get("q"));
			}
			
			$query = $this->db->select('id,idkaabar_code as text')
							->where('type', 'Export')
							->limit(10)
							->get('jobs');
			$json = $query->result_array();
			
			echo json_encode($json);
		}
		else
			echo "Access Denied";
   }
}

