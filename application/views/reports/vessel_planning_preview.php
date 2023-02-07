<!DOCTYPE html>
<html lang="en">
<head>
	<title><?php echo $page_title ?></title>

<style><?php echo file_get_contents(FCPATH.'assets/css/print.css'); ?></style>
</head>

<body>
<h3 class="aligncenter"><?php echo $page_title ?></h3>

<?php
foreach ($rows as $v) {
	$i = 1;
	$total = array(
		'c20' => 0,
		'c40' => 0,
		'g20' => 0,
		'g40' => 0,
		'd20' => 0,
		'd40' => 0,
		'v20' => 0,
		'v40' => 0,
	);
	$filter['vessel'][$v['vessel_name']] = 1;
	$filter['port'][$v['port_name']]     = 1;

	echo '<table class="header big">
<tr>
	<td width="50%" colspan="2"><span class="box_label">Vessel Name</span><br />' . $v['vessel_name'] . '</td>
	<td width="50%" colspan="2"><span class="box_label">Port Name</span><br />' . $v['port_name'] . '</td>
</tr>

<tr>
	<td width="25%"><span class="box_label">ETA</span><br />' . $v['eta_date'] . '</td>
	<td width="25%"><span class="box_label">ETD</span><br />' . $v['etd_date'] . '</td>
	<td width="25%"><span class="box_label">Doc Cutoff</span><br />' . $v['doc_cutoff_date'] . '</td>
	<td width="25%"><span class="box_label">Gate Cutoff</span><br />' . $v['gate_cutoff_date'] . '</td>
</tr>
</table>

<table class="details">
<tr>
	<th width="24px" rowspan="2">No</th>
	<th width="60px" rowspan="2">Line</th>
	<th width="120px nowrap" rowspan="2">Booking</th>
	<th width="80px" rowspan="2">Shipper</th>
	<th rowspan="2">FPD</th>
	<th colspan="2">Containers</th>
	<th colspan="2">Gate In</th>
	<th colspan="2">Doc</th>
	<th width="40px" rowspan="2">SI</th>
	<th colspan="2">OnBoard</th>
</tr>

<tr>
	<th width="40px">C.20</th>
	<th width="40px">C.40</th>
	<th width="40px">C.20</th>
	<th width="40px">C.40</th>
	<th width="40px">C.20</th>
	<th width="40px">C.40</th>
	<th width="40px">C.20</th>
	<th width="40px">C.40</th>
</tr>
</thead>

<tbody>
';
	foreach ($v['jobs'] as $j) {
		$total['c20'] += $j['c20'];
		$total['c40'] += $j['c40'];
		$total['g20'] += $j['g20'];
		$total['g40'] += $j['g40'];
		$total['d20'] += $j['d20'];
		$total['d40'] += $j['d40'];
		$total['v20'] += $j['v20'];
		$total['v40'] += $j['v40'];

		echo '<tr>
	<td class="aligncenter">' . $i++ . '</td>
	<td>' . $j['line'] . '</td>
	<td>' . $j['booking_no'] . '</td>
	<td>' . $j['shipper'] . '</td>
	<td>' . $j['fpod'] . '</td>
	<td class="aligncenter">' . $j['c20'] . '</td>
	<td class="aligncenter">' . $j['c40'] . '</td>
	<td class="aligncenter">' . $j['g20'] . '</td>
	<td class="aligncenter">' . $j['g40'] . '</td>
	<td class="aligncenter">' . $j['d20'] . '</td>
	<td class="aligncenter">' . $j['d40'] . '</td>
	<td class="aligncenter ' . ($j['si_submitted'] == 'Yes' ? 'green' : 'red') . '">'. $j['si_submitted'] . '</td>
	<td class="aligncenter">' . $j['v20'] . '</td>
	<td class="aligncenter">' . $j['v40'] . '</td>
</tr>';
	}
	echo '</tbody>
	<tfoot>
	<tr>
		<th class="alignright" colspan="5">Total</th>
		<th class="aligncenter">' . $total['c20'] . '</th>
		<th class="aligncenter">' . $total['c40'] . '</th>
		<th class="aligncenter">' . $total['g20'] . '</th>
		<th class="aligncenter">' . $total['g40'] . '</th>
		<th class="aligncenter">' . $total['d20'] . '</th>
		<th class="aligncenter">' . $total['d40'] . '</th>
		<th></th>
		<th class="aligncenter">' . $total['v20'] . '</th>
		<th class="aligncenter">' . $total['v40'] . '</th>
	</tr>
	</tfoot>
	</table>
	<br />
	<br />';
} 
?>

</body>
</html>