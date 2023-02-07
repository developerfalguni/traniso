<!DOCTYPE html>
<html lang="en">
<head>
	<title><?php echo $page_title ?></title>
	<style>
		<?php 
			echo file_get_contents(FCPATH.'assets/css/print.css'); 
			$image_data = file_get_contents(FCPATH.'php_uploads/'.$company['logo']);
		?>
		
		body { font-family: "Times New Roman", serif; }
		.Particular { font-size: 0.9em; }
	</style>
</head>

<body>
<table width="100%">
<tr>
	<?php 
	if ((strlen($company['logo'])) > 0) {
		if ($company['id'] == 2)
			echo '<td><img src="data:image/png;base64,' . base64_encode($image_data) . '" height="80px" /></td>';
		else {
			echo '<td width="75%"><img src="data:image/png;base64,' . base64_encode($image_data) . '" height="100px" /></td>';
			echo '<td class="alignright" valign="top" nowrap="true"><span class="tiny">' . $company['address'] . '<br />' . (isset($city['name']) ? $city['name'] . '-' . $city['pincode'] . ' ' . $state['name'] . '<br />' : '') . $company['contact'] . '<br />' . $company['email'] . '</span></td>';
		}
	}
	else {
		echo '<td><h2>' . $company['name'] . '</h2></td>
	<td class="alignright" valign="top" nowrap="true"><span class="tiny">' . $company['address'] . '<br />' . (isset($city['name']) ? $city['name'] . '-' . $city['pincode'] . ' ' . $state['name'] . '<br />' : '') . $company['contact'] . '<br />' . $company['email'] . '</span></td>';
	}
	?>
</tr>
</table>

<h3 class="aligncenter"><?php echo $page_title ?></h3>

<table class="header">
<tr>
	<td width="40%" rowspan="2" colspan="2"><span class="box_label">Importer</span><br />
		<?php echo $party['name'] ?><br />
		<?php echo $party['address'] ?></td>
	<td width="30%"><span class="box_label">Job No</span><br /><?php echo $job['id2_format'] ?></td>
	<td width="15%"><span class="box_label">Date</span><br /><?php echo $job['date'] ?></td>
	<td width="15%"><span class="box_label">Type</span><br /><?php echo $job['cargo_type'] ?></td>
</tr>

<tr>
	<td><span class="box_label">Port of Discharge</span><br /><?php echo $discharge_port ?></td>
	<td colspan="2"><span class="box_label">Shipment Port</span><br /><?php echo $shipment_port ?></td>
</tr>

<tr>
	<td colspan="2"><span class="box_label">Line Name</span><br /><?php echo $line_name ?></td>
	<td colspan="3"><span class="box_label">Vessel Name</span><br /><?php echo (isset($vessel['name']) ? $vessel['name'] . ' - ' . $vessel['voyage_no'] : '&nbsp;') ?></td>
</tr>

<tr>
	<td colspan="2"><span class="box_label">CFS Name</span><br /><?php echo $cfs_name; ?></td>
	<td><span class="box_label">B/E No &amp; Date</span><br /><?php echo $job['be_no'] . ' / ' . $job['be_date'] ?></td>
	<td colspan="2"><span class="box_label">B/L No &amp; Date</span><br /><?php if (strlen(trim($job['bl_no'])) > 0) echo $job['bl_no'] . ' / ' . $job['bl_date']; ?></td>
</tr>

<tr>
	<td colspan="2"><span class="box_label">Description of Goods</span><br /><?php echo $job['details'] ?></td>
	<td><span class="box_label"><?php echo $job['net_weight_unit'] ?></span><br /><?php echo $job['net_weight'] ?></td>
	<?php 
	if($job['cargo_type'] == 'Container') {
		echo '<td width="15%"><span class="box_label">' . $package_type . '</span><br />' . ($job['packages'] == 0 ? '' : $job['packages']) . '</td>
		<td width="15%"><span class="box_label">Containers</span><br />' . ($job['container_20'] > 0 ? '(' . $job['container_20'] . 'x20) ' : NULL) . ($job['container_40'] > 0 ? ' (' . $job['container_40'] . 'x40)' : NULL) . '</td>';
	}
	else {
		echo '<td colspan="2"><span class="box_label">' . $package_type . '</span><br />' . ($job['packages'] == 0 ? '' : $job['packages']) . '</td>';
	} ?>
</tr>
</table>

<h3 class="aligncenter">Weightment Details</h3>
<table class="details">
<thead>
	<tr>
		<th width="100px">Sr No</th>
		<th>Container No</th>
		<th width="100px">Received Weight</th>
	</tr>
</thead>

<tbody>
<?php
	$total = array(
		'net_weight'        => 0,
		'dispatched_weight' => 0,
	);
	$i = 1;
	foreach ($containers as $r) {
		$total['net_weight'] = bcadd($total['net_weight'], $r['net_weight'], 2);

		echo '<tr>
		<td>' . $i++ . '</td>
		<td>' . $r['number'] . '</td>
		<td class="alignright">' . $r['net_weight'] . '</td>
		</tr>';
	}
?>
</tbody>

<tfoot>
<tr>
	<td class="alignright" colspan="2">Total</td>
	<td class="alignright"><?php echo $total['net_weight'] ?></td>
</tr>

<tr>
	<td class="alignright" colspan="2">Difference (BL - Rcvd)</td>
	<td class="alignright"><?php echo ($job['net_weight'] - $total['net_weight']) ?></td>
</tr>
</tfoot>
</table>

<h3 class="aligncenter">Delivery Details</h3>
<table class="details">
<thead>
	<tr>
		<th width="100px">Date</th>
		<th>Container No</th>
		<th>Vehicle No</th>
		<th width="100px">Dispatched Weight</th>
	</tr>
</thead>

<tbody>
<?php
	foreach ($rows as $r) {
		if ($r['dispatch_weight'] > 0) {
			$total['dispatch_weight'] = bcadd($total['dispatch_weight'], $r['dispatch_weight'], 2);

			echo '<tr>
		<td>' . $r['gatepass_date'] . '</td>
		<td>' . $r['number'] . '</td>
		<td>' . $r['vehicle_no'] . '</td>
		<td class="alignright">' . $r['dispatch_weight'] . '</td>
		</tr>';
		}
	}
?>
</tbody>

<tfoot>
<tr>
	<td class="alignright" colspan="3">Total</td>
	<td class="alignright"><?php echo $total['dispatch_weight'] ?></td>
</tr>

<tr>
	<td class="alignright" colspan="3">Difference (Rcvd - Dispatch)</td>
	<td class="alignright"><?php echo ($total['net_weight'] - $total['dispatch_weight']) ?></td>
</tr>
</tfoot>
</table>

</body>
</html>