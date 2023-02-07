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
	<th>Type</th>
	<th>Cargo</th>
	<th>Vessel</th>
	<th>Party Name</th>
	<th>BL No</th>
	<th>CHA Name</th>
	<th>Quantity</th>
	<th>Unit</th>
	<th>Weight</th>
	<th>Unit</th>
</tr>
</thead>

<tbody>
<?php 
$i = 1;
$total = array('pieces' => 0, 'cbm' => 0);
$filter = array(
	'group'  => array(),
	'party'  => array(),
	'vessel' => array(),
	'port'   => array(),
	'cha'    => array(),
);
foreach ($rows as $r) {
	$total['pieces'] += $r['pieces'];
	$total['cbm']    += $r['cbm'];

	$filter['group'][$r['group_name']]     = 1;
	$filter['party'][$r['party_name']]     = 1;
	$filter['vessel'][$r['vessel_voyage']] = 1;
	$filter['port'][$r['indian_port']]     = 1;
	$filter['cha'][$r['cha_name']]         = 1;

	echo '<tr>
	<td class="aligncenter">' . $i++ . '</td>
	<td>' . $r['type'] . '</td>
	<td>' . $r['cargo_type'] . '</td>
	<td>' . $r['vessel_voyage'] . '</td>
	<td>' . $r['party_name'] . '</td>
	<td>' . $r['bl_no'] . '</td>
	<td>' . $r['cha_name'] . '</td>
	<td class="alignright">' . $r['pieces'] . '</td>
	<td class="tiny">' . $r['package_unit'] . '</td>
	<td class="alignright">' . $r['cbm'] . '</td>
	<td class="tiny">' . $r['net_weight_unit'] . '</td>
</tr>';
} 
?>
</tbody>

<tfoot>
<tr>
	<th></th>
	<th class="alignright" colspan="6">Total</th>
	<th class="alignright"><?php echo $total['pieces'] ?></th>
	<th></th>
	<th class="alignright"><?php echo $total['cbm'] ?></th>
	<th></th>
</tr>
</tfoot>
</table>


</body>
</html>