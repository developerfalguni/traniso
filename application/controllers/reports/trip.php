<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use mikehaertl\wkhtmlto\Pdf;

class Trip extends MY_Controller {
	var $_fields;
	var $_parsed_search;
			
	function __construct() {
		parent::__construct();

		$this->load->model('report');

		$this->_fields = array(
			'ref_no'                => "IF(LENGTH(T.party_reference_no) = 0, 'Pending', T.party_reference_no)",
			'job_no'                => 'J.id2_format',
			'from_location'         => 'FL.name',
			'to_location'           => 'TL.name',
			'party'                 => 'PLE.name',
			'rishi_bill_no'         => 'TTI.rishi_bill_no',
			'rishi_cheque_no'       => 'TTI.cheque_no',
			'transporter'           => 'COALESCE(TLE.name, "Pending")',
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
			'bill_date'             => "DATE_FORMAT(VO.date, '%d-%m-%Y')",
		);
	}
	
	function index($starting_row = 0) {
		$default_company = $this->session->userdata("default_company");
		$data['company'] = $this->kaabar->getRow('companies', $default_company['id']);
		$data['years']   = explode('_', $default_company['financial_year']);

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

		$data['rows'] = $this->report->getTrips($from_date, $to_date, $search, $parsed_search);

		$this->load->helper('datefn');
		$data['javascript'] = ['bootstrap-daterangepicker/daterangepicker.js'];
		$data['stylesheet'] = ['bootstrap-daterangepicker/daterangepicker.css'];
		
		$data['docs_url']   = $this->_docs;
		$data['page_title'] = $this->_class . ' Report';
		$data['page']       = $this->_clspath.$this->_class;
		$this->load->view('index', $data);
	}

	function preview($pdf = 0) {
		$default_company = $this->session->userdata('default_company');
		$data['company'] = $this->kaabar->getRow('companies', $default_company['id']);
		$from_date       = $this->session->userdata($this->_class.'_from_date');
		$to_date         = $this->session->userdata($this->_class.'_to_date');
		$search          = $this->session->userdata($this->_class.'_search');
		$parsed_search   = $this->kaabar->parseSearch($search);
		$data['rows']    = $this->report->getTrips($from_date, $to_date, $search, $parsed_search);

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
		$rows          = $this->report->getTrips($from_date, $to_date, $search, $parsed_search);

		$this->_excel($rows, array('id', 'company_id', 'party_reference_no', 'transporter_ledger_id', 'job_id', 'job_id2', 'container_id', 'container_no2', 'container_id2', 'party_ledger_id', 'from_location_id', 'to_location_id', 'remarks', 'self'));
	}

	function email() {
		$default_company = $this->session->userdata('default_company');
		$data['company'] = $this->kaabar->getRow('companies', $default_company['id']);
		$from_date       = $this->session->userdata($this->_class.'_from_date');
		$to_date         = $this->session->userdata($this->_class.'_to_date');
		$search          = $this->session->userdata($this->_class.'_search');
		$parsed_search   = $this->kaabar->parseSearch($search);
		$data['rows']    = $this->report->getTrips($from_date, $to_date, $search, $parsed_search);

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
		redirect($this->agent->referrer());
	}
}
