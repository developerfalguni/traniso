<!DOCTYPE html>
<html lang="en">
<head>
	<title><?php echo $page_title ?></title>

	<style>
		<?php echo file_get_contents(FCPATH.'assets/css/print.css'); ?>
	</style>
</head>

<body>
<h3 class="aligncenter">Pickup Program</h3>

<table class="details">
<thead>
<tr>
	<th>No</th>
	<th>Job No</th>
	<th>Party</th>
	<th>Party Ref</th>
	<th>Stuffing</th>
	<th>Cargo</th>
	<th>Unit</th>
	<th>Containers</th>
	<th>Gross Weight</th>
	<th>POL</th>
	<th>FPD</th>
	<th>Line</th>
	<th>Pickup Location</th>
	<th>Pickup Date</th>
	<th>Stuffing Date</th>
	<th>Targeted Vessel</th>
	<th>ETA</th>
	<th>Cutoff</th>
	<th>Booking No</th>
	<th>Gate Out</th>
</tr>
</thead>

<tbody>
<?php 
$i = 1;
$total = array();
foreach ($rows as $pickup_id => $r) {
	if (isset($total[$r['size']]))
		$total[$r['size']] += $r['containers'];
	else
		$total[$r['size']] = $r['containers'];

	echo '<tr>
	<td class="aligncenter tiny">' . $i++ . '</td>
	<td class="aligncenter tiny nowrap">' . $r['id2_format'] . '</td>
	<td class="tiny">' . $r['shipper_name'] . '</td>
	<td class="tiny">' . $r['party_ref'] . '</td>
	<td class="tiny">' . $r['stuffing_location'] . '</td>
	<td class="tiny">' . $r['cargo_name'] . '</td>
	<td class="tiny">' . $r['unit_code'] . '</td>
	<td class="tiny aligncenter">' . $r['containers'] . ' x ' . $r['size'] . '</td>
	<td class="tiny">' . $r['gross_weight'] . '</td>
	<td class="tiny">' . character_limiter($r['port_of_loading'], 10) . '</td>
	<td class="tiny">' . $r['fpod'] . '</td>
	<td class="tiny">' . character_limiter($r['line_name'], 10) . '</td>
	<td class="tiny">' . $r['pickup_location'] . '</td>
	<td class="tiny">' . $r['pickup_date'] . '</td>
	<td class="tiny">' . $r['stuffing_date'] . '</td>
	<td class="tiny">' . $r['vessel_name'] . '</td>
	<td class="tiny">' . $r['eta_date'] . '</td>
	<td class="tiny">' . $r['gate_cutoff_date'] . '</td>
	<td class="tiny">' . $r['booking_no'] . '</td>
	<td class="tiny aligncenter">' . $r['gate_out'] . '</td>
</tr>';
}
?>
</tbody>
</table>
<br />

<p>Total Containers: 
<?php foreach ($total as $size => $count) {
	echo ' (' . $count . ' x ' . $size . ') ';
}
?>
</p>
</body>
</html>