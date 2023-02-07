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

<?php if ($voucher['category'] == 'Journal') : ?>
<table class="details">
<thead>
<tr>
	<th>Voucher No</th>
	<th>Date</th>
	<th>Bill No</th>
	<th>Bill Date</th>
	<th>Amount</th>
	<th>TDS</th>
	<th width="100px">Paid Amount</th>
	<th>Balance</th>
</tr>
</thead>

<tbody class="tiny">
<?php 
	$total = array(
		'due_amount'  => 0,
		'tds_amount'  => 0,
		'paid_amount' => 0,
		'balance'     => 0,
	);
	foreach ($journals as $r) {
		$total['due_amount']  = bcadd($total['due_amount'], $r['due_amount'], 2);
		$total['tds_amount']  = bcadd($total['tds_amount'], $r['tds_amount'], 2);
		$total['paid_amount'] = bcadd($total['paid_amount'], $r['paid_amount'], 2);
		$total['balance']     = bcadd($total['balance'], $r['balance'], 2);

		echo '<tr>
	<td class="aligncener tiny">' . $r['id2_format'] . '</td>
	<td class="aligncener tiny">' . $r['voucher_date'] . '</td>
	<td class="aligncener tiny">' . $r['invoice_no'] . '</td>
	<td class="aligncener tiny">' . $r['invoice_date'] . '</td>
	<td class="alignright tiny">' . $r['due_amount'] . '</td>
	<td class="alignright tiny">' . $r['tds_amount'] . '</td>
	<td class="alignright tiny">' . $r['paid_amount'] . '</td>
	<td class="alignright tiny">' . $r['balance'] . '</td>
</tr>';
	}
?>
</tbody>

<tfoot>
<tr>
	<th colspan="4" class="alignright">Total</th>
	<th class="alignright"><?php echo $total['due_amount'] ?></th>
	<th class="alignright"><?php echo $total['tds_amount'] ?></th>
	<th class="alignright"><?php echo $total['paid_amount'] ?></th>
	<th class="alignright"><?php echo $total['balance'] ?></th>
</tr>

<tr>
	<td colspan="7"><span class="box_label">Amount in Words</span><br /><?php echo numberToWords($voucher['amount']) ?></td>
	<td><span class="box_label">Amount</span><br /><?php echo $voucher['amount'] ?></td>
</tr>
</tfoot>
</table>

<?php endif;


if ($voucher['category'] == 'Invoices') : ?>
<table class="details">
<thead>
<tr>
	<th>Invoice No</th>
	<th>Date</th>
	<th>Party</th>
	<th>Job No</th>
	<th>Amount</th>
	<th width="100px">Paid Amount</th>
</tr>
</thead>

<tbody class="tiny">
<?php 
	$total = array(
		'amount'      => 0,
		'paid_amount' => 0,
	);
	foreach ($invoices as $r) {
		$total['amount']      = bcadd($total['amount'], $r['amount'], 2);
		$total['paid_amount'] = bcadd($total['paid_amount'], $r['paid_amount'], 2);

		echo '<tr>
	<td class="aligncener tiny">' . $r['id2_format'] . '</td>
	<td class="aligncener tiny">' . $r['voucher_date'] . '</td>
	<td class="aligncener tiny">' . $r['party_name'] . '</td>
	<td class="aligncener tiny">' . $r['job_no'] . '</td>
	<td class="alignright tiny">' . $r['amount'] . '</td>
	<td class="alignright tiny">' . $r['paid_amount'] . '</td>
</tr>';
	}
?>
</tbody>

<tfoot>
<tr>
	<th colspan="4" class="alignright">Total</th>
	<th class="alignright"><?php echo $total['amount'] ?></th>
	<th class="alignright"><?php echo $total['paid_amount'] ?></th>
</tr>

<tr>
	<td colspan="5"><span class="box_label">Amount in Words</span><br /><?php echo numberToWords($voucher['amount']) ?></td>
	<td><span class="box_label">Amount</span><br /><?php echo $voucher['amount'] ?></td>
</tr>
</tfoot>
</table>

<?php endif;


if ($voucher['category'] == 'Pumps') : ?>
<table class="details">
<thead>
<tr>
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
</thead>

<tbody class="tiny">
<?php 
	$total = array(
		'pump_advance'  => 0,
	);
	foreach ($pumps as $r) {
		$total['pump_advance']  = bcadd($total['pump_advance'], $r['pump_advance'], 2);

		echo '<tr>
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
</tbody>

<tfoot>
<tr>
	<th colspan="12" class="alignright">Total</th>
	<th class="alignright"><?php echo $total['pump_advance'] ?></th>
</tr>

<tr>
	<td colspan="6"><span class="box_label">Amount in Words</span><br /><?php echo numberToWords($voucher['amount']) ?></td>
	<td><span class="box_label">Amount</span><br /><?php echo $voucher['amount'] ?></td>
</tr>
</tfoot>
</table>

<?php endif;


if ($voucher['category'] == 'Trips') : ?>
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
	<td colspan="14"><span class="box_label">Amount in Words</span><br /><?php echo numberToWords($voucher['amount']) ?></td>
	<td><span class="box_label">Amount</span><br /><?php echo $voucher['amount'] ?></td>
</tr>
</tfoot>
</tbody>
</table>

<?php endif; ?>


<table class="details">
<tr>
	<td width="33%"><span class="box_label">Prepared By</span><br />&nbsp;<br />&nbsp;<br />&nbsp;</td>
	<td width="34%"><span class="box_label">Checked By</span><br />&nbsp;<br />&nbsp;<br />&nbsp;</td>
	<td width="33%"><span class="box_label">Authorized By</span><br />&nbsp;<br />&nbsp;<br />&nbsp;</td>
</tr>
</table>
</body>
</html>