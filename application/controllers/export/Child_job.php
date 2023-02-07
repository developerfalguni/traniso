<?php

class Child_Job extends MY_Controller {
	function __construct() {
		parent::__construct();
		
		$this->load->model('export');
	}
	
	function index($job_id = 0, $id) {
		$this->edit($job_id, $id);
	}

	function edit($job_id = 0, $id) {
		
		if ($job_id <= 0 OR $this->export->jobsExists($job_id) == 0) {
			setSessionError('You cannot load this page directly, Select a Job first.');
			redirect($this->_clspath."jobs");
		}

		$this->load->library('form_validation');
		
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('shipment_type', 'Shipment Type', 'trim|required');
		
		$default_company = $this->session->userdata('default_company');

		$row = $this->kaabar->getRow($this->_table, $id);
		if ($row == false) {
			$row = array(
				'id'                => 0,
				'job_id'            => $job_id,
				'vessel_id'         => 0,
				'shipment_type'     => 'FCL',
				'vi_job_no'         => '',
				'stuffing_type'     => 'Factory',
				'shipper_site_id'   => 0,
				'godown_id'         => 0,
				'cfs_id'            => 0,
				'packages'          => '',
				'package_type_id'   => 0,
				'net_weight'        => 0,
				'net_weight_unit'   => '',
				'gross_weight'      => 0,
				'gross_weight_unit' => '',
				'marks'             => '',
				'agent_id'          => 0,
				'status'            => 'Pending',
				'remarks'           => '',
			);
		}
		$data['id']           = array('id' => $id);
		$data['job_id']       = array('id' => $job_id);
		$data['child_job_id'] = array('id' => $id);
		$data['row']          = $row;
		
		$data['shipper_id']    = intval($this->kaabar->getField('jobs', $row['job_id'], 'id', 'shipper_id'));
		$data['vessel_name']   = $this->kaabar->getField('vessels', $row['vessel_id'], 'id', 'CONCAT(prefix, " ", name, " ", voyage_no)');
		$data['shipping_line'] = ($row['agent_id'] > 0) ? $this->kaabar->getRow('agents', $row['agent_id']) : array('id' => 0, 'name' => '');

		if ($this->form_validation->run() == false) {
			setSessionError(validation_errors());
			
			$data['jobs']     = $this->export->getJobsInfo($job_id, true, site_url($this->_clspath));
			$data['invoices'] = $this->export->getInvoices($id);

			$data['page_title'] = "VisualImpex Job";
			$data['hide_title'] = true;
			$data['page']       = $this->_clspath.'index';
			$data['job_page']   = $this->_clspath.$this->_class;
			$data['docs_url']   = $this->_docs;
			$this->load->view('index', $data);
		}
		else {
			checkDuplicateFormSubmit($this->_clspath.$this->_class."/edit/$job_id/$id");
			
			$data = array(
				'job_id'            => $job_id,
				'vessel_id'         => $this->input->post('vessel_id'),
				'vi_job_no'         => $this->input->post('vi_job_no'),
				'stuffing_type'     => 'Factory',
				'shipper_site_id'   => $this->input->post('shipper_site_id'),
				'godown_id'         => $this->input->post('godown_id'),
				'cfs_id'            => $this->input->post('cfs_id'),
				'packages'          => $this->input->post('packages'),
				'package_type_id'   => $this->input->post('package_type_id'),
				'bl_no'             => $this->input->post('bl_no'),
				'bl_date'           => $this->input->post('bl_date'),
				'sb_no'             => $this->input->post('sb_no'),
				'sb_date'           => $this->input->post('sb_date'),
				'net_weight'        => $this->input->post('net_weight'),
				'net_weight_unit'   => $this->input->post('net_weight_unit'),
				'gross_weight'      => $this->input->post('gross_weight'),
				'gross_weight_unit' => $this->input->post('gross_weight_unit'),
				'marks'             => $this->input->post('marks'),
				'agent_id'          => $this->input->post('agent_id'),
				'status'            => $this->input->post('status'),
				'remarks'           => $this->input->post('remarks'),
			);
			$id = $this->kaabar->save($this->_table, $data, ['id' => $id]);

			$icegate_sb_id = $this->kaabar->getField('icegate_sb', $id, 'child_job_id', 'id');
			if (! $icegate_sb_id)
				$this->db->insert('icegate_sb', array('child_job_id' => $id));
			
			setSessionAlert('Changes saved successfully', 'success');

			redirect($this->_clspath.$this->_class."/edit/$job_id/$id");
		}
	}

	function deleteJob($job_id, $id) {
		$can_delete = TRUE;

		$this->db->query("UPDATE deliveries_stuffings S INNER JOIN stuffing_invoices SI ON S.id = SI.stuffing_id
			INNER JOIN job_invoices EI ON SI.job_invoice_id = EI.id SET S.container_id = 0 WHERE EI.child_job_id = ?", $id);
		$this->db->query("DELETE FROM child_jobs WHERE id = ?", array($id));
		$this->db->query("DELETE FROM attached_documents WHERE child_job_id = ?", $id);
		$this->db->query("DELETE FROM containers WHERE child_job_id = ?", $id);
		$this->db->query("DELETE FROM job_invoices WHERE child_job_id = ?", $id);
		$this->db->query("DELETE FROM icegate_sb WHERE child_job_id = ?", $id);

		$this->kaabar->delete($this->_table, $id);
		setSessionAlert('Job Deleted Successfully', 'success');
		redirect($this->_clspath.'jobs/edit/'.$job_id);
		
		redirect($this->agent->referrer());
	}
}
