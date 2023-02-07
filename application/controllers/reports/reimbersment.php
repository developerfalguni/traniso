<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use mikehaertl\wkhtmlto\Pdf;
class Reimbersment extends MY_Controller {
	function __construct() {
		parent::__construct();
		
		$this->load->model('report');
	}
	
	function index() {
		$from_date   = null;
		$to_date     = null;
		
		if ($this->input->post('from_date')) {
			$from_date   = $this->input->post('from_date');
			$to_date     = $this->input->post('to_date');
			$this->session->set_userdata($this->_class.'_from_date', $from_date);
			$this->session->set_userdata($this->_class.'_to_date', $to_date);
		}
		
		if ($from_date == null) {
			$from_date   = $this->session->userdata($this->_class.'_from_date');
			$to_date     = $this->session->userdata($this->_class.'_to_date');
		}

		$data['from_date']   = $from_date ? $from_date : date('01-m-Y');
		$data['to_date']     = $to_date ? $to_date : date('d-m-Y');
		
		$data['reimbersment'] = $this->report->getReimbersment(convDate($from_date), convDate($to_date));

		$default_company = $this->session->userdata("default_company");
		$data['years']   = explode('_', $default_company['financial_year']);

		$this->load->helper('datefn');
		$data['javascript'] = ['bootstrap-daterangepicker/daterangepicker.js'];
		$data['stylesheet'] = ['bootstrap-daterangepicker/daterangepicker.css'];

		$data['page_title'] = "Reimbersment Register";
		$data['page']       = $this->_clspath.$this->_class;
		$data['docs_url']   = $this->_docs;
		$this->load->view('index', $data);
	}

	function preview($pdf = 0) {
		$from_date   = $this->session->userdata($this->_class.'_from_date');
		$to_date     = $this->session->userdata($this->_class.'_to_date');
		
		$data['reimbersment'] = $this->report->getReimbersment(convDate($from_date), convDate($to_date));

		$default_company = $this->session->userdata("default_company");
		$data['company'] = $this->kaabar->getRow('companies', $default_company['id']);

		$data['page_title'] = "Reimbersment Register";
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

		$filename = $this->_class.date('_d_m_Y').".xlsx";
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

		$rows = $this->report->getReimbersment(convDate($from_date), convDate($to_date));

		$totals = array('amount' => 0);
		foreach ($rows['heading'] as $index => $code) {
			$totals[$code] = 0;
		}
		foreach ($rows['reimbersment'] as $s) {
			$totals['amount'] = bcadd($totals['amount'], $s['amount'], 2);
			foreach ($rows['heading'] as $index => $code)
				$totals[$code] = bcadd($totals[$code], $s[$code], 2);
		}
		// Header
		$j = 'A';
		$sheet->setCellValue($j++.'1', 'Voucher No');
		$sheet->setCellValue($j++.'1', 'Date');
		$sheet->setCellValue($j++.'1', 'Party');
		$sheet->setCellValue($j++.'1', 'Amount');
		foreach ($rows['heading'] as $index => $code) {
			if ($totals[$code] > 0)
				$sheet->setCellValue($j++.'1', $code);
		}
		$sheet->getStyle('A1:' . $j . '1')->applyFromArray($styleHeading);
		
		// Data
		$i = 2;
		foreach ($rows['reimbersment'] as $s) {
			$j = 'A';
			$sheet->setCellValue($j++.$i, $s['id2_format']);
			$sheet->setCellValue($j++.$i, $s['date']);
			$sheet->setCellValue($j++.$i, $s['party_name']);
			$sheet->setCellValue($j++.$i, number_format($s['amount'], 0, '.', ''));
			foreach ($rows['heading'] as $index => $code) {
				if ($totals[$code] > 0)
					$sheet->setCellValue($j++.$i, number_format($s[$code], 0, '.', ''));
			}
			$i++;
		}
		// $sheet->getStyle('A1:'.$j.$i)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
		$sheet->getStyle('A1:'.$j.$i)->applyFromArray($styleSheet);
		// $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
		$sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

		// redirect output to client browser
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="' . $filename.'"');
		header('Cache-Control: max-age=0');
		$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
		$writer->save('php://output');
	}
}
