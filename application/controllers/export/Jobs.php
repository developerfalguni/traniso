<?php

class Jobs extends MY_Controller {
	function __construct() {
		parent::__construct();
		
		$this->_table2 = 'child_jobs';
		$this->load->model('export');

		$this->_company_id = $this->export->getCompanyID();
		$this->_type = 'Export';
	}
	
	function index() {
		redirect($this->_clspath.$this->_class.'/edit');
	}

	function getJob($job_id = 0){
		
		$response = [];
		
		if($job_id > 0){

			$job = $this->export->getJob($job_id);
			///////// Find Billing 1 Name using Category
			if($job['billing_party_category'] == 'PARTY')
				$job['party_name'] = $this->kaabar->getField('parties', $job['billing_party_id'], 'id', 'name');
			elseif($job['billing_party_category'] == 'AGENT')
				$job['party_name'] = $this->kaabar->getField('new_agents', $job['billing_party_id'], 'id', 'company_name');

			///////// Find Shipper Name using Category
			if($job['shipper_category'] == 'PARTY')
				$job['shipper_name'] = $this->kaabar->getField('parties', $job['shipper_id'], 'id', 'name');
			elseif($job['shipper_category'] == 'AGENT')
				$job['shipper_name'] = $this->kaabar->getField('new_agents', $job['shipper_id'], 'id', 'company_name');
			elseif($job['shipper_category'] == 'CONSIGNEE')
				$job['shipper_name'] = $this->kaabar->getField('consignees', $job['shipper_id'], 'id', 'consignee_name');
			elseif($job['shipper_category'] == 'VENDOR')
				$job['shipper_name'] = $this->kaabar->getField('vendors', $job['shipper_id'], 'id', 'name');

			///////// Find Billing 2 Name using Category
			if($job['billing_party1_category'] == 'PARTY')
				$job['party_name2'] = $this->kaabar->getField('parties', $job['billing_party1_id'], 'id', 'name');
			elseif($job['billing_party1_category'] == 'AGENT')
				$job['party_name2'] = $this->kaabar->getField('new_agents', $job['billing_party1_id'], 'id', 'company_name');
			elseif($job['billing_party1_category'] == 'CONSIGNEE')
				$job['party_name2'] = $this->kaabar->getField('consignees', $job['billing_party1_id'], 'id', 'consignee_name');
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

		$data['page_title'] = "Export - Jobs Master";
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
				'branch_id'         	=> $this->input->post('branch_id'),
				'date'              	=> $this->input->post('date'),
				'type'              	=> $this->_type,
				'shipment_type'     	=> $this->input->post('shipment_type'),
				'sub_type'          	=> $sub_type,
				'job_reference'			=> $this->input->post('job_reference'),
				'billing_party_id'      => $this->input->post('party_id'),
				'billing_party_category'=> $this->input->post('party_category'),
				'shipper_id'        	=> $this->input->post('shipper_id'),
				'shipper_category'   	=> $this->input->post('shipper_category'),	
				'consignee_id'      	=> $this->input->post('consignee_id'),
				'buyer_id'				=> $this->input->post('buyer_id'),
				'notify_id'       		=> $this->input->post('notify_id'),
				'clearance_port_id' 	=> $this->input->post('clearance_port_id'),
				'invoice_no'        	=> $this->input->post('invoice_no'),
				'invoice_date'      	=> $this->input->post('invoice_date'),
				'invoice_types'			=> $this->input->post('invoice_types'),
				'por_id'				=> $this->input->post('por_id'),
				'pol_id'				=> $this->input->post('pol_id'),
				'pod_id'				=> $this->input->post('pod_id'),
				'fpod_id'				=> $this->input->post('fpod_id'),
				'cha_id'		 		=> $this->input->post('cha_id'),
				'line_id'				=> $this->input->post('line_id'),
				'forwarder_id'			=> $this->input->post('forwarder_id'),
				'delivery_type'     	=> $this->input->post('delivery_type'),
				'packages'          	=> $this->input->post('packages'), 	 	// No Of Packages
				'package_type'   		=> $this->input->post('package_type'),		// Packages Unit
				'net_weight'        	=> $this->input->post('net_weight'),
				'gross_weight'      	=> $this->input->post('gross_weight'),
				'unit_id'           	=> $this->input->post('unit_id'),
				'incoterms'				=> $this->input->post('incoterms'),
				'sb_no'             	=> trim($this->input->post('sb_no')),
				'sb_date'           	=> $this->input->post('sb_date'),
				'remarks'           	=> $this->input->post('remarks'),
				'item_description'      => $this->input->post('item_description'),
				'billing_party1_id'     => $this->input->post('party_id1'),
				'billing_party1_category'=> $this->input->post('party_category1'),
				'booking_no'        	=> $this->input->post('booking_no'),
				'booking_date'      	=> $this->input->post('booking_date'),
				'cntr_booking_no'		=> $this->input->post('booking_no'),
				'cntr_booking_date'		=> $this->input->post('booking_date'),
				'booking_validity'      => $this->input->post('booking_validity'),
				'mbl_no'            	=> $this->input->post('mbl_no'),
				'mbl_type'          	=> $this->input->post('mbl_type'),
				'mbl_date'          	=> $this->input->post('mbl_date'),
				'hbl_no'            	=> $this->input->post('hbl_no'),
				'hbl_type'          	=> $this->input->post('hbl_type'),
				'hbl_date'          	=> $this->input->post('hbl_date'),
			);

			$branchcode = $this->kaabar->getRow('branches', ['company_id' => $this->_company_id, 'id' => $data['branch_id']]);
			$id = $this->kaabar->save($this->_table, $data, ['id' => $row_id]);
			if($row_id == 0)
				$this->export->createJobNo($id, $data['date'], $branchcode['series']);

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
		header('Content-type: application/json; charset=utf-8');
		echo json_encode($response, JSON_UNESCAPED_UNICODE);
	}

	function _updateTranshipment($id) {
		$eta_dates  = $this->input->post("eta_date");
		$delete_ids = $this->input->post('delete_id') == false? ['0' => 0] : $this->input->post('delete_id');
		if ($eta_dates) {
			foreach ($eta_dates as $index => $eta_date) {
				$data = array(
					'eta_date' => $eta_dates[$index],
				);
				$this->kaabar->save('transhipments', $data, ['id' => $index]);
			}
		}

		if ($delete_ids != null) {
			foreach ($delete_ids as $index => $tmp) {
				$this->kaabar->delete('transhipments', ['id' => $index]);
			}
		}

		$port_ids = $this->input->post('new_port_id');
		if ($port_ids) {
			$eta_dates      = $this->input->post("new_eta_date");
			foreach ($port_ids as $index => $port_id) {
				if (intval($port_id) > 0) {
					$data = array(
						'job_id'   => $id,
						'port_id'  => $port_id,
						'eta_date' => $eta_dates[$index],
					);
					$this->kaabar->save('transhipments', $data);
				}
			}
		}
	}

	function _updateContainer($id) {
		$containerss = $this->input->post("pc_containers");
		$delete_ids = $this->input->post('pc_delete_id') == false? ['0' => 0] : $this->input->post('pc_delete_id');
		if ($containerss) {
			$container_type_ids = $this->input->post("pc_ct_id");
			foreach ($containerss as $index => $containers) {
				if (intval($containers) > 0) {
					$data = array(
						'containers'        => $containers,
						'container_type_id' => $container_type_ids[$index],
					);
					$this->kaabar->save('job_containers', $data, ['id' => $index]);
				}
			}
		}	

		if ($delete_ids != null) {
			foreach ($delete_ids as $index => $tmp) {
				if ($index > 0) {
					$this->kaabar->delete('job_containers', ['id' => $index]);
				}
			}
		}

		$containerss = $this->input->post("pc_new_containers");
		if ($containerss) {
			$container_type_ids = $this->input->post("pc_new_ct_id");
			foreach ($containerss as $index => $containers) {
				if (intval($containers) > 0) {
					$data = array(
						'job_id'            => $id,
						'containers'        => $containers,
						'container_type_id' => $container_type_ids[$index],
					);
					$this->kaabar->save('job_containers', $data);
				}
			}
		}
	}

	function _updatePickup($id) {
		$containerss = $this->input->post("pick_containers");
		$delete_ids = $this->input->post('pick_delete_id') == false? ['0' => 0] : $this->input->post('pick_delete_id');
		if ($containerss) {
			$container_type_ids = $this->input->post("pick_ct_id");
			$gross_weights      = $this->input->post("pick_gross_weight");
			$pickup_location_ids= $this->input->post("pick_pickup_location_id");
			$pickup_dates       = $this->input->post("pick_pickup_date");
			$stuffing_dates     = $this->input->post("pick_stuffing_date");
			$stuffing_locations	= $this->input->post("pick_stuffing_location");
			foreach ($containerss as $index => $containers) {
				if (strlen($containers) > 0) {
					$data = array(
						'containers'         => $containers,
						'container_type_id'  => $container_type_ids[$index],
						'gross_weight'       => $gross_weights[$index],
						'pickup_location_id' => $pickup_location_ids[$index],
						'pickup_date'        => $pickup_dates[$index],
						'stuffing_date'      => $stuffing_dates[$index],
						'stuffing_location'  => $stuffing_locations[$index],
					);
					$this->kaabar->save('pickups', $data, ['id' => $index]);
				}
			}
		}	

		if ($delete_ids != null) {
			foreach ($delete_ids as $index => $tmp) {
				if ($index > 0) {
					$this->kaabar->delete('pickups', ['id' => $index]);
				}
			}
		}

		$containerss = $this->input->post("pick_new_containers");
		if ($containerss) {
			$container_type_ids = $this->input->post("pick_new_ct_id");
			$gross_weights      = $this->input->post("pick_new_gross_weight");
			$pickup_location_ids= $this->input->post("pick_new_pickup_location_id");
			$pickup_dates       = $this->input->post("pick_new_pickup_date");
			$stuffing_dates     = $this->input->post("pick_new_stuffing_date");
			$stuffing_locations = $this->input->post("pick_new_stuffing_location");
			foreach ($containerss as $index => $containers) {
				if (intval($containers) > 0) {
					$data = array(
						'job_id'             => $id,
						'containers'         => $containers,
						'container_type_id'  => $container_type_ids[$index],
						'gross_weight'       => $gross_weights[$index],
						'pickup_location_id' => $pickup_location_ids[$index],
						'pickup_date'        => $pickup_dates[$index],
						'stuffing_date'      => $stuffing_dates[$index],
						'stuffing_location'  => $stuffing_locations[$index],
					);
					$this->kaabar->save('pickups', $data);
				}
			}
		}
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
					/// Check Invoice generated or not

					$inv = $this->kaabar->getRow('invoices', ['job_id' => $id]);

					if(count($inv) > 0){
						$response['status'] = 'error';
						$response['msg'] = ' Invoice no '.$inv['idkaabar_code'].' generated for this job, Please delete this invoice first...!';
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
						$response['msg'] = 'Job Successfully Deleted...!';	
					}
					
								
				}
				
			}
			else
			{
				$response['status'] = 'error';
				$response['msg'] = 'Please Select Job First then delete';		
			}
			header('Content-type: application/json; charset=utf-8');
			echo json_encode($response, JSON_UNESCAPED_UNICODE);
		}
		else
			echo "Access Denied";
	}


	function importJobs($id) {
		$manual = $this->input->post('manual');
		$job_no = $this->input->post('job_no');
		if ($manual) {
			$this->db->insert('child_jobs', array('job_id' => $id, 'vi_job_no' => $job_no));
		}
		else if ($job_no) {
			$this->load->model('visualimpex');
			$this->visualimpex->export($id, $job_no);

			$stuffings = $this->kaabar->getRows('deliveries_stuffings', $id, 'job_id');
			foreach ($stuffings as $r) {
				// Updating ContainerID in stuffings
				$container_id = $this->kaabar->getField('containers', array(
					'job_id' => $id, 
					'number' => $r['container_no']),
					'id', 'id'
				);
				$this->kaabar->save('deliveries_stuffings', array('container_id' => $container_id), array('id' => $r['id']));
			}

			setSessionAlert('VisualImpex Job Imported Successfully.', 'success');
		}
		redirect($this->agent->referrer());
	}

	function ajaxShipmentType($cargo_type) {
		if ($this->input->is_ajax_request()) {
			$search = strtolower($this->input->post_get('term'));

			if ($cargo_type == 'Container')
				echo json_encode($this->export->getContainerShipmentTypes());
			else
				echo json_encode($this->export->getBulkShipmentTypes());
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
							->where('type', 'Export')
							->limit(10)
							->get('jobs');
			$json = $query->result_array();
			
			// $json[] = ['id' => 0, 'text' => 'New Job'];

			echo json_encode($json);
		}
		else
			echo "Access Denied";
    }

    function getAllJob() {
		$json = [];
		$cont_20 = 0;
		$cont_40 = 0;
		if ($this->input->is_ajax_request()) {

			$rows = $this->kaabar->getRows($this->_table);

			if($rows){

				foreach ($rows as $key => $value) {

					$containers = $this->kaabar->getRows('containers', $value['id'], 'job_id');
					foreach ($containers as $k => $v) {

						$cont_20 += $v['size'] === '20' ? 1 : 0;
						$cont_40 += $v['size'] === '40' ? 1 : 0;
					}

					if($value['shipper_category'] == 'PARTY')
						$shipper_name = $this->kaabar->getField('parties', $value['shipper_id'], 'id', 'name');
					elseif($value['shipper_category'] == 'AGENT')
						$shipper_name = $this->kaabar->getField('new_agents', $value['shipper_id'], 'id', 'company_name');
					elseif($value['shipper_category'] == 'CONSIGNEE')
						$shipper_name = $this->kaabar->getField('consignees', $value['shipper_id'], 'id', 'consignee_name');
					elseif($value['shipper_category'] == 'VENDOR')
						$shipper_name = $this->kaabar->getField('vendors', $value['shipper_id'], 'id', 'name');


					$row = $value;
					$row['date'] = _convDate($value['date']);
					$row['cont'] = $cont_20.' X 20 | '.$cont_40.' X 40';
					$row['shipper_name'] = $shipper_name;

					$json[] = $row;

				}

			}
			
			// echo '<pre>';
			// print_r($json);exit;

			echo json_encode($json);
		}
		else
			echo "Access Denied";
    }

    function filecover($id = 0, $pdf = 0) {

		$id = intval($id);

		$job = $this->export->getJob($id);
		///////// Find Billing 1 Name using Category
		if($job['billing_party_category'] == 'PARTY')
			$job['party_name'] = $this->kaabar->getField('parties', $job['billing_party_id'], 'id', 'name');
		elseif($job['billing_party_category'] == 'AGENT')
			$job['party_name'] = $this->kaabar->getField('new_agents', $job['billing_party_id'], 'id', 'company_name');

		///////// Find Shipper Name using Category
		if($job['shipper_category'] == 'PARTY')
			$job['shipper_name'] = $this->kaabar->getField('parties', $job['shipper_id'], 'id', 'name');
		elseif($job['shipper_category'] == 'AGENT')
			$job['shipper_name'] = $this->kaabar->getField('new_agents', $job['shipper_id'], 'id', 'company_name');
		elseif($job['shipper_category'] == 'CONSIGNEE')
			$job['shipper_name'] = $this->kaabar->getField('consignees', $job['shipper_id'], 'id', 'consignee_name');
		elseif($job['shipper_category'] == 'VENDOR')
			$job['shipper_name'] = $this->kaabar->getField('vendors', $job['shipper_id'], 'id', 'name');

		///////// Find Billing 2 Name using Category
		if($job['billing_party1_category'] == 'PARTY')
			$job['party_name2'] = $this->kaabar->getField('parties', $job['billing_party1_id'], 'id', 'name');
		elseif($job['billing_party1_category'] == 'AGENT')
			$job['party_name2'] = $this->kaabar->getField('new_agents', $job['billing_party1_id'], 'id', 'company_name');
		elseif($job['billing_party1_category'] == 'CONSIGNEE')
			$job['party_name2'] = $this->kaabar->getField('consignees', $job['billing_party1_id'], 'id', 'consignee_name');
		elseif($job['billing_party1_category'] == 'VENDOR')
			$job['party_name2'] = $this->kaabar->getField('vendors', $job['billing_party1_id'], 'id', 'name');

		$cont_20 = 0;
		$cont_40 = 0;
		$containers = $this->kaabar->getRows('containers', $id, 'job_id');
		if($containers){
			foreach ($containers as $key => $value) {
				$cont_20 += $value['size'] === '20' ? 1 : 0;
				$cont_40 += $value['size'] === '40' ? 1 : 0;
			}
		}
		$job['cntr_lot'] = $cont_20.' X 20 | '.$cont_40.' X 40';

		$data['job'] = $job;


		$data['title'] = $this->_type;

		$filename = strtoupper('File Cover - '.$data['job']['idkaabar_code']);

        $data['page_title'] = 'File Cover';

        if($pdf > 0)
        {
        	$pdf = new MYPDF('P', 'mm', 'A4', true, 'UTF-8', false);
			$pdf->SetTitle($filename);
			$pdf->setPrintHeader(true);
			$pdf->setPrintFooter(true);
			$pdf->SetMargins(5, 38, 5, 5);
			$pdf->SetAutoPageBreak(TRUE, 12);
			$pdf->SetFooterMargin(12);
			$pdf->SetAuthor('Chetan Patel - Connect IT Hub');
			$pdf->SetDisplayMode('real', 'default');

			// Pass Data
			$pdf->page_title = $data['page_title'];
			$pdf->invoiceCopy = $data['invoiceCopy'];

			$logo = $this->kaabar->getField('companies', ['id' => $this->_companyId], 'id', 'logo');
			$logodir = $this->kaabar->getDocFolder($this->_path, $this->_companyId);
			$pdf->image_file = FCPATH.'images/'.$logodir.$logo;

			$pdf->company = $data['company'];
			$pdf->input_assets = $data['input_assets'];
			$pdf->og_assets = $data['og_assets'];
			$pdf->label_assets = $data['label_assets'];

			$pdf->AddPage('P', 'A4');

			$page = strtolower('export_quote');
			$this->load->model('Gst_invoice');
			$this->Gst_invoice->$page($pdf, $data, $letterhead);

			$docdir = $this->kaabar->getDocFolder($this->_path, $id);
	        $filename = preg_replace('/[^a-zA-Z0-9_.]/', '_', $filename.'.pdf');
	        $filepath = $this->_path.$docdir.$filename;
	        $pdf->Output($filepath, 'F');
	        return $filename;
        }
        else
        {
        	
        	$pdf = new MYPDF('P', 'mm', 'A4', true, 'UTF-8', false);
			$pdf->SetTitle($filename);
			$pdf->setPrintHeader(FALSE);
			$pdf->setPrintFooter(FALSE);
			$pdf->SetMargins(5, 5, 5, 5);
			$pdf->SetAutoPageBreak(TRUE, 5);
			$pdf->SetFooterMargin(5);
			$pdf->SetAuthor('Chetan Patel - Connect IT Hub');
			$pdf->SetDisplayMode('real', 'default');
	        
			$pdf->AddPage('P', 'A4');
			$page = strtolower('filecover');
			$this->load->model('export_print');

			$this->export_print->$page($pdf, $data, $letterhead = null);
			$file = preg_replace('/[^a-zA-Z0-9_.]/', '_', $filename.'.pdf');
			$pdf->Output($file, 'I');
		}
    }


   
}
