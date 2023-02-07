<?php

use mikehaertl\wkhtmlto\Pdf;

class Auditor extends MY_Controller {
	var $_fields;

	function __construct() {
		parent::__construct();

		$this->_fields = array(
			'status'  => 'J.status',
			'party'   => 'P.name',
			'bl'      => 'CJ.bl_no_date',
			'sb'      => 'CJ.sb_no_date',
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
		$data['javascript'] = array('bootstrap-daterangepicker/daterangepicker.js');
		$data['stylesheet'] = array('bootstrap-daterangepicker/daterangepicker.css');

		$data['page_title'] = humanize($this->_class);
		$data['hide_title'] = true;
		$data['page']       = $this->_clspath.$this->_class;
		$data['docs_url']   = $this->_docs;
		$this->load->view('index', $data);
	}

	function _get($from_date, $to_date, $search, $parsed_search) {
		$sql = "SELECT J.id AS job_id, J.id2_format, J.invoice_no, DATE_FORMAT(J.invoice_date, '%d-%m-%Y') AS invoice_date, 
			P.name AS party_name, L.name AS line, J.bl_no, DATE_FORMAT(J.bl_date, '%d-%m-%Y') AS bl_date, IP.name AS pol, SP.name AS pos, 
			COUNT(IF(CT.size=20, C.id, NULL)) AS container_20, COUNT(IF(CT.size=20, NULL, C.id)) AS container_40, J.status, J.type
		FROM jobs J INNER JOIN parties P ON J.party_id = P.id
			LEFT OUTER JOIN containers C ON J.id = C.job_id
			LEFT OUTER JOIN container_types CT ON C.container_type_id = CT.id
			LEFT OUTER JOIN agents L ON J.line_id = L.id
			LEFT OUTER JOIN indian_ports IP ON J.indian_port_id = IP.id
			LEFT OUTER JOIN ports SP ON J.shipment_port_id = SP.id
			LEFT OUTER JOIN vessels V ON J.vessel_id = V.id
		WHERE J.date >= ? AND J.date <= ?";
		$where = ' AND (';
		if (is_array($parsed_search)) {
			foreach($parsed_search as $key => $value)
				if (isset($this->_fields[$key]))
					$where .= $this->_fields[$key] . " LIKE '%$value%' AND ";
			if (strlen($where) > 6)
				$sql .= substr($where, 0, strlen($where) - 5) . ')';
		}
		$sql .= "
		GROUP BY J.id
		ORDER BY J.id2 DESC";
		$query = $this->db->query($sql, array(
			convDate($from_date), convDate($to_date),
		));
		// return $query->result_array();
		$rows = $query->result_array();
		$result = array();
		foreach ($rows as $row) {
			$result[$row['job_id']] = $row;
		}

		if (count($result) > 0) {
			$sql = "SELECT V.id AS exp_id, CONCAT(VT.name,'/edit/',VB.id,'/',V.id) AS url,
				VD.job_id, V.id2_format AS exp_no, 
				DATE_FORMAT(V.date, '%d-%m-%Y') AS exp_date, L.name AS exp_party_name,
				BI.name AS exp_particulars, V.amount AS exp_amount, V.audited
			FROM voucher_details VD 
				INNER JOIN ledgers BI ON VD.bill_item_id = BI.id
				INNER JOIN vouchers V ON VD.voucher_id = V.id
				INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id
				INNER JOIN voucher_types VT ON (VB.voucher_type_id = VT.id AND VT.name = 'Journal')
				INNER JOIN ledgers L ON V.dr_ledger_id = L.id
			WHERE VD.job_id IN (" . implode(',', array_keys($result)) . ")";
			$query = $this->db->query($sql);
			$rows = $query->result_array();
			foreach ($rows as $row) {
				$result[$row['job_id']]['Expenses'][] = $row;
			}

			$sql = "SELECT V.id AS bill_id, CONCAT(VT.name,'/edit/',VB.id,'/',V.id) AS url,
				V.job_id, V.id2_format AS bill_no, 
				DATE_FORMAT(V.date, '%d-%m-%Y') AS bill_date, L.name AS bill_party_name,
				BI.name AS bill_particulars, VD.amount, V.audited
			FROM voucher_details VD 
				INNER JOIN ledgers BI ON VD.bill_item_id = BI.id
				INNER JOIN vouchers V ON VD.voucher_id = V.id
				INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id
				INNER JOIN voucher_types VT ON (VB.voucher_type_id = VT.id AND VT.name IN ('Invoice', 'Debit Note'))
				INNER JOIN ledgers L ON V.dr_ledger_id = L.id
			WHERE V.job_id IN (" . implode(',', array_keys($result)) . ")";
			$query = $this->db->query($sql);
			$rows = $query->result_array();
			foreach ($rows as $row) {
				$result[$row['job_id']]['Bills'][] = $row;
			}
		}

		return $result;
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
				setSessionAlert('Email has been sent to &lt;' . $to . '&gt;...', 'success');
				
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
			$this->load->view($this->_clspath.$this->_class.'_preview', $data);
		}
	}
	
	function excel() {
		ini_set('max_execution_time', '0');
		ini_set('memory_limit', '512M');

		set_include_path(get_include_path() . PATH_SEPARATOR . 'assets/phpexcel');

		include 'PHPExcel/IOFactory.php';

		$filename = $this->_class.date('_d_m_Y').".xlsx";
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

		$from_date     = $this->session->userdata($this->_class.'_from_date');
		$to_date       = $this->session->userdata($this->_class.'_to_date');
		$search        = $this->session->userdata($this->_class.'_search');
		$parsed_search = $this->kaabar->parseSearch($search);

		$rows = $this->_get($from_date, $to_date, $search, $parsed_search);

		// Header
		$objPHPExcel->getActiveSheet()->setCellValue('A1', 'No');
		$objPHPExcel->getActiveSheet()->setCellValue('B1', 'Job No');
		$objPHPExcel->getActiveSheet()->setCellValue('C1', 'Invoice No &amp; Date');
		$objPHPExcel->getActiveSheet()->setCellValue('D1', 'Importer');
		$objPHPExcel->getActiveSheet()->setCellValue('E1', 'Line');
		$objPHPExcel->getActiveSheet()->setCellValue('F1', 'BL No &amp; Date');
		$objPHPExcel->getActiveSheet()->setCellValue('G1', 'POD');
		$objPHPExcel->getActiveSheet()->setCellValue('H1', 'POS');
		$objPHPExcel->getActiveSheet()->setCellValue('I1', 'Containers');
		$objPHPExcel->getActiveSheet()->setCellValue('K1', 'Status');
		$objPHPExcel->getActiveSheet()->setCellValue('L1', 'Expenses');
		$objPHPExcel->getActiveSheet()->setCellValue('S1', 'Income');
		$objPHPExcel->getActiveSheet()->setCellValue('I2', 'C.20');
		$objPHPExcel->getActiveSheet()->setCellValue('J2', 'C.40');
		$objPHPExcel->getActiveSheet()->setCellValue('L2', 'Bill No');
		$objPHPExcel->getActiveSheet()->setCellValue('M2', 'Date');
		$objPHPExcel->getActiveSheet()->setCellValue('N2', 'Party Name');
		$objPHPExcel->getActiveSheet()->setCellValue('O2', 'Bill Item');
		$objPHPExcel->getActiveSheet()->setCellValue('P2', 'Amt');
		$objPHPExcel->getActiveSheet()->setCellValue('Q2', 'S.Tax');
		$objPHPExcel->getActiveSheet()->setCellValue('R2', 'Total');
		$objPHPExcel->getActiveSheet()->setCellValue('S2', 'Bill No');
		$objPHPExcel->getActiveSheet()->setCellValue('T2', 'Date');
		$objPHPExcel->getActiveSheet()->setCellValue('U2', 'Bill Item');
		$objPHPExcel->getActiveSheet()->setCellValue('V2', 'Amt');
		$objPHPExcel->getActiveSheet()->setCellValue('W2', 'S.Tax');
		$objPHPExcel->getActiveSheet()->setCellValue('X2', 'Total');

		$objPHPExcel->getActiveSheet()->getStyle('A1:X2')->applyFromArray($styleHeading);
		// $objPHPExcel->getActiveSheet()->getColumnDimension($j)->setAutoSize(true);
		
		// Data
		$i = 3;
		$srno = 1;
		$total = array(
			'container_20' => 0,
			'container_40' => 0,
			'expense'      => 0,
			'income'       => 0,
		);
		foreach ($rows as $r) {
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $srno++);
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $r['id2_format']);
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$i, $r['invoice_no_date']);
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $r['party_name']);
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $r['line']);
			$objPHPExcel->getActiveSheet()->setCellValue('F'.$i, $r['bl_no_date']);
			$objPHPExcel->getActiveSheet()->setCellValue('G'.$i, $r['pol']);
			$objPHPExcel->getActiveSheet()->setCellValue('H'.$i, $r['pos']);
			$objPHPExcel->getActiveSheet()->setCellValue('I'.$i, $r['container_20']);
			$objPHPExcel->getActiveSheet()->setCellValue('J'.$i, $r['container_40']);
			$objPHPExcel->getActiveSheet()->setCellValue('K'.$i, $r['status']);

			$j = $i;
			$exp_sub_total = array(
				'amount'     => 0,
				'exp_stax'   => 0,
				'exp_amount' => 0,
			);
			if (isset($r['Expenses'])) {
				foreach ($r['Expenses'] as $e) {
					$exp_sub_total['amount']     += $e['amount'];
					$exp_sub_total['exp_stax']   += $e['exp_stax'];
					$exp_sub_total['exp_amount'] += $e['exp_amount'];
					$total['expense']        += $e['exp_amount'];

					$objPHPExcel->getActiveSheet()->setCellValue('L'.$j, $e['exp_no']);
					$objPHPExcel->getActiveSheet()->setCellValue('M'.$j, $e['exp_date']);
					$objPHPExcel->getActiveSheet()->setCellValue('N'.$j, $e['exp_party_name']);
					$objPHPExcel->getActiveSheet()->setCellValue('O'.$j, $e['exp_particulars']);
					$objPHPExcel->getActiveSheet()->setCellValue('P'.$j, $e['amount']);
					$objPHPExcel->getActiveSheet()->setCellValue('Q'.$j, $e['exp_stax']);
					$objPHPExcel->getActiveSheet()->setCellValue('R'.$j, $e['exp_amount']);
					
					if ($e['audited'] == 'Yes') {
						$objPHPExcel->getActiveSheet()->getStyle('L'.$j)->getFont()->getColor()->setARGB('09A016');
						$objPHPExcel->getActiveSheet()->getStyle('M'.$j)->getFont()->getColor()->setARGB('09A016');
						$objPHPExcel->getActiveSheet()->getStyle('N'.$j)->getFont()->getColor()->setARGB('09A016');
						$objPHPExcel->getActiveSheet()->getStyle('O'.$j)->getFont()->getColor()->setARGB('09A016');
						$objPHPExcel->getActiveSheet()->getStyle('P'.$j)->getFont()->getColor()->setARGB('09A016');
						$objPHPExcel->getActiveSheet()->getStyle('Q'.$j)->getFont()->getColor()->setARGB('09A016');
						$objPHPExcel->getActiveSheet()->getStyle('R'.$j)->getFont()->getColor()->setARGB('09A016');
					}
					$j++;
				}
			}
			
			$k = $i;
			$bill_sub_total = array(
				'amount'      => 0,
				'bill_stax'   => 0,
				'bill_amount' => 0,
			);
			if (isset($r['Bills'])) {
				foreach ($r['Bills'] as $i => $b) {
					$bill_sub_total['amount']      += $b['amount'];
					$bill_sub_total['bill_stax']   += $b['bill_stax'];
					$bill_sub_total['bill_amount'] += $b['bill_amount'];
					$total['income']          += $b['bill_amount'];

					$objPHPExcel->getActiveSheet()->setCellValue('S'.$k, $b['bill_no']);
					$objPHPExcel->getActiveSheet()->setCellValue('T'.$k, $b['bill_date']);
					$objPHPExcel->getActiveSheet()->setCellValue('U'.$k, $b['bill_particulars']);
					$objPHPExcel->getActiveSheet()->setCellValue('V'.$k, $b['amount']);
					$objPHPExcel->getActiveSheet()->setCellValue('W'.$k, $b['bill_stax']);
					$objPHPExcel->getActiveSheet()->setCellValue('X'.$k, $b['bill_amount']);

					if ($b['audited'] == 'Yes') {
						$objPHPExcel->getActiveSheet()->getStyle('S'.$k)->getFont()->getColor()->setARGB('09A016');
						$objPHPExcel->getActiveSheet()->getStyle('T'.$k)->getFont()->getColor()->setARGB('09A016');
						$objPHPExcel->getActiveSheet()->getStyle('U'.$k)->getFont()->getColor()->setARGB('09A016');
						$objPHPExcel->getActiveSheet()->getStyle('V'.$k)->getFont()->getColor()->setARGB('09A016');
						$objPHPExcel->getActiveSheet()->getStyle('W'.$k)->getFont()->getColor()->setARGB('09A016');
						$objPHPExcel->getActiveSheet()->getStyle('X'.$k)->getFont()->getColor()->setARGB('09A016');
					}
					$k++;
				}
			}
			
			$i = max($j, $k);

			$objPHPExcel->getActiveSheet()->setCellValue('P'.$i, $exp_sub_total['amount']);
			$objPHPExcel->getActiveSheet()->setCellValue('Q'.$i, $exp_sub_total['exp_stax']);
			$objPHPExcel->getActiveSheet()->setCellValue('R'.$i, $exp_sub_total['exp_amount']);
			
			$objPHPExcel->getActiveSheet()->setCellValue('V'.$i, $bill_sub_total['amount']);
			$objPHPExcel->getActiveSheet()->setCellValue('W'.$i, $bill_sub_total['bill_stax']);
			$objPHPExcel->getActiveSheet()->setCellValue('X'.$i, $bill_sub_total['bill_amount']);

			$objPHPExcel->getActiveSheet()->setCellValue('Y'.$i, "=V$i-P$i");
			$objPHPExcel->getActiveSheet()->setCellValue('Z'.$i, "=Y$i/V$i");

			$objPHPExcel->getActiveSheet()->getStyle("K$i:Z$i")->applyFromArray($styleHeading);

			$i++;
		}
		$objPHPExcel->getActiveSheet()->setCellValue('I'.$i, $total['container_20']);
		$objPHPExcel->getActiveSheet()->setCellValue('J'.$i, $total['container_40']);
		$objPHPExcel->getActiveSheet()->setCellValue('R'.$i, $total['expense']);
		$objPHPExcel->getActiveSheet()->setCellValue('V'.$i, $total['income']);

		$objPHPExcel->getActiveSheet()->getStyle('A1:Z'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$objPHPExcel->getActiveSheet()->getStyle('A1:Z'.$i)->applyFromArray($styleSheet);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");

		// redirect output to client browser
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="' . $filename.'"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
	}
}
