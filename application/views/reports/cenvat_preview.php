<html>
<head>
	<title><?php echo $page_title ?></title>
	<style>
		<?php echo file_get_contents(FCPATH.'assets/css/print.css'); ?>
	</style>
</head>

<body>

<h2 class="aligncenter"><?php echo $company['name'] ?></h2>
<h4 class="aligncenter"><?php echo $page_title . ' ' . $page_desc ?></h4>

<?php foreach ($stax as $rows) : ?>
<h4><?php echo $rows[0]['stax_category'] ?></h4>
<table class="details">
<thead>
<tr>
	<th width="48px">Sr No</th>
	<th>Party Name</th>
	<th>Service Tax No</th>
	<th>Invoice No</th>
	<th width="100px">Invoice Date</th>
	<th width="100px">Invoice Amount</th>
	<th width="100px">Stax Amount</th>
</tr>
</thead>

<tbody class="tiny">
<?php 
	$i = 1;
	$total = array('invoice' => 0, 'stax' => 0);
	foreach ($rows as $s) {
		$total['invoice'] += $s['stax_on_amount'];
		$total['stax']    += $s['amount'];

		echo '<tr>
	<td class="aligncenter">' . $i++ . '</td>
	<td>' . ($s['party_name'] ? $s['party_name'] : '<span class="red">' . $s['party_ledger'] . '</span>') . '</td>
	<td>' . $s['service_tax_no'] . '</td>
	<td>' . $s['invoice_no'] . '</td>
	<td>' . $s['invoice_date'] . '</td>
	<td class="alignright">' . $s['stax_on_amount'] . '</td>
	<td class="alignright">' . $s['amount'] . '</td>
</tr>';
}
?>
</tbody>

<tfoot>
<tr>
	<th colspan="4" class="alignright">Total</th>
	<th class="alignright"><?php echo $total['invoice'] ?></th>
	<th></th>
	<th class="alignright"><?php echo $total['stax'] ?></th>
</tr>
</tfoot>
</table>
<?php endforeach; ?>

</body>
</html>