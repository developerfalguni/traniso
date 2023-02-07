<?php

class Gatein extends MY_Controller {
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

			
	
			$data['page_title'] = "Gate In";
			$data['page']       = $this->_clspath.'index';
			$data['job_page']   = $this->_clspath.$this->_class;
			$data['hide_title'] = true;
			$data['docs_url']   = $this->_docs;
			$this->load->view('index', $data);
		}
		else {
			checkDuplicateFormSubmit($this->_clspath.$this->_class."/edit/$job_id");

			$gate_ins = $this->input->post("gate_in");
			if ($gate_ins) {
				$child_job_ids = $this->input->post("child_job_id");
				$vessel_ids    = $this->input->post("vessel_id");
				foreach ($gate_ins as $index => $gate_in) {
					if (strlen(trim($gate_in)) > 10) {
						$data = array(
							'vessel_id' => $vessel_ids[$index],
							'gate_in'   => convDate($gate_in) . ':00',
						);
						$this->kaabar->save($this->_table, $data, ['id' => $index]);

						// if ($vessel_ids[$index] > 0)
						// 	$this->kaabar->save('child_jobs', array('vessel_id' => $vessel_ids[$index]), array('id' => $child_job_ids[$index]));
					}
				}
			}

			setSessionAlert('Changes saved successfully', 'success');

			redirect($this->_clspath.$this->_class."/edit/$job_id");
		}
	}

	function _getStuffing($job_id) {
		$sql   = "SELECT S.id, S.job_id, EI.child_job_id, P.name AS party_name,
			GROUP_CONCAT(EI.invoice_no SEPARATOR ', ') AS invoice_no, S.vehicle_no,
			S.container_no, CONCAT(CT.size, ' ', CT.code) AS container_type, S.seal_no, J.fpod,
			DATE_FORMAT(S.gate_in, '%d-%m-%Y %H:%i') AS gate_in, 
			S.vessel_id, CONCAT(V.prefix, ' ', V.name, ' ', V.voyage_no) AS vessel_name, T.code AS terminal_code
		FROM deliveries_stuffings S INNER JOIN container_types CT ON S.container_type_id = CT.id
			INNER JOIN jobs J ON S.job_id = J.id
			INNER JOIN parties P ON J.party_id = P.id
			LEFT OUTER JOIN stuffing_invoices SI ON S.id = SI.stuffing_id
			LEFT OUTER JOIN job_invoices EI ON SI.job_invoice_id = EI.id
			LEFT OUTER JOIN vessels V ON J.vessel_id = V.id
			LEFT OUTER JOIN terminals T ON V.terminal_id = T.id
		WHERE S.job_id = ?
		GROUP BY S.id";
		$query = $this->db->query($sql, array($job_id));
		return $query->result_array();
	}



	function pending() {
		$starting_row = intval($starting_row);if ($this->input->post('vessel_id') == false) {
			setSessionError(validation_errors());
			
			$data['rows']     = $this->_getPending();

			$data['javascript'] = array('jquery.filtertable.js', 'jquery-ui-timepicker-addon.js');
	
			$data['page_title'] = "Gate In";
			$data['page']       = $this->_clspath.$this->_class.'_pending';
			$data['hide_title'] = true;
			$data['docs_url']   = $this->_docs;
			$this->load->view('index', $data);
		}
		else {
			checkDuplicateFormSubmit($this->_clspath.$this->_class."/pending");

			$vessel_ids = $this->input->post("vessel_id");
			if ($vessel_ids) {
				$gate_in_dates = $this->input->post("gate_in_date");
				$gate_in_times = $this->input->post("gate_in_time");
				foreach ($vessel_ids as $index => $vessel_id) {
					if (intval($vessel_id) > 0) {
						$data = array('vessel_id' => $vessel_id);
						if ($gate_in_dates[$index] != '00-00-0000')
							$data['gate_in'] = convDate($gate_in_dates[$index]) . ' ' . $gate_in_times[$index] . ':00';
						$this->kaabar->save('deliveries_stuffings', $data, ['id' => $index]);
					}
				}
			}

			setSessionAlert('Changes saved successfully', 'success');

			redirect($this->_clspath.$this->_class."/pending");
		}
	}

	function _getPending() {
		$sql   = "SELECT S.id, S.job_id, EI.child_job_id, J.id2_format, P.name AS party_name, IP.name AS custom_port, 
			GROUP_CONCAT(EI.invoice_no SEPARATOR ', ') AS invoice_no, 
			S.container_no, CONCAT(CT.size, ' ', CT.code) AS container_type, S.seal_no, J.fpod,
			DATE_FORMAT(S.gate_in, '%d-%m-%Y %H:%i') AS gate_in, S.vessel_id, 
			CONCAT(V.prefix, ' ', V.name, ' ', V.voyage_no) AS vessel_name, S.pickup_location
		FROM deliveries_stuffings S INNER JOIN container_types CT ON S.container_type_id = CT.id
			INNER JOIN jobs J ON S.job_id = J.id
			INNER JOIN indian_ports IP ON J.custom_port_id = IP.id
			INNER JOIN parties P ON J.party_id = P.id
			INNER JOIN stuffing_invoices SI ON S.id = SI.stuffing_id
			INNER JOIN job_invoices EI ON SI.job_invoice_id = EI.id
			LEFT OUTER JOIN vessels V ON S.vessel_id = V.id
		WHERE DATE_FORMAT(S.gate_in, '%Y-%m-%d') = ? OR S.vessel_id = 0 OR S.gate_in = '0000-00-00 00:00:00'
		GROUP BY S.id
		ORDER BY S.gate_in DESC, S.date";
		$query = $this->db->query($sql, array(date('Y-m-d')));
		return $query->result_array();
	}
}
