<?php

use mikehaertl\wkhtmlto\Pdf;

class Short_shipment extends MY_Controller {
	var $_fields;

	function __construct() {
		parent::__construct();
	
		$this->_fields = array(
			'party'   => 'P.name',
			'invoice' => 'J.invoice_no',
			'pol'     => 'IP.name',
			'pod'     => "CONCAT(DP.name, ', ', DC.name)",
			'line'    => 'SL.name',
			'remarks' => 'ED.remarks',
		);

		$this->load->model('export');
	}
	
	function index() {
		if (! Auth::hasAccess()) {
			setSessionError('NO_PERMISSION');
			redirect('main');
		}

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

		if (is_array($data['parsed_search'])) {
			$search = '';
			foreach ($data['parsed_search'] as $key => $value) {
				$search .= $key.':'.$value.' ';
			}
			$data['search'] = $search;
		}

		$default_company = $this->session->userdata("default_company");

		$data['rows'] = $this->export->getShortShipment($search, $data['parsed_search'], $this->_fields);

		$data['page_title'] = humanize($this->_class);
		$data['hide_title'] = true;
		$data['page'] = $this->_clspath.$this->_class;
		$data['docs_url'] = $this->_docs;
		$this->load->view('index', $data);
	}

	function preview($pdf = 0) {
		$data['page_title'] = humanize($this->_class);
		$data['rows'] = $this->export->getShortShipment();

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
		$query = $this->export->getShortShipment($search, $parsed_search, $this->_fields, true);

		$this->load->helper('excel');
		to_excel($query, $this->_class . '_' . date('d-m-Y'));
	}
}
