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
<?php if ($letterhead) : 
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
else : ?>
<br /><br /><br /><br /><br /><br />
<?php endif; ?>

<h3 class="aligncenter"><?php echo $page_title ?></h3>

<table class="header">
<tr>
	<td width="60%"><span class="box_label">To</span><br />
		<?php if ($invoice_type == 'credit_note') {
			echo $voucher['credit_party_name'] . '<br />' . (isset($voucher['credit_party_address']) ? $voucher['credit_party_address'] : '');
		}
		else {
			echo $party['name'] . '<br />' . $party['address'];
		}
		?>
	</td>
	<td width="20%"><span class="box_label"><?php echo $page_title ?> No</span><br /><?php echo $voucher['id2_format'] ?></td>
	<td width="20%"><span class="box_label">Date</span><br /><?php echo $voucher['date'] ?></td>
</tr>
</table>

<table class="details">
<tr>
	<th width="24px">No</th>
	<th colspan="2">Particulars</th>
	<th width="120px">Net Total</th>
</tr>

<?php
	$buffer = '<table class="details">
<tr>
	<th>Sr No</th>
	<th>Date</th>
	<th>Lr No</th>
	<th>Gatepass No</th>
	<th>Vehicle No</th>
	<th>Rate</th>
	<th>Pcs</th>
	<th>CBM</th>
	<th>Amount</th>
	<th>Advance</th>
	<th>Balance</th>
	<th>Total</th>
</tr>
';

	$i            = 1;
	$GrandTotal   = 0;
	$group        = array(
		'trips'   => 0,
		'amount'  => 0,
		'advance' => 0,
		'balance' => 0,
	);
	$total = array(
		'trips'   => 0,
		'amount'  => 0,
		'advance' => 0,
		'balance' => 0,
	);
	$other_row = array();
	$tax_row   = array();
	foreach ($voucher_details['trips'] as $vessel_name => $vessels) {
		$buffer .= '<tr>
			<td>&nbsp;</td>
			<td colspan="10" class="bold">' . $vessel_name . '</td>
			<td class="alignright"></td>
		</tr>';

		foreach ($vessels as $location_name => $v) {
			$buffer .= '<tr>
				<td>&nbsp;</td>
				<td colspan="10" class="bold">' . $location_name . '</td>
				<td class="alignright"></td>
			</tr>';

			foreach ($v as $vjd) {
				if ($vjd['amount'] == 0) continue;
		
				if (isset($service_taxes[$vjd['bill_item_id']])) {
					$tax_row[] = '<tr>
						<td>&nbsp;</td>
						<td colspan="9"><span class="box_label">' . $vjd['particulars'] . '</span></td>
						<td class="alignright nowrap"><span class="tiny">' . $vjd['tax_calculation'] . '</span></td>
						<td class="alignright">' . inr_format($vjd['amount'], 2, '.', '') . '</td>
					</tr>';
				}
				else {
					$lines++;

					$group['trips']  += $vjd['trips'];
					$group['amount']  = bcadd($group['amount'], $vjd['amount']);
					$group['advance'] = bcadd($group['advance'], $vjd['party_advance']);
					$group['balance'] = bcadd($group['balance'], $vjd['balance']);
					$buffer .= '<tr>
						<td class="aligncenter">' . $i++ . '</td>
						<td class="nowrap">' . $vjd['date'] . '</td>
						<td class="nowrap">' . $vjd['lr_no'] . '</td>
						<td class="nowrap">' . $vjd['gatepass_no'] . '</td>
						<td class="nowrap">' . $vjd['registration_no'] . '</td>
						<td class="alignright">' . $vjd['rate'] . '</td>
						<td class="alignright">' . $vjd['units'] . '</td>
						<td class="alignright">' . $vjd['quantity'] . '</td>
						<td class="alignright">' . $vjd['amount'] . '</td>
						<td class="alignright">' . $vjd['party_advance'] . '</td>
						<td class="alignright">' . $vjd['balance'] . '</td>
						<td></td>
					</tr>';
				}
			}
			$buffer .= '<tr>
				<td>&nbsp;</td>
				<td colspan="3" class="alignright bold">Sub Total</td>
				<td class="alignright bold">' . $group['trips'] . '</td>
				<td class="alignright bold"></td>
				<td class="alignright bold"></td>
				<td class="alignright bold"></td>
				<td class="alignright bold">' . $group['amount'] . '</td>
				<td class="alignright bold">' . $group['advance'] . '</td>
				<td class="alignright bold"></td>
				<td class="alignright bold">' . $group['balance'] . '</td>
			</tr>';
				$group = array(
					'trips'   => 0,
					'amount'  => 0,
					'advance' => 0,
					'balance' => 0,
				);
		}
		
		$GrandTotal   = bcadd($GrandTotal, $vjd['amount'], 2);
		$prev_vessel  = $vjd['vessel_name'];
		$prev_product = $vjd['product_name'];
	}

	foreach ($voucher_details['details'] as $vjd) {
		if (isset($service_taxes[$vjd['bill_item_id']])) {
			$tax_row[] = '<tr>
				<td>&nbsp;</td>
				<td><span class="box_label">' . $vjd['particulars'] . '</span></td>
				<td class="alignright nowrap"><span class="tiny">' . $vjd['tax_calculation'] . '</span></td>
				<td class="alignright">' . inr_format($vjd['amount'], 2, '.', '') . '</td>
			</tr>';
		}
		$buffer .= '<tr>
			<td class="aligncenter">' . $i++ . '</td>
			<td colspan="11" class="Particular">' . $vjd['particulars'] . '</td>
			<td class="alignright">' . inr_format($vjd['amount'], 2, '.', '') . '</td>
		</tr>';
	}

	echo '<tr><td>1</td><td colspan="2">Being Transportation charges as per list attached</td><td class="alignright">' . inr_format($GrandTotal) . '</td></tr>';
	for(; $lines <= $max_items - count($tax_row); $lines++) {
		echo '<tr><td style="color: #fff">.</td><td>&nbsp;</td><td>&nbsp;</td></tr>';
	}
	echo join($tax_row);

	if (strlen(trim($voucher['narration'])) > 0) {
		echo '
<tr>
	<td>&nbsp;</td>
	<td colspan="2"><pre class="tiny">' . $voucher['narration'] . '</pre></td>
	<td>&nbsp;</td>
</tr>';
	}

	if (strlen(trim($voucher['remarks'])) > 0) {
		echo '
<tr>
	<td>&nbsp;</td>
	<td colspan="2"><pre class="tiny">' . $voucher['remarks'] . '</pre></td>
	<td>&nbsp;</td>
</tr>';
	}

	$nettotal = round($GrandTotal, 2);
	$nettotal_roff = round($nettotal, 0);
	$roundoff = round($nettotal_roff - $nettotal, 2);
	if ($roundoff != 0) {
		echo '
<tr>
	<td>&nbsp;</td>
	<td class="alignright tiny">Round Off (+/-) :</td>
	<td class="alignright">' . inr_format($roundoff, 2,'.','') . '</td>
</tr>';
		$lines++;
	}

echo '<tr>
	<td colspan="3"><span class="box_label">Total Amount Chargeable (in words)</span><br />' . numberToWords($nettotal_roff) . '</td>
	<td class="nowrap"><span class="box_label">Total</span><br /><div class="big alignright"><img src="' . base_url('images/rupee.png') . '" class="alignbottom" /> ' . inr_format($nettotal_roff, 2,'.','') . '</div></td>
</tr>
</table>';
?>

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

echo '<div class="page-break"></div>

<table class="header">
<tr>
	<td width="50%"><span class="box_label">' . $page_title . ' No</span><br />' . $voucher['id2_format'] . '</td>
	<td width="50%"><span class="box_label">Date</span><br />' . $voucher['date'] . '</td>
</tr>
</table>

';

echo $buffer . '</tbody></table>';
?>

</body>
</html>