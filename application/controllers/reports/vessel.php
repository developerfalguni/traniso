<?php

class Vessel extends MY_Controller {
	var $_company_id;
	var $_fy_year;
	var $_fields, $_ie_fields, $_p_fields;

	function __construct() {
		parent::__construct();

		$this->_company_id = 0;
		$default_company = $this->session->userdata('default_company');
		if ($default_company != false) {
			$this->_company_id = $default_company['id'];
			$this->_fy_year    = $default_company['financial_year'];
		}
		unset($default_company);
		
		$this->_fields = array(
			'party'    => 'P.name',
			'vessel'   => 'CONCAT(V.prefix, " ", V.name, " ", V.voyage_no)',
			'port'     => 'IP.name',
			'cha'      => 'A.name',
			'category' => 'PRD.category',
			'product'  => 'PRD.name',
		);
		$this->_ie_fields = array(
			'type'     => 'J.cargo_type',
			'vessel'   => 'CONCAT(V.prefix, " ", V.name, " ", V.voyage_no)',
			'port'     => 'IP.name',
			'category' => 'PRD.category',
			'product'  => 'PRD.name',
		);
		$this->_p_fields = array(
			'type'   => 'V.type',
			'agent'  => 'A.name',
			'vessel' => 'V.name',
			'voyage' => 'V.voyage_no',
			'port'   => 'IP.name',
			'igmno'  => 'V.igm_no',
			'igm'    => 'V.igm_date',
			'gld'    => 'V.gld_date',
			'eta_date'    => 'V.eta_date',
			'etd_date'    => 'V.etd_date',
			'sail'   => 'V.sailing_date',
			'barge'  => 'V.barging_date',
			'berth'  => 'V.berthing_date',
			'pgr'    => 'V.pgr_begin_date',
		);
		$this->load->model('office');
		$this->load->model('report');
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

		$data['rows'] = $this->_getVessel($data['from_date'], $data['to_date'], $search, $parsed_search);

		$default_company = $this->session->userdata("default_company");
		$data['years']   = explode('_', $default_company['financial_year']);

		$this->load->helper('datefn');
		$data['javascript'] = array('daterangepicker/daterangepicker.js');
		$data['stylesheet'] = array('daterangepicker/daterangepicker-bs3.css');

		$data['page_title'] = "Vessel Register";
		$data['hide_title'] = true;
		$data['page'] = $this->_clspath.$this->_class;
		$data['docs_url'] = $this->_docs;
		$this->load->view('index', $data);
	}

	function preview($pdf = 0) {
		$from_date     = $this->session->userdata($this->_class.'_from_date');
		$to_date       = $this->session->userdata($this->_class.'_to_date');
		$search        = $this->session->userdata($this->_class.'_search');
		$parsed_search = $this->kaabar->parsed_search($search);
		
		$data['rows']  = $this->_getVessel($from_date, $to_date, $search, $parsed_search);

		$default_company = $this->session->userdata("default_company");
		$data['company'] = $this->kaabar->getRow('companies', $default_company['id']);

		$data['page_title'] = humanize($this->_class . ' Register');
		$data['page_desc'] = "For the Period $from_date - $to_date of " . $product;

		if ($pdf) {
			$filename = $data['page_title'];
			$html = $this->load->view($this->_clspath.$this->_class.'_preview', $data, true);
			$this->load->library('wkpdf');
			$this->wkpdf->set_html($html);
			$this->wkpdf->render();
			$this->wkpdf->output('D', "$filename.pdf");
			echo closeWindow();
			$this->kaabar->save($this->_table, array('printed' => 'Yes'), array('id' => $id));
		}
		else {
			$this->load->view($this->_clspath.$this->_class.'_preview', $data);
		}
	}

	function _getVessel($from_date, $to_date, $search, $parsed_search) {
		$sql = "SELECT P.name AS party_name, J.vessel_id, CONCAT(V.prefix, ' ', V.name, ' ', V.voyage_no) AS vessel_voyage, 
		DATE_FORMAT(V.pgr_begin_date, '%d-%m-%Y') AS pgr_begin_date, 
		DATE_FORMAT(
				IF(ISNULL(STR_TO_DATE(IC.ooc_date, '%m/%d/%Y')), STR_TO_DATE(IC.ooc_date, '%Y-%m-%d'), STR_TO_DATE(IC.ooc_date, '%m/%d/%Y')), 
			'%d-%m-%Y') AS ooc_date,
		DATE_FORMAT(AD.date, '%d-%m-%Y') AS do_date, IP.name AS indian_port, J.packages, J.cbm, A.name AS cha_name, 
		J.bl_no, PRD.name AS product_name, PRD.category
		FROM (((((((
				SELECT DISTINCT J.id, J.type, J.cargo_type, J.party_id, J.vessel_id, J.product_id, J.indian_port_id, 
					J.bl_no, J.packages, IF(J.cbm > 0, J.cbm, J.net_weight) AS cbm, J.cha_id
				FROM (vouchers VO INNER JOIN voucher_books VB ON VO.voucher_book_id = VB.id)
					INNER JOIN jobs J ON VO.job_id = J.id
				WHERE VB.company_id = ? AND VO.date >= ? AND VO.date <= ? AND VB.voucher_type_id IN (3,4)
			) J INNER JOIN parties P ON J.party_id = P.id)
			INNER JOIN vessels V ON J.vessel_id = V.id)
			INNER JOIN products PRD ON J.product_id = PRD.id)
			INNER JOIN indian_ports IP ON J.indian_port_id = IP.id)
			LEFT OUTER JOIN icegate_be IC ON J.id = IC.job_id)
			LEFT OUTER JOIN (
				SELECT AD.job_id, AD.date 
				FROM attached_documents AD INNER JOIN document_types DT ON (DT.code = 'DO' AND AD.document_type_id = DT.id)
					INNER JOIN jobs J ON AD.job_id = J.id
					WHERE J.type = 'Import' AND J.cargo_type = 'Bulk'
			) AD ON J.id = AD.job_id)
			LEFT OUTER JOIN agents A ON J.cha_id = A.id
		WHERE J.type = 'Import' AND J.cargo_type = 'Bulk' ";
		$where = ' AND (';
		if (is_array($parsed_search)) {
			foreach($parsed_search as $key => $value)
				if (isset($this->_fields[$key]))
					$where .= $this->_fields[$key] . " LIKE '%$value%' AND ";
			if (strlen($where) > 6)
				$sql .= substr($where, 0, strlen($where) - 5) . ')';
		}
		$sql .= "
		ORDER BY V.name, V.voyage_no";
		$query = $this->db->query($sql, array(
			$this->_company_id, convDate($from_date), convDate($to_date)
		));
		return $query->result_array();
	}


	function income_expense() {
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
		$data['search_fields'] = $this->_ie_fields;

		if (is_array($parsed_search)) {
			$search = '';
			foreach ($parsed_search as $key => $value) {
				$search .= $key.':'.$value.' ';
			}
			$data['search'] = $search;
		}

		$data['rows'] = $this->_getVesselIncomeExpense($data['from_date'], $data['to_date'], $search, $parsed_search);

		$default_company = $this->session->userdata("default_company");
		$data['years']   = explode('_', $default_company['financial_year']);

		$this->load->helper('datefn');
		$data['javascript'] = array('daterangepicker/daterangepicker.js');
		$data['stylesheet'] = array('daterangepicker/daterangepicker-bs3.css');

		$data['page_title'] = "Vessel Income / Expense";
		$data['hide_title'] = true;
		$data['page'] = $this->_clspath.$this->_class.'_income_expense';
		$data['docs_url'] = $this->_docs;
		$this->load->view('index', $data);
	}

	function excel() {
		$from_date     = $this->session->userdata($this->_class.'_from_date');
		$to_date       = $this->session->userdata($this->_class.'_to_date');
		$search        = $this->session->userdata($this->_class.'_search');
		$parsed_search = $this->kaabar->parseSearch($search);

		$rows = $this->_getVesselIncomeExpense($from_date, $to_date, $search, $parsed_search);
		
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

		$objPHPExcel->getActiveSheet()->setCellValue('A1', 'No');
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->setCellValue('B1', 'Cargo Type');
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->setCellValue('C1', 'Vessel');
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->setCellValue('D1', 'Port');
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->setCellValue('E1', 'Product');
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->setCellValue('F1', 'Pieces');
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->setCellValue('G1', 'CBM');
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->setCellValue('H1', 'C.20');
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->setCellValue('I1', 'C.40');
		$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->setCellValue('J1', 'Reimbersment');
		$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->setCellValue('K1', 'Invoice');
		$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->setCellValue('L1', 'Expense');
		$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->setCellValue('M1', 'Total');
		$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getStyle('A1:M1')->applyFromArray($styleHeading);

		// Data
		$i = 2;
		foreach ($rows as $r) {
			$row_total = bcadd(bcadd($r['reimbersment'], $r['invoice'], 2), $r['expense'], 2);

			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $i, $r['cargo_type']);
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $i, $r['vessel_voyage']);
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $i, $r['indian_port']);
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $i, $r['product_name']);
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, $i, $r['packages']);
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(6, $i, $r['cbm']);
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(7, $i, $r['container_20']);
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(8, $i, $r['container_40']);
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(9, $i, abs($r['reimbersment']));
			if ($r['reimbersment'] <= 0) {
				$objPHPExcel->getActiveSheet()->getStyle('J'.($i))->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_DARKGREEN);
			}
			else {
				$objPHPExcel->getActiveSheet()->getStyle('J'.($i))->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);
			}
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(10, $i, abs($r['invoice']));
			if ($r['invoice'] <= 0) {
				$objPHPExcel->getActiveSheet()->getStyle('K'.($i))->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_DARKGREEN);
			}
			else {
				$objPHPExcel->getActiveSheet()->getStyle('K'.($i))->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);
			}
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(11, $i, abs($r['expense']));
			if ($r['expense'] <= 0) {
				$objPHPExcel->getActiveSheet()->getStyle('L'.($i))->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_DARKGREEN);
			}
			else {
				$objPHPExcel->getActiveSheet()->getStyle('L'.($i))->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);
			}
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(12, $i, abs($row_total));
			if ($row_total <= 0) {
				$objPHPExcel->getActiveSheet()->getStyle('M'.($i))->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_DARKGREEN);
			}
			else {
				$objPHPExcel->getActiveSheet()->getStyle('M'.($i))->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);
			}
			$i++;
		}
		//$objPHPExcel->getActiveSheet()->getStyle('A1:G1000')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$objPHPExcel->getActiveSheet()->getStyle('A1:M'.$i)->applyFromArray($styleSheet);
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

	function _getVesselIncomeExpense($from_date, $to_date, $search, $parsed_search) {
		$result = array();
		$sql = "SELECT J.cargo_type, J.vessel_id, CONCAT(V.prefix, ' ', V.name, ' ', V.voyage_no) AS vessel_voyage, 
			IP.name AS indian_port, SUM(J.packages) AS packages, ROUND(SUM(IF(J.cbm > 0, J.cbm, J.net_weight)), 3) AS cbm, 
			SUM(J.container_20) AS container_20, SUM(J.container_40) AS container_40, PRD.name AS product_name, PRD.category,
			0 AS reimbersment, 0 AS invoice, 0 AS expense
		FROM ((jobs J INNER JOIN vessels V ON J.vessel_id = V.id)
			INNER JOIN products PRD ON J.product_id = PRD.id)
			INNER JOIN indian_ports IP ON J.indian_port_id = IP.id
		WHERE J.type = 'Import' AND J.date >= ? AND J.date <= ? ";
		$where = ' AND (';
		if (is_array($parsed_search)) {
			foreach($parsed_search as $key => $value)
				if (isset($this->_ie_fields[$key]))
					$where .= $this->_ie_fields[$key] . " LIKE '%$value%' AND ";
			if (strlen($where) > 6)
				$sql .= substr($where, 0, strlen($where) - 5) . ')';
		}
		$sql .= "
		GROUP BY V.id
		ORDER BY V.name, V.voyage_no";
		$query = $this->db->query($sql, array(
			convDate($from_date), convDate($to_date)
		));
		$rows = $query->result_array();
		foreach ($rows as $row) {
			$result[$row['vessel_id']] = $row;
		}

		if (count($result) > 0) {
			$sql = "SELECT L.id, L.vessel_id FROM ledgers L WHERE L.company_id = ? AND L.vessel_id IN (" . implode(", ", array_keys($result)) . ")";
			$query = $this->db->query($sql, array($this->_company_id));
			$rows = $query->result_array();
			$vessel_ids = array();
			foreach ($rows as $row) {
				$vessel_ids[] = $row['id'];
			}
			
			$rows = $this->report->getTrialBalance(date('d-m-Y'), NULL, $vessel_ids);
			foreach ($rows as $row) {
				foreach ($row as $l) {
					if ($l['account_id'] == 19) {
						$result[$l['vessel_id']]['reimbersment']          += $l['closing'];
						$result[$l['vessel_id']]['reimbersment_ledger_id'] = $l['id'];
					}
					if ($l['account_id'] == 25) {
						$result[$l['vessel_id']]['invoice']          += $l['closing'];
						$result[$l['vessel_id']]['invoice_ledger_id'] = $l['id'];
					}
					if ($l['account_id'] == 27) {
						$result[$l['vessel_id']]['invoice']          -= $l['credit'];
						$result[$l['vessel_id']]['invoice_ledger_id'] = $l['id'];
					}
					if ($l['account_id'] == 26) {
						$result[$l['vessel_id']]['expense']          += $l['closing'];
						$result[$l['vessel_id']]['expense_ledger_id'] = $l['id'];
					}
					if ($l['account_id'] == 27) {
						$result[$l['vessel_id']]['expense']          += $l['debit'];
						$result[$l['vessel_id']]['expense_ledger_id'] = $l['id'];
					}
				}
			}
		}

		return $result;
	}


	function pending() {
		if (! Auth::hasAccess()) {
			setSessionError('NO_PERMISSION');
			redirect('main');
		}

		$this->load->model('import');
		$search = $this->session->userdata($this->_class.'_search');

		if($this->input->post('search_form')) {
			$search = addslashes($this->input->post('search'));
			$this->session->set_userdata($this->_class.'_search', $search);
		}
		
		if ($search == null) {
			$search = '';
			$this->session->set_userdata($this->_class.'_search', $search);
		}

		$data['search'] = $search;
		$data['parsed_search'] = $this->kaabar->parseSearch($search);
		$data['search_fields'] = $this->_p_fields;

		if (is_array($data['parsed_search'])) {
			$search = '';
			foreach ($data['parsed_search'] as $key => $value) {
				$search .= $key.':'.$value.' ';
			}
			$data['search'] = $search;
		}
		$default_company = $this->session->userdata("default_company");

		$data['rows'] = $this->_getPending($search, $data['parsed_search'], $this->_p_fields);

		$data['javascript'] = array('backbonejs/underscore-min.js', 'backbonejs/backbone-min.js');

		$data['page_title'] = humanize($this->_class);
		$data['hide_title'] = true;
		$data['page'] = $this->_clspath.$this->_class.'_pending';
		$data['docs_url'] = $this->_docs;
		$this->load->view('index', $data);
	}

	function _getPending($search, $parsed_search, $fields, $query_only = false) {
		$sql = "SELECT V.id, V.agent_id, V.type, V.name, V.voyage_no, V.igm_no, DATE_FORMAT(V.igm_date, '%d-%m-%Y') AS igm_date, 
			indian_port_id, berth_no, DATE_FORMAT(V.gld_date, '%d-%m-%Y') AS gld_date, 
			DATE_FORMAT(V.eta_date, '%d-%m-%Y') AS eta_date, DATE_FORMAT(V.etd_date, '%d-%m-%Y') AS etd_date, 
			DATE_FORMAT(V.berthing_date, '%d-%m-%Y') AS berthing_date, 
			DATE_FORMAT(V.barging_date, '%d-%m-%Y') AS barging_date, 
			DATE_FORMAT(V.sailing_date, '%d-%m-%Y') AS sailing_date, 
			DATE_FORMAT(V.pgr_begin_date, '%d-%m-%Y') AS pgr_begin_date,
			V.exchange_rate, V.remarks, A.name AS agent_name, IP.name AS port_name
		FROM (vessels V LEFT OUTER JOIN agents A ON V.agent_id = A.id)
			LEFT OUTER JOIN indian_ports IP ON V.indian_port_id = IP.id
		WHERE ((
				V.type = 'Bulk' AND (
				(ISNULL(V.igm_date) OR LENGTH(TRIM(V.igm_date)) = 0 OR V.igm_date = '0000-00-00') OR
				(ISNULL(V.eta_date) OR LENGTH(TRIM(V.eta_date)) = 0 OR V.eta_date = '0000-00-00') OR
				(ISNULL(V.etd_date) OR LENGTH(TRIM(V.etd_date)) = 0 OR V.etd_date = '0000-00-00') OR
				(ISNULL(V.berthing_date) OR LENGTH(TRIM(V.berthing_date)) = 0 OR V.berthing_date = '0000-00-00') OR
				(ISNULL(V.sailing_date) OR LENGTH(TRIM(V.sailing_date)) = 0 OR V.sailing_date = '0000-00-00') OR
				(ISNULL(V.pgr_begin_date) OR LENGTH(TRIM(V.pgr_begin_date)) = 0 OR V.pgr_begin_date = '0000-00-00'))
			) OR (
				V.type = 'Container' AND (
				(ISNULL(V.igm_date) OR LENGTH(TRIM(V.igm_date)) = 0 OR V.igm_date = '0000-00-00') OR
				(ISNULL(V.gld_date) OR LENGTH(TRIM(V.gld_date)) = 0 OR V.gld_date = '0000-00-00') OR
				(ISNULL(V.eta_date) OR LENGTH(TRIM(V.eta_date)) = 0 OR V.eta_date = '0000-00-00') OR
				(ISNULL(V.etd_date) OR LENGTH(TRIM(V.etd_date)) = 0 OR V.etd_date = '0000-00-00') OR
				(ISNULL(V.berthing_date) OR LENGTH(TRIM(V.berthing_date)) = 0 OR V.berthing_date = '0000-00-00') OR
				(ISNULL(V.sailing_date) OR LENGTH(TRIM(V.sailing_date)) = 0 OR V.sailing_date = '0000-00-00') OR
				(ISNULL(V.pgr_begin_date) OR LENGTH(TRIM(V.pgr_begin_date)) = 0 OR V.pgr_begin_date = '0000-00-00'))
			))
		";
				$where = ' AND ';
				if (is_array($parsed_search)) {
					foreach($parsed_search as $key => $value)
						if (isset($fields[$key])) {
							$where .= $fields[$key] . " LIKE '%$value%' AND ";
						}
					if (strlen($where) > 6)
						$sql .= substr($where, 0, strlen($where) - 5);
				}
				$sql .= " 
		ORDER BY V.id DESC
		LIMIT 0, 200";
$this->firephp->info($sql);
		$query = $this->db->query($sql);

		if ($query_only)
			return $query;

		return $query->result_array();
	}
}
