<?php

use mikehaertl\wkhtmlto\Pdf;

class Tds_transport extends MY_Controller {
	var $_company_id;
	var $_fy_year;
	var $_fields;

	function __construct() {
		parent::__construct();
	
		$this->_fields = array(
			'party' => 'P.name',
		);

		$this->_company_id = 0;
		$default_company = $this->session->userdata('default_company');
		if ($default_company != false) {
			$this->_company_id = $default_company['id'];
			$this->_fy_year    = $default_company['financial_year'];
		}
		unset($default_company);
	}
	
	function index() {
		if (! Auth::hasAccess()) {
			setSessionError('NO_PERMISSION');
			redirect('main');
		}

		$from_date = null;
		$to_date   = null;
		$search    = null;
		
		if($this->input->post('from_date')) {
			$from_date = $this->input->post('from_date');
			$to_date   = $this->input->post('to_date');
			$this->session->set_userdata($this->_class.'_from_date', $from_date);
			$this->session->set_userdata($this->_class.'_to_date', $to_date);
		}
		
		if($from_date == null) {
			$from_date = $this->session->userdata($this->_class.'_from_date');
			$to_date   = $this->session->userdata($this->_class.'_to_date');
		}

		$data['from_date'] = $from_date ? $from_date : date('01-m-Y');
		$data['to_date']   = $to_date ? $to_date : date('d-m-Y');

		$data['rows'] = $this->_getTDSTransport($data['from_date'], $data['to_date']);

		$default_company = $this->session->userdata("default_company");
		$data['years']   = explode('_', $default_company['financial_year']);

		$this->load->helper('datefn');
		$data['javascript'] = array('daterangepicker/daterangepicker.js');
		$data['stylesheet'] = array('daterangepicker/daterangepicker-bs3.css');

		$data['page_title'] = humanize($this->_class);
		$data['hide_title'] = true;
		$data['page'] = $this->_clspath.$this->_class;
		$data['docs_url'] = $this->_docs;
		$this->load->view('index', $data);
	}

	function _getTDSTransport($from_date, $to_date, $query_only = false) {
		$sql = "SELECT V.id2, CL.name AS party_name, COALESCE(P.address, A.address) AS address, 
			COALESCE(P.contact, A.contact) AS contact, COALESCE(P.pan_no, A.pan_no) AS pan_no, 
			DATE_FORMAT(V.date, '%d-%m-%Y') AS date, SUM(V.amount) AS amount
		FROM (((vouchers V INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id)
			INNER JOIN ledgers CL ON V.cr_ledger_id = CL.id)
			LEFT OUTER JOIN parties P ON CL.party_id = P.id)
			LEFT OUTER JOIN agents A ON CL.agent_id = A.id
		WHERE VB.company_id = ? AND V.date >= ? AND V.date <= ? AND VB.voucher_type_id = 5 AND V.category != 'N/A'
		GROUP BY V.id2
		ORDER BY V.date";
		$query = $this->db->query($sql, array($this->_company_id, convDate($from_date), convDate($to_date)));
		if ($query_only)
			return $query;
		return $query->result_array();
	}

	function preview($pdf = 0) {
		$data['page_title'] = humanize($this->_class);
		$data['rows'] = $this->_getTDSTransport();

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
		$from_date = $this->session->userdata($this->_class.'_from_date');
		$to_date   = $this->session->userdata($this->_class.'_to_date');
		
		$query = $this->_getTDSTransport($from_date, $to_date, true);

		$this->load->helper('excel');
		to_excel($query, $this->_class . '_' . date('d-m-Y'));
	}
}
