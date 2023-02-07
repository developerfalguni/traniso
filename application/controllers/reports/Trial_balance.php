<?php

use mikehaertl\wkhtmlto\Pdf;

class Trial_balance extends MY_Controller {
	function __construct() {
		parent::__construct();
		
		$this->load->model('report');
	}
	
	function index() {
		if (! Auth::hasAccess()) {
			setSessionError('NO_PERMISSION');
			redirect('main');
		}
		
		$search = null;
		$upto   = null;
		
		if($this->input->post('search_form')) {
			$starting_row = 0;
			$search       = $this->input->post('search');
			$upto         = $this->input->post('upto');
			$this->session->set_userdata($this->_class.'_search', $search);
			$this->session->set_userdata($this->_class.'_upto', $upto);
		}

		if($search == null) {
			$search = $this->session->userdata($this->_class.'_search');
			$upto = $this->session->userdata($this->_class.'_upto');
		}
		$data['search'] = $search ? $search : '';
		$data['upto'] = $upto ? $upto : date('d-m-Y');
		
		$data['rows'] = $this->report->getTrialBalance($data['upto'], $search);
	
		$data['page_title'] = humanize($this->_class);
		$data['hide_title'] = true;
		$data['page']       = $this->_clspath.$this->_class;
		$data['docs_url']   = $this->_docs;
		$this->load->view('index', $data);
	}
	
	function preview($pdf = 0, $summary = 0) {
		$default_company = $this->session->userdata('default_company');
		$data['company'] = $this->kaabar->getRow('companies', $default_company['id']);
		$search          = $this->session->userdata($this->_class.'_search');
		$upto            = $this->session->userdata($this->_class.'_upto');
		
		$data['rows'] = $this->report->getTrialBalance($upto, $search);
		$data['page_title'] = humanize($this->_class) . " for the Year " . $default_company['financial_year'] . "<br />As On $upto";

		if ($pdf) {
			$filename = 'Trial Balance - ' . $default_company['code'] . ' ' . $default_company['financial_year'];
			$html = $this->load->view($this->_clspath.$this->_class.($summary ? '_summary' : '_preview'), $data, true);
			$pdf = new Pdf(array(
				'no-outline',
				'binary' => FCPATH.'wkhtmltopdf',
			));
			$pdf->addPage($html);
			$pdf->send("$filename.pdf");
		}
		else {
			$this->load->view($this->_clspath.$this->_class.($summary ? '_summary' : '_preview'), $data);
		}
	}
	
	function excel() {
		$default_company = $this->session->userdata('default_company');
		$data['company'] = $this->kaabar->getRow('companies', $default_company['id']);
		$search          = $this->session->userdata($this->_class.'_search');
		$upto            = $this->session->userdata($this->_class.'_upto');
		
		$rows = $this->report->getTrialBalance($upto, $search);
		
		ini_set('max_execution_time', '0');
		ini_set('memory_limit', '512M');

		set_include_path(get_include_path() . PATH_SEPARATOR . 'assets/phpexcel');

		include 'PHPExcel/IOFactory.php';

		$filename = humanize($this->_class) . ".xlsx";
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

		$objPHPExcel->getActiveSheet()->setCellValue('A1', 'Group');
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->setCellValue('B1', 'Code');
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->setCellValue('C1', 'Name');
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->setCellValue('D1', 'Opening');
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->setCellValue('E1', 'Debit');
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->setCellValue('F1', 'Credit');
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->setCellValue('G1', 'Closing');
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);

		$objPHPExcel->getActiveSheet()->getStyle('A1:G1')->applyFromArray($styleHeading);

		// Data
		$i = 2;
		foreach ($rows as $group_name => $groups) {
			foreach ($groups as $r) {
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $i, $group_name);
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $i, $r['code']);
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $i, $r['name']);
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $i, $r['opening']);
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $i, $r['debit']);
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, $i, $r['credit']);
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(6, $i, $r['closing']);
				$i++;
			}
		}
		
		//$objPHPExcel->getActiveSheet()->getStyle('A1:G1000')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$objPHPExcel->getActiveSheet()->getStyle('A1:G'.$i)->applyFromArray($styleSheet);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);


		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
		//$objWriter->save($filename);

		// redirect output to client browser
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="' . $filename . '"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
	}
}
