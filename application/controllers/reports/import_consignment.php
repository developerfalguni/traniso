<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use mikehaertl\wkhtmlto\Pdf;

class Import_Consignment extends MY_Controller {
	function __construct() {
		parent::__construct();

		$this->_fields = array(
			'shipment' => 'J.type',
			'job_no'   => 'J.id2_format',
			'party'    => 'P.name',
			'cfs'      => 'CFS.name',
			'vessel'   => "CONCAT(V.prefix, ' ', V.name, ' ', V.voyage_no)",
			'product'  => 'PRD.name',
			'pol'      => 'SP.name',
			'pod'      => 'IP.name',
			'bl'       => 'J.bl_no',
			'be'       => 'J.be_no',
			'line'     => 'SL.name',
			'status'   => 'J.status',
			'website'  => 'WP.name',
		);
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

		$data['rows'] = $this->_get($data['from_date'], $data['to_date'], $data['search'], $parsed_search);

		$default_company = $this->session->userdata("default_company");
		$data['years']   = explode('_', $default_company['financial_year']);

		$this->load->helper('datefn');
		$data['javascript'] = ['bootstrap-daterangepicker/daterangepicker.js'];
		$data['stylesheet'] = ['bootstrap-daterangepicker/daterangepicker.css'];

		$data['page']       = $this->_clspath.$this->_class;
		$data['page_title'] = humanize($this->_class);
		$data['docs_url']   = $this->_docs;
		$this->load->view('index', $data);
	}

	function _get($from_date, $to_date, $search, $parsed_search) {
		$sql = "SELECT J.id, J.id2_format, J.type, 
			REPLACE(REPLACE(REPLACE(ED.shipment_subtype, 'Clearing', 'C'), 'Forwarding', 'F'), 'Transportation', 'T') AS shipment_subtype, 
			P.name AS party_name, CONCAT(V.prefix, ' ', V.name, ' ', V.voyage_no) AS vessel, CFS.name AS cfs_name, SL.name AS line_name,
			PRD.name AS product_name, SP.name AS pol, IP.name AS pod, 
			J.bl_no, DATE_FORMAT(J.bl_date, '%d-%m-%Y') AS bl_date, 
			J.be_no, DATE_FORMAT(J.be_date, '%d-%m-%Y') AS be_date,
			J.container_20, J.container_40, 
			J.net_weight, COALESCE(ICBE.total_duty_amount, 0) AS custom_duty,
			VO.id2_format AS bill_no, DATE_FORMAT(VO.date, '%d-%m-%Y') AS bill_date
		FROM jobs J
			INNER JOIN parties P ON J.party_id = P.id
			LEFT OUTER JOIN indian_ports IP ON J.indian_port_id = IP.id
			LEFT OUTER JOIN ports SP ON J.shipment_port_id = SP.id
			LEFT OUTER JOIN products PRD ON J.product_id = PRD.id
			LEFT OUTER JOIN vessels V ON J.vessel_id = V.id
			LEFT OUTER JOIN agents CFS ON J.cfs_id = CFS.id
			LEFT OUTER JOIN agents SL ON J.line_id = SL.id
			LEFT OUTER JOIN export_details ED ON J.id = ED.job_id
			LEFT OUTER JOIN icegate_be ICBE ON J.id = ICBE.job_id
			LEFT OUTER JOIN parties WP ON J.web_party_id = WP.id
			LEFT OUTER JOIN vouchers VO ON J.id = VO.job_id
		WHERE (J.type = 'Import' AND J.be_date >= ? AND J.be_date <= ?)";
		$where = ' AND (';
		if (is_array($parsed_search)) {
			foreach($parsed_search as $key => $value)
				if (isset($this->_fields[$key]))
					$where .= $this->_fields[$key] . " LIKE '%$value%' AND ";
			if (strlen($where) > 6)
				$sql .= substr($where, 0, strlen($where) - 5) . ') ';
		}
		$sql .= "
		GROUP BY J.id
		ORDER BY J.id2";
		$query = $this->db->query($sql, array(
			convDate($from_date), convDate($to_date), 
		));
		return $query->result_array();
	}

	function preview($pdf = 0, $email = 0) {
		$default_company = $this->session->userdata('default_company');
		$data['company'] = $this->kaabar->getRow('companies', $default_company['id']);
		$from_date       = $this->session->userdata($this->_class.'_from_date');
		$to_date         = $this->session->userdata($this->_class.'_to_date');
		$search          = $this->session->userdata($this->_class.'_search');
		$parsed_search   = $this->kaabar->parseSearch($search);

		if (is_array($parsed_search)) {
			$search = '';
			foreach ($parsed_search as $key => $value) {
				$search .= $key.':'.$value.' ';
			}
			$data['search'] = $search;
		}
		$data['rows']    = $this->_get($from_date, $to_date, $search, $parsed_search);

		$data['page_title'] = humanize($this->_class . ' Report');
		
		if ($pdf) {
			$filename = underscore($data['page_title']);
			$html = $this->load->view($this->_clspath.$this->_class.'_preview', $data, true);
			
			$this->load->helper('email');
			$to      = explode(';', str_replace(' ', '', $this->input->post('to')));
			$cc      = explode(';', str_replace(' ', '', $this->input->post('cc')));
			$bcc     = explode(';', str_replace(' ', '', $this->input->post('bcc')));
			$subject = $this->input->post('subject');
			$message = $this->input->post('message');
			if ($email && count($to) > 0) {
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
				unlink($file);
				setSessionAlert('Email has been sent successfully...', 'success');
				
				//echo $this->email->print_debugger(); exit;
				
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
	
	function excel() {
		$from_date     = $this->session->userdata($this->_class.'_from_date');
		$to_date       = $this->session->userdata($this->_class.'_to_date');
		$search        = $this->session->userdata($this->_class.'_search');
		$parsed_search = $this->kaabar->parseSearch($search);
		$rows          = $this->_get($from_date, $to_date, $search, $parsed_search);

		$this->_excel($rows);
	}



	function summary() {
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

		$data['rows'] = $this->_getSummary($data['from_date'], $data['to_date'], $data['search'], $parsed_search);

		$default_company = $this->session->userdata("default_company");
		$data['years']   = explode('_', $default_company['financial_year']);

		$this->load->helper('datefn');
		$data['javascript'] = ['bootstrap-daterangepicker/daterangepicker.js'];
		$data['stylesheet'] = ['bootstrap-daterangepicker/daterangepicker.css'];

		$data['page']       = $this->_clspath.$this->_class.'_summary';
		$data['page_title'] = humanize($this->_class);
		$data['docs_url']   = $this->_docs;
		$this->load->view('index', $data);
	}

	function _getSummary($from_date, $to_date, $search, $parsed_search) {
		$sql = "SELECT D.id, C.job_id, J.type, J.id2_format, DATE_FORMAT(J.date, '%d-%m-%Y') AS date, 
			P.name AS party_name, CONCAT(V.prefix, ' ', V.name, ' ', V.voyage_no) AS vessel_name,
			PRD.name AS product_name, IP.name AS pod, J.bl_no, J.be_no, 
			IP.name AS port_name, LINE.name AS line_name, CFS.name AS cfs_name,
			COUNT(IF(CT.size = 20, C.id, NULL)) AS container_20, COUNT(IF(CT.size = 40, C.id, NULL)) AS container_40,
			J.net_weight, ROUND(SUM(C.net_weight), 3) AS received_weight, ROUND(J.net_weight - SUM(C.net_weight), 3) AS bl_rcvd_diff,
			ROUND(SUM(D.dispatch_weight), 3) AS dispatch_weight, ROUND(ROUND(SUM(C.net_weight), 3) - ROUND(SUM(D.dispatch_weight), 3), 3) AS rcvd_disp_diff, J.status
		FROM deliveries_stuffings D INNER JOIN jobs J ON D.job_id = J.id
			INNER JOIN parties P ON J.party_id = P.id
			LEFT OUTER JOIN import_details ID ON J.id = ID.job_id
			LEFT OUTER JOIN containers C ON D.container_id = C.id
			LEFT OUTER JOIN container_types CT ON C.container_type_id = CT.id
			LEFT OUTER JOIN indian_ports IP ON J.indian_port_id = IP.id
			LEFT OUTER JOIN ports SP ON J.shipment_port_id = SP.id
			LEFT OUTER JOIN products PRD ON J.product_id = PRD.id
			LEFT OUTER JOIN vessels V ON J.vessel_id = V.id
			LEFT OUTER JOIN agents LINE ON J.line_id = LINE.id
			LEFT OUTER JOIN agents CFS ON J.cfs_id = CFS.id
			LEFT OUTER JOIN parties WP ON J.web_party_id = WP.id
		WHERE J.date >= ? AND J.date <= ? AND J.type = 'Import' ";
		if (is_array($parsed_search)) {
			$where = ' AND ';
			foreach($parsed_search as $key => $value)
				if (isset($this->_fields[$key])) 
					$where .= $this->_fields[$key] . " LIKE '%$value%' AND ";
			if (strlen($where) > 6)
				$sql .= substr($where, 0, strlen($where) - 5);
		}
		$sql .= "
		GROUP BY J.id
		ORDER BY P.name, J.id2";
		$query = $this->db->query($sql, array(
			convDate($from_date), convDate($to_date))
		);
		return $query->result_array();
	}

	function previewSummary($pdf = 0, $email = 0) {
		$default_company = $this->session->userdata('default_company');
		$data['company'] = $this->kaabar->getRow('companies', $default_company['id']);
		$from_date       = $this->session->userdata($this->_class.'_from_date');
		$to_date         = $this->session->userdata($this->_class.'_to_date');
		$search          = $this->session->userdata($this->_class.'_search');
		$parsed_search   = $this->kaabar->parseSearch($search);
		$data['rows']    = $this->_getSummary($from_date, $to_date, $search, $parsed_search);

		$data['page_title'] = humanize($this->_class . ' Report');
		
		if ($pdf) {
			$filename = underscore($data['page_title']);
			$html = $this->load->view($this->_clspath.$this->_class.'_summary_preview', $data, true);
			
			$this->load->helper('email');
			$to      = explode(';', str_replace(' ', '', $this->input->post('to')));
			$cc      = explode(';', str_replace(' ', '', $this->input->post('cc')));
			$bcc     = explode(';', str_replace(' ', '', $this->input->post('bcc')));
			$subject = $this->input->post('subject');
			$message = $this->input->post('message');
			if ($email && count($to) > 0) {
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
				unlink($file);
				setSessionAlert('Email has been sent successfully...', 'success');
				
				//echo $this->email->print_debugger(); exit;
				
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
			$this->load->view($this->_clspath.$this->_class.'_summary_preview', $data);
		}
	}
	
	function excelSummary() {
		$from_date     = $this->session->userdata($this->_class.'_from_date');
		$to_date       = $this->session->userdata($this->_class.'_to_date');
		$search        = $this->session->userdata($this->_class.'_search');
		$parsed_search = $this->kaabar->parseSearch($search);
		$rows          = $this->_getSummary($from_date, $to_date, $search, $parsed_search);

		$this->_excel($rows);
	}
}
