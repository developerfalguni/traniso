<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

function getFileList($path, $types = array()) {
	$CI =& get_instance();
	$CI->load->helper('file');
	
	$files 		= array();
	$filenames  = get_filenames($path, TRUE);

	if (is_array($filenames)) {
		natsort($filenames);
		foreach ($filenames as $f) {
			$type = pathinfo($f, PATHINFO_EXTENSION);
			if (empty($types) OR in_array(strtolower($type), $types)) {
				$md5 = md5($f);
				$files[$md5] = get_file_info($f);
				$files[$md5]['type'] = $type;
				if ($files[$md5]['size'] > 1024)
					$files[$md5]['size'] = round(($files[$md5]['size'] / 1024) / 1024, 2) . ' MB';
			}
		}
	}

	$date = array();
	foreach ($files as $key => $file) {
	    $date[$key] = $file['date'];
	}
	array_multisort($date, SORT_DESC, SORT_NUMERIC, $files);

	return $files;
}