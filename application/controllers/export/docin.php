<?php

class Docin extends MY_Controller {
	function __construct() {
		parent::__construct();
		
		$this->_table = 'child_jobs';
		$this->load->model('export');
	}
	
	function index($job_id) {
		$this->edit($job_id);
	}

	function edit($job_id) {
		$data['job_id'] = array('id' => $job_id);
		$data['child_job_id'] = array('id' => 0);
		
		if ($this->input->post('id') == false) {
			setSessionError(validation_errors());
			
			$data['jobs']     = $this->export->getJobsInfo($job_id, true, site_url($this->_clspath));
			$data['rows']     = $this->_getStuffing($job_id);
			$data['invoices'] = $this->export->getInvoiceNos($job_id);

			
	
			$data['page_title'] = "Document Handover";
			$data['page']       = $this->_clspath.'index';
			$data['job_page']   = $this->_clspath.$this->_class;
			$data['hide_title'] = true;
			$data['docs_url']   = $this->_docs;
			$this->load->view('index', $data);
		}
		else {
			checkDuplicateFormSubmit($this->_clspath.$this->_class."/edit/$job_id");

			$doc_handovers = $this->input->post("doc_handover");
			if ($doc_handovers) {
				$doc_handover_times = $this->input->post("doc_handover_time");
				$child_job_ids = $this->input->post("child_job_id");
				$vessel_ids    = $this->input->post("vessel_id");
				foreach ($doc_handovers as $index => $doc_handover) {
					if (strlen(trim($doc_handover)) > 10) {
						$data = array(
							'doc_handover' => convDate($doc_handover) . ':00',
						);
						$this->kaabar->save($this->_table, $data, ['id' => $index]);

						if ($vessel_ids[$index] > 0)
							$this->kaabar->save('child_jobs', array('vessel_id' => $vessel_ids[$index]), array('id' => $child_job_ids[$index]));
					}
				}
			}

			setSessionAlert('Changes saved successfully', 'success');

			redirect($this->_clspath.$this->_class."/edit/$job_id");
		}
	}

	function _getStuffing($job_id) {
		$sql   = "SELECT CJ.id, J.id2_format, P.name AS party_name, IP.name AS pol, 
			GROUP_CONCAT(EI.invoice_no SEPARATOR ', ') AS invoice_no, 
			CJ.sb_no, J.fpod, GROUP_CONCAT(DISTINCT CONCAT(C.containers, C.container_type) SEPARATOR ',') AS containers,
			DATE_FORMAT(CJ.doc_handover, '%d-%m-%Y %H:%i') AS doc_handover, CJ.vessel_id, 
			IF(COALESCE(CJ.vessel_id, 0) > 0, CONCAT(CV.prefix, ' ', CV.name, ' ', CV.voyage_no), 
			CONCAT(JV.prefix, ' ', JV.name, ' ', JV.voyage_no)) AS vessel_name
		FROM child_jobs CJ INNER JOIN jobs J ON CJ.job_id = J.id
			INNER JOIN indian_ports IP ON J.custom_port_id = IP.id
			INNER JOIN parties P ON J.party_id = P.id
			INNER JOIN job_invoices EI ON CJ.id = EI.child_job_id
			LEFT OUTER JOIN (
				SELECT EI.child_job_id, COUNT(DISTINCT S.id) AS containers, CONCAT('x', CT.size, CT.code) AS container_type
				FROM deliveries_stuffings S INNER JOIN stuffing_invoices SI ON S.id = SI.stuffing_id
					INNER JOIN job_invoices EI ON SI.job_invoice_id = EI.id
					INNER JOIN child_jobs CJ ON EI.child_job_id = CJ.id
					INNER JOIN container_types CT ON S.container_type_id = CT.id
				WHERE CJ.job_id = ?
				GROUP BY CJ.id, CT.id
			) C ON CJ.id = EI.child_job_id
			LEFT OUTER JOIN vessels JV ON J.vessel_id = JV.id
			LEFT OUTER JOIN vessels CV ON CJ.vessel_id = CV.id
		WHERE CJ.job_id = ?
		GROUP BY CJ.id";
		$query = $this->db->query($sql, array($job_id, $job_id));
		return $query->result_array();
	}



	function pending() {
		$starting_row = intval($starting_row);if ($this->input->post('doc_handover_date') == false) {
			setSessionError(validation_errors());
			
			$data['rows']     = $this->_getPending();

			$data['javascript'] = array('jquery.filtertable.js', 'jquery-ui-timepicker-addon.js');
	
			$data['page_title'] = "Document Handover";
			$data['page']   = $this->_clspath.$this->_class.'_pending';
			$data['hide_title'] = true;
			$data['docs_url']   = $this->_docs;
			$this->load->view('index', $data);
		}
		else {
			checkDuplicateFormSubmit($this->_clspath.$this->_class."/pending");

			$doc_handover_dates = $this->input->post("doc_handover_date");
			if ($doc_handover_dates) {
				$doc_handover_times = $this->input->post("doc_handover_time");
				foreach ($doc_handover_dates as $index => $doc_handover_date) {
					if (strlen(trim($doc_handover_date)) == 10) {
						$data = array(
							'doc_handover' => convDate($doc_handover_date) . ' ' . $doc_handover_times[$index] . ':00',
						);
						$this->kaabar->save($this->_table, $data, ['id' => $index]);
					}
				}
			}

			setSessionAlert('Changes saved successfully', 'success');

			redirect($this->_clspath.$this->_class."/pending");
		}
	}

	function _getPending() {
		$sql   = "SELECT CJ.id, J.id2_format, P.name AS party_name, IP.name AS custom_port, GROUP_CONCAT(EI.invoice_no SEPARATOR ', ') AS invoice_no, 
			CJ.sb_no, J.fpod, DATE_FORMAT(CJ.doc_handover, '%d-%m-%Y %H:%i') AS doc_handover, CJ.vessel_id, DS.pickup_location,
			IF(COALESCE(CJ.vessel_id, 0) > 0, CONCAT(CV.prefix, ' ', CV.name, ' ', CV.voyage_no), CONCAT(JV.prefix, ' ', JV.name, ' ', JV.voyage_no)) AS vessel_name
		FROM child_jobs CJ INNER JOIN jobs J ON CJ.job_id = J.id
			INNER JOIN deliveries_stuffings DS ON J.id = DS.job_id
			INNER JOIN indian_ports IP ON J.custom_port_id = IP.id
			INNER JOIN parties P ON J.party_id = P.id
			INNER JOIN job_invoices EI ON CJ.id = EI.child_job_id
			LEFT OUTER JOIN vessels JV ON J.vessel_id = JV.id
			LEFT OUTER JOIN vessels CV ON CJ.vessel_id = CV.id
		WHERE J.date >= '2014-03-01' AND (DATE_FORMAT(CJ.doc_handover, '%Y-%m-%d') = ? OR CJ.doc_handover = '0000-00-00 00:00:00')
		GROUP BY CJ.id
		ORDER BY CJ.doc_handover DESC, CJ.sb_date";
		$query = $this->db->query($sql, array(date('Y-m-d')));
		return $query->result_array();
	}
}
