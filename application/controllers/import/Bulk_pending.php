<?php

class Bulk_pending extends MY_Controller {
	var $_b_map, $_c_map;
	var $_b_fields, $_c_fields;
	var $_parsed_search;

	function __construct() {
		parent::__construct();

		$this->_table = 'import_details';
		$this->load->model('import');
		$this->load->helper('datefn');

		$this->_b_map = array(
			'party'    => 'PL.name',
			'hss'      => 'HSL.name',
			'bl'       => 'J.bl_no',
			'category' => 'PRD.category',
			'product'  => 'PRD.name',
			'vessel'   => 'V.name',
			'port'     => 'IP.name',
			'shipper'  => 'J.vi_shipper_name',
			'status'   => 'IT.status'
		);
		$this->_b_fields = array(
			'party' 	=> 'Party Name',
			'hss'		=> 'High Seas Sale',
			'bl' 		=> 'BL No',
			'product'	=> 'Product',
			'vessel' 	=> 'Vessel Name',
			'port'		=> 'Port',
			'shipper' 	=> 'Shipper Name',
			'status' 	=> 'IceGate Status'
		);
	}
	
	function index() {
		$this->edit();
	}

	function edit() {
		$starting_row = intval($starting_row);$search = $this->session->userdata($this->_class.'_search');
		$sortby = $this->session->userdata($this->_class.'_sortby');

		if($this->input->post('search_form')) {
			$search = addslashes($this->input->post('search'));
			$sortby = $this->input->post('sortby');
			$this->session->set_userdata($this->_class.'_search', $search);
			$this->session->set_userdata($this->_class.'_sortby', $sortby);
		}
		
		if ($search == null) {
			$search = '';
			$this->session->set_userdata($this->_class.'_search', $search);
		}

		if ($sortby == null) {
			$sortby = 'V.name, PL.name';
			$this->session->set_userdata($this->_class.'_sortby', $sortby);
		}
		$data['search'] = $search;
		$data['sortby'] = $sortby;
		$this->_parsed_search  = $this->kaabar->parseSearch($search);
		$data['parsed_search'] = $this->_parsed_search;

		if (is_array($this->_parsed_search)) {
			$search = '';
			foreach ($this->_parsed_search as $key => $value) {
				$search .= $key.':'.$value.' ';
			}
			$data['search'] = $search;
		}
		
		if ($this->input->post('bulk_form') == 1) {
			$this->_update_bulk();
			setSessionAlert('Changes saved successfully', 'success');
		}

		$data['label_class'] = $this->import->getLabelClass();
		$data['icegate_class'] = array(
			''        => 'label-default',
			'RMS'     => 'label-success',
			'OFFICER' => 'label-warning',
			'APP'     => 'label-warning',
			'N.A.'    => 'label-default',
			'OOC'     => 'label-success',
			'EXAM'    => 'label-default',
			'PAYMENT' => 'label-blue',
			'ASSESS'  => 'label-warning',
			'APPRA'   => 'label-info',
		);

		$this->load->helper('datefn');
		$data['search_fields']  = $this->_b_fields;
		$data['rows'] = $this->_bulkPendings($search, $sortby);

		$data['auto_refresh'] = 300;
		
		$data['docs_url']    = $this->_docs;
		$data['page_title']  = $this->_class;
		$data['hide_title']  = true;
		$data['hide_footer'] = true;
		$data['page']        = $this->_clspath.$this->_class;
		$this->load->view('index', $data);
	}
	
	function _bulkPendings($search = '', $sortby = '') {
		$sql = "SELECT P.id, P.job_id, J.id2_format, PL.name AS party_name, 
			GROUP_CONCAT(DISTINCT HSL.name ORDER BY HSL.id SEPARATOR '<br />') AS high_seas, PRD.category,
			PRD.name AS product, J.details, IP.name AS port, J.packages, PK.code AS package_type, 
			IF(J.cbm > 0, J.cbm, J.net_weight) AS cbm, J.net_weight, J.net_weight_unit, 
			IF(LENGTH(TRIM(J.bl_no)) = 0, 'Missing BL No', J.bl_no) AS bl_no, DATE_FORMAT(J.bl_date, '%d-%m-%Y') AS bl_date, 
			J.be_type, J.be_no, IT.appraisement, IT.last_fetched, IT.last_status AS last_status, IT.status AS current_status, 
			IT.section_48, IT.query_raised, 
			IC.id AS challan_id, J.vessel_id, V.name AS vessel_name, V.voyage_no, IF(ISNULL(SL.id), J.vi_shipper_name, SL.name) AS shipper_name, 
			P.custom_duty, P.stamp_duty, J.status
		FROM import_details P INNER JOIN jobs J ON P.job_id = J.id
			INNER JOIN parties PL ON J.party_id = PL.id
			INNER JOIN products PRD ON J.product_id = PRD.id
			INNER JOIN indian_ports IP ON J.indian_port_id = IP.id
			INNER JOIN package_types PK ON J.package_type_id = PK.id
			LEFT OUTER JOIN agents SL ON J.shipper_id = SL.id
			LEFT OUTER JOIN vessels V ON J.vessel_id = V.id
			LEFT OUTER JOIN high_seas HS ON P.job_id = HS.job_id
			LEFT OUTER JOIN parties HSL ON HS.party_id = HSL.id
			LEFT OUTER JOIN icegate_be IT ON J.id = IT.job_id
			LEFT OUTER JOIN icegate_challans IC ON IT.challan_no = IC.challan_no
		WHERE (J.type = 'Import' AND J.cargo_type = 'Bulk')";
				$where = ' AND (';
				if (is_array($this->_parsed_search)) {
					foreach($this->_parsed_search as $key => $value)
						if (isset($this->_b_map[$key]))
							$where .= $this->_b_map[$key] . " LIKE '%$value%' AND ";
					if (strlen($where) > 6)
						$sql .= substr($where, 0, strlen($where) - 5) . ')';
				}
				else {
					$sql .= $where . "PL.name LIKE '%$search%' OR
			HSL.name LIKE '%$search%' OR
			J.bl_no LIKE '%$search%' OR
			J.be_no LIKE '%$search%' OR
			V.name  LIKE '%$search%' OR
			SL.name LIKE '%$search%')";
				}
				$sql .= " AND (
				ISNULL(IT.ooc_date) OR 
				LENGTH(TRIM(IT.ooc_date)) = 0 OR 
				TRIM(IT.ooc_date) = 'N.A.'
			)
		GROUP BY P.job_id
		ORDER BY J.be_no DESC, $sortby";
		$query  = $this->db->query($sql);
		$rows   = $query->result_array();
		$result = array(0);
		foreach ($rows as $r) {
			$result[$r['job_id']] = $r;
		}

		unset($result[0]);
		return $result;
	}

	function _update_bulk() {
		$id = intval($this->input->post('do_payment_id'));
		if ($id > 0) {
			$row = $this->kaabar->getRow($this->_table, $id);
			$data = array();

			$percent = intval($this->input->post('do_percent'));
			if ($percent > 0) {
				if ($percent > 100)	$percent = 100;
				$this->kaabar->save('attached_documents', array(
					'job_id' => $row['job_id'], 
					'date' 	 => date('d-m-Y'),
					'doc_no' => $this->input->post('do_no'),
					'document_type_id' => 35, 
					'remarks' => $percent . '%, ' . $this->input->post('do_pieces') . ' Pcs, ' . $this->input->post('do_cbm') . ' CBM', 
					'received' => 'Yes',
					'received_date' => $this->input->post('do_date'),
					'is_pending' => 'Yes')
				);

				$do = array(
					'job_id'     => $row['job_id'], 
					'do_no'      => $this->input->post('do_no'),
					'date'       => date('d-m-Y'),
					'percentage' => $percent, 
					'pieces'     => $this->input->post('do_pieces'),
					'cbm'        => $this->input->post('do_cbm')
				);
				$this->kaabar->save('delivery_orders', $do);
			}
		}
	}


	function preview($pdf = 0) {
		$search = $this->session->userdata($this->_class.'_search');
		$sortby = $this->session->userdata($this->_class.'_sortby');
		$this->_parsed_search = $this->kaabar->parseSearch($search);

		$default_company = $this->session->userdata('default_company');
		$data['company'] = $this->kaabar->getRow('companies', $default_company['id']);

		$data['rows'] = $this->_bulkPendings($search, $sortby);
		$data['page'] = 'reports/bulk_pending_preview';
		
		$data['page_title'] = humanize($this->_class . ' Report');
		$data['filename']   = strtolower('bulk_' . (strlen($search) > 0 ? $search . '_' : '') . date('d-m-Y'));
		
		$this->_preview($data, $pdf);
	}

	function excel() {
		$search = $this->session->userdata($this->_class.'_search');
		$sortby = $this->session->userdata($this->_class.'_sortby');
		$this->_parsed_search = $this->kaabar->parseSearch($search);

		if (Bulk == 'Tracking') {
			$query = $this->_contTrackingExcel($search, $sortby);
			$rows = $query->result_array();
			$header = array_keys($rows[0]);
		}
		$rows = $this->_bulkPendings($search, $sortby);
		$header = array_keys(reset($rows));

		$filename = (strlen($search) > 0 ? $search . '_' : '') . date('d-m-Y') . ".xlsx";
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
		$search = $this->session->userdata($this->_class.'_search');
		$sortby = $this->session->userdata($this->_class.'_sortby');
		$this->_parsed_search = $this->kaabar->parseSearch($search);

		$default_company = $this->session->userdata('default_company');
		$data['company'] = $this->kaabar->getRow('companies', $default_company['id']);

		$data['rows'] = $this->_bulkPendings($search, $sortby);
		$page = 'reports/pending_' . underscore(Bulk) . '_preview';
		
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
		$this->db->update('jobs', 
			array('status' => $this->input->post('status')),
			array('id'     => $this->input->post('row_id'))
		);

		redirect($this->agent->referrer());
	}

	function updateDO() {
		$id        = $this->input->post('row_id');
		$job_id    = $this->input->post('job_id');
		$do_no     = $this->input->post('do_no');
		$do_date   = $this->input->post('do_date');
		$do_upto   = $this->input->post('do_upto');
		$do_erpark = $this->input->post('do_empty_return_park');

		if ($do_upto != null) {
			if (strlen($do_upto) == 10 && $do_upto != '00-00-0000') {
				// Attach Document
				$do = $this->kaabar->getRow('attached_documents', array('job_id' => $job_id, 'document_type_id' => 35));
				if (! $do) {
					$do = array(
						'id'               => 0,
						'job_id'           => $job_id, 
						'date'             => date('d-m-Y'),
						'doc_no'           => $do_no,
						'document_type_id' => 35,
						'remarks'          => 'Upto: ' . str_replace('-', '/', $do_upto), 
						'received'         => 'Yes',
						'received_date'    => $do_date,
						'is_pending'       => 'Yes'
					);
				}
				else {
					$do['date']          = date('d-m-Y');
					$do['doc_no']        = $do_no;
					$do['remarks']       = 'Upto: ' . str_replace('-', '/', $do_upto);
					$do['received']      = 'Yes';
					$do['received_date'] = $do_date;
					$do['is_pending']    = 'Yes';
				}
				$this->kaabar->save('attached_documents', $do, array('id' => $do['id']));

				// Delivery Order
				if (strlen($do_upto) == 10 && $do_upto != '00-00-0000') {
					$do = $this->kaabar->getRow('delivery_orders', $job_id, 'job_id');
					if (! $do) {
						$do = array(
							'id'       => 0,
							'job_id'   => $job_id, 
							'do_no'    => $do_no,
							'date'     => date('d-m-Y'),
							'validity' => $do_upto, 
							'empty_return_park' => $do_erpark, 
						);
					}
					else {
						$do['do_no']    = $do_no;
						$do['date']     = date('d-m-Y');
						$do['validity'] = $do_upto;
						$do['empty_return_park'] = $do_erpark;
					}
					$this->kaabar->save('delivery_orders', $do, array('id' => $do['id']));
				}
			}
		}

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
}
