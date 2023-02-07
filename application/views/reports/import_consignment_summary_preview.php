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
	<th>Importer</th>
	<th>Vessel</th>
	<th>Cargo</th>
	<th>POD</th>
	<th>BL No</th>
	<th>C.20</th>
	<th>C.40</th>
	<th>BL Qty</th>
	<th>Rcvd Wt.</th>
	<th>Diff/Bal</th>
	<th>Dispatch Wt.</th>
	<th>Diff/Bal</th>
</tr>
</thead>

<tbody>
<?php 
$i = 1;
$filter = array(
	'party'      => array(),
);
$total = array(
	'container_20'    => 0,
	'container_40'    => 0,
	'net_weight'      => 0,
	'received_weight' => 0,
	'bl_rcvd_diff'   => 0,
	'dispatch_weight' => 0,
	'rcvd_disp_diff'  => 0,
	
);
foreach ($rows as $r) {
	$total['container_20']    += $r['container_20'];
	$total['container_40']    += $r['container_40'];
	$total['net_weight']      += $r['net_weight'];
	$total['received_weight'] += $r['received_weight'];
	$total['bl_rcvd_diff']    += $r['bl_rcvd_diff'];
	$total['dispatch_weight'] += $r['dispatch_weight'];
	$total['rcvd_disp_diff']  += $r['rcvd_disp_diff'];
	
	echo '<tr>
	<td class="aligncenter tiny">' . $i++ . '</td>
	<td class="aligncenter">' . $r['id2_format'] . '</td>
	<td class="tiny">' . $r['party_name'] . '</td>
	<td class="tiny">' . $r['vessel'] . '</td>
	<td class="tiny">' . $r['product_name'] . '</td>
	<td class="tiny">' . $r['pod'] . '</td>
	<td class="tiny">' . $r['bl_no'] . '</td>
	<td class="tiny aligncenter">' . $r['container_20'] . '</td>
	<td class="tiny aligncenter">' . $r['container_40'] . '</td>
	<td class="tiny alignright">' . $r['net_weight'] . '</td>
	<td class="tiny alignright">' . $r['received_weight'] . '</td>
	<td class="tiny alignright">' . $r['bl_rcvd_diff']  . '</td>
	<td class="tiny alignright">' . $r['dispatch_weight'] . '</td>
	<td class="tiny alignright">' . $r['rcvd_disp_diff'] . '</td>
</tr>';
} 
?>
</tbody>

<tfoot>
	<tr>
		<th class="alignright" colspan="7">Total</th>
		<th class="alignright"><?php echo $total['container_20'] ?></th>
		<th class="alignright"><?php echo $total['container_40'] ?></th>
		<th class="alignright"><?php echo $total['net_weight'] ?></th>
		<th class="alignright"><?php echo $total['received_weight'] ?></th>
		<th class="alignright"><?php echo $total['bl_rcvd_diff'] ?></th>
		<th class="alignright"><?php echo $total['dispatch_weight'] ?></th>
		<th class="alignright"><?php echo $total['rcvd_disp_diff'] ?></th>
	</tr>
</tfoot>
</table>

</body>
</html>