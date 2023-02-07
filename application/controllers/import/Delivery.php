<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use mikehaertl\wkhtmlto\Pdf;

class Delivery extends MY_Controller {
	var $_fields;
	var $_parsed_search;

	function __construct() {
		parent::__construct();
	
		$this->_table  = 'deliveries_stuffings';
		
		$this->_fields = array(
			'party'           => 'P.name',
			'be_no'           => 'J.be_no',
			'bl_no'           => 'J.bl_no',
			'vehicle_no'      => 'D.vehicle_no',
			'unload_by'       => 'D.unloading_by',
			'unload_location' => 'D.unloading_location',
			'destuffing'      => 'D.destuffing_agent',
			'cfs_date'        => 'DATE_FORMAT(D.cfs_in_date, "%d-%m-%Y")',
			'gatepass_date'   => 'DATE_FORMAT(D.gatepass_date, "%d-%m-%Y")',
			'return_date'     => 'DATE_FORMAT(D.return_date, "%d-%m-%Y")',
			'icegate'         => 'IC.status',
		);

		$this->load->model('import');
	}
	
	function index($job_id = 0) {
		$search = $this->session->userdata($this->_class.'_search');

		if($this->input->post('search_form')) {
			$search = addslashes($this->input->post('search'));
			$this->session->set_userdata($this->_class.'_search', $search);
		}
		
		if ($search == null) {
			$search = '';
			$this->session->set_userdata($this->_class.'_search', $search);
		}

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
		$default_company = $this->session->userdata("default_company");

		$data['rows'] = $this->_getPending($job_id, $search, $data['parsed_search'], $this->_fields);

		$data['javascript'] = array('backbonejs/underscore-min.js', 'backbonejs/backbone-min.js');

		$data['job_id']   = $job_id;
		if ($job_id > 0) {
			$data['jobs']     = $this->import->getJobsInfo($job_id, true, site_url($this->_clspath));
			$data['page']     = $this->_clspath.'index';
			$data['job_page'] = $this->_clspath.$this->_class.'_edit';
		}
		else 
			$data['page'] = $this->_clspath.$this->_class.'_edit';

		$data['page_title'] = humanize($this->_class);
		$data['docs_url']   = $this->_docs;
		$this->load->view('index', $data);
	}

	function _getPending($job_id, $search, $parsed_search, $fields) {
		$this->db->query("INSERT INTO deliveries_stuffings (job_id, container_id, container_no) 
			SELECT job_id, id, number FROM containers WHERE job_id = ? AND id NOT IN (
				SELECT container_id FROM deliveries_stuffings WHERE job_id = ?
			)", array($job_id, $job_id));

		if ($job_id > 0) {
			$sql = "SELECT D.id, C.job_id, C.id AS container_id, C.number, CT.size, J.party_id, P.name AS party_name, 
				J.be_no, J.bl_no, D.vehicle_no, DATE_FORMAT(D.date, '%d-%m-%Y') AS bulk_delivery_date, DATE_FORMAT(D.unloading_date, '%d-%m-%Y') AS date, 
				D.dispatch_weight, D.dispatch_type, D.unloading_location, D.unloading_by, D.destuffing_agent, D.nett_weight,
				DATE_FORMAT(D.unloading_date, '%d-%m-%Y') AS unloading_date, 
				D.gatepass_no, DATE_FORMAT(D.gatepass_date, '%d-%m-%Y') AS gatepass_date, 
				D.lr_no, D.fetched_from, D.location, DATE_FORMAT(D.cfs_in_date, '%d-%m-%Y') AS cfs_in_date_only, 
				DATE_FORMAT(D.cfs_in_date, '%d-%m-%Y %H:%i') AS cfs_in_date, 
				DATE_FORMAT(D.return_date, '%d-%m-%Y') AS return_date, IC.status AS icegate_status
			FROM containers C
				INNER JOIN container_types CT ON C.container_type_id = CT.id
				INNER JOIN jobs J ON C.job_id = J.id
				INNER JOIN parties P ON J.party_id = P.id
				LEFT OUTER JOIN deliveries_stuffings D ON C.id = D.container_id
				LEFT OUTER JOIN icegate_be IC ON C.job_id = IC.job_id
			WHERE J.id = $job_id ";
			if (is_array($parsed_search)) {
				$where = ' AND ';
				foreach($parsed_search as $key => $value)
					if (isset($fields[$key])) {
						if (strtolower($value) == 'empty')
							$where .= "LENGTH(TRIM(" . $fields[$key] . ")) = 0 AND ";
						else
							$where .= $fields[$key] . " LIKE '%$value%' AND ";
					}
				if (strlen($where) > 6)
					$sql .= substr($where, 0, strlen($where) - 5);
			}
			$sql .= "
			UNION
			SELECT D.id, J.id AS job_id, 0 AS container_id, D.container_no AS number, '' AS size, J.party_id, P.name AS party_name, 
				J.be_no, J.bl_no, D.vehicle_no, DATE_FORMAT(D.date, '%d-%m-%Y') AS bulk_delivery_date, DATE_FORMAT(D.unloading_date, '%d-%m-%Y') AS date, 
				D.dispatch_weight, D.dispatch_type, D.unloading_location, D.unloading_by, D.destuffing_agent, D.nett_weight,
				DATE_FORMAT(D.unloading_date, '%d-%m-%Y') AS unloading_date, 
				D.gatepass_no, DATE_FORMAT(D.gatepass_date, '%d-%m-%Y') AS gatepass_date, 
				D.lr_no, '' AS fetched_from, '' AS discharge_location, DATE_FORMAT(D.cfs_in_date, '%d-%m-%Y') AS cfs_in_date_only, 
				DATE_FORMAT(D.cfs_in_date, '%d-%m-%Y %H:%i') AS cfs_in_date, 
				DATE_FORMAT(D.return_date, '%d-%m-%Y') AS return_date, IC.status AS icegate_status
			FROM deliveries_stuffings D
				INNER JOIN jobs J ON D.job_id = J.id
				INNER JOIN parties P ON J.party_id = P.id
				LEFT OUTER JOIN icegate_be IC ON D.job_id = IC.job_id
			WHERE J.id = $job_id AND D.container_id = 0
			ORDER BY container_id, gatepass_date DESC";
		}
		else {
			$sql = "SELECT D.id, C.job_id, C.id AS container_id, C.number, CT.size, J.party_id, P.name AS party_name, 
				J.be_no, J.bl_no, D.vehicle_no, DATE_FORMAT(D.date, '%d-%m-%Y') AS bulk_delivery_date, DATE_FORMAT(D.unloading_date, '%d-%m-%Y') AS date, 
				D.dispatch_weight, D.dispatch_type, D.unloading_location, D.unloading_by, D.destuffing_agent, D.nett_weight,
				DATE_FORMAT(D.unloading_date, '%d-%m-%Y') AS unloading_date, 
				D.gatepass_no, DATE_FORMAT(D.gatepass_date, '%d-%m-%Y') AS gatepass_date, 
				D.lr_no, D.fetched_from, D.location, DATE_FORMAT(D.cfs_in_date, '%d-%m-%Y') AS cfs_in_date_only, 
				DATE_FORMAT(D.cfs_in_date, '%d-%m-%Y %H:%i') AS cfs_in_date, 
				DATE_FORMAT(D.return_date, '%d-%m-%Y') AS return_date, IC.status AS icegate_status
			FROM containers C
				INNER JOIN container_types CT ON C.container_type_id = CT.id
				INNER JOIN jobs J ON C.job_id = J.id
				INNER JOIN parties P ON J.party_id = P.id
				INNER JOIN import_details ID ON J.id = ID.job_id
				LEFT OUTER JOIN deliveries_stuffings D ON C.id = D.container_id
				LEFT OUTER JOIN icegate_be IC ON C.job_id = IC.job_id
			WHERE J.type = 'Import' AND J.status != 'Pending' AND J.status != 'Completed' AND (
				D.cfs_in_date   = '0000-00-00 00:00:00' OR 
				D.gatepass_date = '0000-00-00' OR 
				D.return_date   = '0000-00-00')";
			$limit = 'LIMIT 0, 1000';
			if (is_array($parsed_search)) {
				$limit = '';
				$where = ' AND ';
				foreach($parsed_search as $key => $value)
					if (isset($fields[$key])) {
						if (strtolower($value) == 'empty')
							$where .= "LENGTH(TRIM(" . $fields[$key] . ")) = 0 AND ";
						else
							$where .= $fields[$key] . " LIKE '%$value%' AND ";
					}
				if (strlen($where) > 6)
					$sql .= substr($where, 0, strlen($where) - 5);
			}
			else if (strlen(trim($search)) > 0) {
				$sql .= " AND (J.bl_no LIKE '%$search%' OR C.number LIKE '%$search%')";
			}
			$sql .= "
			ORDER BY J.id, C.id, D.gatepass_date DESC
			$limit";
		}
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function preview($job_id, $pdf = 0, $email = 0) {
		$search                 = $this->session->userdata($this->_class.'_search');
		$this->_parsed_search   = $this->kaabar->parseSearch($search);
		$data['rows']           = $this->_getPending($job_id, $search, $this->_parsed_search, $this->_fields);
		$data['job']            = $this->kaabar->getRow('jobs', $job_id);
		$data['party']          = $this->kaabar->getRow('parties', $data['job']['party_id']);
		$default_company        = $this->session->userdata('default_company');
		$data['company']        = $this->kaabar->getRow('companies', $default_company['id']);
		$data['city']           = $this->kaabar->getRow('cities', $data['company']['city_id']);
		$data['state']          = $this->kaabar->getRow('states', $data['city']['state_id']);
		$data['containers']     = $this->kaabar->getRows('containers', $job_id, 'job_id');
		$hss_parties            = $this->import->getHighSeas($job_id);
		$data['hss_buyer']      = array_pop($hss_parties);
		$data['package_type']   = $this->kaabar->getField('package_types', $data['job']['package_type_id']);
		$data['discharge_port'] = $this->kaabar->getField('indian_ports', $data['job']['indian_port_id']) . ' - INDIA';
		$data['shipment_port']  = $this->kaabar->getField('ports', $data['job']['shipment_port_id']);
		$data['vessel']         = $this->kaabar->getRow('vessels', $data['job']['vessel_id']);
		$data['line_name']      = $this->kaabar->getField('agents', $data['job']['line_id']);
		$data['cfs_name']       = $this->kaabar->getField('agents', $data['job']['cfs_id']);
		
		$data['page_title'] = humanize('Consignment Report');
		
		if ($pdf) {
			$filename = underscore($data['page_title']);
			$html = $this->load->view('reports/'.$this->_class.'_preview', $data, true);
			
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
					'protocol'     => 'smtp',
					'smtp_timeout' => 30,
					'smtp_host'    => Settings::get('smtp_host'),
					'smtp_port'    => Settings::get('smtp_port'),
					'smtp_user'    => Settings::get('smtp_user'),
					'smtp_pass'    => Settings::get('smtp_password'),
					'newline'      => "\r\n",
					'crlf'         => "\r\n"
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
				setSessionAlert('Email has been sent successfully...', 'success');
				
				// echo $this->email->print_debugger(); exit;
				
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
			$this->load->view('reports/'.$this->_class.'_preview', $data);
		}
	}

	function email($job_id) {
		$search                 = $this->session->userdata($this->_class.'_search');
		$this->_parsed_search   = $this->kaabar->parseSearch($search);
		$data['rows']           = $this->_getPending($job_id, $search, $this->_parsed_search, $this->_fields);
		$data['job']            = $this->kaabar->getRow('jobs', $job_id);
		$data['party']          = $this->kaabar->getRow('parties', $data['job']['party_id']);
		$default_company        = $this->session->userdata('default_company');
		$data['company']        = $this->kaabar->getRow('companies', $default_company['id']);
		$data['city']           = $this->kaabar->getRow('cities', $data['company']['city_id']);
		$data['state']          = $this->kaabar->getRow('states', $data['city']['state_id']);
		$data['containers']     = $this->kaabar->getRows('containers', $job_id, 'job_id');
		$hss_parties            = $this->import->getHighSeas($job_id);
		$data['hss_buyer']      = array_pop($hss_parties);
		$data['package_type']   = $this->kaabar->getField('package_types', $data['job']['package_type_id']);
		$data['discharge_port'] = $this->kaabar->getField('indian_ports', $data['job']['indian_port_id']) . ' - INDIA';
		$data['shipment_port']  = $this->kaabar->getField('ports', $data['job']['shipment_port_id']);
		$data['vessel']         = $this->kaabar->getRow('vessels', $data['job']['vessel_id']);
		$data['line_name']      = $this->kaabar->getField('agents', $data['job']['line_id']);
		$data['cfs_name']       = $this->kaabar->getField('agents', $data['job']['cfs_id']);
		
		$page = 'reports/'.$this->_class.'_preview';

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


	function previewAll($pdf = 0, $email = 0) {
		$default_company      = $this->session->userdata("default_company");
		$search               = $this->session->userdata($this->_class.'_search');
		$this->_parsed_search = $this->kaabar->parseSearch($search);
		$data['rows']         = $this->_getPending(0, $search, $this->_parsed_search, $this->_fields);
		$data['company']      = $this->kaabar->getRow('companies', $default_company['id']);
		$data['page_title']   = humanize($this->_class);
		$data['page_desc']    = '';
		
		if ($pdf) {
			$filename = underscore($data['page_title']).'_'.date('Y-m-d');
			$html = $this->load->view('reports/'.$this->_class.'_all_preview', $data, true);
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
			$this->load->view('reports/'.$this->_class.'_all_preview', $data);
		}
	}

	function excel($job_id = 0) {
		$search               = $this->session->set_userdata($this->_class.'_search', $search);
		$this->_parsed_search = $this->kaabar->parseSearch($search);
		$rows                 = $this->_getPending($job_id, $search, $this->_parsed_search, $this->_fields);

		$this->_excel($rows, array('id', 'job_id', 'party_id'));
	}

	function ajaxBL() {
		if ($this->input->is_ajax_request()) {
			$search     = strtolower($this->input->post_get('term'));
			$company_id = $this->_company['id'];
			$sql = "SELECT J.id, J.type, J.be_type, J.bl_no AS bl_no, P.name AS party, 
				J.vessel_id, CONCAT(V.prefix, ' ', V.name, ' ', V.voyage_no) AS vessel_voyage
			FROM jobs J
				INNER JOIN parties P ON J.party_id = P.id
				INNER JOIN vessels V ON J.vessel_id = V.id
			WHERE J.type = 'Import' AND (J.bl_no LIKE '%$search%' OR J.be_no LIKE '%$search%')
			ORDER BY bl_no 
			LIMIT 0, 50";
			$rows = $this->db->query($sql)->result_array();
			header('Content-type: application/json; charset=utf-8');
			echo json_encode($rows, JSON_UNESCAPED_UNICODE);
		}
		else
			echo "Access Denied";
	}
}
