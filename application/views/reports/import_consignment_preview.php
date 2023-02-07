<html>
<head>
	<title><?php echo $page_title ?></title>
	<style>
		<?php echo file_get_contents(FCPATH.'assets/css/print.css'); ?>
	</style>
</head>

<body>

<h4><?php echo $page_title ?></h4>
<table class="details">
<thead>
<tr>
	<th>No</th>
	<th>Job No</th>
	<th>Type</th>
	<th>Importer</th>
	<th>Vessel</th>
	<th>Cargo</th>
	<th>POL</th>
	<th>POD</th>
	<th>Line</th>
	<th>BL No</th>
	<th>BL Date</th>
	<th>BE No</th>
	<th>BE Date</th>
	<th>C.20</th>
	<th>C.40</th>
	<th>BE Quantity</th>
	<th>Custom Duty</th>
</tr>
</thead>

<tbody>
<?php 
$i = 1;
$filter = array(
	'party' => array(),
	'line'  => array(),
);
$total = array(
	'container_20' => 0,
	'container_40' => 0,
	'custom_duty'  => 0,
);
foreach ($rows as $r) {
	$filter['party'][$r['party_name']] = $r['party_name'];
	$filter['line'][$r['line_name']]   = $r['line_name'];

	$total['container_20'] += $r['container_20'];
	$total['container_40'] += $r['container_40'];
	$total['custom_duty']  += $r['custom_duty'];

	echo '<tr>
	<td class="aligncenter tiny">' . $i++ . '</td>
	<td class="aligncenter">' . $r['id2_format'] . '</td>
	<td class="tiny">' . $r['type'] . '</td>
	<td class="tiny">' . $r['party_name'] . '</td>
	<td class="tiny">' . $r['vessel'] . '</td>
	<td class="tiny">' . $r['product_name'] . '</td>
	<td class="tiny">' . $r['pol'] . '</td>
	<td class="tiny">' . $r['pod'] . '</td>
	<td class="tiny">' . $r['line_name'] . '</td>
	<td class="tiny">' . $r['bl_no'] . '</td>
	<td class="tiny">' . $r['bl_date'] . '</td>
	<td class="tiny">' . $r['be_no'] . '</td>
	<td class="tiny">' . $r['be_date'] . '</td>
	<td class="tiny aligncenter">' . $r['container_20'] . '</td>
	<td class="tiny aligncenter">' . $r['container_40'] . '</td>
	<td class="tiny alignright">' . $r['net_weight'] . '</td>
	<td class="tiny alignright">' . $r['custom_duty'] . '</td>
</tr>';
} 
?>
</tbody>

<tfoot>
	<tr>
		<th class="alignright" colspan="13">Total</th>
		<th class="alignright"><?php echo $total['container_20'] ?></th>
		<th class="alignright"><?php echo $total['container_40'] ?></th>
		<th></th>
		<th class="alignright"><?php echo $total['custom_duty'] ?></th>
	</tr>
</tfoot>
</table>

</body>
</html>