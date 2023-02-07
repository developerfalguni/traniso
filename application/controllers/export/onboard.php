<?php

class Onboard extends MY_Controller {
	function __construct() {
		parent::__construct();
		
		$this->_table = 'deliveries_stuffings';
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


	
			$data['page_title'] = "Onboard Details";
			$data['page']       = $this->_clspath.'index';
			$data['job_page']   = $this->_clspath.$this->_class;
			$data['hide_title'] = true;
			$data['docs_url']   = $this->_docs;
			$this->load->view('index', $data);
		}
		else {
			checkDuplicateFormSubmit($this->_clspath.$this->_class."/edit/$job_id");

			$gate_outs = $this->input->post("gate_out_date");
			if ($gate_outs) {
				$gate_out_times = $this->input->post("gate_out_time");
				foreach ($gate_outs as $index => $gate_out) {
					if (strlen(trim($gate_out)) == 10) {
						$data = array(
							'gate_out' => convDate($gate_out) . ' ' . $gate_out_times[$index] . ':00',
						);
						$this->kaabar->save($this->_table, $data, ['id' => $index]);
					}
				}
			}

			setSessionAlert('Changes saved successfully', 'success');

			redirect($this->_clspath.$this->_class."/edit/$job_id");
		}
	}

	function _getStuffing($job_id) {
		$sql   = "SELECT S.id, J.id2_format, P.name AS party_name, IP.name AS pol, 
			J.fpod, S.container_no, CONCAT(CT.size, CT.code) AS size,
			DATE_FORMAT(S.gate_out, '%d-%m-%Y %H:%i') AS gate_out
		FROM deliveries_stuffings S 
			INNER JOIN jobs J ON S.job_id = J.id
			INNER JOIN indian_ports IP ON J.custom_port_id = IP.id
			INNER JOIN parties P ON J.party_id = P.id
			INNER JOIN container_types CT ON S.container_type_id = CT.id
		WHERE S.job_id = ?";
		$query = $this->db->query($sql, array($job_id));
		return $query->result_array();
	}



	function pending() {
		$starting_row = intval($starting_row);if ($this->input->post('gate_out') == false) {
			setSessionError(validation_errors());
			
			$data['rows']     = $this->_getPending();

			$data['javascript'] = array('jquery.filtertable.js', 'jquery-ui-timepicker-addon.js');
	
			$data['page_title'] = "Onboard Details";
			$data['page']   = $this->_clspath.$this->_class.'_pending';
			$data['hide_title'] = true;
			$data['docs_url']   = $this->_docs;
			$this->load->view('index', $data);
		}
		else {
			checkDuplicateFormSubmit($this->_clspath.$this->_class."/pending");

			$gate_outs = $this->input->post("gate_out");
			if ($gate_outs) {
				$gate_out_times = $this->input->post("gate_out_time");
				foreach ($gate_outs as $index => $gate_out) {
					if (strlen(trim($gate_out)) == 10) {
						$data = array(
							'gate_out' => convDate($gate_out) . ' ' . $gate_out_times[$index] . ':00',
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
		$sql   = "SELECT CJ.id, J.id2_format, P.name AS party_name, IP.name AS pol, 
			GROUP_CONCAT(EI.invoice_no SEPARATOR ', ') AS invoice_no, 
			CJ.sb_no, J.fpod, DATE_FORMAT(S.gate_out, '%d-%m-%Y %H:%i') AS gate_out, 
			CJ.vessel_id, 
			IF(COALESCE(CJ.vessel_id, 0) > 0, CONCAT(CV.prefix, ' ', CV.name, ' ', CV.voyage_no), CONCAT(JV.prefix, ' ', JV.name, ' ', JV.voyage_no)) AS vessel_name
		FROM child_jobs CJ INNER JOIN jobs J ON CJ.job_id = J.id
			INNER JOIN indian_ports IP ON J.custom_port_id = IP.id
			INNER JOIN parties P ON J.party_id = P.id
			INNER JOIN job_invoices EI ON CJ.id = EI.child_job_id
			LEFT OUTER JOIN vessels JV ON J.vessel_id = JV.id
			LEFT OUTER JOIN vessels CV ON CJ.vessel_id = CV.id
		WHERE J.date >= '2014-03-01' AND (DATE_FORMAT(S.gate_out, '%Y-%m-%d') = ? OR S.gate_out = '0000-00-00 00:00:00')
		GROUP BY CJ.id
		ORDER BY S.gate_out DESC, CJ.sb_date";
		$query = $this->db->query($sql, array(date('Y-m-d')));
		return $query->result_array();
	}
}
