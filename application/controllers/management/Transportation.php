<?php

use mikehaertl\wkhtmlto\Pdf;

class Transportation extends MY_Controller {
	var $_fields;
	var $_parsed_search;
			
	function __construct() {
		parent::__construct();

		$this->_fields = array(
			'job_no'                => 'J.id2_format',
			'from_location'         => 'FL.name',
			'to_location'           => 'TL.name',
			'party'                 => 'PLE.name',
			'rishi_bill_no'         => 'TTI.rishi_bill_no',
			'rishi_cheque_no'       => 'TTI.cheque_no',
			'transporter'           => 'TLE.name',
			'transporter_bill_no'   => 'TTI.bill_no',
			'transporter_bill_date' => 'DATE_FORMAT("%d-%m-%Y", TTI.date)',
			'pump'                  => 'PA.pump_name',
			'registration_no'       => 'T.registration_no',
			'owned'                 => 'IF(ISNULL(V.id), 0, 1)',
			'lr_no'                 => 'T.lr_no',
			'billed'                => 'IF(ISNULL(VO.id), 0, 1)',
			'cheque_no'             => 'TTI.cheque_no',
			't_inward'              => 'IF(ISNULL(TTI.id), 0, 1)',
			'f_inward'              => 'IF(ISNULL(FTI.id), 0, 1)',
		);
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

		$data['rows'] = $this->_getTrips($from_date, $to_date, $search, $parsed_search);

		$this->load->helper('datefn');
		$data['javascript'] = array('daterangepicker/daterangepicker.js');
		$data['stylesheet'] = array('daterangepicker/daterangepicker-bs3.css');
		
		$data['docs_url']   = $this->_docs;
		$data['page_title'] = $this->_class . ' Report';
		$data['page']       = $this->_clspath.$this->_class;
		$this->load->view('index', $data);
	}

	function _getTrips($from_date, $to_date, $search, $parsed_search) {
		$default_company = $this->session->userdata("default_company");

		$sql = "SELECT T.*, J.id2_format AS job_no, FL.name AS from_location, TL.name AS to_location, 
			PLE.name AS party_name, TLE.name AS transporter_name, 
			IF(ISNULL(V.id), 0, 1) AS self, DATE_FORMAT(T.date, '%d-%m-%Y') AS date, 
			TA.self_adv, TA.party_adv, PA.pump_name, PA.amount AS pump_adv,
			ROUND(COALESCE(TA.amount, 0) + COALESCE(PA.amount, 0), 2) AS allowance, 
			GROUP_CONCAT(PY.id2_format) AS payment_no, SUM(PYTRIP.advance) AS cheque_advance,
			ROUND(T.transporter_rate - (COALESCE(TA.amount, 0) + COALESCE(PA.amount, 0) + SUM(COALESCE(PYTRIP.advance, 0)) + COALESCE(TTID.amount, 0)), 2) AS balance, 
			CONCAT(VT.name, '/edit/', VO.voucher_book_id, '/', VO.id) AS url, VO.id2_format AS bill_no,
			COALESCE(TTI.id, 0) AS transporter_inward_id, TTI.rishi_bill_no, TTI.cheque_no, TTI.bill_no AS transporter_bill_no, DATE_FORMAT(TTI.date, '%d-%m-%Y') AS transporter_date,
			COALESCE(FTI.id, 0) AS fuel_inward_id, CI.id2_format AS rishi_payment_no
		FROM trips T 
			INNER JOIN locations FL ON T.from_location_id = FL.id
			INNER JOIN locations TL ON T.to_location_id = TL.id
			LEFT OUTER JOIN jobs J ON T.job_id = J.id
			LEFT OUTER JOIN ledgers PLE ON T.party_ledger_id = PLE.id
			LEFT OUTER JOIN ledgers TLE ON T.transporter_ledger_id = TLE.id
			LEFT OUTER JOIN rishi.equipments V ON (T.registration_no = V.registration_no AND LENGTH(V.registration_no) > 0)
			LEFT OUTER JOIN (
				SELECT TA.trip_id, 
					IF(TA.advance_by = 'Self', ROUND(SUM(TA.amount), 2), 0) AS self_adv, 
					IF(TA.advance_by = 'Party', ROUND(SUM(TA.amount), 2), 0) AS party_adv,
					SUM(TA.amount) AS amount
				FROM trip_advances TA INNER JOIN trips T ON TA.trip_id = T.id
				WHERE T.company_id = ?
				GROUP BY TA.trip_id
			) TA ON T.id = TA.trip_id
			LEFT OUTER JOIN (
				SELECT PA.trip_id, GROUP_CONCAT(DISTINCT A.name) AS pump_name, SUM(PA.amount) AS amount
				FROM pump_advances PA INNER JOIN trips T ON PA.trip_id = T.id
					INNER JOIN agents A ON PA.agent_id = A.id
				WHERE T.company_id = ?
				GROUP BY PA.trip_id
			) PA ON T.id = PA.trip_id
			LEFT OUTER JOIN trip_inward_details TTID ON T.id = TTID.trip_id
			LEFT OUTER JOIN trip_inwards TTI ON (TTI.type = 'Transporter' AND TTID.trip_inward_id = TTI.id)
			LEFT OUTER JOIN trip_inward_details FTID ON T.id = FTID.trip_id
			LEFT OUTER JOIN trip_inwards FTI ON (FTI.type = 'Fuel' AND FTID.trip_inward_id = FTI.id)
			LEFT OUTER JOIN voucher_trips PYTRIP ON (T.id = PYTRIP.trip_id AND PYTRIP.pump_advance_id = 0 AND PYTRIP.advance > 0)
			LEFT OUTER JOIN vouchers PY ON PYTRIP.voucher_id = PY.id
			LEFT OUTER JOIN voucher_books PYVB ON (PY.voucher_book_id = PYVB.id AND PYVB.voucher_type_id = 7)
			LEFT OUTER JOIN voucher_details VD ON T.id = VD.trip_id
			LEFT OUTER JOIN vouchers VO ON VD.voucher_id = VO.id
			LEFT OUTER JOIN voucher_books VB ON VO.voucher_book_id = VB.id
			LEFT OUTER JOIN voucher_types VT ON VB.voucher_type_id = VT.id
			LEFT OUTER JOIN rishi.container_invoice_details CID ON T.id = CID.container_trip_id
			LEFT OUTER JOIN rishi.container_invoices CI ON (CID.container_invoice_id = CI.id)
		WHERE (T.date >= ? AND T.date <= ?)";
		$where = ' AND (';
		if (is_array($parsed_search)) {
			foreach($parsed_search as $key => $value)
				if (isset($this->_fields[$key]))
					if ($key == 'rishi_cheque_no' && $value == 'Paid') {
						$where .= "LENGTH(" . $this->_fields[$key] . ") > 0 AND " . $this->_fields[$key] . " != 'PENDING' AND ";
					}
					elseif ($key == 'rishi_cheque_no' && $value == 'Processed') {
						$where .= $this->_fields[$key] . " = 'PENDING' AND ";
					}
					elseif ($key == 'rishi_cheque_no' && $value == 'Pending') {
						$where .= "LENGTH(" . $this->_fields[$key] . ") = 0 AND ";
					}
					else {
						$where .= $this->_fields[$key] . " LIKE '%$value%' AND ";
					}
			if (strlen($where) > 6)
				$sql .= substr($where, 0, strlen($where) - 5) . ')';
		}
		$sql .= " 
		GROUP BY T.id
		ORDER BY T.date DESC, T.registration_no";
		$query = $this->db->query($sql, array(
			$default_company['id'], $default_company['id'], 
			convDate($from_date), convDate($to_date)
		));
		return $query->result_array();
	}

	function preview($pdf = 0) {
		$default_company       = $this->session->userdata('default_company');
		$data['company']       = $this->kaabar->getRow('companies', $default_company['id']);
		$from_date             = $this->session->userdata($this->_class.'_from_date');
		$to_date               = $this->session->userdata($this->_class.'_to_date');
		$search                = $this->session->userdata($this->_class.'_search');
		$parsed_search         = $this->kaabar->parseSearch($search);
		$data['rows']          = $this->_getTrips($from_date, $to_date, $search, $parsed_search);

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
		$rows          = $this->_getTrips($from_date, $to_date, $search, $parsed_search);

		$this->_excel($rows, array('id', 'company_id', 'party_reference_no', 'transporter_ledger_id', 'job_id', 'job_id2', 'container_id', 'container_no2', 'container_id2', 'party_ledger_id', 'from_location_id', 'to_location_id', 'remarks', 'self'));
	}

	function email() {
		$default_company = $this->session->userdata('default_company');
		$data['company'] = $this->kaabar->getRow('companies', $default_company['id']);
		$from_date       = $this->session->userdata($this->_class.'_from_date');
		$to_date         = $this->session->userdata($this->_class.'_to_date');
		$search          = $this->session->userdata($this->_class.'_search');
		$parsed_search   = $this->kaabar->parseSearch($search);
		$data['rows']    = $this->_getTrips($from_date, $to_date, $search, $parsed_search);

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