<?php

use mikehaertl\wkhtmlto\Pdf;

class Tds_partywise extends MY_Controller {
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
		
		$data['rows'] = $this->_getTDS(convDate($from_date), convDate($to_date));

		$default_company = $this->session->userdata("default_company");
		$data['years'] = explode('_', $default_company['financial_year']);

		$this->load->helper('datefn');
		$data['javascript'] = array('daterangepicker/daterangepicker.js');
		$data['stylesheet'] = array('daterangepicker/daterangepicker-bs3.css');

		$data['page_title'] = "TDS Partywise";
		$data['hide_title'] = true;
		$data['page']       = $this->_clspath.$this->_class;
		$data['docs_url']   = $this->_docs;
		$this->load->view('index', $data);
	}

	function _getTDS($from_date, $to_date) {
		$data = array();

		$sql = "SELECT CT.id AS tds_payment_id, CL.name AS ledger_name, DT.id AS tds_deductee_id, 
			DT.type, DT.name, DL.name AS party_name, COALESCE(P.pan_no, A.pan_no, S.pan_no) AS pan_no,
			SUM(V.invoice_amount) AS invoice_amount, SUM(V.tds_amount) AS tds_amount, 
			SUM(V.tds_surcharge_amount) AS tds_surcharge_amount, SUM(V.tds_edu_cess_amount) AS tds_edu_cess_amount, 
			SUM(V.tds_hedu_cess_amount) AS tds_hedu_cess_amount
		FROM vouchers V INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id
			INNER JOIN voucher_types VT ON VB.voucher_type_id = VT.id
			INNER JOIN ledgers CL ON V.cr_ledger_id = CL.id
			INNER JOIN tds_classes CT ON CL.tds_class_id = CT.id
			INNER JOIN ledgers DL ON V.dr_ledger_id = DL.id
			LEFT OUTER JOIN parties P ON DL.party_id = P.id
			LEFT OUTER JOIN agents A ON DL.agent_id = A.id
			LEFT OUTER JOIN staffs S ON DL.staff_id = S.id
			LEFT OUTER JOIN tds_classes DT ON DL.tds_class_id = DT.id
			LEFT OUTER JOIN vouchers VTS ON V.tds_payment_id = VTS.id
		WHERE (VB.company_id = ? AND V.date >= ? AND V.date <= ?) AND CL.tds_class_id > 0 AND CT.type = 'Payment'
		GROUP BY P.id, A.id, S.id
		ORDER BY CT.id";
		$query = $this->db->query($sql, array($this->_company_id, $from_date, $to_date));
		$rows = $query->result_array();
		foreach ($rows as $row) {
			$data[$row['pan_no']] = $row;
		}
		return $data;
	}

	function preview($pdf = 0) {
		$from_date   = $this->session->userdata($this->_class.'_from_date');
		$to_date     = $this->session->userdata($this->_class.'_to_date');
		
		$data['rows'] = $this->_getTDS(convDate($from_date), convDate($to_date));

		$default_company = $this->session->userdata("default_company");
		$data['company'] = $this->kaabar->getRow('companies', $default_company['id']);

		$data['page_title'] = 'TDS Partywise';
		$data['page_desc'] = "For the Period $from_date - $to_date";

		if ($pdf) {
			$filename = $data['page_title'];
			$html = $this->load->view($this->_clspath.$this->_class.'_preview', $data, true);
			$pdf = new Pdf(array(
				'no-outline',
				'binary' => FCPATH.'wkhtmltopdf',
			));
			$pdf->addPage($html);
			$pdf->send("$filename.pdf");
		}
		else {
			$this->load->view($this->_clspath.$this->_class.'_preview', $data);
		}
	}

	
	function excel() {
		$from_date = $this->session->userdata($this->_class.'_from_date');
		$to_date   = $this->session->userdata($this->_class.'_to_date');
		$rows      = $this->_getTDS(convDate($from_date), convDate($to_date));
		
		ini_set('max_execution_time', '0');
		ini_set('memory_limit', '512M');

		set_include_path(get_include_path() . PATH_SEPARATOR . 'assets/phpexcel');

		include 'PHPExcel/IOFactory.php';

		$filename = "TDS Register.xlsx";
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

		$objPHPExcel->getActiveSheet()->setCellValue('A1', 'Sr No');
		$objPHPExcel->getActiveSheet()->setCellValue('B1', 'Party');
		$objPHPExcel->getActiveSheet()->setCellValue('C1', 'PAN No');
		$objPHPExcel->getActiveSheet()->setCellValue('D1', 'Invoice');
		$objPHPExcel->getActiveSheet()->setCellValue('E1', 'TDS');
		$objPHPExcel->getActiveSheet()->setCellValue('F1', 'Surcharge');
		$objPHPExcel->getActiveSheet()->setCellValue('G1', 'Edu Cess');
		$objPHPExcel->getActiveSheet()->setCellValue('H1', 'H.Edu Cess');
		$objPHPExcel->getActiveSheet()->getStyle('A1:H1')->applyFromArray($styleHeading);

		// Data
		$i = 2;
		foreach ($rows as $pan_no => $r) {
			$j = 'A';
			$objPHPExcel->getActiveSheet()->setCellValue(($j++).$i, ($i-1));
			$objPHPExcel->getActiveSheet()->setCellValue(($j++).$i, $r['party_name']);
			$objPHPExcel->getActiveSheet()->setCellValue(($j++).$i, $pan_no);
			$objPHPExcel->getActiveSheet()->setCellValue(($j++).$i, $r['invoice_amount']);
			$objPHPExcel->getActiveSheet()->setCellValue(($j++).$i, $r['tds_amount']);
			$objPHPExcel->getActiveSheet()->setCellValue(($j++).$i, $r['tds_surcharge_amount']);
			$objPHPExcel->getActiveSheet()->setCellValue(($j++).$i, $r['tds_edu_cess_amount']);
			$objPHPExcel->getActiveSheet()->setCellValue(($j++).$i, $r['tds_hedu_cess_amount']);
			$i++;
		}

		//$objPHPExcel->getActiveSheet()->getStyle('A1:G1000')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$objPHPExcel->getActiveSheet()->getStyle('A1:'.$j.$i)->applyFromArray($styleSheet);
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
