<?php

ini_set('max_execution_time', '0');
ini_set('memory_limit', '512M');

use GuzzleHttp\Client;
use Wangoviridans\Ganon;

class Icegate_sb extends MY_Controller {
	var $_fetch_cookie;

	function __construct() {
		parent::__construct();

		$this->_table = 'icegate_sb';
		//$this->_fetch_cookie = FCPATH . 'tmp/' . $this->_class . '_cookie';

		$icegate_sb_progress = [
			'progress' => 0,
			'total'    => 0,
			'percent'  => 0,
		];
		$this->cache->save('icegate_sb_progress', $icegate_sb_progress, 300);

		$this->_sb_cookie      = new \GuzzleHttp\Cookie\CookieJar;
		$this->_challan_cookie = new \GuzzleHttp\Cookie\CookieJar;
		$this->_client         = new Client([
			'cookies' => true,
			'verify'  => false,
		]);

		$this->load->model('export');

		$this->load->library('Zebra_cURL');
	}
	
	function _search_array($needle, $array) {
		foreach ($array as $key => $value)
        	if (stripos($value, $needle) !== false)
	            return $key;
        return false;
	}

	function _checkCookie($type = 'fetch') {
		if ($type == 'fetch') {
			if (file_exists($this->_fetch_cookie)) {
				$file_stat = stat($this->_fetch_cookie);
				if ((date("U") - $file_stat['mtime']) > 590) {	// 9 Min 50 Sec.
					unlink($this->_fetch_cookie);
				}
				else {
					echo 'Already fetching IceGate status...';
					exit;
				}
			}
		}
		else if ($type == 'challan') {
			if (file_exists($this->_challan_cookie)) {
				$file_stat = stat($this->_challan_cookie);
				if ((date("U") - $file_stat['mtime']) > 590) {	// 9 Min 50 Sec.
					unlink($this->_challan_cookie);
				}
				else {
					echo 'Already fetching IceGate status...';
					exit;
				}
			}
		}
	}

	function index($child_job_id) {
		if (intval($child_job_id) == 0) {
			echo closeWindow();
			return;
		}

		$sql = "SELECT CJ.job_id, CJ.sb_no, CONCAT(IP.name, ' (', IP.unece_code, ')') AS icegate_port 
		FROM child_jobs CJ INNER JOIN jobs J ON CJ.job_id = J.id
			INNER JOIN indian_ports IP ON J.custom_port_id = IP.id 
		WHERE CJ.id = ?";
		$query = $this->db->query($sql, array($child_job_id));
		$port = $query->row_array();
		$data = $port;

		$icegate = $this->kaabar->getRow($this->_table, $child_job_id, 'child_job_id');
		if ($icegate == false) {
			$this->kaabar->save($this->_table, array('child_job_id' => $child_job_id));
			$icegate = $this->kaabar->getRow($this->_table, $child_job_id, 'child_job_id');
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

		$data['child_job_id'] = $child_job_id;
		$data['jobs']         = $this->export->getJobsInfo($port['job_id'], false);
		$data['icegate']      = $icegate;
		$data['page']         = $this->_clspath.$this->_class;
		$data['page_title']   = "IceGate";
		$data['hide_title']   = true;
		$data['hide_menu']    = true;
		$data['hide_footer']  = true;
		$this->load->view('index', $data);
	}

	function captcha_1($child_job_id = 0) {
		$this->load->helper('file');
		$this->session->set_userdata($this->_class.'_progress', '0');
		$this->session->set_userdata($this->_class.'_total', '0');
		$this->session->set_userdata($this->_class.'_percent', '0%');
		
		if ($this->input->post('captchaResp')) {

			$content    = read_file($this->_fetch_cookie);
			$start      = strpos($content, "JSESSIONID");
			$start     += 11;
			$jsessionid = substr($content, $start, 32);

			$this->zebra_curl->option(array(
				CURLOPT_REFERER    => "https://enquiry.icegate.gov.in/enquiryatices/SBTrack_Ices_action",
				CURLOPT_COOKIEFILE => $this->_fetch_cookie,
				CURLOPT_IPRESOLVE  => CURL_IPRESOLVE_V4,
			));
			$this->zebra_curl->post("https://enquiry.icegate.gov.in/enquiryatices/SBTrack_Ices_action", 
				array(
					'sbTrack_location' => 'Kandla SEA (INIXY1)',
					'SB_NO'            => 123456,
					'SB_DT'            => '2013/07/04',
					'captchaResp'      => $this->input->post('captchaResp')
				),
				function($result) {
					if (! $result->response[1] == CURLE_OK)
						die('An error occured: ' . $result->response[0]);
				}
			);
			unlink(FCPATH . "tmp/$jsessionid.jpg");
			$this->track($child_job_id);
			return;
		}



		if (file_exists($this->_fetch_cookie))
		 	unlink($this->_fetch_cookie);

		$chetan = $this->zebra_curl->cookies($this->_fetch_cookie, TRUE); 

		echo "<pre>";
		print_r($this->_fetch_cookie);
		exit;

		$this->zebra_curl->download("https://enquiry.icegate.gov.in/enquiryatices/CaptchaImg.jpg", FCPATH . "tmp", null);
		$content    = read_file($this->_fetch_cookie);
		$start      = strpos($content, "JSESSIONID");
		$start     += 11;
		$jsessionid = trim(substr($content, $start, 106));

		rename(FCPATH . "tmp/CaptchaImg.jpg", FCPATH . "tmp/$jsessionid.jpg");

		$path = FCPATH . "tmp/$jsessionid.jpg";
		$type = pathinfo($path, PATHINFO_EXTENSION);
		$imgdata = file_get_contents($path);
		$base64 = base64_encode($imgdata);
		
		$fix_session = "# Netscape HTTP Cookie File
			# http://curl.haxx.se/rfc/cookie_spec.html
			# This file was generated by libcurl! Edit at your own risk.

			www.icegate.gov.in	FALSE	/TrackAtICES	FALSE	0	JSESSIONID	$jsessionid
			icegate.gov.in	FALSE	/TrackAtICES	FALSE	0	JSESSIONID	$jsessionid
			";
		write_file($this->_fetch_cookie, $fix_session);

		$data['image']       = $base64;
		$data['page_title']  = 'Icegate Captcha';
		$data['page']        = $this->_clspath.'icegate_captcha';
		$data['hide_menu']   = true;
		$data['hide_title']  = true;
		$data['hide_footer'] = true;
		$this->load->view('index', $data);
	}


	function captcha($job_id = 0) {
		$this->load->helper('file');
		$icegate_sb_progress = [
			'progress' => 0,
			'total'    => 0,
			'percent'  => 0,
		];
		$this->cache->save('icegate_sb_progress', $icegate_sb_progress, 300);

		if ($this->input->post('captchaResp')) {
			$this->_sb_cookie = unserialize($this->cache->get('icegate_sb_cookie'));

			$response = $this->_client->post('https://enquiry.icegate.gov.in/enquiryatices/SBTrack_Ices_action', [
				'cookies'     => $this->_sb_cookie,
				'headers' => [
					'Referer' => 'https://enquiry.icegate.gov.in/enquiryatices/sbTrack',
				],
				'form_params' => [
					'sbTrack_location'   => 'MUNDRA SEA (INMUN1)',
					'SB_NO'              => '6694988',
					'SB_DT'              => '2019/09/03',
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

		$cookie = unserialize($this->cache->get('icegate_sb_cookie'));
		//if ($cookie === FALSE) {
			$response = $this->_client->get('https://enquiry.icegate.gov.in/enquiryatices/sbTrack', [
				'cookies' => $this->_sb_cookie
			]);
			$this->cache->save('icegate_sb_cookie', serialize($this->_sb_cookie), 500);

			$body  = str_replace(['&nbsp;'], '', $response->getBody()->read(1024*1024*2));
			$html  = str_get_dom($body);
			$inputs = $html('input');

			foreach($inputs as $i) {
				if ($i->getAttribute('id') == 'SB_trackICESform_csrfPreventionSalt')
					$data['csrfPreventionSalt'] = trim($i->getAttribute('value'));
			}
		//}


		$response = $this->_client->get('https://enquiry.icegate.gov.in/enquiryatices/CaptchaImg.jpg', [
			'cookies' => $this->_sb_cookie
		]);
		$this->cache->save('icegate_sb_cookie', serialize($this->_sb_cookie), 500);
		
		if ($response->getStatusCode() != 200) {
			return;
		}

		$image = base64_encode($response->getBody()->read(1024*1024*2));

		$data['image']       = $image;
		$data['page_title']  = 'Icegate Captcha';
		$data['page']        = $this->_clspath.'icegate_captcha';
		$this->load->view('plain', $data);
	}

	function track($child_job_id = 0) {
		ini_set('max_execution_time', '0');
		ini_set('memory_limit', '512M');

		$this->load->library('simple_html_dom');

		$fetch_freq  = Settings::get('icegate_fetch_frequency_min');
		$fetch_delay = date("Y-m-d H:i:00", strtotime("-" . $fetch_freq . " min"));

		if ($child_job_id == 0) {
			$sql = "SELECT IC.id, IC.child_job_id, CJ.sb_no, DATE_FORMAT(CJ.sb_date, '%Y/%m/%d') AS sb_date, 
				CONCAT(IP.name, ' (', IP.unece_code, ')') AS icegate_port, IC.last_status, IC.status
				FROM child_jobs CJ INNER JOIN jobs J ON CJ.job_id = J.id
					INNER JOIN icegate_sb IC ON CJ.id = IC.child_job_id
					INNER JOIN indian_ports IP ON J.custom_port_id = IP.id
				WHERE LENGTH(TRIM(CJ.sb_no)) > 0 AND 
					(LENGTH(TRIM(IC.leo_date)) = 0 OR TRIM(IC.leo_date) = 'N.A.' OR IC.ep_copy_print_status = 'N.A.') AND 
					(IC.last_fetched = '0000-00-00 00:00:00' OR IC.last_fetched <= '$fetch_delay')
				ORDER BY IC.child_job_id";
		}
		else  {
			$sql = "SELECT IC.id, IC.child_job_id, CJ.sb_no, DATE_FORMAT(CJ.sb_date, '%Y/%m/%d') AS sb_date, 
				CONCAT(IP.name, ' (', IP.unece_code, ')') AS icegate_port, IC.last_status, IC.status
				FROM child_jobs CJ INNER JOIN jobs J ON CJ.job_id = J.id
					INNER JOIN icegate_sb IC ON CJ.id = IC.child_job_id
					INNER JOIN indian_ports IP ON J.custom_port_id = IP.id
				WHERE IC.child_job_id = $child_job_id";
		}
		$query = $this->db->query($sql);
		$rows = $query->result_array();

		$total_rows = count($rows);
		foreach ($rows as $progress => $job) {

			$data  = array('last_fetched' => date('Y-m-d H:i:s'));

			//////////// Fetch General Details of Shipping Bill
			$this->_body = '';
			$response = $this->_client->post('https://enquiry.icegate.gov.in/enquiryatices/SB_IcesDetails_action', [
				'cookies'     => $this->_sb_cookie,
				'headers' => [
					'Referer' => 'https://enquiry.icegate.gov.in/enquiryatices/SBTrack_Ices_action',
				],
				'form_params' => [
					'SB_NO'            => intval($job['sb_no']),
					'SB_DT'            => $job['sb_date'],
					'sbTrack_location' => $job['icegate_port']
				]
			]);
			if ($response->getStatusCode() != 200) {
				return;
			}

			$this->_body = str_replace(['&nbsp;'], '', $response->getBody()->read(1024*1024*2));

			$found = strpos($this->_body, "An Error Occured, Result Page Not Found! Please try again.");
			if ($found) die('An Error Occured, Result Page Not Found! Please try again.');

			$found = strpos($this->_body, "Your Session is expire. Please try again.");
			if ($found) die('Your Session is expire. Please try again.');

			$found = strpos($this->_body, "No Record found");

			if ($found == false) {

				$table = [];
				$html  = str_get_dom($this->_body);
				foreach($html('tr') as $tr) {
					foreach($tr('th') as $index => $th) {
						$header[$index] = trim($th->getPlainText());
					}
					foreach($tr('td') as $index => $td) {
						$table[$header[$index]] = $td->getPlainText();
					}
				}

				echo "<pre>";
				print_r($table);


				if (count($table) == 15)
					$data += array(
				    	'iec_no'            => trim($table['IEC']),
				    	'cha_no'            => trim($table['CHA No.']),
				    	'job_no'            => trim($table['Job No.']),
				    	'job_date'          => trim($table['Job Date']),
				    	'port_of_discharge' => trim($table['Port of Discharge']),
				    	'total_package'     => trim($table['Total Package']),
				    	'gross_weight'      => trim($table['Gross Weight (Kg)']),
				    	'fob_inr'           => trim($table['FOB(INR)']),
				    	'total_cess'        => trim($table['Total Cess (INR)']),
				    	'drawback'          => trim($table['Drawback']),
				    	'str'               => trim($table['STR']),
				    	'total_dbk_str'     => trim($table['Total (DBK+STR)']),
				    );

				unset($html);
				unset($header);
				unset($table);
			}

			//////////// Fetch Current Status of Shipping Bill
			$this->_body = '';
			$response = $this->_client->post('https://enquiry.icegate.gov.in/enquiryatices/sbCURR_icesTrack_Action', [
				'cookies'     => $this->_sb_cookie,
				'headers' => [
					'Referer' => 'https://enquiry.icegate.gov.in/enquiryatices/SBTrack_Ices_action',
				],
				'form_params' => [
					'SB_NO'            => intval($job['sb_no']),
					'SB_DT'            => $job['sb_date'],
					'sbTrack_location' => $job['icegate_port']
				]
			]);
			if ($response->getStatusCode() != 200) {
				return;
			}

			$this->_body = str_replace(['&nbsp;'], '', $response->getBody()->read(1024*1024*2));

			print_r($this->_body);

			$found = strpos($this->_body, "An Error Occured, Result Page Not Found! Please try again.");
			if ($found) die('An Error Occured, Result Page Not Found! Please try again.');

			$found = strpos($this->_body, "Your Session is expire. Please try again.");
			if ($found) die('Your Session is expire. Please try again.');

			$found = strpos($this->_body, "No Record found");
			if ($found == false) {


				$table = [];
				$html  = str_get_dom($this->_body);
				foreach($html('tr') as $tr) {
					foreach($tr('th') as $index => $th) {
						$header[$index] = trim($th->getPlainText());
					}
					foreach($tr('td') as $index => $td) {
						$table[$header[$index]] = $td->getPlainText();
					}
				}

				echo "<pre>";
				print_r($table);

				


				if (count($table) == 6) {
			    	$data += array(
				    	'current_queue'        		=> trim($table['Current Que']),
				    	'leo_date'             		=> trim($table['LEO Date']),
				    	'ep_copy_print_status' 		=> trim($table['EP Copy Print Status']),
				    	'dbk_scroll_no'        		=> trim($table['DBK Scroll No']),
				    	'dbk_scroll_date'      		=> trim($table['Scroll Date']),
				    	'egm_integration_status'    => trim($table['EGM Integration Status'])
				    );
				}
			    else if (count($table) == 27) {
			        $data += array(
				    	'wharehouse_code'      => trim($table['Warehouse Code']),
				    	'wharehouse_name'      => trim($table['Warehouse Name']),
				    	'current_queue'        => trim($table['Current Que']),
				    	'current_status'       => trim($table['Current Status']),
				    	'appraising_date'      => trim($table['Appraising Date']),
				    	'ac_apr'               => trim($table['A.C(APR)']),
				    	'ac_apr_date'          => trim($table['A.C(APR) Date']),
				    	'exam_mark_id'         => trim($table['Exam Mark ID']),
				    	'mark_date'            => trim($table['Mark Date']),
				    	'insp_eo'              => trim($table['Insp/E.O']),
				    	'exam_date'            => trim($table['EXAM DATE']),
				    	'supdt_ao_id'          => trim($table['Supdt/A.O.Id']),
				    	'dbk_ac_id'            => trim($table['DBK A.C ID']),
				    	'dbk_ac_id_date'       => trim($table['DBK A.C ID Date']),
				    	'dbk_supdt_id'         => trim($table['DBK Supdt. ID']),
				    	'dbk_supdt_date'       => trim($table['DBK Supdt. Date']),
				    	'depd_supdt'           => trim($table['DEPB Supdt']),
				    	'depb_supdt_date'      => trim($table['DEPB Supdt Date']),
				    	'depb_lic'             => trim($table['DEPB Lic']),
				    	'depb_lic_date'        => trim($table['DEPB Lic Date']),
				    	'sample_drawn'         => trim($table['Sample Drawn']),
				    	'test_report'          => trim($table['Test Report']),
				    	'leo_date'             => trim($table['LEO Date']),
				    	'ep_copy_print_status' => trim($table['EP Copy Print Status']),
				    	'print_status'         => trim($table['Print Status']),
				    	'dbk_scroll_no'        => trim($table['DBK Scroll No']),
				    	'dbk_scroll_date'      => trim($table['Scroll Date']),
			  		);
				}

				unset($html);
				unset($header);
				unset($table);
			}


			//////////// Fetch EGM Status of Shipping Bill
			$this->_body = '';
			$response = $this->_client->post('https://enquiry.icegate.gov.in/enquiryatices/sbEGM_icesTrack_Action', [
				'cookies'     => $this->_sb_cookie,
				'headers' => [
					'Referer' => 'https://enquiry.icegate.gov.in/enquiryatices/SBTrack_Ices_action',
				],
				'form_params' => [
					'SB_NO'            => intval($job['sb_no']),
					'SB_DT'            => $job['sb_date'],
					'sbTrack_location' => $job['icegate_port']
				]
			]);
			if ($response->getStatusCode() != 200) {
				return;
			}

			$this->_body = str_replace(['&nbsp;'], '', $response->getBody()->read(1024*1024*2));

			$found = strpos($this->_body, "An Error Occured, Result Page Not Found! Please try again.");
			if ($found) die('An Error Occured, Result Page Not Found! Please try again.');

			$found = strpos($this->_body, "Your Session is expire. Please try again.");
			if ($found) die('Your Session is expire. Please try again.');

			$found = strpos($this->_body, "No Record found");
			if ($found == false) {


				$table = [];
				$html  = str_get_dom($this->_body);
				foreach($html('tr') as $tr) {
					foreach($tr('th') as $index => $th) {
						$header[$index] = trim($th->getPlainText());
					}
					foreach($tr('td') as $index => $td) {
						$table[$header[$index]] = $td->getPlainText();
					}
				}

				echo "<pre>";
				print_r($table);


				if (count($table) == 5)
			    	$data += array(
						'egm_no'        => trim($table['EGM No.']),
						'egm_date'      => trim($table['EGM Date']),
						'container_no'  => trim($table['Container No.']),
						'seal_no'       => trim($table['Seal No.']),
						'error_message' => trim($table['Error Message']),
					);

				unset($html);
				unset($header);
				unset($table);
			}


			//////////// Fetch Drawback Query Details of Shipping Bill
			$this->_body = '';
			$response = $this->_client->post('https://enquiry.icegate.gov.in/enquiryatices/sbDBKqurydtls_Action', [
				'cookies'     => $this->_sb_cookie,
				'headers' => [
					'Referer' => 'https://enquiry.icegate.gov.in/enquiryatices/SBTrack_Ices_action',
				],
				'form_params' => [
					'SB_NO'            => intval($job['sb_no']),
					'SB_DT'            => $job['sb_date'],
					'sbTrack_location' => $job['icegate_port']
				]
			]);
			if ($response->getStatusCode() != 200) {
				return;
			}

			$this->_body = str_replace(['&nbsp;'], '', $response->getBody()->read(1024*1024*2));

			$found = strpos($this->_body, "An Error Occured, Result Page Not Found! Please try again.");
			if ($found) die('An Error Occured, Result Page Not Found! Please try again.');

			$found = strpos($this->_body, "Your Session is expire. Please try again.");
			if ($found) die('Your Session is expire. Please try again.');

			$found = strpos($this->_body, "No Record found");

			// echo "<pre>";
			// print_r($this->_body);
			// exit;

			if ($found == false) {

				$table = [];
				$html  = str_get_dom($this->_body);
				foreach($html('tr') as $tr) {
					foreach($tr('th') as $index => $th) {
						$header[$index] = trim($th->getPlainText());
					}
					foreach($tr('td') as $index => $td) {
						$table[$header[$index]] = $td->getPlainText();
					}
				}

				echo "<pre>";
				print_r($table);
				exit;
				
				if (count($table) == 6)
			    	$data += array(
						'query_no'     => trim($table['Query No.']),
						'query_date'   => trim($table['Query Date']),
						'query_text'   => trim($table['Query Text']),
						'pending_with' => trim($table['Pending With']),
						'officer_name' => trim($table['Officer Name']),
						'reply_date'   => trim($table['Reply Date']),
					);

				unset($html);
				unset($header);
				unset($table);
			}

			//// SAVE DATA TO DATABASE
			if (count($data) > 1) {
		    	$data['last_status'] = $job['status'];
				$this->kaabar->save($this->_table, $data, array('id' => $job['id']));
			}

			$icegate_sb_progress = [
				'progress' => ($progress+1),
				'total'    => $total_rows,
				'percent'  => ceil(($progress+1)/$total_rows * 100) . '%',
			];
			$this->cache->save('icegate_sb_progress', $icegate_sb_progress, 300);
		}

		if (file_exists($this->_fetch_cookie))
		 	unlink($this->_fetch_cookie);

		if ($child_job_id > 0) {
			setSessionAlert('Record Fetched from IceGate', 'success');
			redirect($this->_clspath.$this->_class."/index/$child_job_id");
		}
		else if (! $this->_is_cli) {
			setSessionAlert('Record Fetched from IceGate', 'success');
			echo closeSelfAndRefreshParent();
		}
	}

	function getProgress() {
		header('Content-type: application/json');
		echo json_encode($this->cache->get('icegate_sb_progress'));
	}
}
