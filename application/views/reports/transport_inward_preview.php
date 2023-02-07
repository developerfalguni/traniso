<!DOCTYPE html>
<html lang="en">
<head>
	<title><?php echo $page_title ?></title>
	<style>
		<?php 
			echo file_get_contents(FCPATH.'assets/css/print.css');
			$image_data = file_get_contents(FCPATH.'php_uploads/'.$company['logo']);
		?>
	
		body { font-family: "Times New Roman", serif; }
		.Particular { font-size: 0.9em; }
	</style>
</head>

<body>
<table width="100%">
<tr>
	<?php 
	if ((strlen($company['logo'])) > 0) {
		echo '<td width="75%"><img src="data:image/png;base64,' . base64_encode($image_data) . '" height="100px" /></td>';
		echo '<td class="alignright" valign="top" nowrap="true"><span class="tiny">' . $company['address'] . '<br />' . (isset($city['name']) ? $city['name'] . '-' . $city['pincode'] . ' ' . $state['name'] . '<br />' : '') . $company['contact'] . '<br />' . $company['email'] . '</span></td>';
	}
	else {
		echo '<td><h2>' . $company['name'] . '</h2></td>
	<td class="alignright" valign="top" nowrap="true"><span class="tiny">' . $company['address'] . '<br />' . (isset($city['name']) ? $city['name'] . '-' . $city['pincode'] . ' ' . $state['name'] . '<br />' : '') . $company['contact'] . '<br />' . $company['email'] . '</span></td>';
	}
	?>
</tr>
</table>
<br />
<h4><?php echo $page_title ?></h4>

<table class="details">
<thead>
<tr>
	<th>No</th>
	<th>Type</th>
	<th>Bill No</th>
	<th>Date</th>
	<th>Transporter Bill No</th>
	<th>Bill Date</th>
	<th>Transporter Name</th>
	<th>Trips</th>
	<th>Cheque No</th>
	<th>Cheque Date</th>
	<th>Processed On</th>
	<th>Amount</th>
</tr>
</thead>

<tbody>
<?php 
$filter = array(
	'transporter'     => array(),
);
$total = array(
	'amount' => 0,
);
$i = 1;
foreach ($rows as $bills) {
	$group = array(
		'amount' => 0,
	);
	foreach ($bills as $r) {
		$filter['transporter'][$r['transporter_name']] = 1;

		$total['amount'] = bcadd($total['amount'], $r['amount'], 2);
		$group['amount'] = bcadd($group['amount'], $r['amount'], 2);

		echo '<tr>
		<td class="tiny aligncenter">' . $i++ . '</td>
		<td class="tiny aligncenter">' . $r['type'] . '</td>
		<td class="tiny aligncenter">' . $r['rishi_bill_no'] . '</td>
		<td class="tiny aligncenter">' . $r['date'] . '</td>
		<td class="tiny">' . $r['bill_no'] . '</td>
		<td class="tiny">' . $r['bill_date'] . '</td>
		<td class="tiny">' . $r['transporter_name'] . '</td>
		<td class="aligncenter">' . $r['trips'] . '</td>
		<td class="tiny">' . $r['cheque_no'] . '</td>
		<td class="tiny">' . $r['cheque_date'] . '</td>
		<td class="tiny">' . $r['processed_date'] . '</td>
		<td class="alignright">' . $r['amount'] . '</td>
	</tr>';
	}
	echo '<tr>
	<th class="alignright" colspan="11">Grand Total</th>
	<th class="alignright">' . inr_format($group['amount']) . '</th>
</tr>';
}
?>
</tbody>

<tfoot>
<tr>
	<th class="alignright" colspan="11">Grand Total</th>
	<th class="alignright"><?php echo inr_format($total['amount']) ?></th>
</tr>
</tfoot>
</table>

</body>
</html>