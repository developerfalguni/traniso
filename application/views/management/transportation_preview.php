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
	echo $header;
	?>
</tr>
</table>
<br />
<h4><?php echo $page_title ?></h4>

<table class="details">
<thead>
<tr>
	<th width="24px">No</th>
	<th>Date</th>
	<th>Job No</th>
	<th>Container</th>
	<th>Size</th>
	<th>From</th>
	<th>To</th>
	<th>Party</th>
	<th>Party Rate</th>
	<th>Transporter</th>
	<th>TPT. Rate</th>
	<th>Vehicle No</th>
	<th>LR No</th>
	<th>Self Adv</th>
	<th>Party Adv</th>
	<th>Pump Name</th>
	<th>Pump Adv</th>
	<th>Payment No</th>
	<th>Chq. Advance</th>
	<th>Advance</th>
	<th>Balance</th>
	<th>Bill No</th>
</tr>
</thead>

<tbody>
<?php 
$total = array(
	'transporter_rate' => 0,
	'party_rate'       => 0,
	'self_adv'         => 0,
	'party_adv'        => 0,
	'pump_adv'         => 0,
	'allowance'        => 0,
	'balance'          => 0,
	'cheque_advance'   => 0,
);
$i = 1;
foreach ($rows as $r) {
	$total['transporter_rate'] = bcadd($total['transporter_rate'], $r['transporter_rate'], 2);
	$total['party_rate']       = bcadd($total['party_rate'], $r['party_rate'], 2);
	$total['self_adv']         = bcadd($total['self_adv'], $r['self_adv'], 2);
	$total['party_adv']        = bcadd($total['party_adv'], $r['party_adv'], 2);
	$total['pump_adv']         = bcadd($total['pump_adv'], $r['pump_adv'], 2);
	$total['allowance']        = bcadd($total['allowance'], $r['allowance'], 2);
	$total['balance']          = bcadd($total['balance'], $r['balance'], 2);
	$total['cheque_advance']   = bcadd($total['cheque_advance'], $r['cheque_advance'], 2);

	echo '<tr>
	<td class="tiny aligncenter">' . $i++ . '</td>
	<td class="tiny aligncenter">' . $r['date'] . '</td>
	<td class="tiny">' . $r['job_no'] . '</td>
	<td class="tiny">' . $r['container_no'] . '</td>
	<td class="tiny">' . $r['container_size'] . '</td>
	<td class="tiny">' . $r['from_location'] . '</td>
	<td class="tiny">' . $r['to_location'] . '</td>
	<td class="tiny">' . $r['party_name'] . '</td>
	<td class="tiny alignright">' . inr_format($r['party_rate']) . '</td>
	<td class="tiny">' .  $r['transporter_name'] . '</td>
	<td class="tiny alignright">' . inr_format($r['transporter_rate']) . '</td>
	<td class="tiny">' . ($r['self'] ? '<span class="label label-success">' . $r['registration_no'] . '</span>' : $r['registration_no']) . '</td>
	<td class="tiny">' . $r['lr_no'] . '</td>
	<td class="tiny alignright">' . inr_format($r['self_adv']) . '</td>
	<td class="tiny alignright">' . inr_format($r['party_adv']) . '</td>
	<td class="tiny">' .  $r['pump_name'] . '</td>
	<td class="tiny alignright">' . inr_format($r['pump_adv']) . '</td>
	<td class="tiny">' . $r['payment_no'] . '</td>
	<td class="tiny alignright">' . $r['cheque_advance'] . '</td>
	<td class="tiny alignright">' . inr_format($r['allowance']) . '</td>
	<td class="tiny alignright">' . inr_format($r['balance']) . '</td>
	<td class="tiny">' . $r['bill_no'] . '</td>
</tr>';
} 
?>
</tbody>

<tfoot>
<tr>
	<th class="alignright" colspan="8">Total</th>
	<th class="alignright"><?php echo inr_format($total['party_rate']) ?></th>
	<th></th>
	<th class="alignright"><?php echo inr_format($total['transporter_rate']) ?></th>
	<th></th>
	<th></th>
	<th class="alignright"><?php echo inr_format($total['self_adv']) ?></th>
	<th class="alignright"><?php echo inr_format($total['party_adv']) ?></th>
	<th></th>
	<th class="alignright"><?php echo inr_format($total['pump_adv']) ?></th>
	<th></th>
	<th class="alignright"><?php echo inr_format($total['cheque_advance']) ?></th>
	<th class="alignright"><?php echo inr_format($total['allowance']) ?></th>
	<th class="alignright"><?php echo inr_format(round($total['balance'], 0)) ?></th>
	<th></th>
</tr>
</tfoot>
</table>

</body>
</html>