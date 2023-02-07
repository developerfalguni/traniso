<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use mikehaertl\wkhtmlto\Pdf;
class Inward extends MY_Controller {
	function __construct() {
		parent::__construct();

		$this->_fields = array(
			'party' => 'A.name',
			'bl'    => 'CJ.bl_no',
			'sb'    => 'CJ.sb_no',
		);
	}
	
	function index() {
		$from_date = null;
		$to_date   = null;
		$party_id  = null;
		
		if ($this->input->post('from_date')) {
			$from_date = $this->input->post('from_date');
			$to_date   = $this->input->post('to_date');
			$party_id  = $this->input->post('party_id');
			$this->session->set_userdata($this->_class.'_from_date', $from_date);
			$this->session->set_userdata($this->_class.'_to_date', $to_date);
			$this->session->set_userdata($this->_class.'_party_id', $party_id);
		}
		
		if ($from_date == null) {
			$from_date = $this->session->userdata($this->_class.'_from_date');
			$to_date   = $this->session->userdata($this->_class.'_to_date');
			$party_id  = $this->session->userdata($this->_class.'_party_id');
		}

		$data['from_date'] = $from_date ? $from_date : date('01-m-Y');
		$data['to_date']   = $to_date ? $to_date : date('d-m-Y');
		$data['party_id']  = $party_id ? $party_id : 0;
		
		$data['rows'] = $this->_get($data['from_date'], $data['to_date'], $data['party_id']);

		$default_company = $this->session->userdata("default_company");
		$data['years']   = explode('_', $default_company['financial_year']);

		$this->load->helper('datefn');
		$data['javascript'] = ['bootstrap-daterangepicker/daterangepicker.js'];
		$data['stylesheet'] = ['bootstrap-daterangepicker/daterangepicker.css'];

		$data['page']       = $this->_clspath.$this->_class;
		$data['page_title'] = humanize($this->_class);
		$data['docs_url']   = $this->_docs;
		$this->load->view('index', $data);
	}

	function _get($from_date, $to_date, $party_id) {
		$party_search = ($party_id > 0 ? "AND J.party_id = $party_id " : '');
		$sql = "SELECT J.id, J.id2_format, J.party_id, P.name AS party_name, DATE_FORMAT(J.date, '%d-%m-%Y') AS date, 
			SUM(IB.amount) AS inward_amount, B.outward_amount, 
			(B.outward_amount - SUM(IB.tds_amount + IB.amount)) AS balance_amount
		FROM jobs J INNER JOIN parties P ON J.party_id = P.id
			LEFT OUTER JOIN inwards IB ON J.id = IB.job_id
			LEFT OUTER JOIN (
				SELECT B.job_id, SUM(B.amount) AS outward_amount
				FROM outwards B INNER JOIN jobs J ON B.job_id = J.id
				WHERE (J.date >= ? AND J.date <= ?) $party_search
				GROUP BY J.id
			)B ON J.id = B.job_id
		WHERE (J.date >= ? AND J.date <= ?) $party_search
		GROUP BY J.id
		ORDER BY J.id2 DESC";
		$query = $this->db->query($sql, array(
			convDate($from_date), convDate($to_date),
			convDate($from_date), convDate($to_date)
		));
		return $query->result_array();
	}

	function _getDetails($from_date, $to_date, $party_id = 0) {
		$result = array(
			'inward'  => array(),
			'outward' => array(),
		);

		if ($party_id == 0)
			return $result;

		$sql = "SELECT J.id2_format, C.name AS cargo_name, IP.name AS loading_port, J.final_place_of_delivery, V.name AS vessel_name, I.*,
			DATE_FORMAT(I.date, '%d-%m-%Y') AS date, DATE_FORMAT(I.cheque_date, '%d-%m-%Y') AS cheque_date,
			GROUP_CONCAT(ID.particulars SEPARATOR ', ') AS particulars
		FROM jobs J INNER JOIN inwards I ON J.id = I.job_id
			LEFT OUTER JOIN inward_details ID ON I.id = ID.inward_id
			LEFT OUTER JOIN indian_ports IP ON J.loading_port_id = IP.id
			LEFT OUTER JOIN cargos C ON J.cargo_id = C.id
			LEFT OUTER JOIN vessels V ON J.vessel_id = V.id
		WHERE J.date >= ? AND J.date <= ? AND J.party_id = ?
		GROUP BY I.id
		ORDER BY I.date";
		$query = $this->db->query($sql, array(
			convDate($from_date), convDate($to_date), $party_id
		));
		$result['inward'] = $query->result_array();

		$sql = "SELECT J.id2_format, C.name AS cargo_name, IP.name AS loading_port, J.final_place_of_delivery, V.name AS vessel_name, B.*,
			DATE_FORMAT(B.date, '%d-%m-%Y') AS date
		FROM jobs J INNER JOIN outwards B ON J.id = B.job_id
			LEFT OUTER JOIN indian_ports IP ON J.loading_port_id = IP.id
			LEFT OUTER JOIN cargos C ON J.cargo_id = C.id
			LEFT OUTER JOIN vessels V ON J.vessel_id = V.id
		WHERE J.date >= ? AND J.date <= ? AND J.party_id = ?
		GROUP BY B.id
		ORDER BY B.date";
		$query = $this->db->query($sql, array(
			convDate($from_date), convDate($to_date), $party_id
		));
		$result['outward'] = $query->result_array();
		
		return $result;
	}

	function preview($pdf = 0, $email = 0) {
		$default_company = $this->session->userdata('default_company');
		$data['company'] = $this->kaabar->getRow('companies', $default_company['id']);
		$from_date       = $this->session->userdata($this->_class.'_from_date');
		$to_date         = $this->session->userdata($this->_class.'_to_date');
		$party_id        = $this->session->userdata($this->_class.'_party_id');
		$data['rows']    = $this->_getDetails($from_date, $to_date, $party_id);

		$data['party']      = $this->kaabar->getRow('parties', $party_id);
		$data['date']       = date('d-m-Y');
		$data['page_title'] = humanize($this->_class . ' Report');
		
		if ($pdf) {
			$filename = underscore($data['page_title']);
			$html = $this->load->view($this->_clspath.$this->_class.'_preview', $data, true);
			
			$this->load->helper('email');
			$to      = $this->kaabar->checkEmail(explode(';', str_replace(' ', '', $this->input->post('to'))));
			$cc      = $this->kaabar->checkEmail(explode(';', str_replace(' ', '', $this->input->post('cc'))));
			$bcc     = $this->kaabar->checkEmail(explode(';', str_replace(' ', '', $this->input->post('bcc'))));
			$subject = $this->input->post('subject');
			$message = $this->input->post('message');
			if ($email && count($to) > 0) {
				$this->load->helper('file');
				$file = FCPATH."tmp/$filename.pdf";
				$pdf = new Pdf(array(
					'no-outline',
					'binary' => FCPATH.'wkhtmltopdf',
				));
				$pdf->addPage($html);
				$pdf->saveAs($file);
				
				$config = array(
					'protocol' => 'smtp',
					'smtp_timeout' => 30,
					'smtp_host' => Settings::get('smtp_host'),
					'smtp_port' => Settings::get('smtp_port'),
					'smtp_user' => Settings::get('smtp_user'),
					'smtp_pass' => Settings::get('smtp_password'),
					'newline'   => "\r\n",
					'crlf'      => "\r\n"
				);
				$this->load->library('email', $config);
				$this->email->from(Settings::get('smtp_user'));
				$this->email->to($to);
				$this->email->cc($cc);
				$this->email->bcc($bcc);
				$this->email->subject($subject);
				$this->email->message($message);
				$this->email->attach($file);
				$this->email->send();
				unlink($file);
				setSessionAlert('Email has been sent to &lt;' . $to . '&gt;...', 'success');
				
				$this->db->query("UPDATE $this->_table SET email_sent='Yes' WHERE id IN (" . implode(',', array_keys($data['rows'])) . ")");
				//echo $this->email->print_debugger(); exit;
				
				redirect($this->agent->referrer());
			}
			elseif ($pdf) {
				$pdf = new Pdf(array(
					'no-outline',
					'binary' => FCPATH.'wkhtmltopdf',
				));
				$pdf->addPage($html);
				$pdf->send("$filename.pdf");
			}
		}
		else {
			$this->load->view($this->_clspath.$this->_class.'_preview', $data);
		}
	}
	
	function excel() {
		$filename = $this->_class."_program.xlsx";
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

		$from_date = $this->session->userdata($this->_class.'_from_date');
		$to_date   = $this->session->userdata($this->_class.'_to_date');
		$party_id  = $this->session->userdata($this->_class.'_party_id');
		$rows      = $this->_getPending($from_date, $to_date, $party_id);

		// Header
		$header = array_keys($rows[0]);
		$col = 'A';
		foreach ($header as $j => $h) {
			$sheet->setCellValue($col . '1', humanize($h));
			$sheet->getColumnDimension($col)->setAutoSize(true);
			$col++;
		}
		$sheet->getStyle('A1:' . $col . '1')->applyFromArray($styleHeading);
		
		// Data
		$i = 2;
		foreach ($rows as $row) {
			$j = 'A';
			foreach ($row as $f => $v) {
				$sheet->setCellValue($j++ . $i, html_entity_decode($v));
			}
			$i++;
		}
		$sheet->getStyle('A1:' . $col . $i)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
		$sheet->getStyle('A1:' . $col . $i)->applyFromArray($styleSheet);

		$sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
		$sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

		// redirect output to client browser
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="' . $filename.'"');
		header('Cache-Control: max-age=0');
		$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
		$writer->save('php://output');
	}

	function _getJobDetails($job_id) {
		$result = array(
			'inward'  => array(),
			'outward' => array(),
		);

		if ($job_id == 0)
			return $result;

		$sql = "SELECT J.id2_format, C.name AS cargo_name, IP.name AS loading_port, J.final_place_of_delivery, V.name AS vessel_name, I.*,
			DATE_FORMAT(I.date, '%d-%m-%Y') AS date, DATE_FORMAT(I.cheque_date, '%d-%m-%Y') AS cheque_date,
			GROUP_CONCAT(ID.particulars SEPARATOR ', ') AS particulars
		FROM jobs J INNER JOIN inwards I ON J.id = I.job_id
			LEFT OUTER JOIN inward_details ID ON I.id = ID.inward_id
			LEFT OUTER JOIN indian_ports IP ON J.loading_port_id = IP.id
			LEFT OUTER JOIN cargos C ON J.cargo_id = C.id
			LEFT OUTER JOIN vessels V ON J.vessel_id = V.id
		WHERE J.id = ?
		GROUP BY I.id
		ORDER BY I.date";
		$query = $this->db->query($sql, array($job_id));
		$result['inward'] = $query->result_array();

		$sql = "SELECT J.id2_format, C.name AS cargo_name, IP.name AS loading_port, J.final_place_of_delivery, V.name AS vessel_name, B.*,
			DATE_FORMAT(B.date, '%d-%m-%Y') AS date
		FROM jobs J INNER JOIN outwards B ON J.id = B.job_id
			LEFT OUTER JOIN indian_ports IP ON J.loading_port_id = IP.id
			LEFT OUTER JOIN cargos C ON J.cargo_id = C.id
			LEFT OUTER JOIN vessels V ON J.vessel_id = V.id
		WHERE J.id = ?
		GROUP BY B.id
		ORDER BY B.date";
		$query = $this->db->query($sql, array($job_id));
		$result['outward'] = $query->result_array();
		
		return $result;
	}

	function previewJob($job_id, $pdf = 0, $email = 0) {
		$default_company = $this->session->userdata('default_company');
		$data['company'] = $this->kaabar->getRow('companies', $default_company['id']);
		$party_id        = $this->kaabar->getField('jobs', $job_id, 'id', 'party_id');
		$data['rows']    = $this->_getJobDetails($job_id);

		$data['party']      = $this->kaabar->getRow('parties', $party_id);
		$data['date']       = date('d-m-Y');
		$data['page_title'] = humanize($this->_class . ' Report');
		
		if ($pdf) {
			$filename = underscore($data['page_title']);
			$html = $this->load->view($this->_clspath.$this->_class.'_preview', $data, true);
			
			$this->load->helper('email');
			$to      = $this->kaabar->checkEmail(explode(';', str_replace(' ', '', $this->input->post('to'))));
			$cc      = $this->kaabar->checkEmail(explode(';', str_replace(' ', '', $this->input->post('cc'))));
			$bcc     = $this->kaabar->checkEmail(explode(';', str_replace(' ', '', $this->input->post('bcc'))));
			$subject = $this->input->post('subject');
			$message = $this->input->post('message');
			if ($email && count($to) > 0) {
				$this->load->helper('file');
				$file = FCPATH."tmp/$filename.pdf";
				$pdf = new Pdf(array(
					'no-outline',
					'binary' => FCPATH.'wkhtmltopdf',
				));
				$pdf->addPage($html);
				$pdf->saveAs($file);
				
				$config = array(
					'protocol' => 'smtp',
					'smtp_timeout' => 30,
					'smtp_host' => Settings::get('smtp_host'),
					'smtp_port' => Settings::get('smtp_port'),
					'smtp_user' => Settings::get('smtp_user'),
					'smtp_pass' => Settings::get('smtp_password'),
					'newline'   => "\r\n",
					'crlf'      => "\r\n"
				);
				$this->load->library('email', $config);
				$this->email->from(Settings::get('smtp_user'));
				$this->email->to($to);
				$this->email->cc($cc);
				$this->email->bcc($bcc);
				$this->email->subject($subject);
				$this->email->message($message);
				$this->email->attach($file);
				$this->email->send();
				unlink($file);
				setSessionAlert('Email has been sent to &lt;' . $to . '&gt;...', 'success');
				
				$this->db->query("UPDATE $this->_table SET email_sent='Yes' WHERE id IN (" . implode(',', array_keys($data['rows'])) . ")");
				//echo $this->email->print_debugger(); exit;
				
				redirect($this->agent->referrer());
			}
			elseif ($pdf) {
				$pdf = new Pdf(array(
					'no-outline',
					'binary' => FCPATH.'wkhtmltopdf',
				));
				$pdf->addPage($html);
				$pdf->send("$filename.pdf");
			}
		}
		else {
			$this->load->view($this->_clspath.$this->_class.'_preview', $data);
		}
	}
}
