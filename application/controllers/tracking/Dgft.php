<?php

include(APPPATH.'libraries/ganon.php');

class Dgft extends MY_Controller {
	var $_fetch_cookie;

	function __construct() {
		parent::__construct();

		$this->_fetch_cookie   = FCPATH . 'tmp/' . $this->_class . '_cookie';
	}
	
	function index($party_id = 0) {
		ini_set('max_execution_time', '0');
		ini_set('memory_limit', '512M');

		$this->load->library('Zebra_cURL');

		if ($party_id == 0) {
			$sql = "SELECT P.id, P.name, P.iec_no FROM parties P WHERE LENGTH(TRIM(P.iec_no)) > 0";
		}
		else {
			$sql = "SELECT P.id, P.name, P.iec_no FROM parties P WHERE P.id = $party_id AND LENGTH(TRIM(P.iec_no)) > 0";
		}
		$query = $this->db->query($sql);
		$parties = $query->result_array();

		foreach ($parties as $party) {
			$this->_body = '';
			$this->zebra_curl->option(CURLOPT_REFERER, "http://dgft.delhi.nic.in:8100/dgft/IecPrint");
			$this->zebra_curl->post("http://dgft.delhi.nic.in:8100/dgft/IecPrint", array(
					'iec'  => trim($party['iec_no']),
					'name' => strtoupper(substr(trim($party['name']), 0, 4))
				), 
				function($result) {
					if ($result->response[1] == CURLE_OK)
						$this->_body = str_replace('&nbsp;', '', $result->body);
					else
						die('An error occured: ' . $result->response[0]);
				}
			);

			$found  = strpos($this->_body, "The name Given By you does not match with the data OR you have entered less than three letters");
			$found |= strpos($this->_body, "IEC is not proper");
			$found |= strpos($this->_body, "No Data Available For The Given IEC");
			if ($found == false) {
				$html = str_get_dom($this->_body);
				$i = 1;
				foreach ($html('table') as $table_index => $html_table) {
					switch ($table_index) {
						case 0: 	// IEC Details
							foreach($html_table('td') as $element) {
								if (($i % 3) == 1) {
									$last_field = $element->getPlainText();
									$table[$last_field] = '';
								}
								if (($i % 3) == 0)
									$table[$last_field] = $element->getInnerText();

								$i++;
							}
							break;

						case 1:		// Directors List
							foreach($html_table('td') as $element) {
								if (($i % 2) == 1)
									$table['directors'][] = $element->getInnerText();
								$i++;
							}
							break;

						case 2:		// Branches Details
							foreach($html_table('td') as $element) {
								if (($i % 2) == 1)
									$table['branches'][] = $element->getInnerText();
								$i++;
							}
							break;

						case 3:		// Registration Details
							foreach($html_table('td') as $element) {
								if (($i % 2) == 1)
									$table['registration_details'][] = $element->getInnerText();
								$i++;
							}
							break;

						case 4:		// RCMC Details
							foreach($html_table('td') as $element) {
								if (($i % 2) == 1)
									$table['rcmc_details'][] = $element->getInnerText();
								$i++;
							}
							break;
					}
				}

				if (count($table) >= 14)
					$data = array(
						'party_id'             => $party['id'],
						'iec_no'               => trim($table['IEC']),
						'iec_allotment_date'   => trim($table['IEC Allotment Date']),
						'file_number'          => trim($table['File Number']),
						'file_date'            => trim($table['File Date']),
						'party_name_address'   => trim($table['Party Name and Address']),
						'phone_no'             => trim($table['Phone No']),
						'email'                => trim($table['e_mail']),
						'exporter_type'        => trim($table['Exporter Type']),
						'date_establishment'   => trim($table['Date of Establishment']),
						'bin_pan_extension'    => trim($table['BIN (PAN+Extension)']),
						'pan_issue_date'       => trim($table['PAN ISSUE DATE']),
						'pan_issued_by'        => trim($table['PAN ISSUED BY']),
						'nature_of_concern'    => trim($table['Nature Of Concern']),
						'banker_detail'        => trim($table['Banker Detail']),
						'directors'            => trim(implode('<hr />', $table['directors'])),
						'branches'             => trim(implode('<hr />', $table['branches'])),
						'registration_details' => trim(implode('<hr />', $table['registration_details'])),
						'rcmc_details'         => trim(implode('<hr />', $table['rcmc_details'])),
						);
				unset($html);
				unset($table);

				if (count($data) > 1) {
					$dgft_body_id = intval($this->kaabar->getField('dgft_iecs', $party['id'], 'party_id', 'id'));
					$this->kaabar->save('dgft_iecs', $data, array('id' => $dgft_body_id));
				}
			}
		}

		// Updating PAN No FROM dgft_iecs
		$this->db->query("UPDATE dgft_iecs DI INNER JOIN parties P ON TRIM(DI.iec_no) = TRIM(P.iec_no)
			SET P.pan_no = LEFT(DI.bin_pan_extension, 10)");

		if ($party_id > 0) {
			setSessionAlert('Record Fetched from DGFT', 'success');
			$this->load->library('user_agent');
  	 		redirect($this->agent->referrer());
		}
		else if (! $this->_is_cli) 
			echo closeWindow();
	}

	function update() {
		$iecs = array();
		$sql = "SELECT COALESCE(HSS.party_id, J.party_id) AS party_id, COALESCE(HSS.name, P.name) AS name, BE.iec_no 
		FROM icegate_be BE INNER JOIN jobs J ON BE.job_id = J.id
			INNER JOIN parties P ON J.party_id = P.id
			LEFT OUTER JOIN (
				SELECT HS1.*, HP.name
				FROM high_seas HS1 INNER JOIN parties HP ON HS1.party_id = HP.id
					LEFT OUTER JOIN high_seas HS2 ON HS1.job_id = HS2.job_id AND HS1.id < HS2.id
				WHERE HS2.id IS NULL
			) HSS ON HSS.job_id = J.id
		WHERE BE.iec_no NOT IN (
			SELECT iec_no FROM dgft_iecs
		)
		AND LENGTH(TRIM(BE.iec_no)) > 0
		GROUP BY BE.iec_no
		ORDER BY party_id";
		$query = $this->db->query($sql);
		$rows  = $query->result_array();
		foreach ($rows as $r) {
			$iecs[$r['party_id']][] = $r['iec_no'];
		}

		foreach ($iecs as $party_id => $iec_nos) {
			if (count($iec_nos) == 1) {
				$this->kaabar->save('parties', array('iec_no' => $iec_nos[0]), array('id' => $party_id));
			}
		}

		$this->index();
	}
}
