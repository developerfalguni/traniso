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
<?php if ($letterhead) : ?>
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
<?php else : ?>
<br /><br /><br /><br /><br /><br />
<?php endif; ?>

<h3 class="aligncenter"><?php echo $page_title ?></h3>

<table class="header">
<tr>
	<td rowspan="2" colspan="2"><span class="box_label">To</span><br />
		<?php echo (isset($voucher['debit_party_name']) ? $voucher['debit_party_name'] : ''); ?><br />
		<?php if (isset($voucher['debit_party_address'])) 
			  	echo $voucher['debit_party_address']; ?></td>
	<td width="30%"><span class="box_label"><?php echo $page_title ?> No</span><br /><?php echo $voucher['id2_format'] ?></td>
	<td width="15%"><span class="box_label">Date</span><br /><?php echo $voucher['date'] ?></td>
	<td width="15%"><span class="box_label">Type</span><br /><?php echo $job['cargo_type'] ?></td>
</tr>

<tr>
	<td><span class="box_label">Port of Shipment</span><br />&nbsp;</td>
	<td colspan="2"><span class="box_label">Port of Discharge</span><br />&nbsp;</td>	
</tr>

<tr>
	<td width="40%" colspan="2"><span class="box_label">&nbsp;<br />&nbsp;</td>
	<td colspan="3"><span class="box_label">Vessel Name</span><br />&nbsp;</td>
</tr>

<tr>
	<td colspan="2"><span class="box_label">&nbsp;</span><br />&nbsp;</td>
	<td><span class="box_label">B/L No &amp; Date</span><br />&nbsp;</td>
	<td colspan="2"><span class="box_label">B/E No &amp; Date</span><br />&nbsp;</td>
</tr>

<tr>
	<td colspan="2"><span class="box_label">Description of Goods</span><br />&nbsp;</td>
	<td><span class="box_label">Unit</span><br />&nbsp;</td>
	<td width="15%"><span class="box_label">Type</span><br />&nbsp;</td>
	<td width="15%"><span class="box_label">Containers</span><br />&nbsp;</td>
</tr>
</table>

<table class="details">
<tr>
	<th width="24px">No</th>
	<th>Particulars</th>
	<th width="80px">Qty</th>
	<th width="80px">Rate</th>
	<th width="120px">Amount</th>
</tr>

<?php
	$lines = 1;
	$i = 1;
	$GrandTotal = 0;
	$tax_row = array();
	foreach ($voucher_details as $vjd) {
		if ($vjd['amount'] == 0) continue;
		
		if (isset($service_taxes[$vjd['bill_item_id']])) {
			if (strlen(trim($voucher['narration'])) > 0) {
				echo '
<tr>
	<td>&nbsp;</td>
	<td><span class="tiny">' . $voucher['narration'] . '</span></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>';
				$lines++;
			}

			$tax_row[] = '<tr>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td colspan="2" class="alignright nowrap"><span class="tiny">' . $vjd['tax_calculation'] . '</span></td>
	<td class="alignright">' . inr_format($vjd['amount'], 2, '.', '') . '</td>
</tr>';
		}
		else {
			$lines++;
			echo '<tr>
	<td class="aligncenter">' . $i++ . '</td>
	<td class="Particular">' . $vjd['particulars'] . '</td>
	<td class="alignright">' . $vjd['units'] . '</td>
	<td class="alignright">' . $vjd['rate'] . '</td>
	<td class="alignright">' . inr_format($vjd['amount'], 2, '.', '') . '</td>
</tr>';
		}
		
		$GrandTotal = bcadd($GrandTotal, $vjd['amount'], 2);
	}

	for(; $lines <= $max_items - count($tax_row); $lines++) {
		echo '<tr><td style="color: #fff">.</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>';
	}
	echo join($tax_row);

	if (strlen(trim($voucher['remarks'])) > 0) {
		echo '
<tr>
	<td>&nbsp;</td>
	<td colspan="3"><pre class="tiny">' . $voucher['remarks'] . '</pre></td>
	<td>&nbsp;</td>
</tr>';
		$lines++;
	}

	$nettotal = round($GrandTotal, 2);
	$nettotal_roff = round($nettotal, 0);
	$roundoff = round($nettotal_roff - $nettotal, 2);
	if ($roundoff != 0) {
		echo '
<tr>
	<td>&nbsp;</td>
	<td colspan="3" class="alignright tiny">Round Off (+/-) :</td>
	<td class="alignright">' . inr_format($roundoff, 2,'.','') . '</td>
</tr>';
		$lines++;
	}

?>
<tr>
	<td colspan="4"><span class="box_label">Total Amount Chargeable (in words)</span><br /><?php echo numberToWords($nettotal_roff) ?></td>
	<td class="nowrap"><span class="box_label">Total</span><br /><div class="big alignright"><img src="<?php echo base_url('assets/images/rupee.png') ?>" class="alignbottom" /> <?php echo inr_format($nettotal_roff, 2,'.','') ?></div></td>
</tr>
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
echo $footer;
?>

</body>
</html>