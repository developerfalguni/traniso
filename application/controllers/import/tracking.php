<?php

class Tracking extends MY_Controller {
	var $_b_map, $_c_map;
	var $_b_fields, $_c_fields;
	var $_parsed_search;

	function __construct() {
		parent::__construct();

		$this->_table = 'import_details';
		$this->load->model('import');
		$this->load->helper('datefn');

		$this->_c_map = array(
			'party'   => 'PL.name',
			'hss'     => 'HSL.name',
			'bl'      => 'J.bl_no',
			'vessel'  => 'V.name',
			'line'    => 'SL.name',
			'cfs'     => 'CFS.name',
			'port'    => 'IP.name',
			'pod'     => 'P.place_of_delivery',
			'shipper' => 'J.vi_shipper_name',
			'remarks' => 'P.remarks',
			'status'  => 'J.status',
			'website' => 'WP.name',
		);
		$this->_c_fields = array(
			'party'   => 'Party Name',
			'hss'     => 'High Seas Sale',
			'bl'      => 'BL No',
			'vessel'  => 'Vessel Name',
			'line'    => 'Line Name',
			'cfs'     => 'CFS.name',
			'port'    => 'Port Name',
			'pod'     => 'Place of Delivery',
			'shipper' => 'Shipper Name',
			'remarks' => 'Remarks',
			'status'  => 'Status',
			'website' => 'Website Party Name',
		);
	}
	
	function index() {
		$starting_row = intval($starting_row);$search = $this->session->userdata($this->_class.'_search');
		if($this->input->post('search_form')) {
			$search = addslashes($this->input->post('search'));
			$this->session->set_userdata($this->_class.'_search', $search);
		}
		
		if ($search == null) {
			$search = '';
			$this->session->set_userdata($this->_class.'_search', $search);
		}

		$data['search'] = $search;
		$this->_parsed_search  = $this->kaabar->parseSearch($search);
		$data['parsed_search'] = $this->_parsed_search;

		if (is_array($this->_parsed_search)) {
			$search = '';
			foreach ($this->_parsed_search as $key => $value) {
				$search .= $key.':'.$value.' ';
			}
			$data['search'] = $search;
		}
		
		$data['search_fields']  = $this->_c_fields;
		$data['rows'] = $this->_contTrackings($search);

		$data['auto_refresh'] = 300;
		
		$data['docs_url']    = $this->_docs;
		$data['page_title']  = $this->_class . " - Import - Pending";
		$data['hide_title']  = true;
		$data['hide_footer'] = true;
		$data['page']        = $this->_clspath.$this->_class;
		$this->load->view('index', $data);
	}
	
	function _contTrackings($search = '') {
		$sql = "SELECT P.id, P.job_id, J.id2_format, PL.name AS party_name, GROUP_CONCAT(DISTINCT HSL.name ORDER BY HSL.id SEPARATOR '<br />') AS high_seas, 
			COALESCE(SHIPPER.name, J.vi_shipper_name) AS shipper_name, CFS.name AS cfs_name, 
			IF(J.cbm > 0, J.cbm, J.net_weight) AS cbm, J.net_weight, J.net_weight_unit, 
			(J.container_20 + J.container_40) AS total_containers,
			CONCAT(IF(J.container_20 > 0, CONCAT(J.container_20, 'x20 '), ''), IF(J.container_40 > 0, CONCAT(J.container_40, 'x40 '), '')) AS containers,
			J.house_bl, IF(LENGTH(TRIM(J.bl_no)) = 0, 'Missing BL No', J.bl_no) AS bl_no, DATE_FORMAT(bl_date, '%d-%m-%Y') AS bl_date, 
			J.vessel_id, P.temp_vessel_name, DATE_FORMAT(P.temp_eta, '%d-%m-%Y') AS temp_eta,  V.name AS vessel_name, V.voyage_no, DATE_FORMAT(V.eta_date, '%d-%m-%Y') AS eta_date, 
			IP.name AS indian_port, P.place_of_delivery, SL.name AS line_name, CHA.name AS cha_name, 
			P.free_days, DATE_FORMAT(IF(ISNULL(V.eta_date), DATE_ADD(P.temp_eta, INTERVAL (P.free_days-1) DAY), DATE_ADD(V.eta_date, INTERVAL (P.free_days-1) DAY)), '%d-%m-%Y') AS free_days_upto, DATE_FORMAT(P.original_bl_received, '%d-%m-%Y') AS original_bl_received,
			P.remarks, J.status
		FROM import_details P
			INNER JOIN jobs J ON P.job_id = J.id
			INNER JOIN parties PL ON J.party_id = PL.id
			LEFT OUTER JOIN agents SHIPPER ON J.shipper_id = SHIPPER.id
			LEFT OUTER JOIN agents SL ON J.line_id = SL.id
			LEFT OUTER JOIN agents CFS ON J.cfs_id = CFS.id
			LEFT OUTER JOIN agents CHA ON J.cha_id = CHA.id
			LEFT OUTER JOIN vessels V ON J.vessel_id = V.id
			LEFT OUTER JOIN indian_ports IP ON J.indian_port_id = IP.id
			LEFT OUTER JOIN high_seas HS ON P.job_id = HS.job_id
			LEFT OUTER JOIN parties HSL ON HS.party_id = HSL.id
			LEFT OUTER JOIN parties WP ON J.web_party_id = WP.id
		WHERE (J.type = 'Import' AND J.cargo_type = 'Container' AND LENGTH(J.be_no) = 0)";
				$where = ' AND (';
				if (is_array($this->_parsed_search)) {
					foreach($this->_parsed_search as $key => $value)
						if (isset($this->_c_map[$key]))
							$where .= $this->_c_map[$key] . " LIKE '%$value%' AND ";
					if (strlen($where) > 6)
						$sql .= substr($where, 0, strlen($where) - 5) . ')';
				}
				else {
					$sql .= $where . "PL.name LIKE '%$search%' OR
			HSL.name LIKE '%$search%' OR
			J.bl_no LIKE '%$search%' OR
			V.name LIKE '%$search%' OR
			P.temp_vessel_name LIKE '%$search%' OR
			IP.name LIKE '%$search%' OR
			SL.name LIKE '%$search%')";
				}
			
				$sql .= " AND (
				J.status != 'Completed'
			)
		GROUP BY P.job_id
		ORDER BY IF(ISNULL(V.eta_date) OR V.eta_date = '0000-00-00', P.temp_eta, V.eta_date), J.bl_date";
		$query = $this->db->query($sql);
		$rows   = $query->result_array();
		$result = array(0);
		foreach ($rows as $r) {
			$result[$r['job_id']] = $r;
		}

		// Fetch Deliveries
		$sql = "SELECT C.job_id, 
			COUNT(IF(CT.size = 20 AND D.cfs_in_date != '0000-00-00 00:00:00', C.id, NULL)) AS container_20, 
			COUNT(IF(CT.size = 40 AND D.cfs_in_date != '0000-00-00 00:00:00', C.id, NULL)) AS container_40,
			ROUND(SUM(C.net_weight), 2) AS net_weight, ROUND(SUM(D.dispatch_weight), 2) AS dispatch_weight
		FROM deliveries_stuffings D LEFT OUTER JOIN containers C ON D.container_id = C.id
			LEFT OUTER JOIN container_types CT ON C.container_type_id = CT.id
		WHERE D.job_id IN (" . implode(',', array_keys($result)) . ")
		GROUP BY D.job_id";
		$query = $this->db->query($sql);
		$rows  = $query->result_array();
		foreach ($rows as $r) {
			if (isset($result[$r['job_id']]))
				$result[$r['job_id']]['delivery'] = $r;
		}

		unset($result[0]);
		return $result;
	}

	function _contTrackingExcel($search = '') {
		$sql = "SELECT P.id, P.job_id, J.id2_format, PL.name AS customer_name, GROUP_CONCAT(DISTINCT HSL.name ORDER BY HSL.id SEPARATOR ' >> ') AS high_seas, 
			COALESCE(SHIPPER.name, J.vi_shipper_name) AS shipper_name, CFS.name AS cfs_name, (J.container_20 + J.container_40) AS total_containers,
			J.bl_no, CONCAT(IF(J.container_20 > 0, CONCAT(J.container_20, 'x20 '), ''), IF(J.container_40 > 0, CONCAT(J.container_40, 'x40 '), '')) AS containers,
			J.net_weight, J.net_weight_unit, SL.name AS shipping_line, 
			IF(ISNULL(V.name), P.temp_vessel_name, V.name) AS vessel_name, 
			CONCAT(IP.name, IF(LENGTH(P.place_of_delivery) > 0, CONCAT(' >> ', P.place_of_delivery), '')) AS place_of_discharge, 	
			DATE_FORMAT(IF(ISNULL(V.eta_date), DATE_ADD(P.temp_eta, INTERVAL (P.free_days-1) DAY), DATE_ADD(V.eta_date, INTERVAL (P.free_days-1) DAY)), '%d-%b-%Y') AS free_days_upto,
			IF(ISNULL(V.eta_date), DATE_FORMAT(P.temp_eta, '%d-%b-%Y'), DATE_FORMAT(V.eta_date, '%d-%b-%Y')) AS eta_date, 
			IF(P.original_bl_received = '0000-00-00', 'No', 'Yes') AS original_doc_rcvd,
			P.remarks AS current_status
		FROM import_details P INNER JOIN jobs J ON P.job_id = J.id
			INNER JOIN parties PL ON J.party_id = PL.id
			LEFT OUTER JOIN agents SHIPPER ON J.shipper_id = SHIPPER.id
			LEFT OUTER JOIN agents SL ON J.line_id = SL.id
			LEFT OUTER JOIN agents CFS ON J.cfs_id = CFS.id
			LEFT OUTER JOIN vessels V ON J.vessel_id = V.id
			LEFT OUTER JOIN indian_ports IP ON J.indian_port_id = IP.id
			LEFT OUTER JOIN high_seas HS ON P.job_id = HS.job_id
			LEFT OUTER JOIN parties HSL ON HS.party_id = HSL.id
			LEFT OUTER JOIN parties WP ON J.web_party_id = WP.id
		WHERE (J.type = 'Import' AND J.cargo_type = 'Container' AND LENGTH(J.be_no) = 0)";
			$where = ' AND (';
			if (is_array($this->_parsed_search)) {
				foreach($this->_parsed_search as $key => $value)
					if (isset($this->_c_map[$key]))
						$where .= $this->_c_map[$key] . " LIKE '%$value%' AND ";
				if (strlen($where) > 6)
					$sql .= substr($where, 0, strlen($where) - 5) . ')';
			}
			else {
				$sql .= $where . "PL.name LIKE '%$search%' OR
			HSL.name LIKE '%$search%' OR
			J.bl_no LIKE '%$search%' OR
			V.name LIKE '%$search%' OR
			P.temp_vessel_name LIKE '%$search%' OR
			IP.name LIKE '%$search%' OR
			SL.name LIKE '%$search%')";
			}
			
			$sql .= " AND (
				J.status != 'Completed'
			)
		GROUP BY P.job_id
		ORDER BY (ISNULL(V.eta_date) OR V.eta_date = '0000-00-00'), V.eta_date, (P.temp_eta = '0000-00-00'), P.temp_eta, V.eta_date, J.bl_date";

		$query = $this->db->query($sql);
		$rows = $query->result_array();
		$result = array(0);
		foreach ($rows as $r) {
			$result[$r['job_id']] = $r;
		}

		// Fetch Deliveries
		$sql = "SELECT C.job_id, 
			COUNT(IF(CT.size = 20 AND D.cfs_in_date != '0000-00-00 00:00:00', C.id, NULL)) AS container_20, 
			COUNT(IF(CT.size = 40 AND D.cfs_in_date != '0000-00-00 00:00:00', C.id, NULL)) AS container_40,
			ROUND(SUM(C.net_weight), 2) AS net_weight, ROUND(SUM(D.dispatch_weight), 2) AS dispatch_weight
		FROM deliveries_stuffings D LEFT OUTER JOIN containers C ON D.container_id = C.id
			LEFT OUTER JOIN container_types CT ON C.container_type_id = CT.id
		WHERE D.job_id IN (" . implode(',', array_keys($result)) . ")
		GROUP BY D.job_id";
		$query = $this->db->query($sql);
		$rows  = $query->result_array();
		foreach ($rows as $r) {
			if (isset($result[$r['job_id']]))
				$result[$r['job_id']]['delivery'] = $r;
		}
		
		return $result;
	}

	function preview($pdf = 0) {
		$default_company = $this->session->userdata('default_company');
		$data['company'] = $this->kaabar->getRow('companies', $default_company['id']);

		$search = $this->session->userdata($this->_class.'_search');
		$this->_parsed_search = $this->kaabar->parseSearch($search);
		$data['rows'] = $this->_contTrackingExcel($search);
		$data['page'] = 'reports/' . $this->_class . '_preview';
		
		$data['page_title'] = humanize($this->_class . ' Report');
		$data['filename']   = strtolower($this->_class . '_' . (strlen($search) > 0 ? $search . '_' : '') . date('d-m-Y'));
		
		$this->_preview($data, $pdf);
	}


	function excel() {
		$search = $this->session->userdata($this->_class.'_search');
		$this->_parsed_search = $this->kaabar->parseSearch($search);
		$rows = $this->_contTrackingExcel($search);
		$header = array_keys($rows[0]);

		$filename = $this->_class . '_' . (strlen($search) > 0 ? $search . '_' : '') . date('d-m-Y') . ".xlsx";
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


		// Header
		$last_col = 'A';
		foreach ($header as $i => $h) {
			$sheet->setCellValue($last_col++.'1', humanize($h));
			$sheet->getColumnDimensionByColumn($i)->setAutoSize(true);
		}
		$last_col--;
		$sheet->getStyle('A1:' . $last_col . '1')->applyFromArray($styleHeading);
		
		// Data
		$i = 2;
		foreach ($rows as $row) {
			$j = 'A';
			foreach ($row as $f => $v) {
				if ($f == 'id2_format') {
					$sheet->setCellValueExplicit($j++.$i, $v, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
				}
				else {
					$sheet->setCellValue($j++.$i, html_entity_decode(str_replace('<strong>', '', str_replace('</strong>', '', $v))));
				}
				if ($f == 'eta_date' && strlen($v) != 0 && $v != '00-00-0000' && daysDiff(date('d-m-Y'), $v, 'd-m-Y') <= 1) {
					$sheet->getStyle('A'.$i.':'.$last_col.$i)->applyFromArray($styleYellow);
				}
			}
			$i++;
		}
		$sheet->getStyle('A1:' . $last_col.$i)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
		$sheet->getStyle('A1:' . $last_col.$i)->applyFromArray($styleSheet);

		// redirect output to client browser
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="' . $filename.'"');
		header('Cache-Control: max-age=0');
		$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
		$writer->save('php://output');
	}

	function email() {
		$default_company = $this->session->userdata('default_company');
		$data['company'] = $this->kaabar->getRow('companies', $default_company['id']);

		$search               = $this->session->userdata($this->_class.'_search');
		$this->_parsed_search = $this->kaabar->parseSearch($search);
		$data['rows']         = $this->_contTrackingExcel($search);
		$page                 = 'reports/'.$this->_class.'_preview';
		
		$data['page_title'] = humanize($this->_class . ' Report');
		$html = $this->load->view($page, $data, true);
		$this->load->helper(array('file', 'email'));

		$to      = $this->kaabar->checkEmail(explode(';', str_replace(' ', '', $this->input->post('to'))));
		$cc      = $this->kaabar->checkEmail(explode(';', str_replace(' ', '', $this->input->post('cc'))));
		$bcc     = $this->kaabar->checkEmail(explode(';', str_replace(' ', '', $this->input->post('bcc'))));
		$subject = $this->input->post('subject');
		$message = $this->input->post('message');
		$message .= '<hr />' . $html;

		if (count($to) > 0) {
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
			setSessionAlert('Email has been sent To: &lt;' . implode(', ', $to) . '&gt;...', 'success');
		}
		redirect($this->agent->referrer());
	}

	function updateStatus() {
		$this->kaabar->save($this->_table, 
			array('status' => $this->input->post('status')),
			array('id'     => $this->input->post('row_id'))
		);

		redirect($this->agent->referrer());
	}

	function updateETA() {
		$data = array();

		$data['temp_vessel_name'] = $this->input->post('vessel');
		$data['temp_eta']         = $this->input->post('eta');
		$data['free_days']        = $this->input->post('free_days');
		$data['remarks']          = $this->input->post('remarks');

		// if ($status[$id] == 'Program') {
		// 	$icegate = $this->kaabar->getField('icegate_be', $row['job_id'], 'job_id', 'status');
		// 	if ($icegate == 'OOC' && strlen($do_uptos[$id]) == 10 && $do_uptos[$id] != '00-00-0000')
		// 		$data['status'] = 'Delivery';
		// }

		// original_bl_received should not be 3 days old from current date.
		if (Auth::isAdmin())
			$data['original_bl_received'] 	= $this->input->post('original_bl_received');
		else if(daysDiff($original_bl_receiveds[$id], date('d-m-Y'), 'd-m-Y') <= 3)
			$data['original_bl_received'] 	= $this->input->post('original_bl_received');

		$data['id'] = $this->input->post('row_id');
		$this->kaabar->save($this->_table, $data, array('id' => $data['id']));

		redirect($this->agent->referrer());
	}

	function getLineEmail() {
		$job_id = $this->input->post('job_id');
		$sql = "SELECT P.id, P.job_id, J.id2_format, PL.name AS party_name, GROUP_CONCAT(DISTINCT HSL.name ORDER BY HSL.id SEPARATOR '<br />') AS high_seas, 
			COALESCE(SHIPPER.name, J.vi_shipper_name) AS shipper_name, CFS.name AS cfs_name, CFS.email AS cfs_email,
			IF(J.cbm > 0, J.cbm, J.net_weight) AS cbm, J.net_weight, J.net_weight_unit, 
			(J.container_20 + J.container_40) AS total_containers,
			CONCAT(IF(J.container_20 > 0, CONCAT(J.container_20, 'x20 '), ''), IF(J.container_40 > 0, CONCAT(J.container_40, 'x40 '), '')) AS containers,
			J.house_bl, IF(LENGTH(TRIM(J.bl_no)) = 0, 'Missing BL No', J.bl_no) AS bl_no, DATE_FORMAT(bl_date, '%d-%m-%Y') AS bl_date, 
			J.vessel_id, P.temp_vessel_name, DATE_FORMAT(P.temp_eta, '%d-%m-%Y') AS temp_eta, 
			CONCAT(V.prefix, ' ', V.name, ' ', V.voyage_no) AS vessel_name, DATE_FORMAT(V.eta_date, '%d-%m-%Y') AS eta_date, 
			IP.name AS indian_port, P.place_of_delivery, SL.name AS line_name, SL.email AS line_email, CHA.name AS cha_name, 
			P.free_days, DATE_FORMAT(IF(ISNULL(V.eta_date), DATE_ADD(P.temp_eta, INTERVAL (P.free_days-1) DAY), 
			DATE_ADD(V.eta_date, INTERVAL (P.free_days-1) DAY)), '%d-%m-%Y') AS free_days_upto, 
			DATE_FORMAT(P.original_bl_received, '%d-%m-%Y') AS original_bl_received, P.remarks, J.status
		FROM import_details P INNER JOIN jobs J ON P.job_id = J.id
			INNER JOIN parties PL ON J.party_id = PL.id
			LEFT OUTER JOIN agents SHIPPER ON J.shipper_id = SHIPPER.id
			LEFT OUTER JOIN agents SL ON J.line_id = SL.id
			LEFT OUTER JOIN agents CFS ON J.cfs_id = CFS.id
			LEFT OUTER JOIN agents CHA ON J.cha_id = CHA.id
			LEFT OUTER JOIN vessels V ON J.vessel_id = V.id
			LEFT OUTER JOIN indian_ports IP ON J.indian_port_id = IP.id
			LEFT OUTER JOIN high_seas HS ON P.job_id = HS.job_id
			LEFT OUTER JOIN parties HSL ON HS.party_id = HSL.id
			LEFT OUTER JOIN parties WP ON J.web_party_id = WP.id
		WHERE P.id = ?";
		$query = $this->db->query($sql, array($job_id));
		$row = $query->row();

		$result = array(
			'to'      => $row->line_email,
			'cc'      => $row->cfs_email,
			'bcc'     => Settings::get('smtp_user'),
			'subject' => 'EN-Block Movement for BL : ' . $row->bl_no,
			'message' => "Dear Sir,

Request you to kindly move $row->containers containers vide BL No: $row->bl_no arriving per $row->vessel_name on $row->eta_date to CFS:$row->cfs_name.

Thanks and Regards
" . Settings::get('company_name') . "

CC to: $row->cfs_name",
		);

		header('Content-type: application/json');
		echo json_encode($result);
	}

	function email_line() {
		$this->load->helper('email');
		$to      = $this->kaabar->checkEmail(explode(';', str_replace(' ', '', $this->input->post('to'))));
		$cc      = $this->kaabar->checkEmail(explode(';', str_replace(' ', '', $this->input->post('cc'))));
		$bcc     = $this->kaabar->checkEmail(explode(';', str_replace(' ', '', $this->input->post('bcc'))));
		$subject = $this->input->post('subject');
		$message = $this->input->post('message');

		if (count($to) > 0) {
			$config = array(
				'protocol'     => 'smtp',
				'smtp_timeout' => 30,
				'smtp_host'    => Settings::get('smtp_host'),
				'smtp_port'    => Settings::get('smtp_port'),
				'smtp_user'    => Settings::get('smtp_user'),
				'smtp_pass'    => Settings::get('smtp_password'),
				'newline'      => "\r\n",
				'crlf'         => "\r\n",
			);

			$this->load->library('email', $config);
			$this->email->from(Settings::get('smtp_user'));
			$this->email->to($to);
			$this->email->cc($cc);
			$this->email->bcc($bcc);
			$this->email->subject($subject);
			$this->email->message($message);
			
			$this->email->send();
			// echo $this->email->print_debugger(); exit;
			setSessionAlert('Email has been sent To: &lt;' . implode(', ', $to) . '&gt;...', 'success');
		}
		redirect($this->agent->referrer());
	}
}
