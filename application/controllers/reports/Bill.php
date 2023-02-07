
<?php

use mikehaertl\wkhtmlto\Pdf;

class Bill extends MY_Controller {
	var $_fields;
	var $_parsed_search;

	function __construct() {
		parent::__construct();
		
		$this->_fields = array(
			'shipment' => 'J.type',
			'type'     => 'J.cargo_type',
			'group'    => 'PL.group_name',
			'party'    => 'P.name',
			'category' => 'PRD.category',
			'product'  => 'PRD.name',
			'cha'      => 'CHA.name',
			'vessel'   => "CONCAT(V.prefix, ' ', V.name, ' ', V.voyage_no)"
		);
	}
	
	function index() {
		echo 'Access Deined';
	}

	function pending() {
		if (! Auth::hasAccess()) {
			setSessionError('NO_PERMISSION');
			redirect('main');
		}

		$default_company = $this->session->userdata("default_company");
		$data['company'] = $this->kaabar->getRow('companies', $default_company['id']);
		$data['years']   = explode('_', $default_company['financial_year']);

		$from_date = null;
		$to_date   = null;
		$search    = null;

		if($this->input->post('from_date')) {
			$from_date = $this->input->post('from_date');
			$to_date   = $this->input->post('to_date');
			$search    = $this->input->post('search');
			$this->session->set_userdata($this->_class.'_from_date', $from_date);
			$this->session->set_userdata($this->_class.'_to_date', $to_date);
			$this->session->set_userdata($this->_class.'_search', $search);
		}
		
		if($from_date == null) {
			$from_date = $this->session->userdata($this->_class.'_from_date');
			$to_date   = $this->session->userdata($this->_class.'_to_date');
			$search = $this->session->userdata($this->_class.'_search');
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

		$data['jobs'] = $this->_getBillPending($data['from_date'], $data['to_date'], $search);

		$this->load->helper('datefn');
		$data['javascript'] = array('daterangepicker/daterangepicker.js');
		$data['stylesheet'] = array('daterangepicker/daterangepicker-bs3.css');

		$data['page_title'] = "Bills Pending Report";
		$data['hide_title'] = true;
		$data['page'] = $this->_clspath.$this->_class.'_pending';
		$data['docs_url'] = $this->_docs;
		$this->load->view('index', $data);
	}

	function preview($pdf = 0) {
		$from_date = $this->session->userdata($this->_class.'_from_date');
		$to_date   = $this->session->userdata($this->_class.'_to_date');
		$search    = $this->session->userdata($this->_class.'_search');
		$data['rows']  = $this->_getBillPending($from_date, $to_date, $search);

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
		$rows      = $this->_getBillPending($from_date, $to_date, $search);

		set_include_path(get_include_path() . PATH_SEPARATOR . 'assets/phpexcel');

		include 'PHPExcel/IOFactory.php';

		$filename = "STAX Summary.xlsx";
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

		$objPHPExcel->getActiveSheet()->setCellValue('A1', 'No');
		$objPHPExcel->getActiveSheet()->setCellValue('B1', 'Type');
		$objPHPExcel->getActiveSheet()->setCellValue('C1', 'Cargo Type');
		$objPHPExcel->getActiveSheet()->setCellValue('D1', 'Group Name');
		$objPHPExcel->getActiveSheet()->setCellValue('E1', 'Party Name');
		$objPHPExcel->getActiveSheet()->setCellValue('F1', 'Vessel Name');
		$objPHPExcel->getActiveSheet()->setCellValue('G1', 'BL No');
		$objPHPExcel->getActiveSheet()->setCellValue('H1', 'Product');
		$objPHPExcel->getActiveSheet()->setCellValue('I1', 'Category');
		$objPHPExcel->getActiveSheet()->setCellValue('J1', 'Packages');
		$objPHPExcel->getActiveSheet()->setCellValue('K1', 'Net Weight');
		$objPHPExcel->getActiveSheet()->setCellValue('L1', 'Containers');
		$objPHPExcel->getActiveSheet()->setCellValue('M1', 'Reimbersment');

		$objPHPExcel->getActiveSheet()->getStyle('A1:M1')->applyFromArray($styleHeading);

		// Data
		$i = 2;
		foreach ($rows as $r) {
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $i-1);
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $r['type']);
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$i, $r['cargo_type']);
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $r['group_name']);
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $r['party_name']);
			$objPHPExcel->getActiveSheet()->setCellValue('F'.$i, $r['vessel_name']);
			$objPHPExcel->getActiveSheet()->setCellValue('G'.$i, $r['bl_no']);
			$objPHPExcel->getActiveSheet()->setCellValue('H'.$i, $r['product']);
			$objPHPExcel->getActiveSheet()->setCellValue('I'.$i, $r['category']);
			$objPHPExcel->getActiveSheet()->setCellValue('J'.$i, $r['packages']);
			$objPHPExcel->getActiveSheet()->setCellValue('K'.$i, $r['net_weight']);
			$objPHPExcel->getActiveSheet()->setCellValue('L'.$i, $r['containers']);
			$objPHPExcel->getActiveSheet()->setCellValue('M'.$i, $r['reimbersment']);
			$i++;
		}


		//$objPHPExcel->getActiveSheet()->getStyle('A1:G1000')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$objPHPExcel->getActiveSheet()->getStyle('A1:M'.$i)->applyFromArray($styleSheet);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);


		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
		//$objWriter->save($filename);

		// redirect output to client browser
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="' . $filename.'"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
	}

	function _getBillPending($from_date, $to_date, $search) {
		$company = $this->session->userdata('default_company');

		$sql = "SELECT T.id, T.type, T.cargo_type, T.group_name, T.party_name, T.vessel_name, T.bl_no, T.cha_name,
			T.product, T.category, T.packages, T.net_weight, T.containers, SUM(T.reimbersment) AS reimbersment
		FROM (
			SELECT DISTINCT J.id, J.type, J.cargo_type, PL.group_name, P.name AS party_name, 
				CONCAT(V.prefix, ' ', V.name, ' ', V.voyage_no) AS vessel_name, IF(J.type = 'Import', J.bl_no, 
				PRD.name AS product, PRD.category, CHA.name AS cha_name, CONCAT(J.packages, ' ', PT.code) AS packages, 
				IF(J.cbm > 0, CONCAT(J.cbm, ' CBM'), CONCAT(J.net_weight, ' ', J.net_weight_unit)) AS net_weight, 
				CONCAT(IF(J.container_20 > 0, CONCAT(J.container_20, 'x20 '), ''), IF(J.container_40 > 0, CONCAT(J.container_40, 'x40 '), '')) AS containers, VJD.amount AS reimbersment
			FROM jobs J INNER JOIN parties P ON J.party_id = P.id
				INNER JOIN vessels V ON J.vessel_id = V.id
				INNER JOIN products PRD ON J.product_id = PRD.id
				INNER JOIN package_types PT ON J.package_type_id = PT.id
				LEFT OUTER JOIN ledgers PL ON J.party_id = PL.party_id
				LEFT OUTER JOIN voucher_details VJD ON VJD.job_id = J.id
				LEFT OUTER JOIN vouchers VO ON VJD.voucher_id = VO.id
				LEFT OUTER JOIN voucher_books VB ON VO.voucher_book_id = VB.id
				LEFT OUTER JOIN agents CHA ON J.cha_id = CHA.id
			WHERE (J.date >= ? AND J.date <= ?) AND 
				J.id NOT IN (
					SELECT DISTINCT V.job_id 
					FROM (vouchers V INNER JOIN voucher_books VB ON V.voucher_book_id = VB.id)
						INNER JOIN jobs J ON V.job_id = J.id
					WHERE J.date >= ? AND J.date <= ? AND VB.voucher_type_id IN (3,4)
				)"; // AND
					// VJD.job_id > 0 AND VO.job_id = 0 AND VB.voucher_type_id = 7";
		$where = ' AND (';
		if (is_array($this->_parsed_search)) {
			foreach($this->_parsed_search as $key => $value)
				if (isset($this->_fields[$key]))
					$where .= $this->_fields[$key] . " LIKE '%$value%' AND ";
			if (strlen($where) > 6)
				$sql .= substr($where, 0, strlen($where) - 5) . ')';
		}
		
		$sql .= "
			ORDER BY J.type, J.cargo_type, V.name, P.name
		) T
		GROUP BY T.id";
		$query = $this->db->query($sql, array(
			convDate($from_date), convDate($to_date),
			convDate($from_date), convDate($to_date),
		));
		return $query->result_array();
	}
}
