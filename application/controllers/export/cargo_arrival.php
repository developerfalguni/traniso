<?php

class Cargo_arrival extends MY_Controller {
	function __construct() {
		parent::__construct();
		
		$this->load->model('export');
	}
	
	function index($job_id) {
		$this->edit($job_id);
	}

	function edit($job_id) {
		$data['job_id'] = array('id' => $job_id);
		$data['child_job_id'] = array('id' => 0);
		
		if ($this->input->post('new_date') == false) {
			setSessionError(validation_errors());
			
			$data['jobs']     = $this->export->getJobsInfo($job_id, true, site_url($this->_clspath));
			$data['rows']     = $this->export->getCargoArrivals($job_id);
			$data['invoices'] = $this->export->getInvoiceNos($job_id);
	
			$data['page_title'] = "Cargo Arrival";
			$data['page']       = $this->_clspath.'index';
			$data['job_page']   = $this->_clspath.$this->_class;
			$data['hide_title'] = true;
			$data['docs_url']   = $this->_docs;
			$this->load->view('index', $data);
		}
		else {
			checkDuplicateFormSubmit($this->_clspath.$this->_class."/edit/$job_id");
			
			$delete_ids       = $this->input->post('delete_id') == false ? ['0' => 0] : $this->input->post('delete_id');

			$dates              = $this->input->post('date');
			$job_invoice_ids = $this->input->post("job_invoice_id");
			$vehicle_nos        = $this->input->post('vehicle_no');
			$units              = $this->input->post('units');
			$unit_ids           = $this->input->post('unit_id');
			$dispatch_weights   = $this->input->post('dispatch_weight');
			$received_weights   = $this->input->post('received_weight');
			$supplier_names     = $this->input->post('supplier_name');
			// $supplier_places    = $this->input->post('supplier_place');
			// $lr_nos             = $this->input->post('lr_no');
			// $transporters       = $this->input->post('transporter');
			$remarkss           = $this->input->post('remarks');
			
			foreach ($dates as $index => $date) {
				if (!in_array($index, $delete_ids) && 
					strlen(trim($vehicle_nos[$index])) > 0 && 
					$dispatch_weights[$index] > 0) {
					$data = array(
						'job_id'            => $job_id,
						'job_invoice_id' => $job_invoice_ids[$index],
						'date'              => $date,
						'vehicle_no'        => strtoupper(preg_replace('/[^a-z0-9]/i', '', $vehicle_nos[$index])),
						'units'             => $units[$index],
						'unit_id'           => $unit_ids[$index],
						'dispatch_weight'   => $dispatch_weights[$index],
						'received_weight'   => $received_weights[$index],
						'supplier_name'     => $supplier_names[$index],
						// 'supplier_place'    => $supplier_places[$index],
						// 'lr_no'             => $lr_nos[$index],
						// 'transporter'       => $transporters[$index],
						'remarks'           => $remarkss[$index],
					);
					$this->kaabar->save($this->_table, $data, ['id' => $index]);
				}
				
			}

			if ($delete_ids != null) {
				foreach ($delete_ids as $index) {
					$this->kaabar->delete($this->_table, $index);
				}
			}

			$dates              = $this->input->post('new_date');
			$job_invoice_ids = $this->input->post("new_job_invoice_id");
			$vehicle_nos        = $this->input->post('new_vehicle_no');
			$units              = $this->input->post('new_units');
			$unit_ids           = $this->input->post('new_unit_id');
			$dispatch_weights   = $this->input->post('new_dispatch_weight');
			$received_weights   = $this->input->post('new_received_weight');
			$supplier_names     = $this->input->post('new_supplier_name');
			// $supplier_places    = $this->input->post('new_supplier_place');
			// $lr_nos             = $this->input->post('new_lr_no');
			// $transporters       = $this->input->post('new_transporter');
			$remarkss           = $this->input->post('new_remarks');
			
			foreach ($dates as $index => $date) {
				if (strlen(trim($vehicle_nos[$index])) > 0 && 
					$dispatch_weights[$index] > 0) {
					$data = array(
						'job_id'            => $job_id,
						'job_invoice_id' => $job_invoice_ids[$index],
						'date'              => $date,
						'vehicle_no'        => strtoupper(preg_replace('/[^a-z0-9]/i', '', $vehicle_nos[$index])),
						'units'             => $units[$index],
						'unit_id'           => $unit_ids[$index],
						'dispatch_weight'   => $dispatch_weights[$index],
						'received_weight'   => $received_weights[$index],
						'supplier_name'     => $supplier_names[$index],
						// 'supplier_place'    => $supplier_places[$index],
						// 'lr_no'             => $lr_nos[$index],
						// 'transporter'       => $transporters[$index],
						'remarks'           => $remarkss[$index],
					);
					$this->kaabar->save($this->_table, $data);
				}
				
			}
			setSessionAlert('Changes saved successfully', 'success');

			redirect($this->_clspath.$this->_class."/edit/$job_id");
		}
	}

	function pending($starting_row = 0) {
		$starting_row = intval($starting_row);$search = addslashes($this->input->post('search'));
		if ($search == false && $this->input->post('search_form')) {
			$this->session->unset_userdata($this->_class.'_search'); 
			$search = false;
			redirect($this->_clspath.$this->_class);
		}
		if ($search && $search != $this->session->userdata($this->_class.'_search')) {
			$this->session->set_userdata($this->_class.'_search', $search);
			redirect($this->_clspath.$this->_class);
		}
		else {
			$search = $this->session->userdata($this->_class.'_search');
		}
		$data['search'] = $search;
		$data['show_search'] = true;
		
		$data['list'] = array(
			'heading' => array('Job No', 'Line', 'Booking No', 'Party Name', 'SB No', 'Invoice No', 'Containers', 'Port of Loading', 'Gateway Port', 'POD', 'FPD'),
			'class' => array(
				// 'id'           => 'ID',
				'id2_format'      => array('class' => 'Text', 'link' => 'id'),
				'line_code'       => 'Text',
				'booking_no'      => 'Text',
				'party_name'      => 'Text',
				'sb_no'           => 'Text',
				'invoice_no_date' => 'Text',
				'containers'      => 'Text',
				'custom_port_id'    => 'Text',
				'loading_port'    => 'Text',
				'discharge_port'  => 'Text',
				'fpod'            => 'Text',
			),
			'link_col' => "id2_format",
			'link_url' => $this->_clspath.$this->_class."/pending_edit/");
		
		$this->load->library('pagination');
		$config['base_url']    = site_url($this->_clspath.$this->_class.'/pending_edit');
		$config['uri_segment'] = (strlen($this->_clspath) > 0 ? (3+substr_count($this->_clspath, '/')) : 3);
		$config['total_rows']  = $this->export->countJobs($search);
		$config['per_page']    = Settings::get('rows_per_page');
		$this->pagination->initialize($config);
		
		$data['list']['data'] = $this->export->getJobs($search, $starting_row, $config['per_page']);
		
		$data['page_title'] = 'Export Jobs';
		$data['page']       = 'list';
		$data['docs_url']   = $this->_docs;
		$this->load->view('index', $data);
	}

	function pending_edit($job_id) {
		$data['job_id'] = array('id' => $job_id);
		$data['child_job_id'] = array('id' => 0);
		
		if ($this->input->post('new_date') == false) {
			setSessionError(validation_errors());
			
			$data['jobs']     = $this->export->getJobsInfo($job_id, true, site_url($this->_clspath));
			$data['rows']     = $this->export->getCargoArrivals($job_id);
			$data['invoices'] = $this->export->getInvoiceNos($job_id);
			
			$data['page_title'] = "Cargo Arrival";
			$data['job_page']   = $this->_clspath.$this->_class;
			$data['page']       = $this->_clspath.$this->_class.'_pending';
			$data['hide_title'] = true;
			$data['docs_url']   = $this->_docs;
			$this->load->view('index', $data);
		}
		else {
			checkDuplicateFormSubmit($this->_clspath.$this->_class."/pending_edit/$job_id");
			
			$delete_ids       = $this->input->post('delete_id') == false ? ['0' => 0] : $this->input->post('delete_id');

			$dates              = $this->input->post('date');
			$job_invoice_ids = $this->input->post("job_invoice_id");
			$vehicle_nos        = $this->input->post('vehicle_no');
			$units              = $this->input->post('units');
			$unit_ids           = $this->input->post('unit_id');
			$dispatch_weights   = $this->input->post('dispatch_weight');
			$received_weights   = $this->input->post('received_weight');
			$supplier_names     = $this->input->post('supplier_name');
			// $supplier_places    = $this->input->post('supplier_place');
			// $lr_nos             = $this->input->post('lr_no');
			// $transporters       = $this->input->post('transporter');
			$remarkss           = $this->input->post('remarks');
			
			foreach ($dates as $index => $date) {
				if (!in_array($index, $delete_ids) && 
					strlen(trim($vehicle_nos[$index])) > 0 && 
					$dispatch_weights[$index] > 0) {
					$data = array(
						'job_id'            => $job_id,
						'job_invoice_id' => $job_invoice_ids[$index],
						'date'              => $date,
						'vehicle_no'        => strtoupper(preg_replace('/[^a-z0-9]/i', '', $vehicle_nos[$index])),
						'units'             => $units[$index],
						'unit_id'           => $unit_ids[$index],
						'dispatch_weight'   => $dispatch_weights[$index],
						'received_weight'   => $received_weights[$index],
						'supplier_name'     => $supplier_names[$index],
						// 'supplier_place'    => $supplier_places[$index],
						// 'lr_no'             => $lr_nos[$index],
						// 'transporter'       => $transporters[$index],
						'remarks'           => $remarkss[$index],
					);
					$this->kaabar->save($this->_table, $data, ['id' => $index]);
				}
				
			}

			if ($delete_ids != null) {
				foreach ($delete_ids as $index) {
					$this->kaabar->delete($this->_table, $index);
				}
			}

			$dates              = $this->input->post('new_date');
			$job_invoice_ids = $this->input->post("new_job_invoice_id");
			$vehicle_nos        = $this->input->post('new_vehicle_no');
			$units              = $this->input->post('new_units');
			$unit_ids           = $this->input->post('new_unit_id');
			$dispatch_weights   = $this->input->post('new_dispatch_weight');
			$received_weights   = $this->input->post('new_received_weight');
			$supplier_names     = $this->input->post('new_supplier_name');
			// $supplier_places    = $this->input->post('new_supplier_place');
			// $lr_nos             = $this->input->post('new_lr_no');
			// $transporters       = $this->input->post('new_transporter');
			$remarkss           = $this->input->post('new_remarks');
			
			foreach ($dates as $index => $date) {
				if (strlen(trim($vehicle_nos[$index])) > 0 && 
					$dispatch_weights[$index] > 0) {
					$data = array(
						'job_id'            => $job_id,
						'job_invoice_id' => $job_invoice_ids[$index],
						'date'              => $date,
						'vehicle_no'        => strtoupper(preg_replace('/[^a-z0-9]/i', '', $vehicle_nos[$index])),
						'units'             => $units[$index],
						'unit_id'           => $unit_ids[$index],
						'dispatch_weight'   => $dispatch_weights[$index],
						'received_weight'   => $received_weights[$index],
						'supplier_name'     => $supplier_names[$index],
						// 'supplier_place'    => $supplier_places[$index],
						// 'lr_no'             => $lr_nos[$index],
						// 'transporter'       => $transporters[$index],
						'remarks'           => $remarkss[$index],
					);
					$this->kaabar->save($this->_table, $data);
				}
				
			}
			setSessionAlert('Changes saved successfully', 'success');

			redirect($this->_clspath.$this->_class."/pending_edit/$job_id");
		}
	}
}
