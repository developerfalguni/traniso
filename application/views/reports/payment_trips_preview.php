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
	<th>Container No</th>
	<th>Qty</th>
	<th>Job No</th>
	<th>Trailer No</th>
	<th>Transporter</th>
	<th>Trip Date</th>
	<th>Party Name</th>
	<th>Cargo</th>
	<th>From</th>
	<th>To</th>
	<th>Rate</th>
	<th>Advance</th>
	<th>Balance</th>
</tr>

<tbody class="tiny">
<?php
$i  = 1;
$total = array(
	'pump_advance' => 0,
	'amount'       => 0,
	'balance'      => 0,
);

foreach ($trips as $r) {
	$total['pump_advance'] += $r['pump_advance'];
	$total['amount']       += $r['transporter_rate'];
	$total['balance']      += $r['balance'];

	echo '<tr class="Details">
		<td class="aligncenter">' . $i++ . '</td>
		<td>' . $r['container_no'] . '</td>
		<td>' . $r['container_size'] . '</td>
		<td>' . $r['job_no'] . '</td>
		<td>' . ($r['self'] ? '<span class="bold">' . $r['registration_no'] . '</span>' : $r['registration_no']) . '</td>
		<td>' . $r['transporter_name'] . '</td>
		<td>' . $r['date'] . '</td>
		<td>' . $r['party_name'] . '</td>
		<td>' . $r['product_name'] . '</td>
		<td>' . $r['from_location'] . '</td>
		<td>' . $r['to_location'] . '</td>
		<td class="alignright">' . $r['transporter_rate'] . '</td>
		<td class="alignright">' . $r['pump_advance'] . '</td>
		<td class="alignright">' . $r['balance'] . '</td>
	</tr>';
}
?>

<tfoot>
<tr>
	<th colspan="11" class="alignright">Total</th>
	<th class="alignright"><?php echo $total['amount'] ?></th>
	<th class="alignright"><?php echo $total['pump_advance'] ?></th>
	<th class="alignright"><?php echo $total['balance'] ?></th>
</tr>
<tr>
	<td colspan="15"><span class="box_label">Amount in Words</span><br /><span class="tiny"><?php echo numberToWords(round($total['balance'], 0)) ?></span></td>
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