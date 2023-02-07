<?php

use mikehaertl\wkhtmlto\Pdf;

class Delivery extends MY_Controller {
	var $_fields;

	function __construct() {
		parent::__construct();

		$this->_fields = array(
			'party'     => 'P.name',
			'container' => "COALESCE(C.number, 'LOOSE')",
			'bl_no'     => 'J.bl_no',
			'be_no'     => 'J.be_no',
			'vehicle'   => 'D.vehicle_no',
		);
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
			$search    = $this->input->post('search');
			$this->session->set_userdata($this->_class.'_from_date', $from_date);
			$this->session->set_userdata($this->_class.'_to_date', $to_date);
			$this->session->set_userdata($this->_class.'_search', $search);
		}
		
		if($from_date == null) {
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
		$data['javascript'] = array('daterangepicker/daterangepicker.js');
		$data['stylesheet'] = array('daterangepicker/daterangepicker-bs3.css');

		$data['page']       = $this->_clspath.$this->_class;
		$data['page_title'] = humanize($this->_class);
		$data['hide_title'] = true;
		$data['docs_url']   = $this->_docs;
		$this->load->view('index', $data);
	}

	function _get($from_date, $to_date, $search, $parsed_search) {
		$sql = "SELECT D.id, C.job_id, J.id2_format, DATE_FORMAT(D.gatepass_date, '%d-%m-%Y') AS gatepass_date, 
			P.name AS party_name, COALESCE(C.number, 'LOOSE') AS number, CT.size, J.bl_no, J.be_no, 
			D.vehicle_no, D.dispatch_weight
		FROM deliveries_stuffings D INNER JOIN jobs J ON D.job_id = J.id
			INNER JOIN parties P ON J.party_id = P.id
			LEFT OUTER JOIN containers C ON D.container_id = C.id
			LEFT OUTER JOIN container_types CT ON C.container_type_id = CT.id
			LEFT OUTER JOIN container_trackings CTR ON C.id = CTR.container_id
		WHERE D.gatepass_date >= ? AND D.gatepass_date <= ? AND J.type = 'Import' AND D.dispatch_weight > 0 ";
		if (is_array($parsed_search)) {
			$where = ' AND ';
			foreach($parsed_search as $key => $value)
				if (isset($this->_fields[$key])) 
					$where .= $this->_fields[$key] . " LIKE '%$value%' AND ";
			if (strlen($where) > 6)
				$sql .= substr($where, 0, strlen($where) - 5);
		}
		$sql .= "
		ORDER BY P.name, D.gatepass_date DESC";
		$query = $this->db->query($sql, array(convDate($from_date), convDate($to_date)));
		return $query->result_array();
	}

	function preview($pdf = 0, $email = 0) {
		$default_company = $this->session->userdata('default_company');
		$data['company'] = $this->kaabar->getRow('companies', $default_company['id']);
		$from_date       = $this->session->userdata($this->_class.'_from_date');
		$to_date         = $this->session->userdata($this->_class.'_to_date');
		$search          = $this->session->userdata($this->_class.'_search');
		$parsed_search   = $this->kaabar->parseSearch($search);
		$data['rows']    = $this->_get($from_date, $to_date, $search, $parsed_search);

		$data['page']       = $this->_clspath.$this->_class.'_list_preview';
		$data['filename']   = underscore($data['page_title']).date('_d_m_Y');

		if ($pdf) {
			$filename = underscore($data['page_title']);
			$html = $this->load->view($data['page'], $data, true);
			
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
				
				$this->load->library('user_agent');
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
			$this->load->view($data['page'], $data);
		}
	}

	function email() {
		$default_company = $this->session->userdata('default_company');
		$data['company'] = $this->kaabar->getRow('companies', $default_company['id']);
		$from_date       = $this->session->userdata($this->_class.'_from_date');
		$to_date         = $this->session->userdata($this->_class.'_to_date');
		$search          = $this->session->userdata($this->_class.'_search');
		$parsed_search   = $this->kaabar->parseSearch($search);
		$data['rows']    = $this->_get($from_date, $to_date, $search, $parsed_search);
		$page            = $this->_clspath.$this->_class.'_list_preview';

		$data['page_title'] = humanize($this->_class . ' Report');
		$html = $this->load->view($page, $data, true);
		$this->load->helper(array('file', 'email'));

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
	
	function excel() {
		$from_date     = $this->session->userdata($this->_class.'_from_date');
		$to_date       = $this->session->userdata($this->_class.'_to_date');
		$search        = $this->session->userdata($this->_class.'_search');
		$parsed_search = $this->kaabar->parseSearch($search);
		$rows          = $this->_get($from_date, $to_date, $search, $parsed_search);

		$this->_excel($rows);
	}
}
