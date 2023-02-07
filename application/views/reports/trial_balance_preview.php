<!DOCTYPE html>
<html lang="en">
<head>
	<title><?php echo $page_title ?></title>
	<style>
		<?php echo file_get_contents(FCPATH.'assets/css/print.css'); ?>
	
		body { font-family: "Times New Roman", serif; }
		.tiny { font-size: 0.7em !important; }
		.SubTotal { background-color: #eee !important; }
	</style>
</head>

<body>
<h2 class="aligncenter"><?php echo $company['name'] ?></h2>
<h4 class="aligncenter"><?php echo $page_title ?></h4>

<table class="details">
<thead>
<tr>
	<th width="80px">Code</th>
	<th>Name</th>
	<th width="100px">Opening</th>
	<th width="100px">Debit</th>
	<th width="100px">Credit</th>
	<th width="100px">Closing</th>
</tr>
</thead>

<tbody>
<?php 
$total = array('opening' => 0, 'debit' => 0, 'credit' => 0, 'closing' => 0);
foreach ($rows as $group_name => $groups) {
	$group = array('opening' => 0, 'debit' => 0, 'credit' => 0, 'closing' => 0);
	echo '<tr class="SubTotal bold"><td colspan="6">' . $group_name . '</td></tr>';
	foreach ($groups as $r) {
		$group['opening'] = bcadd($group['opening'], $r['opening'], 2);
		$group['debit']   = bcadd($group['debit'], $r['debit'], 2);
		$group['credit']  = bcadd($group['credit'], $r['credit'], 2);
		$group['closing'] = bcadd($group['closing'], $r['closing'], 2);

		$total['opening'] = bcadd($total['opening'], $r['opening'], 2);
		$total['debit']   = bcadd($total['debit'], $r['debit'], 2);
		$total['credit']  = bcadd($total['credit'], $r['credit'], 2);
		$total['closing'] = bcadd($total['closing'], $r['closing'], 2);

		echo '<tr>
<td>' . $r['code'] . '</td>
<td>' . $r['name'] . '</td>
<td class="alignright">' . inr_format($r['opening']) . '</td>
<td class="alignright">' . inr_format($r['debit']) . '</td>
<td class="alignright">' . inr_format($r['credit']) . '</td>
<td class="alignright">' . inr_format($r['closing']) . '</td>
</tr>';
	}
	echo '<tr>
<td class="alignright bold" colspan="2">Group Total</td>
<td class="alignright bold">' . inr_format($group['opening']) . '</td>
<td class="alignright bold">' . inr_format($group['debit']) . '</td>
<td class="alignright bold">' . inr_format($group['credit']) . '</td>
<td class="alignright bold">' . inr_format($group['closing']) . '</td>
</tr>';
} 
?>
</tbody>

<tfoot>
<tr>
	<th class="alignright" colspan="2">Grand Total</th>
	<th class="alignright"><?php echo inr_format($total['opening']) ?></th>
	<th class="alignright"><?php echo inr_format($total['debit']) ?></th>
	<th class="alignright"><?php echo inr_format($total['credit']) ?></th>
	<th class="alignright"><?php echo inr_format($total['closing']) ?></th>
</tr>
</tfoot>
</table>

</body>
</html>