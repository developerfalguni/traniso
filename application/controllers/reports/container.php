<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use mikehaertl\wkhtmlto\Pdf;
class Container extends MY_Controller {
	var $_fields;
	var $_c_fields;

	function __construct() {
		parent::__construct();
		
		$this->_fields = array(
			'type'     => 'J.type',
			'group'    => 'P.group_name',
			'party'    => 'P.name',
			'vessel'   => 'CONCAT(V.name, " ", V.voyage_no)',
			'port'     => 'IP.name',
			'category' => 'PRD.category',
			'product'  => 'PRD.name',
			'cfs'      => 'CFS.name',
			'cha'      => 'CHA.name'
		);

		$this->_c_fields = array(
			'type'  => 'J.type',
			'party' => 'P.name',
			'line'  => 'LINE.name',
			'cfs'   => 'CFS.name',
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
		
		$data['rows'] = $this->_getContainer($from_date, $to_date, $search, $parsed_search);

		$default_company = $this->session->userdata("default_company");
		$data['years']   = explode('_', $default_company['financial_year']);

		$this->load->helper('datefn');
		$data['javascript'] = ['bootstrap-daterangepicker/daterangepicker.js'];
		$data['stylesheet'] = ['bootstrap-daterangepicker/daterangepicker.css'];

		$data['page_title'] = "Container Register";
		$data['page']       = $this->_clspath.$this->_class;
		$data['docs_url']   = $this->_docs;
		$this->load->view('index', $data);
	}

	function preview($pdf = 0) {
		$from_date     = $this->session->userdata($this->_class.'_from_date');
		$to_date       = $this->session->userdata($this->_class.'_to_date');
		$search 	   = $this->session->userdata($this->_class.'_search');
		$parsed_search = $this->kaabar->parseSearch($search);

		$data['rows']  = $this->_getContainer($from_date, $to_date, $search, $parsed_search);

		$default_company = $this->session->userdata("default_company");
		$data['company'] = $this->kaabar->getRow('companies', $default_company['id']);

		$data['page_title'] = humanize($this->_class . ' Register');
		$data['page_desc']  = "For the Period $from_date - $to_date";

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
		$default_company = $this->session->userdata('default_company');
		$company = $this->kaabar->getRow('companies', $default_company['id']);

		$from_date     = $this->session->userdata($this->_class.'_from_date');
		$to_date       = $this->session->userdata($this->_class.'_to_date');
		$search 	   = $this->session->userdata($this->_class.'_search');
		$parsed_search = $this->kaabar->parseSearch($search);

		$rows       = $this->_getContainer($from_date, $to_date, $search, $parsed_search, true);
		$page_title = "Container Register";

		$filename    = "container_register.xlsx";
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

		$sheet->setCellValue('A1', 'Invoice No');
		$sheet->getColumnDimension('A')->setAutoSize(true);
		$sheet->setCellValue('B1', 'Invoice Date');
		$sheet->getColumnDimension('B')->setAutoSize(true);
		$sheet->setCellValue('C1', 'Party');
		$sheet->getColumnDimension('C')->setAutoSize(true);
		$sheet->setCellValue('D1', 'Vessel');
		$sheet->getColumnDimension('D')->setAutoSize(true);
		$sheet->setCellValue('E1', 'BL No');
		$sheet->getColumnDimension('E')->setAutoSize(true);
		$sheet->setCellValue('F1', 'CFS');
		$sheet->getColumnDimension('F')->setAutoSize(true);
		$sheet->setCellValue('G1', 'CFS');
		$sheet->getColumnDimension('G')->setAutoSize(true);
		$sheet->setCellValue('H1', 'Product');
		$sheet->getColumnDimension('H')->setAutoSize(true);
		$sheet->setCellValue('I1', 'Port');
		$sheet->getColumnDimension('I')->setAutoSize(true);
		$sheet->setCellValue('J1', '20');
		$sheet->getColumnDimension('J')->setAutoSize(true);
		$sheet->setCellValue('K1', '40');
		$sheet->getColumnDimension('K')->setAutoSize(true);

		$sheet->getStyle('A1:K1')->applyFromArray($styleHeading);

		$i = 2;
		foreach ($rows as $group_name => $group_rows) {
			$sheet->setCellValueByColumnAndRow(0, $i, $group_name);
			$sheet->getStyle('A'.$i.':K'.$i)->applyFromArray($styleHeading);
			$i++;
			foreach ($group_rows as $r) {
				$sheet->setCellValueByColumnAndRow(0, $i, $r['id2_format']);
				$sheet->setCellValueByColumnAndRow(1, $i, $r['date']);
				$sheet->setCellValueByColumnAndRow(2, $i, $r['party_name']);
				$sheet->setCellValueByColumnAndRow(3, $i, $r['vessel_voyage']);
				$sheet->setCellValueByColumnAndRow(4, $i, $r['bl_no']);
				$sheet->setCellValueByColumnAndRow(5, $i, $r['cfs_name']);
				$sheet->setCellValueByColumnAndRow(6, $i, $r['cha_name']);
				$sheet->setCellValueByColumnAndRow(7, $i, $r['product_name']);
				$sheet->setCellValueByColumnAndRow(8, $i, $r['indian_port']);
				$sheet->setCellValueByColumnAndRow(9, $i, $r['container_20']);
				$sheet->setCellValueByColumnAndRow(10, $i, $r['container_40']);
				$i++;
			}
		}
		
		$sheet->getStyle('A1:K'.$i)->applyFromArray($styleSheet);
		$sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
		$sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

		// redirect output to client browser
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="' . $filename.'"');
		header('Cache-Control: max-age=0');
		$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
		$writer->save('php://output');
	}

		function _getContainer($from_date, $to_date, $search, $parsed_search) {
		$default_company = $this->session->userdata('default_company');

		$sql = "SELECT CONCAT(VT.name, '/edit/', VO.voucher_book_id, '/', VO.id2, '/', VO.id3) AS url, 
			VO.id2_format, DATE_FORMAT(VO.date, '%Y-%m') AS date,
			P.group_name, P.name AS party_name, COALESCE(ED.vessel_name, CONCAT(V.prefix, ' ', V.name, ' ', V.voyage_no)) AS vessel_voyage, 
			J.bl_no, PRD.name AS product_name, PRD.category,
			CFS.name AS cfs_name, CHA.name AS cha_name, IP.name AS indian_port, J.container_20, J.container_40
		FROM jobs J INNER JOIN indian_ports IP ON J.indian_port_id = IP.id
			INNER JOIN products PRD ON J.product_id = PRD.id
			INNER JOIN vouchers VO ON J.id = VO.job_id
			INNER JOIN voucher_books VB ON VO.voucher_book_id = VB.id
			INNER JOIN voucher_types VT ON VB.voucher_type_id = VT.id
			INNER JOIN ledgers P ON VO.dr_ledger_id = P.id
			LEFT OUTER JOIN vessels V ON J.vessel_id = V.id
			LEFT OUTER JOIN export_details ED ON J.id = ED.job_id
			LEFT OUTER JOIN agents CFS ON J.cfs_id = CFS.id
			LEFT OUTER JOIN agents CHA ON J.cha_id = CHA.id
		WHERE (VB.company_id = ? AND VO.date >= ? AND VO.date <= ? AND VB.voucher_type_id = 4 AND J.cargo_type = 'Container')";
				$where = ' AND (';
				if (is_array($parsed_search)) {
					foreach($parsed_search as $key => $value)
						if (isset($this->_fields[$key]))
							$where .= $this->_fields[$key] . " LIKE '%$value%' AND ";
					if (strlen($where) > 6)
						$sql .= substr($where, 0, strlen($where) - 5) . ')';
				}
				$sql .= "
		GROUP BY J.id
		ORDER BY VO.date, VO.id2_format";
		$query = $this->db->query($sql, array(
			$default_company['id'], convDate($from_date), convDate($to_date)
		));
		$rows = $query->result_array();
		$result = array();
		foreach ($rows as $row) {
			$result[$row['group_name']][] = $row;
		}
		return $result;
	}


	function cfs() {
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
		$data['search_fields'] = $this->_c_fields;

		if (is_array($parsed_search)) {
			$search = '';
			foreach ($parsed_search as $key => $value) {
				$search .= $key.':'.$value.' ';
			}
			$data['search'] = $search;
		}
		
		$data['rows'] = $this->_getCFSContainer($data['from_date'], $data['to_date'], $search, $parsed_search);

		$default_company = $this->session->userdata("default_company");
		$data['years']   = explode('_', $default_company['financial_year']);

		$this->load->helper('datefn');
		$data['javascript'] = ['bootstrap-daterangepicker/daterangepicker.js'];
		$data['stylesheet'] = ['bootstrap-daterangepicker/daterangepicker.css'];

		$data['page_title'] = "Container Register";
		$data['page'] = $this->_clspath.$this->_class.'_cfs';
		$data['docs_url'] = $this->_docs;
		$this->load->view('index', $data);
	}

	function preview2($pdf = 0) {
		$from_date     = $this->session->userdata($this->_class.'_from_date');
		$to_date       = $this->session->userdata($this->_class.'_to_date');
		$search 	   = $this->session->userdata($this->_class.'_search');
		$parsed_search = $this->kaabar->parseSearch($search);

		$data['rows']  = $this->_getCFSContainer($from_date, $to_date, $search, $parsed_search);

		$default_company = $this->session->userdata("default_company");
		$data['company'] = $this->kaabar->getRow('companies', $default_company['id']);

		$data['page_title'] = humanize($this->_class . ' Register');
		$data['page_desc']  = "For the Period $from_date - $to_date";

		if ($pdf) {
			$filename = $data['page_title'];
			$html = $this->load->view($this->_clspath.$this->_class.'_cfs_preview', $data, true);
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
			$this->load->view($this->_clspath.$this->_class.'_cfs_preview', $data);
		}
	}

	function excel2() {
		$default_company = $this->session->userdata('default_company');
		$company = $this->kaabar->getRow('companies', $default_company['id']);

		$from_date     = $this->session->userdata($this->_class.'_from_date');
		$to_date       = $this->session->userdata($this->_class.'_to_date');
		$search 	   = $this->session->userdata($this->_class.'_search');
		$parsed_search = $this->kaabar->parseSearch($search);

		$rows       = $this->_getCFSContainer($from_date, $to_date, $search, $parsed_search, true);
		$page_title = "Container Register";

		$filename    = "container_register.xlsx";
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

		$sheet->setCellValue('A1', 'Invoice No');
		$sheet->getColumnDimension('A')->setAutoSize(true);
		$sheet->setCellValue('B1', 'Invoice Date');
		$sheet->getColumnDimension('B')->setAutoSize(true);
		$sheet->setCellValue('C1', 'Party');
		$sheet->getColumnDimension('C')->setAutoSize(true);
		$sheet->setCellValue('D1', 'Vessel');
		$sheet->getColumnDimension('D')->setAutoSize(true);
		$sheet->setCellValue('E1', 'BL');
		$sheet->getColumnDimension('E')->setAutoSize(true);
		$sheet->setCellValue('F1', 'BE / SB');
		$sheet->getColumnDimension('F')->setAutoSize(true);
		$sheet->setCellValue('G1', 'CFS');
		$sheet->getColumnDimension('G')->setAutoSize(true);
		$sheet->setCellValue('H1', 'CFS');
		$sheet->getColumnDimension('H')->setAutoSize(true);
		$sheet->setCellValue('I1', 'Product');
		$sheet->getColumnDimension('I')->setAutoSize(true);
		$sheet->setCellValue('J1', 'Port');
		$sheet->getColumnDimension('J')->setAutoSize(true);
		$sheet->setCellValue('K1', '20');
		$sheet->getColumnDimension('K')->setAutoSize(true);
		$sheet->setCellValue('L1', '40');
		$sheet->getColumnDimension('L')->setAutoSize(true);

		$sheet->getStyle('A1:L1')->applyFromArray($styleHeading);

		$i = 2;
		foreach ($rows as $group_name => $group_rows) {
			$sheet->setCellValueByColumnAndRow(0, $i, $group_name);
			$sheet->getStyle('A'.$i.':I'.$i)->applyFromArray($styleHeading);
			$i++;
			foreach ($group_rows as $r) {
				$sheet->setCellValueByColumnAndRow(0, $i, $r['id2_format']);
				$sheet->setCellValueByColumnAndRow(1, $i, $r['date']);
				$sheet->setCellValueByColumnAndRow(2, $i, $r['party_name']);
				$sheet->setCellValueByColumnAndRow(3, $i, $r['vessel_voyage']);
				$sheet->setCellValueByColumnAndRow(4, $i, $r['bl_no']);
				$sheet->setCellValueByColumnAndRow(5, $i, $r['be_sb']);
				$sheet->setCellValueByColumnAndRow(6, $i, $r['cfs_name']);
				$sheet->setCellValueByColumnAndRow(7, $i, $r['cha_name']);
				$sheet->setCellValueByColumnAndRow(8, $i, $r['product_name']);
				$sheet->setCellValueByColumnAndRow(9, $i, $r['indian_port']);
				$sheet->setCellValueByColumnAndRow(10, $i, $r['container_20']);
				$sheet->setCellValueByColumnAndRow(11, $i, $r['container_40']);
				$i++;
			}
		}
		
		$sheet->getStyle('A1:L'.$i)->applyFromArray($styleSheet);
		$sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
		$sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

		// redirect output to client browser
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="' . $filename.'"');
		header('Cache-Control: max-age=0');
		$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
		$writer->save('php://output');
	}

	function _getCFSContainer($from_date, $to_date, $search, $parsed_search) {
		$default_company = $this->session->userdata('default_company');

		$sql = "SELECT J.id AS job_id, J.type, CONCAT(VT.name, '/edit/', VO.voucher_book_id, '/', VO.id2, '/', VO.id3) AS url, 
			VO.id2_format, VO.invoice_no, DATE_FORMAT(VO.invoice_date, '%d-%m-%Y') AS invoice_date,
			P.name AS party_name, CONCAT(V.prefix, ' ', V.name, ' ', V.voyage_no) AS vessel_voyage, 
			IF(LENGTH(J.bl_no) = 0, 'BL Missing', J.bl_no) AS bl_no, IF(J.type = 'Import', J.be_no, J.sb_no) AS be_sb,
			CFS.name AS cfs_name, LINE.name AS line_name, J.container_20, J.container_40
		FROM agents CFS INNER JOIN ledgers L ON CFS.id = L.agent_id
			INNER JOIN vouchers VO ON (VO.date >= ? AND VO.date <= ? AND VO.cr_ledger_id = L.id)
			INNER JOIN voucher_books VB ON (VB.company_id = ? AND VB.voucher_type_id = 5 AND VO.voucher_book_id = VB.id)
			INNER JOIN voucher_types VT ON VB.voucher_type_id = VT.id
			INNER JOIN voucher_details VJD ON VO.id = VJD.voucher_id
			INNER JOIN jobs J ON (J.cargo_type = 'Container' AND VJD.job_id = J.id)
			INNER JOIN parties P ON J.party_id = P.id
			INNER JOIN vessels V ON J.vessel_id = V.id
			LEFT OUTER JOIN agents LINE ON J.line_id = LINE.id";
		$where = ' WHERE (';
		if (is_array($parsed_search)) {
			foreach($parsed_search as $key => $value)
				if (isset($this->_c_fields[$key]))
					$where .= $this->_c_fields[$key] . " LIKE '%$value%' AND ";
			if (strlen($where) > 8)
				$sql .= substr($where, 0, strlen($where) - 5) . ')';
		}
		$sql .= "
		GROUP BY J.id
		ORDER BY VO.date, VO.id2";
		$query = $this->db->query($sql, array(
			convDate($from_date), convDate($to_date), $default_company['id']
		));
		$rows = $query->result_array();
		$result = array();
		foreach ($rows as $row) {
			$result[$row['group_name']][] = $row;
		}
		return $result;
	}
}
