<?php

use mikehaertl\wkhtmlto\Pdf;

class Pickup extends MY_Controller {
	var $_fields, $_h_fields;

	function __construct() {
		parent::__construct();

		$this->_fields = array(
			'shipper'  => 'SHPR.name',
			'location' => 'PICKL.name',
		);

		$this->_h_fields = array(
			'shipper'  => 'PICK.shipper_name',
			'location' => 'PICKL.name',
		);
	}
	
	function index() {
		if (! Auth::hasAccess()) {
			setSessionError('NO_PERMISSION');
			redirect('main');
		}

		$date     = null;
		$search   = null;
		$show_all = null;

		if($this->input->post('date')) {
			$date     = $this->input->post('date');
			$search   = $this->input->post('search');
			$show_all = $this->input->post('show_all');
			$this->session->set_userdata($this->_class.'_date', $date);
			$this->session->set_userdata($this->_class.'_search', $search);
			$this->session->set_userdata($this->_class.'_show_all', $show_all);
		}
		
		if($date == null) {
			$date     = $this->session->userdata($this->_class.'_date');
			$search   = $this->session->userdata($this->_class.'_search');
			$show_all = $this->session->userdata($this->_class.'_show_all');
		}

		$data['date']     = $date ? $date : date('d-m-Y');
		$data['show_all'] = $show_all ? $show_all : 0;
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

		$rows    = $this->_get($data['date'], $data['show_all'], $data['search'], $parsed_search);
		$history = $this->_getHistory($data['date'], $data['show_all'], $data['search'], $parsed_search);
		$data['history'] = count($history);

		if ($data['history'] == 0) {
			$data['rows']   = $rows;
			$data['rclass'] = array();
		}
		else {
			$result = $this->_getDiff($rows, $history);
			$data['rows']   = $result['rows'];
			$data['rclass'] = $result['rclass'];
		}

		$default_company = $this->session->userdata("default_company");
		$data['years']   = explode('_', $default_company['financial_year']);

		$data['page']       = $this->_clspath.$this->_class;
		$data['page_title'] = humanize($this->_class);
		$data['hide_title'] = true;
		$data['docs_url']   = $this->_docs;
		$this->load->view('index', $data);
	}
	
	function _get($date, $show_all = 0, $search, $parsed_search) {
		$result = array();
		$show_all = ($show_all ? 'AND PICK.pickup_date >= "' . convDate($date) . '"' : 'AND PICK.pickup_date = "' . convDate($date) . '"');
		$pickup_location_id = (Settings::get('pickup_location_id', 0) > 0 ? ' AND PICK.pickup_location_id = ' . Settings::get('pickup_location_id') : '');
		$sql = "SELECT PICK.id AS pickup_id, J.id AS job_id, J.id2_format, SHPR.name AS shipper_name,
			PICK.stuffing_location, C.name AS cargo_name, U.code AS unit_code,
			PICK.containers, CONCAT(CT.size, ' ', CT.code) AS size, PICK.gross_weight, LP.name AS port_of_loading, 
			J.fpod, L.code AS line_code, PICK.pickup_location_id, PICKL.name AS pickup_location, 
			DATE_FORMAT(PICK.pickup_date, '%d-%m-%Y') AS pickup_date, 
			DATE_FORMAT(PICK.stuffing_date, '%d-%m-%Y') AS stuffing_date,
			CONCAT(V.prefix, ' ', V.name, ' ', V.voyage_no) AS vessel_name, 
			DATE_FORMAT(COALESCE(V.eta_date, '0000-00-00'), '%d-%m-%Y') AS eta_date, 
			DATE_FORMAT(COALESCE(V.gate_cutoff_date, '0000-00-00'), '%d-%m-%Y') AS gate_cutoff_date, J.booking_no,
			GROUP_CONCAT(S.containers, ' x ', S.size, ' ', S.code) AS gate_out
		FROM pickups PICK INNER JOIN container_types CT ON PICK.container_type_id = CT.id
			INNER JOIN jobs J ON PICK.job_id = J.id
			LEFT OUTER JOIN pickup_locations PICKL ON PICK.pickup_location_id = PICKL.id
			LEFT OUTER JOIN parties SHPR ON J.shipper_id = SHPR.id
			LEFT OUTER JOIN products C ON J.product_id = C.id
			LEFT OUTER JOIN units U ON J.unit_id = U.id
			LEFT OUTER JOIN vessels V ON J.vessel_id = V.id
			LEFT OUTER JOIN indian_ports LP ON J.loading_port_id = LP.id
			LEFT OUTER JOIN agents L ON J.line_id = L.id
			LEFT OUTER JOIN (
				SELECT S.pickup_id, COUNT(S.id) AS containers, CT.size, CT.code 
				FROM deliveries_stuffings S INNER JOIN container_types CT ON S.container_type_id = CT.id
				GROUP BY S.pickup_id
			) S ON PICK.id = S.pickup_id
		WHERE J.stuffing_type != 'CFS' AND J.sub_type != 'Forwarding' $show_all $pickup_location_id " ;
		$where = ' AND (';
		if (is_array($parsed_search)) {
			foreach($parsed_search as $key => $value)
				if (isset($this->_fields[$key]))
					$where .= $this->_fields[$key] . " LIKE '%$value%' AND ";
			if (strlen($where) > 6)
				$sql .= substr($where, 0, strlen($where) - 5) . ') ';
		}
		$sql .= "
		GROUP BY PICK.id
		ORDER BY PICK.pickup_date, PICK.id";
		$query = $this->db->query($sql);
		$rows  = $query->result_array();
		foreach ($rows as $row) {
			$result[$row['pickup_id']] = $row;
		}
		return $result;
	}

	function _getHistory($date, $show_all = 0, $search, $parsed_search) {
		$result = array();
		$pickup_location_id = (Settings::get('pickup_location_id', 0) > 0 ? ' AND PICK.pickup_location_id = ' . Settings::get('pickup_location_id') : '');
		$sql    = "SELECT PICK.*, PICKL.name AS pickup_location,
			DATE_FORMAT(pickup_date, '%d-%m-%Y') AS pickup_date, 
			DATE_FORMAT(stuffing_date, '%d-%m-%Y') AS stuffing_date,
			DATE_FORMAT(eta_date, '%d-%m-%Y') AS eta_date,
			DATE_FORMAT(gate_cutoff_date, '%d-%m-%Y') AS gate_cutoff_date
		FROM pickup_programs AS PICK INNER JOIN pickup_locations PICKL ON PICK.pickup_location_id = PICKL.id
		WHERE pickup_date = ? $pickup_location_id ";
		$where = ' AND (';
		if (is_array($parsed_search)) {
			foreach($parsed_search as $key => $value)
				if (isset($this->_h_fields[$key]))
					$where .= $this->_h_fields[$key] . " LIKE '%$value%' AND ";
			if (strlen($where) > 6)
				$sql .= substr($where, 0, strlen($where) - 5) . ') ';
		}
		$sql .= "
		ORDER BY entry_no, PICK.id";
		$query  = $this->db->query($sql, array(convDate($date)));
		$rows   = $query->result_array();
		foreach ($rows as $row) {
			$result[$row['pickup_id']][$row['entry_no']] = $row;
		}
		return $result;
	}

	function _getDiff($rows, $history) {
		$result = array(
			'rows'   => array(),
			'rclass' => array(),
		);
		
		foreach ($history as $pickup_id => $entries) {
			foreach ($entries as $entry_no => $h) {
				$result['rows'][$pickup_id] = $h;
				if (! in_array($pickup_id, array_keys($rows))) {
					$result['rclass'][$pickup_id] = array('row' => 'Deleted');
					continue;
				}
				
				foreach ($rows[$pickup_id] as $f => $v) {
					if ($h[$f] != $v) {
						$result['rows'][$pickup_id][$f] = $v;
						$result['rclass'][$pickup_id][$f] = 'Changed';
					}
				}
			}
		}

		foreach ($rows as $r) {
			if (! in_array($r['pickup_id'], array_keys($history))) {
				$result['rows'][$r['pickup_id']] = $r;
				$result['rclass'][$r['pickup_id']] = array('row' => 'NewPickup');
			}
		}
		
		return $result;
	}

	function snapshot() {
		if (Auth::hasAccess(Auth::UPDATE)) {
			$date     = date('d-m-Y');
			$search   = '';
			$show_all = 0;
			$parsed_search = $this->kaabar->parseSearch($search);
			$rows     = $this->_get($date, $show_all, $search, $parsed_search);
			$history  = $this->_getHistory($date, $show_all, $search, $parsed_search);

			if (count($rows) == 0) {
				$this->load->library('user_agent');
				redirect($this->agent->referrer());
			}
			
			$sql = "SELECT MAX(entry_no) as last_entry FROM pickup_programs WHERE pickup_date = ?";
			$query = $this->db->query($sql, array(convDate($date)));
			$entry_no = $query->row_array();
			$entry_no = ($entry_no['last_entry']) ? $entry_no['last_entry']+1 : 1;
			foreach ($rows as $index => $row) { 
				$rows[$index]['entry_no']         = $entry_no;
				$rows[$index]['pickup_date']      = convDate($rows[$index]['pickup_date']);
				$rows[$index]['stuffing_date']    = convDate($rows[$index]['stuffing_date']);
				$rows[$index]['eta_date']         = convDate($rows[$index]['eta_date']);
				$rows[$index]['gate_cutoff_date'] = convDate($rows[$index]['gate_cutoff_date']);
				unset($rows[$index]['pickup_location']);
			}
			$this->db->insert_batch('pickup_programs', $rows);
		}
		else {
			setSessionError('Only user <strong>ravi</strong> can take a snapshot.');
		}

		$this->load->library('user_agent');
		redirect($this->agent->referrer());
	}

	function preview($pdf = 0, $email = 0) {
		$date     = $this->session->userdata($this->_class.'_date');
		$search   = $this->session->userdata($this->_class.'_search');
		$show_all = $this->session->userdata($this->_class.'_show_all');
		$date     = $date ? $date : date('d-m-Y');
		$search   = $search ? $search : '';
		$show_all = $show_all ? $show_all : 0;
		$parsed_search = $this->kaabar->parseSearch($search);
		$rows     = $this->_get($date, $show_all, $search, $parsed_search);
		$history  = $this->_getHistory($date, $show_all, $search, $parsed_search);

		if (count($history) == 0) {
			$data['rows']   = $rows;
			$data['rclass'] = array();
		}
		else {
			$result = $this->_getDiff($rows, $history);
			$data['rows']   = $result['rows'];
			$data['rclass'] = $result['rclass'];
		}
		
		$data['page_title'] = humanize($this->_class . ' Report');
		
		$html = $this->load->view($this->_clspath.$this->_class.'_preview', $data, true);
		$filename = underscore($data['page_title']);

		if ($email) {
			$this->load->helper(array('file', 'email'));

			$to      = $this->kaabar->checkEmail(explode(';', str_replace(' ', '', $this->input->post('to'))));
			$cc      = $this->kaabar->checkEmail(explode(';', str_replace(' ', '', $this->input->post('cc'))));
			$bcc     = $this->kaabar->checkEmail(explode(';', str_replace(' ', '', $this->input->post('bcc'))));
			$subject = $this->input->post('subject');
			$message = $this->input->post('message');
			$message .= '<hr />' . $html;

			if ($email && count($to) > 0) {
				$config = array(
					'protocol'     => 'smtp',
					'smtp_timeout' => 30,
					'smtp_host'    => Settings::get('smtp_host'),
					'smtp_port'    => Settings::get('smtp_port'),
					'smtp_user'    => Settings::get('smtp_user'),
					'smtp_pass'    => Settings::get('smtp_password'),
					'newline'      => "\r\n",
					'crlf'         => "\r\n",
					'mailtype'     => "html"
				);

				$this->load->library('email', $config);
				$this->email->from(Settings::get('smtp_user'));
				$this->email->to($to);
				$this->email->cc($cc);
				$this->email->bcc($bcc);
				$this->email->subject($subject);
				$this->email->message($message);
				
				$this->email->send();
				//echo $this->email->print_debugger(); exit;
				$sql = "SELECT MAX(entry_no) as last_entry FROM pickup_programs WHERE pickup_date = ?";
				$query = $this->db->query($sql, array(convDate($date)));
				$entry_no = $query->row_array();
				$entry_no = ($entry_no['last_entry']) ? $entry_no['last_entry']+1 : 1;
				foreach ($data['rows'] as $index => $row) { 
					$data['rows'][$index]['entry_no']         = $entry_no;
					$data['rows'][$index]['pickup_date']      = convDate($data['rows'][$index]['pickup_date']);
					$data['rows'][$index]['stuffing_date']    = convDate($data['rows'][$index]['stuffing_date']);
					$data['rows'][$index]['eta_date']         = convDate($data['rows'][$index]['eta_date']);
					$data['rows'][$index]['gate_cutoff_date'] = convDate($data['rows'][$index]['gate_cutoff_date']);
				}
				$this->db->insert_batch('pickup_programs', $data['rows']);
				setSessionAlert('Email has been sent To: &lt;' . implode(', ', $to) . '&gt;...', 'success');
			}
			$this->load->library('user_agent');
			redirect($this->agent->referrer());
		}
		elseif ($pdf) {
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
			echo $html;
		}
	}
	
	function excel($email = 0) {
		ini_set('max_execution_time', '0');
		ini_set('memory_limit', '512M');

		set_include_path(get_include_path() . PATH_SEPARATOR . 'assets/phpexcel');

		include 'PHPExcel/IOFactory.php';

		$filename = $this->_class . "_program.xlsx";
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

		$styleYellow = array(
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'startcolor' => array(
					'argb' => 'FFFFFF88',
				),
			)
		);

		$styleGreen = array(
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'startcolor' => array(
					'argb' => 'FF1BDB1A',
				),
			)
		);

		$date     = $this->session->userdata($this->_class.'_date');
		$search   = $this->session->userdata($this->_class.'_search');
		$show_all = $this->session->userdata($this->_class.'_show_all');
		$date     = $date ? $date : date('d-m-Y');
		$search   = $search ? $search : '';
		$show_all = $show_all ? $show_all : 0;
		$parsed_search = $this->kaabar->parseSearch($search);
		$rows     = $this->_get($date, $show_all, $search, $parsed_search);
		$history  = $this->_getHistory($date, $show_all, $search, $parsed_search);

		if (count($history) == 0) {
			$rclass = array();
		}
		else {
			$result = $this->_getDiff($rows, $history);
			$rows   = $result['rows'];
			$rclass = $result['rclass'];
		}
		
		// Header
		$skip_cols = array('id', 'pickup_id', 'entry_no', 'job_id');
		$header = array_keys(reset($rows));
		$j = 'A';
		foreach ($header as $f) {
			if (in_array($f, $skip_cols))
				continue;
			$objPHPExcel->getActiveSheet()->setCellValue($j . '1', humanize($f));
			$objPHPExcel->getActiveSheet()->getColumnDimension($j)->setAutoSize(true);
			$j++;
		}
		$objPHPExcel->getActiveSheet()->getStyle('A1:' . $j . '1')->applyFromArray($styleHeading);
		

		// Data
		$i = 2;
		foreach ($rows as $pickup_id => $r) {
			$style = (isset($rclass[$pickup_id]['row']) ? $rclass[$pickup_id]['row'] : false);
			$row_style = false;
			if ($style == 'NewPickup') 
				$row_style = $styleGreen;
			else if ($style == 'Deleted') 
				$row_style = $styleYellow;

			$j = 'A';
			foreach ($r as $f => $v) {
				if (in_array($f, $skip_cols))
					continue;
				$objPHPExcel->getActiveSheet()->setCellValue($j.$i, html_entity_decode($v));
				$col_style = (isset($rclass[$pickup_id][$f]) ? $styleGreen : false);
				if ($col_style) {
					$objPHPExcel->getActiveSheet()->getStyle($j.$i.':'.$j.$i)->applyFromArray($col_style);
				}
				$j++;
			}
			if ($row_style)
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':'.$j.$i)->applyFromArray($row_style);
			$i++;
		}

		$objPHPExcel->getActiveSheet()->getStyle('A1:' . $j . $i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$objPHPExcel->getActiveSheet()->getStyle('A1:' . $j . $i)->applyFromArray($styleSheet);

		$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");

		if ($email) {
			$config = array(
				'protocol' => 'smtp',
				'smtp_timeout' => 30,
				'smtp_host' => Settings::get('smtp_host'),
				'smtp_port' => Settings::get('smtp_port'),
				'smtp_user' => Settings::get('smtp_user'),
				'smtp_pass' => Settings::get('smtp_password'),
				'newline'   => "\r\n",
				'crlf'      => "\r\n",
				'mailtype'     => "html"
			);
			$this->load->library('email', $config);
			$this->load->helper(array('file', 'email'));

			$to      = $this->kaabar->checkEmail(explode(';', str_replace(' ', '', $this->input->post('to'))));
			$cc      = $this->kaabar->checkEmail(explode(';', str_replace(' ', '', $this->input->post('cc'))));
			$bcc     = $this->kaabar->checkEmail(explode(';', str_replace(' ', '', $this->input->post('bcc'))));
			$subject = $this->input->post('subject');
			$message = $this->input->post('message');

			$this->email->from(Settings::get('smtp_user'));
			$this->email->to($to);
			$this->email->cc($cc);
			$this->email->bcc($bcc);
			$this->email->subject($subject);
			$this->email->message($message);
			
			if ($to && valid_email($to)) {
				$filename = FCPATH.'/tmp/'.$filename;
				$objWriter->save($filename);
				$this->email->attach($filename);
				$this->email->send();
				unlink($file);
				setSessionAlert('Email has been sent to &lt;' . implode(', ', $to) . '&gt;...', 'success');
				//echo $this->email->print_debugger(); exit;
			}
			$this->load->library('user_agent');
			redirect($this->agent->referrer());
		}
		else {
			// redirect output to client browser
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment;filename="' . $filename.'"');
			header('Cache-Control: max-age=0');
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
			$objWriter->save('php://output');
		}
	}
}
