<!DOCTYPE html>
<html lang="en">
<head>
	<title><?php echo $page_title ?></title>
	<style>
		<?php echo file_get_contents(FCPATH.'assets/css/print.css'); ?>
	
		body { font-family: "Times New Roman", serif; }
		.tiny { font-size: 0.7em !important; }
	</style>
</head>

<body>
<h2 class="aligncenter"><?php echo $company['name'] ?></h2>
<h4 class="aligncenter"><?php echo $page_title . '<br />' . $page_desc ?></h4>

<table class="details">
<thead>
<tr>
	<th>No</th>
	<th>Type</th>
	<th>Job No</th>
	<th>Importer</th>
	<th>C.20</th>
	<th>C.40</th>
	<th>D.Note Dt</th>
	<th>D.Note Amount</th>
	<th>Inv Date</th>
	<th>Inv Amount</th>
	<th>S.Tax</th>
	<th>Tpt Invoice </th>
	<th>Job Amount</th>
	<th>Expenses</th>
	<th>Tpt Payment</th>
	<th>Balance</th>
</tr>
</thead>

<tbody>
<?php 
$i = 1;
$total = array(
	'container_20' => 0,
	'container_40' => 0,
	'debit_note'   => 0,
	'invoice'      => 0,
	'tpt_invoice'  => 0,
	'job_amount'   => 0,
	'expenses'     => 0,
	'tpt_payment'  => 0,
	'net_profit'   => 0,
);
foreach ($rows as $r) {

	$job_amount = ($r['debit_note']['amount'] + $r['invoice']['amount'] + $r['transportation']['amount']);
	
	if ($debit_note == 2 && $r['debit_note']['amount'] == 0) {
		continue;
	}
	else if ($debit_note == 1 && $r['debit_note']['amount'] > 0) {
		continue;
	}
	else if ($invoice == 2 && $r['invoice']['amount'] == 0) {
		continue;
	}
	else if ($invoice == 1 && $r['invoice']['amount'] > 0) {
		continue;
	}
	else if ($tpt_invoice == 2 && $r['transportation']['amount'] == 0) {
		continue;
	}
	else if ($tpt_invoice == 1 && $r['transportation']['amount'] > 0) {
		continue;
	}
	else if ($jobamt == 2 && $job_amount == 0) {
		continue;
	}
	else if ($jobamt == 1 && $job_amount > 0) {
		continue;
	}
	else if ($expenses == 2 && $r['expenses'] == 0) {
		continue;
	}
	else if ($expenses == 1 && $r['expenses'] > 0) {
		continue;
	}
	else if ($payment == 2 && $r['payment'] == 0) {
		continue;
	}
	else if ($payment == 1 && $r['payment'] > 0) {
		continue;
	}

	$net_profit = $job_amount - ($r['expense'] + $r['payment']);

	$total['container_20'] += $r['container_20'];
	$total['container_40'] += $r['container_40'];
	$total['invoice']      += $r['invoice']['amount'];
	$total['debit_note']   += $r['debit_note']['amount'];
	$total['tpt_invoice']  += $r['transportation']['amount'];
	$total['job_amount']   += $job_amount;
	$total['expenses']     += $r['expense'];
	$total['tpt_payment']  += $r['payment'];
	$total['net_profit']   += $net_profit;

	echo '<tr class="JobRow">
	<td class="aligncenter tiny">' . $i++ . '</td>
	<td class="tiny">' . $r['type'] . '</td>
	<td class="aligncenter tiny">' . $r['id2_format'] . '</td>
	<td class="tiny">' . $r['party_name'] . '</td>
	<td class="aligncenter tiny">' . $r['container_20'] . '</td>
	<td class="aligncenter tiny">' . $r['container_40'] . '</td>
	<td class="tiny">' . $r['debit_note']['no_date'] . '</td>
	<td class="alignright tiny">' . $r['debit_note']['amount'] . '</td>
	<td class="tiny">' . $r['invoice']['no_date'] . '</td>
	<td class="alignright tiny">' . $r['invoice']['amount'] . '</td>
	<td class="alignright tiny">' . $r['invoice']['stax_amount'] . '</td>
	<td class="alignright tiny">' . $r['transportation']['amount'] . '</td>
	<td class="alignright tiny">' . $job_amount . '</td>
	<td class="alignright tiny">' . $r['expense'] . '</td>
	<td class="alignright tiny">' . $r['payment'] . '</td>
	<td class="alignright tiny">' . $net_profit . '</td>
</tr>';
} 
?>
</tbody>
<tfoot>
	<tr>
		<th class="alignright" colspan="4">Total</th>
		<th class="alignright"><?php echo $total['container_20']; ?></th>
		<th class="alignright"><?php echo $total['container_40']; ?></th>
		<th></th>
		<th class="alignright"><?php echo $total['debit_note']; ?></th>
		<th></th>
		<th class="alignright"><?php echo $total['invoice']; ?></th>
		<th></th>
		<th class="alignright"><?php echo $total['tpt_invoice']; ?></th>
		<th class="alignright"><?php echo $total['job_amount']; ?></th>
		<th class="alignright"><?php echo $total['expenses']; ?></th>
		<th class="alignright"><?php echo $total['tpt_payment']; ?></th>
		<th class="alignright"><?php echo $total['net_profit']; ?></th>
	</tr>
</tfoot>
</table>

</body>
</html>