<?php

use mikehaertl\wkhtmlto\Pdf;

class Stuffing extends MY_Controller {
	function __construct() {
		parent::__construct();

		$this->_table = 'deliveries_stuffings';
	}
	
	function index() {
		if (! Auth::hasAccess()) {
			setSessionError('NO_PERMISSION');
			redirect('main');
		}

		$date            = null;
		$show_all        = null;
		$shipper_id      = null;
		$shipper_site_id = null;

		if($this->input->post('date')) {
			$date          = $this->input->post('date');
			$show_all      = $this->input->post('show_all');
			$shipper_id      = $this->input->post('shipper_id');
			$shipper_site_id = $this->input->post('shipper_site_id');
			$this->session->set_userdata($this->_class.'_date', $date);
			$this->session->set_userdata($this->_class.'_show_all', $show_all);
			$this->session->set_userdata($this->_class.'_shipper_id', $shipper_id);
			$this->session->set_userdata($this->_class.'_shipper_site_id', $shipper_site_id);
		}
		
		if($date == null) {
			$date            = $this->session->userdata($this->_class.'_date');
			$show_all        = $this->session->userdata($this->_class.'_show_all');
			$shipper_id      = $this->session->userdata($this->_class.'_shipper_id');
			$shipper_site_id = $this->session->userdata($this->_class.'_shipper_site_id');
		}

		$data['date']              = $date ? $date : date('d-m-Y');
		$data['show_all']          = $show_all ? $show_all : 0;
		$data['shipper_id']        = $shipper_id ? $shipper_id : 0;
		$data['shipper_name']      = $this->kaabar->getField('parties', $data['shipper_id']);
		$data['shipper_site_id']   = $shipper_site_id ? $shipper_site_id : 0;
		$data['shipper_site_name'] = $this->kaabar->getField('party_sites', $data['shipper_site_id']);
		$default_company           = $this->session->userdata("default_company");

		$data['rows'] = $this->_getPending($data['date'], $data['show_all'], $data['shipper_id'], $data['shipper_site_id']);

		$data['page']       = $this->_clspath.$this->_class;
		$data['page_title'] = humanize($this->_class . ' Program');
		$data['hide_title'] = true;
		$data['docs_url']   = $this->_docs;
		$this->load->view('index', $data);
	}

	function _getPending($date, $show_all, $shipper_id, $shipper_site_id, $stuffing_id = false) {
		$sql_show_all        = ($show_all ? 'AND S.stuffing_date >= ? ' : 'AND S.stuffing_date = ? ');
		$sql_shipper_id      = ($shipper_id > 0 ? "AND J.shipper_id = $shipper_id " : '');
		$sql_shipper_site_id = ($shipper_site_id > 0 ? "AND J.shipper_site_id = $shipper_site_id " : '');
		$sql_stuffing_id     = ($stuffing_id ? "AND S.id IN ($stuffing_id) " : '');
		$location_id         = (Settings::get('pickup_location_id', 0) > 0 ? ' AND PICK.location_id = ' . Settings::get('pickup_location_id') : '');

		$sql = "SELECT J.id AS job_id, J.id2_format, S.id, J.shipper_id, J.shipper_site_id, SHPR.name AS shipper_name, 
			PICK.stuffing_location, C.name AS cargo_name, U.code AS unit_code, S.lr_no,
			S.vehicle_no, S.container_no, S.seal_no, CONCAT(CT.size, ' ', CT.code) AS container_type,
			IP.name AS custom_port_id, GP.name AS gateway_port, J.fpod, L.code AS line_code, DATE_FORMAT(S.stuffing_date, '%d-%m-%Y') AS stuffing_date,
			CONCAT(V.name, ' ', V.voyage_no) AS vessel_name, DATE_FORMAT(V.eta_date, '%d-%m-%Y') AS eta_date, 
			DATE_FORMAT(V.etd_date, '%d-%m-%Y') AS etd_date, DATE_FORMAT(V.gate_cutoff_date, '%d-%m-%Y') AS gate_cutoff_date, 
			J.booking_no, S.email_sent_stuffing
		FROM deliveries_stuffings S INNER JOIN container_types CT ON S.container_type_id = CT.id
			INNER JOIN jobs J ON S.job_id = J.id
			LEFT OUTER JOIN parties SHPR ON J.shipper_id = SHPR.id
			LEFT OUTER JOIN indian_ports IP ON J.custom_port_id = IP.id
			LEFT OUTER JOIN indian_ports GP ON J.loading_port_id = GP.id
			LEFT OUTER JOIN products C ON J.product_id = C.id
			LEFT OUTER JOIN units U ON J.unit_id = U.id
			LEFT OUTER JOIN vessels V ON J.vessel_id = V.id
			LEFT OUTER JOIN agents L ON J.line_id = L.id
			LEFT OUTER JOIN pickups PICK ON S.pickup_id = PICK.id
		WHERE J.stuffing_type != 'CFS' " . 
			($sql_stuffing_id ? $sql_stuffing_id : $sql_show_all . $sql_shipper_id . $sql_shipper_site_id . $location_id) . 
		" ORDER BY SHPR.name, stuffing_location, J.booking_no";
		$query  = $this->db->query($sql, array(convDate($date)));
		$rows   = $query->result_array();
		$result = array();
		foreach ($rows as $row) {
			$result[$row['id']] = $row;
		}
		return $result;
	}

	function preview($pdf = 0, $email = 0) {
		if ($email) {
			$this->load->helper('email');
			$to      = $this->kaabar->checkEmail(explode(';', str_replace(' ', '', $this->input->post('to'))));
			$cc      = $this->kaabar->checkEmail(explode(';', str_replace(' ', '', $this->input->post('cc'))));
			$bcc     = $this->kaabar->checkEmail(explode(';', str_replace(' ', '', $this->input->post('bcc'))));
			$subject = $this->input->post('subject');
			$message = $this->input->post('message');
			$attachment_type = $this->input->post('attachment_type');
			if ($email && count($to) > 0) {
				$this->load->helper('file');
				if ($attachment_type == 'PDF') {
					$stuffing_id     = $this->input->post('stuffing_id');
					$default_company = $this->session->userdata('default_company');
					$data['company'] = $this->kaabar->getRow('companies', $default_company['id']);
					$date            = $this->session->userdata($this->_class.'_date');
					$show_all        = $this->session->userdata($this->_class.'_show_all');
					$shipper_id      = $this->session->userdata($this->_class.'_shipper_id');
					$shipper_site_id = $this->session->userdata($this->_class.'_shipper_site_id');
					
					$data['rows']         = $this->_getPending($date, $show_all, $shipper_id, $shipper_site_id, $stuffing_id);
					$data['shipper']      = $this->kaabar->getRow('parties', $shipper_id);
					$data['shipper_site'] = $this->kaabar->getRow('party_sites', $shipper_site_id);
					$data['date']         = date('d-m-Y');
					$data['template']     = $this->kaabar->getField('document_templates', 'Stuffing Program', 'name', 'template');
					$data['page_title']   = humanize($this->_class . ' Program');

					$file = FCPATH.'tmp/'.$this->_class . '_program_'.$date.'.pdf';
					$html = $this->load->view($this->_clspath.$this->_class.'_preview', $data, true);
					$pdf = new Pdf(array(
						'no-outline',
						'binary'      => FCPATH.'wkhtmltopdf',
						'orientation' => 'Landscape',
					));
					$pdf->addPage($html);
					$pdf->saveAs($file);

					$this->db->query("UPDATE $this->_table SET email_sent_stuffing='Yes' WHERE id IN (" . implode(',', array_keys($data['rows'])) . ")");
				}
				else 
					$file = $this->excel($email);
				
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
				$this->email->message(nl2br($message));
				$this->email->attach($file);
				$this->email->send();
				unlink($file);
				setSessionAlert('Email has been sent to &lt;' . $to . '&gt;...', 'success');
				
				//echo $this->email->print_debugger(); exit;
				
				$this->load->library('user_agent');
				redirect($this->agent->referrer());
				return;
			}
		}

		$stuffing_id          = $this->input->post('stuffing_id');
		$default_company      = $this->session->userdata('default_company');
		$data['company']      = $this->kaabar->getRow('companies', $default_company['id']);
		$date                 = $this->session->userdata($this->_class.'_date');
		$show_all             = $this->session->userdata($this->_class.'_show_all');
		$shipper_id           = $this->session->userdata($this->_class.'_shipper_id');
		$shipper_site_id      = $this->session->userdata($this->_class.'_shipper_site_id');
		$data['rows']         = $this->_getPending($date, $show_all, $shipper_id, $shipper_site_id, $stuffing_id);
		$data['shipper']      = $this->kaabar->getRow('parties', $shipper_id);
		$data['shipper_site'] = $this->kaabar->getRow('party_sites', $shipper_site_id);
		$data['date']         = date('d-m-Y');
		$data['template']     = $this->kaabar->getField('document_templates', 'Stuffing Program', 'name', 'template');
		$data['page_title']   = humanize($this->_class . ' Program');
		
		if ($pdf) {
			$filename = $this->_class . '_program_'.$date;
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
	
	function excel($email = 0) {
		ini_set('max_execution_time', '0');
		ini_set('memory_limit', '512M');

		set_include_path(get_include_path() . PATH_SEPARATOR . 'assets/phpexcel');

		include 'PHPExcel/IOFactory.php';

		$filename = $this->_class."_program.xlsx";
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

		$stuffing_id   = $this->input->post('stuffing_id');
		$date          = $this->session->userdata($this->_class.'_date');
		$show_all      = $this->session->userdata($this->_class.'_show_all');
		$shipper_id      = $this->session->userdata($this->_class.'_shipper_id');
		$shipper_site_id = $this->session->userdata($this->_class.'_shipper_site_id');

		$date            = $date ? $date : date('d-m-Y');
		$show_all        = $show_all ? $show_all : 0;
		$shipper_id      = $shipper_id ? $shipper_id : 0;
		$shipper_name    = $this->kaabar->getField('parties', $shipper_id);
		$shipper_site_id = $shipper_site_id ? $shipper_site_id : 0;

		$rows = $this->_getPending($date, $show_all, $shipper_id, $shipper_site_id, $stuffing_id);
		
		if (count($rows) == 0) {
			echo closeWindow();
			return;
		}

		$hide_cols = array('id', 'job_id', 'id', 'shipper_id', 'shipper_site_id', 'booking_no', 'email_sent_stuffing');

		// Header
		$header = reset($rows);
		$col = 'A';
		foreach ($header as $f => $v) {
			if (in_array($f, $hide_cols))
				continue;
			$objPHPExcel->getActiveSheet()->setCellValue($col . '1', humanize($f));
			$objPHPExcel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
			$col++;
		}
		$objPHPExcel->getActiveSheet()->getStyle('A1:' . $col . '1')->applyFromArray($styleHeading);
		
		// Data
		$i = 2;
		foreach ($rows as $row) {
			$j = 'A';
			foreach ($row as $f => $v) {
				if (in_array($f, $hide_cols))
					continue;
				$objPHPExcel->getActiveSheet()->setCellValue($j++ . $i, html_entity_decode($v));
			}
			$i++;
		}
		$objPHPExcel->getActiveSheet()->getStyle('A1:'.$col.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$objPHPExcel->getActiveSheet()->getStyle('A1:'.$col.$i)->applyFromArray($styleSheet);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");

		if ($email) {
			$this->db->query("UPDATE $this->_table SET email_sent_stuffing='Yes' WHERE id IN (" . implode(',', array_keys($rows)) . ")");
			$objWriter->save(FCPATH.'tmp/'.$filename);
			return FCPATH.'tmp/'.$filename;
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


	function _get($job_id) {
		$sql = "SELECT J.id AS job_id, J.id2_format, S.id, J.shipper_id, J.shipper_site_id, SHPR.name AS shipper_name, 
			COALESCE(SHPRS.name, G.name) AS stuffing_location, C.name AS cargo_name, U.code AS unit_code,
			S.vehicle_no, S.container_no, S.seal_no, CONCAT(CT.size, ' ', CT.code) AS container_type,
			IP.name AS custom_port_id, J.fpod, L.code AS line_code, DATE_FORMAT(S.stuffing_date, '%d-%m-%Y') AS stuffing_date,
			V.name AS vessel_name, DATE_FORMAT(V.eta_date, '%d-%m-%Y') AS eta_date, DATE_FORMAT(V.gate_cutoff_date, '%d-%m-%Y') AS gate_cutoff_date,
			J.booking_no, S.email_sent_stuffing
		FROM deliveries_stuffings S INNER JOIN container_types CT ON S.container_type_id = CT.id
			INNER JOIN jobs J ON S.job_id = J.id
			LEFT OUTER JOIN parties SHPR ON J.shipper_id = SHPR.id
			LEFT OUTER JOIN party_sites SHPRS ON (J.stuffing_type = 'Factory' AND J.shipper_site_id = SHPRS.id)
			LEFT OUTER JOIN godowns G ON (J.stuffing_type = 'Godown' AND J.godown_id = G.id)
			LEFT OUTER JOIN indian_ports IP ON J.custom_port_id = IP.id
			LEFT OUTER JOIN products C ON J.product_id = C.id
			LEFT OUTER JOIN units U ON J.unit_id = U.id
			LEFT OUTER JOIN vessels V ON J.vessel_id = V.id
			LEFT OUTER JOIN agents L ON J.line_id = L.id
		WHERE J.id = ? 
		ORDER BY SHPR.name, C.name, J.booking_no";
		$query  = $this->db->query($sql, array($job_id));
		$rows   = $query->result_array();
		$result = array();
		foreach ($rows as $row) {
			$result[$row['id']] = $row;
		}
		return $result;
	}

	function previewJob($job_id, $pdf = 0, $email = 0) {
		if ($email) {
			$this->load->helper('email');
			$to      = $this->kaabar->checkEmail(explode(';', str_replace(' ', '', $this->input->post('to'))));
			$cc      = $this->kaabar->checkEmail(explode(';', str_replace(' ', '', $this->input->post('cc'))));
			$bcc     = $this->kaabar->checkEmail(explode(';', str_replace(' ', '', $this->input->post('bcc'))));
			$subject = $this->input->post('subject');
			$message = $this->input->post('message');
			$attachment_type = $this->input->post('attachment_type');
			if ($email && count($to) > 0) {
				$this->load->helper('file');
				if ($attachment_type == 'PDF') {
					$stuffing_id     = $this->input->post('stuffing_id');
					$default_company = $this->session->userdata('default_company');
					$data['company'] = $this->kaabar->getRow('companies', $default_company['id']);
					
					$data['rows']         = $this->_getPending($job_id);
					$shipper_id           = $this->session->userdata($this->_class.'_shipper_id');
					$shipper_site_id      = $this->session->userdata($this->_class.'_shipper_site_id');
					$data['rows']         = $this->_getPending($date, $show_all, $shipper_id, $shipper_site_id, $stuffing_id);
					$data['shipper']      = $this->kaabar->getRow('parties', $shipper_id);
					$data['shipper_site'] = $this->kaabar->getRow('party_sites', $shipper_site_id);
					$data['date']         = date('d-m-Y');
					$data['template']     = $this->kaabar->getField('document_templates', 'Stuffing Program', 'name', 'template');
					$data['page_title']   = humanize($this->_class . ' Program');

					$file = FCPATH.'tmp/'.$this->_class . '_program_'.$date.'.pdf';
					$html = $this->load->view($this->_clspath.$this->_class.'_preview', $data, true);
					$pdf = new Pdf(array(
						'no-outline',
						'binary'      => FCPATH.'wkhtmltopdf',
						'orientation' => 'Landscape',
					));
					$pdf->addPage($html);
					$pdf->saveAs($file);

					$this->db->query("UPDATE $this->_table SET email_sent_stuffing='Yes' WHERE id IN (" . implode(',', array_keys($data['rows'])) . ")");
				}
				else 
					$filename = $this->excel($email);
				
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
				$this->email->message(nl2br($message));
				$this->email->attach($filename);
				$this->email->send();
				unlink($file);
				setSessionAlert('Email has been sent to &lt;' . $to . '&gt;...', 'success');
				
				//echo $this->email->print_debugger(); exit;
				
				$this->load->library('user_agent');
				redirect($this->agent->referrer());
				return;
			}
		}

		$stuffing_id          = $this->input->post('stuffing_id');
		$default_company      = $this->session->userdata('default_company');
		$data['company']      = $this->kaabar->getRow('companies', $default_company['id']);
		$date                 = $this->session->userdata($this->_class.'_date');
		$show_all             = $this->session->userdata($this->_class.'_show_all');
		$shipper_id           = $this->session->userdata($this->_class.'_shipper_id');
		$shipper_site_id      = $this->session->userdata($this->_class.'_shipper_site_id');
		$data['rows']         = $this->_getPending($date, $show_all, $shipper_id, $shipper_site_id, $stuffing_id);
		$data['shipper']      = $this->kaabar->getRow('parties', $shipper_id);
		$data['shipper_site'] = $this->kaabar->getRow('party_sites', $shipper_site_id);
		$data['date']         = date('d-m-Y');
		$data['template']     = $this->kaabar->getField('document_templates', 'Stuffing Program', 'name', 'template');
		$data['page_title']   = humanize($this->_class . ' Program');
		
		if ($pdf) {
			$filename = $this->_class . '_program_'.$date;
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
}
