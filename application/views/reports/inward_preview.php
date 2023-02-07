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
<table class="header">
<tr>
	<td width="50%"><span class="box_label">To</span><br /><?php echo $party['name'] ?></td>
	<td width="25%"><span class="box_label">Date</span><br /><?php echo $date ?></td>
</tr>
</table>

<h3>Inward Bills</h3>
<table class="details">
<tr>
	<th width="24px">No</th>
	<th>Job No</th>
	<th>Bill Date</th>
	<th>Bill No</th>
	<th>Party Name</th>
	<th>Particulars</th>
	<th>Cheque No</th>
	<th>Cheque Date</th>
	<th width="120px">Amount</th>
</tr>

<tbody class="tiny">
<?php
$i  = 1;
$total = array(
	'inward'  => 0,
	'outward' => 0,
);
foreach ($rows['inward'] as $r) {
	$total['inward'] = bcadd($total['inward'], $r['amount'], 2);

	echo '<tr>
	<td class="aligncenter">' . $i++ . '</td>
	<td>' . $r['id2_format'] . '</td>
	<td>' . $r['date'] . '</td>
	<td>' . $r['party_bill_no'] . '</td>
	<td>' . $r['party_name'] . '</td>
	<td>' . $r['particulars'] . '</td>
	<td>' . $r['cheque_no'] . '</td>
	<td>' . $r['cheque_date'] . '</td>
	<td class="alignright">' . inr_format($r['tds_amount']+$r['amount']) . '</td>
</tr>';
}
?>
</tbody>

<tfoot>
<tr>
	<td class="alignright" colspan="8">Total</td>
	<td class="alignright"><?php echo inr_format($total['inward']) ?></td>
</tr>
</tfoot>
</table>


<h3>Outward Bills</h3>
<table class="details">
<tr>
	<th width="24px">No</th>
	<th>Job No</th>
	<th>Type</th>
	<th>Bill Date</th>
	<th>Bill No</th>
	<th>Remarks</th>
	<th width="120px">Amount</th>
</tr>

<tbody class="tiny">
<?php
$i  = 1;
foreach ($rows['outward'] as $r) {
	$total['outward'] = bcadd($total['outward'], $r['amount'], 2);

	echo '<tr>
	<td class="aligncenter">' . $i++ . '</td>
	<td>' . $r['id2_format'] . '</td>
	<td>' . $r['type'] . '</td>
	<td>' . $r['bill_no'] . '</td>
	<td>' . $r['date'] . '</td>
	<td>' . $r['remarks'] . '</td>
	<td class="alignright">' . inr_format($r['amount']) . '</td>
</tr>';
}
?>
</tbody>

<tfoot>
<tr>
	<td class="alignright" colspan="6">Total</td>
	<td class="alignright"><?php echo inr_format($total['outward']) ?></td>
</tr>

<tr>
	<td class="alignright" colspan="6">Balance Total</td>
	<td class="alignright"><?php echo inr_format($total['outward'] - $total['inward']) ?></td>
</tr>
</tfoot>
</table>

</body>
</html>