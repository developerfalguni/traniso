<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use mikehaertl\wkhtmlto\Pdf;
class Balance_sheet extends MY_Controller {
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
		$starting_row = intval($starting_row);$upto = null;
		
		if($this->input->post('search_form')) {
			$starting_row = 0;
			$upto         = $this->input->post('upto');
			$this->session->set_userdata($this->_class.'_upto', $upto);
		}

		if($upto == null) {
			$upto = $this->session->userdata($this->_class.'_upto');
		}
		$data['upto'] = $upto ? $upto : date('d-m-Y');

		$data['balance_sheet'] = $this->_get($data['upto']);

		$default_company = $this->session->userdata("default_company");
		$data['page_title'] = "Balance Sheet";
		$data['page_desc'] = $default_company['code'] . ' (' . str_replace('_', '-', $default_company['financial_year'] . ')');


		$data['page'] = $this->_clspath.$this->_class;
		$data['docs_url'] = $this->_docs;
		$this->load->view('index', $data);
	}

	function _get($upto) {
		$bs = array('liabilities' => array(), 'assets' => array());

		$sql = "SELECT AG.id, AG.name,
			SUM(IF(A.default_dr_cr = 'Dr', IF(TB.credit > 0, -TB.credit, TB.debit), '')) AS debit,
			SUM(IF(A.default_dr_cr = 'Cr', IF(TB.debit > 0, -TB.debit, TB.credit), '')) AS credit
		FROM ((ledgers L INNER JOIN account_groups AG ON L.account_group_id = AG.id)
			INNER JOIN accounts A ON AG.account_id = A.id)
			INNER JOIN (
			SELECT CLOSE.id, 
				IF(SUM(amount) > 0, ROUND(SUM(amount), 2), 0) AS debit, 
				IF(SUM(amount) < 0, ROUND(ABS(SUM(amount)), 2), 0) AS credit
			FROM (
					SELECT L.id, ROUND(opening_balance, 2) AS amount
					FROM ledgers L 
					WHERE company_id = ?
				UNION ALL
					SELECT V.dr_ledger_id AS id, SUM(V.amount) AS amount
					FROM vouchers V INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id
					WHERE VB.company_id = ? AND V.date <= ?
					GROUP BY V.dr_ledger_id
				UNION ALL
					SELECT V.cr_ledger_id AS id, SUM(-V.amount) AS amount
					FROM vouchers V INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id
					WHERE VB.company_id = ? AND V.date <= ? AND VB.voucher_type_id != 4
					GROUP BY V.cr_ledger_id
				UNION ALL
					SELECT V.cr_ledger_id AS id, SUM(-VJD.amount) AS amount
					FROM voucher_details VJD INNER JOIN vouchers V ON VJD.voucher_id = V.id
						INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id
						INNER JOIN ledgers BI ON VJD.bill_item_id = BI.id
					WHERE VB.company_id = ? AND V.date <= ? AND 
						BI.category = 'Bill Items' AND 
						VB.voucher_type_id = 4
					GROUP BY V.cr_ledger_id
				UNION ALL
					SELECT VJD.bill_item_id AS id, SUM(-VJD.amount) AS amount
					FROM voucher_details VJD INNER JOIN vouchers V ON VJD.voucher_id = V.id
						INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id
						INNER JOIN ledgers BI ON VJD.bill_item_id = BI.id
					WHERE VB.company_id = ? AND V.date <= ? AND 
						BI.category != 'Bill Items' AND 
						BI.stax_category_id > 0 AND 
						VB.voucher_type_id = 4
					GROUP BY VJD.bill_item_id
				) AS CLOSE
			GROUP BY id
		) AS TB ON L.id = TB.id
		WHERE A.affects_gross_profit = 0 AND (TB.debit > 0 OR TB.credit > 0)
		GROUP BY AG.id
		ORDER BY A.sort_order";
		$query = $this->db->query($sql, array(
			$this->_company_id,
			$this->_company_id, convDate($upto), 
			$this->_company_id, convDate($upto), 
			$this->_company_id, convDate($upto), 
			$this->_company_id, convDate($upto),
		));
		$rows = $query->result_array();
		foreach($rows as $row) {
			if ($row['credit'] > 0) {
				$bs['liabilities'][$row['id']][$row['name']] = $row['credit'];
			}
			else if ($row['debit'] < 0) {
				$bs['liabilities'][$row['id']][$row['name']] = abs($row['debit']);
			}
			else if ($row['debit'] > 0) {
				$bs['assets'][$row['id']][$row['name']] = $row['debit'];
			}
			else {
				$bs['assets'][$row['id']][$row['name']] = abs($row['credit']);
			}
		}

		$sql = "SELECT AG.id, L.id AS ledger_id, L.name,
			IF(A.default_dr_cr = 'Dr', IF(TB.credit > 0, -TB.credit, TB.debit), '') AS debit,
			IF(A.default_dr_cr = 'Cr', IF(TB.debit > 0, -TB.debit, TB.credit), '')  AS credit
		FROM ((ledgers L INNER JOIN account_groups AG ON L.account_group_id = AG.id)
			INNER JOIN accounts A ON AG.account_id = A.id)
			INNER JOIN (
			SELECT CLOSE.id, 
				IF(SUM(amount) > 0, ROUND(SUM(amount), 2), 0) AS debit, 
				IF(SUM(amount) < 0, ROUND(ABS(SUM(amount)), 2), 0) AS credit
			FROM (
					SELECT L.id, ROUND(opening_balance, 2) AS amount
					FROM ledgers L 
					WHERE company_id = ?
				UNION ALL
					SELECT V.dr_ledger_id AS id, SUM(V.amount) AS amount
					FROM vouchers V INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id
					WHERE VB.company_id = ? AND V.date <= ?
					GROUP BY V.dr_ledger_id
				UNION ALL
					SELECT V.cr_ledger_id AS id, SUM(-V.amount) AS amount
					FROM vouchers V INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id
					WHERE VB.company_id = ? AND V.date <= ? AND VB.voucher_type_id != 4
					GROUP BY V.cr_ledger_id
				UNION ALL
					SELECT V.cr_ledger_id AS id, SUM(-VJD.amount) AS amount
					FROM voucher_details VJD INNER JOIN vouchers V ON VJD.voucher_id = V.id
						INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id
						INNER JOIN ledgers BI ON VJD.bill_item_id = BI.id
					WHERE VB.company_id = ? AND V.date <= ? AND 
						BI.category = 'Bill Items' AND 
						VB.voucher_type_id = 4
					GROUP BY V.cr_ledger_id
				UNION ALL
					SELECT VJD.bill_item_id AS id, SUM(-VJD.amount) AS amount
					FROM voucher_details VJD INNER JOIN vouchers V ON VJD.voucher_id = V.id
						INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id
						INNER JOIN ledgers BI ON VJD.bill_item_id = BI.id
					WHERE VB.company_id = ? AND V.date <= ? AND 
						BI.category != 'Bill Items' AND 
						BI.stax_category_id > 0 AND 
						VB.voucher_type_id = 4
					GROUP BY VJD.bill_item_id
				) AS CLOSE
			GROUP BY id
		) AS TB ON L.id = TB.id
		WHERE A.affects_gross_profit = 0 AND (TB.debit > 0 OR TB.credit > 0)
		ORDER BY A.sort_order, L.name";
		$query = $this->db->query($sql, array(
			$this->_company_id, 
			$this->_company_id, convDate($upto),
			$this->_company_id, convDate($upto),
			$this->_company_id, convDate($upto),
			$this->_company_id, convDate($upto),
		));
		$rows = $query->result_array();
		foreach ($rows as $row) {
			if (isset($bs['liabilities'][$row['id']]))
				$bs['liabilities'][$row['id']]['details'][$row['ledger_id']] = array('name' => $row['name'], 'amount' => ($row['debit'] == '' ? $row['credit'] : $row['debit']));
			else
				$bs['assets'][$row['id']]['details'][$row['ledger_id']] = array('name' => $row['name'], 'amount' => ($row['debit'] == '' ? $row['credit'] : $row['debit']));
		}

		return $bs;
	}

	function preview($pdf = 0, $summary = 0) {
		$upto = $this->session->userdata($this->_class.'_upto');

		$data['balance_sheet'] = $this->_get($upto);

		$default_company    = $this->session->userdata("default_company");
		$data['company']    = $this->kaabar->getRow('companies', $default_company['id']);
		$data['page_title'] = humanize($this->_class) . " for the Year " . $default_company['financial_year'] . "<br />As On $upto";
		$data['pdf']        = true;

		if ($pdf) {
			$filename = 'Balance Sheet - ' . $default_company['code'] . ' ' . $default_company['financial_year'];
			$html = $this->load->view($this->_clspath.$this->_class.($summary ? '_summary' : ''), $data, true);
			$pdf = new Pdf(array(
				'no-outline',
				'binary'      => FCPATH.'wkhtmltopdf',
				'orientation' => 'Landscape',
			));
			$pdf->addPage($html);
			$pdf->send("$filename.pdf");
		}
		else {
			$this->load->view($this->_clspath.$this->_class.($summary ? '_summary' : ''), $data);
		}
	}

	function excel() {
		$upto            = $this->session->userdata($this->_class.'_upto');
		$balance_sheet   = $this->_get($upto);
		$default_company = $this->session->userdata("default_company");
		$company         = $this->kaabar->getRow('companies', $default_company['id']);
		
		$filename = 'Balance Sheet - ' . $default_company['code'] . ' ' . $default_company['financial_year'] . ".xlsx";
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

		$sheet->setCellValue('A1', 'Liabilities');
		$sheet->setCellValue('B1', '');
		$sheet->setCellValue('C1', 'Credit');
		$sheet->setCellValue('D1', '');
		$sheet->setCellValue('E1', 'Assets');
		$sheet->setCellValue('F1', '');
		$sheet->setCellValue('G1', 'Debit');
		
		// Data
		$i = 2;
		foreach ($balance_sheet['liabilities'] as $gid => $groups) {
			foreach ($groups as $key => $value) {
				if (is_array($value)) {
					foreach ($value as $ledger_id => $ledger) {
						$sheet->setCellValue('A'.$i, $ledger['name']);
						$sheet->setCellValue('B'.$i, $ledger['amount']);
						$i++;
					}
				}
				else {
					$sheet->setCellValue('A'.$i, $key);
					$sheet->setCellValue('C'.$i, $value);

					$total['liabilities'] += $value;
					$i++;
				}
			}
		}

		$j = 2;
		foreach ($balance_sheet['assets'] as $gid => $groups) {
			foreach ($groups as $key => $value) {
				if (is_array($value)) {
					foreach ($value as $ledger_id => $ledger) {
						$sheet->setCellValue('E'.$j, $ledger['name']);
						$sheet->setCellValue('F'.$j, $ledger['amount']);
						$j++;
					}
				}
				else {
					$sheet->setCellValue('E'.$j, $key);
					$sheet->setCellValue('G'.$j, $value);

					$total['assets'] += $value;
					$j++;
				}
			}
		}
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
