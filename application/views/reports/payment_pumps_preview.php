<html>
<head>
	<title>Bill Summary</title>
	<style>
		<?php echo file_get_contents(FCPATH.'assets/css/print.css'); ?>
	</style>
</head>

<body>

<h2><?php echo $company['name'] ?></h2>
<br />
<h4><?php echo $page_title ?></h4>
<table class="header">
<tr>
	<td width="20%"><span class="box_label">Rishi Bill No</span><br /><?php echo $voucher['id2_format'] ?></td>
	<td width="20%"><span class="box_label">Bill Date</span><br /><?php echo $voucher['date'] ?></td>	
	<td width="20%"><span class="box_label">Cheque No</span><br /><?php echo $voucher['cheque_no'] ?></td>
	<td width="20%"><span class="box_label">Cheque Date</span><br /><?php echo $voucher['cheque_date'] ?></td>
	<td width="20%"><span class="box_label">Processed Date</span><br /><?php echo $voucher['processed_date'] ?></td>
</tr>

<tr>
	<td width="20%"><span class="box_label">Party Bill No</span><br /><?php echo $voucher['invoice_no'] ?></td>
	<td colspan="3"><span class="box_label">Party Name</span><br /><?php echo $party['name'] ?></td>
	<td width="20%"><span class="box_label">Party PAN No</span><br /><?php echo $party['pan_no'] ?></td>
</tr>
</table>

<table class="details">
<tr>
	<th>No</th>
	<th>Date</th>
	<th>Job No</th>
	<th>Container No</th>
	<th>Size</th>
	<th>Vehicle No</th>
	<th>Party / <span class="orange">Party Ref. No</span></th>
	<th>Transporter</th>
	<th>Rate</th>
	<th>LR No</th>
	<th>Pump</th>
	<th>Slip No</th>
	<th>Pump Adv.</th>
</tr>

<tbody class="tiny">
<?php
$i  = 1;
$total = array(
	'pump_advance' => 0,
	'amount'       => 0,
);

foreach ($pumps as $r) {
	$total['pump_advance'] += $r['pump_advance'];
	$total['amount']       += $r['transporter_rate'];

	echo '<tr class="Details">
		<td class="aligncenter">' . $i++ . '</td>
		<td class="tiny">' . $r['date'] . '</td>
		<td class="tiny">' . $r['job_no'] . '</td>
		<td class="tiny">' . $r['container_no'] . '</td>
		<td class="tiny">' . $r['container_size'] . '</td>
		<td class="tiny">' . ($r['self'] ? '<span class="label label-success">' . $r['registration_no'] . '</span>' : $r['registration_no']) . '</td>
		<td class="tiny">' . $r['party_name'] . '<br /><span class="orange">' . $r['party_reference_no'] . '</span></td>
		<td class="tiny">' . $r['transporter_name'] . '</td>
		<td class="tiny">' . $r['transporter_rate'] . '</td>
		<td class="tiny">' . $r['lr_no'] . '</td>
		<td class="tiny">' . $r['pump_agent'] . '</td>
		<td class="tiny">' . $r['slip_no'] . '</td>
		<td class="tiny">' . $r['pump_advance'] . '</td>
	</tr>';
}
?>

<tfoot>
<tr>
	<th colspan="11" class="alignright">Total</th>
	<th class="alignright"><?php echo $total['amount'] ?></th>
	<th class="alignright"><?php echo $total['pump_advance'] ?></th>
</tr>
<tr>
	<td colspan="15"><span class="box_label">Amount in Words</span><br /><span class="tiny"><?php echo numberToWords(round($total['pump_advance'], 0)) ?></span></td>
</tr>
</tfoot>
</tbody>
</table>

<table class="details">
<tr>
	<td width="33%"><span class="box_label">Prepared By</span><br />&nbsp;<br />&nbsp;<br />&nbsp;</td>
	<td width="34%"><span class="box_label">Checked By</span><br />&nbsp;<br />&nbsp;<br />&nbsp;</td>
	<td width="33%"><span class="box_label">Authorized By</span><br />&nbsp;<br />&nbsp;<br />&nbsp;</td>
</tr>
</table>
</body>
</html>