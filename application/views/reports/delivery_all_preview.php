<!DOCTYPE html>
<html lang="en">
<head>
	<title><?php echo $page_title ?></title>
	<style>
		<?php echo file_get_contents(FCPATH.'assets/css/print.css'); ?>
	
		body { font-family: "Times New Roman", serif; }
		.tiny { font-size: 0.7em !important; }
	</style>
</head>

<body>
<h2 class="aligncenter"><?php echo $company['name'] ?></h2>
<h4 class="aligncenter"><?php echo $page_title ?></h4>
<h5 class="aligncenter"><?php echo $page_desc ?></h5>
<table class="details tiny">
<thead>
<tr>
	<th>No</th>
	<th>Party</th>
	<th>Container No</th>
	<th>Size</th>
	<th>BE No</th>
	<th>BL No</th>
	<th>Vehicle No</th>
	<th>Disp. Wt.</th>
	<th>Unloading Location</th>
	<th>Dispatch Type</th>
	<th>Unloading Date</th>
	<th>Fetched</th>
	<th>Location</th>
	<th>CFS In Date</th>
	<th>GatePass No</th>
	<th>GatePass Date</th>
	<th>LR No</th>
	<th>Return Date</th>
	<th>Icegate</th>
</tr>
</thead>

<tbody>
<?php 
$i = 1;
$prev_party_id = 0;
foreach ($rows as $r) {
	if ($prev_party_id == 0) {
		$prev_party_id = $r['party_id'];
	}
	if ($prev_party_id != $r['party_id']) {
		echo '<tr>
			<td colspan="19">&nbsp;</td>
		</tr>';
	}

	echo '<tr id="' . $r['id'] . '">
	<td class="aligncenter">' . $i++ . '</td>
	<td>' . $r['party_name'] . '</td>
	<td>' . $r['number'] . '</td>
	<td class="aligncenter">' . $r['size'] . '</td>
	<td>' . $r['be_no'] . '</td>
	<td>' . $r['bl_no'] . '</td>
	<td>' . $r['vehicle_no'] . '</td>
	<td>' . $r['dispatch_weight'] . '</td>
	<td>' . $r['unloading_location'] . '</td>
	<td>' . $r['dispatch_type'] . '</td>
	<td class="aligncenter nowrap">' . $r['unloading_date'] . '</td>
	<td>' . $r['fetched_from'] . '</td>
	<td>' . $r['location'] . '</td>
	<td class="aligncenter nowrap">' . $r['cfs_in_date'] . '</td>
	<td>' . $r['gatepass_no'] . '</td>
	<td class="aligncenter nowrap">' . $r['gatepass_date'] . '</td>
	<td>' . $r['lr_no'] . '</td>
	<td>' . $r['return_date'] . '</td>
	<td>' . $r['icegate_status'] . '</td>
</tr>';
	
	$prev_party_id = $r['party_id'];
} 
?>
</tbody>
</table>

</body>
</html>