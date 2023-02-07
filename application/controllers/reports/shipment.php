<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use mikehaertl\wkhtmlto\Pdf;
class Shipment extends MY_Controller {
		var $_fields;

	function __construct() {
		parent::__construct();

		$this->_fields = array(
			'job_no' => 'J.id2_format',
		);
	}
	
	function index() {
		

		$from_date     = null;
		$to_date       = null;
		$search        = null;
		$shipper_id      = null;
		$shipper_site_id = null;

		if ($this->input->post('from_date')) {
			$from_date     = $this->input->post('from_date');
			$to_date       = $this->input->post('to_date');
			$search        = $this->input->post('search');
			$shipper_id      = $this->input->post('shipper_id');
			$shipper_site_id = $this->input->post('shipper_site_id');
			$this->session->set_userdata($this->_class.'_from_date', $from_date);
			$this->session->set_userdata($this->_class.'_to_date', $to_date);
			$this->session->set_userdata($this->_class.'_search', $search);
			$this->session->set_userdata($this->_class.'_shipper_id', $shipper_id);
			$this->session->set_userdata($this->_class.'_shipper_site_id', $shipper_site_id);
		}
		
		if ($from_date == null) {
			$from_date     = $this->session->userdata($this->_class.'_from_date');
			$to_date       = $this->session->userdata($this->_class.'_to_date');
			$search        = $this->session->userdata($this->_class.'_search');
			$shipper_id      = $this->session->userdata($this->_class.'_shipper_id');
			$shipper_site_id = $this->session->userdata($this->_class.'_shipper_site_id');
		}

		$data['from_date']     = $from_date ? $from_date : date('01-m-Y');
		$data['to_date']       = $to_date ? $to_date : date('d-m-Y');
		$data['search']        = $search ? $search : '';
		$parsed_search         = $this->kaabar->parseSearch($search);
		$data['parsed_search'] = $parsed_search;
		$data['search_fields'] = $this->_fields;

		if (is_array($parsed_search)) {
			$search = '';
			foreach ($parsed_search as $key => $value) {
				$search .= $key.':'.$value.' ';
			}
			$data['search'] = $search;
		}
		$data['shipper_id']        = $shipper_id ? $shipper_id : 0;
		$data['shipper_name']      = $this->kaabar->getField('parties', $data['shipper_id']);
		$data['shipper_site_id']   = $shipper_site_id ? $shipper_site_id : 0;
		$data['shipper_site_name'] = $this->kaabar->getField('party_sites', $data['shipper_site_id']);

		$data['rows'] = $this->_getShipment($data['from_date'], $data['to_date'], $data['search'], $parsed_search, $data['shipper_id'], $data['shipper_site_id']);

		$default_company = $this->session->userdata("default_company");
		$data['years']   = explode('_', $default_company['financial_year']);

		$this->load->helper('datefn');
		$data['javascript'] = ['bootstrap-daterangepicker/daterangepicker.js'];
		$data['stylesheet'] = ['bootstrap-daterangepicker/daterangepicker.css'];

		$data['template']   = $this->kaabar->getField('document_templates', 'Shipment Details cum Stuffing Report', 'name', 'template');
		$data['page']       = $this->_clspath.$this->_class;
		$data['page_title'] = humanize($this->_class . ' Details cum Stuffing Report');
		$data['docs_url']   = $this->_docs;
		$this->load->view('index', $data);
	}

	function _getShipment($from_date, $to_date, $search, $parsed_search, $shipper_id, $shipper_site_id, $stuffing_id = false) {
		$sql_shipper_id      = ($shipper_id > 0 ? "AND J.shipper_id = $shipper_id " : '');
		$sql_shipper_site_id = ($shipper_site_id > 0 ? "AND CJ.shipper_site_id = $shipper_site_id " : '');
		$sql_stuffing_id   = ($stuffing_id ? "AND S.id IN ($stuffing_id) " : '');

		$sql = "SELECT J.id AS job_id, J.id2_format, CJ.id AS child_job_id, EI.id AS invoice_id, S.id, J.shipper_id, CJ.shipper_site_id, 
			SHPR.name AS shipper_name, CO.name AS consignee_name, GROUP_CONCAT(DISTINCT EI.invoice_no SEPARATOR ', ') AS invoice_no, 
			GROUP_CONCAT(DISTINCT DATE_FORMAT(EI.invoice_date, '%d-%m-%Y') SEPARATOR ', ') AS invoice_date,
			S.location AS stuffing_location, PRD.name AS product_name, S.units, U.code AS unit_code,
			S.vehicle_no, S.container_no, CT.size, S.seal_no, CONCAT(CT.size, ' ', CT.code) AS container_type, 
			S.gross_weight, S.nett_weight, S.flexi_tank_no,
			IP.name AS loading_port, GROUP_CONCAT(DISTINCT COALESCE(TSP.name, 'Direct') SEPARATOR ', ') AS transhipment, J.fpod, L.code AS line_code, 
			DATE_FORMAT(S.pickup_date, '%d-%m-%Y') AS pickup_date, DATE_FORMAT(S.stuffing_date, '%d-%m-%Y') AS stuffing_date,
			CONCAT(V.prefix, ' ', V.name, ' ', V.voyage_no) AS vessel_name, DATE_FORMAT(V.eta_date, '%d-%m-%Y') AS eta_date, DATE_FORMAT(V.etd_date, '%d-%m-%Y') AS etd_date, J.shipment_details, S.email_sent_shipment
		FROM deliveries_stuffings S INNER JOIN container_types CT ON S.container_type_id = CT.id
			INNER JOIN jobs J ON S.job_id = J.id
			INNER JOIN child_jobs CJ ON J.id = CJ.job_id
			LEFT OUTER JOIN stuffing_invoices SI ON S.id = SI.stuffing_id
			LEFT OUTER JOIN job_invoices EI ON SI.job_invoice_id = EI.id
			LEFT OUTER JOIN consignees CO ON J.consignee_id = CO.id
			LEFT OUTER JOIN parties SHPR ON J.shipper_id = SHPR.id
			LEFT OUTER JOIN party_sites SHPRS ON (CJ.stuffing_type = 'Factory' AND CJ.shipper_site_id = SHPRS.id)
			LEFT OUTER JOIN godowns G ON (CJ.stuffing_type = 'Godown' AND CJ.godown_id = G.id)
			LEFT OUTER JOIN indian_ports IP ON J.loading_port_id = IP.id
			LEFT OUTER JOIN ports POD ON J.discharge_port_id = POD.id
			LEFT OUTER JOIN transhipments TS ON J.id = TS.job_id
			LEFT OUTER JOIN ports TSP ON TS.port_id = TSP.id
			LEFT OUTER JOIN products PRD ON J.product_id = PRD.id
			LEFT OUTER JOIN units U ON J.unit_id = U.id
			LEFT OUTER JOIN vessels V ON J.vessel_id = V.id
			LEFT OUTER JOIN agents L ON J.line_id = L.id
		WHERE (S.stuffing_date >= ? AND S.stuffing_date <= ?) " . 
			($sql_stuffing_id ? $sql_stuffing_id : $sql_shipper_id . $sql_shipper_site_id);
		$where = ' AND (';
		if (is_array($parsed_search)) {
			foreach($parsed_search as $key => $value)
				if (isset($this->_fields[$key]))
					$where .= $this->_fields[$key] . " LIKE '%$value%' AND ";
			if (strlen($where) > 6)
				$sql .= substr($where, 0, strlen($where) - 5) . ') ';
		}
		$sql .= "
		GROUP BY S.id
		ORDER BY J.id, EI.id";
		$query  = $this->db->query($sql, array(convDate($from_date), convDate($to_date)));
		$rows   = $query->result_array();
		$result = array();
		foreach ($rows as $row) {
			$result[$row['id']] = $row;
		}
		return $result;
	}

	function preview($pdf = 0, $email = 0) {
		//$data['template']   = $this->kaabar->getField('document_templates', 'Shipment Details cum Stuffing Report', 'name', 'template');
		
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
					$default_company = $this->session->userdata('default_company');
					$data['company'] = $this->kaabar->getRow('companies', $default_company['id']);
					$stuffing_id     = $this->input->post('stuffing_id');
					$from_date       = $this->session->userdata($this->_class.'_from_date');
					$to_date         = $this->session->userdata($this->_class.'_to_date');
					$search          = $this->session->userdata($this->_class.'_search');
					$shipper_id      = $this->session->userdata($this->_class.'_shipper_id');
					$shipper_site_id = $this->session->userdata($this->_class.'_shipper_site_id');
					$parsed_search   = $this->kaabar->parseSearch($search);
					
					$data['rows']         = $this->_getShipment($from_date, $to_date, $search, $parsed_search, $shipper_id, $shipper_site_id, $stuffing_id);
					$data['date']         = date('d-m-Y');
					$data['page_title']   = humanize($this->_class . ' Details cum Stuffing Report');
					$data['shipper']      = $this->kaabar->getRow('parties', $shipper_id);
					$data['shipper_site'] = $this->kaabar->getRow('party_sites', $shipper_site_id);

					$file = FCPATH.'tmp/'.$this->_class . '_program_'.$data['date'].'.pdf';
					$html = $this->load->view($this->_clspath.$this->_class.'_preview', $data, true);
					$pdf = new Pdf(array(
						'no-outline',
						'binary'      => FCPATH.'wkhtmltopdf',
						'orientation' => 'Landscape',
					));
					$pdf->addPage($html);
					$pdf->saveAs($file);

					$this->db->query("UPDATE deliveries_stuffings SET email_sent_shipment='Yes' WHERE id IN (" . implode(',', array_keys($data['rows'])) . ")");
				}
				else 
					$file = $this->excel($email);
				
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
				
				//echo $this->email->print_debugger(); exit;
				
				redirect($this->agent->referrer());
				return;
			}
		}

		$stuffing_id          = $this->input->post('stuffing_id');
		$default_company      = $this->session->userdata('default_company');
		$data['company']      = $this->kaabar->getRow('companies', $default_company['id']);
		$from_date            = $this->session->userdata($this->_class.'_from_date');
		$to_date              = $this->session->userdata($this->_class.'_to_date');
		$search               = $this->session->userdata($this->_class.'_search');
		$shipper_id           = $this->session->userdata($this->_class.'_shipper_id');
		$shipper_site_id      = $this->session->userdata($this->_class.'_shipper_site_id');
		$parsed_search        = $this->kaabar->parseSearch($search);
		$data['rows']         = $this->_getShipment($from_date, $to_date, $search, $parsed_search, $shipper_id, $shipper_site_id, $stuffing_id);
		$data['date']         = date('d-m-Y');
		$data['page_title']   = humanize($this->_class . ' Details cum Stuffing Report');
		$data['shipper']      = $this->kaabar->getRow('parties', $shipper_id);
		$data['shipper_site'] = $this->kaabar->getRow('party_sites', $shipper_site_id);

		if ($pdf) {
			$filename = $this->_class . '_program_' . $from_date . '_' . $to_date;
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

		$stuffing_id     = $this->input->post('stuffing_id');
		$default_company = $this->session->userdata('default_company');
		$data['company'] = $this->kaabar->getRow('companies', $default_company['id']);
		$from_date       = $this->session->userdata($this->_class.'_from_date');
		$to_date         = $this->session->userdata($this->_class.'_to_date');
		$search          = $this->session->userdata($this->_class.'_search');
		$shipper_id        = $this->session->userdata($this->_class.'_shipper_id');
		$shipper_site_id   = $this->session->userdata($this->_class.'_shipper_site_id');
		$parsed_search   = $this->kaabar->parseSearch($search);
		$rows            = $this->_getShipment($from_date, $to_date, $search, $parsed_search, $shipper_id, $shipper_site_id, $stuffing_id);

		// Header
		$header = reset($rows);
		$col = 'A';
		foreach ($header as $h => $v) {
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
		$sheet->getStyle('A1:'.$col.$i)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
		$sheet->getStyle('A1:'.$col.$i)->applyFromArray($styleSheet);
		$sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
		$sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

		if ($email) {
			$this->db->query("UPDATE deliveries_stuffings SET email_sent_shipment='Yes' WHERE id IN (" . implode(',', array_keys($rows)) . ")");
			$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
			$writer->save(FCPATH.'tmp/'.$filename);
			return FCPATH.'tmp/'.$filename;
		}
		else {
			// redirect output to client browser
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment;filename="' . $filename.'"');
			header('Cache-Control: max-age=0');
			$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
			$writer->save('php://output');
		}
	}
}
