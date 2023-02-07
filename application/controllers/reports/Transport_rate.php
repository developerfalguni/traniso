<?php

use mikehaertl\wkhtmlto\Pdf;

class Transport_rate extends MY_Controller {
	var $_fields;
	var $_parsed_search;
			
	function __construct() {
		parent::__construct();

		$this->_fields = array(
			'party'           => 'L.name',
			'from_location'   => 'FL.name',
			'to_location'     => 'TL.name',
			'wef_date'        => "DATE_FORMAT(TR.wef_date, '%d-%m-%Y')",
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

		$data['rows'] = $this->_get($from_date, $to_date, $search, $parsed_search);

		$this->load->helper('datefn');
		$data['javascript'] = array('daterangepicker/daterangepicker.js');
		$data['stylesheet'] = array('daterangepicker/daterangepicker-bs3.css');
		
		$data['docs_url']   = $this->_docs;
		$data['page_title'] = $this->_class . ' Report';
		$data['page']       = $this->_clspath.$this->_class;
		$this->load->view('index', $data);
	}

	function _get($from_date, $to_date, $search, $parsed_search) {
		$default_company = $this->session->userdata("default_company");

		$sql = "SELECT TR.*, DATE_FORMAT(TR.wef_date, '%d-%m-%Y') AS wef_date, L.name AS party_name, FL.name AS from_location, TL.name AS to_location
		FROM transport_rates TR
			INNER JOIN ledgers L ON TR.ledger_id = L.id
			INNER JOIN locations FL ON TR.from_location_id = FL.id
			INNER JOIN locations TL ON TR.to_location_id = TL.id
		WHERE (TR.wef_date >= ? AND TR.wef_date <= ?)";
		$where = ' AND (';
		if (is_array($parsed_search)) {
			foreach($parsed_search as $key => $value)
				if (isset($this->_fields[$key]))
					$where .= $this->_fields[$key] . " LIKE '%$value%' AND ";
			if (strlen($where) > 6)
				$sql .= substr($where, 0, strlen($where) - 5) . ')';
		}
		$sql .= " 
		ORDER BY TR.wef_date DESC, L.name";
		$query = $this->db->query($sql, array(
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
		$rows          = $this->_get($from_date, $to_date, $search, $parsed_search);

		$this->_excel($rows, array('id', 'company_id', 'party_reference_no', 'transporter_ledger_id', 'job_id', 'job_id2', 'container_id', 'container_no2', 'container_id2', 'party_ledger_id', 'from_location_id', 'to_location_id', 'remarks', 'self'));
	}

	function email() {
		$default_company = $this->session->userdata('default_company');
		$data['company'] = $this->kaabar->getRow('companies', $default_company['id']);
		$from_date       = $this->session->userdata($this->_class.'_from_date');
		$to_date         = $this->session->userdata($this->_class.'_to_date');
		$search          = $this->session->userdata($this->_class.'_search');
		$parsed_search   = $this->kaabar->parseSearch($search);
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
