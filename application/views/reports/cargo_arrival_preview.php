<!DOCTYPE html>
<html lang="en">
<head>
	<title><?php echo $page_title ?></title>
	<style>
		<?php 
			echo file_get_contents(FCPATH.'assets/css/print.css'); 
			$image_data = file_get_contents(FCPATH.'php_uploads/'.$company['logo']);
		?>
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
			echo '<td width="60%"><img src="data:image/png;base64,' . base64_encode($image_data) . '" height="80px" /></td>';
			echo '<td class="alignright" valign="top"><span class="tiny">' . $company['address'] . '<br />' . (isset($city['name']) ? $city['name'] . '-' . $city['pincode'] . ' ' . $state['name'] . '<br />' : '') . $company['contact'] . '<br />' . $company['email'] . '</span></td>';
		}
	}
	?>
</tr>
</table>

<?php 
$first_run = true;
foreach ($rows as $invoice_no => $eis) : 
	if ($first_run)
		$first_run = false;
	else 
		echo '<div class="page-break"></div>';
?>

<table class="header">
<tr>
	<td width="50%"><span class="box_label">To</span><br /><?php echo $party['name'] ?></td>
	<td width="25%"><span class="box_label">Invoice No</span><br /><?php echo $invoice_no ?></td>
	<td width="25%"><span class="box_label">Date</span><br /><?php echo $date ?></td>
</tr>
</table>

<?php 
	$cargo_total = array(
		'units'           => 0,
		'dispatch_weight' => 0,
		'received_weight' => 0
	);

	if (isset($eis['cargo_arrivals'])) : 
?>
<h3 class="aligncenter"><?php echo $page_title ?></h3>

<table class="details">
<thead>
<tr>
	<th width="100px">Date</th>
	<th width="80px">Job No</th>
	<th>Supplier Name</th>
	<th>Marks</th>
	<th>Vehicle No</th>
	<th>Units</th>
	<th>Packaging</th>
	<th>Dispatch Weight</th>
	<th>Rcvd. Weight</th>
</tr>
</thead>

<tbody>
<?php 
	foreach ($eis['cargo_arrivals'] as $r) {
		$cargo_total['units']          += $r['units'];
		$cargo_total['dispatch_weight'] = bcadd($cargo_total['dispatch_weight'], $r['dispatch_weight'], 3);
		$cargo_total['received_weight'] = bcadd($cargo_total['received_weight'], $r['received_weight'], 3);

		echo '<tr>
	<td class="tiny aligncenter">'.$r['date'].'</td>
	<td class="tiny aligncenter">' . $r['id2_format'] . '</td>
	<td class="tiny">'.$r['supplier_name'].'</td>
	<td class="tiny">'.$r['remarks'].'</td>
	<td class="tiny">' . $r['vehicle_no'] . '</td>
	<td class="tiny alignright">'.$r['units'].'</td>
	<td class="tiny alignright">'.$r['code'].'</td>
	<td class="tiny alignright">'.$r['dispatch_weight'].'</td>
	<td class="tiny alignright">'.$r['received_weight'].'</td>
</tr>';
	}
?>
</tbody>

<tfoot>
	<tr>
		<th colspan="5" class="alignright">Total</th>
		<th class="alignright"><?php echo $cargo_total['units'] ?></th>
		<th></th>
		<th class="alignright"><?php echo $cargo_total['dispatch_weight'] ?></th>
		<th class="alignright"><?php echo $cargo_total['received_weight'] ?></th>
	</tr>
</tfoot>
</table>
<br />

<?php 
	endif;

$stuffing_total = array(
	'units'        => 0,
	'gross_weight' => 0,
	'nett_weight'  => 0,
);
if (isset($eis['stuffing_details'])) : 

?>
<h3 class="aligncenter">Stuffing Details</h3>
<table class="details">
<thead>
<tr>
	<th width="80px">Job No</th>
	<th>Container No</th>
	<th>Pickup Date</th>
	<th>Stuffing Date</th>
	<th>Seal No</th>
	<th>Units</th>
	<th>Unit</th>
	<th>Gross Weight</th>
	<th>Nett Weight</th>
</tr>
</thead>
<tbody>
<?php 
foreach ($eis['stuffing_details'] as $s) {
	$stuffing_total['units']   += $s['units'];
	$stuffing_total['gross_weight']  = bcadd($stuffing_total['gross_weight'], $s['gross_weight'], 3);
	$stuffing_total['nett_weight']  = bcadd($stuffing_total['nett_weight'], $s['nett_weight'], 3);

	echo '<tr>
	<td class="tiny aligncenter">'.$s['id2_format'].'</td>
	<td class="tiny aligncenter">'.$s['container_no'].'</td>
	<td class="tiny aligncenter">'.$s['pickup_date'].'</td>
	<td class="tiny aligncenter">'.$s['stuffing_date'].'</td>
	<td class="tiny aligncenter">'.$s['seal_no'].'</td>
	<td class="tiny alignright">'.$s['units'].'</td>
	<td class="tiny aligncenter">'.$s['code'].'</td>
	<td class="tiny alignright">'.$s['gross_weight'].'</td>
	<td class="tiny alignright">'.$s['nett_weight'].'</td>
</tr>';
}
?>
</body>

<tfoot>
	<tr>
		<th colspan="5" class="alignright">Total</th>
		<th class="alignright"><?php echo $stuffing_total['units'] ?></th>
		<th></th>
		<th class="alignright"><?php echo $stuffing_total['gross_weight'] ?></th>
		<th class="alignright"><?php echo $stuffing_total['nett_weight'] ?></th>
	</tr>
</tfoot>
</table>
<br/ >
<?php endif; ?>

<table class="details">
	<thead>
		<tr>
			<th rowspan="2">Balance</th>
			<th colspan="2">Cargo Arrivals</th>
			<th colspan="3">Stuffing Details</th>
			<th colspan="3">Difference</th>
		</tr>
		<tr>
			<th width="80px">Units</th>
			<th>Received Weight</th>
			<th width="80px">Units</th>
			<th>Gross Weight</th>
			<th>Nett Weight</th>
			<th width="80px">Units</th>
			<th>Rcvd. - Gross</th>
			<th>Rcvd. - Nett</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>Total</td>
			<td class="alignright"><?php echo $cargo_total['units'] ?></td>
			<td class="alignright"><?php echo $cargo_total['received_weight'] ?></td>
			<td class="alignright"><?php echo $stuffing_total['units'] ?></td>
			<td class="alignright"><?php echo $stuffing_total['gross_weight'] ?></td>
			<td class="alignright"><?php echo $stuffing_total['nett_weight'] ?></td>
			<td class="alignright"><strong><?php echo $cargo_total['units']-$stuffing_total['units'] ?></strong></td>
			<td class="alignright"><strong><?php echo bcsub($cargo_total['received_weight'], $stuffing_total['gross_weight'], 3) ?></strong></td>
			<td class="alignright"><strong><?php echo bcsub($cargo_total['received_weight'], $stuffing_total['nett_weight'], 3) ?></strong></td>
		</tr>
	</tbody>
</table>

<?php endforeach; ?>

</body>
</html>