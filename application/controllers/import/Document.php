<?php

use mikehaertl\wkhtmlto\Pdf;

class Document extends MY_Controller {
	function __construct() {
		parent::__construct();
	
		$this->load->model('import');
	}
	
	function index($job_id = 0) {
		if (! Auth::hasAccess()) {
			setSessionError('NO_PERMISSION');
			redirect('main');
		}

		if ($job_id <= 0) {
			setSessionError('SELECT_JOB');
			redirect($this->_clspath."jobs");
		}
		
		$data['show_search'] = false;
		
		$data['list']['heading'] = array('ID', 'Date', 'Document No', 'Name');
		$data['list']['class'] = array(
			'id'         => 'ID',
			'doument_no' => 'Date',
			'date'       => 'Date',
			'name'       => 'Text');
		$data['list']['link_col'] = 'id';
		$data['list']['link_url'] = $this->_clspath.$this->_class."/edit/$job_id/";
		
		$data['list']['data'] = $this->import->getDocuments($job_id);
		$data['jobs'] = $this->import->getJobsInfo($job_id, true, site_url($this->_clspath));

		$data['buttons'] = array(anchor($this->_clspath.$this->_class."/edit/$job_id/0", '<i class="fa fa-plus"></i> <u>A</u>dd', 'class="btn btn-success" id="AddNew"'));

		$data['page_title'] = humanize($this->_class);
		$data['hide_title'] = true;
		$data['page']       = $this->_clspath.'index';
		$data['job_page']   = 'list';
		$data['docs_url']   = $this->_docs;
		$this->load->view('index', $data);		
	}

	function edit($job_id, $id) {
		$this->load->library('form_validation');

		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('date', 'Date', 'trim|required|min_length[10]');
		$this->form_validation->set_rules('name', 'Name', 'trim|required');
		$this->form_validation->set_rules('document', 'Document', 'trim');

		$row = $this->kaabar->getRow($this->_table, $id);
		if($row == false) {
			$row = array(
				'id'          => 0,
				'job_id'      => 0,
				'document_no' => '',
				'date'        => date("d-m-Y"),
				'name'        => '',
				'document'    => '',
			);
		}
		
		$data['job_id'] = $job_id;
		$data['id'] = array('id' => $id);
		$data['row'] = $row;

		if ($this->form_validation->run() == false) {
			setSessionError(validation_errors());
			
			$data['javascript'] = array('js/jquery.base64.js');

			$data['jobs'] = $this->import->getjobsInfo($job_id, true, site_url($this->_clspath));

			$default_company  = $this->session->userdata('default_company');
			$data['company']  = $this->kaabar->getRow('companies', $default_company['id']);
			$data['company']['city']  = $this->kaabar->getCity($data['company']['city_id']);
			$data['job']	  = $this->kaabar->getRow('jobs', $job_id);
			$data['job']	  = $this->kaabar->getRow('jobs', $job_id);
			$data['party']	  = $this->kaabar->getRow('parties', $data['job']['party_id']);
			$hss_parties   	  = $this->import->getHighSeas($job_id);
			$data['hss_buyer']	    = array_pop($hss_parties);
			$data['vessel_name'] 	= $this->kaabar->getField('vessels', $data['job']['vessel_id'], 'id', 'CONCAT(prefix, " ", name, " ", voyage_no)');
			$data['indian_port']    = $this->kaabar->getField('indian_ports', $data['job']['indian_port_id']);
			$data['product_name']   = $this->kaabar->getField('products', $data['job']['product_id']);
			$data['package_type']   = $this->kaabar->getField('package_types', $data['job']['package_type_id']);
			$data['shipment_port']  = $this->kaabar->getField('ports', $data['job']['shipment_port_id']);
			$data['origin_country'] = $this->kaabar->getField('countries', $data['job']['origin_country_id']);
			$data['cha_name']       = $this->kaabar->getField('agents', $data['job']['cha_id']);
			$data['shipper_name']   = $this->kaabar->getField('agents', $data['job']['shipper_id']);
			$data['line_name']      = $this->kaabar->getField('agents', $data['job']['line_id']);

			$data['page_title'] = humanize($this->_class);
			$data['hide_title'] = true;
			$data['page']       = $this->_clspath.'index';
			$data['job_page']   = $this->_clspath.$this->_class.'_edit';
			$data['docs_url']   = $this->_docs;
			$this->load->view('index', $data);
		}
		else {
			checkDuplicateFormSubmit($this->_clspath.$this->_class."/edit/$job_id/$id");
			
			if (Auth::hasAccess(($id > 0 ? Auth::UPDATE : Auth::CREATE))) {
				$data = array(
					'job_id'      => $job_id,
					'document_no' => $this->input->post('document_no'),
					'date'        => $this->input->post('date'),
					'name'        => $this->input->post('name'),
					'document'    => base64_decode($this->input->post('document'))
				);
				$id = $this->kaabar->save($this->_table, $data, $row);
				
				setSessionAlert('SAVED', 'success');
			}
			else
				setSessionError('NO_PERMISSION');

			redirect($this->_clspath.$this->_class."/edit/$job_id/$id");
		}
	}

	function delete($job_id, $id) {
		$this->kaabar->delete($this->_table, $id);
		redirect($this->_clspath.$this->_class.'/index/'.$job_id);
	}
	
	function preview($id = 0, $pdf = 1) {
		if (! Auth::hasAccess()) {
			setSessionError('NO_PERMISSION');
			redirect('main');
		}

		if ($id <= 0) {
			$id = $this->input->post('id');
		}

		if ($id == 0) {
			setSessionError('NOTHING_PRINT');
			echo closeWindow();
			return;
		}

		$default_company  = $this->session->userdata('default_company');
		$data['company']  = $this->kaabar->getRow('companies', $default_company['id']);
		$data['document'] = $this->kaabar->getRow($this->_table, $id);
		$bl_no 			  = $this->kaabar->getField('jobs', $data['document']['job_id'], 'id', 'bl_no');

		$data['page_title'] = humanize($this->_class);
		$data['page'] = "reports/document";
		$filename = $default_company['code'] . ' - ' . str_replace('/', '_', $data['document']['name']) . ' - ' . $bl_no;
		if ($pdf) {
			$html = $this->load->view('report', $data, true);
			$pdf = new Pdf(array(
				'no-outline',
				'binary'      => FCPATH.'wkhtmltopdf',
			));
			$pdf->addPage($html);
			$pdf->send("$filename.pdf");
		}
		else {
			$this->load->view('report', $data);
		}
	}
}
