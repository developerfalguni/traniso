<!DOCTYPE html>
<html lang="en">
<head>
	<title><?php echo $page_title ?></title>
	<style>
		<?php echo file_get_contents(FCPATH.'assets/css/print.css'); ?>
	
		body { font-family: "Times New Roman", serif; }
		.SubTotal { background-color: #eee !important; }
		.Opening  { background-color: #ffff00 !important; }
	</style>
</head>

<body>
<h2 class="aligncenter">Group Summary</h2>

<table class="details">
<thead>
<tr>
	<th width="80px">Code</th>
	<th>Name</th>
<?php 
	foreach ($companies as $c) {
		$col_total[$c->company_id] = 0;
		echo '<th>' . $c->company_name . '</th>'; 
	}
?>
	<th>Total</th>
</tr>
</thead>

<tbody class="tiny">
<?php 

$processed_ledgers = array();
foreach ($ledgers as $ledger) {
	$processed_ledgers[$ledger->code][$ledger->company_id] = array(
		'code'         => $ledger->code,
		'name'         => $ledger->name,
		'only_opening' => $ledger->only_opening,
		'closing'      => $ledger->closing,
	);
}

foreach ($processed_ledgers as $ledger) {
	$row_total = 0;
	$echo_party = 1;
	$buffer = '';
	$open_class = '';
	foreach ($companies as $c) {
		if (isset($ledger[$c->company_id])) {
			if ($ledger[$c->company_id]['only_opening'])
				$open_class = 'Opening';

			if ($echo_party) {
				echo '<tr>
					<td>' . $ledger[$c->company_id]['code'] . '</td>
					<td>' . $ledger[$c->company_id]['name'] . '</td>' .
					$buffer;
				$echo_party = 0;
			}
			echo '<td class="alignright ' . $open_class . '"><span class="' . ($ledger[$c->company_id]['closing'] >= 0 ? '' : 'red') . '">' . 
				inr_format($ledger[$c->company_id]['closing']) . '</span></td>';
			$row_total                 = bcadd($row_total, $ledger[$c->company_id]['closing'], 2);
			$col_total[$c->company_id] = bcadd($col_total[$c->company_id], $ledger[$c->company_id]['closing'], 2);
		}
		else {
			if ($echo_party)
				$buffer .= '<td class="' . $open_class . '">-</td>';
			else
				echo '<td class="' . $open_class . '">-</td>';
		}
	}
	echo '<td class="alignright bold ' . $open_class . '"><span class="' . ($row_total >= 0 ? '' : 'red') . '">' . inr_format(number_format($row_total, 2, '.', '')) . '</span></td>
</tr>
';
}
?>

<tr>
	<td></td>
	<td class="alignright big">Grand Total</td>
<?php 
	$row_total = 0;
	foreach ($companies as $c) {
		echo '<td class="alignright big"><span class="' . ($col_total[$c->company_id] >= 0 ? '' : 'red') . '">' . inr_format($col_total[$c->company_id]) . '</span></td>';
		$row_total = bcadd($row_total, $col_total[$c->company_id], 2);
	} 
	echo '<td class="alignright big"><span class="' . ($row_total >= 0 ? '' : 'red') . '">' . inr_format($row_total) . '</span></td>';
?>
</tr>
</tbody>
</table>

</body>
</html>