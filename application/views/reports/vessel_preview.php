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
<table class="details">
<thead>
<tr>
	<th width="48px">No</th>
	<th>Vessel</th>
	<th width="100px">Pieces</th>
	<th width="100px">CBM</th>
</tr>
</thead>

<tbody>
<?php 
$i = 1;
$total = array('pieces' => 0, 'cbm' => 0);
foreach ($rows as $r) {
	$total['pieces'] += $r['pieces'];
	$total['cbm']    += $r['cbm'];

	echo '<tr>
	<td class="aligncenter">' . $i++ . '</td>
	<td>' . $r['vessel_voyage'] . '</td>
	<td class="alignright">' . $r['pieces'] . '</td>
	<td class="alignright">' . $r['cbm'] . '</td>
</tr>';
} 
?>
</tbody>

<tfoot>
<tr>
	<th></th>
	<th class="alignright">Total</th>
	<th class="alignright"><?php echo $total['pieces'] ?></th>
	<th class="alignright"><?php echo $total['cbm'] ?></th>
</tr>
</tfoot>
</table>

</body>
</html>