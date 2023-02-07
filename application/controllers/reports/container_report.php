<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use mikehaertl\wkhtmlto\Pdf;

class Container_report extends MY_Controller {
	function __construct() {
		parent::__construct();
	
		$this->_fields = array(
			'container' => 'CNT.number',
			'seal'      => 'CNT.seal_no',
			'party'     => 'P.name',
			'sb_no'     => 'J.sb_no',
			'line'      => 'SL.name',
			'cfs'       => 'CFS.name',
			'product'   => 'PRD.name',
			'pol'       => 'IP.name',
		);

		$this->load->model('export');
	}
	
	function index() {
		

		$search = $this->session->userdata($this->_class.'_search');

		if($this->input->post('search_form')) {
			$search = addslashes($this->input->post('search'));
			$this->session->set_userdata($this->_class.'_search', $search);
		}
		
		if ($search == null) {
			$search = '';
			$this->session->set_userdata($this->_class.'_search', $search);
		}

		$data['search'] = $search;
		$data['parsed_search'] = $this->kaabar->parseSearch($search);
		$data['search_fields'] = $this->_fields;

		$default_company = $this->session->userdata("default_company");

		$data['rows'] = $this->export->getContainerReport($search, $data['parsed_search'], $this->_fields);

		$data['page_title'] = humanize($this->_class);
		$data['page'] = $this->_clspath.$this->_class;
		$data['docs_url'] = $this->_docs;
		$this->load->view('index', $data);
	}

	function preview($pdf = 0) {
		$data['page_title'] = humanize($this->_class);
		$data['rows'] = $this->export->getContainerReport();

		if ($pdf) {
			$filename = $data['page_title'].".pdf";
			$html = $this->load->view($this->_clspath.$this->_class.'_preview', $data, true);
			$pdf = new Pdf(array(
				'no-outline',
				'binary'      => FCPATH.'wkhtmltopdf',
				'orientation' => 'Landscape',
			));
			$pdf->addPage($html);
			$pdf->send("$filename.pdf");
			echo closeWindow();
		}
		else {
			$this->load->view($this->_clspath.$this->_class.'_preview', $data);
		}
	}

	
	function excel() {
		$search = $this->session->userdata($this->_class.'_search');
		$parsed_search = $this->kaabar->parseSearch($search);
		$query = $this->export->getContainerReport($search, $parsed_search, $this->_fields, true);

		$this->load->helper('excel');
		to_excel($query, $this->_class . '_' . date('d-m-Y'));
	}
}
