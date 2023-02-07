<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use mikehaertl\wkhtmlto\Pdf;
class Tds extends MY_Controller {
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
		
		$data['tds'] = $this->_getTDS(convDate($from_date), convDate($to_date));

		$default_company = $this->session->userdata("default_company");
		$data['years'] = explode('_', $default_company['financial_year']);

		$this->load->helper('datefn');
		$data['javascript'] = ['bootstrap-daterangepicker/daterangepicker.js'];
		$data['stylesheet'] = ['bootstrap-daterangepicker/daterangepicker.css'];

		$data['page_title'] = "TDS Register";


		$data['page'] = $this->_clspath.$this->_class;
		$data['docs_url'] = $this->_docs;
		$this->load->view('index', $data);
	}

	function _getTDS($from_date, $to_date) {
		$data = array('summary' => array(), 'detail' => array());

		$sql = "SELECT CL.name AS ledger_name, DL.tds_class_id, DT.type, DT.name, SUM(V.invoice_amount) AS invoice_amount, 
			SUM(V.tds_amount) AS tds_amount, SUM(V.tds_surcharge_amount) AS surcharge, 
			SUM(V.tds_edu_cess_amount) AS edu_cess, SUM(V.tds_hedu_cess_amount) AS hedu_cess
		FROM vouchers V INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id
			INNER JOIN ledgers CL ON V.cr_ledger_id = CL.id
			INNER JOIN tds_classes CT ON CL.tds_class_id = CT.id
			INNER JOIN ledgers DL ON V.dr_ledger_id = DL.id
			LEFT OUTER JOIN tds_classes DT ON DL.tds_class_id = DT.id
		WHERE (VB.company_id = ? AND V.date >= ? AND V.date <= ?) AND CL.tds_class_id > 0 AND CT.type = 'Payment'
		GROUP BY DL.tds_class_id, CT.id";
		$query = $this->db->query($sql, array($this->_company_id, $from_date, $to_date));
		$rows = $query->result_array();
		foreach ($rows as $row) {
			if ($row['tds_class_id'] == 4 OR $row['tds_class_id'] == 5) {
				if (isset($data['summary'][$row['name']][$row['ledger_name']])) {
					$data['summary'][$row['name']][$row['ledger_name']]['invoice_amount'] += $row['invoice_amount'];
					$data['summary'][$row['name']][$row['ledger_name']]['tds_amount']     += $row['tds_amount'];
					$data['summary'][$row['name']][$row['ledger_name']]['surcharge']      += $row['surcharge'];
					$data['summary'][$row['name']][$row['ledger_name']]['edu_cess']       += $row['edu_cess'];
					$data['summary'][$row['name']][$row['ledger_name']]['hedu_cess']      += $row['hedu_cess'];
				}
				else {
					$data['summary'][$row['name']][$row['ledger_name']]['invoice_amount'] = $row['invoice_amount'];
					$data['summary'][$row['name']][$row['ledger_name']]['tds_amount']     = $row['tds_amount'];
					$data['summary'][$row['name']][$row['ledger_name']]['surcharge']      = $row['surcharge'];
					$data['summary'][$row['name']][$row['ledger_name']]['edu_cess']       = $row['edu_cess'];
					$data['summary'][$row['name']][$row['ledger_name']]['hedu_cess']      = $row['hedu_cess'];
				}
			}
			else {
				if (isset($data['summary']['Other Than Company'][$row['ledger_name']])) {
					$data['summary']['Other Than Company'][$row['ledger_name']]['invoice_amount'] += $row['invoice_amount'];
					$data['summary']['Other Than Company'][$row['ledger_name']]['tds_amount']     += $row['tds_amount'];
					$data['summary']['Other Than Company'][$row['ledger_name']]['surcharge']      += $row['surcharge'];
					$data['summary']['Other Than Company'][$row['ledger_name']]['edu_cess']       += $row['edu_cess'];
					$data['summary']['Other Than Company'][$row['ledger_name']]['hedu_cess']      += $row['hedu_cess'];
				}
				else {
					$data['summary']['Other Than Company'][$row['ledger_name']]['invoice_amount'] = $row['invoice_amount'];
					$data['summary']['Other Than Company'][$row['ledger_name']]['tds_amount']     = $row['tds_amount'];
					$data['summary']['Other Than Company'][$row['ledger_name']]['surcharge']      = $row['surcharge'];
					$data['summary']['Other Than Company'][$row['ledger_name']]['edu_cess']       = $row['edu_cess'];
					$data['summary']['Other Than Company'][$row['ledger_name']]['hedu_cess']      = $row['hedu_cess'];
				}
			}
		}


		$sql = "SELECT CT.id AS tds_payment_id, CL.name AS ledger_name, DT.id AS tds_deductee_id, 
			DT.type, DT.name, DL.name AS party_name, CONCAT(VT.name, '/edit/', V.voucher_book_id, '/', V.id) AS url,
			V.id2_format, DATE_FORMAT(V.date, '%d-%m-%Y') AS credit_date, 
			V.invoice_amount, V.tds_amount, V.tds, V.tds_surcharge_amount, V.tds_edu_cess_amount, V.tds_hedu_cess_amount, 
			DATE_FORMAT(VTS.date, '%d-%m-%Y') AS tds_stax_date, VTS.tds_stax_bsr_code, VTS.tds_stax_challan_no
		FROM vouchers V INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id
			INNER JOIN voucher_types VT ON VB.voucher_type_id = VT.id
			INNER JOIN ledgers CL ON V.cr_ledger_id = CL.id
			INNER JOIN tds_classes CT ON CL.tds_class_id = CT.id
			INNER JOIN ledgers DL ON V.dr_ledger_id = DL.id
			LEFT OUTER JOIN tds_classes DT ON DL.tds_class_id = DT.id
			LEFT OUTER JOIN vouchers VTS ON V.tds_payment_id = VTS.id
		WHERE (VB.company_id = ? AND V.date >= ? AND V.date <= ?) AND CL.tds_class_id > 0 AND CT.type = 'Payment'
		ORDER BY CT.id";
		$query = $this->db->query($sql, array($this->_company_id, $from_date, $to_date));
		$rows = $query->result_array();
		foreach ($rows as $row) {
			if ($row['tds_deductee_id'] == 4 OR $row['tds_deductee_id'] == 5)
				$data['detail'][$row['ledger_name']][$row['name']][] = $row;
			else
				$data['detail'][$row['ledger_name']]['Other Than Company'][] = $row;
		}
		return $data;
	}

	function preview($pdf = 0) {
		$from_date   = $this->session->userdata($this->_class.'_from_date');
		$to_date     = $this->session->userdata($this->_class.'_to_date');
		
		$data['tds'] = $this->_getTDS(convDate($from_date), convDate($to_date));

		$default_company = $this->session->userdata("default_company");
		$data['company'] = $this->kaabar->getRow('companies', $default_company['id']);

		$data['page_title'] = 'TDS Register';
		$data['page_desc'] = "For the Period $from_date - $to_date";

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
		$from_date = $this->session->userdata($this->_class.'_from_date');
		$to_date   = $this->session->userdata($this->_class.'_to_date');
		$tds       = $this->_getTDS(convDate($from_date), convDate($to_date));
		
		$filename = "TDS Register.xlsx";
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

		$sheet->setCellValue('B1', 'TDS Type');
		$sheet->setCellValue('C1', 'Category');
		$sheet->setCellValue('E1', 'Invoice');
		$sheet->setCellValue('G1', 'Amount');
		$sheet->setCellValue('H1', 'Surcharge');
		$sheet->setCellValue('I1', 'Edu Cess');
		$sheet->setCellValue('J1', 'H.Edu Cess');

		$sheet->getStyle('A1:J1')->applyFromArray($styleHeading);

		// Data
		$i = 2;
		foreach ($tds['summary'] as $deductee_type => $ledgers) {
			foreach ($ledgers as $ledger_name => $dr) {
				$sheet->setCellValueByColumnAndRow(1, $i, $deductee_type);
				$sheet->setCellValueByColumnAndRow(2, $i, $ledger_name);
				$sheet->setCellValueByColumnAndRow(4, $i, $dr['invoice_amount']);
				$sheet->setCellValueByColumnAndRow(6, $i, $dr['tds_amount']);
				$sheet->setCellValueByColumnAndRow(7, $i, $dr['surcharge']);
				$sheet->setCellValueByColumnAndRow(8, $i, $dr['edu_cess']);
				$sheet->setCellValueByColumnAndRow(9, $i, $dr['hedu_cess']);
				$i++;
			}
		}

		foreach ($tds['detail'] as $ledger => $payments) : 
			foreach ($payments as $deductee => $tds_details) :
				$i+=2;
				$sheet->setCellValue('B'.$i, $ledger);
				$sheet->setCellValue('C'.$i, $deductee);
				$sheet->getStyle('B'.$i.':C'.$i)->applyFromArray($styleHeading);

				$i++;
				$sheet->setCellValue('A'.$i, 'Sr No');
				$sheet->getColumnDimension('A')->setAutoSize(true);
				$sheet->setCellValue('B'.$i, 'Voucher');
				$sheet->getColumnDimension('B')->setAutoSize(true);
				$sheet->setCellValue('C'.$i, 'Party');
				$sheet->getColumnDimension('C')->setAutoSize(true);
				$sheet->setCellValue('D'.$i, 'Credit Date');
				$sheet->getColumnDimension('D')->setAutoSize(true);
				$sheet->setCellValue('E'.$i, 'Invoice');
				$sheet->getColumnDimension('E')->setAutoSize(true);
				$sheet->setCellValue('F'.$i, 'TDS');
				$sheet->getColumnDimension('F')->setAutoSize(true);
				$sheet->setCellValue('G'.$i, 'Amount');
				$sheet->getColumnDimension('G')->setAutoSize(true);
				$sheet->setCellValue('H'.$i, 'Surcharge');
				$sheet->getColumnDimension('H')->setAutoSize(true);
				$sheet->setCellValue('I'.$i, 'Edu Cess');
				$sheet->getColumnDimension('I')->setAutoSize(true);
				$sheet->setCellValue('J'.$i, 'H.Edu Cess');
				$sheet->getColumnDimension('J')->setAutoSize(true);
				$sheet->setCellValue('K'.$i, 'Date');
				$sheet->getColumnDimension('K')->setAutoSize(true);
				$sheet->setCellValue('L'.$i, 'BSR Code');
				$sheet->getColumnDimension('L')->setAutoSize(true);
				$sheet->setCellValue('M'.$i, 'Challan No');
				$sheet->getColumnDimension('M')->setAutoSize(true);

				$sheet->getStyle('A'.$i.':M'.$i)->applyFromArray($styleHeading);
				$i++;

				$sr_no = 1;
				foreach ($tds_details as $tdr) {
					$j = 'A';
					$sheet->setCellValue(($j++).$i, $sr_no++);
					$sheet->setCellValue(($j++).$i, $tdr['id2_format']);
					$sheet->setCellValue(($j++).$i, $tdr['party_name']);
					$sheet->setCellValue(($j++).$i, $tdr['credit_date']);
					$sheet->setCellValue(($j++).$i, $tdr['invoice_amount']);
					$sheet->setCellValue(($j++).$i, $tdr['tds']);
					$sheet->setCellValue(($j++).$i, $tdr['tds_amount']);
					$sheet->setCellValue(($j++).$i, $tdr['tds_surcharge_amount']);
					$sheet->setCellValue(($j++).$i, $tdr['tds_edu_cess_amount']);
					$sheet->setCellValue(($j++).$i, $tdr['tds_hedu_cess_amount']);
					$sheet->setCellValue(($j++).$i, $tdr['tds_stax_date']);
					$sheet->setCellValueExplicit(($j++).$i, $tdr['tds_stax_bsr_code'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
					$sheet->setCellValueExplicit(($j++).$i, $tdr['tds_stax_challan_no'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
					$i++;
				}
			endforeach; 
		endforeach;

		//$sheet->getStyle('A1:G1000')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
		$sheet->getStyle('A1:M2000')->applyFromArray($styleSheet);
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
