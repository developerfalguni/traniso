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
		echo '<td width="75%"><img src="data:image/png;base64,' . base64_encode($image_data) . '" height="100px" /></td>';
		echo '<td class="alignright" valign="top" nowrap="true"><span class="tiny">' . $company['address'] . '<br />' . (isset($city['name']) ? $city['name'] . '-' . $city['pincode'] . ' ' . $state['name'] . '<br />' : '') . $company['contact'] . '<br />' . $company['email'] . '</span></td>';
	}
	else {
		echo '<td><h2>' . $company['name'] . '</h2></td>
	<td class="alignright" valign="top" nowrap="true"><span class="tiny">' . $company['address'] . '<br />' . (isset($city['name']) ? $city['name'] . '-' . $city['pincode'] . ' ' . $state['name'] . '<br />' : '') . $company['contact'] . '<br />' . $company['email'] . '</span></td>';
	}
	?>
</tr>
</table>
<br />
<h4><?php echo $page_title ?></h4>

<table class="details">
<thead>
<tr>
	<th rowspan="2">No</th>
	<th rowspan="2">Sub Type</th>
	<th rowspan="2">Job No</th>
	<th rowspan="2">Importer</th>
	<th rowspan="2">Vessel</th>
	<th rowspan="2">Cargo</th>
	<th rowspan="2">POL</th>
	<th rowspan="2">POD</th>
	<th rowspan="2">BL No &amp; Date</th>
	<th rowspan="2">SB No &amp; Date</th>
	<th colspan="2" class="orange">Planned</th>
	<th colspan="2" class="green">Stuffing</th>
	<th rowspan="2">SB Quantity</th>
	<th rowspan="2">FOB INR</th>
</tr>

<tr>
	<th class="orange">C.20</th>
	<th class="orange">C.40</th>
	<th class="green">C.20</th>
	<th class="green">C.40</th>
</tr>
</thead>

<tbody>
<?php 
$i = 1;
$total = array(
	'planned_20'   => 0,
	'planned_40'   => 0,
	'container_20' => 0,
	'container_40' => 0,
	'net_weight'   => 0,
	'fob_inr'      => 0,
);
foreach ($rows as $r) {
	$filter['party'][$r['party_name']] = $r['party_name'];

	$total['planned_20']   += $r['planned_20'];
	$total['planned_40']   += $r['planned_40'];
	$total['container_20'] += $r['container_20'];
	$total['container_40'] += $r['container_40'];
	$total['net_weight']   += $r['net_weight'];
	$total['fob_inr']      += $r['fob_inr'];

	echo '<tr>
	<td class="aligncenter tiny">' . $i++ . '</td>
	<td class="tiny">' . $r['sub_type'] . '</td>
	<td class="aligncenter big">' . $r['id2_format'] . '</td>
	<td class="tiny">' . $r['party_name'] . '</td>
	<td class="tiny">' . $r['vessel'] . '</td>
	<td class="tiny">' . $r['cargo_name'] . '</td>
	<td class="tiny">' . $r['pol'] . '</td>
	<td class="tiny">' . $r['pod'] . '</td>
	<td class="tiny nowrap">' . str_replace(', ', '<br />', $r['bl_no_date']) . '</td>
	<td class="tiny nowrap">' . str_replace(', ', '<br />', $r['sb_no_date']) . '</td>
	<td class="big aligncenter orange">' . $r['planned_20'] . '</td>
	<td class="big aligncenter orange">' . $r['planned_40'] . '</td>
	<td class="big aligncenter green">' . $r['container_20'] . '</td>
	<td class="big aligncenter green">' . $r['container_40'] . '</td>
	<td class="tiny alignright">' . $r['net_weight'] . '</td>
	<td class="tiny alignright">' . inr_format($r['fob_inr']) . '</td>
</tr>';
} 
?>
</tbody>
<tfoot>
	<tr>
		<th class="alignright" colspan="10">Total</th>
		<th class="alignright"><?php echo $total['planned_20']; ?></th>
		<th class="alignright"><?php echo $total['planned_40']; ?></th>
		<th class="alignright"><?php echo $total['container_20']; ?></th>
		<th class="alignright"><?php echo $total['container_40']; ?></th>
		<th class="alignright"><?php echo $total['net_weight'] ?></th>
		<th class="alignright"><?php echo inr_format($total['fob_inr']) ?></th>
	</tr>
</tfoot>
</table>

</body>
</html>