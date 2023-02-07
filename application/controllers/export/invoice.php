<?php

class Invoice extends MY_Controller {
	function __construct() {
		parent::__construct();
		
		$this->_table = "job_invoices";
		$this->load->model('export');
	}
	
	function index($job_id = 0, $child_job_id = 0) {
		$job_id = intval($job_id);
		if ($job_id <= 0 OR $this->export->jobsExists($job_id) == 0) {
			setSessionError('You cannot load this page directly, Select a Job first.');
			redirect($this->_clspath."jobs");
		}
		
		$data['list'] = array(
			'heading' => array('ID', 'Invoice No' , 'Date', 'Terms', 'Currency', 'Invoice Value'),
			'class' => array(
				'id'            => 'ID',
				'invoice_no'    => 'Text',
				'invoice_date'  => 'Date',
				'toi'           => 'Text',
				'currency'      => 'Text',
				'invoice_value' => 'Number'),
			'link_col' => "id",
			'link_url' => $this->_clspath.$this->_class."/edit/$job_id/$child_job_id/");
		
		$data['list']['data'] = $this->export->getInvoices($child_job_id);
		
		$data['job_id'] = array('id' => $job_id);
		$data['child_job_id'] = array('id' => $child_job_id);
		$data['jobs'] = $this->export->getJobsInfo($job_id, true, site_url($this->_clspath));
		
		$data['buttons'] = array(anchor($this->_clspath.$this->_class."/edit/$job_id/$child_job_id/0", '<i class="fa fa-plus"></i> Add New', 'class="btn btn-success"'));
		
		$data['page_title'] = humanize($this->_class);
		$data['page']       = $this->_clspath.'index';
		$data['job_page']   = 'list';
		$data['docs_url']   = $this->_docs;
		$this->load->view('index', $data);
	}

	function edit($job_id, $child_job_id, $id = 0) {
		if ($job_id <= 0 OR $this->export->jobsExists($job_id) == 0) {
			setSessionError('You cannot load this page directly, Select a Job first.');
			redirect($this->_clspath."jobs");
		}

		$this->load->library('form_validation');
		
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('invoice_no', 'Invoice No', 'trim|required');
		$this->form_validation->set_rules('invoice_date', 'Invoice Date', 'trim|required');
		
		$default_company = $this->session->userdata('default_company');

		$row = $this->kaabar->getRow($this->_table, $id);
		if ($row == false) {
			$row = array(
				'id'            => 0,
				'child_job_id'  => $child_job_id,
				'invoice_no'    => '',
				'invoice_date'  => '00-00-0000',
				'toi'           => 'C&F',
				'currency_id'   => 2,
				'invoice_value' => 0,				
			);
		}
		$data['id'] = array('id' => $id);
		$data['job_id'] = array('id' => $job_id);
		$data['child_job_id'] = array('id' => $child_job_id);
		$data['row'] = $row;

		if ($this->form_validation->run() == false) {
			setSessionError(validation_errors());
			
			$data['jobs'] = $this->export->getJobsInfo($job_id, true, site_url($this->_clspath));
			$data['products'] = $this->kaabar->getRows('export_product_details', $id, 'job_invoice_id', '*', 'sr_no');

			$data['page_title'] = "Export Invoice";
			$data['page']       = $this->_clspath.'index';
			$data['job_page']   = $this->_clspath.$this->_class.'_edit';
			$data['hide_title'] = true;
			$data['docs_url']   = $this->_docs;
			$this->load->view('index', $data);
		}
		else {
			checkDuplicateFormSubmit($this->_clspath.$this->_class."/edit/$job_id/$child_job_id/$id");
			
			$data = array(
				'child_job_id'  => $child_job_id,
				'invoice_no'    => $this->input->post('invoice_no'),
				'invoice_date'  => $this->input->post('invoice_date'),
				'toi'           => $this->input->post('toi'),
				'currency_id'   => $this->input->post('currency_id'),
				'invoice_value' => $this->input->post('invoice_value'),
			);
			$id = $this->kaabar->save($this->_table, $data, ['id' => $id]);
			$this->_updateProducts($id);

			setSessionAlert('Changes saved successfully', 'success');

			redirect($this->_clspath.$this->_class."/edit/$job_id/$child_job_id/$id");
		}
	}

	function _updateProducts($id) {
		$descriptions = $this->input->post("description");
		$delete_ids   = $this->input->post('delete_id') == false? ["0" => "0"] : $this->input->post('delete_id');
		if ($descriptions) {
			$sr_nos         = $this->input->post("sr_no");
			$hs_codes       = $this->input->post("hs_code");
			$quantitys      = $this->input->post("quantity");
			$quantity_units = $this->input->post("quantity_unit");
			foreach ($descriptions as $index => $description) {
				$data = array(
					'sr_no'         => $sr_nos[$index],
					'hs_code'       => $hs_codes[$index],
					'description'   => $description,
					'quantity'      => $quantitys[$index],
					'quantity_unit' => $quantity_units[$index],
				);
				$this->kaabar->save('export_product_details', $data, ['id' => $index]);
			}
		}	

		if ($delete_ids != null) {
			foreach ($delete_ids as $index => $tmp) {
				if ($index > 0) {
					$this->kaabar->delete('export_product_details', ['id' => $index]);
				}
			}
		}

		$descriptions = $this->input->post('new_description');
		if ($descriptions) {
			$sr_nos         = $this->input->post("new_sr_no");
			$hs_codes       = $this->input->post("new_hs_code");
			$quantitys      = $this->input->post("new_quantity");
			$quantity_units = $this->input->post("new_quantity_unit");
			foreach ($descriptions as $index => $description) {
				if (strlen(trim($description)) > 0) {
					$data = array(
						'sr_no'             => $sr_nos[$index],
						'job_invoice_id' => $id,
						'hs_code'           => $hs_codes[$index],
						'description'       => $description,
						'quantity'          => $quantitys[$index],
						'quantity_unit'     => $quantity_units[$index],
					);
					$this->kaabar->save('export_product_details', $data);
				}
			}
		}
	}
}
