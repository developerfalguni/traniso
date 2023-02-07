<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use mikehaertl\wkhtmlto\Pdf;

class Cargo_arrival extends MY_Controller {
	function __construct() {
		parent::__construct();

		$this->_fields = array(
			'job_no'     => 'J.id2_format',
			'invoice_no' => 'EI.invoice_no',
		);
	}
	
	function index() {
		$date     = null;
		$party_id = null;
		$search   = null;

		if ($this->input->post('date')) {
			$date     = $this->input->post('date');
			$party_id = $this->input->post('party_id');
			$search   = $this->input->post('search');
			$this->session->set_userdata($this->_class.'_date', $date);
			$this->session->set_userdata($this->_class.'_party_id', $party_id);
			$this->session->set_userdata($this->_class.'_search', $search);
		}
		
		if($date == null) {
			$date     = $this->session->userdata($this->_class.'_date');
			$party_id = $this->session->userdata($this->_class.'_party_id');
			$search   = $this->session->userdata($this->_class.'_search');
		}

		$data['date']          = $date ? $date : date('d-m-Y');
		$data['party_id']      = $party_id ? $party_id : 0;
		$data['search']        = $search ? $search : '';
		$parsed_search         = $this->kaabar->parseSearch($search);
		$data['parsed_search'] = $parsed_search;
		$data['search_fields'] = $this->_fields;

		if (is_array($parsed_search)) {
			$search = '';
			foreach ($parsed_search as $key => $value) {
				$search .= $key.':'.$value.' ';
			}
			$data['search'] = $search;
		}
		$data['rows'] = $this->_getCargoArrivals($data['date'], $data['party_id'], $data['search'], $data['parsed_search']);

		$default_company = $this->session->userdata("default_company");
		$data['years']   = explode('_', $default_company['financial_year']);

		$data['page']       = $this->_clspath.$this->_class;
		$data['page_title'] = humanize($this->_class);
		$data['docs_url']   = $this->_docs;
		$this->load->view('index', $data);
	}
	
	function _getCargoArrivals($date, $party_id, $search, $parsed_search) {
		$result  = array();
		$job_ids = array(0);

		$party_id   = ($party_id > 0 ? "AND J.party_id = $party_id " : '');
		$search = ($search > 0 ? "AND EI.id = $search " : '');
		$sql = "SELECT C.job_id, J.id2_format, DATE_FORMAT(C.date, '%d-%m-%Y') AS date, J.party_id, P.name AS party_name, 
			COALESCE(EI.id, 0) AS job_invoice_id, EI.invoice_no, C.vehicle_no, C.units, U.code, ROUND(C.dispatch_weight, 3) AS dispatch_weight, 
			ROUND(C.received_weight, 3) AS received_weight, 
			C.supplier_name, C.supplier_place, C.lr_no, C.transporter, C.remarks
		FROM cargo_arrivals C INNER JOIN jobs J ON C.job_id = J.id
			INNER JOIN parties P ON J.party_id = P.id
			LEFT OUTER JOIN units U ON C.unit_id = U.id
			LEFT OUTER JOIN job_invoices EI ON C.job_invoice_id = EI.id
		WHERE C.date <= ? $party_id ";
		$where = ' AND (';
		if (is_array($parsed_search)) {
			foreach($parsed_search as $key => $value)
				if (isset($this->_fields[$key]))
					$where .= $this->_fields[$key] . " LIKE '%$value%' AND ";
			if (strlen($where) > 6)
				$sql .= substr($where, 0, strlen($where) - 5) . ') ';
		}
		$sql .= "
		ORDER BY J.id";
		$query = $this->db->query($sql, array(convDate($date)));
		$rows = $query->result_array();
		foreach ($rows as $row) {
			$job_ids[$row['job_id']] = 1;
			$result[$row['invoice_no']]['cargo_arrivals'][] = $row;
		}

		$sql = "SELECT S.*, DATE_FORMAT(S.pickup_date, '%d-%m-%Y') AS pickup_date, DATE_FORMAT(S.stuffing_date, '%d-%m-%Y') AS stuffing_date, 
			U.code, J.id2_format, COALESCE(EI.id, 0) AS job_invoice_id, EI.invoice_no 
		FROM deliveries_stuffings S INNER JOIN jobs J ON S.job_id = J.id
			LEFT OUTER JOIN stuffing_invoices SI ON S.id = SI.stuffing_id
			LEFT OUTER JOIN job_invoices EI ON SI.job_invoice_id = EI.id
			LEFT OUTER JOIN units U ON S.unit_id = U.id
		WHERE (S.units > 0 OR S.gross_weight > 0 OR S.nett_weight > 0) AND 
			S.job_id IN (" . implode(',', array_keys($job_ids)) . ") ";
		$where = ' AND (';
		if (is_array($parsed_search)) {
			foreach($parsed_search as $key => $value)
				if (isset($this->_fields[$key]))
					$where .= $this->_fields[$key] . " LIKE '%$value%' AND ";
			if (strlen($where) > 6)
				$sql .= substr($where, 0, strlen($where) - 5) . ') ';
		}
		$query = $this->db->query($sql, array(convDate($date)));
		$rows = $query->result_array();
		foreach ($rows as $row) {
			$result[$row['invoice_no']]['stuffing_details'][] = $row;
		}
		return $result;
	}

	function preview($pdf = 0, $email = 0) {
		$default_company = $this->session->userdata('default_company');
		$data['company'] = $this->kaabar->getRow('companies', $default_company['id']);
		$date            = $this->session->userdata($this->_class.'_date');
		$party_id        = $this->session->userdata($this->_class.'_party_id');
		$search          = $this->session->userdata($this->_class.'_search');
		$parsed_search   = $this->kaabar->parseSearch($search);
		$data['rows']    = $this->_getCargoArrivals($date, $party_id, $search, $parsed_search);

		$data['party']   = $this->kaabar->getRow('parties', $party_id);
		$data['date']    = date('d-m-Y');
		$data['page_title'] = humanize($this->_class);
		
		if ($pdf) {
			$filename = underscore($data['page_title']);
			$html = $this->load->view($this->_clspath.$this->_class.'_preview', $data, true);
			
			$this->load->helper('email');
			$to      = $this->kaabar->checkEmail(explode(';', str_replace(' ', '', $this->input->post('to'))));
			$cc      = $this->kaabar->checkEmail(explode(';', str_replace(' ', '', $this->input->post('cc'))));
			$bcc     = $this->kaabar->checkEmail(explode(';', str_replace(' ', '', $this->input->post('bcc'))));
			$subject = $this->input->post('subject');
			$message = $this->input->post('message');
			if ($email && $to != false) {
				$this->load->helper('file');
				$file   = FCPATH."tmp/$filename.pdf";
				$pdf = new Pdf(array(
					'no-outline',
					'binary'      => FCPATH.'wkhtmltopdf',
					'orientation' => 'Landscape',
				));
				$pdf->addPage($html);
				$pdf->saveAs($file);
				
				$config = array(
					'protocol' => 'smtp',
					'smtp_timeout' => 30,
					'smtp_host' => Settings::get('smtp_host'),
					'smtp_port' => Settings::get('smtp_port'),
					'smtp_user' => Settings::get('smtp_user'),
					'smtp_pass' => Settings::get('smtp_password'),
					'newline'   => "\r\n",
					'crlf'      => "\r\n"
				);
				$this->load->library('email', $config);
				$this->email->from(Settings::get('smtp_user'));
				$this->email->to($to);
				$this->email->cc($cc);
				$this->email->bcc($bcc);
				$this->email->subject($subject);
				$this->email->message($message);
				$this->email->attach($file);
				$this->email->send();
				//echo $this->email->print_debugger(); exit;

				unlink($file);
				setSessionAlert('Email has been sent to &lt;' . $to . '&gt;...', 'success');

				redirect($this->agent->referrer());
			}
			elseif ($pdf) {
				$pdf = new Pdf(array(
					'no-outline',
					'binary'      => FCPATH.'wkhtmltopdf',
					'orientation' => 'Landscape',
				));
				$pdf->addPage($html);
				$pdf->send("$filename.pdf");
			}
		}
		else {
			$this->load->view($this->_clspath.$this->_class.'_preview', $data);
		}
	}
}
