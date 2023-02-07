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
<?php 
	if ((strlen($company['logo'])) > 0) {
		// $type = pathinfo(base_url('/php_uploads/' . $company['logo']), PATHINFO_EXTENSION);
		// $data = file_get_contents(base_url('/php_uploads/' . $company['logo']));
		// $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
		$header = str_replace('{{company_logo}}', '<img src="data:image/png;base64,' . base64_encode($image_data) . '" width="100%" />', $company['letterhead']);
	}
	else  {
		$header = str_replace('{{company_logo}}', '<h2>' . $company['name'] . '</h2>', $company['letterhead']);
	}
	
	$header = str_replace('<p>&nbsp;</p>', '', $header);
	$header = str_replace('{{company_name}}', $company['name'], $header);
	$header = str_replace('{{company_address}}', $company['address'], $header);
	$header = str_replace('{{company_city}}', (isset($city['name']) ? $city['name'] . '-' . $city['pincode'] . ' ' . $state['name'] : ''), $header);
	$header = str_replace('{{company_contact}}', $company['contact'], $header);
	$header = str_replace('{{company_email}}', $company['email'], $header);
	$header = str_replace('{{company_pan_no}}', $company['pan_no'], $header);
	$header = str_replace('{{company_tan_no}}', $company['tan_no'], $header);
	$header = str_replace('{{company_service_tax_no}}', $company['service_tax_no'], $header);
	$header = str_replace('{{company_cha_no}}', $company['cha_no'], $header);
	$header = str_replace('{{company_cha_license_no}}', $company['cha_license_no'], $header);
	$header = str_replace('{{company_cin_no}}', $company['cin_no'], $header);
	// echo $header;
?>

<h3 class="aligncenter"><?php echo $page_title ?></h3>
<table class="details">
<tr>
	<th>Sr No</th>
	<th>Job No</th>
	<th>Customer Name</th>
	<th>Shipper Name</th>
	<th>BL No</th>
	<th>BL Weight</th>
	<th>Containers / CFS</th>
	<th>Shipping Line</th>
	<th>Vessel Name</th>
	<th>Place Of Discharge</th>
	<th>Eta</th>
	<th>Free Days Upto</th>
	<th>Doc. Rcvd.</th>
	<th>Current Status</th>
</tr>

<?php 
$total = 0;
$i = 1;
foreach ($rows as $r) {
	$total += $r['total_containers'];

	echo '<tr ' . (strlen($r['eta_date']) != 0 && 
		$r['eta_date'] != '00-00-0000' && 
		daysDiff(date('d-m-Y'), $r['eta_date'], 'd-m-Y') <= 1 ? ' style="background-color: #ffa;"' : null) . 
		'>
		<td class="tiny">' . $i++ . ' </td>
		<td class="tiny aligncenter">' . $r['id2_format'] . '</td>
		<td class="tiny">' . $r['customer_name'] . '&nbsp;</td>
		<td class="tiny">' . $r['shipper_name'] . '&nbsp;</td>
		<td class="tiny nowrap">' . $r['bl_no'] . '&nbsp;</td>
		<td class="tiny">' . $r['net_weight'] . '&nbsp;' . $r['net_weight_unit'] . '</td>
		<td class="tiny nowrap">' . $r['containers'] . '<br />';
		if (isset($r['delivery'])) {
			$is_container_20 = 0;
			if ($r['delivery']['container_20'] > 0) {
				$is_container_20 = 1;
		 		echo '<br />' . $r['delivery']['container_20'] . 'x20 ';
			}
		 	if ($r['delivery']['container_40'] > 0) {
		 		if (! $is_container_20) 
		 			echo '<br />';
		 		echo $r['delivery']['container_40'] . 'x40';
		 	}
		}
		echo '</td>
		<td class="tiny">' . $r['shipping_line'] . '&nbsp;</td>
		<td class="tiny">' . $r['vessel_name'] . '&nbsp;</td>
		<td class="tiny">' . $r['place_of_discharge'] . '&nbsp;</td>
		<td class="tiny nowrap">' . $r['eta_date'] . '&nbsp;</td>
		<td class="tiny nowrap">' . $r['free_days_upto'] . '&nbsp;</td>
		<td class="tiny aligncenter">' . $r['original_doc_rcvd'] . '&nbsp;</td>
		<td class="tiny">' . $r['current_status'] . '&nbsp;</td>
	</tr>';
}
?>

<tfoot>
<tr>
	<td class="alignright" colspan="6">Total</td>
	<td><?php echo $total ?></td>
	<td colspan="6"></td
</tr>
</tfoot>
</table>

</body>
</html>