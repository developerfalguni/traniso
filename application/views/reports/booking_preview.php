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
	<th>No</th>
	<th>Sub Type</th>
	<th>Job No</th>
	<th>Importer</th>
	<th>Vessel</th>
	<th>Cargo</th>
	<th>POL</th>
	<th>POD</th>
	<th>Containers</th>
	<th>SB Quantity</th>
	<th>FOB INR</th>
</tr>
</thead>

<tbody>
<?php 
$i = 1;
$filter = array(
	'party'      => array(),
);
$total = array(
	'containers' => 0,
	'net_weight' => 0,
	'fob_inr'    => 0,
);
foreach ($rows as $r) {
	$filter['party'][$r['party_name']] = $r['party_name'];

	$total['containers'] += $r['total_containers'];
	$total['net_weight'] += $r['net_weight'];
	$total['fob_inr']    += $r['fob_inr'];

	echo '<tr>
	<td class="aligncenter tiny">' . $i++ . '</td>
	<td class="tiny">' . $r['sub_type'] . '</td>
	<td class="aligncenter big">' . $r['id2_format'] . '</td>
	<td class="tiny">' . $r['party_name'] . '</td>
	<td class="tiny">' . $r['vessel'] . '</td>
	<td class="tiny">' . $r['cargo_name'] . '</td>
	<td class="tiny">' . $r['pol'] . '</td>
	<td class="tiny">' . $r['pod'] . '</td>
	<td class="tiny aligncenter">' . $r['containers'] . '</td>
	<td class="tiny alignright">' . $r['net_weight'] . '</td>
	<td class="tiny alignright">' . inr_format($r['fob_inr']) . '</td>
</tr>';
} 
?>
</tbody>
<tfoot>
	<tr>
		<th class="alignright" colspan="8">Total</th>
		<th class="alignright"><?php echo $total['containers'] ?></th>
		<th class="alignright"><?php echo $total['net_weight'] ?></th>
		<th class="alignright"><?php echo inr_format($total['fob_inr']) ?></th>
	</tr>
</tfoot>
</table>

</body>
</html>