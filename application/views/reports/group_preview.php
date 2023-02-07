<!DOCTYPE html>
<html lang="en">
<head>
	<title><?php echo $page_title ?></title>
	<style>
		<?php echo file_get_contents(FCPATH.'assets/css/print.css'); ?>
	
		body { font-family: "Times New Roman", serif; }
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
<?php 
	$col_group_total[0] = 0;
	foreach ($rows['companies'] as $company_id => $code) {
		$col_total[$company_id] = 0;
		$col_group_total[$company_id] = 0;
		echo '<th>' . $code . '</th>'; 
	}
?>
	<th>Total</th>
</tr>
</thead>

<tbody class="tiny">
<?php 
foreach ($rows['ledgers'] as $group_name => $groups) {
	echo '<tr class="SubTotal">
	<td class="bold" colspan="' . (count($rows['companies'])+3) . '">' . $group_name . '</td>
</tr>';
	foreach($groups as $code => $l) {
		echo '<tr>
	<td>' . $code . '</td>
	<td>' . $l['name'] . '</td>';
		$row_total = 0;
		foreach ($rows['companies'] as $company_id => $code) {
			if (isset($l['closing'][$company_id])) {
				echo '<td class="alignright"><span class="' . ($l['closing'][$company_id]['closing'] >= 0 ? '' : 'red') . '">' . 
					inr_format($l['closing'][$company_id]['closing']) . '</span></td>';
				$row_total                    = bcadd($row_total, $l['closing'][$company_id]['closing'], 2);
				$col_total[$company_id]       = bcadd($col_total[$company_id], $l['closing'][$company_id]['closing'], 2);
				$col_group_total[$company_id] = bcadd($col_group_total[$company_id], $l['closing'][$company_id]['closing'], 2);
				$col_group_total[0]           = bcadd($col_group_total[0], $l['closing'][$company_id]['closing'], 2);
			}
			else 
				echo '<td class="aligncenter">-</td>';
		}
		echo '<td class="alignright bold"><span class="' . ($row_total >= 0 ? '' : 'red') . '">' . inr_format(number_format($row_total, 2, '.', '')) . '</span></td>
</tr>
';
	}
	echo '<tr>
	<td></td>
	<td class="alignright bold">Group Total</td>';
	
		foreach ($rows['companies'] as $company_id => $code) {
			echo '<td class="alignright bold"><span class="' . ($col_group_total[$company_id] >= 0 ? '' : 'red') . '">' . inr_format($col_group_total[$company_id]) . '</span></td>';
			$col_group_total[$company_id] = 0;
		}
		echo '<td class="alignright bold"><span class="' . ($col_group_total[0] >= 0 ? '' : 'red') . '">' . inr_format($col_group_total[0]) . '</span></td>
	</tr>';
	$col_group_total[0] = 0;
}
?>

<tr>
	<td></td>
	<td class="alignright big">Grand Total</td>
<?php 
	$row_total = 0;
	foreach ($rows['companies'] as $company_id => $code) {
		echo '<td class="alignright big"><span class="' . ($col_total[$company_id] >= 0 ? '' : 'red') . '">' . inr_format($col_total[$company_id]) . '</span></td>';
		$row_total = bcadd($row_total, $col_total[$company_id], 2);
	} 
	echo '<td class="alignright big"><span class="' . ($row_total >= 0 ? '' : 'red') . '">' . inr_format($row_total) . '</span></td>';
?>
</tr>
</tbody>
</table>

</body>
</html>