<?php

ini_set('max_execution_time', '0');
ini_set('memory_limit', '512M');

class Stax extends MY_Controller {
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
		
		$data['category']    = $this->report->getSTAXCategory(convDate($from_date), convDate($to_date));
		$data['stax']        = $this->report->getSTAX(convDate($from_date), convDate($to_date));

		$default_company = $this->session->userdata("default_company");
		$data['years']   = explode('_', $default_company['financial_year']);

		$this->load->helper('datefn');
		$data['javascript'] = ['bootstrap-daterangepicker/daterangepicker.js'];
		$data['stylesheet'] = ['bootstrap-daterangepicker/daterangepicker.css'];

		$data['page_title'] = "Service Tax";

		$data['page'] = $this->_clspath.$this->_class;
		$data['docs_url'] = $this->_docs;
		$this->load->view('index', $data);
	}

	function preview($pdf = 0) {
		$from_date   = $this->session->userdata($this->_class.'_from_date');
		$to_date     = $this->session->userdata($this->_class.'_to_date');
		
		$data['category'] = $this->report->getSTAXCategory($data['from_date'], $data['to_date']);
		$data['stax']     = $this->report->getSTAX($data['from_date'], $data['to_date']);

		$default_company = $this->session->userdata("default_company");
		$data['company'] = $this->kaabar->getRow('companies', $default_company['id']);

		$data['page_title'] = 'Service Tax';
		$data['page_desc'] = "( $from_date to $to_date )";

		if ($pdf) {
			$filename = $data['page_title'];
			$html = $this->load->view($this->_clspath.$this->_class.'_preview', $data, true);
			$pdf = new Pdf(array(
				'no-outline',
				'binary'      => FCPATH.'wkhtmltopdf',
				'orientation' => 'Landscape',
			));
			$pdf->addPage($html);
			$pdf->send("$filename.pdf");
			echo closeWindow();
			$this->kaabar->save($this->_table, array('printed' => 'Yes'), array('id' => $id));
		}
		else {
			$this->load->view($this->_clspath.$this->_class.'_preview', $data);
		}
	}

	function excel() {
		$from_date   = $this->session->userdata($this->_class.'_from_date');
		$to_date     = $this->session->userdata($this->_class.'_to_date');

		$category = $this->report->getSTAXCategory(convDate($from_date), convDate($to_date));
		$stax     = $this->report->getSTAX(convDate($from_date), convDate($to_date));

		$filename = "STAX Register.xlsx";
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

		$sheet->setCellValue('A1', 'S.Tax Category');
		$sheet->setCellValue('B1', 'Service Charge');
		$sheet->setCellValue('C1', 'SC Credit Note');
		$sheet->setCellValue('D1', 'Payment');
		$sheet->setCellValue('F1', 'Opening');
		$sheet->setCellValue('H1', 'Credit');
		$sheet->setCellValue('I1', 'Credit Note');
		$sheet->setCellValue('J1', 'Debit');
		$sheet->setCellValue('K1', 'Cr - Dr');
		$sheet->setCellValue('L1', 'To Pay');
		$sheet->getStyle('A1:L1')->applyFromArray($styleHeading);

		// Data
		$i = 2;
		foreach ($category as $id => $c) {
			$j = 'A';
			$to_pay = bcsub(bcadd(bcadd($c['payment'], $c['opening'], 2), $c['debit'], 2), $c['credit'], 2);
			$sheet->setCellValue($j++.$i, $c['name']);
			$sheet->setCellValue($j++.$i, $c['service_charge']);
			$sheet->setCellValue($j++.$i, $c['credit_note_sc']);
			$sheet->setCellValue($j++.$i, $c['payment']); 
			$sheet->setCellValue($j++.$i, ($c['payment'] >= 0 ? 'Dr' : 'Cr'));
			$sheet->setCellValue($j++.$i, abs($c['opening']));
			$sheet->setCellValue($j++.$i, ($c['opening'] >= 0 ? 'Dr' : 'Cr'));
			$sheet->setCellValue($j++.$i, $c['credit']);
			$sheet->setCellValue($j++.$i, $c['credit_note']);
			$sheet->setCellValue($j++.$i, $c['debit']);
			$sheet->setCellValue($j++.$i, "=((H$i+I$i)-J$i)");
			$sheet->setCellValue($j++.$i, abs($to_pay));
			$sheet->setCellValue($j++.$i, ($to_pay >= 0 ? 'Dr' : 'Cr'));
			$i++;
		}

		$totals = array('amount' => 0);
		foreach ($stax['heading'] as $index => $row) {
			$totals[$row['code']] = 0;
		}
		foreach ($stax['stax'] as $s) {
			$totals['amount'] = bcadd($totals['amount'], $s['amount'], 2);
			foreach ($stax['heading'] as $row)
				$totals[$row['code']] = bcadd($totals[$row['code']], $s[$row['code']], 2);
		}


		$i += 2;
		$j = 'A';
		$sheet->setCellValue($j++.$i, 'Voucher No');
		$sheet->setCellValue($j++.$i, 'Date');
		$sheet->setCellValue($j++.$i, 'Party');
		$sheet->setCellValue($j++.$i, 'Invoice Amount');
		foreach ($stax['heading'] as $index => $row) {
			if ($totals[$row['code']] > 0)
				$sheet->setCellValue($j++.$i, $row['code']);
		}
		$i++;
		foreach ($stax['stax'] as $s) {
			$j = 'A';
			$sheet->setCellValue($j++.$i, $s['id2_format']);
			$sheet->setCellValue($j++.$i, $s['date']);
			$sheet->setCellValue($j++.$i, $s['party_name']);
			$sheet->setCellValue($j++.$i, $s['amount']);
			foreach ($stax['heading'] as $row) {
				if ($totals[$row['code']] > 0)
					$sheet->setCellValue($j++.$i, $s[$row['code']]);
			}
			$i++;
		}

		//$sheet->getStyle('A1:G1000')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
		$sheet->getStyle('A1:'.$j.$i)->applyFromArray($styleSheet);
		$sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
		$sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);


		// redirect output to client browser
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="' . $filename.'"');
		header('Cache-Control: max-age=0');
		$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
		$writer->save('php://output');
	}


	function bulk_container() {
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
		
		$data['stax'] = $this->report->getSTAXCategoryBulkCont($data['from_date'], $data['to_date']);

		$default_company = $this->session->userdata("default_company");
		$data['years']   = explode('_', $default_company['financial_year']);

		$this->load->helper('datefn');
		$data['javascript'] = ['bootstrap-daterangepicker/daterangepicker.js'];
		$data['stylesheet'] = ['bootstrap-daterangepicker/daterangepicker.css'];

		$data['page_title'] = "Service Tax (Bulk/Container)";
		$data['page'] = $this->_clspath.'stax_bulk_container';
		$data['docs_url'] = $this->_docs;
		$this->load->view('index', $data);
	}


	function excel2() {
		$filename = "STAX Summary.xlsx";
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

		$stax = $this->report->getSTAXSummary();

		$sheet->setCellValue('A1', 'Month');
		$j = 'B';
		foreach ($stax['stax'] as $id => $name) {
			$sheet->setCellValue($j.'1', $name);
			$j++;
			$j++;
		}
		$sheet->getStyle('A1:'.$j.'1')->applyFromArray($styleHeading);
		$last_j = $j;

		// Data
		$i = 2;
		foreach ($stax['months'] as $month => $hrows) {
			$sheet->setCellValue('A'.$i, $hrows['month']);
			$j = 'B';
			//foreach ($hrows as $id => $hrow) {
			foreach ($stax['stax'] as $id => $name) {
				if (isset($hrows[$id])) {
					$sheet->setCellValue($j.$i, $hrows[$id]['credit']);
					$sheet->getStyle($j.$i)->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
					$j++;
					$sheet->setCellValue($j.$i, $hrows[$id]['debit']);
					$sheet->getStyle($j.$i)->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_DARKGREEN);
					$j++;
				}
				else {
					$j++;
					$j++;
				}
			}
			$i++;
		}


		//$sheet->getStyle('A1:G1000')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
		$sheet->getStyle('A1:'.$last_j.$i)->applyFromArray($styleSheet);
		$sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
		$sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);


		// redirect output to client browser
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="' . $filename.'"');
		header('Cache-Control: max-age=0');
		$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
		$writer->save('php://output');
	}
}
