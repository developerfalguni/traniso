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
<h4 class="aligncenter"><?php echo $page_title . ' ' . $page_desc ?></h4>

<table class="details">
<thead>
<tr>
	<th>No</th>
	<th>Invoice No</th>
	<th>Invoice Date</th>
	<th>Party</th>
	<th>Vessel</th>
	<th>BL No</th>
	<th>BE / SB</th>
	<th>CFS</th>
	<th>Product</th>
	<th>Port</th>
	<th>20'</th>
	<th>40'</th>
</tr>
</thead>

<tbody>
<?php 
$total = array('c20' => 0, 'c40' => 0);
$i = 1;
foreach ($rows as $group_name => $group_rows) {
	$group_total = array('c20' => 0, 'c40' => 0);

	echo '<tr class="SubTotal">
	<td class="bold" colspan="11">' . $group_name . '</td>
</tr>';
	foreach ($group_rows as $r) {
		$group_total['c20'] += $r['container_20'];
		$group_total['c40'] += $r['container_40'];

		$total['c20'] += $r['container_20'];
		$total['c40'] += $r['container_40'];

		echo '<tr>
	<td class="aligncenter">' . $i++ . '</td>
	<td>' . $r['id2_format'] . '</td>
	<td class="aligncenter">' . $r['date'] . '</td>
	<td>' . $r['party_name'] . '</td>
	<td>' . $r['vessel_voyage'] . '</td>
	<td>' . $r['bl_no'] . '</td>
	<td>' . $r['be_sb'] . '</td>
	<td>' . $r['cfs_name'] . '</td>
	<td>' . $r['product_name'] . '</td>
	<td>' . $r['indian_port'] . '</td>
	<td class="alignright">' . $r['container_20'] . '</td>
	<td class="alignright">' . $r['container_40'] . '</td>
</tr>';
	}
	echo '<td class="alignright bold" colspan="10">Total</td>
	<td class="alignright bold">' . $group_total['c20'] . '</td>
	<td class="alignright bold">' . $group_total['c40'] . '</td>
</tr>
';
} 
?>
</tbody>

<tfoot>
<tr>
	<th class="alignright" colspan="10">Total</th>
	<th class="alignright"><?php echo $total['c20'] ?></th>
	<th class="alignright"><?php echo $total['c40'] ?></th>
</tr>
</tfoot>
</table>


</body>
</html>