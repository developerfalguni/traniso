<?php

class Line_cfs_payment extends MY_Controller {
	function __construct() {
		parent::__construct();
	
		$this->load->model('import');
	}
	
	function index($job_id = 0) {
		$data['jobs'] = $this->import->getJobsInfo($job_id, true, site_url($this->_clspath));

		if($job_id > 0) {	
			$this->load->helper('datefn');

			$data['job']       = $this->kaabar->getRow('jobs', $job_id);
			$data['gld_date']  = $this->kaabar->getField('vessels', $data['job']['vessel_id'], 'id', 'gld_date');
			$data['free_days'] = $this->kaabar->getField('import_details', $job_id, 'job_id', 'free_days');
			
			// Line Payment
			$query = $this->db->query("SELECT D.*, DATE_FORMAT(gate_out, '%Y-%m-%d') AS gate_out,
				DATE_FORMAT(IF(gatepass_date = '0000-00-00 00:00:00', NOW(), gatepass_date), '%Y-%m-%d') AS gatepass_date, CT.size 
			FROM deliveries_stuffings D INNER JOIN containers C ON D.container_id = C.id
				INNER JOIN container_types CT ON C.container_type_id = CT.id
				INNER JOIN jobs J ON C.job_id = J.id
			WHERE D.job_id = ?", array($job_id));
			$data['line_containers'] = $query->result_array();
			$data['line_rates']      = $this->kaabar->getRows('agent_rates', $data['job']['line_id'], 'agent_id');

			// CFS Payment
			$query = $this->db->query("SELECT D.*, DATE_FORMAT(D.gate_out, '%Y-%m-%d') AS gate_out,
				DATE_FORMAT(IF(gatepass_date = '0000-00-00 00:00:00', NOW(), gatepass_date), '%Y-%m-%d') AS gatepass_date, CT.size 
			FROM (deliveries_stuffings D INNER JOIN containers C ON D.container_id = C.id)
				INNER JOIN container_types CT ON C.container_type_id = CT.id
			WHERE D.job_id = ?", array($job_id));
			$data['cfs_containers'] = $query->result_array();
			$data['cfs_rates']      = $this->kaabar->getRows('agent_rates', $data['job']['cfs_id'], 'agent_id');
		}

		$data['docs_url']   = $this->_docs;
		$data['page_title'] = humanize($this->_class);
		$data['page']       = $this->_clspath.'index';
		$data['job_page']   = $this->_clspath.$this->_class.'_edit';
	
		$this->load->view('index', $data);
	}
}
