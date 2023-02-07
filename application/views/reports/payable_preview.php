<html>
<head>
	<title><?php echo $page_title ?></title>
	<style>
		<?php echo file_get_contents(FCPATH.'assets/css/print.css'); ?>
	
		body {
			font-size: 75% !important;
		}
	</style>
</head>

<body>

<h4 class="aligncenter"><?php echo $page_title ?></h4>
<p class="aligncenter"><?php echo $page_desc ?></p>

<table class="details">
<thead>
<tr>
	<th>No</th>
	<th>Voucher No</th>
	<th>Date</th>
	<th>Job No</th>
	<!-- <th>Debit Account</th> -->
	<th>Credit Account</th>
	<th>Bill No</th>
	<th>Payable</th>
	<th>TDS</th>
	<th>Paid</th>
	<th>Balance</th>
</tr>
</thead>

<tbody>
<?php 
$total = array(
	'amount'     => 0,
	'tds_amount' => 0,
	'paid'       => 0,
	'balance'    => 0,
);
$group = array(
	'amount'     => 0,
	'tds_amount' => 0,
	'paid'       => 0,
	'balance'    => 0,
);
$i = 1;
$prev_cr = 0;
foreach ($rows as $r) {
	$total['amount']     = bcadd($total['amount'], $r['amount'], 2);
	$total['tds_amount'] = bcadd($total['tds_amount'], $r['tds_amount'], 2);
	$total['paid']       = bcadd($total['paid'], $r['paid_amount'], 2);
	$total['balance']    = bcadd($total['balance'], $r['balance_amount'], 2);

	if ($prev_cr > 0 AND $prev_cr != $r['cr_ledger_id']) {
		echo '<tr>
	<th colspan="6" class="alignright">Group Total</th>
	<th class="alignright">' . inr_format($group['amount']) . '</th>
	<th class="alignright">' . inr_format($group['tds_amount']) . '</th>
	<th class="alignright">' . inr_format($group['paid']) . '</th>
	<th class="alignright">' . inr_format($group['balance']) . '</th>
</tr>';
		$group = array(
			'amount'  => 0,
			'paid'    => 0,
			'balance' => 0,
		);
	}

	echo '<tr>
	<td class="tiny aligncenter">' . $i++ . '</td>
	<td class="tiny aligncenter nowrap">' . $r['id2_format'] . '</td>
	<td class="tiny aligncenter nowrap">' . $r['date'] . '</td>
	<td class="tiny aligncenter nowrap">' . $r['job_no'] . '</td>
	<!-- <td class="tiny">' . $r['debit_name'] . '</td> -->
	<td class="tiny">' . $r['credit_name'] . '</td>
	<td class="tiny">' . $r['invoice_no'] . '</td>
	<td class="tiny alignright">' . inr_format($r['amount']) . '</td>
	<td class="tiny alignright">' . inr_format($r['tds_amount']) . '</td>
	<td class="tiny alignright">' . inr_format($r['paid_amount']) . '</td>
	<td class="tiny alignright">' . inr_format($r['balance_amount']) . '</td>
</tr>';

	$group['amount']     = bcadd($group['amount'], $r['amount'], 2);
	$group['tds_amount'] = bcadd($group['tds_amount'], $r['tds_amount'], 2);
	$group['paid']       = bcadd($group['paid'], $r['paid_amount'], 2);
	$group['balance']    = bcadd($group['balance'], $r['balance_amount'], 2);

	$prev_cr = $r['cr_ledger_id'];
} 
?>
<tr>
	<th colspan="6" class="alignright">Group Total</th>
	<th class="alignright"><?php echo inr_format($group['amount']) ?></th>
	<th class="alignright"><?php echo inr_format($group['tds_amount']) ?></th>
	<th class="alignright"><?php echo inr_format($group['paid']) ?></th>
	<th class="alignright"><?php echo inr_format($group['balance']) ?></th>
</tr>
</tbody>

<tfoot>
<tr>
	<th colspan="6" class="alignright">Total</th>
	<th class="alignright"><?php echo inr_format($total['amount']) ?></th>
	<th class="alignright"><?php echo inr_format($total['tds_amount']) ?></th>
	<th class="alignright"><?php echo inr_format($total['paid']) ?></th>
	<th class="alignright"><?php echo inr_format($total['balance']) ?></th>
</tr>
</tfoot>
</table>

</body>
</html>