<?php

ini_set('max_execution_time', '0');
ini_set('memory_limit', '512M');

use GuzzleHttp\Client;
use League\CLImate\CLImate;
use Wangoviridans\Ganon;

class Mict extends MY_Controller {
	var $_cookie;
	var $_client;
	var $_cli;
	var $_sms;

	function __construct() {
		parent::__construct();

		$this->_cookie = new \GuzzleHttp\Cookie\CookieJar;
		$this->_client = new Client([
			'cookies' => true,
			'verify'  => false,
		]);
		$response = $this->_client->get('http://dpworldmundra.com/php/searchContainerNo.php', ['cookies' => $this->_cookie]);
		$this->_cli = new CLImate;
		$this->_sms = array(
			'import' => array('7698945785', '7698945785', '7698945785', '7698945785', '7698945785', '7698945785', '7698945785', '7698945785', '7698945785'),
			'export' => array('7698945785', '7698945785', '7698945785', '7698945785', '7698945785', '7698945785')
		);
	}
	
	function index($job_id = 0) {

		if ($this->input->post('job_id'))
			$job_id = $this->input->post('job_id');

		if (is_array($job_id)) {
			$sql = "SELECT DS.id, IF(J.type = 'Import', V.eta_date, J.date) AS date, DS.container_id, DS.job_id, J.type, DS.container_no
			FROM deliveries_stuffings DS
				INNER JOIN jobs J ON DS.job_id = J.id
				INNER JOIN vessels V ON J.vessel_id = V.id
				INNER JOIN terminals T ON V.terminal_id = T.id
			WHERE T.code = 'MICT' AND DS.container_id > 0 AND DS.job_id IN (" . implode(',', $job_id) . ") AND (
				(J.type = 'Import' AND  DS.cfs_in_date = '0000-00-00 00:00:00') OR 
				(J.type = 'Export' AND (DS.gate_in = '0000-00-00 00:00:00' OR DS.gate_out = '0000-00-00 00:00:00'))
			)";
		}
		else if ($job_id > 0) {
			$sql = "SELECT DS.id, IF(J.type = 'Import', V.eta_date, J.date) AS date, DS.container_id, DS.job_id, J.type, DS.container_no
			FROM deliveries_stuffings DS
				INNER JOIN jobs J ON DS.job_id = J.id
				INNER JOIN vessels V ON J.vessel_id = V.id
				INNER JOIN terminals T ON V.terminal_id = T.id
			WHERE T.code = 'MICT' AND DS.container_id > 0 AND DS.job_id = $job_id AND (
				(J.type = 'Import' AND  DS.cfs_in_date = '0000-00-00 00:00:00') OR 
				(J.type = 'Export' AND (DS.gate_in = '0000-00-00 00:00:00' OR DS.gate_out = '0000-00-00 00:00:00'))
			)";
		}
		else {
			$sql = "SELECT DS.id, IF(J.type = 'Import', V.eta_date, J.date) AS date, DS.container_id, DS.job_id, J.type, DS.container_no
			FROM deliveries_stuffings DS
				INNER JOIN jobs J ON DS.job_id = J.id
				INNER JOIN vessels V ON J.vessel_id = V.id
				INNER JOIN terminals T ON V.terminal_id = T.id
			WHERE T.code = 'MICT' AND DS.container_id > 0 AND (
				(J.type = 'Import' AND  DS.cfs_in_date = '0000-00-00 00:00:00') OR 
				(J.type = 'Export' AND (DS.gate_in = '0000-00-00 00:00:00' OR DS.gate_out = '0000-00-00 00:00:00'))
			)";
		}
		$query = $this->db->query($sql);
		$rows = $query->result_array();

		foreach ($rows as $r) {
			if (ENVIRONMENT == 'development')
				$this->_cli->inline($r['container_no']. ',');

			$response = $this->_client->post('http://dpworldmundra.com/php/searchContainerNoResult.php', [
				'headers' => [
					'Referer' => 'http://dpworldmundra.com/php/searchContainerNo.php'
				],
				'form_params' => [
					'SUBMIT'      => ' Search ',
					'btnClick'    => 'ok',
					'containerNo' => $r['container_no']
				]
			]);
			
			if ($response->getStatusCode() != 200) {
				return;
			}

			$body = $response->getBody()->read(1024*1024*2);

			$table = [];
			$found = strpos($body, "No Match Found.");
			if ($found == false) {
				$html       = str_get_dom($body);
				$html_table = $html('td');
				foreach($html_table as $index => $td) {
					
					if ($index >= 10 && $index <= 21) {
						$header[$index-10] = trim($td->getPlainText());
					}

					if ($index >= 22 && $index <= 33) {
						$table[$header[($index-22)]] = trim($td->getPlainText());
					}
				}

				// $data = [
				// 	'job_id'         => $r['job_id'],
				// 	'container_id'   => $r['id'],
				// 	'fetched_from'   => 'MICT',
				// 	'no'           => trim($table['No.']),
				// 	'container_no' => trim($table['Container No.']),
				// 	'eq_size'      => trim($table['EQ Size']),
				// 	'category'       => trim($table['Category']),
				// 	'status'         => trim($table['Status']),
				// 	'gross_weight'   => trim($table['Gross Weight']),
				// 	'location'       => trim($table['DIS Location']),
				// 	'vessel_name'    => trim($table['Vessel']),
				// 	'voyage_no'      => trim($table['Voyage']),
				// 	'position'       => trim($table['Position']),
				// 	'in_time'        => trim($table['In Time']),
				// 	'out_time'       => trim($table['Out Time']),
				// ];

				if (count($table) >= 12) {

					$gate_in  = (strlen($table['In Time']) == 0 ? '0000-00-00 00:00:00' : date('Y-m-d H:i:s', strtotime($table['In Time'])));
					$gate_out = (strlen($table['Out Time']) == 0 ? '0000-00-00 00:00:00' : date('Y-m-d H:i:s', strtotime($table['Out Time'])));

					$datetime1 = new DateTime($r['date']);
					$datetime2 = new DateTime($gate_in);
					$interval = $datetime1->diff($datetime2);

					if (intval($interval->format('%r%a')) >= 0 AND $r['type'] == 'Import' AND $table['Category'] == 'IMPORT') {
						$data = [
							'job_id'       => $r['job_id'],
							'container_id' => $r['container_id'],
							'container_no' => $r['container_no'],
							'fetched_from' => 'MICT',
							'cfs_in_date'  => (strlen($table['Out Time']) == 0 ? '0000-00-00 00:00:00' : date('Y-m-d H:i', strtotime($table['Out Time']))),
							'location'     => $table['DIS Location'],
						];
						$this->kaabar->save('deliveries_stuffings', $data, ['id' => $r['id']]);

						if (ENVIRONMENT == 'development')
							$this->_cli->lightBlue()->inline($data['location'])
								->white()->inline(',')
								->green()->out($data['cfs_in_date']);

						// Sending Arrived SMS
						$query = $this->db->query("SELECT J.party_id, P.name AS party_name, J.id2_format, J.bl_no, COUNT(DS.id) AS containers, MAX(DS.cfs_in_date) AS cfs_in_date
						FROM deliveries_stuffings DS
							INNER JOIN jobs J ON DS.job_id = J.id
							INNER JOIN parties P ON J.party_id = P.id
						WHERE J.id = ? AND J.id NOT IN (
							SELECT DS.job_id FROM deliveries_stuffings DS WHERE DS.job_id = ? AND DS.cfs_in_date = '0000-00-00 00:00:00' GROUP BY DS.job_id
						)
						GROUP BY J.id", [$r['job_id'], $r['job_id']]);
						if ($query->num_rows() > 0) {
							$job_row = $query->row();

							$this->db->insert('sms_queue', [
								'mobile_no' => join(',', $this->_sms['import']),
								'message'   => "$job_row->party_name, Job:$job_row->id2_format, BL:$job_row->bl_no, Cont:$job_row->containers, Arrived in CFS on $job_row->cfs_in_date"
							]);
						}
					}
					if (intval($interval->format('%r%a')) >= 0 AND $r['type'] == 'Export' AND $table['Category'] == 'EXPORT') {
						$data = [
							'job_id'       => $r['job_id'],
							'container_id' => $r['container_id'],
							'container_no' => $r['container_no'],
							'fetched_from' => 'MICT',
							'location'     => $table['DIS Location'],
							'gate_in'      => (strlen($table['In Time']) == 0 ? '0000-00-00 00:00:00' : date('Y-m-d H:i', strtotime($table['In Time']))),
							'gate_out'     => (strlen($table['Out Time']) == 0 ? '0000-00-00 00:00:00' : date('Y-m-d H:i', strtotime($table['Out Time']))),
						];
						$this->kaabar->save('deliveries_stuffings', $data, ['id' => $r['id']]);

						if (ENVIRONMENT == 'development')
							$this->_cli->lightBlue()->inline($data['location'])
								->white()->inline(',')
								->red()->inline($data['gate_in'])
								->white()->inline(',')
								->green()->out($data['gate_out']);

						// Sending Gated IN SMS
						$query = $this->db->query("SELECT J.party_id, P.name AS party_name, J.id2_format, GROUP_CONCAT(DISTINCT CJ.sb_no) AS sb_no, J.fpod,
							COUNT(DISTINCT DS.id) AS containers, DS.fetched_from, MAX(DS.gate_in) AS gate_in
						FROM deliveries_stuffings DS
							INNER JOIN jobs J ON DS.job_id = J.id
							INNER JOIN parties P ON J.party_id = P.id
							INNER JOIN child_jobs CJ ON J.id = CJ.job_id
						WHERE J.id = ? AND DS.sms_sent_gate_in = 'No' AND J.id NOT IN (
							SELECT DS.job_id FROM deliveries_stuffings DS WHERE DS.job_id = ? AND DS.gate_in = '0000-00-00 00:00:00' GROUP BY DS.job_id
						)
						GROUP BY J.id", [$r['job_id'], $r['job_id']]);
						if ($query->num_rows() > 0) {
							$job_row = $query->row();

 							$this->db->insert('sms_queue', [
								'mobile_no' => join(',', $this->_sms['export']),
								'message'   => "$job_row->party_name, Job:$job_row->id2_format, SB:$job_row->sb_no, FPD:$job_row->fpod, Cont:$job_row->containers, Have Gated In $job_row->fetched_from On $job_row->gate_in"
							]);

							$this->db->update('deliveries_stuffings', ['sms_sent_gate_in' => 'Yes'], ['job_id' => $data['job_id']]);
						}

						// Sending On Board SMS
						$query = $this->db->query("SELECT J.party_id, P.name AS party_name, J.id2_format, GROUP_CONCAT(DISTINCT CJ.sb_no) AS sb_no, J.fpod,
							COUNT(DISTINCT DS.id) AS containers
						FROM deliveries_stuffings DS INNER JOIN jobs J ON DS.job_id = J.id
							INNER JOIN parties P ON J.party_id = P.id
							INNER JOIN child_jobs CJ ON J.id = CJ.job_id
						WHERE J.id = ? AND DS.sms_sent_gate_out = 'No' AND J.id NOT IN (
							SELECT DS.job_id FROM deliveries_stuffings DS WHERE DS.job_id = ? AND DS.gate_out = '0000-00-00 00:00:00' GROUP BY DS.job_id
						)
						GROUP BY J.id", [$r['job_id'], $r['job_id']]);
						if ($query->num_rows() > 0) {
							$job_row = $query->row();

 							$this->db->insert('sms_queue', [
								'mobile_no' => join(',', $this->_sms['export']),
								'message'   => "$job_row->party_name, Job:$job_row->id2_format, SB:$job_row->sb_no, FPD:$job_row->fpod, Cont:$job_row->containers, Boarded on Vessel"
							]);

							$this->db->update('deliveries_stuffings', ['sms_sent_gate_out' => 'Yes'], ['job_id' => $r['job_id']]);
						}
					}
				}
				else {
					if (ENVIRONMENT == 'development')
						$this->_cli->red()->out('No Data.');
				}

				unset($html);
				unset($table);
			}
			else {
				if (ENVIRONMENT == 'development')
					$this->_cli->red()->out('No Records Found.');
			}
		}

		if (! $this->input->is_cli_request())
			echo closeSelfAndRefreshParent();
	}
}
