<?php

class Msc extends MY_Controller {
	function __construct() {
		parent::__construct();
	}
	
	function index($job_id) {
		
	}

	function fetch($job_id = 0) {
		ini_set('max_execution_time', '0');
		ini_set('memory_limit', '256M');

		if ($job_id == 0) {
			$sql = "SELECT J.bl_no FROM jobs J WHERE LENGTH(TRIM(J.bl_no)) > 0";
		}
		else  {
			$sql = "SELECT J.bl_no FROM jobs J WHERE J.id = $job_id";
		}

		$query = $this->db->query($sql);
		$rows = $query->result_array();

		$this->load->library('simple_html_dom');

		foreach ($rows as $job) {
			$e = base64_encode($job['bl_no'] . '|CT|');

			exec('curl -s ' . 
				'--user-agent "Mozilla/5.0 (X11; Linux x86_64; rv:18.0) Gecko/20100101 Firefox/18.0" ' . 
				'-H "X-Requested-With: XMLHttpRequest" ' . 
				'--referer http://tracking.mscgva.ch/msctracking.php ' . 
				'http://tracking.mscgva.ch/MSCTrackingData.php?e='.$e, $msc_bl);

			if (strpos($msc_bl[0], "The number you entered is not valid.") == false) {
				$html = str_get_html(join('', $msc_bl));
				$divs = $html->find('div');
				foreach($divs as $div) {
					if (strlen($div->href) > 0) {
						ChromePhp::info($div->href);
						exec('curl -s ' . 
							'--user-agent "Mozilla/5.0 (X11; Linux x86_64; rv:18.0) Gecko/20100101 Firefox/18.0" ' . 
							'-H "X-Requested-With: XMLHttpRequest" ' . 
							'--referer http://tracking.mscgva.ch/msctracking.php ' . 
							'http://tracking.mscgva.ch/'.$div->href, $msc_ct);
						ChromePhp::info($msc_ct);
					}
				}
			}
			else 
				ChromePhp::info("The number you entered is not valid.");


	/*		exec('curl -s ' . 
				'--user-agent "Mozilla/5.0 (X11; Linux x86_64; rv:18.0) Gecko/20100101 Firefox/18.0" ' . 
				'-H "X-Requested-With: XMLHttpRequest" ' . 
				'--referer http://tracking.mscgva.ch/msctracking.php ' . 
				'--data "e=Q1R8TVNDVUVDNDg1MzE0fE1FRFUxNzEzNTUwfA==" ' . 
				'http://tracking.mscgva.ch/MSCTrackingDetails.php', $msc_ct);
	*/
		}
	}
}
