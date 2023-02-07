<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

function getDaysInBetween($sDate, $eDate, $format = 'Y-m-d', $default_value = null) {
	$day      = 86400; // Day in seconds
	//$format = 'Y-m-d'; // Output format (see PHP date funciton)
	$sTime    = strtotime($sDate); // Start as time
	$eTime    = strtotime($eDate); // End as time
	$numDays  = round(($eTime - $sTime) / $day) + 1;
	$days     = [];
	
	// Get days
	for ($d = 0; $d < $numDays; $d++) {
		$days[date($format, ($sTime + ($d * $day)))] = $default_value;
	} 
	
	// Return days
	return $days;
}

function getMonthsInBetween($date1, $date2, $format = 'Y-m', $display_format = 'M-Y') {
	$date1 = date('Y-m', strtotime($date1));
	$date2 = date('Y-m', strtotime($date2));

	if($date1 < $date2) {
		$past = $date1;
		$future = $date2;
	}
	else {
		$past = $date2;
		$future = $date1;
	}

	$months = array();
	for($i = $past; $past <= $future; $i++) {
		$timestamp = strtotime($past.'-01');
		$months[date($format, $timestamp)] = date($display_format, $timestamp);
		$past = date('Y-m', strtotime('+1 month', $timestamp));
	}

	return $months;
}

/*
function getMonthsInBetween($date1, $date2) {
	$months   = array();
	$start    = new DateTime($date1);
	$start->modify('first day of this month');
	$end      = new DateTime($date2);
	$end->modify('first day of this month');
	$interval = DateInterval::createFromDateString('1 month');
	$period   = new DatePeriod($start, $interval, $end);

	foreach ($period as $dt) {
	    $months[$dt->format("Y-m")] = $dt->format("Y-m");
	}
}
*/

function _convDate($date) {
	if (is_string($date) && (strlen($date) == 10 OR strlen($date) == 19)) {
		if (preg_match("/([0-9]{4})-([0-9]{1,2})-([0-9]{1,2}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})/", $date, $regs)) 
			return "$regs[3]-$regs[2]-$regs[1]";
		else if (preg_match("/([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})/", $date, $regs))
			return "$regs[3]-$regs[2]-$regs[1]";
	}
	return $date;
}

function secondsDiff($sDate, $eDate) {
	$sTime = strtotime($sDate); // Start as time
	$eTime = strtotime($eDate); // End as time
	return $eTime - $sTime;
}

function daysDiff($sDate, $eDate) {
	$day = 86400; // Day in seconds
	$sTime = strtotime($sDate); // Start as time
	$eTime = strtotime($eDate); // End as time
	return round(($eTime - $sTime) / $day) + 1;
}

function moment($timestamp) {
	$result = 'Invalid Time';
	if ($timestamp == '0000-00-00 00:00:00') {
		$result = 'No Time';
	}
	else {
		$lapsed = secondsDiff($timestamp, date('Y-m-d H:i:s'));
		$result = $lapsed . ' Sec ago';
		if ($lapsed > 60) {
			$lapsed = round($lapsed / 60, 0);
			$result = $lapsed . ' Min ago';
			if ($lapsed > 60) {
				$lapsed = round($lapsed / 60, 0);
				$result = $lapsed . ' Hrs ago';
				if ($lapsed > 24) {
					$lapsed = round($lapsed / 24, 0);
					$result = $lapsed . ' Days ago';
					if ($lapsed > 365) {
						$lapsed = round($lapsed / 365, 0);
						$result = $lapsed . ' Yrs ago';
					}
				}
			}
		}
	}
	return $result;
}