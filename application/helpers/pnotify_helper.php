<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

// class: Error, OK, Warning
// Note: Only One Error, One Warning and One OK message can be show Simultaneously

function setSessionError($message) {
	if (strlen(trim($message)) == 0) return;

	$CI =& get_instance();
	
	$data['alert'] = $CI->session->userdata("alert");
	$data['alert']['error'] = trim($message);
	if (! $data['alert']['error']) {
		$data['alert']['error'] = explode("\n", trim($message));
	}
	$CI->session->set_userdata($data);
}

function setSessionAlert($message, $class) {
	if (strlen(trim($message)) == 0) return;

	$CI =& get_instance();

	$data['alert'] = $CI->session->userdata("alert");
	$data['alert'][$class] = trim($message);
	if (! $data['alert'][$class]) {
		$data['alert'][$class] = explode("\n", trim($message));
	}
	$CI->session->set_userdata($data);
}
