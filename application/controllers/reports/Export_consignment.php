<?php

use mikehaertl\wkhtmlto\Pdf;

class Export_consignment extends MY_Controller {
	var $_fields;

	function __construct() {
		parent::__construct();

		$this->_fields = array(
			'subtype' => 'J.sub_type',
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
		$data['javascript'] = array('daterangepicker/daterangepicker.js');
		$data['stylesheet'] = array('daterangepicker/daterangepicker-bs3.css');

		$data['page']       = $this->_clspath.$this->_class;
		$data['page_title'] = humanize($this->_class);
		$data['hide_title'] = true;
		$data['docs_url']   = $this->_docs;
		$this->load->view('index', $data);
	}

	function _get($from_date, $to_date, $search, $parsed_search) {
		$sql = "SELECT J.id AS job_id, REPLACE(REPLACE(REPLACE(J.sub_type, 'Clearing', 'C'), 'Forwarding', 'F'), 'Transportation', 'T') AS sub_type, 
			J.id2_format, P.name AS party_name, CONCAT(V.prefix, ' ', V.name, ' ', V.voyage_no) AS vessel,
			C.name AS cargo_name, IP.name AS pol, DP.name AS pod, 
			CJ.bl_no_date, CJ.sb_date, CJ.sb_no_date,
			SUM(IF(PCT.size = 20, PC.containers, NULL)) AS planned_20, SUM(IF(PCT.size = 20, NULL, PC.containers)) AS planned_40,
			S.container_20, S.container_40,
			CJ.net_weight, COALESCE(CJ.fob_inr, 0) AS fob_inr
		FROM jobs J INNER JOIN parties P ON J.party_id = P.id
			LEFT OUTER JOIN (
				SELECT CJ.job_id, MIN(CJ.sb_date) AS sb_date, ROUND(SUM(CJ.net_weight), 3) AS net_weight,
					GROUP_CONCAT(DISTINCT CONCAT(CJ.bl_no, ' ', DATE_FORMAT(CJ.bl_date, '%d-%m-%Y')) SEPARATOR ', ') AS bl_no_date,
					GROUP_CONCAT(DISTINCT CONCAT(CJ.sb_no, ' ', DATE_FORMAT(CJ.sb_date, '%d-%m-%Y')) SEPARATOR ', ') AS sb_no_date,
					SUM(ISB.fob_inr) AS fob_inr
				FROM child_jobs CJ LEFT OUTER JOIN icegate_sb ISB ON CJ.id = ISB.child_job_id
				WHERE CJ.sb_date >= ? AND CJ.sb_date <= ?
				GROUP BY CJ.job_id
			) CJ ON J.id = CJ.job_id
			LEFT OUTER JOIN (
				SELECT S.job_id, SUM(IF(CT.size = 20, 1, NULL)) AS container_20, SUM(IF(CT.size = 20, NULL, 1)) AS container_40
				FROM deliveries_stuffings S INNER JOIN jobs J ON S.job_id = J.id
					LEFT OUTER JOIN container_types CT ON S.container_type_id = CT.id
				WHERE (J.date >= ? AND J.date <= ?)
				GROUP BY  S.job_id
			) S ON J.id = S.job_id
			LEFT OUTER JOIN indian_ports IP ON J.loading_port_id = IP.id
			LEFT OUTER JOIN ports DP ON J.discharge_port_id = DP.id
			LEFT OUTER JOIN products C ON J.product_id = C.id
			LEFT OUTER JOIN vessels V ON J.vessel_id = V.id
			LEFT OUTER JOIN planned_containers PC ON J.id = PC.job_id
			LEFT OUTER JOIN container_types PCT ON PC.container_type_id = PCT.id
		WHERE J.type = 'Export' AND J.date >= ? AND J.date <= ?";
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
		ORDER BY IF(ISNULL(CJ.sb_date), 1, 0), CJ.sb_date, J.id2 DESC";
		$query = $this->db->query($sql, array(
			convDate($from_date), convDate($to_date),
			convDate($from_date), convDate($to_date),
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

		$filename = $this->_class.".xlsx";
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

		$styleYellow = array(
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'startcolor' => array(
					'argb' => 'FFFFFF88',
				),
			)
		);

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
			$objPHPExcel->getActiveSheet()->setCellValue($col . '1', humanize($h));
			$objPHPExcel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
			$col++;
		}
		$objPHPExcel->getActiveSheet()->getStyle('A1:' . $col . '1')->applyFromArray($styleHeading);
		
		// Data
		$i = 2;
		foreach ($rows as $row) {
			$j = 'A';
			foreach ($row as $f => $v) {
				$objPHPExcel->getActiveSheet()->setCellValue($j++ . $i, html_entity_decode($v));
			}
			$i++;
		}
		$objPHPExcel->getActiveSheet()->getStyle('A1:' . $col . $i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$objPHPExcel->getActiveSheet()->getStyle('A1:' . $col . $i)->applyFromArray($styleSheet);

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
