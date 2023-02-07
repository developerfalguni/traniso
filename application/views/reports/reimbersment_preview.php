<html>
<head>
	<title><?php echo $page_title ?></title>
	<style>
		<?php echo file_get_contents(FCPATH.'assets/css/print.css'); ?>
	
		.details { font-size: 7pt; }
		.color1  { background-color: #FF7AA3 !important; }
		.color2  { background-color: #FF7B7A !important; }
		.color3  { background-color: #FFB97A !important; }
		.color4  { background-color: #FFEA7A !important; }
		.color5  { background-color: #E7FF7A !important; }
		.color6  { background-color: #B3FF7A !important; }
		.color7  { background-color: #7AFF91 !important; }
		.color8  { background-color: #7AFFE4 !important; }
		.color9  { background-color: #7AEAFF !important; }
		.color10 { background-color: #7ABBFF !important; }
		.color11 { background-color: #7A88FF !important; }
		.color12 { background-color: #917AFF !important; }
		.color13 { background-color: #C77AFF !important; }
	</style>
</head>

<body>

<h2 class="aligncenter"><?php echo $company['name'] ?></h2>
<h4 class="aligncenter"><?php echo $page_title . ' ' . $page_desc ?></h4>

<?php 
$totals = array('amount' => 0);
foreach ($reimbersment['heading'] as $index => $code) {
	$totals[$code] = 0;
}
foreach ($reimbersment['reimbersment'] as $s) {
	$totals['amount'] = bcadd($totals['amount'], $s['amount'], 2);
	foreach ($reimbersment['heading'] as $index => $code)
		$totals[$code] = bcadd($totals[$code], $s[$code], 2);
}
?>

<table class="details">
<thead>
<tr>
	<th>Voucher No</th>
	<th>Date</th>
	<th>Party</th>
	<th>Amount</th>
<?php 
foreach ($reimbersment['heading'] as $index => $code) {
	if ($totals[$code] > 0)
		echo '<th>' . $code . '</th>';
}
?>
</tr>
</thead>

<tbody>
<?php 
	foreach ($reimbersment['reimbersment'] as $s) {
		echo '<tr>
	<td>' . $s['id2_format'] . '</td>
	<td>' . $s['date'] . '</td>
	<td>' . $s['party_name'] . '</td>
	<td class="alignright">' . number_format($s['amount'], 0, '.', '') . '</td>';
	foreach ($reimbersment['heading'] as $index => $code) {
		if ($totals[$code] > 0)
			echo '<td class="alignright">' . number_format($s[$code], 0, '.', '') . '</td>';
	}
	echo '</tr>';
}
?>
</tbody>

<tfoot>
<tr>
	<th colspan="3">Total</th>
<?php 
foreach ($totals as $code => $amount) {
	if ($amount > 0)
		echo '<th class="alignright">' . number_format($amount, 0, '.', '') . '</th>';
}
?>
</tr>
</tfoot>
</table>

</body>
</html>