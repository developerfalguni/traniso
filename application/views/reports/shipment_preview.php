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

<?php 
$header = '
<table width="100%">
<tr>';
	if ((strlen($company['logo'])) > 0) {
		$header .= '<td width="60%"><img src="data:image/png;base64,' . base64_encode($image_data) . '" height="80px" /></td>';
		$header .= '<td class="alignright" valign="top"><span class="tiny">' . $company['address'] . '<br />' . (isset($city['name']) ? $city['name'] . '-' . $city['pincode'] . ' ' . $state['name'] . '<br />' : '') . $company['contact'] . '<br />' . $company['email'] . '</span></td>';
	}
$header .= '
</tr>
</table>

<h3 class="aligncenter">Shipment Details cum Stuffing Report</h3>';

$total = array(
	'containers'   => 0,
	'units'        => 0,
	'nett_weight'  => 0,
	'gross_weight' => 0,
);
$prev_job_id = null;
$i = 1;
foreach ($rows as $r) {
	if (is_null($prev_job_id) OR $prev_job_id != $r['job_id']) {
		if (! is_null($prev_job_id))
			echo '
		<tfoot>
		<tr>
			<td colspan="7" class="alignright">Total</td>
			<td class="alignright">' . $total['containers'] . '</td>
			<td></td>
			<td class="alignright">' . $total['units'] . '
			<td class="alignright">' . number_format($total['nett_weight'], 3) . '</td>
			<td class="alignright">' . number_format($total['gross_weight'], 3) . '</td>
			<td colspan="2"></td>
		</tr>
		</tfoot>
		</table>

		<div class="page-break"></div>';

		$i = 1;
		$total = array(
			'containers'   => 0,
			'units'        => 0,
			'nett_weight'  => 0,
			'gross_weight' => 0,
		);

		echo $header . 
		'<table class="header">
		<tr>
			<td width="50%" colspan="2"><span class="box_label">To</span><br />' . $shipper['name'] . '</td>
			<td width="25%"><span class="box_label">NSPL Job No</span><br />' . $r['id2_format'] . '</td>
			<td width="25%"><span class="box_label">Shipping Line</span><br />' . $r['line_code'] . '</td>
		</tr>

		<tr>
			<td colspan="2"><span class="box_label">Consignee</span><br />' . $r['consignee_name'] . '</td>
			<td><span class="box_label">ETA Date</span><br />' . $r['eta_date'] . '</td>
			<td><span class="box_label">ETD Date</span><br />' . $r['etd_date'] . '</td>
		</tr>

		<tr>
			<td colspan="2"><span class="box_label">Vessel Name</span><br />' . $r['vessel_name'] . '</td>
			<td colspan="2"><span class="box_label">POL</span><br />' . $r['loading_port'] . '</td>
		</tr>

		<tr>
			<td colspan="2"><span class="box_label">Product Name</span><br />' . $r['product_name'] . ' in ' . $r['unit_code'] . '</td>
			<td><span class="box_label">Transhipment Port</span><br />' . $r['transhipment'] . '</td>
			<td><span class="box_label">FPD</span><br />' . $r['fpod'] . '</td>
		</tr>

		<tr>
			<td colspan="4"><span class="box_label">&nbsp;</span><br />' . $r['shipment_details'] . '</td>
		</tr>
		</table>

		<table class="details">
		<tr>
			<th>No</th>
			<th>Invoice No</th>
			<th>Invoice Date</th>
			<th>Pickup Date</th>
			<th>Stuffing Date</th>
			<th>Vehicle No</th>
			<th>Container No</th>
			<th>Size</th>
			<th>Seal No</th>
			<th>Packages</th>
			<th>Nett Weight</th>
			<th>Gross Weight</th>' . 
			($r['unit_code'] == 'FLEXI' ? '<th>Flexi Tank No</th>' : '') .
			'<th>Stuffing Location</th>
		</tr>';
	}

	$total['containers']   += 1;
	$total['units']        += $r['units'];
	$total['nett_weight']  += $r['nett_weight'];
	$total['gross_weight'] += $r['gross_weight'];

	echo '<tr>
<td class="aligncenter tiny">' . $i++ . '</td>
<td class="tiny">' . $r['invoice_no'] . '</td>
<td class="aligncenter tiny">' . $r['invoice_date'] . '</td>
<td class="aligncenter tiny">' . $r['pickup_date'] . '</td>
<td class="aligncenter tiny">' . $r['stuffing_date'] . '</td>
<td class="tiny">' . $r['vehicle_no'] . '</td>
<td class="tiny">' . $r['container_no'] . '</td>
<td class="tiny aligncenter">' . $r['size'] . '</td>
<td class="tiny">' . $r['seal_no'] . '</td>
<td class="tiny alignright">' . $r['units'] . ' ' . $r['unit_code'] . '</td>
<td class="tiny alignright">' . number_format($r['nett_weight'], 3) . '</td>
<td class="tiny alignright">' . number_format($r['gross_weight'], 3) . '</td>' . 
($r['unit_code'] == 'FLEXI' ? '<td class="aligncenter tiny">' . $r['flexi_tank_no'] . '</td>' : '') .
'<td class="aligncenter tiny">' . $r['stuffing_location'] . '</td>
</tr>';

	$prev_job_id = $r['job_id'];
}
?>
<tfoot>
<tr>
	<td colspan="7" class="alignright">Total</td>
	<td class="alignright"><?php echo $total['containers'] ?></td>
	<td></td>
	<td class="alignright"><?php echo $total['units'] ?></td>
	<td class="alignright"><?php echo number_format($total['nett_weight'], 3) ?></td>
	<td class="alignright"><?php echo number_format($total['gross_weight'], 3) ?></td>
	<td colspan="2"></td>
</tr>
</tfoot>
</table>

</body>
</html>