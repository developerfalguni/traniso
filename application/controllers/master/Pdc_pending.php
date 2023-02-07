<?php

class Pdc_pending extends MY_Controller {
	var $_fields;
	
	function __construct() {
		parent::__construct();

		$this->_table = 'issued_cheques';
		$this->_fields = array(
			'bank'   => 'L.name',
			'cheque' => 'IC.cheque_no',
			'favor'  => 'IC.favor',
		);
	}
	
	function index() {
		if (! Auth::hasAccess()) {
			setSessionError('NO_PERMISSION');
			redirect('main');
		}

		$search = $this->session->userdata($this->_class.'_search');

		if ($this->input->post('search_form')) {
			$search = addslashes($this->input->post('search'));
			$this->session->set_userdata($this->_class.'_search', $search);
		}
		
		if ($search == null) {
			$search = '';
			$this->session->set_userdata($this->_class.'_search', $search);
		}

		$data['search'] = $search;
		$data['search_fields'] = $this->_fields;
		$parsed_search  = $this->kaabar->parseSearch($search);
		$data['parsed_search'] = $parsed_search;
		if (is_array($parsed_search)) {
			$search = '';
			foreach ($parsed_search as $key => $value) {
				$search .= $key.':'.$value.' ';
			}
			$data['search'] = $search;
		}

		$realization_dates = $this->input->post('realization_date');
		if ($realization_dates != null) {
			foreach ($realization_dates as $index => $realization_date) {
				$this->kaabar->save('issued_cheques', array('realization_date' => $realization_date), array('id' => $index));
			}
		}

		$data['rows'] = $this->_getPdcPending($search, $parsed_search);
		$default_company = $this->session->userdata("default_company");
		
		$data['page_title'] = humanize($this->_class);
		$data['hide_title'] = true;
		$data['page'] = $this->_clspath.$this->_class;
		$data['docs_url'] = $this->_docs;
		$this->load->view('index', $data);
	}

	function _getPdcPending($search, $parsed_search) {
		$default_company = $this->session->userdata('default_company');
		$sql = "SELECT IC.id, C.code AS company_code, IC.bank_ledger_id, L.name AS bank, 
			IF(IC.cheque_date <= NOW(), DATE_FORMAT(NOW(), '%d-%m-%Y'), DATE_FORMAT(IC.cheque_date, '%d-%m-%Y')) AS cheque_date, 
			IF(IC.cheque_date <= NOW(), DATE_FORMAT(NOW(), '%Y-%m'), DATE_FORMAT(IC.cheque_date, '%Y-%m')) AS `year_month`,
			IC.cheque_no, IC.favor, IC.amount, DATE_FORMAT(IC.realization_date, '%d-%m-%Y') AS realization_date
		FROM (issued_cheques IC INNER JOIN companies C ON IC.company_id = C.id)
			INNER JOIN ledgers L ON L.id = IC.bank_ledger_id
		WHERE (IC.company_id = ? AND IC.cancelled = 'No') AND 
			(IC.realization_date = '0000-00-00' OR 
			IC.realization_date = DATE(CURDATE()) OR 
			IC.realization_date >= DATE(CURDATE() - 2))";
		$where = ' AND (';
		if (is_array($parsed_search)) {
			foreach($parsed_search as $key => $value)
				if (isset($this->_fields[$key]))
					$where .= $this->_fields[$key] . " LIKE '%$value%' AND ";
			if (strlen($where) > 6)
				$sql .= substr($where, 0, strlen($where) - 5) . ')';
		}
		$sql .= "
		ORDER BY IC.bank_ledger_id, IF(IC.realization_date = '0000-00-00', 1, 0), IC.cheque_date";
		$query = $this->db->query($sql, array($default_company['id']));
		return $query->result_array();
	}

	function excel() {
		$search        = $this->session->userdata($this->_class.'_search');
		$parsed_search = $this->kaabar->parseSearch($search);	

		$rows = $this->_getPdcPending($search, $parsed_search);
		
		ini_set('max_execution_time', '0');
		ini_set('memory_limit', '512M');

		set_include_path(get_include_path() . PATH_SEPARATOR . 'assets/phpexcel');
		include 'PHPExcel/IOFactory.php';

		$filename = "PDC Pending Report.xlsx";
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

		$objPHPExcel->getActiveSheet()->setCellValue('A1', 'Sr No');
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->setCellValue('B1', 'Bank');
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->setCellValue('C1', 'Month');
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->setCellValue('D1', 'Date');
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->setCellValue('E1', 'Cheque No');
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->setCellValue('F1', 'Favoring');
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->setCellValue('G1', 'Amount');
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->setCellValue('H1', 'Realization Date');
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getStyle('A1:G1')->applyFromArray($styleHeading);

		// Data
		$i = 2;
		foreach ($rows as $r) {			
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $i, $i-1);
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $i, $r['bank']);
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $i, date('Y-M', strtotime($r['cheque_date'])));
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $i, $r['cheque_date']);
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $i, $r['cheque_no']);
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, $i, $r['favor']);
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(6, $i, $r['amount']);
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(7, $i, $r['realization_date']);
			$i++;
		}
		$i++;
		
		$objPHPExcel->getActiveSheet()->getStyle('A1:H'.$i)->applyFromArray($styleSheet);
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

	function import() {
		if (! Auth::hasAccess(Auth::CREATE)) {
			setSessionError('NO_PERMISSION');
			redirect('main');
		}
		
		ini_set('max_execution_time', '0');
		ini_set('memory_limit', '512M');
		set_include_path(get_include_path() . PATH_SEPARATOR . 'assets/phpexcel');
		include 'PHPExcel/IOFactory.php';

		$config['upload_path']   = './php_uploads/';
		$config['allowed_types'] = '*';
		$config['encrypt_name']  = true;
		$config['remove_spaces'] = true;

		$this->load->library('upload', $config);
		$this->upload->initialize($config);
		$this->upload->do_upload();
		$excel = $this->upload->data();

		$inputFileName = $excel['full_path'];		
		$objReader = new PHPExcel_Reader_CSV();
		
		$objPHPExcel = $objReader->load($inputFileName);

		$row             = array();
		$hasData         = true;
		//$sheet_name    = $this->input->post('sheet_name');
		$starting_row    = $this->input->post('starting_row');
		$account_col     = $this->input->post('account_col');
		$realization_col = $this->input->post('realization_col');
		$favor_col       = $this->input->post('favor_col');
		$cheque_no_col   = $this->input->post('cheque_no_col');
		$amount_col      = $this->input->post('amount_col');

		$blank_rows = 0;
		$reconcilation = array();
		while($blank_rows <= 10) {
			$account_no_str = $objPHPExcel->getActiveSheet()->getCell($account_col.$starting_row)->getValue();
			preg_match("/[0-9]{12}/", $account_no_str, $regs);
			if (strlen(trim($account_no_str)) == 0 || !isset($regs[0])) {
				$blank_rows++;
				$starting_row++;
				continue;
			}
			$blank_rows = 0;
			$account_no = $regs[0];
			$date = $objPHPExcel->getActiveSheet()->getCell($realization_col.$starting_row)->getValue();
			$cheque_no = $objPHPExcel->getActiveSheet()->getCell($cheque_no_col.$starting_row)->getValue();
			if (strlen(trim($cheque_no)) > 0) {
				$reconcilation[] = array(
					'account_no'       => $account_no,
					'realization_date' => date('Y-m-d', strtotime($date)),
					'favor'            => $objPHPExcel->getActiveSheet()->getCell($favor_col.$starting_row)->getValue(),
					'cheque_no'        => $cheque_no,
					'amount'           => $objPHPExcel->getActiveSheet()->getCell($amount_col.$starting_row)->getValue(),
				);
			}
			$starting_row++;
		}

		foreach ($reconcilation as $r) {
			$this->db->query('UPDATE issued_cheques IC SET IC.realization_date = ?, IC.amount = ? 
				WHERE IC.account_no = ? AND IC.cheque_no = ?',
				array($r['realization_date'], $r['amount'], $r['account_no'], $r['cheque_no'])
			);
		}

		$objPHPExcel->disconnectWorksheets();
		unset($objPHPExcel);
		unlink($excel['full_path']);

		$this->load->library('user_agent');
		redirect($this->agent->referrer());
	}
}
