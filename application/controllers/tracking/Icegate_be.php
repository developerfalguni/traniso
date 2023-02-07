<?php

ini_set('max_execution_time', '0');
ini_set('memory_limit', '512M');

use GuzzleHttp\Client;
use Wangoviridans\Ganon;

class Icegate_be extends MY_Controller {
	var $_be_cookie;
	var $_challan_cookie;

	function __construct() {
		parent::__construct();

		$icegate_be_progress = [
			'progress' => 0,
			'total'    => 0,
			'percent'  => 0,
		];

		$icegate_challan_progress = [
			'progress' => 0,
			'total'    => 0,
			'percent'  => 0,
		];

		$this->cache->save('icegate_be_progress', $icegate_be_progress, 300);
		$this->cache->save('icegate_challan_progress', $icegate_challan_progress, 300);

		$this->_be_cookie      = new \GuzzleHttp\Cookie\CookieJar;
		$this->_challan_cookie = new \GuzzleHttp\Cookie\CookieJar;
		$this->_client         = new Client([
			'cookies' => true,
			'verify'  => false,
		]);

		$this->load->model('import');
	}

	function index($job_id) {
		if (intval($job_id) == 0) {
			echo closeWindow();
			return;
		}

		$sql = "SELECT J.be_no, CONCAT(IP.name, ' (', IP.unece_code, ')') AS icegate_port 
		FROM jobs J INNER JOIN indian_ports IP ON J.indian_port_id = IP.id 
		WHERE J.id = ?";
		$query = $this->db->query($sql, [$job_id]);
		$port = $query->row_array();
		$data = $port;

		$icegate = $this->kaabar->getRow('icegate_be', $job_id, 'job_id');
		if ($icegate == false) {
			$this->kaabar->save('icegate_be', ['job_id' => $job_id]);
			$icegate = $this->kaabar->getRow('icegate_be', $job_id, 'job_id');
		}
		$challans = [];
		if (strlen($icegate['challan_no']) > 0) {
			$sql = "SELECT id, reference_id, payment_datetime, iec_no, iec_name, bank_branch_code,
				bank_transaction_no, document_type, ices_location_code, bank_name, receipt_datetime
			FROM icegate_challans 
			WHERE be_no = ?";
			$query = $this->db->query($sql, [$data['be_no']]);
			$challans = $query->result_array();
			if ($challans) {
				foreach ($challans as $index => $challan) {
					$sql = "SELECT sr_no, challan_no, be_no, be_date, duty_amount, ices_status_code 
					FROM icegate_challans 
					WHERE reference_id = ?
					ORDER BY sr_no";
					$query = $this->db->query($sql, [$challan['reference_id']]);
					$challans[$index]['details'] = $query->result_array();
				}
			}
		}
		
		if ($icegate['last_fetched'] == '00-00-0000 00:00:00') {
			$data['page_desc'] = "<span class=\"label label-default\">Fetch is Pending.</span>";
		}
		else {
			$this->load->helper('datefn');
			$lapsed    = secondsDiff($icegate['last_fetched'], date('d-m-Y H:i:s'));
			$data['page_desc'] = "<span class=\"label label-default\">$lapsed Sec ago.</span>";
			if ($lapsed > 60) {
				$lapsed    = round($lapsed / 60, 0);
				$data['page_desc'] = "<span class=\"label label-default\">$lapsed Min ago.</span>";
				if ($lapsed > 60) {
					$lapsed    = round($lapsed / 60, 0);
					$data['page_desc'] = "<span class=\"label label-default\">$lapsed Hrs ago.</span>";
					if ($lapsed > 24) {
						$lapsed    = round($lapsed / 24, 0);
						$data['page_desc'] = "<span class=\"label label-default\">$lapsed Days ago.</span>";
						if ($lapsed > 365) {
							$lapsed    = round($lapsed / 365, 0);
							$data['page_desc'] = "<span class=\"label label-default\">$lapsed Yrs ago.</span>";
						}
					}
				}
			}
		}

		$data['job_id']     = $job_id;
		$data['jobs']       = $this->import->getJobsInfo($job_id, false);
		$data['icegate']    = $icegate;
		$data['challans']   = $challans;
		$data['page']       = $this->_clspath.$this->_class;
		$data['page_title'] = humanize($this->_class);
		$this->load->view('plain', $data);
	}

	function captcha($job_id = 0) {

		$this->load->helper('file');
		$icegate_be_progress = [
			'progress' => 0,
			'total'    => 0,
			'percent'  => 0,
		];
		$this->cache->save('icegate_be_progress', $icegate_be_progress, 300);

		if ($this->input->post('captchaResp')) {
			$this->_be_cookie = unserialize($this->cache->get('icegate_be_cookie'));

			$response = $this->_client->post('https://enquiry.icegate.gov.in/enquiryatices/BETrack_Ices_action', [
				'cookies'     => $this->_be_cookie,
				'headers' => [
					'Referer' => 'https://enquiry.icegate.gov.in/enquiryatices/beTrackIces',
				],
				'form_params' => [
					'beTrack_location'   => 'Kandla SEA (INIXY1)',
					'BE_NO'              => '123456',
					'BE_DT'              => '2015/03/01',
					'csrfPreventionSalt' => $this->input->post('csrfPreventionSalt'),
					'captchaResp'        => $this->input->post('captchaResp')
				]
			]);

			if ($response->getStatusCode() != 200) {
				return;
			}

			$this->track($job_id);
			return;
		}

		$cookie = unserialize($this->cache->get('icegate_be_cookie'));
		//if ($cookie === FALSE) {
			$response = $this->_client->get('https://enquiry.icegate.gov.in/enquiryatices/beTrackIces', [
				'cookies' => $this->_be_cookie
			]);
			$this->cache->save('icegate_be_cookie', serialize($this->_be_cookie), 500);

			$body  = str_replace(['&nbsp;'], '', $response->getBody()->read(1024*1024*2));
			$html  = str_get_dom($body);
			$inputs = $html('input');
			foreach($inputs as $i) {
				if ($i->getAttribute('id') == 'BE_trackICESform_csrfPreventionSalt')
					$data['csrfPreventionSalt'] = trim($i->getAttribute('value'));
			}
		//}

		$response = $this->_client->get('https://enquiry.icegate.gov.in/enquiryatices/CaptchaImg.jpg', [
			'cookies' => $this->_be_cookie
		]);
		$this->cache->save('icegate_be_cookie', serialize($this->_be_cookie), 500);
		
		if ($response->getStatusCode() != 200) {
			return;
		}

		$image = base64_encode($response->getBody()->read(1024*1024*2));

		$data['image']       = $image;
		$data['page_title']  = 'Icegate Captcha';
		$data['page']        = $this->_clspath.'icegate_captcha';
		$this->load->view('plain', $data);
	}

	function track($job_id = 0) {

		$fetch_freq  = Settings::get('icegate_fetch_frequency_min');
		$fetch_delay = date("Y-m-d H:i:00", strtotime("-" . $fetch_freq . " min"));

		if ($job_id == 0) {

			$sql = "SELECT IT.id, IT.job_id, J.be_no, DATE_FORMAT(J.be_date, '%Y/%m/%d') AS be_date, J.party_id, P.name AS party_name, P.iec_no,
				CONCAT(IP.name, ' (', IP.unece_code, ')') AS icegate_port, IT.last_status, IT.status, ID.id AS payment_id, J.cha_id
				FROM icegate_be IT
					INNER JOIN jobs J ON IT.job_id = J.id
					INNER JOIN parties P ON J.party_id = P.id
					INNER JOIN indian_ports IP ON J.indian_port_id = IP.id
					LEFT OUTER JOIN import_details ID ON IT.job_id = ID.job_id
				WHERE (LENGTH(TRIM(J.be_no)) > 0 AND J.be_no > 0) AND 
					(LENGTH(TRIM(IT.ooc_date)) = 0 OR TRIM(IT.ooc_date) = 'N.A.') AND 
					(IT.last_fetched = '0000-00-00 00:00:00' OR IT.last_fetched <= '$fetch_delay')
				ORDER BY IT.last_fetched";
		}
		else  {
			$sql = "SELECT IT.id, IT.job_id, J.be_no, DATE_FORMAT(J.be_date, '%Y/%m/%d') AS be_date, J.party_id, P.name AS party_name, P.iec_no,
				CONCAT(IP.name, ' (', IP.unece_code, ')') AS icegate_port, IT.last_status, IT.status, ID.id AS payment_id, J.cha_id
				FROM icegate_be IT
					INNER JOIN jobs J ON IT.job_id = J.id
					INNER JOIN parties P ON J.party_id = P.id
					INNER JOIN indian_ports IP ON J.indian_port_id = IP.id
					LEFT OUTER JOIN import_details ID ON IT.job_id = ID.job_id
				WHERE IT.job_id = $job_id";
		}
		$query = $this->db->query($sql);
		$rows = $query->result_array();

		$total_rows = count($rows);
		foreach ($rows as $progress => $job) {
			$data  = ['last_fetched' => date('Y-m-d H:i:s')];

			$body = '';
			
			$response = $this->_client->post('https://enquiry.icegate.gov.in/enquiryatices/BE_IcesDetails_action', [
				'cookies'     => $this->_be_cookie,
				'headers' => [
					'Referer' => 'https://enquiry.icegate.gov.in/enquiryatices/BETrack_Ices_action',
				],
				'form_params' => [
					'BE_NO'            => intval($job['be_no']),
					'BE_DT'            => $job['be_date'],
					'beTrack_location' => $job['icegate_port']
				]
			]);

			if ($response->getStatusCode() != 200) {
				return;
			}

			$body = str_replace(['&nbsp;'], '', $response->getBody()->read(1024*1024*2));

			$found = strpos($body, "An Error Occured, Result Page Not Found! Please try again.");
			if ($found) die('An Error Occured, Result Page Not Found! Please try again.');

			$found = strpos($body, "Your Session is expire. Please try again.");
			if ($found) die('Your Session is expire. Please try again.');

			$found = strpos($body, "No Record found");
			if ($found == false) {
				$table = [];
				$html  = str_get_dom($body);
				foreach($html('tr') as $tr) {
					foreach($tr('th') as $index => $th) {
						$header[$index] = trim($th->getPlainText());
					}

					foreach($tr('td') as $index => $td) {
						$table[$header[$index]] = $td->getPlainText();
					}
				}

				if (count($table) == 14)
					$data += [
						'iec_no' 			=> trim($table['IEC']),
						'total_value' 		=> trim($table['TOT VAL']),
						'type' 				=> trim($table['TYP']),
						'cha_no' 			=> trim($table['CHA Number']),
						'first_check' 		=> trim($table['FIRST CHECK']),
						'prior_be' 			=> trim($table['PRIOR BE']),
						'section_48' 		=> trim($table['SEC48']),
						'appraising_group'  => trim($table['APPRAISING GROUP']),
						'accessible_value'  => trim($table['TOTAL ASSESSABLE VALUE']),
						'total_package' 	=> trim($table['TOTAL PACKAGE']),
						'gross_weight'  	=> trim($table['GROSS WEIGHT (Kg)']),
						'total_duty' 		=> trim($table['TOTAL DUTY (INR)']),
						'fine_penalty'  	=> trim($table['FINE PENALTY (INR)']),
						'wbe_no' 			=> trim($table['WBE No.'])
					];
				unset($html);
				unset($header);
				unset($table);
			}

			$body = '';
			$response = $this->_client->post('https://enquiry.icegate.gov.in/enquiryatices/BE_IcesCURRST_action', [
				'cookies'     => $this->_be_cookie,
				'headers' => [
					'Referer' => 'https://enquiry.icegate.gov.in/enquiryatices/BETrack_Ices_action',
				],
				'form_params' => [
					'BE_NO'            => intval($job['be_no']),
					'BE_DT'            => $job['be_date'],
					'beTrack_location' => $job['icegate_port']
				]
			]);

			if ($response->getStatusCode() != 200) {
				return;
			}

			$body = str_replace(['&nbsp;'], '', $response->getBody()->read(1024*1024*2));

			$found = strpos($body, "An Error Occured, Result Page Not Found! Please try again.");
			if ($found) die('An Error Occured, Result Page Not Found! Please try again.');

			$found = strpos($body, "Your Session is expire. Please try again.");
			if ($found) die('Your Session is expire. Please try again.');

			$found = strpos($body, "No Record found");
			if ($found == false) {
				$table = [];
				$html = str_get_dom($body);
				foreach($html('tr') as $tr) {
					foreach($tr('th') as $index => $th) {
						$header[$index] = trim($th->getPlainText());
					}

					foreach($tr('td') as $index => $td) {
						if ($header[$index] == 'QUERY RAISED' && isset($table[$header[$index]])) 
							$table[$header[$index]] .= " # " . $td->getPlainText();
						else if ($header[$index] == 'QUERY REPLY' && isset($table[$header[$index]])) 
							$table[$header[$index]] .= " # " . $td->getPlainText();
						else 
							$table[$header[$index]] = $td->getPlainText();
					}
				}

				/*echo "<pre>";
				print_r($table);*/

				if (count($table) == 11)
					$data += [
						'appraisement'		=> trim($table['APPRAISEMENT']),
						'current_queue' 	=> trim($table['CURRENT QUEUE']),
						'query_raised'		=> trim($table['NO OF QUERY RAISED']),
						'query_reply'		=> trim($table['NO OF QUERY REPLIED']),
						'reply_date' 		=> trim($table['REPLY DATE']),
						'reply_status' 		=> trim($table['REPLY STATUS']),
						'appraisement_date' => trim($table['APPR DATE']),
						'assessment_date'	=> trim($table['ASSESS DATE']),
						'payment_date'		=> trim($table['PAYMENT DATE']),
						'exam_date'			=> trim($table['EXAM DATE']),
						'ooc_date'			=> trim($table['OOC DATE'])
					];
				unset($html);
				unset($header);
				unset($table);
			}


			$body = '';
			$response = $this->_client->post('https://enquiry.icegate.gov.in/enquiryatices/BE_IcesDUTYdtls_action', [
				'cookies'     => $this->_be_cookie,
				'headers' => [
					'Referer' => 'https://enquiry.icegate.gov.in/enquiryatices/BETrack_Ices_action',
				],
				'form_params' => [
					'BE_NO'            => intval($job['be_no']),
					'BE_DT'            => $job['be_date'],
					'beTrack_location' => $job['icegate_port']
				]
			]);

			if ($response->getStatusCode() != 200) {
				return;
			}

			$body = str_replace(['&nbsp;'], '', $response->getBody()->read(1024*1024*2));

			$found = strpos($body, "An Error Occured, Result Page Not Found! Please try again.");
			if ($found) die('An Error Occured, Result Page Not Found! Please try again.');

			$found = strpos($body, "Your Session is expire. Please try again.");
			if ($found) die('Your Session is expire. Please try again.');

			$found = strpos($body, "No Record found");
			if ($found == false) {
				$table = [];
				$html  = str_get_dom($body);
				foreach($html('tr') as $tr) {
					foreach($tr('th') as $index => $th) {
						$header[$index] = trim($th->getPlainText());
					}

					foreach($tr('td') as $index => $td) {
						$table[$header[$index]] = $td->getPlainText();
					}
				}

				/*echo "<pre>";
				print_r($table);*/

				if (count($table) == 8)
					$data += [
						'challan_no' 		=> trim($table['CHALLAN No.']),
						'duty_amount'		=> trim($table['DUTY AMOUNT (INR)']),
						'fine_amount' 		=> trim($table['FINE AMOUNT (INR)']),
						'interest_amount' 	=> trim($table['INTEREST AMOUNT (INR)']),
						'penalty_amount'	=> trim($table['PENAL AMOUNT (INR)']),
						'total_duty_amount'	=> trim($table['TOTAL DUTY (INR)']),
						'duty_paid' 		=> trim($table['DUTY PAID (INR)']),
						'payment_mode' 		=> trim($table['MODE OF PAYMENT'])
					];
				unset($html);
				unset($header);
				unset($table);
			}

			if (count($data) > 1) {

				$data['last_status'] = $job['status'];
				$import_details      = ['job_id' => $job['job_id']];

				if (isset($data['appraisement_date'])) {
					if (strlen(trim($data['appraisement_date'])) > 0 && $data['appraisement_date'] != 'N.A.') {
						$data['status'] = 'APPRA';
						$import_details['appraisement_date'] = date('d-m-Y', strtotime($data['appraisement_date']));
					}
					if (strlen(trim($data['assessment_date'])) > 0 && $data['assessment_date'] != 'N.A.') {
						$data['status'] = 'ASSESS';
						$import_details['assessment_date'] = date('d-m-Y', strtotime($data['assessment_date']));
					}
					if (strlen(trim($data['exam_date'])) > 0 && $data['exam_date'] != 'N.A.') {
						$data['status'] = 'EXAM';
						$import_details['exam_date'] = date('d-m-Y', strtotime($data['exam_date']));
					}
					if (strlen(trim($data['payment_date'])) > 0 && $data['payment_date'] != 'N.A.') {
						$data['status'] = 'PAYMENT';
						$import_details['payment_date'] = date('d-m-Y', strtotime($data['payment_date']));
					}
					if (strlen(trim($data['ooc_date'])) > 0 && $data['ooc_date'] != 'N.A.') {
						$data['status'] = 'OOC';
						$import_details['ooc_date'] = date('d-m-Y', strtotime($data['ooc_date']));
					}
				}
				$this->kaabar->save('icegate_be', $data, ['id' => $job['id']]);

				// Updating Custom Duty
				if (isset($data['total_duty_amount']) && $data['total_duty_amount'] > 0)
					$import_details['custom_duty'] = $data['total_duty_amount'];
				else if (isset($data['total_duty']) && $data['total_duty'] > 0)
					$import_details['custom_duty'] = $data['total_duty'];

				// Calculating Stamp Duty from Accessible Value
				if (isset($data['accessible_value']) && intval($data['accessible_value']) > 0)
					$import_details['stamp_duty'] = round($data['accessible_value'] * Settings::get('stamp_duty') / 100);

				$this->kaabar->save('import_details', $import_details, ['id' => $job['payment_id']]);

				// Updating cha_id in jobs, if cha_id is zero and icegate cha_no exists in agents.
				if ($job['cha_id'] == 0) {
					$cha_id = $this->kaabar->getField('agents', $data['cha_no'], 'cha_no', 'id');
					$this->db->update('jobs', ['cha_id' => $cha_id], ['id' => $job['job_id']]);
				}
			}

			$icegate_be_progress = [
				'progress' => ($progress+1),
				'total'    => $total_rows,
				'percent'  => ceil(($progress+1)/$total_rows * 100) . '%',
			];
			$this->cache->save('icegate_be_progress', $icegate_be_progress, 300);
		}

		if ($job_id > 0) {
			setSessionAlert('Record Fetched from IceGate', 'success');
			redirect($this->_clspath.$this->_class."/index/$job_id");
		}
		else if (! $this->input->is_cli_request()){
			echo closeSelfAndRefreshParent();
		}
	}

	function getProgress() {
		header('Content-type: application/json');
		echo json_encode($this->cache->get('icegate_be_progress'));
	}


	function captchaChallan($job_id = 0) {
		
		$sql = "SELECT J.be_no, CONCAT(IP.name, ' (', IP.unece_code, ')') AS icegate_port 
		FROM jobs J INNER JOIN indian_ports IP ON J.indian_port_id = IP.id 
		WHERE J.id = ?";
		$query = $this->db->query($sql, [$job_id]);
		$port = $query->row_array();

		$icegate = $this->kaabar->getRow('icegate_be', $job_id, 'job_id');
		if ($icegate == false) {
			$this->kaabar->save('icegate_be', ['job_id' => $job_id]);
			$icegate = $this->kaabar->getRow('icegate_be', $job_id, 'job_id');
		}

		$this->load->helper('file');
		$icegate_challan_progress = [
			'progress' => 0,
			'total'    => 0,
			'percent'  => 0,
		];

		$this->cache->save('icegate_challan_progress', $icegate_challan_progress, 300);

		if ($this->input->post('captchaResp')) {

			$this->_challan_cookie = unserialize($this->cache->get('icegate_challan_cookie'));

			$response = $this->_client->post('https://epayment.icegate.gov.in/epayment/locationAction', [
				'cookies'     => $this->_challan_cookie,
				'headers' => [
					'Referer' => 'https://epayment.icegate.gov.in/epayment/multiChallanAction',
				],
				'form_params' => [
					'docType' 		=> 'BE',
					'iec'           => $icegate['iec_no'],
					'locationName'  => $port['icegate_port'],
					'captchaResp'   => $this->input->post('captchaResp')
				]
			]);

			if ($response->getStatusCode() != 200) {
				return;
			}

			$this->get_challan($job_id);
			return;
		}

		$cookie = unserialize($this->cache->get('icegate_challan_cookie'));
		
		$response = $this->_client->get('https://epayment.icegate.gov.in/epayment/CaptchaImg.jpg', [
			'cookies' => $this->_challan_cookie
		]);
		$this->cache->save('icegate_challan_cookie', serialize($this->_challan_cookie), 500);
		
		if ($response->getStatusCode() != 200) {
			return;
		}

		$image = base64_encode($response->getBody()->read(1024*1024*2));

		$data['image']       = $image;
		$data['page_title']  = 'Icegate Captcha';
		$data['page']        = $this->_clspath.'icegate_challan_captcha';
		$this->load->view('plain', $data);
	}

	function getChallanProgress() {
		header('Content-type: application/json');
		echo json_encode($this->cache->get('icegate_challan_progress'));
	}

	function get_challan($job_id = 0) {

		$years = explode('_', $this->kaabar->getFinancialYear(date('Y-m-d')));
		$from  = $years[0].'/04/01';
		$to    = date('Y/m/d');

		$response = $this->_client->get('https://epayment.icegate.gov.in/epayment/', [
			'cookies' => $this->_challan_cookie
		]);

		if ($job_id == 0) {
			$sql = "SELECT IC.iec_no, IP.name, IP.unece_code
			FROM icegate_be IC 
				INNER JOIN jobs J ON IC.job_id = J.id
				INNER JOIN indian_ports IP ON J.indian_port_id = IP.id
			WHERE LENGTH(TRIM(J.be_no)) > 0 AND 
				LENGTH(TRIM(IC.iec_no)) > 0 AND LENGTH(TRIM(challan_no)) > 0 AND 
				(LENGTH(TRIM(IC.payment_date)) != 0 OR IC.payment_date != 'N.A.') AND 
				IC.challan_no NOT IN (SELECT challan_no FROM icegate_challans)
			GROUP BY IC.iec_no
			ORDER BY IC.iec_no";
		}
		else {
			$sql = "SELECT IC.iec_no, IP.name, IP.unece_code
			FROM icegate_be IC 
				INNER JOIN jobs J ON IC.job_id = J.id
				INNER JOIN indian_ports IP ON J.indian_port_id = IP.id
			WHERE IC.job_id = $job_id AND LENGTH(TRIM(J.be_no)) > 0 AND 
				LENGTH(TRIM(IC.iec_no)) > 0 AND LENGTH(TRIM(challan_no)) > 0 AND 
				(LENGTH(TRIM(IC.payment_date)) != 0 OR IC.payment_date != 'N.A.')";
				// AND 
				// IC.challan_no NOT IN (SELECT challan_no FROM icegate_challans)
				//LENGTH(TRIM(IC.iec_no)) > 0 AND LENGTH(TRIM(challan_no)) > 0 AND 
		}

		$query = $this->db->query($sql);
		$jobs = $query->result_array();

		foreach ($jobs as $job) {

			for ($today_all = 0; $today_all < 2; $today_all++) {
				if ($today_all == 0) {
					// Fetching Todays Transactions
					$body = '';
					$response = $this->_client->post('https://epayment.icegate.gov.in/epayment/epayTodayTran_action', [
						'cookies'     => $this->_challan_cookie,
						'headers' => [
							'Referer' => 'https://epayment.icegate.gov.in/epayment/multiChallanAction',
						],
						'form_params' => [
							'iec'             => $job['iec_no'],
							'locationName'    => $job['unece_code'],
							'location'        => 'Please Select Location',
							'refId'           => '',
							'startDate'       => '',
							'endDate'         => '',
							'reportStartDate' => '',
							'reportEndDate'   => '',
						]
					]);

					if ($response->getStatusCode() != 200) {
						return;
					}
				}
				else {
					// Fetching Previous Date Transactions
					$body = '';
					$response = $this->_client->post('https://epayment.icegate.gov.in/epayment/epayDATETran_action', [
						'cookies'     => $this->_challan_cookie,
						'headers' => [
							'Referer' => 'https://epayment.icegate.gov.in/epayment/multiChallanAction',
						],
						'form_params' => [
							'iec'             => $job['iec_no'],
							'refId'           => '',
							'locationName'    => $job['unece_code'],
							'location'        => 'All',
							'startDate'       => $from,
							'endDate'         => $to,
							'reportStartDate' => '',
							'reportEndDate'   => '',
						]
					]);

					if ($response->getStatusCode() != 200) {
						return;
					}
				}

				$body = str_replace(['&nbsp;'], '', $response->getBody()->read(1024*1024*2));

				$reference_ids = [];
				$found = strpos($body, "No Record found");


				print_r($body);


				if ($found == false) {

					$header = [];
					$table  = [];
					$html   = str_get_dom($body);
					$html_table = $html('table');
					foreach($html_table as $t) {
						foreach($t('tr') as $tr) {
							if ($tr->getAttribute('class') == 'thText') {
								foreach ($tr('th') as $index => $th) {
									$header[$index] = $th->getPlainText();
								}
							}

							if (count($header) == 10) {
								if ($tr->getAttribute('class') == 'tdText') {
									$i = 0;
									foreach ($tr('td') as $index => $td) {

										//$table[$header[$index]] = trim($td->getPlainText());
										$table[$header[$index]] = trim($td->getPlainText());
										//$reference_ids[] = $table;
										/*if ($i == 2) // Ref ID
											$reference_ids[] = trim($td->getPlainText());

										if ($index % 5 == 0)
											$i = -1;
										$i++;*/
									}
									$reference_ids[] = $table;
								}
							}
						}
					}
					unset($html);
					unset($html_table);
					unset($header);
					unset($table);
				}

				foreach($reference_ids as $refid) {

					$query = $this->db->query("SELECT id FROM icegate_challans WHERE reference_id = ?", [$refid['Ref ID']]);
					$row   = $query->row_array();
					if ($row == false) {
						$body = '';
						if ($today_all == 0) {
							$response = $this->_client->post('https://epayment.icegate.gov.in/epayment/printAction', [
								'cookies'     => $this->_challan_cookie,
								'headers' => [
									'Referer' => 'https://epayment.icegate.gov.in/epayment/multiChallanAction',
								],
								'form_params' => [
									'iec'             => $job['iec_no'],
									'refId'           => $refid['Ref ID'],
									'locationName'    => $job['unece_code'],
									'location'        => 'Please Select Location',
									'startDate'       => '',
									'endDate'         => '',
									'reportStartDate' => '',
									'reportEndDate'   => '',
								]
							]);
						}
						else {
							$response = $this->_client->post('https://epayment.icegate.gov.in/epayment/printAction_DT', [
								'cookies'     => $this->_challan_cookie,
								'headers' => [
									'Referer' => 'https://epayment.icegate.gov.in/epayment/multiChallanAction',
								],
								'form_params' => [
									'iec'             => $job['iec_no'],
									'refId'           => $refid['Ref ID'],
									'locationName'    => $job['unece_code'],
									'location'        => 'Please Select Location',
									'startDate'       => $from,
									'endDate'         => $to,
									'reportStartDate' => '',
									'reportEndDate'   => '',
								]
							]);
						}

						if ($response->getStatusCode() != 200) {
							return;
						}

						$body = str_replace(['&nbsp;'], '', $response->getBody()->read(1024*1024*2));

						echo "<pre>";
						print_r($body);

						$html   = '';
						$header = [];
						$table  = [];
						$duties = 0;
						$html   = str_get_dom($body);
						$html_table = $html('table');
						foreach($html_table as $t) {
							foreach($t('tr') as $tr_index => $tr) {
								if ($tr_index == 0) {
									continue;
								}
								else if ($tr_index > 0 AND $tr_index < 6) {
									foreach($tr('th') as $th_index => $th) {
										if ($th->getAttribute('class') == 'thText')
											$header[$th_index] = trim($th->getPlainText());
									}
									
									foreach($tr('td') as $td_index => $td) {
										if ($td->getAttribute('class') == 'tdText')
											$table[$header[$td_index]] = trim($td->getPlainText());
									}
								}
								else if ($tr_index == 7) {
									foreach($tr('th') as $th) {
										$table['duties']['heading'][] = trim($th->getPlainText());
									}
								}
								else if ($tr_index > 7) {
									if ($tr->getAttribute('class') == 'tdText') {
										foreach($tr('td') as $td) {
											$table['duties']['rows'][] = $td->getPlainText();
										}
									}
								}
							}
							break;
						}
						unset($html);
						unset($html_table);
						unset($tds);

						if (count($table) >= 10) {
							$data = [
								'id' 				  => 0,
								'reference_id'        => $table['ICEGATE Reference ID'],
								'payment_datetime'    => $table['Date & Time of Payment'],
								'iec_no'              => $table['IEC'],
								'iec_name'            => $table['IEC Name'],
								'bank_branch_code'    => $table['Bank Branch Code'],
								'bank_transaction_no' => $table['Bank Transaction Number'],
								'document_type'       => $table['Document Type'],
								'ices_location_code'  => $table['ICES Location Code'],
								'bank_name'           => $table['Bank Name'],
								'receipt_datetime'    => $table['Receipt Date & Time']
							];

							// For Multiple Challans
							$table_count = count($table['duties']['heading']);
							$row_count   = count($table['duties']['rows']);
							for ($i = 0; $i < $row_count; $i+=6) {
								if (! isset($table['duties']['rows'][($i+5)])) break;
								$data['sr_no']            = $table['duties']['rows'][($i)];
								$data['challan_no']       = $table['duties']['rows'][($i+1)];
								$data['be_no']            = $table['duties']['rows'][($i+2)];
								$data['be_date']          = $table['duties']['rows'][($i+3)];
								$data['duty_amount']      = $table['duties']['rows'][($i+4)];
								$data['ices_status_code'] = $table['duties']['rows'][($i+5)];

							$this->kaabar->save('icegate_challans', $data);
							}
						}
					}
				}
			}
		}
		exit;

		if (! $this->input->is_cli_request()){
			setSessionAlert('Challans Fetched from IceGate', 'success');
			redirect($this->_clspath.$this->_class."/index/$job_id");
		}
	}

	function print_challan($id) {
		$data['challans'] = [];
		$reference_id     = $this->kaabar->getField('icegate_challans', $id, 'id', 'reference_id');
		if ($reference_id)
			$data['challans'] = $this->kaabar->getRows('icegate_challans', $reference_id, 'reference_id');
		$data['script'] = 'window.print();';
		$data['page_title'] = 'IceGate Challan';
		$this->load->view($this->_clspath.'icegate_challan', $data);
	}
}
