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

<h3 class="aligncenter">Tentative Factory Stuffing Program</h3>

<table class="header">
<tr>
	<td width="50%"><span class="box_label">To</span><br /><?php echo $shipper['name'] ?></td>
	<td width="25%"><span class="box_label">Date</span><br /><?php echo $date ?></td>
</tr>
</table>

<table class="details">
<tr>
	<th>No</th>
	<th>Stuffing Location</th>
	<th>Cargo</th>
	<th>Unit</th>
	<th>Stuffing Date</th>
	<th>Vehicle No / LR No</th>
	<th>Container No / Seal No</th>
	<th>Type</th>
	<th>POL / FPD</th>
	<th>Line</th>
	<th>Vessel</th>
	<th>ETA / ETD / Cutoff</th>
	<th>Party Ref</th>
	<th>Booking No</th>
</tr>

<?php
	$i = 1;
	foreach ($rows as $r) {
		echo '<tr>
	<td class="aligncenter tiny">' . $i++ . '</td>
	<td class="aligncenter tiny">' . $r['stuffing_location'] . '</td>
	<td class="tiny">' . $r['cargo_name'] . '</td>
	<td class="tiny">' . $r['unit_code'] . '</td>
	<td class="aligncenter tiny">' . $r['stuffing_date'] . '</td>
	<td class="tiny">' . $r['vehicle_no'] . '<br />' . $r['lr_no'] . '</td>
	<td class="tiny">' . $r['container_no'] . '<br />' . $r['seal_no'] . '</td>
	<td class="aligncenter tiny">' . $r['container_type'] . '</td>
	<td class="tiny">' . character_limiter($r['gateway_port'], 10) . '<br />' . $r['fpod'] . '</td>
	<td class="tiny">' . $r['line_code'] . '</td>
	<td class="tiny">' . $r['vessel_name'] . '</td>
	<td class="aligncenter tiny nowrap">' . $r['eta_date'] . '<br />' . $r['etd_date'] . '<br />' . $r['gate_cutoff_date'] . '</td>
	<td class="aligncenter tiny nowrap">' . $r['party_ref'] . '</td>
	<td class="aligncenter tiny nowrap">' . $r['booking_no'] . '</td>
</tr>';
	}
?>
</table>

<table>
<tr>
	<td colspan="2" class="big"><?php echo $template ?></td>
</tr>

<tr>
	<td width="50%">For <?php echo $company['name'] ?><br /><br /><br />Authorized Signatory</td>
	<td></td>
</tr>
</table>

</body>
</html>