<?php

class Container extends MY_Controller {
	function __construct() {
		parent::__construct();
	
		$this->load->model('import');
	}
	
	function index($job_id = 0, $starting_row = 0) {
		$starting_row = intval($starting_row);$config['per_page'] = Settings::get('rows_per_page');
		$data['job_id']     = $job_id;
		$net_weights        = $this->input->post('net_weight');
		if ($net_weights) {
			foreach($net_weights as $container_id => $net_weight) {
				$this->kaabar->save($this->_table, array('net_weight' => $net_weight), array('id' => $container_id));
				// $delivery = $this->kaabar->getRow('deliveries_stuffings', array('container_id' => $container_id));
				// if ($delivery) {
				// 	if ($delivery['dispatch_weight'] == 0)
				// 	$this->db->update('deliveries_stuffings', array('dispatch_weight' => $net_weight), array('container_id' => $container_id));
				// }
				// else {
				// 	$this->db->insert('deliveries_stuffings', array('container_id' => $container_id, 'dispatch_weight' => $net_weight));
				// }
				setSessionAlert('Changes saved successfully', 'success');
			}
		}

		$data['rows'] = $this->import->getContainers($job_id, $search = '', $starting_row, $config['per_page']);
		
		$data['jobs'] = $this->import->getJobsInfo($job_id, true, site_url($this->_clspath));
		
		// $data['buttons'] = array(anchor($this->_clspath.$this->_class."/edit/$job_id/0", '<i class="fa fa-plus"></i><span class="hidden-xs"> Add</span>', 'class="btn btn-success" id="AddNew"'));
		
		$data['page_title'] = humanize($this->_class);
		$data['page']       = $this->_clspath.'index';
		$data['job_page']   = $this->_clspath.$this->_class;
		$this->load->view('index', $data);
	}

	function edit($job_id = 0, $id = 0) {
		if ($job_id <= 0) {
			setSessionError('SELECT_JOBS');
			redirect($this->_clspath."jobs");
		}

		$this->db->select("id");
		$query = $this->db->get_where('jobs', array('id' => $job_id));
		$row = $query->row_array();
		if($row == false) {
			setSessionError('SELECT_JOBS');
			redirect($this->_clspath."jobs");
		}
		
		$this->load->library('form_validation');
		
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('container_type_id', 'Container Type', 'trim');
		if ($id > 0) {
			$this->form_validation->set_rules('number', 'Container Number', 'trim|required');
			$this->form_validation->set_rules('seal', 'Seal No', 'trim|required');
		}
		
		$row = $this->kaabar->getRow($this->_table, $id);
		if ($row == false) {
			$row = array(
				'id'                => 0,
				'job_id'            => $job_id,
				'container_type_id' => 0,
				'number'            => '',
				'seal'              => ''
			);
		}
		
		$data['id'] = ['id' => $id];
		$data['row'] = $row;
		
		if ($this->form_validation->run() == false) {
			setSessionError(validation_errors());
			
			$data['jobs']       = $this->import->getJobsInfo($job_id, true, site_url($this->_clspath));
			$data['focus_id']   = "Date";
			$data['page_title'] = humanize($this->_class);
			$data['page']       = $this->_clspath.'index';
			$data['job_page']   = $this->_clspath.$this->_class.'_edit';
			$data['docs_url']   = $this->_docs;
			$this->load->view('index', $data);
		}
		else {
			checkDuplicateFormSubmit($this->_clspath.$this->_class."/index/$job_id");

			if ($id == 0) {
				if (strlen($this->input->post('number_seal')) > 0) {
					$containers = explode("\n", str_replace("\r", '', $this->input->post('number_seal')));
					foreach($containers as $c) {
						list($n, $s) = explode(" ", $c);
						$data = array(
							'job_id'            => $job_id,
							'container_type_id' => $this->input->post('container_type_id'),
							'number'            => $n,
							'seal'              => (is_null($s) ? '' : $s)
						);
						$this->kaabar->save($this->_table, $data, ['id' => $id]);
					}
					setSessionAlert('Changes saved successfully', 'success');
					redirect($this->_clspath.$this->_class."/index/$job_id");
				}
			}
			else {
				$data = array(
					'job_id'             => $job_id,
					'container_type_id'  => $this->input->post('container_type_id'),
					'number'             => $this->input->post('number'),
					'seal'               => $this->input->post('seal')
				);
				$id = $this->kaabar->save($this->_table, $data, ['id' => $id]);
			}
			setSessionAlert('Changes saved successfully', 'success');

			redirect($this->_clspath.$this->_class."/edit/$job_id/$id");
		}
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

	function preview($job_id, $pdf = 0) {
		$default_company = $this->session->userdata('default_company');
	    $data['company']    = $this->kaabar->getRow('companies', $default_company['id']);
		$search             = $this->session->userdata($this->_class.'_search');
		$data['rows']       = $this->import->getContainers($job_id, $search = '');
		$data['page']       = 'reports/'.$this->_class.'_import_preview';
		$data['page_title'] = humanize($this->_class . 's');
		$data['filename']   = strtolower((strlen($search) > 0 ? $search . '_' : '') . date('d-m-Y'));
		
		$this->_preview($data, $pdf);
	}
}
