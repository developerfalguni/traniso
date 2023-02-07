<?php

class Job_expense extends MY_Controller {
	function __construct() {
		parent::__construct();
	
		$this->load->model('export');
	}
	
	function index($job_id = 0) {
		$this->edit($job_id);
	}

	function edit($job_id = 0) {
		$job_id = intval($job_id);
		if ($job_id <= 0) {
			setSessionError('SELECT_JOBS');
			redirect($this->_clspath."jobs");
		}

		if ($this->export->jobsExists($job_id) != $job_id) {
			setSessionError('SELECT_JOBS');
			redirect($this->_clspath."jobs");
		}

		$this->load->library('form_validation');
		
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('silica_gel', 'Silica Gel', 'trim');
		
		$row = $this->kaabar->getRow($this->_table, $job_id, 'job_id');
		if($row == false) {
			$row = array(
				'id'                              => 0,
				'job_id'                          => $job_id,
				'silica_gel_vendor'               => '',
				'silica_gel'                      => '',
				'craft_paper_vendor'              => '',
				'craft_paper_sides'               => '',
				'wood_plank_vendor'               => '',
				'wood_plank'                      => 'No',
				'door_net'                        => 'No',
				'lashing'                         => 'No',
				'choking'                         => 'No',
				'third_party_surveyor'            => 'No',
				'special_weightment'              => 'No',
				'empty_lift_on'                   => 'No',
				'fumigation_vendor'               => '',
				'fumigation_dose'                 => '',
				'fumigant'                        => '',
				'fumigation_dose_unit'            => '',
				'rebagging'                       => '',
				'coo_issuing_authority'           => '',
				'eia_certificate'                 => '',
				'eia_type'                        => '',
				'phytosanitary_issuing_authority' => '',
				'non_gmo'                         => 'No',
				'health_certificate'              => 'No',
				'legalization_of_certificates'    => 'No',
			);
		}
		
		$data['id']     = array('id' => $row['id']);
		$data['job_id'] = array('id' => $job_id);
		$data['child_job_id'] = array('id' => 0);
		$data['row']    = $row;
		
		if ($this->form_validation->run() == false) {
			setSessionError(validation_errors());
			
			$data['jobs']           = $this->export->getJobsInfo($job_id, true, site_url($this->_clspath));
			$data['all_vouchers']   = $this->accounting->getJobVouchers($job_id);
			$data['vouchers']       = $this->accounting->getBLVouchers($job_id);
			$data['transportation'] = $this->accounting->getTransportationExpenses($job_id);
			
			$data['focus_id']   = "SilicaGelVendor";
			$data['page_title'] = humanize($this->_class . ' &amp; Certificate');
			$data['hide_title'] = true;
			$data['page']       = $this->_clspath.'index';
			$data['job_page']   = $this->_clspath.$this->_class.'_edit';
			$data['docs_url']   = $this->_docs;
			$this->load->view('index', $data);
		}
		else {
			checkDuplicateFormSubmit($this->_clspath.$this->_class."/edit/$job_id");
					
			if (Auth::hasAccess(Auth::UPDATE)) {
				$data = array(
					'job_id'                => $job_id,
					'silica_gel_vendor'     => $this->input->post('silica_gel_vendor'),
					'silica_gel'            => $this->input->post('silica_gel'),
					'craft_paper_vendor'    => $this->input->post('craft_paper_vendor'),
					'craft_paper_sides'     => ($this->input->post('craft_paper_sides') != false ? implode(',', $this->input->post('craft_paper_sides')) : ''),
					'wood_plank_vendor'     => $this->input->post('wood_plank_vendor'),
					'wood_plank'            => $this->input->post('wood_plank'),
					'door_net'              => $this->input->post('door_net'),
					'lashing'               => $this->input->post('lashing'),
					'choking'               => $this->input->post('choking'),
					'third_party_surveyor'  => $this->input->post('third_party_surveyor'),
					'special_weightment'    => $this->input->post('special_weightment'),
					'empty_lift_on'         => $this->input->post('empty_lift_on'),
					'fumigation_vendor'     => $this->input->post('fumigation_vendor'),
					'fumigation_dose'       => $this->input->post('fumigation_dose'),
					'fumigant'              => $this->input->post('fumigant'),
					'fumigation_dose_unit'  => $this->input->post('fumigation_dose_unit'),
					'rebagging'             => $this->input->post('rebagging'),
					'coo_issuing_authority' => $this->input->post('coo_issuing_authority'),
					'eia_certificate'       => $this->input->post('eia_certificate'),
					'eia_type'              => $this->input->post('eia_type'),
					'phytosanitary_issuing_authority' => $this->input->post('phytosanitary_issuing_authority'),
					'non_gmo'                         => $this->input->post('non_gmo'),
					'health_certificate'              => $this->input->post('health_certificate'),
					'legalization_of_certificates'    => $this->input->post('legalization_of_certificates'),
				);
				$id = $this->kaabar->save($this->_table, $data, $row);
				setSessionAlert('SAVED', 'success');
			}
			else
				setSessionError('NO_PERMISSION');
			redirect($this->_clspath.$this->_class."/edit/$job_id");
		}
	}
}
