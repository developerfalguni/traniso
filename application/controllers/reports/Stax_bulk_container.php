<?php

ini_set('max_execution_time', '0');
ini_set('memory_limit', '512M');

class Stax_bulk_container extends MY_Controller {
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
	}
	
	function index() {
		if (! Auth::hasAccess()) {
			setSessionError('NO_PERMISSION');
			redirect('main');
		}

		$from_date   = null;
		$to_date     = null;
		
		if($this->input->post('from_date')) {
			$from_date   = $this->input->post('from_date');
			$to_date     = $this->input->post('to_date');
			$this->session->set_userdata($this->_class.'_from_date', $from_date);
			$this->session->set_userdata($this->_class.'_to_date', $to_date);
		}
		
		if($from_date == null) {
			$from_date   = $this->session->userdata($this->_class.'_from_date');
			$to_date     = $this->session->userdata($this->_class.'_to_date');
		}

		$data['from_date']   = $from_date ? $from_date : date('01-m-Y');
		$data['to_date']     = $to_date ? $to_date : date('d-m-Y');
		
		$data['stax'] = $this->_getSTAX($data['from_date'], $data['to_date']);

		$default_company = $this->session->userdata("default_company");
		$data['years']   = explode('_', $default_company['financial_year']);

		$this->load->helper('datefn');
		$data['javascript'] = array('daterangepicker/daterangepicker.js');
		$data['stylesheet'] = array('daterangepicker/daterangepicker-bs3.css');

		$data['page_title'] = "Service Tax (Bulk/Container)";
		$data['hide_title'] = true;
		$data['page'] = $this->_clspath.'stax_bulk_container';
		$data['docs_url'] = $this->_docs;
		$this->load->view('index', $data);
	}

	function _getSTAX($from_date, $to_date) {
		$result = array();

		// Searching Bulk Service Charge in Invoices
		$sql = "SELECT ST.id, ST.name, SUM(VJD.amount) AS service_charge
		FROM ((vouchers V INNER JOIN voucher_details VJD ON V.id = VJD.voucher_id)
			INNER JOIN ledgers BI ON VJD.bill_item_id = BI.id)
			INNER JOIN stax_categories ST ON BI.stax_category_id = ST.id
		WHERE BI.category = 'Bill Items' AND 
			BI.stax_category_id > 0 AND 
			V.id IN (
			SELECT V.id 
			FROM (vouchers V INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id)
				INNER JOIN ledgers CL ON V.cr_ledger_id = CL.id
			WHERE VB.company_id = ? AND V.date >= ? AND V.date <= ? AND 
				VB.voucher_type_id = 4 AND 
				INSTR(UPPER(CL.name), 'CONTAINER HANDLING') = 0
		)
		GROUP BY ST.id
		ORDER BY ST.name";
		$query = $this->db->query($sql, array($this->_company_id, convDate($from_date), convDate($to_date)));
		$rows = $query->result_array();
		foreach ($rows as $row) {
			if (isset($result[$row['id']])) {
				$result[$row['id']]['bulk_sc'] += $row['service_charge'];
			}
			else {
				$result[$row['id']] = array(
					'name'        => $row['name'], 
					'bulk_sc'     => $row['service_charge'],
					'cont_sc'     => 0,
					'bulk_credit' => 0,
					'cont_credit' => 0,
					'bulk_debit'  => 0,
					'cont_debit'  => 0
				);
			}
		}

		// Searching Container Service Charge in Invoices
		$sql = "SELECT ST.id, ST.name, SUM(VJD.amount) AS service_charge
		FROM ((vouchers V INNER JOIN voucher_details VJD ON V.id = VJD.voucher_id)
			INNER JOIN ledgers BI ON VJD.bill_item_id = BI.id)
			INNER JOIN stax_categories ST ON BI.stax_category_id = ST.id
		WHERE BI.category = 'Bill Items' AND 
			BI.stax_category_id > 0 AND 
			V.id IN (
			SELECT V.id 
			FROM (vouchers V INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id)
				INNER JOIN ledgers CL ON V.cr_ledger_id = CL.id
			WHERE VB.company_id = ? AND V.date >= ? AND V.date <= ? AND 
				VB.voucher_type_id = 4 AND 
				INSTR(UPPER(CL.name), 'CONTAINER HANDLING') > 0
		)
		GROUP BY ST.id
		ORDER BY ST.name";
		$query = $this->db->query($sql, array($this->_company_id, convDate($from_date), convDate($to_date)));
		$rows = $query->result_array();
		foreach ($rows as $row) {
			if (isset($result[$row['id']])) {
				$result[$row['id']]['cont_sc'] += $row['service_charge'];
			}
			else {
				$result[$row['id']] = array(
					'name'        => $row['name'], 
					'bulk_sc'     => 0,
					'cont_sc'     => $row['service_charge'],
					'bulk_credit' => 0,
					'cont_credit' => 0,
					'bulk_debit'  => 0,
					'cont_debit'  => 0
				);
			}
		}

		// Fetching All Debit entries from Journal Vouchers
		$sql = "SELECT ST.id, ST.name, SUM(V.amount) AS debit
		FROM ((vouchers V INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id)
			INNER JOIN ledgers DL ON V.dr_ledger_id = DL.id)
			INNER JOIN stax_categories ST ON DL.stax_category_id = ST.id
		WHERE VB.company_id = ? AND V.date >= ? AND V.date <= ? AND 
			  VB.voucher_type_id NOT IN (4,7) AND 
			  DL.category = 'General' AND 
			  DL.stax_category_id > 0
		GROUP BY ST.id
		ORDER BY ST.name";
		$query = $this->db->query($sql, array(
			$this->_company_id, convDate($from_date), convDate($to_date),
		));
		$rows = $query->result_array();
		foreach ($rows as $row) {
			if (isset($result[$row['id']])) {
				$result[$row['id']]['bulk_debit'] += $row['debit'];
			}
			else {
				$result[$row['id']] = array(
					'name'        => $row['name'], 
					'bulk_sc'     => 0,
					'cont_sc'     => 0,
					'bulk_credit' => 0,
					'cont_credit' => 0, 
					'bulk_debit'  => $row['debit'],
					'cont_debit'  => 0
				);
			}
		}

		// Fetching Container Debit entries from Journal Vouchers
		$sql = "SELECT ST.id, ST.name, SUM(V.amount) AS debit
		FROM vouchers V INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id
			INNER JOIN ledgers DL ON V.dr_ledger_id = DL.id
			INNER JOIN stax_categories ST ON DL.stax_category_id = ST.id
			INNER JOIN (
				SELECT VB.company_id, V.voucher_book_id, V.id2
				FROM vouchers V INNER JOIN voucher_books VB ON (VB.voucher_type_id NOT IN (4,7) AND V.voucher_book_id = VB.id)
					INNER JOIN ledgers DL ON V.dr_ledger_id = DL.id
				WHERE VB.company_id = ? AND V.date >= ? AND V.date <= ? AND 
					INSTR(UPPER(DL.name), 'CONTAINER HANDLING') > 0
			) V2 ON VB.company_id = V2.company_id AND V.voucher_book_id = V2.voucher_book_id AND V.id2 = V2.id2
		WHERE VB.company_id = ? AND V.date >= ? AND V.date <= ? AND 
			VB.voucher_type_id NOT IN (4,7) AND 
			DL.category = 'General' AND 
			DL.stax_category_id > 0 
		GROUP BY ST.id
		ORDER BY ST.name";
		$query = $this->db->query($sql, array(
			$this->_company_id, convDate($from_date), convDate($to_date),
			$this->_company_id, convDate($from_date), convDate($to_date)
		));
		$rows = $query->result_array();
		foreach ($rows as $row) {
			if (isset($result[$row['id']])) {
				$result[$row['id']]['bulk_debit'] = bcsub($result[$row['id']]['bulk_debit'], $row['debit'], 2);
				$result[$row['id']]['cont_debit'] = $row['debit'];
			}
			else {
				$result[$row['id']] = array(
					'name'        => $row['name'], 
					'bulk_sc'     => 0,
					'cont_sc'     => 0,
					'bulk_credit' => 0,
					'cont_credit' => 0, 
					'bulk_debit'  => bcsub($result[$row['id']]['bulk_debit'], $row['debit'], 2), 
					'cont_debit'  => $row['debit'],
				);
			}
		}


		// Fetching All Credit entries from Invoices
		$sql = "SELECT ST.id, DATE_FORMAT(V.date, '%Y-%m') AS date, ST.name, SUM(VJD.amount) AS credit
		FROM ((((vouchers V INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id)
			INNER JOIN ledgers DL ON V.dr_ledger_id = DL.id)
			INNER JOIN voucher_details VJD ON V.id = VJD.voucher_id)
			INNER JOIN ledgers BI ON VJD.bill_item_id = BI.id)
			INNER JOIN stax_categories ST ON BI.stax_category_id = ST.id
		WHERE VB.company_id = ? AND V.date >= ? AND V.date <= ? AND 
			  VB.voucher_type_id = 4 AND 
			  BI.category != 'Bill Items' AND 
			  BI.stax_category_id > 0
		GROUP BY ST.id, DATE_FORMAT(V.date, '%Y-%m')
		ORDER BY ST.name";
		$query = $this->db->query($sql, array(
			$this->_company_id, convDate($from_date), convDate($to_date),
		));
		$rows = $query->result_array();
		foreach ($rows as $row) {
			if (isset($result[$row['id']])) {
				$result[$row['id']]['bulk_credit'] += $row['credit'];
			}
			else {
				$result[$row['id']] = array(
					'name'        => $row['name'], 
					'bulk_sc'     => 0,
					'cont_sc'     => 0,
					'bulk_credit' => $row['credit'],
					'cont_credit' => 0, 
					'bulk_debit'  => 0,
					'cont_debit'  => 0
				);
			}
		}

		// Fetching Container Credit entries from Invoices
		$sql = "SELECT ST.id, DATE_FORMAT(V.date, '%Y-%m') AS date, ST.name, SUM(VJD.amount) AS credit
		FROM vouchers V INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id
			INNER JOIN ledgers DL ON V.dr_ledger_id = DL.id
			INNER JOIN voucher_details VJD ON V.id = VJD.voucher_id
			INNER JOIN ledgers BI ON VJD.bill_item_id = BI.id
			INNER JOIN stax_categories ST ON BI.stax_category_id = ST.id
			INNER JOIN (
				SELECT VB.company_id, V.voucher_book_id, V.id2
				FROM vouchers V INNER JOIN voucher_books VB ON (VB.voucher_type_id = 4 AND V.voucher_book_id = VB.id)
					INNER JOIN ledgers CL ON V.cr_ledger_id = CL.id
				WHERE VB.company_id = ? AND V.date >= ? AND V.date <= ? AND 
					INSTR(UPPER(CL.name), 'CONTAINER HANDLING') > 0
			) V2 ON VB.company_id = V2.company_id AND V.voucher_book_id = V2.voucher_book_id AND V.id2 = V2.id2
		WHERE VB.company_id = ? AND V.date >= ? AND V.date <= ? AND 
			  VB.voucher_type_id = 4 AND 
			  BI.category != 'Bill Items' AND 
			  BI.stax_category_id > 0
		GROUP BY ST.id, DATE_FORMAT(V.date, '%Y-%m')
		ORDER BY ST.name";
		$query = $this->db->query($sql, array(
			$this->_company_id, convDate($from_date), convDate($to_date),
			$this->_company_id, convDate($from_date), convDate($to_date)
		));
		$rows = $query->result_array();
		foreach ($rows as $row) {
			if (isset($result[$row['id']])) {
				$result[$row['id']]['bulk_credit'] = bcsub($result[$row['id']]['bulk_credit'], $row['credit'], 2);
				$result[$row['id']]['cont_credit'] = $row['credit'];
			}
			else {
				$result[$row['id']] = array(
					'name'        => $row['name'], 
					'bulk_sc'     => 0,
					'cont_sc'     => 0,
					'bulk_credit' => bcsub($result[$row['id']]['bulk_credit'], $row['credit'], 2), 
					'cont_credit' => $row['credit'],
					'bulk_debit'  => 0,
					'cont_debit'  => 0,
				);
			}
		}

		return $result;
	}

	function excel() {
		set_include_path(get_include_path() . PATH_SEPARATOR . 'assets/phpexcel');

		include 'PHPExcel/IOFactory.php';

		$filename = "STAX Summary.xlsx";
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

		$stax = $this->_getSTAXSummary();

		$objPHPExcel->getActiveSheet()->setCellValue('A1', 'Month');
		$j = 'B';
		foreach ($stax['stax'] as $id => $name) {
			$objPHPExcel->getActiveSheet()->setCellValue($j.'1', $name);
			$j++;
			$j++;
		}
		$objPHPExcel->getActiveSheet()->getStyle('A1:'.$j.'1')->applyFromArray($styleHeading);
		$last_j = $j;

		// Data
		$i = 2;
		foreach ($stax['months'] as $month => $hrows) {
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $hrows['month']);
			$j = 'B';
			//foreach ($hrows as $id => $hrow) {
			foreach ($stax['stax'] as $id => $name) {
				if (isset($hrows[$id])) {
					$objPHPExcel->getActiveSheet()->setCellValue($j.$i, $hrows[$id]['credit']);
					$objPHPExcel->getActiveSheet()->getStyle($j.$i)->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);
					$j++;
					$objPHPExcel->getActiveSheet()->setCellValue($j.$i, $hrows[$id]['debit']);
					$objPHPExcel->getActiveSheet()->getStyle($j.$i)->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_DARKGREEN);
					$j++;
				}
				else {
					$j++;
					$j++;
				}
			}
			$i++;
		}


		//$objPHPExcel->getActiveSheet()->getStyle('A1:G1000')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$objPHPExcel->getActiveSheet()->getStyle('A1:'.$last_j.$i)->applyFromArray($styleSheet);
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
