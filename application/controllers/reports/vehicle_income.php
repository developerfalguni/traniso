<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use mikehaertl\wkhtmlto\Pdf;
class Vehicle_income extends MY_Controller {
	var $_fields;
	var $_parsed_search;
	var $_company_id;
	var $_fy_year;
			
	function __construct() {
		parent::__construct();

		$this->_fields = array(
			'from_location'   => 'FL.name',
			'to_location'     => 'TL.name',
			'party'           => 'PLE.name',
			'transporter'     => 'TLE.name',
			'registration_no' => 'T.registration_no',
			'lr_no'           => 'T.lr_no',
		);

		$this->_company_id = 0;
		$default_company = $this->session->userdata('default_company');
		if ($default_company != false) {
			$this->_company_id = $default_company['id'];
			$this->_fy_year    = $default_company['financial_year'];
		}
		unset($default_company);
	}
	
	function index($starting_row = 0) {
		$default_company = $this->session->userdata("default_company");
		$data['company'] = $this->kaabar->getRow('companies', $default_company['id']);
		$data['years']   = explode('_', $default_company['financial_year']);

		$from_date = null;
		$to_date   = null;
		$search    = null;

		if ($this->input->post('from_date')) {
			$from_date = $this->input->post('from_date');
			$to_date   = $this->input->post('to_date');
			$search    = $this->input->post('search');
			$this->session->set_userdata($this->_class.'_from_date', $from_date);
			$this->session->set_userdata($this->_class.'_to_date', $to_date);
			$this->session->set_userdata($this->_class.'_search', $search);
		}
		
		if ($from_date == null) {
			$from_date = $this->session->userdata($this->_class.'_from_date');
			$to_date   = $this->session->userdata($this->_class.'_to_date');
			$search = $this->session->userdata($this->_class.'_search');
		}
		
		$data['from_date']     = $from_date ? $from_date : date('01-m-Y');
		$data['to_date']       = $to_date ? $to_date : date('d-m-Y');
		$data['search']        = $search;
		$parsed_search         = $this->kaabar->parseSearch($search);
		$data['parsed_search'] = $parsed_search;
		$data['search_fields'] = $this->_fields;
			
		if (is_array($parsed_search)) {
			$search = '';
			foreach ($parsed_search as $key => $value) {
				$search .= $key.':'.$value.' ';
			}
			$data['search'] = $search;
		}

		$data['rows'] = $this->_getTrips($from_date, $to_date, $search, $parsed_search);

		$this->load->helper('datefn');
		$data['javascript'] = ['bootstrap-daterangepicker/daterangepicker.js'];
		$data['stylesheet'] = ['bootstrap-daterangepicker/daterangepicker.css'];
		
		$data['docs_url']   = $this->_docs;
		$data['page_title'] = $this->_class . ' Report';
		$data['page']       = $this->_clspath.$this->_class;
		$this->load->view('index', $data);
	}

	function _getTrips($from_date, $to_date, $search, $parsed_search) {
		$result = array();
		$sql = "SELECT T.*, FL.name AS from_location, TL.name AS to_location, PLE.name AS party_name, TLE.name AS transporter_name, 
			IF(ISNULL(V.id), 0, 1) AS self, L.id AS ledger_id, DATE_FORMAT(T.date, '%d-%m-%Y') AS date, 
			TA.self_adv, TA.party_adv, PA.amount AS pump_adv,
			ROUND(COALESCE(TA.amount, 0) + COALESCE(PA.amount, 0), 2) AS allowance, 
			ROUND(T.party_rate - ROUND(COALESCE(TA.amount, 0) + COALESCE(PA.amount, 0), 2), 2) AS balance,
			VO.id2_format AS bill_no, DATE_FORMAT(VO.date, '%d-%m-%Y') AS bill_date
		FROM trips T 
			INNER JOIN locations FL ON T.from_location_id = FL.id
			INNER JOIN locations TL ON T.to_location_id = TL.id
			INNER JOIN vehicles V ON (T.registration_no = V.registration_no AND LENGTH(V.registration_no) > 0 AND ! ISNULL(V.id))
			INNER JOIN ledgers L ON V.id = L.vehicle_id
			LEFT OUTER JOIN ledgers PLE ON T.party_ledger_id = PLE.id
			LEFT OUTER JOIN ledgers TLE ON T.transporter_ledger_id = TLE.id
			LEFT OUTER JOIN voucher_details VD ON T.id = VD.trip_id
			LEFT OUTER JOIN vouchers VO ON VD.voucher_id = VO.id
			LEFT OUTER JOIN (
				SELECT TA.trip_id, 
					IF(TA.advance_by = 'Self', ROUND(SUM(TA.amount), 2), 0) AS self_adv, 
					IF(TA.advance_by = 'Party', ROUND(SUM(TA.amount), 2), 0) AS party_adv,
					SUM(TA.amount) AS amount
				FROM trip_advances TA INNER JOIN trips T ON TA.trip_id = T.id
				WHERE T.company_id = ?
				GROUP BY TA.trip_id
			) TA ON T.id = TA.trip_id
			LEFT OUTER JOIN (
				SELECT PA.trip_id, SUM(PA.amount) AS amount
				FROM pump_advances PA INNER JOIN trips T ON PA.trip_id = T.id
				WHERE T.company_id = ?
				GROUP BY PA.trip_id
			) PA ON T.id = PA.trip_id
		WHERE (T.date >= ? AND T.date <= ?)";
		$where = ' AND (';
		if (is_array($parsed_search)) {
			foreach($parsed_search as $key => $value)
				if (isset($this->_fields[$key]))
					$where .= $this->_fields[$key] . " LIKE '%$value%' AND ";
			if (strlen($where) > 6)
				$sql .= substr($where, 0, strlen($where) - 5) . ')';
		}
		$sql .= " 
		GROUP BY T.id
		ORDER BY T.registration_no, T.date DESC";
		$query = $this->db->query($sql, array(
			$this->_company_id, $this->_company_id,
			convDate($from_date), convDate($to_date)
		));
		$rows = $query->result_array();
		foreach ($rows as $row) {
			$result[$row['ledger_id']]['trips'][] = $row;
		}
		$ledger_ids = implode(',', array_keys($result));
		if (strlen($ledger_ids) == 0)
			$ledger_ids = '0';

		$sql = "SELECT A.deemed_positive, AG.id AS group_id, AG.name AS group_name, L.id AS ledger_id, L.name,
			SUM(L.opening_balance) AS opening, 
			SUM(COALESCE(TB.debit, 0)) AS debit, SUM(COALESCE(ABS(TB.credit), 0)) AS credit, 
			(SUM(L.opening_balance) + SUM(COALESCE(TB.debit, 0)) + SUM(COALESCE(TB.credit, 0))) AS closing
		FROM ledgers L
			INNER JOIN account_groups AG ON L.account_group_id = AG.id
			INNER JOIN accounts A ON AG.account_id = A.id
			LEFT OUTER JOIN ledgers LP ON L.parent_ledger_id = LP.id
			LEFT OUTER JOIN (
			SELECT CLOSE.id, ROUND(SUM(CLOSE.debit), 2) AS debit, ROUND(SUM(credit), 2) AS credit
			FROM (
					SELECT V.dr_ledger_id AS id, SUM(V.amount) AS debit, 0 AS credit
					FROM vouchers V INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id
					WHERE VB.company_id = ? AND V.date >= ? AND V.date <= ? AND V.dr_ledger_id IN ($ledger_ids)
					GROUP BY V.dr_ledger_id 
				UNION ALL
					SELECT V.cr_ledger_id AS id, 0 AS debit, SUM(-V.amount) AS credit
					FROM vouchers V INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id
					WHERE VB.company_id = ? AND V.date >= ? AND V.date <= ? AND VB.voucher_type_id != 4
					GROUP BY V.cr_ledger_id
				UNION ALL
					SELECT V.cr_ledger_id AS id, 0 AS debit, SUM(-VJD.amount) AS credit
					FROM voucher_details VJD INNER JOIN vouchers V ON VJD.voucher_id = V.id
						INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id
						INNER JOIN ledgers BI ON VJD.bill_item_id = BI.id
					WHERE VB.company_id = ? AND V.date >= ? AND V.date <= ? AND VB.voucher_type_id = 4 AND 
						BI.category = 'Bill Items'
					GROUP BY V.cr_ledger_id
				UNION ALL
					SELECT VJD.bill_item_id AS id, 0 AS debit, SUM(-VJD.amount) AS credit
					FROM voucher_details VJD INNER JOIN vouchers V ON VJD.voucher_id = V.id
						INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id
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
		WHERE L.company_id = ? AND A.affects_gross_profit = 1 AND L.id IN ($ledger_ids)
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
		$rows = $query->result_array();
		foreach ($rows as $row) {
			$result[$row['ledger_id']]['closing'] = $row['closing'];
		}
		return $result;
	}

	function preview($pdf = 0) {
		$default_company       = $this->session->userdata('default_company');
		$data['company']       = $this->kaabar->getRow('companies', $default_company['id']);
		$from_date             = $this->session->userdata($this->_class.'_from_date');
		$to_date               = $this->session->userdata($this->_class.'_to_date');
		$search                = $this->session->userdata($this->_class.'_search');
		$parsed_search         = $this->kaabar->parseSearch($search);
		$data['rows']          = $this->_getTrips($from_date, $to_date, $search, $parsed_search);

		$data['page_title'] = humanize($this->_class . ' Report');	
		
		if ($pdf) {
			$filename = underscore($data['page_title']).date('_d_m_Y');			
			$html = $this->load->view($this->_clspath.$this->_class.'_preview', $data, true);
			$pdf = new Pdf(array(
				'no-outline',
				'binary'      => FCPATH.'wkhtmltopdf',
				'orientation' => 'Landscape',
			));
			$pdf->addPage($html);
			$pdf->send("$filename.pdf");
		}
		else {
			$this->load->view($this->_clspath.$this->_class.'_preview', $data);			
		}
	}
	
	function excel() {
		$from_date     = $this->session->userdata($this->_class.'_from_date');
		$to_date       = $this->session->userdata($this->_class.'_to_date');
		$search        = $this->session->userdata($this->_class.'_search');
		$parsed_search = $this->kaabar->parseSearch($search);

		$filename = $this->_class.date('_d_m_Y').".xlsx";
		$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
		$sheet       = $spreadsheet->getActiveSheet();
		
		$styleSheet = [
			'font' => [
				'name' => 'Times New Roman',
				'size' => 10
			],
		];

		$styleMainHeading = [
			'font' => [
				'name' => 'Times New Roman',
				'bold' => true,
				'size' => 14,
			],
			'alignment' => [
				'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
			]
		];

		$styleHeading = [
			'font' => [
				'bold' => true,
			],
			'alignment' => [
				'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
			]
		];

		// Header
		$j = 'A';
		$i = 1;
		$sheet->setCellValue($j++.$i, 'No');
		$sheet->setCellValue($j++.$i, 'Date');
		$sheet->setCellValue($j++.$i, 'Party Ref');
		$sheet->setCellValue($j++.$i, 'Bill No');
		$sheet->setCellValue($j++.$i, 'Bill Date');
		$sheet->setCellValue($j++.$i, 'Container');
		$sheet->setCellValue($j++.$i, 'Size');
		$sheet->setCellValue($j++.$i, 'From');
		$sheet->setCellValue($j++.$i, 'To');
		$sheet->setCellValue($j++.$i, 'Party');
		$sheet->setCellValue($j++.$i, 'Vehicle No');
		$sheet->setCellValue($j++.$i, 'LR No');
		$sheet->setCellValue($j++.$i, 'Party Rate');
		$sheet->setCellValue($j++.$i, 'Trans Rate');
		$sheet->setCellValue($j++.$i, 'Self Adv');
		$sheet->setCellValue($j++.$i, 'Party Adv');
		$sheet->setCellValue($j++.$i, 'Pump Adv');
		$sheet->setCellValue($j++.$i, 'Advance');
		$sheet->setCellValue($j++.$i, 'Balance');
		$sheet->getStyle('A'.$i.':'.$j.$i)->applyFromArray($styleHeading);
		$i++;
		
		// Data
		$rows = $this->_getTrips($from_date, $to_date, $search, $parsed_search);
		$sr_no = 1;
		$total = [
			'transporter_rate' => 0,
			'party_rate'       => 0,
			'self_adv'         => 0,
			'party_adv'        => 0,
			'pump_adv'         => 0,
			'allowance'        => 0,
			'balance'          => 0,
			'expenses'         => 0,
		];
		foreach ($rows as $ledger_id => $vehicles) {
			$sr_no = 1;
			$group = [
				'transporter_rate' => 0,
				'party_rate'       => 0,
				'self_adv'         => 0,
				'party_adv'        => 0,
				'pump_adv'         => 0,
				'allowance'        => 0,
				'balance'          => 0,
			];
			$registration_no = '';
			foreach ($vehicles['trips'] as $r) {
				$filter['from_location'][$r['from_location']]     = 1;
				$filter['to_location'][$r['to_location']]         = 1;
				$filter['product'][$r['product_name']]            = 1;
				$filter['party'][$r['party_name']]                = 1;
				$filter['transporter'][$r['transporter_name']]    = 1;
				$filter['registration_no'][$r['registration_no']] = 1;

				$total['transporter_rate'] = bcadd($total['transporter_rate'], $r['transporter_rate']);
				$total['party_rate']       = bcadd($total['party_rate'], $r['party_rate']);
				$total['self_adv']         = bcadd($total['self_adv'], $r['self_adv']);
				$total['party_adv']        = bcadd($total['party_adv'], $r['party_adv']);
				$total['pump_adv']         = bcadd($total['pump_adv'], $r['pump_adv']);
				$total['allowance']        = bcadd($total['allowance'], $r['allowance']);
				$total['balance']          = bcadd($total['balance'], $r['balance']);

				$group['transporter_rate'] = bcadd($group['transporter_rate'], $r['transporter_rate']);
				$group['party_rate']       = bcadd($group['party_rate'], $r['party_rate']);
				$group['self_adv']         = bcadd($group['self_adv'], $r['self_adv']);
				$group['party_adv']        = bcadd($group['party_adv'], $r['party_adv']);
				$group['pump_adv']         = bcadd($group['pump_adv'], $r['pump_adv']);
				$group['allowance']        = bcadd($group['allowance'], $r['allowance']);
				$group['balance']          = bcadd($group['balance'], $r['balance']);

				$j = 'A';
				$sheet->setCellValue($j++.$i, $sr_no++);
				$sheet->setCellValue($j++.$i, $r['date']);
				$sheet->setCellValue($j++.$i, $r['party_reference_no']);
				$sheet->setCellValue($j++.$i, $r['bill_no']);
				$sheet->setCellValue($j++.$i, $r['bill_date']);
				$sheet->setCellValue($j++.$i, $r['container_no']);
				$sheet->setCellValue($j++.$i, $r['container_size']);
				$sheet->setCellValue($j++.$i, $r['from_location']);
				$sheet->setCellValue($j++.$i, $r['to_location']);
				$sheet->setCellValue($j++.$i, $r['party_name']);
				$sheet->setCellValue($j++.$i, $r['registration_no']);
				$sheet->setCellValue($j++.$i, $r['lr_no']);
				$sheet->setCellValue($j++.$i, $r['party_rate']);
				$sheet->setCellValue($j++.$i, $r['transporter_rate']);
				$sheet->setCellValue($j++.$i, $r['self_adv']);
				$sheet->setCellValue($j++.$i, $r['party_adv']);
				$sheet->setCellValue($j++.$i, $r['pump_adv']);
				$sheet->setCellValue($j++.$i, $r['allowance']);
				$sheet->setCellValue($j++.$i, $r['balance']);

				$registration_no = $r['registration_no'];
				$i++;
			}

			$total['expenses'] = bcadd($total['expenses'], (isset($vehicles['closing']) ? $vehicles['closing'] : 0));

			$j = 'K';
			$sheet->setCellValue($j++.$i, '(' . $registration_no . ') Total');
			$j++;
			$sheet->setCellValue($j++.$i, $group['party_rate']);
			$sheet->setCellValue($j++.$i, $group['transporter_rate']);
			$sheet->setCellValue($j++.$i, $group['self_adv']);
			$sheet->setCellValue($j++.$i, $group['party_adv']);
			$sheet->setCellValue($j++.$i, $group['pump_adv']);
			$sheet->setCellValue($j++.$i, $group['allowance']);
			$sheet->setCellValue($j++.$i, $group['balance']);

			$i++;
			$j = 'R';
			$sheet->setCellValue($j++.$i, '(' . $registration_no . ') Expenses');
			$sheet->setCellValue($j++.$i, (isset($vehicles['closing']) ? $vehicles['closing'] : 0));

			$i++;
			$j = 'R';
			$sheet->setCellValue($j++.$i, '(' . $registration_no . ')  Net Amount');
			$sheet->setCellValue($j++.$i, $group['balance'] - (isset($vehicles['closing']) ? $vehicles['closing'] : 0));

			$i++;
		} 
		
		$j = 'K';
		$sheet->setCellValue($j++.$i, 'Grand Total');
		$j++;
		$sheet->setCellValue($j++.$i, $total['party_rate']);
		$sheet->setCellValue($j++.$i, $total['transporter_rate']);
		$sheet->setCellValue($j++.$i, $total['self_adv']);
		$sheet->setCellValue($j++.$i, $total['party_adv']);
		$sheet->setCellValue($j++.$i, $total['pump_adv']);
		$sheet->setCellValue($j++.$i, $total['allowance']);
		$sheet->setCellValue($j++.$i, $total['balance']);

		$i++;
		$j = 'R';
		$sheet->setCellValue($j++.$i, 'Total Expenses');
		$sheet->setCellValue($j++.$i, $total['expenses']);

		$i++;
		$j = 'R';
		$sheet->setCellValue($j++.$i, 'Net Amount');
		$sheet->setCellValue($j++.$i, ($total['balance'] - $total['expenses']));

		$sheet->getStyle('A1:'.$j.$i)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
		$sheet->getStyle('A1:'.$j.$i)->applyFromArray($styleSheet);
		$sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
		$sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

		// redirect output to client browser
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.$filename.'"');
		header('Cache-Control: max-age=0');
		$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
		$writer->save('php://output');
	}
}
