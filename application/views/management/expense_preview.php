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
<h4 class="aligncenter"><?php echo $page_title ?></h4>

<table class="details tiny">
<thead>
<tr>
	<th>No</th>
	<th>Voucher No</th>
	<th>Date</th>
	<th>Job No</th>
	<th>Debit Account</th>
	<th>Bill Nos</th>
	<th>Credit Account</th>
	<th>Cheque No</th>
	<th>Cheque Date</th>
	<th>Realization Date</th>
	<th>Amount</th>
</tr>
</thead>

<tbody>
<?php 
$total = 0;
$i = 1;
foreach ($rows as $r) {
	$filter['debit'][$r['debit_name']]   = 1;
	$filter['credit'][$r['credit_name']] = 1;

	$total = bcadd($total, $r['amount'], 2);

	echo '<tr>
	<td class="aligncenter">' . $i++ . '</td>
	<td class="aligncenter">' . $r['id2_format'] . '</td>
	<td class="aligncenter">' . $r['date'] . '</td>
	<td>' . $r['job_no'] . '</td>
	<td>' . $r['debit_name'] . '</td>
	<td>' . $r['bill_nos'] . '</td>
	<td>' . $r['credit_name'] . '</td>
	<td>' . $r['cheque_no'] . '</td>
	<td>' . $r['cheque_date'] . '</td>
	<td>' . $r['realization_date'] . '</td>
	<td class="alignright">' . inr_format($r['amount']) . '</td>
</tr>';
} 
?>
</tbody>

<tfoot>
<tr>
	<th colspan="10" class="alignright">Total</th>
	<th class="alignright"><?php echo inr_format($total) ?></th>
</tr>
</tfoot>
</body>
</html>