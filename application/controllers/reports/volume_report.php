<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use mikehaertl\wkhtmlto\Pdf;

class Volume_report extends MY_Controller {
	function __construct() {
		parent::__construct();
	
		$this->_fields = array(
			'party'    => 'P.name',
			'category' => 'PRD.category',
			'product'  => 'PRD.name',
			'pol'      => 'IP.name',
			'pod'      => "CONCAT(DP.name, ', ', DC.name)",
			'line'     => 'SL.name',
			'stuffing' => 'ED.stuffing_type'
		);

		$this->load->model('export');
	}
	
	function index() {
		$from_date = null;
		$to_date   = null;
		$search    = null;
		
		if ($this->input->post('from_date')) {
			$from_date = $this->input->post('from_date');
			$to_date   = $this->input->post('to_date');
			$search    = $this->input->post('search');
			$this->session->set_userdata($this->_class.'_from_date', $from_date);
			$this->session->set_userdata($this->_class.'_to_date', $to_date);
			$this->session->set_userdata($this->_class.'_search', $search);
		}
		
		if ($from_date == null) {
			$from_date = $this->session->userdata($this->_class.'_from_date');
			$to_date   = $this->session->userdata($this->_class.'_to_date');
			$search    = $this->session->userdata($this->_class.'_search');
		}

		$data['from_date'] = $from_date ? $from_date : date('01-m-Y');
		$data['to_date']   = $to_date ? $to_date : date('d-m-Y');
		$data['search']    = $search ? $search : '';
		$parsed_search     = $this->kaabar->parseSearch($search);
		$data['parsed_search'] = $parsed_search;
		$data['search_fields'] = $this->_fields;

		if (is_array($parsed_search)) {
			$search = '';
			foreach ($parsed_search as $key => $value) {
				$search .= $key.':'.$value.' ';
			}
			$data['search'] = $search;
		}
		
		$data['rows'] = $this->export->getVolumeReport($data['from_date'], $data['to_date'], $search, $data['parsed_search'], $this->_fields);

		$default_company = $this->session->userdata("default_company");
		$data['years']   = explode('_', $default_company['financial_year']);

		$this->load->helper('datefn');
		$data['javascript'] = ['bootstrap-daterangepicker/daterangepicker.js'];
		$data['stylesheet'] = ['bootstrap-daterangepicker/daterangepicker.css'];

		$data['page_title'] = humanize($this->_class);
		$data['page'] = $this->_clspath.$this->_class;
		$data['docs_url'] = $this->_docs;
		$this->load->view('index', $data);
	}

	function preview($pdf = 0) {
		$data['page_title'] = humanize($this->_class);
		$data['rows'] = $this->export->getVolumeReport();

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
		$query = $this->export->getVolumeReport($search, $parsed_search, $this->_fields, true);

		$this->load->helper('excel');
		to_excel($query, $this->_class . '_' . date('d-m-Y'));
	}
}
