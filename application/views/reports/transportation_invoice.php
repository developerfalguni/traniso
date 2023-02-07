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
<?php if ($letterhead) {
	$header = $company['letterhead'];

	if ((strlen($company['logo'])) > 0) {
		$header = str_replace('{{company_logo}}', '<img src="data:image/png;base64,' . base64_encode($image_data) . '" width="100%" />', $header);
	}
	else  {
		$header = str_replace('{{company_logo}}', '<h2>' . $company['name'] . '<h2>', $header);
	}
	
	$header = str_replace('<p>&nbsp;</p>', '', $header);
	$header = str_replace('{{company_name}}', $company['name'], $header);
	$header = str_replace('{{company_address}}', $company['address'], $header);
	$header = str_replace('{{company_city}}', (isset($city['name']) ? $city['name'] . '-' . $city['pincode'] . ' ' . $state['name'] : ''), $header);
	$header = str_replace('{{company_contact}}', $company['contact'], $header);
	$header = str_replace('{{company_email}}', str_replace(', ', '<br />', $company['email']), $header);
	$header = str_replace('{{company_pan_no}}', $company['pan_no'], $header);
	$header = str_replace('{{company_tan_no}}', $company['tan_no'], $header);
	$header = str_replace('{{company_service_tax_no}}', $company['service_tax_no'], $header);
	$header = str_replace('{{company_cha_no}}', $company['cha_no'], $header);
	$header = str_replace('{{company_cha_license_no}}', $company['cha_license_no'], $header);
}
else {
	$header = '<br /><br /><br /><br /><br /><br />';
}

$header .= '<h3 class="aligncenter">' . $page_title . '</h3>

<table class="header">
<tr>
	<td width="60%" colspan="2"><span class="box_label">To</span><br />' . 
		$debit_ledger['name'] . '<br />' . $party['address'] . '</td>
	<td width="20%"><span class="box_label">' . $page_title . ' No</span><br />' . $voucher['id2_format'] . '</td>
	<td width="20%"><span class="box_label">Date</span><br />' . $voucher['date'] . '</td>
</tr>

<tr>
	<td colspan="2"><span class="box_label">BL No</span><br />' . $job['bl_no'] . '</td>
	<td colspan="2"><span class="box_label">BE No</span><br />' . $job['be_no'] . '</td>
</tr>
</table>

<table class="details">
<tr>
	<th rowspan="2" width="24px">No</th>
	<th colspan="10">Particulars</th>
	<th rowspan="2" width="80px">Net Total</th>
</tr>

<tr>
	<th>Date</th>
	<th>Lr No</th>
	<th>Party Ref. No</th>
	<th>Container No</th>
	<th>Vehicle No</th>
	<th>From</th>
	<th>To</th>
	<th>Unit</th>
	<th>Rate</th>
	<th>Advance</th>
</tr>';

echo $header;
$max_items   = 17;
$total_lines = count($voucher_details);
$hard_limit  = ($max_items + 5);
$lines       = 0;
$i           = 1;
$j           = 0;
$GrandTotal  = 0;
$total       = array(
	'amount'  => 0,
	'advance' => 0,
	'balance' => 0,
	'rate'    => 0
);
$other_row = array();
$tax_row   = array();
foreach ($voucher_details as $vjd) {
	if ($vjd['amount'] == 0) continue;

	if (isset($service_taxes[$vjd['bill_item_id']])) {
		$tax_row[] = '<tr>
			<td>&nbsp;</td>
			<td colspan="9"><span class="box_label">' . $vjd['particulars'] . '</span></td>
			<td class="alignright nowrap"><span class="tiny">' . $vjd['tax_calculation'] . '</span></td>
			<td class="alignright">' . inr_format($vjd['amount'], 2, '.', '') . '</td>
		</tr>';
		$GrandTotal = bcadd($GrandTotal, $vjd['amount'], 2);
	}
	else {
		$lines++;
		$j++;
	
		if ($vjd['trip_id'] == 0) {
			$total['rate']    += $vjd['rate'];
			$total['advance'] += $vjd['advance'];

			echo '<tr>
				<td class="tiny aligncenter">' . $i++ . '</td>
				<td class="tiny" colspan="8">' . $vjd['particulars'] . '</td>
				<td class="tiny alignright">' . $vjd['rate'] . '</td>
				<td class="tiny alignright">' . $vjd['advance'] . '</td>
				<td class="tiny alignright">' . inr_format($vjd['amount']) . '</td>
			</tr>';
			$GrandTotal = bcadd($GrandTotal, $vjd['amount'], 2);
		}
		else {
			$total['rate']    += $vjd['rate'];
			$total['advance'] += $vjd['advance'];

			echo '<tr>
				<td class="tiny aligncenter">' . $i++ . '</td>
				<td class="tiny nowrap">' . $vjd['date'] . '</td>
				<td class="tiny nowrap">' . $vjd['lr_no'] . '</td>
				<td class="tiny nowrap">' . $vjd['party_reference_no'] . '</td>
				<td class="tiny nowrap">' . $vjd['container_no'] . '</td>
				<td class="tiny nowrap">' . $vjd['registration_no'] . '</td>
				<td class="tiny nowrap">' . $vjd['from_location'] . '</td>
				<td class="tiny nowrap">' . $vjd['to_location'] . '</td>
				<td class="tiny alignright">' . $vjd['units'] . '</td>
				<td class="tiny alignright">' . $vjd['rate'] . '</td>
				<td class="tiny alignright">' . $vjd['advance'] . '</td>
				<td class="tiny alignright">' . inr_format($vjd['amount'] - $vjd['advance']) . '</td>
			</tr>';
			$GrandTotal = bcadd($GrandTotal, ($vjd['amount'] - $vjd['advance']), 2);
		}

		if (($max_items - $j) <= 5) {
			if ($j % $hard_limit == 0 && $lines < $total_lines) {
				$j = 0;
				echo '<tr><td colspan="12" class="alignright">P.T.O...</td></tr></table><div class="page-break"></div>' . $header;
			}
		}
		else if (($max_items - $j) < 5 && $j % $max_items == 0 && $lines < $total_lines) {
			$j = 0;
			echo '<tr><td colspan="12" class="alignright">P.T.O...</td></tr></table><div class="page-break"></div>' . $header;
		}
	}
	
}
for(; $j <= $max_items; $j++) {
	echo '<tr><td style="color: #fff">.</td><td colspan="10">&nbsp;</td><td>&nbsp;</td></tr>';
}
echo join($tax_row);

$nettotal      = round($GrandTotal, 2);
$nettotal_roff = round($nettotal, 0);
$roundoff      = round($nettotal_roff - $nettotal, 2);
if ($roundoff != 0) {
	echo '
<tr>
<td>&nbsp;</td>
<td colspan="10" class="alignright tiny">Round Off (+/-) :</td>
<td class="alignright">' . inr_format($roundoff, 2,'.','') . '</td>
</tr>';
}
?>

<tr>
	<td colspan="6"><span class="box_label">Total Amount Chargeable (in words)</span><br /><?php echo numberToWords($nettotal_roff) ?></td>
	<td colspan="2" class="nowrap"><span class="box_label">Total</span><br /><div class="alignright"><img src="<?php echo base_url('assets/images/rupee.png') ?>" class="alignbottom" /><?php echo inr_format($total['rate']) ?></td>
	<td colspan="2" class="nowrap"><span class="box_label">Advance</span><br /><div class="alignright"><img src="<?php echo base_url('assets/images/rupee.png') ?>" class="alignbottom" /><?php echo inr_format($total['advance']) ?></td>
	<td colspan="2" class="nowrap"><span class="box_label">Net Amount</span><br /><div class="alignright"><img src="<?php echo base_url('assets/images/rupee.png') ?>" class="alignbottom" /> <?php echo inr_format($nettotal_roff, 2,'.','') ?></div></td>
</tr>

<?php
if (strlen(trim($voucher['narration'])) > 0) {
	echo '
<tr>
<td colspan="11"><pre class="tiny">' . $voucher['narration'] . '</pre></td>
</tr>';
	$lines++;
}
?>
</table>

<?php
$footer = str_replace('<p>&nbsp;</p>', '', $company['letterfoot']);
$footer = str_replace('{{company_name}}', $company['name'], $footer);
$footer = str_replace('{{company_pan_no}}', $company['pan_no'], $footer);
$footer = str_replace('{{company_tan_no}}', $company['tan_no'], $footer);
$footer = str_replace('{{company_service_tax_no}}', $company['service_tax_no'], $footer);
$footer = str_replace('{{company_cha_no}}', $company['cha_no'], $footer);
$footer = str_replace('{{company_cha_license_no}}', $company['cha_license_no'], $footer);
$footer = str_replace('{{company_cin_no}}', $company['cin_no'], $footer);
$footer = str_replace('{{invoice_remarks}}', $voucher['remarks'], $footer);
echo $footer;
?>

</body>
</html>