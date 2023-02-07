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
	<th>Bill No</th>
	<th>Date</th>
	<th>Debit Account</th>
	<!-- <th>Credit Account</th> -->
	<th>Invoice</th>
	<th>Advance</th>
	<th>Net Amount</th>
	<th>Receipts</th>
	<th>Balance</th>
</tr>
</thead>

<tbody>
<?php 
$total = array(
	'amount'  => 0,
	'advance' => 0,
	'net'     => 0,
	'receipt' => 0,
	'balance' => 0,
);
$group = array(
	'amount'  => 0,
	'advance' => 0,
	'net'     => 0,
	'receipt' => 0,
	'balance' => 0,
);
$i = 1;
$prev_dr = 0;
foreach ($rows as $r) {
	$total['amount']  = bcadd($total['amount'], $r['amount'], 2);
	$total['advance'] = bcadd($total['advance'], $r['advance_amount'], 2);
	$total['net']     = bcadd($total['net'], $r['net_amount'], 2);
	$total['receipt'] = bcadd($total['receipt'], $r['receipt_amount'], 2);
	$total['balance'] = bcadd($total['balance'], $r['balance_amount'], 2);

	if ($prev_dr > 0 AND $prev_dr != $r['dr_ledger_id']) {
		echo '<tr>
	<th colspan="4" class="alignright">Group Total</th>
	<th class="alignright">' . inr_format($group['amount']) . '</th>
	<th class="alignright">' . inr_format($group['advance']) . '</th>
	<th class="alignright">' . inr_format($group['net']) . '</th>
	<th class="alignright">' . inr_format($group['receipt']) . '</th>
	<th class="alignright">' . inr_format($group['balance']) . '</th>
</tr>';
		$group = array(
			'amount'  => 0,
			'advance' => 0,
			'net'     => 0,
			'receipt' => 0,
			'balance' => 0,
		);
	}

	echo '<tr>
	<td class="tiny aligncenter">' . $i++ . '</td>
	<td class="tiny aligncenter nowrap">' . $r['id2_format'] . '</td>
	<td class="tiny aligncenter nowrap">' . $r['date'] . '</td>
	<td class="tiny">' . $r['debit_name'] . '</td>
	<!-- <td class="tiny">' . $r['credit_name'] . '</td> -->
	<td class="tiny alignright">' . inr_format($r['amount'] - $r['advance']) . '</td>
	<td class="tiny alignright">' . inr_format($r['advance_amount']) . '</td>
	<td class="tiny alignright">' . inr_format($r['net_amount']) . '</td>
	<td class="tiny alignright">' . inr_format($r['receipt_amount']) . '</td>
	<td class="tiny alignright">' . inr_format($r['balance_amount']) . '</td>
</tr>';

	$group['amount']  = bcadd($group['amount'], $r['amount'], 2);
	$group['advance'] = bcadd($group['advance'], $r['advance_amount'], 2);
	$group['net']     = bcadd($group['net'], $r['net_amount'], 2);
	$group['receipt'] = bcadd($group['receipt'], $r['receipt_amount'], 2);
	$group['balance'] = bcadd($group['balance'], $r['balance_amount'], 2);

	$prev_dr = $r['dr_ledger_id'];
} 
?>
<tr>
	<th colspan="4" class="alignright">Group Total</th>
	<th class="alignright"><?php echo inr_format($group['amount']) ?></th>
	<th class="alignright"><?php echo inr_format($group['advance']) ?></th>
	<th class="alignright"><?php echo inr_format($group['net']) ?></th>
	<th class="alignright"><?php echo inr_format($group['receipt']) ?></th>
	<th class="alignright"><?php echo inr_format($group['balance']) ?></th>
</tr>
</tbody>

<tfoot>
<tr>
	<th colspan="4" class="alignright">Total</th>
	<th class="alignright"><?php echo inr_format($total['amount']) ?></th>
	<th class="alignright"><?php echo inr_format($total['advance']) ?></th>
	<th class="alignright"><?php echo inr_format($total['net']) ?></th>
	<th class="alignright"><?php echo inr_format($total['receipt']) ?></th>
	<th class="alignright"><?php echo inr_format($total['balance']) ?></th>
</tr>
</tfoot>
</table>

</body>
</html>