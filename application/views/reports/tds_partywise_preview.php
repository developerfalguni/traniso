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

<table class="details">
<thead>
<tr>
	<th width="48px">Sr No</th>
	<th>Party</th>
	<th>PAN No</th>
	<th width="100px">Invoice</th>
	<th width="80px">TDS</th>
	<th width="80px">Surcharge</th>
	<th width="80px">Edu Cess</th>
	<th width="80px">H.Edu Cess</th>
</tr>
</thead>

<tbody>
<?php 
$i = 1;
$total = array(
	'invoice'   => 0,
	'surcharge' => 0,
	'edu_cess'  => 0,
	'hedu_cess' => 0,
);
foreach ($rows as $pan_no => $r) {
	$total['invoice']   += $r['invoice_amount'];
	$total['tds']       += $r['tds_amount'];
	$total['surcharge'] += $r['tds_surcharge_amount'];
	$total['edu_cess']  += $r['tds_edu_cess_amount'];
	$total['hedu_cess'] += $r['tds_hedu_cess_amount'];

	echo '<tr>
	<td class="aligncenter">' . $i++ . '</td>
	<td>' . $r['party_name'] . '</td>
	<td>' . $pan_no . '</td>
	<td class="alignright">' . inr_format($r['invoice_amount']) . '</td>
	<td class="alignright">' . inr_format($r['tds_amount']) . '</td>
	<td class="alignright">' . inr_format($r['tds_surcharge_amount']) . '</td>
	<td class="alignright">' . inr_format($r['tds_edu_cess_amount']) . '</td>
	<td class="alignright">' . inr_format($r['tds_hedu_cess_amount']) . '</td>
</tr>
	';
}
	echo '
<tfoot>
<tr>
	<th colspan="3" class="alignright">Total</th>
	<th class="alignright">' . $total['invoice'] . '</th>
	<th class="alignright">' . $total['tds'] . '</th>
	<th class="alignright">' . $total['surcharge'] . '</th>
	<th class="alignright">' . $total['edu_cess'] . '</th>
	<th class="alignright">' . $total['hedu_cess'] . '</th>
</tr>
</tfoot>';
?>
</tbody>
</table>

</body>
</html>