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
</tr>
</table>

<h3 class="aligncenter"><?php echo $page_title ?></h3>
<table class="details">
<tr>
	<th>Job No</th>
	<th>Party Name</th>
	<th>Shipper Name</th>
	<th>BL No / BE No</th>
	<th>Cont. / BL Qty</th>
	<th>Received Weight</th>
	<th>Dispatched Weight</th>
	<th>Balance Weight</th>
	<th>Shipping Line / CFS</th>
	<th>Vessel / POD</th>
	<th>ETA</th>
	<th>Free Days</th>
	<th>IceGate</th>
	<th>C. Duty</th>
	<th>Expenses</th>
	<th>BL Rcvd.</th>
	<th>DO</th>
	<th>Remarks</th>
</tr>

<?php 
	$total = array(
		'containers'  => 0,
		'custom_duty' => 0,
		'expenses'    => 0,
	);
	$i = 1;
	$dispatch_wt_total = $received_wt_total = $balabce_wt = 0;
	foreach ($rows as $r) {
		$total['containers']  += $r['total_containers'];
		$total['custom_duty'] += $r['custom_duty'];
		$total['expenses']    += $r['expenses'];

		$balabce_wt = $r['delivery']['net_weight'] - $r['delivery']['dispatch_weight'];
		$dispatch_wt_total += $r['delivery']['net_weight'];
		$received_wt_total += $r['delivery']['dispatch_weight'];

		echo '<tr>
	<td class="tiny aligncenter nowrap">' . $r['id2_format'] . '</td>
	<td class="tiny">' . $r['party_name'] . '</td>
	<td class="tiny">' . $r['shipper_name'] . '</td>
	<td class="tiny blue">' . $r['bl_no'] . '/<br />'.$r['be_no'].'</td>
	<td class="tiny">' . $r['containers'] . '<br />' . $r['net_weight'] . ' '. $r['net_weight_unit'] . '</td>
	<td class="tiny">' . $r['delivery']['net_weight'] . '</td>
	<td class="tiny">' . $r['delivery']['dispatch_weight'] . '</td>
	<td class="tiny">' . $balabce_wt . '</td>
	<td class="tiny">' . character_limiter($r['line_name'], 10) . '<br />' . character_limiter($r['cfs_name'], 10) . '</td>
	<td class="tiny">' . $vessel . '<br /><span class="blue">' . $r['indian_port'] . (strlen($r['place_of_delivery']) > 0 ? ' >>> ' . $r['place_of_delivery'] : '') . '</span></td>
	<td class="tiny ' . ($r['eta_date'] != '00-00-0000' && daysDiff(date('d-m-Y'), $r['eta_date'], 'd-m-Y') <= 1 && $r['status'] == 'Pending' ? ' markRed' : '') . '">
		' . $r['eta_date'] . '</td>
		
	<td class="tiny">' . ($r['eta_date'] == '00-00-0000' ? '' : $r['free_days'] . '<br /><span class="label label-default">' . $r['free_days_upto']) . '</span></td>
	
	<td class="tiny">';
	if (strlen($r['be_no']) > 0) {
		echo ($r['query_raised'] != 'N.A.' && strlen($r['query_raised']) > 0 ? '<span class="label label-red">' . (stripos($r['query_raised'], '#') !== false ? '??' : '?') . '</span>' : '') . 
			($r['section_48'] == 'Y' ? '<span class="label label-danger">SEC48</span> ' : '') . 
			'-' . $r['current_status'] ;
		}
	if (! is_null($r['challan_id']))
		echo '-C<br />';
	echo '<div class="tiny alignright">' . moment($r['last_fetched']) . '</div></td>

	<td class="tiny alignright">' . inr_format($r['custom_duty']) . '</td>
	<td class="tiny alignright">' . inr_format($r['expenses']) . '</td>
	
	<td class="tiny">' . $r['original_bl_received'] . '</td>
	<td class="tiny">' . $r['do_no'] . $r['delivery_date'] . $r['validity'] . $r['empty_return_park'] . '</td>
	<td class="tiny">' . $r['remarks'] . '</td>
	</tr>';
}
?>

<tfoot>
<tr>
	<td class="alignright" colspan="4">Total</td>
	<td class="aligncenter"><?php echo $total['containers'] ?></td>
	<td class="aligncenter"><?php echo $dispatch_wt_total ?></td>
	<td class="aligncenter"><?php echo $received_wt_total ?></td>
	<td class="aligncenter"><?php echo ($dispatch_wt_total - $received_wt_total) ?></td>
	<td colspan="5"></td>
	<td class="aligncenter"><?php echo inr_format($total['custom_duty']) ?></td>
	<td class="aligncenter"><?php echo inr_format($total['expenses']) ?></td>
	<td colspan="3"></td>
</tr>
</tfoot>
</table>

</body>
</html>