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
<h4>Container Inward Bill</h4>
<table class="header">
<tr>
	<td width="20%"><span class="box_label">Rishi Bill No</span><br /><?php echo $bills['rishi_bill_no'] ?></td>
	<td width="20%"><span class="box_label">Bill Date</span><br /><?php echo $bills['date'] ?></td>	
	<td width="20%"><span class="box_label">Cheque No</span><br /><?php echo $bills['cheque_no'] ?></td>
	<td width="20%"><span class="box_label">Cheque Date</span><br /><?php echo $bills['cheque_date'] ?></td>
	<td width="20%"><span class="box_label">Processed Date</span><br /><?php echo $bills['processed_date'] ?></td>
</tr>

<tr>
	<td width="20%"><span class="box_label">Party Bill No</span><br /><?php echo $bills['bill_no'] ?></td>
	<td colspan="3"><span class="box_label">Party Name</span><br /><?php echo $transporter_ledger['name'] ?></td>
	<td width="20%"><span class="box_label">Party PAN No</span><br /><?php echo $transporter_ledger['pan_no'] ?></td>
</tr>
</table>

<table class="details">
<tr>
	<th>No</th>
	<th>Container No</th>
	<th>Job No</th>
	<th>Trailer No</th>
	<th>Transporter</th>
	<th>Trip Date</th>
	<th>Party Name</th>
	<th>From</th>
	<th>To</th>
	<?php 
	if ($bills['type'] == 'Transporter') {
		echo '<th>Billed</th>
		<th>Freight</th>
		<th>Fuel</th>
		<th>Cash</th>
		<th>Cheque</th>
		<th>Amount</th>';
	}
	else {
		echo '<th>Fuel</th>';
	}
	?>
</tr>

<tbody class="tiny">
<?php
$i  = 1;
$total = array(
	'transporter_rate' => 0, 
	'fuel'             => 0, 
	'advance'          => 0, 
	'cheque_advance'   => 0, 
	'advance'          => 0
);
foreach ($rows as $r) {
	$total['fuel']    += $r['fuel'];
	if ($bills['type'] == 'Transporter') {
		$total['transporter_rate'] += $r['transporter_rate'];
		$total['advance']          += $r['advance'];
		$total['cheque_advance']   += $r['cheque_advance'];
		$total['amount']            = bcadd($total['amount'], $r['amount'], 2);
	}
	echo '<tr class="Details">
		<td class="aligncenter">' . $i++ . '</td>
		<td>' . $r['container_no'] . '<br />' . $r['qty'] . '</td>
		<td>' . $r['job_no'] . '</td>
		<td>' . ($r['self'] ? '<span class="bold">' . $r['registration_no'] . '</span>' : $r['registration_no']) . '</td>
		<td>' . $r['transporter_name'] . '</td>
		<td>' . $r['date'] . '</td>
		<td>' . $r['party_name'] . '</td>
		<td>' . $r['from_location'] . '</td>
		<td>' . $r['to_location'] . '</td>';
		if ($bills['type'] == 'Transporter') {
			echo '<td class="alignright">' . $r['party_rate'] . '</td>
			<td class="alignright">' . $r['transporter_rate'] . '</td>
			<td class="alignright">' . $r['fuel'] . '</td>
			<td class="alignright">' . $r['advance'] . '</td>
			<td class="alignright">' . $r['cheque_advance'] . '</td>
			<td class="alignright">' . inr_format($r['amount']) . '</td>';
		}
		else {
			echo '<td class="alignright">' . $r['fuel'] . '</td>';
		}
	'</tr>';
}

foreach ($odetails as $r) {
	// $lines++;
	// $j++;

	echo '<tr>
		<td class="aligncenter">' . $i++ . '</td>
		<td colspan="13">' . $r['particulars'] . '</td>
		<td class="tiny alignright">' . inr_format($r['amount']) . '</td>
	</tr>';
	$total['amount'] = bcadd($total['amount'], $r['amount'], 2);

	// if (($max_items - $j) <= 5) {
	// 	if ($j % $hard_limit == 0 && $lines < $total_lines) {
	// 		$j = 0;
	//		echo '<tr><td colspan="9" class="alignright">P.T.O...</td></tr></table><div class="page-break"></div>' . $header;
	// 	}
	// }
	// else if (($max_items - $j) < 5 && $j % $max_items == 0 && $lines < $total_lines) {
	// 	$j = 0;
	// 	echo '<tr><td colspan="9" class="alignright">P.T.O...</td></tr></table><div class="page-break"></div>' . $header;
	// }
}

?>

<tfoot>
<?php
if($bills['type'] == 'Transporter') {
	$net_amount = $total['amount'];
	echo '<tr>
		<th colspan="10" class="alignright">Total</th>
		<th class="alignright">'. $total['transporter_rate'] .'</th>
		<th class="alignright">'. $total['fuel'] .'</th>
		<th class="alignright">'. $total['advance'] .'</th>
		<th class="alignright">'. $total['cheque_advance'] .'</th>
		<th class="alignright">'. inr_format($total['amount']) .'</th>
	</tr>

	<tr>
		<th colspan="14" class="alignright">NET Total</th>
		<th class="alignright">'. inr_format(round($net_amount, 0)) .'</th>
	</tr>';
}
else {
	echo '<tr>
		<th colspan="' . ($bills['type'] == 'Transporter' ? 15 : 9) . '" class="alignright">Total</th>
		<th class="alignright">'. $total['fuel'] .'</th>
	</tr>';
}
?>
<tr>
	<td colspan="<?php echo ($bills['type'] == 'Transporter' ? 15 : 10) ?>"><span class="box_label">Amount in Words</span><br /><span class="tiny"><?php echo numberToWords(round(($bills['type'] == 'Transporter' ? round($net_amount, 0) : $total['fuel']), 0)) ?></span></td>
</tr>
<tr>
	<td colspan="<?php echo ($bills['type'] == 'Transporter' ? 15 : 10) ?>"><span class="box_label">Remarks</span><br /><span class="tiny"><?php echo $bills['remarks'] ?></span></td>
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