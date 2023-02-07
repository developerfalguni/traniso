<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use mikehaertl\wkhtmlto\Pdf;
class Container_volume_billwise extends MY_Controller {
	function __construct() {
		parent::__construct();

		$this->_fields = array(
			'type'           => 'J.type',
			'party'          => 'P.name',
			'debit_note'     => 'debit_note',
			'invoice'        => 'invoice',
		);
	}
	
	function index() {
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

		$data['rows'] = $this->_get($data['from_date'], $data['to_date'], $data['search'], $parsed_search);

		$default_company = $this->session->userdata("default_company");
		$data['years']   = explode('_', $default_company['financial_year']);

		$this->load->helper('datefn');
		$data['javascript'] = ['bootstrap-daterangepicker/daterangepicker.js'];
		$data['stylesheet'] = ['bootstrap-daterangepicker/daterangepicker.css'];

		$data['page_title'] = humanize($this->_class);
		$data['page']       = $this->_clspath.$this->_class;
		$data['docs_url']   = $this->_docs;
		$this->load->view('index', $data);
	}

	function _get($from_date, $to_date, $search, $parsed_search) {
		$sql = "SELECT J.id AS job_id, J.type, J.id2_format, P.name AS party_name, 
			J.container_20, J.container_40,
			GROUP_CONCAT(DISTINCT V.id2_format SEPARATOR ', ') AS invoice_no,
			DATE_FORMAT(MIN(V.date), '%d-%m-%Y') AS invoice_date,
			SUM(V.amount) AS amount
		FROM jobs J
			INNER JOIN parties P ON J.party_id = P.id
			INNER JOIN vouchers V ON J.id = V.job_id 
			INNER JOIN voucher_books VB ON (V.voucher_book_id = VB.id AND VB.job_type IN ('Import', 'Export', 'Import-Export'))
			INNER JOIN voucher_types VT ON (VB.voucher_type_id = VT.id AND VT.name = 'Invoice')
		WHERE V.date >= ? AND V.date <= ?";
		$where = ' AND (';
		if (is_array($parsed_search)) {
			foreach($parsed_search as $key => $value)
				if (isset($this->_fields[$key]) AND ($key == 'type' OR $key == 'party'))
					$where .= $this->_fields[$key] . " LIKE '%$value%' AND ";
			if (strlen($where) > 6)
				$sql .= substr($where, 0, strlen($where) - 5) . ')';
		}
		$sql .= "
		GROUP BY J.id
		ORDER BY J.id2";
		$query = $this->db->query($sql, array(
			convDate($from_date), convDate($to_date),
		));
		return $query->result_array();
	}

	function preview($pdf = 0, $invoice = 0) {
		$default_company = $this->session->userdata('default_company');
		$data['company'] = $this->kaabar->getRow('companies', $default_company['id']);
		$from_date       = $this->session->userdata($this->_class.'_from_date');
		$to_date         = $this->session->userdata($this->_class.'_to_date');
		$search          = $this->session->userdata($this->_class.'_search');
		$parsed_search   = $this->kaabar->parseSearch($search);

		$data['rows']        = $this->_get($from_date, $to_date, $search, $parsed_search);
		$data['invoice']     = $invoice;
		$data['page_title']  = humanize($this->_class . ' Report');
		
		if ($pdf) {
			$filename = humanize($this->_class) . '_' . date('d-m-Y');
			$html = $this->load->view($this->_clspath.$this->_class.'_preview', $data, true);

			$pdf = new Pdf(array(
				'no-outline',
				'binary'      => FCPATH.'wkhtmltopdf',
				'orientation' => 'Landscape',
			));
			$pdf->addPage($html);
			$pdf->send("$filename.pdf");
			echo closeWindow();
		}
		else {
			$this->load->view($this->_clspath.$this->_class.'_preview', $data);
		}
	}
	
	function excel() {
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

		$from_date     = $this->session->userdata($this->_class.'_from_date');
		$to_date       = $this->session->userdata($this->_class.'_to_date');
		$search        = $this->session->userdata($this->_class.'_search');
		$parsed_search = $this->kaabar->parseSearch($search);

		$rows = $this->_get($from_date, $to_date, $search, $parsed_search);

		// Header
		$sheet->setCellValue('A1', 'No');
		$sheet->setCellValue('B1', 'Job No');
		$sheet->setCellValue('C1', 'Invoice No &amp; Date');
		$sheet->setCellValue('D1', 'Importer');
		$sheet->setCellValue('E1', 'Line');
		$sheet->setCellValue('F1', 'BL No &amp; Date');
		$sheet->setCellValue('G1', 'POD');
		$sheet->setCellValue('H1', 'POS');
		$sheet->setCellValue('I1', 'Containers');
		$sheet->setCellValue('K1', 'Status');
		$sheet->setCellValue('L1', 'Expenses');
		$sheet->setCellValue('S1', 'Income');
		$sheet->setCellValue('I2', 'C.20');
		$sheet->setCellValue('J2', 'C.40');
		$sheet->setCellValue('L2', 'Bill No');
		$sheet->setCellValue('M2', 'Date');
		$sheet->setCellValue('N2', 'Party Name');
		$sheet->setCellValue('O2', 'Bill Item');
		$sheet->setCellValue('P2', 'Amt');
		$sheet->setCellValue('Q2', 'S.Tax');
		$sheet->setCellValue('R2', 'Total');
		$sheet->setCellValue('S2', 'Bill No');
		$sheet->setCellValue('T2', 'Date');
		$sheet->setCellValue('U2', 'Bill Item');
		$sheet->setCellValue('V2', 'Amt');
		$sheet->setCellValue('W2', 'S.Tax');
		$sheet->setCellValue('X2', 'Total');

		$sheet->getStyle('A1:X2')->applyFromArray($styleHeading);
		// $sheet->getColumnDimension($j)->setAutoSize(true);
		
		// Data
		$i = 3;
		$srno = 1;
		$total = array(
			'container_20' => 0,
			'container_40' => 0,
			'expense'      => 0,
			'income'       => 0,
		);
		foreach ($rows as $r) {
			$sheet->setCellValue('A'.$i, $srno++);
			$sheet->setCellValue('B'.$i, $r['id2_format']);
			$sheet->setCellValue('C'.$i, $r['invoice_no_date']);
			$sheet->setCellValue('D'.$i, $r['party_name']);
			$sheet->setCellValue('E'.$i, $r['line']);
			$sheet->setCellValue('F'.$i, $r['bl_no_date']);
			$sheet->setCellValue('G'.$i, $r['pol']);
			$sheet->setCellValue('H'.$i, $r['pos']);
			$sheet->setCellValue('I'.$i, $r['container_20']);
			$sheet->setCellValue('J'.$i, $r['container_40']);
			$sheet->setCellValue('K'.$i, $r['status']);

			$j = $i;
			$exp_sub_total = array(
				'amount'     => 0,
				'exp_stax'   => 0,
				'exp_amount' => 0,
			);
			if (isset($r['Expenses'])) {
				foreach ($r['Expenses'] as $e) {
					$exp_sub_total['amount']     += $e['amount'];
					$exp_sub_total['exp_stax']   += $e['exp_stax'];
					$exp_sub_total['exp_amount'] += $e['exp_amount'];
					$total['expense']        += $e['exp_amount'];

					$sheet->setCellValue('L'.$j, $e['exp_no']);
					$sheet->setCellValue('M'.$j, $e['exp_date']);
					$sheet->setCellValue('N'.$j, $e['exp_party_name']);
					$sheet->setCellValue('O'.$j, $e['exp_particulars']);
					$sheet->setCellValue('P'.$j, $e['amount']);
					$sheet->setCellValue('Q'.$j, $e['exp_stax']);
					$sheet->setCellValue('R'.$j, $e['exp_amount']);
					
					if ($e['audited'] == 'Yes') {
						$sheet->getStyle('L'.$j)->getFont()->getColor()->setARGB('09A016');
						$sheet->getStyle('M'.$j)->getFont()->getColor()->setARGB('09A016');
						$sheet->getStyle('N'.$j)->getFont()->getColor()->setARGB('09A016');
						$sheet->getStyle('O'.$j)->getFont()->getColor()->setARGB('09A016');
						$sheet->getStyle('P'.$j)->getFont()->getColor()->setARGB('09A016');
						$sheet->getStyle('Q'.$j)->getFont()->getColor()->setARGB('09A016');
						$sheet->getStyle('R'.$j)->getFont()->getColor()->setARGB('09A016');
					}
					$j++;
				}
			}
			
			$k = $i;
			$bill_sub_total = array(
				'amount'      => 0,
				'bill_stax'   => 0,
				'bill_amount' => 0,
			);
			if (isset($r['Bills'])) {
				foreach ($r['Bills'] as $i => $b) {
					$bill_sub_total['amount']      += $b['amount'];
					$bill_sub_total['bill_stax']   += $b['bill_stax'];
					$bill_sub_total['bill_amount'] += $b['bill_amount'];
					$total['income']          += $b['bill_amount'];

					$sheet->setCellValue('S'.$k, $b['bill_no']);
					$sheet->setCellValue('T'.$k, $b['bill_date']);
					$sheet->setCellValue('U'.$k, $b['bill_particulars']);
					$sheet->setCellValue('V'.$k, $b['amount']);
					$sheet->setCellValue('W'.$k, $b['bill_stax']);
					$sheet->setCellValue('X'.$k, $b['bill_amount']);

					if ($b['audited'] == 'Yes') {
						$sheet->getStyle('S'.$k)->getFont()->getColor()->setARGB('09A016');
						$sheet->getStyle('T'.$k)->getFont()->getColor()->setARGB('09A016');
						$sheet->getStyle('U'.$k)->getFont()->getColor()->setARGB('09A016');
						$sheet->getStyle('V'.$k)->getFont()->getColor()->setARGB('09A016');
						$sheet->getStyle('W'.$k)->getFont()->getColor()->setARGB('09A016');
						$sheet->getStyle('X'.$k)->getFont()->getColor()->setARGB('09A016');
					}
					$k++;
				}
			}
			
			$i = max($j, $k);

			$sheet->setCellValue('P'.$i, $exp_sub_total['amount']);
			$sheet->setCellValue('Q'.$i, $exp_sub_total['exp_stax']);
			$sheet->setCellValue('R'.$i, $exp_sub_total['exp_amount']);
			
			$sheet->setCellValue('V'.$i, $bill_sub_total['amount']);
			$sheet->setCellValue('W'.$i, $bill_sub_total['bill_stax']);
			$sheet->setCellValue('X'.$i, $bill_sub_total['bill_amount']);

			$sheet->setCellValue('Y'.$i, "=V$i-P$i");
			$sheet->setCellValue('Z'.$i, "=Y$i/V$i");

			$sheet->getStyle("K$i:Z$i")->applyFromArray($styleHeading);

			$i++;
		}
		$sheet->setCellValue('I'.$i, $total['container_20']);
		$sheet->setCellValue('J'.$i, $total['container_40']);
		$sheet->setCellValue('R'.$i, $total['expense']);
		$sheet->setCellValue('V'.$i, $total['income']);

		$sheet->getStyle('A1:Z'.$i)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
		$sheet->getStyle('A1:Z'.$i)->applyFromArray($styleSheet);
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
