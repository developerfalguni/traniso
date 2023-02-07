<?php

use Wangoviridans\Ganon;

class Traces extends MY_Controller {
	var $_fetch_cookie;

	function __construct() {
		parent::__construct();

		$this->_fetch_cookie   = FCPATH . 'tmp/' . $this->_class . '_cookie';

		$this->load->library('Zebra_cURL');
	}
	
	function _search_array($needle, $array) {
		foreach ($array as $key => $value)
        	if (stripos($value, $needle) !== false)
	            return $key;
        return false;
	}

	function index() {
		echo 'Fetched';
		// if (intval($row_id) == 0) {
		// 	echo closeWindow();
		// 	return;
		// }

		// $data['page']        = $this->_clspath.$this->_class;
		// $data['page_title']  = "Traces";
		// $data['hide_title']  = true;
		// $data['hide_menu']   = true;
		// $data['hide_footer'] = true;
		// $this->load->view('index', $data);
	}

	function captcha($table = 'parties', $row_id = 0) {
		$this->load->helper('file');
		
		if ($this->input->post('j_captcha')) {
			$content    = read_file($this->_fetch_cookie);
			$start      = strpos($content, "JSESSIONID");
			$start     += 11;
			$jsessionid = substr($content, $start, 37);

			$post = array(
				'username'   => $this->input->post('username'),   // SWAYAM001
				'j_username' => $this->input->post('username') . '^' . $this->input->post('j_tanPan'),
				'j_password' => $this->input->post('j_password'), // Tejash1234
				'j_tanPan'   => $this->input->post('j_tanPan'),   // RKTS03760B
				'j_captcha'  => $this->input->post('j_captcha')
			);
			$this->zebra_curl->cookies($this->_fetch_cookie, TRUE);
			$this->zebra_curl->option(array(
				CURLOPT_USERAGENT  => "Mozilla/5.0 (X11; Linux x86_64; rv:48.0) Gecko/20100101 Firefox/48.0",
				CURLOPT_REFERER    => "https://www.tdscpc.gov.in/app/login.xhtml"
			));
			$this->zebra_curl->post("https://www.tdscpc.gov.in/app/j_security_check", 
				$post,
				function($result) {
					if (! $result->response[1] == CURLE_OK)
						die('An error occured: ' . $result->response[0]);
				}
			);

			$this->_body = '';
			$this->zebra_curl->option(array(
				CURLOPT_USERAGENT => "Mozilla/5.0 (X11; Linux x86_64; rv:48.0) Gecko/20100101 Firefox/48.0",
				CURLOPT_REFERER   => "https://www.tdscpc.gov.in/app",
			));
			$this->zebra_curl->get("https://www.tdscpc.gov.in/app/ded/panverify.xhtml", 
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
				if ($input->getAttribute('name') == 'javax.faces.ViewState') {
					$javax_faces_ViewState = $input->getAttribute('value');
					break;
				}
			}
			unset($inputs);
			$this->session->set_userdata('javax_faces_ViewState', $javax_faces_ViewState);
			unlink(FCPATH . "tmp/$jsessionid.png");
			echo closeWindow();
			return;
		}

		if (file_exists($this->_fetch_cookie))
		 	unlink($this->_fetch_cookie);

		$this->zebra_curl->cookies($this->_fetch_cookie, TRUE); 
		$this->zebra_curl->option(array(
			CURLOPT_USERAGENT => "Mozilla/5.0 (X11; Linux x86_64; rv:48.0) Gecko/20100101 Firefox/48.0",
			CURLOPT_REFERER   => "https://www.tdscpc.gov.in/app/login.xhtml"
		));
		$this->zebra_curl->download("https://www.tdscpc.gov.in/app/srv/GetCaptchaImg", 
			FCPATH . "tmp", null);
		$content    = read_file($this->_fetch_cookie);
		$start      = strpos($content, "JSESSIONID");
		$start     += 11;
		$jsessionid = substr($content, $start, 37);
		rename(FCPATH . "tmp/GetCaptchaImg", FCPATH . "tmp/$jsessionid.png");
		
		$data['image']       = "$jsessionid.png";
		$data['page_title']  = 'Traces Captcha';
		$data['page']        = $this->_clspath.'traces_captcha';
		$this->load->view('plain', $data);
	}

	function track($table = 'parties', $row_id = 0) {
		ini_set('max_execution_time', '0');
		ini_set('memory_limit', '512M');

		$this->load->library('simple_html_dom');

		if ($row_id == 0) {
			$sql = "SELECT T.id, T.pan_no FROM $table T 
				WHERE LENGTH(TRIM(T.pan_no)) > 0 AND LENGTH(TRIM(T.traces_name)) = 0";
		}
		else  {
			$sql = "SELECT T.id, T.pan_no FROM $table T 
				WHERE T.id = $row_id AND LENGTH(TRIM(T.pan_no)) > 0 AND LENGTH(TRIM(T.traces_name)) = 0";
		}
		$query = $this->db->query($sql);
		$rows  = $query->result_array();

		$javax_faces_ViewState  = $this->session->userdata('javax_faces_ViewState');
		foreach ($rows as $r) {
			$data = array();

			$post['clickGo1']               = 'Go';
			$post['frmType1']               = '24Q';
			$post['pannumber']              = $r['pan_no'];
			$post['pandetailsForm1_SUBMIT'] = 1;
			$post['javax.faces.ViewState']  = $javax_faces_ViewState;
			$this->_body = '';
			$this->zebra_curl->cookies($this->_fetch_cookie, TRUE);
			$this->zebra_curl->option(array(
				CURLOPT_REFERER => "https://www.tdscpc.gov.in/app/ded/panverify.xhtml",
			));
			$this->zebra_curl->post("https://www.tdscpc.gov.in/app/ded/panverify.xhtml", 
				$post, 
				function($result) {
					if ($result->response[1] == CURLE_OK)
						$this->_body = $result->body;
					else
						die('An error occured: ' . $result->response[0]);
				}
			);

			$found = strpos($this->_body, "PAN Details");
			if ($found) {
				$html = str_get_dom($this->_body);

				$inputs = $html('input');
				foreach($inputs as $input) {
					if ($input->getAttribute('name') == 'javax.faces.ViewState') {
						$javax_faces_ViewState = $input->getAttribute('value');
						break;
					}
				}
				unset($inputs);

				foreach ($html('table') as $table_index => $html_table) {
					if ($table_index == 2) {
						foreach($html_table('td') as $element_index => $element) {
							if ($element_index == 1) {
								$data['traces_name'] = trim($element->getPlainText());
								break;
							}
						}
					}

					$html_table->clear();
					unset($html_table);
				    unset($this->_body);
				}
			}

		    if (count($data) == 1) {
				$this->kaabar->save($table, $data, array('id' => $r['id']));
			}
		}
		$this->session->set_userdata('javax_faces_ViewState', $javax_faces_ViewState);

		// if (file_exists($this->_fetch_cookie))
		//  	unlink($this->_fetch_cookie);

		setSessionAlert('Record Fetched from Traces', 'success');
		echo closeSelfAndRefreshParent();
	}
}
