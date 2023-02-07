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
		if ($company['id'] == 2)
			echo '<td><img src="data:image/png;base64,' . base64_encode($image_data) . '" height="80px" /></td>';
		else {
			echo '<td width="75%"><img src="data:image/png;base64,' . base64_encode($image_data) . '" height="100px" /></td>';
			echo '<td class="alignright" valign="top" nowrap="true"><span class="tiny">' . $company['address'] . '<br />' . (isset($city['name']) ? $city['name'] . '-' . $city['pincode'] . ' ' . $state['name'] . '<br />' : '') . $company['contact'] . '<br />' . $company['email'] . '</span></td>';
		}
	}
	else {
		echo '<td><h2>' . $company['name'] . '</h2></td>
	<td class="alignright" valign="top" nowrap="true"><span class="tiny">' . $company['address'] . '<br />' . (isset($city['name']) ? $city['name'] . '-' . $city['pincode'] . ' ' . $state['name'] . '<br />' : '') . $company['contact'] . '<br />' . $company['email'] . '</span></td>';
	}
	?>
</tr>
</table>

<h3 class="aligncenter"><?php echo $page_title ?></h3>

<table class="header">

<tr>
	<td width="50%" colspan="2"><span class="box_label"><?php echo 'IMPORTER' ?></span><br /><strong><?php echo $party['name']; ?></strong></td>
	<td colspan="3"><span class="box_label"><?php echo 'HIGH SEAS' ?></span><br />
		<strong><?php 
		foreach ($high_seas as $r) {
			echo $r['name'] . '<br />';
		}
		?></strong></td>
</tr>

<tr>
	<td colspan="2"><span class="box_label">VESSEL NAME</span><br /><strong><?php echo (isset($vessel['name']) ? $vessel['name'] . ' - ' . $vessel['voyage_no'] : '&nbsp;') ?></strong></td>	
	<td><span class="box_label">B/L No</span><br /><?php echo $job['bl_no'] ?></td>
	<td colspan="2"><span class="box_label">B/L Date</span><br /><?php echo $job['bl_date'] ?></td>
	<!-- <td colspan="2"><span class="box_label">B/E No &amp; Date</span><br /><?php echo $job['be_no'] . ' / ' . $job['be_date'] ?></td> -->
</tr>

<tr>
	<td><span class="box_label">VESSEL NAME</span><br /><strong><?php echo (isset($vessel['name']) ? $vessel['name'] . ' - ' . $vessel['voyage_no'] : '&nbsp;') ?></strong></td>
	<td></td>
	<td><span class="box_label">B/E No</span><br /><?php echo $job['be_no'] ?></td>
	<td colspan="2"><span class="box_label">B/E Date</span><br /><?php echo $job['be_date'] ?></td>	
</tr>

<tr>
	<td colspan="2"><span class="box_label">Description of Goods</span><br /><?php echo $voucher['product_details'] ?></td>
	<td><span class="box_label"><?php echo $job['net_weight_unit'] ?></span><br /><?php echo $job['net_weight'] ?></td>
<?php 
	if ($job['cargo_type'] == 'Bulk') {
		echo '<td><span class="box_label">' . $package_type . '</span><br />' . $voucher['pieces'] . '</td>';
		echo '<td><span class="box_label">' . ($job['net_weight_unit'] != 'CBM' ? 'CBM' : '') . '</span><br />' . ($job['net_weight_unit'] != 'CBM' ? $voucher['cbm'] : '') . '</td>';
	}
	else
		echo '<td width="15%"><span class="box_label">' . $package_type . '</span><br />' . $job['packages'] . '</td>
	<td width="15%"><span class="box_label">Containers</span><br />' . ($job['container_20'] > 0 ? '(' . $job['container_20'] . 'x20) ' : NULL) . ($job['container_40'] > 0 ? ' (' . $job['container_40'] . 'x40)' : NULL) . '</td>';
?>
</tr>
</table>

<table class="details">
<tr>
<?php if($job['cargo_type'] == 'Container') {
	echo '<td width="160px" rowspan="' . ($max_items+5) . '" class="tiny"><span class="box_label">Container Number(s)</span><br />
	<table class="noborder">
	<tr>
	<td>'; 
	$i = 1;
	foreach ($containers as $c) {
		if ($i > 30) {
			$i = 1;
			echo '</td><td>';
		}
		echo $c['number'] . '<br />';
		$i++;
	}
	echo '
	</td>
	</tr>
	</table>
	</td>';
}
?>
	<th width="24px">No</th>
	<th colspan="2">Particulars</th>
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
			$tax_row[] = '<tr>
	<td>&nbsp;</td>
	<td><span class="tiny"><span class="box_label">' . $vjd['particulars'] . '</span></span></td>
	<td class="alignright nowrap"><span class="tiny">' . $vjd['tax_calculation'] . '</span></td>
	<td class="alignright">' . inr_format($vjd['amount'], 2, '.', '') . '</td>
</tr>';
		}
		else {
			$lines++;
			echo '<tr>
	<td class="aligncenter">' . $i++ . '</td>
	<td colspan="2" class="Particular">' . $vjd['particulars'] . '</td>
	<td class="alignright">' . inr_format($vjd['amount'], 2, '.', '') . '</td>
</tr>';
		}
		
		$GrandTotal = bcadd($GrandTotal, $vjd['amount'], 2);
	}

	for(; $lines <= $max_items - count($tax_row); $lines++) {
		echo '<tr><td style="color: #fff">.</td><td colspan="2">&nbsp;</td><td>&nbsp;</td></tr>';
	}
	echo join($tax_row);

	if (strlen(trim($voucher['narration'])) > 0) {
		echo '
<tr>
	<td>&nbsp;</td>
	<td colspan="2"><pre class="tiny">' . $voucher['narration'] . '</pre></td>
	<td>&nbsp;</td>
</tr>';
		$lines++;
	}

	if (strlen(trim($voucher['remarks'])) > 0) {
		echo '
<tr>
	<td>&nbsp;</td>
	<td colspan="2"><pre class="tiny">' . $voucher['remarks'] . '</pre></td>
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
	<td colspan="2" class="alignright tiny">Round Off (+/-) :</td>
	<td class="alignright">' . inr_format($roundoff, 2,'.','') . '</td>
</tr>';
		$lines++;
	}

?>
<tr>
	<td colspan="3"><span class="box_label">Total Amount Chargeable (in words)</span><br /><?php echo numberToWords($nettotal_roff) ?></td>
	<td class="nowrap"><span class="box_label">Total</span><br /><div class="big alignright"><img src="<?php echo base_url('assets/images/rupee.png') ?>" class="alignbottom" /> <?php echo inr_format($nettotal_roff, 2,'.','') ?></div></td>
</tr>
</table>

<table class="details">
<tr>
	<td width="30%"><span class="box_label">Service Tax No</span><br /><?php echo $company['service_tax_no']; ?></td>
	<td width="30%"><span class="box_label">PAN No</span><br /><?php echo $company['pan_no']; ?></td>
	<td class="alignright tiny nowrap" valign="bottom" rowspan="2">For <?php echo $company['name'] ?><br /><br /><br /><br />Authorised Signatory</td>
</tr>
<tr>
	<td width="60%" colspan="2"><p class="tiny">Subject To Gandhidham Jurisdiction<br />E. &amp; O. E.</p></td>
</tr>
</table>

</body>
</html>