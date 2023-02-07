<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use mikehaertl\wkhtmlto\Pdf;
class Profit_loss extends MY_Controller {
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
		$default_company    = $this->session->userdata("default_company");
		$data['years']   = explode('_', $this->_fy_year);
		
		$from_date = null;
		$to_date   = null;
		$search    = null;
		
		if($this->input->post('search_form')) {
			$starting_row = 0;
			$from_date = $this->input->post('from_date');
			$to_date   = $this->input->post('to_date');
			$this->session->set_userdata($this->_class.'_from_date', $from_date);
			$this->session->set_userdata($this->_class.'_to_date', $to_date);
		}

		if($search == null) {
			$from_date = $this->session->userdata($this->_class.'_from_date');
			$to_date   = $this->session->userdata($this->_class.'_to_date');
		}
		$data['from_date'] = $from_date ? $from_date : date('01-04-'.$data['years'][0]);
		$data['to_date']   = $to_date ? $to_date : date('d-m-Y');

		$data['profit_loss'] = $this->_get($data['from_date'], $data['to_date']);

		$this->load->helper('datefn');
		$data['javascript'] = ['bootstrap-daterangepicker/daterangepicker.js'];
		$data['stylesheet'] = ['bootstrap-daterangepicker/daterangepicker.css'];

		$data['company_id'] = $default_company['id'];
		$data['page_title'] = "Profit and Loss";
		$data['page_desc']  = $default_company['code'] . ' (' . str_replace('_', '-', $default_company['financial_year'] . ')');
		$data['page']       = $this->_clspath.$this->_class;
		$data['docs_url']   = $this->_docs;
		$this->load->view('index', $data);
	}

	function _get($from_date, $to_date) {
		// $fy = explode("_", $this->kaabar->getFinancialYear($upto));
		$sql = "SELECT A.deemed_positive, AG.id AS group_id, AG.name AS group_name, L.id AS ledger_id, L.name,
			SUM(L.opening_balance) AS opening, 
			SUM(COALESCE(TB.debit, 0)) AS debit, SUM(COALESCE(ABS(TB.credit), 0)) AS credit, 
			(SUM(L.opening_balance) + SUM(COALESCE(TB.debit, 0)) + SUM(COALESCE(TB.credit, 0))) AS closing
		FROM ledgers L INNER JOIN account_groups AG ON L.account_group_id = AG.id
			INNER JOIN accounts A ON AG.account_id = A.id
			LEFT OUTER JOIN ledgers LP ON L.parent_ledger_id = LP.id
			LEFT OUTER JOIN (
			SELECT CLOSE.id, ROUND(SUM(CLOSE.debit), 2) AS debit, ROUND(SUM(credit), 2) AS credit
			FROM (
					SELECT V.dr_ledger_id AS id, SUM(V.amount) AS debit, 0 AS credit
					FROM vouchers V INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id
					WHERE VB.company_id = ? AND V.date >= ? AND V.date <= ?
					GROUP BY V.dr_ledger_id 
				UNION ALL
					SELECT V.cr_ledger_id AS id, 0 AS debit, SUM(-V.amount) AS credit
					FROM vouchers V INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id
					WHERE VB.company_id = ? AND V.date >= ? AND V.date <= ? AND VB.voucher_type_id != 4
					GROUP BY V.cr_ledger_id
				UNION ALL
					SELECT V.cr_ledger_id AS id, 0 AS debit, SUM(-VJD.amount) AS credit
					FROM ((voucher_details VJD INNER JOIN vouchers V ON VJD.voucher_id = V.id)
						INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id)
						INNER JOIN ledgers BI ON VJD.bill_item_id = BI.id
					WHERE VB.company_id = ? AND V.date >= ? AND V.date <= ? AND VB.voucher_type_id = 4 AND 
						BI.category = 'Bill Items'
					GROUP BY V.cr_ledger_id
				UNION ALL
					SELECT VJD.bill_item_id AS id, 0 AS debit, SUM(-VJD.amount) AS credit
					FROM ((voucher_details VJD INNER JOIN vouchers V ON VJD.voucher_id = V.id)
						INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id)
						INNER JOIN ledgers BI ON VJD.bill_item_id = BI.id
					WHERE VB.company_id = ? AND V.date >= ? AND V.date <= ? AND VB.voucher_type_id = 4 AND 
						BI.category != 'Bill Items' AND 
						BI.stax_category_id > 0
					GROUP BY VJD.bill_item_id
				UNION ALL
					SELECT VJD.bill_item_id AS id, SUM(VJD.amount) AS debit, 0 AS credit
					FROM voucher_details VJD INNER JOIN vouchers V ON VJD.voucher_id = V.id
						INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id
						INNER JOIN ledgers BI ON VJD.bill_item_id = BI.id
					WHERE VB.company_id = ? AND V.date >= ? AND V.date <= ? AND 
						BI.category != 'Bill Items' AND 
						BI.stax_category_id > 0 AND 
						VB.voucher_type_id = 2
					GROUP BY VJD.bill_item_id
				) AS CLOSE
			GROUP BY id
		) AS TB ON L.id = TB.id
		WHERE L.company_id = ? AND A.affects_gross_profit = 1
		GROUP BY IF(L.parent_ledger_id > 0, LP.id, L.id)
		HAVING (opening != 0 OR debit != 0 OR credit != 0)
		ORDER BY AG.id, L.name";
		$query = $this->db->query($sql, array(
			$this->_company_id, convDate($from_date), convDate($to_date),
			$this->_company_id, convDate($from_date), convDate($to_date),
			$this->_company_id, convDate($from_date), convDate($to_date),
			$this->_company_id, convDate($from_date), convDate($to_date),
			$this->_company_id, convDate($from_date), convDate($to_date),
			$this->_company_id
		));
		return $query->result_array();
	}

	function preview($pdf = 0, $summary = 0) {
		$upto = $this->session->userdata($this->_class.'_upto');
		$data['profit_loss'] = $this->_get($upto);

		$default_company = $this->session->userdata("default_company");
		$data['company'] = $this->kaabar->getRow('companies', $default_company['id']);
		$data['page_title'] = humanize($this->_class) . " for the Year " . $default_company['financial_year'] . "<br />As On $upto";
		$data['pdf'] = true;

		if ($pdf) {
			$filename = 'Profit and Loss - ' . $default_company['code'] . ' ' . $default_company['financial_year'];
			$html = $this->load->view($this->_clspath.$this->_class.($summary ? '_summary' : null), $data, true);
			$pdf = new Pdf(array(
				'no-outline',
				'binary'      => FCPATH.'wkhtmltopdf',
				'orientation' => 'Landscape',
			));
			$pdf->addPage($html);
			$pdf->send("$filename.pdf");
		}
		else {
			$this->load->view($this->_clspath.$this->_class.($summary ? '_summary' : null), $data);
		}
	}

	function excel() {
		$upto            = $this->session->userdata($this->_class.'_upto');
		$profit_loss     = $this->report->getProfitLoss($upto);
		$default_company = $this->session->userdata("default_company");
		$company         = $this->kaabar->getRow('companies', $default_company['id']);
		
		$filename = 'Profit and Loss - ' . $default_company['code'] . ' ' . $default_company['financial_year'] . ".xlsx";
		// Create a new PhpSpreadsheet Object
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

		$sheet->setCellValue('A1', 'Expenses');
		$sheet->setCellValue('B1', '');
		$sheet->setCellValue('C1', 'Debit');
		$sheet->setCellValue('D1', '');
		$sheet->setCellValue('E1', 'Income');
		$sheet->setCellValue('F1', '');
		$sheet->setCellValue('G1', 'Credit');
		
		// Data
		// Pending... :(
		
		$sheet->getStyle('A1:G'.max($i, $j))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
		$sheet->getStyle('A1:G'.max($i, $j))->applyFromArray($styleSheet);

		// redirect output to client browser
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="' . $filename.'"');
		header('Cache-Control: max-age=0');
		$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
		$writer->save('php://output');	
	}
}
