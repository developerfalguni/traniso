<?php

use mikehaertl\wkhtmlto\Pdf;

class Group extends MY_Controller {
	function __construct() {
		parent::__construct();
		
		$this->load->model('report');
	}
	
	function index($upto = null, $group_id = null, $company_id = null) {
		if (! Auth::hasAccess()) {
			setSessionError('NO_PERMISSION');
			redirect('main');
		}

		$default_company = $this->session->userdata("default_company");
		$data['default_company'] = $default_company;
		$data['years']   = explode('_', $default_company['financial_year']);

		// $upto      = null;
		// $group_id  = null;
		// $company_id= null;
		$companies = null;
		$group 	   = null;
		
		if($this->input->post('upto')) {
			$upto       = $this->input->post('upto');
			$group_id   = $this->input->post('group_id');
			$company_id = $this->input->post('company_id');
			$companies  = $this->input->post('companies');
			$group      = $this->input->post('group');
			$days       = $this->input->post('days');
			$staff_id   = $this->input->post('staff_id');
			foreach ($companies as $i => $c) {
				if ($c == $company_id)
					unset($companies[$i]);
				
				if (! Auth::hasAccess(Auth::READ, $c))
					unset($companies[$i]);
			}

			$this->session->set_userdata($this->_class.'_upto', $upto);
			$this->session->set_userdata($this->_class.'_group_id', $group_id);
			$this->session->set_userdata($this->_class.'_company_id', $company_id);
			$this->session->set_userdata($this->_class.'_companies', $companies);
			$this->session->set_userdata($this->_class.'_group', $group);
			$this->session->set_userdata($this->_class.'_days', $days);
			$this->session->set_userdata($this->_class.'_staff_id', $staff_id);
		}
		
		if($upto == null) {
			$upto       = $this->session->userdata($this->_class.'_upto');
			$group_id   = $this->session->userdata($this->_class.'_group_id');
			$company_id = $this->session->userdata($this->_class.'_company_id');
			$companies  = $this->session->userdata($this->_class.'_companies');
			$group      = $this->session->userdata($this->_class.'_group');
			$days       = $this->session->userdata($this->_class.'_days');
			$staff_id   = $this->session->userdata($this->_class.'_staff_id');
		}

		if (! Auth::hasAccess(Auth::READ, $company_id))
			$company_id = 0;

		$data['upto']       = ($upto ? $upto : date('d-m-Y'));
		$data['group_id']   = ($group_id ? $group_id : 406);
		$data['company_id'] = ($company_id ? $company_id : 0);
		$data['companies']  = ($companies ? $companies : array());
		$data['group']      = ($group ? $group : '');
		$data['days']       = ($days ? $days : 0);
		$data['staff_id']   = ($staff_id ? $staff_id : 0);
		$data['collection_persons'] = $this->report->getCollectionStaff();

		$data['rows']   = $this->report->getGroupLedgers(convDate($data['upto']), $data['group_id'], 
			$data['company_id'], $data['companies'], $data['group'], $data['staff_id']
		);

		$data['search_fields'] = $this->_fields;
		$data['page_title']    = 'Group Register';
		$data['hide_title']    = true;
		$data['page']          = $this->_clspath.$this->_class;
		$data['docs_url']      = $this->_docs;
		$this->load->view('index', $data);
	}

	function preview($pdf = 0, $email = 0) {
		$default_company = $this->session->userdata('default_company');
		$data['company'] = $this->kaabar->getRow('companies', $default_company['id']);
	
		$upto       = $this->session->userdata($this->_class.'_upto');
		$group_id   = $this->session->userdata($this->_class.'_group_id');
		$company_id = $this->session->userdata($this->_class.'_company_id');
		$companies  = $this->session->userdata($this->_class.'_companies');
		$group      = $this->session->userdata($this->_class.'_group');
		$staff_id   = $this->session->userdata($this->_class.'_staff_id');
		$data['companies']  = ($companies ? $companies : array());
		$data['rows']       = $this->report->getGroupLedgers(convDate($upto), $group_id, $company_id, $data['companies'], $group, $staff_id);
		$data['page_title'] = "Group Ledger Register";

		if ($pdf) {
			$filename = 'group_ledger';
			$html = $this->load->view($this->_clspath.$this->_class.'_preview', $data, true);
			
			$this->load->helper('email');
			$to      = $this->input->post('to');
			$subject = $this->input->post('subject');
			$message = $this->input->post('message');
			if ($to && valid_email($to)) {
				$this->load->helper('file');
				$file = FCPATH."tmp/$filename.pdf";
				$pdf = new Pdf(array(
					'no-outline',
					'binary' => FCPATH.'wkhtmltopdf',
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
				$this->email->subject($subject);
				$this->email->message($message);
				$this->email->attach($file);
				$this->email->send();
				unlink($file);
				setSessionAlert('Email has been sent to &lt;' . $to . '&gt;...', 'success');
				
				//echo $this->email->print_debugger(); exit;
				
				$this->load->library('user_agent');
				redirect($this->agent->referrer());
			}
			elseif ($pdf) {
				$pdf = new Pdf(array(
					'no-outline',
					'binary' => FCPATH.'wkhtmltopdf',
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
		ini_set('max_execution_time', '0');
		ini_set('memory_limit', '512M');

		set_include_path(get_include_path() . PATH_SEPARATOR . 'assets/phpexcel');

		include 'PHPExcel/IOFactory.php';

		$default_company = $this->session->userdata('default_company');
		$company = $this->kaabar->getRow('companies', $default_company['id']);
	
		$upto      = $this->session->userdata($this->_class.'_upto');
		$group_id  = $this->session->userdata($this->_class.'_group_id');
		$company_id= $this->session->userdata($this->_class.'_company_id');
		$companies = $this->session->userdata($this->_class.'_companies');
		$group 	   = $this->session->userdata($this->_class.'_group');
		$staff_id  = $this->session->userdata($this->_class.'_staff_id');
		$companies = ($companies ? $companies : array());
		foreach ($companies as $i => $c) {
			if ($c == $default_company['id'])
				unset($companies[$i]);
		}
		$rows       = $this->report->getGroupLedgers(convDate($upto), $group_id, $company_id, $companies, $group, $staff_id);
		$page_title = "Group Ledger Register";

		$filename    = "group_ledger.xlsx";
		$objPHPExcel = new PHPExcel();

		$styleSheet = array(
			'font' => array(
				'name' => 'Times New Roman',
				'size' => 10
			),
		);

		$styleHeading = array(
			'font' => array(
				'bold' => true,
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
			)
		);

		$objPHPExcel->getActiveSheet()->setCellValue('A1', 'Group Name');
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->setCellValue('B1', 'Code');
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->setCellValue('C1', 'Name');
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
		$col = 'D';

		foreach ($rows['companies'] as $company_id => $code) {
			$objPHPExcel->getActiveSheet()->setCellValue($col.'1', $code);
			$objPHPExcel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
			$col++;
		}
		$objPHPExcel->getActiveSheet()->getStyle('A1:'.$col.'1')->applyFromArray($styleHeading);

		// Data
		$i = 2;
		foreach ($rows['ledgers'] as $group_name => $groups) {
			foreach($groups as $code => $l) {
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $i, $group_name);
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $i, $code);
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $i, $l['name']);
				$c = 3;
				foreach ($rows['companies'] as $company_id => $code) {
					if (isset($l['closing'][$company_id]))
						$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($c++, $i, $l['closing'][$company_id]['closing']);
					else 
						$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($c++, $i, '-');
				}
				$i++;
			}
		}

		$objPHPExcel->getActiveSheet()->getStyle('A1:'.$col.$i)->applyFromArray($styleSheet);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
		//$objWriter->save($filename);

		// redirect output to client browser
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="' . $filename.'"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
	}


	function summary() {
		$to_date   = $this->session->userdata($this->_class.'_upto');
		$years     = explode('_', $this->kaabar->getFinancialYear($to_date));
		$from_date = '01-04-'.$years[0];
		$companies = json_decode($this->input->post('companies'));
		$ledgers   = json_decode($this->input->post('ledgers'));
		$pdf_files = array();

		$config = array(
			'protocol'     => 'smtp',
			'smtp_timeout' => 30,
			'smtp_host'    => Settings::get('smtp_host'),
			'smtp_port'    => Settings::get('smtp_port'),
			'smtp_user'    => Settings::get('smtp_user'),
			'smtp_pass'    => Settings::get('smtp_password'),
			'newline'      => "\r\n",
			'crlf'         => "\r\n"
		);
		$this->load->library('email', $config);
		$this->load->helper(array('file', 'email'));

		$to      = $this->kaabar->checkEmail(explode(';', str_replace(' ', '', $this->input->post('to'))));
		$cc      = $this->kaabar->checkEmail(explode(';', str_replace(' ', '', $this->input->post('cc'))));
		$bcc     = $this->kaabar->checkEmail(explode(';', str_replace(' ', '', $this->input->post('bcc'))));
		$subject = $this->input->post('subject');
		$message = $this->input->post('message');

		$this->email->from(Settings::get('smtp_user'));
		$this->email->to($to);
		$this->email->cc($cc);
		$this->email->bcc($bcc);
		$this->email->subject($subject);
		$this->email->message($message);
		
		if ($to && valid_email($to)) {
			$data['companies'] = $companies;
			$data['ledgers']   = $ledgers;
			$html              = $this->load->view($this->_clspath.'combined_preview', $data, true);
			$pdf_file          = FCPATH."tmp/group_summary.pdf";
			$pdf_files[]       = $pdf_file;
			
			$pdf = new Pdf(array(
				'no-outline',
				'binary' => FCPATH.'wkhtmltopdf',
			));
			$pdf->addPage($html);
			$pdf->saveAs($file);

			$this->email->attach($file);
			$this->email->send();
			//echo $this->email->print_debugger(); exit;
			foreach ($pdf_files as $file) {
				unlink($file);
			}
			setSessionAlert('Email has been sent to &lt;' . $to . '&gt;...', 'success');
		}

		$this->load->library('user_agent');
		redirect($this->agent->referrer());
	}

	function combined() {
		$to_date   = $this->session->userdata($this->_class.'_upto');
		$years     = explode('_', $this->kaabar->getFinancialYear($to_date));
		$from_date = '01-04-'.$years[0];
		$companies = json_decode($this->input->post('companies'));
		$ledgers   = json_decode($this->input->post('ledgers'));
		$pdf_files = array();

		$config = array(
			'protocol'     => 'smtp',
			'smtp_timeout' => 30,
			'smtp_host'    => Settings::get('smtp_host'),
			'smtp_port'    => Settings::get('smtp_port'),
			'smtp_user'    => Settings::get('smtp_user'),
			'smtp_pass'    => Settings::get('smtp_password'),
			'newline'      => "\r\n",
			'crlf'         => "\r\n"
		);
		$this->load->library('email', $config);
		$this->load->helper(array('file', 'email'));

		$to      = $this->kaabar->checkEmail(explode(';', str_replace(' ', '', $this->input->post('to'))));
		$cc      = $this->kaabar->checkEmail(explode(';', str_replace(' ', '', $this->input->post('cc'))));
		$bcc     = $this->kaabar->checkEmail(explode(';', str_replace(' ', '', $this->input->post('bcc'))));
		$subject = $this->input->post('subject');
		$message = $this->input->post('message');

		$this->email->from(Settings::get('smtp_user'));
		$this->email->to($to);
		$this->email->cc($cc);
		$this->email->bcc($bcc);
		$this->email->subject($subject);
		$this->email->message($message);
		
		if ($to && valid_email($to)) {
			$data['companies'] = $companies;
			$data['ledgers']   = $ledgers;
			$html              = $this->load->view($this->_clspath.'combined_preview', $data, true);
			$pdf_file          = FCPATH."tmp/group_summary.pdf";
			$pdf_files[]       = $pdf_file;
			
			$pdf = new Pdf(array(
				'no-outline',
				'binary' => FCPATH.'wkhtmltopdf',
			));
			$pdf->addPage($html);
			$pdf->saveAs($file);

			$this->email->attach($file);

			foreach ($ledgers as $ledger) {
				$data         = array(
					'show_desc'  => false,
					'monthly'    => false,
					'company'    => $this->kaabar->getRow('companies', $ledger->company_id),
					'party_name' => $ledger->name,
					'page_title' => $ledger->name . '<br />Ledger Account (' . $from_date . ' to ' . $to_date . ')',
				);
				$this->accounting->setCompany($ledger->company_id);
				$data['rows'] = $this->report->getLedgers($from_date, $to_date, $ledger->ledger_id, 0, false, false, $ledger->company_id);
				$filename     = $data['company']['code'] . '_' . underscore(str_replace(array('/', '\\', '.', ',', '!', '#', '$', '%', '&', '*'), '', $ledger->name . '_' . $ledger->ledger_id));
				$html         = $this->load->view($this->_clspath.'account_preview', $data, true);
				$file = FCPATH."tmp/$filename.pdf";
				$pdf_files[] = $file;
				
				$pdf = new Pdf(array(
					'no-outline',
					'binary' => FCPATH.'wkhtmltopdf',
				));
				$pdf->addPage($html);
				$pdf->saveAs($file);

				$this->email->attach($file);
			}
			$this->email->send();
			// echo $this->email->print_debugger(); exit;
			foreach ($pdf_files as $file) {
			  	unlink($file);
			}
			setSessionAlert('Email has been sent to &lt;' . $to . '&gt;...', 'success');
		}

		$this->load->library('user_agent');
		redirect($this->agent->referrer());
	}

	function ajaxEmail() {
		if ($this->_is_ajax) {
			$search    = addslashes(strtolower($this->input->post('term')));
			$ledger_id = $this->input->post('ledger_id');
			$sql = "SELECT DISTINCT P.name, P.email
			FROM parties P INNER JOIN ledgers L ON P.id = L.party_id
			WHERE L.id IN (" . (is_array($ledger_id) ? implode(",", $ledger_id) : $ledger_id) . ") 
			UNION
			SELECT PC.person_name AS name, PC.email 
			FROM party_contacts PC INNER JOIN ledgers L ON PC.party_id = L.party_id
			WHERE L.id IN (" . (is_array($ledger_id) ? implode(",", $ledger_id) : $ledger_id) . ") 
			UNION
			SELECT fullname AS name, email FROM users 
			WHERE (fullname LIKE '%$search%' OR email LIKE '%$search%')
			ORDER BY name";
			$this->kaabar->getJson($sql);
		}
		else {
			echo 
			"Access Denied";
		}
	}
}
