<?php

use mikehaertl\wkhtmlto\Pdf;

class Transport_inward extends MY_Controller {
	var $_fields;
	var $_parsed_search;
	var $_company_id;
	var $_fy_year;
			
	function __construct() {
		parent::__construct();

		$this->_fields = array(
			'bill_no'             => 'TI.transporter_bill_no',
			'transporter_bill_no' => 'TI.transporter_bill_no',
			'transporter'         => 'TL.name',
			'cheque_no'           => 'CI.cheque_no',
		);

		$this->_company_id = 0;
		$default_company = $this->session->userdata('default_company');
		if ($default_company != false) {
			$this->_company_id = $default_company['id'];
			$this->_fy_year    = $default_company['financial_year'];
		}
		unset($default_company);
	}
	
	function index($starting_row = 0) {
		if (! Auth::hasAccess()) {
			setSessionError('NO_PERMISSION');
			redirect('main');
		}

		$default_company = $this->session->userdata("default_company");
		$data['company'] = $this->kaabar->getRow('companies', $default_company['id']);
		$data['years']   = explode('_', $default_company['financial_year']);

		$from_date = null;
		$to_date   = null;
		$search    = null;

		if($this->input->post('from_date')) {
			$from_date = $this->input->post('from_date');
			$to_date   = $this->input->post('to_date');
			$search    = $this->input->post('search');
			$this->session->set_userdata($this->_class.'_from_date', $from_date);
			$this->session->set_userdata($this->_class.'_to_date', $to_date);
			$this->session->set_userdata($this->_class.'_search', $search);
		}
		
		if($from_date == null) {
			$from_date = $this->session->userdata($this->_class.'_from_date');
			$to_date   = $this->session->userdata($this->_class.'_to_date');
			$search = $this->session->userdata($this->_class.'_search');
		}
		
		$data['from_date']     = $from_date ? $from_date : date('01-m-Y');
		$data['to_date']       = $to_date ? $to_date : date('d-m-Y');
		$data['search']        = $search;
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

		$data['rows'] = $this->_get($from_date, $to_date, $search, $parsed_search);

		$this->load->helper('datefn');
		$data['javascript'] = array('daterangepicker/daterangepicker.js');
		$data['stylesheet'] = array('daterangepicker/daterangepicker-bs3.css');
		
		$data['page_title'] = $this->_class . ' Report';
		$data['page']       = $this->_clspath.$this->_class;
		$data['docs_url']   = $this->_docs;
		$this->load->view('index', $data);
	}

	function _get($from_date, $to_date, $search, $parsed_search) {
		$result = array();
		$sql = "SELECT CI.id, CI.type, CI.rishi_bill_no, DATE_FORMAT(CI.date, '%d-%m-%Y') AS date, T.cargo_type,
			CI.bill_no, DATE_FORMAT(CI.bill_date, '%d-%m-%Y') AS bill_date, CI.transporter_ledger_id, TL.name AS transporter_name,
			COUNT(DISTINCT T.id) AS trips, CI.cheque_no, DATE_FORMAT(CI.cheque_date, '%d-%m-%Y') AS cheque_date, 
			DATE_FORMAT(CI.processed_date, '%d-%m-%Y') AS processed_date, ROUND(SUM(CID.amount), 2) AS amount
		FROM trip_inwards CI INNER JOIN ledgers TL ON CI.transporter_ledger_id = TL.id
			LEFT OUTER JOIN trip_inward_details CID ON CI.id = CID.trip_inward_id
			LEFT OUTER JOIN trips T ON CID.trip_id = T.id
		WHERE CI.company_id = ? AND CI.type = 'Transporter' AND CI.date >= ? AND CI.date <= ? ";
		$where = ' AND (';
		if (is_array($parsed_search)) {
			foreach($parsed_search as $key => $value)
				if (isset($this->_fields[$key]))
					$where .= $this->_fields[$key] . " LIKE '%$value%' AND ";
			if (strlen($where) > 6)
				$sql .= substr($where, 0, strlen($where) - 5) . ')';
		}
		$sql .= "
		GROUP BY CI.id
		ORDER BY CI.transporter_ledger_id, CI.date";
		$query = $this->db->query($sql, array($this->_company_id, convDate($from_date), convDate($to_date)));
		$rows = $query->result_array();
		$result = array();
		foreach ($rows as $row) {
			$result[$row['transporter_ledger_id']][] = $row;
		}
		return $result;
	}

	function preview($pdf = 0) {
		$default_company       = $this->session->userdata('default_company');
		$data['company']       = $this->kaabar->getRow('companies', $default_company['id']);
		$from_date             = $this->session->userdata($this->_class.'_from_date');
		$to_date               = $this->session->userdata($this->_class.'_to_date');
		$search                = $this->session->userdata($this->_class.'_search');
		$parsed_search         = $this->kaabar->parseSearch($search);
		$data['rows']          = $this->_get($from_date, $to_date, $search, $parsed_search);

		$data['page_title'] = humanize($this->_class . ' Report');	
		
		if ($pdf) {
			$filename = underscore($data['page_title']).date('_d_m_Y');			
			$html = $this->load->view($this->_clspath.$this->_class.'_preview', $data, true);
			$pdf = new Pdf(array(
				'no-outline',
				'binary'      => FCPATH.'wkhtmltopdf',
				'orientation' => 'Landscape',
			));
			$pdf->addPage($html);
			$pdf->send("$filename.pdf");
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

		$sql = "SELECT CI.id, CI.type, CI.rishi_bill_no, DATE_FORMAT(CI.date, '%d-%m-%Y') AS date, T.cargo_type,
			CI.bill_no, DATE_FORMAT(CI.bill_date, '%d-%m-%Y') AS bill_date, CI.transporter_ledger_id, TL.name AS transporter_name,
			COUNT(DISTINCT T.id) AS trips, CI.cheque_no, DATE_FORMAT(CI.cheque_date, '%d-%m-%Y') AS cheque_date, 
			DATE_FORMAT(CI.processed_date, '%d-%m-%Y') AS processed_date, ROUND(SUM(CID.amount), 2) AS amount
		FROM trip_inwards CI INNER JOIN ledgers TL ON CI.transporter_ledger_id = TL.id
			LEFT OUTER JOIN trip_inward_details CID ON CI.id = CID.trip_inward_id
			LEFT OUTER JOIN trips T ON CID.trip_id = T.id
		WHERE CI.company_id = ? AND CI.type = 'Transporter' AND CI.date >= ? AND CI.date <= ? ";
		$where = ' AND (';
		if (is_array($parsed_search)) {
			foreach($parsed_search as $key => $value)
				if (isset($this->_fields[$key]))
					$where .= $this->_fields[$key] . " LIKE '%$value%' AND ";
			if (strlen($where) > 6)
				$sql .= substr($where, 0, strlen($where) - 5) . ')';
		}
		$sql .= "
		GROUP BY CI.id
		ORDER BY CI.transporter_ledger_id, CI.date";
		$query = $this->db->query($sql, array($this->_company_id, convDate($from_date), convDate($to_date)));
		$rows = $query->result_array();

		$this->_excel($rows);
	}

	function email() {
		$default_company = $this->session->userdata('default_company');
		$data['company'] = $this->kaabar->getRow('companies', $default_company['id']);
		$from_date       = $this->session->userdata($this->_class.'_from_date');
		$to_date         = $this->session->userdata($this->_class.'_to_date');
		$search          = $this->session->userdata($this->_class.'_search');
		$data['rows']    = $this->_get($from_date, $to_date, $search, $parsed_search);

		$data['page_title'] = humanize($this->_class . ' Report');	
		
		$html = $this->load->view($this->_clspath.$this->_class.'_preview', $data, true);
		$this->load->helper(array('email'));

		$to      = $this->kaabar->checkEmail(explode(';', str_replace(' ', '', $this->input->post('to'))));
		$cc      = $this->kaabar->checkEmail(explode(';', str_replace(' ', '', $this->input->post('cc'))));
		$bcc     = $this->kaabar->checkEmail(explode(';', str_replace(' ', '', $this->input->post('bcc'))));
		$subject = $this->input->post('subject');
		$message = $this->input->post('message');
		$message .= '<hr />' . $html;

		if (count($to) > 0) {
			$config = array(
				'protocol'     => 'smtp',
				'smtp_timeout' => 30,
				'smtp_host'    => Settings::get('smtp_host'),
				'smtp_port'    => Settings::get('smtp_port'),
				'smtp_user'    => Settings::get('smtp_user'),
				'smtp_pass'    => Settings::get('smtp_password'),
				'newline'      => "\r\n",
				'crlf'         => "\r\n",
				'mailtype'     => "html"
			);

			$this->load->library('email', $config);
			$this->email->from(Settings::get('smtp_user'));
			$this->email->to($to);
			$this->email->cc($cc);
			$this->email->bcc($bcc);
			$this->email->subject($subject);
			$this->email->message($message);
			
			$this->email->send();
			//echo $this->email->print_debugger(); exit;
			setSessionAlert('Email has been sent To: &lt;' . implode(', ', $to) . '&gt;...', 'success');
		}
		$this->load->library('user_agent');
		redirect($this->agent->referrer());
	}
}
