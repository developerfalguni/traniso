<?php

class Container extends MY_Controller {
	function __construct() {
		parent::__construct();
		$this->_table = "containers";
		$this->load->model('export');
	}
	
	function index($job_id = 0, $starting_row = 0) {

		$data['job_id']     = $job_id;
		
		$jobs = $this->kaabar->getRow('jobs', $job_id);
		foreach ($jobs as $f => $v) {
			$jobs[$f] = _convDate($v);
		}
		
	
		$rows = $this->kaabar->getRows($this->_table, $job_id, 'job_id');

		if(empty($rows))
			$containers = $this->kaabar->getRow('jobs', $job_id);

		foreach ($rows as $key => $value) {
			
			$deleteBtn = form_checkbox(array('name' => 'delete_id['.$value['id'].']', 'value' => $value['id'], 'checked' => false, 'class' => 'DeleteCheckbox'));

			$rows[$key]['delete_btn'] = $deleteBtn;

		}

		$data['jobs'] = $jobs;
		$data['rows'] = $rows;

		header('Content-type: application/json; charset=utf-8');
		echo json_encode($data, JSON_UNESCAPED_UNICODE);
	}

	function edit($job_id = 0) {

		$response = [];
		$query = $this->db->get_where('jobs', array('id' => $job_id));
		$row = $query->row_array();
		if($row == false) {

			$row = array(
				'booking_no'		=> '',
				'booking_date'		=> '',
				'vessel_name'		=> '',
				'vessel_voyage'		=> '',
				'eta_date'			=> '',
				'etd_date'			=> '',
				'expiry_date'		=> '',
			); 

			$response['success'] = false;
		    $response['messages'] = 'Job No Not found';
		}
		else
		{
			$jobs = array(
        		'cntr_booking_no'	=> $this->input->post('booking_no'),
				'cntr_booking_date'	=> $this->input->post('booking_date'),
				'booking_no'		=> $this->input->post('booking_no'),
				'booking_date'		=> $this->input->post('booking_date'),
				'vessel_name'		=> $this->input->post('vessel_name'),
				'vessel_voyage'		=> $this->input->post('vessel_voyage'),
				'eta_date'			=> $this->input->post('eta_date'),
				'etd_date'			=> $this->input->post('etd_date'),
				'expiry_date'		=> $this->input->post('expiry_date'),
        	);

			$chetan = $this->kaabar->save('jobs', $jobs, ['id' => $job_id]);

			$delete_ids = $this->input->post('delete_id') == false ? ['0' => '0'] : $this->input->post('delete_id');
			$numbers = $this->input->post('number');
			$new_numbers = $this->input->post('new_number');

			
			if ($numbers != null) {

				$size         	= $this->input->post('size');
				$container_type = $this->input->post('container_type');
				$from         	= $this->input->post('from');
				$to         	= $this->input->post('to');
				$vehicle_no     = $this->input->post('vehicle_no');
				$transporter    = $this->input->post('transporter');
				$line_seal 		= $this->input->post('line_seal');
				$shipper_seal   = $this->input->post('shipper_seal');
				$custom_seal    = $this->input->post('custom_seal');

				$cntr_packages    = $this->input->post('cntr_packages');
				$cntr_gross_weight    = $this->input->post('cntr_gross_weight');
				$cntr_net_weight    = $this->input->post('cntr_net_weight');
				
				foreach ($numbers as $index => $number) {

					$data = array(
						'job_id'      		=> $job_id,
						'number'    		=> $number,
						'size'       		=> $size[$index],
						'container_type'	=> $container_type[$index],
						'from'         		=> $from[$index],
						'to'         	 	=> $to[$index],
						'vehicle_no'    	=> $vehicle_no[$index],
						'transporter' 		=> $transporter[$index],
						'line_seal'      	=> $line_seal[$index],
						'shipper_seal'   	=> $shipper_seal[$index],
						'custom_seal'    	=> $custom_seal[$index],
						'cntr_packages'    	=> $cntr_packages[$index],
						'cntr_gross_weight' => $cntr_gross_weight[$index],
						'cntr_net_weight'   => $cntr_net_weight[$index],
					);
					
					$this->kaabar->save($this->_table, $data, ['id' => $index]);
				}
			}

			if ($delete_ids != null) {
				foreach ($delete_ids as $index) {
					if ($index > 0) {
						$this->kaabar->delete($this->_table, $index);
					}
				}
			}

			if ($new_numbers != null) {

				$size         		= $this->input->post('new_size');
				$container_type  	= $this->input->post('new_container_type');
				$from         		= $this->input->post('new_from');
				$to         		= $this->input->post('new_to');
				$vehicle_no         = $this->input->post('new_vehicle_no');
				$transporter     	= $this->input->post('new_transporter');
				$line_seal 			= $this->input->post('new_line_seal');
				$shipper_seal       = $this->input->post('new_shipper_seal');
				$custom_seal        = $this->input->post('new_custom_seal');
				$cntr_packages    = $this->input->post('new_cntr_packages');
				$cntr_gross_weight    = $this->input->post('new_cntr_gross_weight');
				$cntr_net_weight    = $this->input->post('new_cntr_net_weight');


				$data = [];

				foreach ($new_numbers as $index => $number) {
					
					if ($number != null) {
						$data = array(
							'job_id'      		=> $job_id,
							'number'    		=> $number,
							'size'           	=> $size[$index],
							'container_type'    => $container_type[$index],
							'from'         		=> $from[$index],
							'to'         		=> $to[$index],
							'vehicle_no'        => $vehicle_no[$index],
							'transporter'       => $transporter[$index],
							'line_seal'      	=> $line_seal[$index],
							'shipper_seal'      => $shipper_seal[$index],
							'custom_seal'       => $custom_seal[$index],
							'cntr_packages'    	=> $cntr_packages[$index],
							'cntr_gross_weight' => $cntr_gross_weight[$index],
							'cntr_net_weight'   => $cntr_net_weight[$index],
						);
						$id = $this->kaabar->save($this->_table, $data);
					}
					
				}
				
			}

			$response['success'] = true;
			$response['messages'] = 'Successfull Updated';

			header('Content-type: application/json; charset=utf-8');
			echo json_encode($response, JSON_UNESCAPED_UNICODE);
		}		        		

		
		
		
		// $response = array();
		// if ($job_id <= 0) {
		// 	setSessionError('SELECT_JOBS');
		// 	redirect($this->_clspath."jobs");
		// }

		// $this->db->select("id");
		// $query = $this->db->get_where('jobs', array('id' => $job_id));
		// $row = $query->row_array();
		// if($row == false) {

		// 	$row = array(
		// 		'booking_no'		=> '',
		// 		'booking_date'		=> '',
		// 		'vessel_name'		=> '',
		// 		'fpod_eta'			=> '',
		// 		'fpod_etd'			=> '',
		// 		'expiry_date'		=> '',
		// 	); 

		// 	$response['success'] = false;
		//     $response['messages'] = 'Job No Not found';
		// }
		// else
		// {
		// 	if($this->input->method() == 'post'){

		// 		$this->load->library('form_validation');
		// 		$this->form_validation->set_rules('number[]', 'Container No', 'trim');
		// 		$this->form_validation->set_error_delimiters('<p class="text-danger">','</p>');

		// 		if ($this->form_validation->run() == FALSE) {
		// 			$response['success'] = false;
		//         	foreach ($_POST as $key => $value) {
		//         		$response['messages'][$key] = form_error($key);
		//         	}
		//         }
		// 		else
		// 		{

		// 			$ids = $this->input->post('id');

		// 			if($ids != null){

		// 				foreach ($ids as $index => $id) {

		// 					$data = array(
		// 		        		'number' 			=> $this->input->post('number')[$index],
		// 		        		'container_type_id' => $this->input->post('container_type_id')[$index],	
		// 		        		'from' 				=> $this->input->post('from')[$index],	
		// 		        		'to'				=> $this->input->post('to')[$index],	
		// 		        		'vehicle_no' 		=> $this->input->post('vehicle_no')[$index],	
		// 		        		'transporter_id' 	=> $this->input->post('transporter_id')[$index],	
		// 		        		'line_seal' 		=> $this->input->post('line_seal')[$index],	
		// 		        		'shipper_seal' 		=> $this->input->post('shipper_seal')[$index],	
		// 		        		'custom_seal' 		=> $this->input->post('custom_seal')[$index],
		// 		        		'net_weight' 		=> $this->input->post('net_weight')[$index],	
		// 		        	);

				  //       	$jobs = array(
				  //       		'cntr_booking_no'		=> $this->input->post('booking_no'),
						// 		'cntr_booking_date'		=> $this->input->post('booking_date'),
						// 		'vessel_name'		=> $this->input->post('vessel_name'),
						// 		'fpod_eta'			=> $this->input->post('fpod_eta'),
						// 		'fpod_etd'			=> $this->input->post('fpod_etd'),
						// 		'expiry_date'		=> $this->input->post('expiry_date'),
				  //       	);

						// 	$this->kaabar->save($this->_table, $data, ['id' => $id]);
						// 	$this->kaabar->save('jobs', $jobs, ['id' => $job_id]);

						// }		        		
		// 	        }

		// 	        $response['job_id'] = $job_id;
		// 	        $response['success'] = true;
		//         	$response['messages'] = 'Succesfully Saved';
			        
		// 	    }

		//         // $this->load->library('form_validation');
			
		// 		// $this->form_validation->set_error_delimiters('', '');
		// 		// $this->form_validation->set_rules('container_type_id', 'Container Type', 'trim');
		// 		// if ($id > 0) {
		// 		// 	$this->form_validation->set_rules('number', 'Container Number', 'trim|required');
		// 		// 	$this->form_validation->set_rules('seal', 'Seal No', 'trim|required');
		// 		// }
		// 	}
		// 	else
		// 	{
		// 		$row = $this->kaabar->getRow($this->_table, ['id' => $id, 'job_id' => $job_id]);

		// 		$container_types =  form_dropdown('container_type_id', getSelectOptions('container_types', 'id', "CONCAT(size,' ',code, ' - ', name)", 'where size = '.$row['size']), $row['container_type_id'], 'id="container_type_id" class="form-control form-control-sm"');
		// 		$response['container_types'] = $this->minifier($container_types);

		// 		$transporter =  form_dropdown('transporter_id', getSelectOptions('agents', 'id', 'name', 'where type = "Transporter"'), $row['transporter_id'], 'id="transporter_id" class="form-control form-control-sm"');
		// 		$response['transporter'] = $this->minifier($transporter);

		// 		$response['success'] = true;
		// 		$response['row'] = $row;
				
		// 	}
			
		// }


			
		// header('Content-type: application/json; charset=utf-8');
		// echo json_encode($response, JSON_UNESCAPED_UNICODE);

		// if ($this->form_validation->run() == false) {
		// 	setSessionError(validation_errors());
			
		// 	$data['jobs']       = $this->import->getJobsInfo($job_id, true, site_url($this->_clspath));
		// 	$data['focus_id']   = "Date";
		// 	$data['page_title'] = humanize($this->_class);
		// 	$data['page']       = $this->_clspath.'index';
		// 	$data['job_page']   = $this->_clspath.$this->_class.'_edit';
		// 	$data['docs_url']   = $this->_docs;
		// 	$this->load->view('index', $data);
		// }
		// else {
		// 	checkDuplicateFormSubmit($this->_clspath.$this->_class."/index/$job_id");

		// 	if ($id == 0) {
		// 		if (strlen($this->input->post('number_seal')) > 0) {
		// 			$containers = explode("\n", str_replace("\r", '', $this->input->post('number_seal')));
		// 			foreach($containers as $c) {
		// 				list($n, $s) = explode(" ", $c);
		// 				$data = array(
		// 					'job_id'            => $job_id,
		// 					'container_type_id' => $this->input->post('container_type_id'),
		// 					'number'            => $n,
		// 					'seal'              => (is_null($s) ? '' : $s)
		// 				);
		// 				$this->kaabar->save($this->_table, $data, ['id' => $id]);
		// 			}
		// 			setSessionAlert('Changes saved successfully', 'success');
		// 			redirect($this->_clspath.$this->_class."/index/$job_id");
		// 		}
		// 	}
		// 	else {
		// 		$data = array(
		// 			'job_id'             => $job_id,
		// 			'container_type_id'  => $this->input->post('container_type_id'),
		// 			'number'             => $this->input->post('number'),
		// 			'seal'               => $this->input->post('seal')
		// 		);
		// 		$id = $this->kaabar->save($this->_table, $data, ['id' => $id]);
		// 	}
		// 	setSessionAlert('Changes saved successfully', 'success');

		// 	redirect($this->_clspath.$this->_class."/edit/$job_id/$id");
		// }
	}

	function minifier($code) {
	    $search = array(
	          
	        // Remove whitespaces after tags
	        '/\>[^\S ]+/s',
	          
	        // Remove whitespaces before tags
	        '/[^\S ]+\</s',
	          
	        // Remove multiple whitespace sequences
	        '/(\s)+/s',
	          
	        // Removes comments
	        '/<!--(.|\s)*?-->/'
	    );
	    $replace = array('>', '<', '\\1');
	    $code = preg_replace($search, $replace, $code);
	    return $code;
	}
	
	function deleteContainer($job_id, $id = 0) {
		if (Auth::isAdmin()) {
			if ($id == 0) 
				$this->db->delete($this->_table, array('job_id' => $job_id));
			else
				$this->db->delete($this->_table, array('id' => $id));
			setSessionAlert('All Containers Delete Successfully', 'success');
		}
		redirect($this->_clspath.$this->_class."/index/$job_id");
	}

	function deleteattach() {

		$job_id = $this->input->post('job_id');
		$id = $this->input->post('row_id');

		$response = [];

		if($job_id AND $id)
		{

			$file = $this->kaabar->getField($this->_table, $id, 'id', 'file');
			$docdir = $this->export->getDocFolder($this->_path, $job_id);
			
			$path = $this->_path.$docdir.$file;
			

			if($this->db->update($this->_table, array("file" => ''), "id = $id")){

				if(file_exists($path))
					unlink($path);

				$response['success'] = true;
				$response['messages'] = 'Attachment Successfull Deleted';
			}
			else{
				$response['success'] = false;
				$response['messages'] = 'Something wrong, Try Again';
			}
		}
		else
		{
			$response['success'] = false;
			$response['messages'] = 'Something wrong, Try Again';
		}

		header('Content-type: application/json; charset=utf-8');
		echo json_encode($response, JSON_UNESCAPED_UNICODE);
	}

	function preview($job_id, $pdf = 0) {
		$default_company = $this->session->userdata('default_company');
	    $data['company']    = $this->kaabar->getRow('companies', $default_company['id']);
		$search             = $this->session->userdata($this->_class.'_search');
		$data['rows']       = $this->export->getContainers($job_id, $search = '');
		$data['page']       = 'reports/'.$this->_class.'_import_preview';
		$data['page_title'] = humanize($this->_class . 's');
		$data['filename']   = strtolower((strlen($search) > 0 ? $search . '_' : '') . date('d-m-Y'));
		
		$this->_preview($data, $pdf);
	}
}
