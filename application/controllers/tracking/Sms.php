<?php

class Sms extends CI_Controller {
	function __construct() {
		parent::__construct();

		$this->load->library('bhashsms');
	}	

	function index() {
		/*$query = $this->db->query('SELECT * FROM sms_queue WHERE LENGTH(message_id) = 0 AND LENGTH(error_message) = 0 LIMIT 0, 50');
		$rows  = $query->result_array();
		foreach ($rows as $row) {
			$message_id = $this->bhashsms->send('RISHIS', $row['mobile_no'], $row['message']);
			if (substr($message_id, 0, 2) == 'S.') {
				$this->db->update('sms_queue', array('message_id' => $message_id), array('id' => $row['id']));
			}
			else if (strlen($message_id) > 0) {
				$this->db->update('sms_queue', array('error_message' => $message_id), array('id' => $row['id']));
			}
		}*/
	}
}
