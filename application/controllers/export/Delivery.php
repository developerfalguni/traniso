<?php

class Delivery extends MY_Controller {
	function __construct() {
		parent::__construct();
		
		$this->_table = "containers";
		$this->load->model('export');
	}
	
	function index($job_id = 0, $child_job_id) {
		if (! Auth::hasAccess()) {
			setSessionError('NO_PERMISSION');
			redirect('main');
		}
	
		$job_id = intval($job_id);
		if ($job_id <= 0 OR $this->export->jobsExists($job_id) == 0) {
			setSessionError('SELECT_JOB');
			redirect($this->_clspath."jobs");
		}
		
		$data['list'] = array(
			'heading' => array('ID', 'Container Type', 'Number', 'Seal', 'Seal Date'),
			'class' => array(
				'id'             => 'ID',
				'container_type' => 'Text',
				'number'         => 'Date',
				'seal'           => 'Text',
				'seal_date'      => 'Text'),
			'link_col' => "id",
			'link_url' => $this->_clspath.$this->_class."/edit/$job_id/$child_job_id/");
		
		$data['list']['data'] = $this->export->getContainerList($child_job_id);
		
		$data['job_id'] = array('id' => $job_id);
		$data['jobs'] = $this->export->getJobsInfo($job_id, true, site_url($this->_clspath));
		
		$data['buttons'] = array(anchor($this->_clspath.$this->_class."/edit/$job_id/$child_job_id/0", '<i class="fa fa-plus"></i> Add New', 'class="btn btn-success"'));
		
		$data['page_title'] = humanize($this->_class);
		$data['hide_title'] = true;
		$data['page']       = $this->_clspath.'index';
		$data['job_page']   = 'list';
		$data['docs_url']   = $this->_docs;
		$this->load->view('index', $data);
	}

	function edit($job_id, $child_job_id, $id = 0) {
		if ($job_id <= 0 OR $this->export->jobsExists($job_id) == 0) {
			setSessionError('SELECT_JOB');
			redirect($this->_clspath."jobs");
		}

		$this->load->library('form_validation');
		
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('number', 'Container No', 'trim|required');
		$this->form_validation->set_rules('seal', 'Seal No', 'trim|required');
		
		$default_company = $this->session->userdata('default_company');

		$row = $this->kaabar->getRow($this->_table, $id);
		if ($row == false) {
			$row = array(
				'id'                => 0,
				'child_job_id'      => $child_job_id,
				'container_type_id' => 0,
				'number'            => '',
				'seal'              => '',
				'seal_date'         => '00-00-0000',
			);
		}
		$data['id'] = array('id' => $id);
		$data['job_id'] = array('id' => $job_id);
		$data['row'] = $row;

		if ($this->form_validation->run() == false) {
			setSessionError(validation_errors());
			
			$data['jobs'] = $this->export->getJobsInfo($job_id, true, site_url($this->_clspath));

			$data['page_title'] = "Container List";
			$data['page']       = $this->_clspath.'index';
			$data['job_page']   = $this->_clspath.$this->_class.'_edit';
			$data['hide_title'] = true;
			$data['docs_url']   = $this->_docs;
			$this->load->view('index', $data);
		}
		else {
//			checkDuplicateFormSubmit($this->_clspath.$this->_class."/edit/$id");
			
			if (Auth::hasAccess(($id > 0 ? Auth::UPDATE : Auth::CREATE))) {
				$data = array(
					'child_job_id'      => $child_job_id,
					'container_type_id' => $this->input->post('container_type_id'),
					'number'            => $this->input->post('number'),
					'seal'              => $this->input->post('seal'),
					'seal_date'         => $this->input->post('seal_date'),
				);
				$id = $this->kaabar->save($this->_table, $data, $row);

				setSessionAlert('SAVED', 'success');
			}
			else
				setSessionError('NO_PERMISSION');
			redirect($this->_clspath.$this->_class."/edit/$job_id/$child_job_id/$id");
		}
	}
}
