<?php

use mikehaertl\wkhtmlto\Pdf;

class Account extends MY_Controller {
	var $_company_id;
	var $_fy_year;

	function __construct() {
		parent::__construct();

		$this->_company_id = 0;
		$default_company = $this->session->userdata('default_company');
		if ($default_company != false) {
			$this->_company_id = $default_company['id'];
			$this->_fy_year    = $default_company['financial_year'];
		}
		unset($default_company);
		$this->load->model('report');
	}
	
	function index($ledger_id = null, $company_id = null) {
		if (! Auth::hasAccess()) {
			setSessionError('NO_PERMISSION');
			redirect('main');
		}

		if (! is_null($company_id)) {
			$default_company = $this->session->userdata('default_company');
			$row = $this->kaabar->getRow('companies', $company_id);
			$default_company['id']   = $row['id'];
			$default_company['code'] = $row['code'];
			$this->session->set_userdata("default_company", $default_company);
			$this->accounting->setCompany($default_company['id']);
			$this->_company_id = $default_company['id'];
			$this->_fy_year    = $default_company['financial_year'];
			setSessionAlert('COMPANY_CHANGED', 'alert-info');
		}
		$data['company'] = $this->kaabar->getRow('companies', $this->_company_id);
		$data['years']   = explode('_', $this->_fy_year);

		$from_date = null;
		$to_date   = null;
		$filter_id = null;
		$filter2   = null;
		$child_id  = null;
		$monthly   = null;
		$show_desc = null;
		
		if ($this->input->post('ledger_id')) {
			$from_date = $this->input->post('from_date');
			$to_date   = $this->input->post('to_date');
			$ledger_id = $this->input->post('ledger_id');
			$child_id  = $this->input->post('child_id');
			$filter_id = $this->input->post('filter_id');
			$filter2   = $this->input->post('filter2');
			$monthly   = $this->input->post('monthly');
			$show_desc = $this->input->post('show_desc');
			$this->session->set_userdata($this->_class.'_from_date', $from_date);
			$this->session->set_userdata($this->_class.'_to_date', $to_date);
			$this->session->set_userdata($this->_class.'_ledger_id', $ledger_id);
			$this->session->set_userdata($this->_class.'_filter_id', $filter_id);
			$this->session->set_userdata($this->_class.'_filter2', $filter2);
			$this->session->set_userdata($this->_class.'_child_id', $child_id);
			$this->session->set_userdata($this->_class.'_monthly', $monthly);
			$this->session->set_userdata($this->_class.'_showdesc', $show_desc);
		}
		
		if ($ledger_id == null) {
			$from_date = $this->session->userdata($this->_class.'_from_date');
			$to_date   = $this->session->userdata($this->_class.'_to_date');
			$ledger_id = $this->session->userdata($this->_class.'_ledger_id');
			$filter_id = $this->session->userdata($this->_class.'_filter_id');
			$filter2   = $this->session->userdata($this->_class.'_filter2');
			$child_id  = $this->session->userdata($this->_class.'_child_id');
			$monthly   = $this->session->userdata($this->_class.'_monthly');
			$show_desc = $this->session->userdata($this->_class.'_showdesc');
		}

		$data['from_date'] = $from_date ? $from_date : date('01-04-'.$data['years'][0]);
		$data['to_date']   = $to_date ? $to_date : date('d-m-Y');
		$data['ledger_id'] = $ledger_id ? $ledger_id : 0;
		$data['child_id']  = $child_id ? $child_id : '';
		$data['filter_id'] = $filter_id ? $filter_id : '';
		$data['filter2']   = $filter2 ? $filter2 : '';
		$data['monthly']   = $monthly ? $monthly : 0;
		$data['show_desc'] = $show_desc ? $show_desc : 0;
		$data['ledger']    = $this->kaabar->getRow('ledgers', $data['ledger_id']);
		//$data['child']   = $this->kaabar->getRow('ledgers', $data['child_id']);
		$data['childs']    = $this->_getChilds($data['ledger_id']);
		$filter_id         = $filter_id ? $filter_id : '';

		// Small hack if open from Group register
		$this->session->set_userdata($this->_class.'_from_date', $data['from_date']);
		$this->session->set_userdata($this->_class.'_to_date', $data['to_date']);
		$this->session->set_userdata($this->_class.'_ledger_id', $data['ledger_id']);
		$this->session->set_userdata($this->_class.'_filter_id', $data['filter_id']);
		$this->session->set_userdata($this->_class.'_filter2', $data['filter2']);
		$this->session->set_userdata($this->_class.'_child_id', $data['child_id']);
		$this->session->set_userdata($this->_class.'_monthly', $data['monthly']);
		$this->session->set_userdata($this->_class.'_showdesc', $data['show_desc']);

		if ($data['ledger'])
			$data['to_email']  = $this->kaabar->getField('parties', $data['ledger']['party_id'], 'id', 'email');

		$ledger_id = ($data['child_id'] > 0 ? $data['child_id'] : $data['ledger_id']);
		if ($monthly)
			$data['rows'] = $this->report->getLedgersMonthly($data['from_date'], $data['to_date'], $ledger_id, $filter_id, $filter2);
		else
			$data['rows'] = $this->report->getLedgers($data['from_date'], $data['to_date'], $ledger_id, $filter_id, $filter2, $data['show_desc']);

		$this->load->helper('datefn');
		$data['javascript'] = array('bootstrap-daterangepicker/daterangepicker.js');
		$data['stylesheet'] = array('bootstrap-daterangepicker/daterangepicker.css');


		$data['search_fields'] = $this->_fields;
		if ($data['ledger'])
			$data['page_title'] = $data['ledger']['name'] . '<br />Ledger Account (' . $from_date . ' to ' . $to_date . ')';
		else 
			$data['page_title'] = 'Ledger Account (' . $from_date . ' to ' . $to_date . ')';
		$data['hide_title'] = true;
		$data['page']       = $this->_clspath.$this->_class;
		$data['docs_url']   = $this->_docs;
		$this->load->view('index', $data);
	}

	function ajaxParent() {
		$default_company = $this->session->userdata('default_company');
		$company_id = $default_company['id'];
		if ($this->_is_ajax) {
			$search = strtolower($this->input->get('term'));
			$sql = "SELECT L.id, L.code, L.name, L.group_name
				FROM ledgers L 
				WHERE (company_id = $company_id AND parent_ledger_id = 0) AND
					(L.code LIKE '%$search%' OR L.name LIKE '%$search%')
				ORDER BY L.code";
			$this->kaabar->getJson($sql);
		}
		else {
			echo "Access Denied";
		}
	}

	function ajaxChild($parent_id) {
		$default_company = $this->session->userdata('default_company');
		$company_id = $default_company['id'];
		if ($this->_is_ajax) {
			$search = strtolower($this->input->get('term'));
			$sql = "SELECT L.id, L.code, L.name, L.group_name
				FROM ledgers L 
				WHERE (company_id = $company_id AND parent_ledger_id = $parent_id) AND
					(L.code LIKE '%$search%' OR L.name LIKE '%$search%')
				ORDER BY L.code";
			$this->kaabar->getJson($sql);
		}
		else {
			echo "Access Denied";
		}
	}

	function _getChilds($parent_id = 0) {
		if ($parent_id > 0) {
			$default_company = $this->session->userdata('default_company');
			$sql = "SELECT L.id, L.code, L.name, L.group_name
				FROM ledgers L 
				WHERE (company_id = ? AND parent_ledger_id = ?)
				ORDER BY L.code";
			$query = $this->db->query($sql, array($default_company['id'], $parent_id));
			return $query->result_array();
		}
		return array();
	}

	function preview($pdf = 0, $email = 0) {
		$data['preview'] = true;
		$default_company = $this->session->userdata('default_company');
		$data['company'] = $this->kaabar->getRow('companies', $default_company['id']);
		$from_date       = $this->session->userdata($this->_class.'_from_date');
		$to_date         = $this->session->userdata($this->_class.'_to_date');
		$ledger_id       = $this->session->userdata($this->_class.'_ledger_id');
		$child_id        = $this->session->userdata($this->_class.'_child_id');
		$filter_id       = $this->session->userdata($this->_class.'_filter_id');
		$filter2         = $this->session->userdata($this->_class.'_filter2');
		$monthly         = $this->session->userdata($this->_class.'_monthly');
		$show_desc       = $this->session->userdata($this->_class.'_showdesc');
		$party_name      = $this->kaabar->getField('ledgers', $ledger_id, 'id', 'name');
		$ledger          = $this->kaabar->getRow('ledgers', $ledger_id);
		$to_email        = $this->kaabar->getField('parties', $ledger['party_id'], 'id', 'email');

		$data['monthly']   = $monthly;
		$data['show_desc'] = $show_desc;
		$ledger_id = ($child_id > 0 ? $child_id : $ledger_id);
		if ($monthly)
			$data['rows'] = $this->report->getLedgersMonthly($from_date, $to_date, $ledger_id, $filter_id, $filter2);
		else
			$data['rows'] = $this->report->getLedgers($from_date, $to_date, $ledger_id, $filter_id, $filter2, $show_desc);

		$data['page_title'] = $party_name . '<br />Ledger Account (' . $from_date . ' to ' . $to_date . ')';
		
		if ($pdf) {
			$filename = 'ledger_account';
			$html = $this->load->view($this->_clspath.$this->_class.'_preview', $data, true);
			
			$this->load->helper('email');
			$to      = $this->input->post('to');
			$cc      = $this->input->post('cc');
			$bcc     = $this->input->post('bcc');
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
				$this->email->cc($cc);
				$this->email->bcc($bcc);
				$this->email->subject($subject);
				$this->email->message($message);
				$this->email->attach($file);
				$this->email->send();
				unlink($file);
				setSessionAlert('Email has been sent to &lt;' . $to . '&gt;...', 'success');
				
				//echo $this->email->print_debugger(); exit;

				// If email in Party Master is blank. Save email in $to to Party Master
				if (strlen(trim($to_email)) == 0)
					$this->kaabar->save('parties', array('email' => $to), array('id' => $ledger['party_id']));
				
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
		$from_date = $this->session->userdata($this->_class.'_from_date');
		$to_date   = $this->session->userdata($this->_class.'_to_date');
		$ledger_id = $this->session->userdata($this->_class.'_ledger_id');
		$child_id  = $this->session->userdata($this->_class.'_child_id');
		$filter_id = $this->session->userdata($this->_class.'_filter_id');
		$filter2   = $this->session->userdata($this->_class.'_filter2');
		$party_name= $this->kaabar->getField('ledgers', $ledger_id, 'id', 'name');
		$ledger_id = ($child_id > 0 ? $child_id : $ledger_id);

		$rows = $this->report->getLedgers($from_date, $to_date, $ledger_id, $filter_id, $filter2, false);
		
		ini_set('max_execution_time', '0');
		ini_set('memory_limit', '512M');

		set_include_path(get_include_path() . PATH_SEPARATOR . 'assets/phpexcel');

		include 'PHPExcel/IOFactory.php';

		$filename = $party_name . ".xlsx";
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

		$objPHPExcel->getActiveSheet()->setCellValue('A1', 'Date');
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->setCellValue('B1', 'No');
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->setCellValue('C1', 'Description');
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->setCellValue('D1', 'Chq No Date');
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->setCellValue('E1', 'Debit');
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->setCellValue('F1', 'Credit');
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->setCellValue('G1', 'Balance');
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);

		$objPHPExcel->getActiveSheet()->getStyle('A1:G1')->applyFromArray($styleHeading);

		// Data
		$balance = 0;
		$i = 0;
		foreach ($rows['vouchers'] as $r) {
			if ($i == 0) {
				$balance += round($r['amount'], 0);
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $i+2, 'Opening Balance');
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(6, $i+2, $balance);
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(7, $i+2, $this->accounting->getDrCr($balance));
				if ($balance >= 0) {
					$objPHPExcel->getActiveSheet()->getStyle('G'.($i+2))->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_DARKGREEN);
					$objPHPExcel->getActiveSheet()->getStyle('H'.($i+2))->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_DARKGREEN);
				}
				else {
					$objPHPExcel->getActiveSheet()->getStyle('G'.($i+2))->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);
					$objPHPExcel->getActiveSheet()->getStyle('H'.($i+2))->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);
				}
				$i++;
				continue;
			}

			$amount = ($r['debit'] + $r['credit']);
			$balance += $amount;

			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $i+2, $r['date']);
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $i+2, $r['no'].'-'.$r['id3']);
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $i+2, $this->accounting->getToBy($amount) . ' ' . $r['ledger2']);
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $i+2, ($r['cheque_no_date'] == '0 / 00-00-0000' ? '' : $r['cheque_no_date']));
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $i+2, ($r['debit'] > 0 ? number_format($r['debit'], 2, '.', '') : ''));
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, $i+2, ($r['credit'] < 0 ? number_format(abs($r['credit']), 2, '.', '') : ''));
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(6, $i+2, abs($balance));
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(7, $i+2, $this->accounting->getDrCr($balance));
			if ($balance >= 0) {
				$objPHPExcel->getActiveSheet()->getStyle('G'.($i+2))->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_DARKGREEN);
				$objPHPExcel->getActiveSheet()->getStyle('H'.($i+2))->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_DARKGREEN);
			}
			else {
				$objPHPExcel->getActiveSheet()->getStyle('G'.($i+2))->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);
				$objPHPExcel->getActiveSheet()->getStyle('H'.($i+2))->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);
			}
			$i++;
		}
		$i++;
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $i+2, 'Closing Balance');
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $i+2, '=SUM(E2:E' . ($i+1) . ')');
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, $i+2, '=SUM(F2:F' . ($i+1) . ')');
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(6, $i+2, abs($balance));
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(7, $i+2, $this->accounting->getDrCr($balance));
		if ($balance >= 0) {
			$objPHPExcel->getActiveSheet()->getStyle('G'.($i+2))->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_DARKGREEN);
			$objPHPExcel->getActiveSheet()->getStyle('H'.($i+2))->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_DARKGREEN);
		}
		else {
			$objPHPExcel->getActiveSheet()->getStyle('G'.($i+2))->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);
			$objPHPExcel->getActiveSheet()->getStyle('H'.($i+2))->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);
		}

		$i += 2;
		//$objPHPExcel->getActiveSheet()->getStyle('A1:G1000')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$objPHPExcel->getActiveSheet()->getStyle('A1:H'.$i)->applyFromArray($styleSheet);
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
}
