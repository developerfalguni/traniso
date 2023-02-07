<?php

class Bl_master extends MY_Controller {
	function __construct() {
		parent::__construct();
		
		$this->_table = 'child_jobs';
		$this->load->model('export');
	}
	
	function index($job_id) {
		$this->edit($job_id);
	}

	function edit($job_id) {
		$data['job_id'] = array('id' => $job_id);
		$data['child_job_id'] = array('id' => 0);
		
		if ($this->input->post('sb_no') == false) {
			setSessionError(validation_errors());
			
			$data['jobs'] = $this->export->getJobsInfo($job_id, true, site_url($this->_clspath));
			$data['rows'] = $this->export->getBLMasters($job_id);
	
			$data['page_title'] = "BL Master";
			$data['page']       = $this->_clspath.'index';
			$data['job_page']   = $this->_clspath.$this->_class;
			$data['hide_title'] = true;
			$data['docs_url']   = $this->_docs;
			$this->load->view('index', $data);
		}
		else {
			checkDuplicateFormSubmit($this->_clspath.$this->_class."/edit/$job_id");
			
			if (Auth::hasAccess(($job_id > 0 ? Auth::UPDATE : Auth::CREATE))) {
				$child_job_ids      = $this->input->post('child_job_id');
				$sb_nos             = $this->input->post('sb_no');
				$sb_dates           = $this->input->post('sb_date');
				$mr_nos             = $this->input->post('mr_no');
				$mr_dates           = $this->input->post('mr_date');
				$bl_nos             = $this->input->post('bl_no');
				$bl_dates           = $this->input->post('bl_date');
				foreach ($sb_nos as $index => $sb_no) {
					$data = array(
						'sb_no'             => $sb_no,
						'sb_date'           => $sb_dates[$index],
						'mr_no'             => $mr_nos[$index],
						'mr_date'           => $mr_dates[$index],
						'bl_no'             => $bl_nos[$index],
						'bl_date'           => $bl_dates[$index],
					);
					$this->kaabar->save($this->_table, $data, array('id' => $index));

					if (strlen(trim($data['bl_no'])) > 0)
						$this->db->update('jobs', array('status' => 'Bills'), array('id' => $job_id));

					$icegate_sb_id = $this->kaabar->getField('icegate_sb', $index, 'child_job_id', 'id');
					if (! $icegate_sb_id)
						$this->db->insert('icegate_sb', array('child_job_id' => $index));
				}
				setSessionAlert('SAVED', 'success');
			}
			else
				setSessionError('NO_PERMISSION');
			redirect($this->_clspath.$this->_class."/edit/$job_id");
		}
	}
}
