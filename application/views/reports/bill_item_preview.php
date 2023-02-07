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
	<th>Party</th>
	<th>Vessel</th>
	<th>BL</th>
	<th>Bill Date</th>
	<th>Pieces</th>
	<th>CBM</th>
	<th>Amount</th>
</tr>
</thead>

<tbody>
<?php 
$i = 1;
$prev_vessel = 0;
$group = array('pieces' => 0, 'cbm' => 0, 'amount' => 0);
$total = array('pieces' => 0, 'cbm' => 0, 'amount' => 0);
foreach ($rows as $r) {
	if ($prev_vessel != 0 AND $prev_vessel != $r['vessel_id']) {
		echo '<tr>
	<th class="alignright" colspan="5">Sub Total</th>
	<th class="alignright">' . $group['pieces'] . '</th>
	<th class="alignright">' . $group['cbm'] . '</th>
	<th class="alignright">' . inr_format($group['amount']) . '</th>
</tr>';
		$group = array('pieces' => 0, 'cbm' => 0, 'amount' => 0);
	}
	$group['pieces'] += $r['pieces'];
	$group['cbm']    += $r['cbm'];
	$group['amount'] =  bcadd($group['amount'], $r['amount'], 2);

	$total['pieces'] += $r['pieces'];
	$total['cbm']    += $r['cbm'];
	$total['amount'] =  bcadd($total['amount'], $r['amount'], 2);

	echo '<tr>
	<td class="aligncenter">' . $i++ . '</td>
	<td>' . $r['party_name'] . '</td>
	<td>' . $r['vessel_voyage'] . '</td>
	<td>' . $r['bl_no'] . '</td>
	<td class="aligncenter">' . $r['bill_date'] . '</td>
	<td class="alignright">' . $r['pieces'] . '</td>
	<td class="alignright">' . $r['cbm'] . '</td>
	<td class="alignright">' . $r['amount'] . '</td>
</tr>';

	$prev_vessel = $r['vessel_id'];
} 
?>
<tr>
	<th class="alignright" colspan="5">Sub Total</th>
	<th class="alignright"><?php echo $group['pieces'] ?></th>
	<th class="alignright"><?php echo $group['cbm'] ?></th>
	<th class="alignright"><?php echo inr_format($group['amount']) ?></th>
</tr>
</tbody>

<tfoot>
<tr>
	<th class="alignright" colspan="5">Total</th>
	<th class="alignright"><?php echo $total['pieces'] ?></th>
	<th class="alignright"><?php echo $total['cbm'] ?></th>
	<th class="alignright"><?php echo inr_format($total['amount']) ?></th>
</tr>
</tfoot>
</table>

</body>
</html>