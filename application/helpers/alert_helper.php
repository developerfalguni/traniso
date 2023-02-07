<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

// class: Error, OK, Warning
// Note: Only One Error, One Warning and One OK message can be show Simultaneously

function setSessionError($message) {
	if (strlen(trim($message)) == 0) return;

	$CI =& get_instance();
	
	$data['alert'] = $CI->session->userdata("alert");
	$data['alert']['error'] = $message;
	if (! $data['alert']['error']) {
		$data['alert']['error'] = explode("\n", trim($message));
	}
	$CI->session->set_userdata($data);
}

function setSessionAlert($message, $class) {
	if (strlen(trim($message)) == 0) return;

	$CI =& get_instance();

	$data['alert'] = $CI->session->userdata("alert");
	$data['alert'][$class] = $message;
	if (! $data['alert'][$class]) {
		$data['alert'][$class] = explode("\n\r", trim($message));
	}
	$CI->session->set_userdata($data);
}

function showSessionAlerts() {
	$CI =& get_instance();
	$error_msg = FALSE;
	$alert = $CI->session->userdata('alert');

	if($alert) {
		foreach($alert as $class => $message) {
			if (is_array($message)) {
				$error_msg .= '<div class="alert ' . $class . '"><button class="close" data-dismiss="alert">×</button>';
				foreach($message as $msg)
					$error_msg .= "$msg<br />";
				$error_msg .= "</div>";
			}
			else 
				$error_msg .= '<div class="alert ' . $class . '"><button class="close" data-dismiss="alert">×</button>' . $message . '</div>';
		}
		$CI->session->unset_userdata('alert');
	}
	return $error_msg;
}

function showSessionNotification($position = '.top-right') {
	$CI =& get_instance();
	$error_msg = FALSE;
	$alert = $CI->session->userdata('alert');

	if($alert) {
		foreach($alert as $class => $message) {
			if (is_array($message)) {
				foreach($message as $msg)
					$error_msg .= "$('$position').notify({message: {html: '$msg'}, type: '" . str_replace('alert-', '', $class) . "', fadeOut: {enabled: true, delay: 10000}}).removeClass('hide');\n";
			}
			else 
				$error_msg .= "$('$position').notify({message: {html: '$message'}, type: '" . str_replace('alert-', '', $class) . "', fadeOut: {enabled: true, delay: 10000}}).removeClass('hide');\n";
		}
		$CI->session->unset_userdata('alert');
	}
	return $error_msg;
}
