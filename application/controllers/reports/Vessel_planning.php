<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use mikehaertl\wkhtmlto\Pdf;
class Vessel_planning extends MY_Controller {
	var $_fields;
	var $_fields1;

	function __construct() {
		parent::__construct();

		$this->_fields = array(
			'vessel'  => 'CONCAT(V.prefix, " ", V.name, " ", V.voyage_no)',
			'port'    => 'IP.name',
		);

		$this->_fields1 = array(
			'shipper' => 'P.code',
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

		$data['from_date'] = $from_date ? $from_date : date("d-m-Y", strtotime("-1 day"));
		$data['to_date']   = $to_date ? $to_date : date("d-m-Y", strtotime("+5 day"));
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

		$parsed_search1     = $this->kaabar->parseSearch($search);
		$data['parsed_search1'] = $parsed_search1;
		$data['search_fields1'] = $this->_fields1;

		if (is_array($parsed_search1)) {
			$search = '';
			foreach ($parsed_search1 as $key => $value) {
				$search .= $key.':'.$value.' ';
			}
			$data['search'] = $search;
		}

		$data['rows'] = $this->_get($data['from_date'], $data['to_date'], $data['search'], $parsed_search, $parsed_search1);

		$default_company = $this->session->userdata("default_company");
		$data['years']   = explode('_', $default_company['financial_year']);

		$this->load->helper('datefn');
		$data['javascript'] = ['bootstrap-daterangepicker/daterangepicker.js'];
		$data['stylesheet'] = array('daterangepicker/daterangepicker-bs3.css', 'css/print.css');

		$data['page']       = $this->_clspath.$this->_class;
		$data['page_title'] = humanize($this->_class);
		$data['docs_url']   = $this->_docs;
		$this->load->view('index', $data);
	}

	function _get($from_date, $to_date, $search, $parsed_search, $parsed_search1) {
		$result = array();
		$sql = "SELECT V.id, V.terminal_id, T.code AS terminal, S.name AS service_name,
			V.id AS vessel_id, CONCAT(V.prefix, ' ', V.name, ' ', V.voyage_no) AS vessel_name, 
			DATE_FORMAT(V.eta_date, '%W, %D %M %Y') AS eta_date,
			DATE_FORMAT(V.etd_date, '%W, %D %M %Y') AS etd_date,
			DATE_FORMAT(V.sailing_date, '%d-%m-%Y') AS sailing_date,
			DATE_FORMAT(V.doc_cutoff_date, '%d-%m-%Y %H:%i') AS doc_cutoff_date,
			DATE_FORMAT(V.gate_cutoff_date, '%d-%m-%Y %H:%i') AS gate_cutoff_date,
			IP.name AS port_name
		FROM vessels V INNER JOIN indian_ports IP ON V.indian_port_id = IP.id
			LEFT OUTER JOIN services S ON V.service_id = S.id
			LEFT OUTER JOIN terminals T ON V.terminal_id = T.id
		WHERE (V.eta_date >= ? AND V.eta_date <= ?)";
		$where = ' AND (';
		if (is_array($parsed_search)) {
			foreach($parsed_search as $key => $value)
				if (isset($this->_fields[$key]))
					$where .= $this->_fields[$key] . " LIKE '%$value%' AND ";
			if (strlen($where) > 6)
				$sql .= substr($where, 0, strlen($where) - 5) . ') ';
		}
		$sql .= '
		ORDER BY V.eta_date';
		$query  = $this->db->query($sql, array(convDate($from_date), convDate($to_date)));
		$rows = $query->result_array();
		
		if (count($rows) > 0) {
			
			foreach ($rows as $r) {
				$result[$r['id']] = $r;
				$result[$r['id']]['jobs'] = array();
			}
			$vessels = implode(', ', array_keys($result));
			$sql = "SELECT J.id, J.vessel_id, L.code AS line, J.booking_no, P.code AS shipper, 
				PC.p20, PC.p40,
				COUNT(IF(CT.size = 20, S.id, NULL)) AS c20, COUNT(IF(CT.size = 20, NULL, S.id)) AS c40, J.fpod,
				COUNT(IF(S.gate_in = '0000-00-00 00:00:00', NULL, IF(CT.size = 20, 1, NULL))) AS g20, 
				COUNT(IF(S.gate_in = '0000-00-00 00:00:00', NULL, IF(CT.size = 40, 1, NULL))) AS g40,
				COALESCE(C.d20, 0) AS d20, COALESCE(C.d40, 0) AS d40, J.si_submitted,
				COUNT(IF(S.gate_out = '0000-00-00 00:00:00', NULL, IF(CT.size = 20, 1, NULL))) AS v20, 
				COUNT(IF(S.gate_out = '0000-00-00 00:00:00', NULL, IF(CT.size = 40, 1, NULL))) AS v40
			FROM jobs J INNER JOIN parties P ON J.party_id = P.id
				LEFT OUTER JOIN agents L ON J.line_id = L.id
				LEFT OUTER JOIN deliveries_stuffings S ON J.id = S.job_id
				LEFT OUTER JOIN container_types CT ON S.container_type_id = CT.id
				LEFT OUTER JOIN (
					SELECT PC.job_id, SUM(IF(CT.size = 20, PC.containers, 0)) AS p20, SUM(IF(CT.size = 20, 0, PC.containers)) AS p40
					FROM job_containers PC INNER JOIN jobs J ON PC.job_id = J.id
						LEFT OUTER JOIN container_types CT ON PC.container_type_id = CT.id
					WHERE J.vessel_id IN ($vessels)
					GROUP BY J.id
				) AS PC ON J.id = PC.job_id
				LEFT OUTER JOIN (
					SELECT S.job_id, COUNT(DISTINCT IF(CJ.doc_handover = '0000-00-00 00:00:00', NULL, IF(CT.size = 20, S.container_no, NULL))) AS d20, 
						COUNT(DISTINCT IF(CJ.doc_handover = '0000-00-00 00:00:00', NULL, IF(CT.size = 40, S.container_no, NULL))) AS d40
					FROM deliveries_stuffings S INNER JOIN stuffing_invoices SI ON S.id = SI.stuffing_id
						LEFT OUTER JOIN job_invoices EI ON SI.job_invoice_id = EI.id
						LEFT OUTER JOIN child_jobs CJ ON EI.child_job_id = CJ.id
						LEFT OUTER JOIN jobs J ON CJ.job_id = J.id
						LEFT OUTER JOIN container_types CT ON S.container_type_id = CT.id
					WHERE J.vessel_id IN ($vessels)
					GROUP BY S.job_id
				) C ON J.id = C.job_id
			WHERE J.type = 'Export' AND J.vessel_id IN ($vessels)";
			$where = ' AND (';
			if (is_array($parsed_search1)) {
				foreach($parsed_search1 as $key => $value)
					if (isset($this->_fields1[$key]))
						$where .= $this->_fields1[$key] . " LIKE '%$value%' AND ";
				if (strlen($where) > 6)
					$sql .= substr($where, 0, strlen($where) - 5) . ') ';
			}
			$sql .= "GROUP BY J.id
			ORDER BY J.date, J.id";
			$query  = $this->db->query($sql);
			$rows = $query->result_array();

			foreach ($rows as $r) {
				$result[$r['vessel_id']]['jobs'][] = $r;
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
		$data['rows']    = $this->_get($from_date, $to_date, $search, $parsed_search);

		$data['page_title'] = humanize($this->_class);
		
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
		$filename = $this->_class.".xlsx";
		$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
		$sheet       = $spreadsheet->getActiveSheet();
		
		$styleSheet = [
			'font' => [
				'name' => 'Times New Roman',
				'size' => 10
			],
		];

		$styleHeading = [
			'font' => [
				'bold' => true,
			],
			'alignment' => [
				'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
			]
		];

		$styleYellow = [
			'fill' => [
				'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
				'startColor' => [
					'argb' => 'FFFFFF88',
				],
			]
		];

		$from_date     = $this->session->userdata($this->_class.'_from_date');
		$to_date       = $this->session->userdata($this->_class.'_to_date');
		$search        = $this->session->userdata($this->_class.'_search');
		$parsed_search = $this->kaabar->parseSearch($search);

		if (is_array($parsed_search)) {
			$search = '';
			foreach ($parsed_search as $key => $value) {
				$search .= $key.':'.$value.' ';
			}
			$data['search'] = $search;
		}
		$rows = $this->_get($from_date, $to_date, $search, $parsed_search);

		// Header
		$header = array_keys($rows[0]);
		$col = 'A';
		foreach ($header as $j => $h) {
			$sheet->setCellValue($col . '1', humanize($h));
			$sheet->getColumnDimension($col)->setAutoSize(true);
			$col++;
		}
		$sheet->getStyle('A1:' . $col . '1')->applyFromArray($styleHeading);
		
		// Data
		$i = 2;
		foreach ($rows as $row) {
			$j = 'A';
			foreach ($row as $f => $v) {
				$sheet->setCellValue($j++ . $i, html_entity_decode($v));
			}
			$i++;
		}
		$sheet->getStyle('A1:' . $col . $i)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
		$sheet->getStyle('A1:' . $col . $i)->applyFromArray($styleSheet);

		$sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
		$sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

		// redirect output to client browser
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="' . $filename.'"');
		header('Cache-Control: max-age=0');
		$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
		$writer->save('php://output');
	}


	function _getLoadlist($vessel_id) {
		$result = array();
		$sql = "SELECT L.code AS line, J.booking_no, CONCAT(V.prefix, ' ', V.name, ' ', V.voyage_no) AS vessel_name, 		 
			S.container_no, CONCAT(CT.size, 'x', CT.code) AS container_type
		FROM deliveries_stuffings S LEFT OUTER JOIN container_types CT ON S.container_type_id = CT.id
			LEFT OUTER JOIN jobs J ON S.job_id = J.id
			LEFT OUTER JOIN agents L ON J.line_id = L.id
			LEFT OUTER JOIN vessels V ON J.vessel_id = V.id
		WHERE J.vessel_id = ?
		ORDER BY L.code, J.booking_no";
		$query  = $this->db->query($sql, array($vessel_id));
		$rows = $query->result_array();
		$result = array();
		foreach ($rows as $row) {
			$result[$row['vessel_name']][$row['line']][$row['booking_no']][] = array(
				'container_no' => $row['container_no'], 'container_type' => $row['container_type']);
		 };
		 return $result;
	}

	function loadlist($vessel_id) {
		$filename = $this->_class.".xlsx";
		$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
		$sheet       = $spreadsheet->getActiveSheet();
		
		$styleSheet = [
			'font' => [
				'name' => 'Times New Roman',
				'size' => 10
			],
		];

		$styleHeading = [
			'font' => [
				'bold' => true,
			],
			'alignment' => [
				'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
			]
		];

		$styleYellow = [
			'fill' => [
				'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
				'startColor' => [
					'argb' => 'FFFFFF88',
				],
			]
		];

		$rows = $this->_getLoadlist($vessel_id);
		$j = 'A';
		$i = 1;
		foreach ($rows as $vessel_name => $v) {
			foreach ($v as $line_name => $l) {
				$col = $j;
				$sheet->setCellValue($col++.$i, "Vessel Name:");
				$sheet->setCellValue($col.$i++, $vessel_name);
				$col = $j;
				$sheet->setCellValue($col++.$i, "Line Name:");
				$sheet->setCellValue($col.$i++, $line_name);
				foreach ($l as $booking_no => $b) {
					$i++;
					$col = $j;
					$sheet->setCellValue($col++.$i, "Booking No:");
					$sheet->setCellValue($col.$i++, $booking_no);
					foreach ($b as $c) {
						$col = $j;
						$sheet->setCellValue($col++.$i, $c['container_no']);
						$sheet->setCellValue($col.$i++, $c['container_type']);
					}
				}
				$j++;
				$j++;
				$j++;
				$sheet->getStyle('A1:'.$j.$i)->applyFromArray($styleSheet);
				$i = 1;
			}
		}

		// redirect output to client browser
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="' . $filename.'"');
		header('Cache-Control: max-age=0');
		$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
		$writer->save('php://output');
	}
}
