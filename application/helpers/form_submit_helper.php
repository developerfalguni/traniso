<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

function checkDuplicateFormSubmit($url = 'main') {
	$CI =& get_instance();
	
	$form_id = md5(serialize($_POST));
	$previous_id = $CI->session->userdata('form_id');

	if (ENVIRONMENT == 'production' AND strcmp($form_id, $previous_id) == 0) {
		setSessionAlert('There is Nothing to Update...', 'warning');
		redirect($url);
	}
	else {
		$CI->session->set_userdata('form_id', $form_id);
	}
}

/* End: form_pi.php */
