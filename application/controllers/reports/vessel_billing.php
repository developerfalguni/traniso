<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use mikehaertl\wkhtmlto\Pdf;
class Vessel_billing extends MY_Controller {
	var $_fields;
	var $_parsed_search;

	function __construct() {
		parent::__construct();
		
		$this->_fields = array(
			'shipment' => 'J.type',
			'type'     => 'J.cargo_type',
			'group'    => 'PL.group_name',
			'party'    => 'PL.name',
			'category' => 'PRD.category',
			'product'  => 'PRD.name',
			'cha'      => 'CHA.name',
			'port'     => 'IP.name',
			'vessel'   => "CONCAT(V.prefix, ' ', V.name, ' ', V.voyage_no)"
		);
	}
	
	function index() {
		$default_company = $this->session->userdata("default_company");
		$data['company'] = $this->kaabar->getRow('companies', $default_company['id']);
		$data['years']   = explode('_', $default_company['financial_year']);

		$from_date  = null;
		$to_date    = null;
		$bill_items = null;
		$hide_stos  = null;
		$search     = null;

		if ($this->input->post('from_date')) {
			$from_date  = $this->input->post('from_date');
			$to_date    = $this->input->post('to_date');
			$bill_items = $this->input->post('bill_items');
			$hide_stos  = $this->input->post('hide_stos');
			$search     = $this->input->post('search');
			$this->session->set_userdata($this->_class.'_from_date', $from_date);
			$this->session->set_userdata($this->_class.'_to_date', $to_date);
			$this->session->set_userdata($this->_class.'_bill_items', $bill_items);
			$this->session->set_userdata($this->_class.'_hide_stos', $hide_stos);
			$this->session->set_userdata($this->_class.'_search', $search);
		}
		
		if ($from_date == null) {
			$from_date  = $this->session->userdata($this->_class.'_from_date');
			$to_date    = $this->session->userdata($this->_class.'_to_date');
			$bill_items = $this->session->userdata($this->_class.'_bill_items');
			$hide_stos  = $this->session->userdata($this->_class.'_hide_stos');
			$search     = $this->session->userdata($this->_class.'_search');
		}

		$data['from_date']     = $from_date ? $from_date : date('01-m-Y');
		$data['to_date']       = $to_date ? $to_date : date('d-m-Y');
		$data['bill_items']    = ($bill_items ? $bill_items : array('CD', 'CAMEN', 'STAMP', 'STAX', 'STPS', 'STTR'));
		$data['hide_stos']     = ($hide_stos ? 1 : 0);
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

		$data['jobs'] = $this->_getVesselBilling($data['from_date'], $data['to_date'], $data['bill_items'], $data['hide_stos'], $search);

		$this->load->helper('datefn');
		$data['javascript'] = ['bootstrap-daterangepicker/daterangepicker.js'];
		$data['stylesheet'] = ['bootstrap-daterangepicker/daterangepicker.css'];

		$data['page_title'] = "Bills Pending Report";
		$data['page']       = $this->_clspath.$this->_class;
		$data['docs_url']   = $this->_docs;
		$this->load->view('index', $data);
	}

	function preview($pdf = 0) {
		$from_date    = $this->session->userdata($this->_class.'_from_date');
		$to_date      = $this->session->userdata($this->_class.'_to_date');
		$bill_items   = $this->session->userdata($this->_class.'_bill_items');
		$hide_stos    = $this->session->userdata($this->_class.'_hide_stos');
		$search       = $this->session->userdata($this->_class.'_search');
		$data['rows'] = $this->_getVesselBilling($from_date, $to_date, $bill_items, $hide_stos, $search);

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
		$from_date  = $this->session->userdata($this->_class.'_from_date');
		$to_date    = $this->session->userdata($this->_class.'_to_date');
		$bill_items = $this->session->userdata($this->_class.'_bill_items');
		$hide_stos  = $this->session->userdata($this->_class.'_hide_stos');
		$search     = $this->session->userdata($this->_class.'_search');
		$this->_parsed_search  = $this->kaabar->parseSearch($search);
		$data['parsed_search'] = $this->_parsed_search;
		$data['search_fields'] = $this->_fields;
		
		$rows       = $this->_getVesselBilling($from_date, $to_date, $bill_items, $hide_stos, $search);

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
		$sheet->setCellValue('D1', 'Ledger Name');
		$sheet->setCellValue('E1', 'Party Name');
		$sheet->setCellValue('F1', 'Vessel Name');
		$sheet->setCellValue('G1', 'Berth No');
		$sheet->setCellValue('H1', 'BL No');
		$sheet->setCellValue('I1', 'Packages');
		$sheet->setCellValue('J1', 'Net Weight');
		$sheet->setCellValue('K1', 'Containers');
		$sheet->setCellValue('L1', 'Reimbersment');
		$sheet->setCellValue('M1', 'Invoice');
		$sheet->setCellValue('N1', 'ShivShakti');

		$sheet->getStyle('A1:N1')->applyFromArray($styleHeading);

		// Data
		$i = 2;
		foreach ($rows as $r) {
			$sheet->setCellValue('A'.$i, $i-1);
			$sheet->setCellValue('B'.$i, $r['type']);
			$sheet->setCellValue('C'.$i, $r['cargo_type']);

			if (strlen($r['reimbersment_name']) > 0) {
				$sheet->setCellValue('D'.$i, $r['reimbersment_name']);
			}
			else if (strlen($r['invoice_name']) > 0) {
				$sheet->setCellValue('D'.$i, $r['invoice_name']);
			}
			else  {
				$sheet->setCellValue('D'.$i, $r['shivshakti_name']);
			}
			$sheet->setCellValue('E'.$i, $r['party_name']);
			$sheet->setCellValue('F'.$i, $r['vessel_name']);
			$sheet->setCellValue('G'.$i, $r['berth_no']);
			$sheet->setCellValue('H'.$i, $r['bl_no']);
			$sheet->setCellValue('I'.$i, $r['packages']);
			$sheet->setCellValue('J'.$i, $r['net_weight']);
			$sheet->setCellValue('K'.$i, $r['containers']);
			$sheet->setCellValue('L'.$i, $r['reimbersment']);
			$sheet->setCellValue('M'.$i, $r['invoice']);
			$sheet->setCellValue('N'.$i, $r['shivshakti']);
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

	function _getVesselBilling($from_date, $to_date, $bill_items, $hide_stos, $search) {
		$company = $this->session->userdata('default_company');

		$sql = "SELECT id, type, cargo_type, vessel_id, vessel_name, berth_no, party_name,
			bl_no, cha_name, packages, net_weight, containers, indian_port,
			GROUP_CONCAT(reimbersment_url SEPARATOR '') AS reimbersment_url, 
			GROUP_CONCAT(DISTINCT reimbersment_group SEPARATOR '') AS reimbersment_group, 
			GROUP_CONCAT(DISTINCT reimbersment_name SEPARATOR '') AS reimbersment_name, SUM(reimbersment) AS reimbersment, 
			GROUP_CONCAT(invoice_url SEPARATOR '') AS invoice_url, 
			GROUP_CONCAT(DISTINCT invoice_group SEPARATOR '') AS invoice_group, 
			GROUP_CONCAT(DISTINCT invoice_name SEPARATOR '') AS invoice_name, SUM(invoice) AS invoice, 
			GROUP_CONCAT(shivshakti_url SEPARATOR '') AS shivshakti_url, 
			GROUP_CONCAT(DISTINCT shivshakti_group SEPARATOR '') AS shivshakti_group, 
			GROUP_CONCAT(DISTINCT shivshakti_name SEPARATOR '') AS shivshakti_name, SUM(shivshakti) AS shivshakti
		FROM (
			SELECT J.id, J.type, J.cargo_type, P.name AS party_name, 
				J.vessel_id, CONCAT(V.prefix, ' ', V.name, ' ', V.voyage_no) AS vessel_name, V.berth_no, 
				J.bl_no, CHA.name AS cha_name, J.packages, IF(J.cbm > 0, J.cbm, J.net_weight) AS net_weight, 
				(J.container_20 + J.container_40) AS containers, IP.name AS indian_port, 
				IF(VB.company_id = 1 AND VB.voucher_type_id = 3, PL.group_name, '') AS reimbersment_group,
				IF(VB.company_id = 1 AND VB.voucher_type_id = 3, PL.name, '') AS reimbersment_name,
				IF(VB.company_id = 1 AND VB.voucher_type_id = 3, CONCAT(VT.name,'/edit/',VB.id,'/',VO.id2,'/',VO.id3), '') AS reimbersment_url,
				SUM(IF(VB.company_id = 1 AND VB.voucher_type_id = 3, VJD.amount, 0)) AS reimbersment,
				IF(VB.company_id = 1 AND VB.voucher_type_id = 4, PL.group_name, '') AS invoice_group,
				IF(VB.company_id = 1 AND VB.voucher_type_id = 4, PL.name, '') AS invoice_name,
				IF(VB.company_id = 1 AND VB.voucher_type_id = 4, CONCAT(VT.name,'/edit/',VB.id,'/',VO.id2,'/',VO.id3), '') AS invoice_url,
				SUM(IF(VB.company_id = 1 AND VB.voucher_type_id = 4, VJD.amount, 0)) AS invoice, 
				IF(VB.company_id = 2 AND VB.voucher_type_id = 4, PL.group_name, '') AS shivshakti_group,
				IF(VB.company_id = 2 AND VB.voucher_type_id = 4, PL.name, '') AS shivshakti_name,
				IF(VB.company_id = 2 AND VB.voucher_type_id = 4, CONCAT(VT.name,'/edit/',VB.id,'/',VO.id2,'/',VO.id3), '') AS shivshakti_url,
				SUM(IF(VB.company_id = 2 AND VB.voucher_type_id = 4, VJD.amount, 0)) AS shivshakti
			FROM (((((((((vouchers VO INNER JOIN voucher_details VJD ON VO.id = VJD.voucher_id)
				INNER JOIN ledgers BI ON VJD.bill_item_id = BI.id)
				INNER JOIN voucher_books VB ON ((VB.company_id = 1 OR VB.company_id = 2) AND VO.voucher_book_id = VB.id))
				INNER JOIN voucher_types VT ON VB.voucher_type_id = VT.id)
				INNER JOIN ledgers PL ON VO.dr_ledger_id = PL.id)
				INNER JOIN jobs J ON VO.job_id = J.id)
				INNER JOIN parties P ON J.party_id = P.id)
				INNER JOIN indian_ports IP ON J.indian_port_id = IP.id)
				INNER JOIN vessels V ON J.vessel_id = V.id)
				LEFT OUTER JOIN agents CHA ON J.cha_id = CHA.id
			WHERE (VO.date >= ? AND VO.date <= ?) AND VB.voucher_type_id IN (3,4) AND ";
				if ($hide_stos)
					$sql .= "PL.code != 'SWAYA' AND ";
				$sql .= "BI.code NOT IN ('" . implode("', '", $bill_items) . "') ";
			$where = ' AND (';
			if (is_array($this->_parsed_search)) {
				foreach($this->_parsed_search as $key => $value)
					if (isset($this->_fields[$key]))
						$where .= $this->_fields[$key] . " LIKE '%$value%' AND ";
				if (strlen($where) > 6)
					$sql .= substr($where, 0, strlen($where) - 5) . ')';
			}
			
			$sql .= "
			GROUP BY VO.id
		) T 
		GROUP BY id
		ORDER BY vessel_name, bl_no";
		$query = $this->db->query($sql, array(
			convDate($from_date), convDate($to_date),
		));
		return $query->result_array();
	}
}
