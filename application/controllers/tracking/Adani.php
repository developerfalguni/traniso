<?php

ini_set('max_execution_time', '0');
ini_set('memory_limit', '512M');

include(APPPATH.'libraries/ganon.php');

class Adani extends MY_Controller {
	var $_fetch_cookie;
	var $_sms;

	function __construct() {
		parent::__construct();

		$this->_sms = array(
			'import' => array('7698945785', '7698945785', '7698945785', '7698945785', '7698945785', '7698945785', '7698945785', '7698945785'),
			'export' => array('7698945785', '7698945785', '7698945785', '7698945785', '7698945785', '7698945785')
		);
		$this->_fetch_cookie = FCPATH . 'tmp/' . $this->_class . '_cookie';
	}
	
	function index($terminal = '2', $job_id = 0) {
		$this->load->library('Zebra_cURL');

		if ($this->input->post('job_id'))
			$job_id = $this->input->post('job_id');

		if (is_array($job_id)) {
			$sql = "SELECT DS.id, IF(J.type = 'Import', V.eta_date, J.date) AS date, DS.container_id, DS.job_id, J.type, DS.container_no
			FROM deliveries_stuffings DS INNER JOIN jobs J ON DS.job_id = J.id
				INNER JOIN vessels V ON J.vessel_id = V.id
				INNER JOIN terminals T ON V.terminal_id = T.id
			WHERE T.code = 'Adani CT$terminal' AND DS.container_id > 0 AND DS.job_id IN (" . implode(',', $job_id) . ") AND (
				(J.type = 'Import' AND  DS.cfs_in_date = '0000-00-00 00:00:00') OR 
				(J.type = 'Export' AND (DS.gate_in = '0000-00-00 00:00:00' OR DS.gate_out = '0000-00-00 00:00:00'))
			)";
		}
		else if ($job_id > 0) {
			$sql = "SELECT DS.id, IF(J.type = 'Import', V.eta_date, J.date) AS date, DS.container_id, DS.job_id, J.type, DS.container_no
			FROM deliveries_stuffings DS INNER JOIN jobs J ON DS.job_id = J.id
				INNER JOIN vessels V ON J.vessel_id = V.id
				INNER JOIN terminals T ON V.terminal_id = T.id
			WHERE T.code = 'Adani CT$terminal' AND DS.container_id > 0 AND DS.job_id = $job_id AND (
				(J.type = 'Import' AND  DS.cfs_in_date = '0000-00-00 00:00:00') OR 
				(J.type = 'Export' AND (DS.gate_in = '0000-00-00 00:00:00' OR DS.gate_out = '0000-00-00 00:00:00'))
			)";
		}
		else {
			$sql = "SELECT DS.id, IF(J.type = 'Import', V.eta_date, J.date) AS date, DS.container_id, DS.job_id, J.type, DS.container_no
			FROM deliveries_stuffings DS INNER JOIN jobs J ON DS.job_id = J.id
				INNER JOIN vessels V ON J.vessel_id = V.id
				INNER JOIN terminals T ON V.terminal_id = T.id
			WHERE T.code = 'Adani CT$terminal' AND DS.container_id > 0 AND (
				(J.type = 'Import' AND  DS.cfs_in_date = '0000-00-00 00:00:00') OR 
				(J.type = 'Export' AND (DS.gate_in = '0000-00-00 00:00:00' OR DS.gate_out = '0000-00-00 00:00:00'))
			)";
		}
		$query = $this->db->query($sql);
		$rows  = $query->result_array();

		foreach ($rows as $r) {
			$this->_body = '';
			$this->zebra_curl->cookies($this->_fetch_cookie, TRUE);
			$this->zebra_curl->option(array(
				CURLOPT_REFERER => "http://www.adaniports.com/port_operations_ContainerInquiryAtCT" . $terminal . ".aspx"
			));

			$this->zebra_curl->get("http://www.adaniports.com/port_operations_ContainerInquiryAtCT" . $terminal . ".aspx", 
				function($result) {
					if ($result->response[1] == CURLE_OK)
						$this->_body = $result->body;
					else
						die('An error occured: ' . $result->response[0]);
				}
			);
			$html   = str_get_dom($this->_body);
			$inputs = $html('input');
			foreach($inputs as $input) {
				$post[$input->getAttribute('name')] = $input->getAttribute('value');
			}

			$post['txtContainerID'] = $r['container_no'];
			$this->zebra_curl->post("http://www.adaniports.com/port_operations_ContainerInquiryAtCT$terminal.aspx", 
				$post,
				function($result) {
					if ($result->response[1] == CURLE_OK)
						$this->_body = $result->body;
					else
						die('An error occured: ' . $result->response[0]);
				}
			);

			$table = array();
			$found = strpos($this->_body, "No Records found");
			if ($found == false) {
				$html       = str_get_dom($this->_body);
				$html_table = $html('table');
				foreach($html_table as $t) {
					if ($t->getAttribute('id') == ('gvCT'.$terminal)) {
						foreach ($t('tr') as $tr) {
							foreach ($tr('th') as $index => $th)
								$header[$index] = $th->getPlainText();

							foreach ($tr('td') as $index => $td)
								$table[$header[$index]] = trim($td->getPlainText());
						}
					}
				}

				if (count($table) >= 9)
					$data = array(
						'job_id'       => $r['job_id'],
						'container_id' => $r['id'],
						'fetched_from' => 'Adani CT'.$terminal,
						'category'     => trim($table['Category']),
						'status'       => trim($table['Status']),
						'gross_weight' => trim($table['Weight']),
						'location'     => trim($table['Location']),
						'position'     => trim($table['Position']),
						'in_time'      => trim($table['In Time ']),
						'out_time'     => trim($table['Out Time']),
					);
				unset($html);
				unset($table);

				$gate_in  = convDate(str_replace('.', '-', trim($data['in_time'])) . ':00');
				$gate_out = convDate(str_replace('.', '-', trim($data['out_time'])) . ':00');

				$datetime1 = new DateTime($r['date']);
				$datetime2 = new DateTime($gate_in);
				$interval = $datetime1->diff($datetime2);

				if (intval($interval->format('%r%a')) >= 0) {
					if (count($data) > 1 AND ($r['type'] == 'Import' AND $data['category'] == 'I')) {
						$data = array(
							'job_id'       => $r['job_id'],
							'container_id' => $r['container_id'],
							'container_no' => $r['container_no'],
							'fetched_from' => $data['fetched_from'],
							'location'     => $data['location'],
							'cfs_in_date'  => str_replace('.', '-', $data['out_time'])
						);
						$this->kaabar->save('deliveries_stuffings', $data, array('id' => $r['id']));

						// Sending Arrived SMS
						$query = $this->db->query("SELECT P.name AS party_name, J.id2_format, J.bl_no, COUNT(DS.id) AS containers, MAX(DS.cfs_in_date) AS cfs_in_date
						FROM deliveries_stuffings DS INNER JOIN jobs J ON DS.job_id = J.id
							INNER JOIN parties P ON J.party_id = P.id
						WHERE J.id = ? AND J.id NOT IN (
							SELECT DS.job_id FROM deliveries_stuffings DS WHERE DS.job_id = ? AND DS.cfs_in_date = '0000-00-00 00:00:00' GROUP BY DS.job_id
						)
						GROUP BY DS.job_id", array($data['job_id'], $data['job_id']));
						if ($query->num_rows() > 0) {
							$job_row = $query->row();
							foreach ($this->_sms['import'] as $mobile_no) {
								$this->db->insert('sms_queue', array(
									'mobile_no' => $mobile_no,
									'message'   => "$job_row->party_name, Job:$job_row->id2_format, BL:$job_row->bl_no, Cont:$job_row->containers, Arrived in CFS on $job_row->cfs_in_date"
								));
							}
						}
					}
					else if (count($data) > 1 AND ($r['type'] == 'Export' AND $data['category'] == 'E')) {
						$data = array(
							'job_id'        => $r['job_id'],
							'container_id'  => $r['container_id'],
							'container_no'  => $r['container_no'],
							'fetched_from'  => $data['fetched_from'],
							'location'      => $data['location'],
							'gate_in'  => str_replace('.', '-', $data['in_time']),
							'gate_out' => str_replace('.', '-', $data['out_time']),
						);
						$this->kaabar->save('deliveries_stuffings', $data, array('id' => $r['id']));

						// Sending Gated IN SMS
						$query = $this->db->query("SELECT P.name AS party_name, J.id2_format, GROUP_CONCAT(DISTINCT CJ.sb_no) AS sb_no, J.fpod,
							COUNT(DISTINCT DS.id) AS containers, DS.fetched_from, MAX(DS.gate_in) AS gate_in
						FROM deliveries_stuffings DS INNER JOIN jobs J ON DS.job_id = J.id
							INNER JOIN parties P ON J.party_id = P.id
							INNER JOIN child_jobs CJ ON J.id = CJ.job_id
						WHERE J.id = ? AND DS.sms_sent_gate_in = 'No' AND J.id NOT IN (
							SELECT DS.job_id FROM deliveries_stuffings DS WHERE DS.job_id = ? AND DS.gate_in = '0000-00-00 00:00:00' GROUP BY DS.job_id
						)
						GROUP BY J.id", array($data['job_id'], $data['job_id']));
						if ($query->num_rows() > 0) {
							$job_row = $query->row();
							foreach ($this->_sms['export'] as $mobile_no) {
								$this->db->insert('sms_queue', array(
									'mobile_no' => $mobile_no,
									'message'   => "$job_row->party_name, Job:$job_row->id2_format, SB:$job_row->sb_no, FPD:$job_row->fpod, Cont:$job_row->containers, Have Gated In $job_row->fetched_from On $job_row->gate_in"
								));
							}

							$this->db->update('deliveries_stuffings', array('sms_sent_gate_in' => 'Yes'), array('job_id' => $data['job_id']));
						}

						// Sending On Board SMS
						$query = $this->db->query("SELECT P.name AS party_name, J.id2_format, GROUP_CONCAT(DISTINCT CJ.sb_no) AS sb_no, J.fpod,
							COUNT(DISTINCT DS.id) AS containers
						FROM deliveries_stuffings DS INNER JOIN jobs J ON DS.job_id = J.id
							INNER JOIN parties P ON J.party_id = P.id
							INNER JOIN child_jobs CJ ON J.id = CJ.job_id
						WHERE J.id = ? AND DS.sms_sent_gate_out = 'No' AND J.id NOT IN (
							SELECT DS.job_id FROM deliveries_stuffings DS WHERE DS.job_id = ? AND DS.gate_out = '0000-00-00 00:00:00' GROUP BY DS.job_id
						)
						GROUP BY J.id", array($data['job_id'], $data['job_id']));
						if ($query->num_rows() > 0) {
							$job_row = $query->row();
							foreach ($this->_sms['export'] as $mobile_no) {
								$this->db->insert('sms_queue', array(
									'mobile_no' => $mobile_no,
									'message'   => "$job_row->party_name, Job:$job_row->id2_format, SB:$job_row->sb_no, FPD:$job_row->fpod, Cont:$job_row->containers, Boarded on Vessel"
								));
							}
							$this->db->update('deliveries_stuffings', array('sms_sent_gate_out' => 'Yes'), array('job_id' => $data['job_id']));
						}
					}
				}
			}
		}

		if (! $this->_is_cli) 
			echo closeSelfAndRefreshParent();
	}
}
