<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use mikehaertl\wkhtmlto\Pdf;
class Bond extends MY_Controller {
	var $_fields;
	var $_parsed_search;

	function __construct() {
		parent::__construct();
		
		$this->_fields = array(
			'type'      => 'J.cargo_type',
			'party'     => 'P.name',
			'vessel'    => "CONCAT(V.prefix, ' ', V.name, ' ', V.voyage_no)",
			'product'   => 'PRD.name',
			'cha'       => 'CHA.name',
			'port'      => 'IP.name',
			'warehouse' => 'W.name',
		);
	}
	
	function index() {
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
			$search    = $this->session->userdata($this->_class.'_search');
		}

		$data['from_date']     = $from_date ? $from_date : date('01-m-Y');
		$data['to_date']       = $to_date ? $to_date : date('d-m-Y');
		$data['search']        = $search;
		$this->_parsed_search  = $this->kaabar->parseSearch($search);
		$data['parsed_search'] = $this->_parsed_search;
		$data['search_fields'] = $this->_fields;

		if (is_array($this->_parsed_search)) {
			$search = '';
			foreach ($this->_parsed_search as $key => $value) {
				$search .= $key.':'.$value.' ';
			}
			$data['search'] = $search;
		}

		$data['rows'] = $this->_getBond($data['from_date'], $data['to_date'], $search);

		$this->load->helper('datefn');
		$data['javascript'] = ['bootstrap-daterangepicker/daterangepicker.js'];
		$data['stylesheet'] = ['bootstrap-daterangepicker/daterangepicker.css'];

		$data['page_title'] = "InBond / ExBond Report";
		$data['page']       = $this->_clspath.$this->_class;
		$data['docs_url']   = $this->_docs;
		$this->load->view('index', $data);
	}

	function preview($pdf = 0) {
		$from_date = $this->session->userdata($this->_class.'_from_date');
		$to_date   = $this->session->userdata($this->_class.'_to_date');
		$search    = $this->session->userdata($this->_class.'_search');
		$data['rows']  = $this->_getBond($from_date, $to_date, $search);

		$default_company = $this->session->userdata("default_company");
		$data['company'] = $this->kaabar->getRow('companies', $default_company['id']);

		$data['page_title'] = humanize($this->_class . ' Register');
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
			$this->kaabar->save($this->_table, array('printed' => 'Yes'), array('id' => $id));
		}
		else {
			$this->load->view($this->_clspath.$this->_class.'_preview', $data);
		}
	}

	function excel() {
		$from_date = $this->session->userdata($this->_class.'_from_date');
		$to_date   = $this->session->userdata($this->_class.'_to_date');
		$search    = $this->session->userdata($this->_class.'_search');
		$rows      = $this->_getBond($from_date, $to_date, $search);

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

		$sheet->setCellValue('A1', 'No');
		$sheet->setCellValue('B1', 'Type');
		$sheet->setCellValue('C1', 'Cargo Type');
		$sheet->setCellValue('D1', 'Group Name');
		$sheet->setCellValue('E1', 'Party Name');
		$sheet->setCellValue('F1', 'Vessel Name');
		$sheet->setCellValue('G1', 'BL No');
		$sheet->setCellValue('H1', 'CHA');
		$sheet->setCellValue('I1', 'Product');
		$sheet->setCellValue('J1', 'Category');
		$sheet->setCellValue('K1', 'Packages');
		$sheet->setCellValue('L1', 'Net Weight');
		$sheet->setCellValue('M1', 'Containers');
		$sheet->setCellValue('N1', 'Reimbersment');

		$sheet->getStyle('A1:M1')->applyFromArray($styleHeading);

		// Data
		$i = 2;
		foreach ($rows as $r) {
			$sheet->setCellValue('A'.$i, $i-1);
			$sheet->setCellValue('B'.$i, $r['type']);
			$sheet->setCellValue('C'.$i, $r['cargo_type']);
			$sheet->setCellValue('D'.$i, $r['group_name']);
			$sheet->setCellValue('E'.$i, $r['party_name']);
			$sheet->setCellValue('F'.$i, $r['vessel_name']);
			$sheet->setCellValue('G'.$i, $r['bl_no']);
			$sheet->setCellValue('H'.$i, $r['cha_name']);
			$sheet->setCellValue('I'.$i, $r['product']);
			$sheet->setCellValue('J'.$i, $r['category']);
			$sheet->setCellValue('K'.$i, $r['packages']);
			$sheet->setCellValue('L'.$i, $r['net_weight']);
			$sheet->setCellValue('M'.$i, $r['containers']);
			$sheet->setCellValue('N'.$i, $r['reimbersment']);
			$i++;
		}


		//$sheet->getStyle('A1:G1000')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
		$sheet->getStyle('A1:N'.$i)->applyFromArray($styleSheet);
		$sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
		$sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);


		// redirect output to client browser
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="' . $filename.'"');
		header('Cache-Control: max-age=0');
		$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
		$writer->save('php://output');
	}

	function _getBond($from_date, $to_date, $search) {
		$company = $this->session->userdata('default_company');
		$result  = array();
		$jobs_id = array();

		$sql = "SELECT J.id, J.cargo_type, P.name AS party_name, GROUP_CONCAT(HSL.name SEPARATOR '<br />') AS high_seas, 
			CONCAT(V.prefix, ' ', V.name, ' ', V.voyage_no) AS vessel_name, V.berth_no,
			J.bl_no, J.packages, IF(J.cbm > 0, J.cbm, J.net_weight) AS net_weight, 
			J.be_no, DATE_FORMAT(J.be_date, '%d-%m-%Y') AS be_date,
			CHA.name AS cha_name, PRD.name AS product_name,  W.name AS warehouse, IP.name AS indian_port
		FROM jobs J INNER JOIN parties P ON J.party_id = P.id
			INNER JOIN products PRD ON J.product_id = PRD.id
			INNER JOIN indian_ports IP ON J.indian_port_id = IP.id
			INNER JOIN vessels V ON J.vessel_id = V.id
			INNER JOIN agents CHA ON J.cha_id = CHA.id
			LEFT OUTER JOIN warehouses W ON J.bond_warehouse_id = W.id
			LEFT OUTER JOIN high_seas HS ON J.id = HS.job_id
			LEFT OUTER JOIN parties HSL ON HS.party_id = HSL.id
		WHERE (J.type = 'Import' AND J.be_type = 'In-Bond' AND J.be_date >= ? AND J.be_date <= ?) ";
		$where = ' AND (';
		if (is_array($this->_parsed_search)) {
			foreach($this->_parsed_search as $key => $value)
				if (isset($this->_fields[$key]))
					$where .= $this->_fields[$key] . " LIKE '%$value%' AND ";
			if (strlen($where) > 6)
				$sql .= substr($where, 0, strlen($where) - 5) . ')';
		}
		$sql .= "
		GROUP BY J.id";
		$query = $this->db->query($sql, array(convDate($from_date), convDate($to_date)));
		$rows = $query->result_array();
		foreach ($rows as $r) {
			$result[$r['id']] = $r;
		}

		if (count($result) > 0) {
			$sql = "SELECT J.id, P.name AS party_name, GROUP_CONCAT(HSL.name SEPARATOR '<br />') AS high_seas, 
				J.packages, IF(J.cbm > 0, J.cbm, J.net_weight) AS net_weight, 
				CONCAT(V.prefix, ' ', V.name, ' ', V.voyage_no) AS vessel_name,
				J.be_no, DATE_FORMAT(J.be_date, '%d-%m-%Y') AS be_date, CHA.name AS cha_name, J.in_bond_job_id,
				PRD.name AS product_name, IP.name AS indian_port
			FROM jobs J INNER JOIN parties P ON J.party_id = P.id
				INNER JOIN agents CHA ON J.cha_id = CHA.id
				INNER JOIN products PRD ON J.product_id = PRD.id
				INNER JOIN indian_ports IP ON J.indian_port_id = IP.id
				INNER JOIN vessels V ON J.vessel_id = V.id
				LEFT OUTER JOIN high_seas HS ON J.id = HS.job_id
				LEFT OUTER JOIN parties HSL ON HS.party_id = HSL.id
			WHERE J.in_bond_job_id IN (" . implode(',', array_keys($result)) . ")
			GROUP BY J.id";
			$query = $this->db->query($sql);
			$rows = $query->result_array();
			foreach ($rows as $r) {
				$jobs_id[$r['id']] = 1;
				$result[$r['in_bond_job_id']]['ExBond'][$r['id']] = $r;
			}

			if (count($jobs_id) > 0) {
				$sql = "SELECT C.code AS company, V.job_id, VB.voucher_type_id, CONCAT(LOWER(VT.name),'/edit/',VB.id,'/',V.id,'/',V.id3,'/',VB.company_id) AS url, V.id2_format, J.in_bond_job_id
				FROM (((vouchers V INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id)
					INNER JOIN voucher_types VT ON VB.voucher_type_id = VT.id)
					INNER JOIN companies C ON VB.company_id = C.id)
					INNER JOIN jobs J ON V.job_id = J.id
				WHERE VB.company_id IN (1,2) AND VB.voucher_type_id IN (3,4) AND 
					V.job_id IN (" . implode(',', array_keys($jobs_id)) . ")
				GROUP BY V.id";
				$query  = $this->db->query($sql);
				$rows = $query->result_array();
				foreach ($rows as $r) {
					$result[$r['in_bond_job_id']]['ExBond'][$r['job_id']]['vouchers'][$r['voucher_type_id']][] = $r;
				}
			}
		
			$sql = "SELECT C.code AS company, V.job_id, VB.voucher_type_id, CONCAT(LOWER(VT.name),'/edit/',VB.id,'/',V.id,'/',V.id3,'/',VB.company_id) AS url, V.id2_format
			FROM ((vouchers V INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id)
				INNER JOIN voucher_types VT ON VB.voucher_type_id = VT.id)
				INNER JOIN companies C ON VB.company_id = C.id
			WHERE VB.company_id IN (1,2) AND VB.voucher_type_id IN (3,4) AND 
				V.job_id IN (" . implode(',', array_keys($result)) . ")
			GROUP BY V.id";
			$query  = $this->db->query($sql);
			$rows = $query->result_array();
			foreach ($rows as $r) {
				$result[$r['job_id']]['vouchers'][$r['voucher_type_id']][] = $r;
			}
		}
		return $result;
	}
}
