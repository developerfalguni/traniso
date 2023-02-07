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
		if ($company['id'] == 2)
			echo '<td><img src="data:image/png;base64,' . base64_encode($image_data) . '" height="80px" /></td>';
		else {
			echo '<td width="75%"><img src="data:image/png;base64,' . base64_encode($image_data) . '" height="100px" /></td>';
			echo '<td class="alignright" valign="top" nowrap="true"><span class="tiny">' . $company['address'] . '<br />' . (isset($city['name']) ? $city['name'] . '-' . $city['pincode'] . ' ' . $state['name'] . '<br />' : '') . $company['contact'] . '<br />' . $company['email'] . '</span></td>';
		}
	}
	else {
		echo '<td><h2>' . $company['name'] . '</h2></td>
	<td class="alignright" valign="top" nowrap="true"><span class="tiny">' . $company['address'] . '<br />' . (isset($city['name']) ? $city['name'] . '-' . $city['pincode'] . ' ' . $state['name'] . '<br />' : '') . $company['contact'] . '<br />' . $company['email'] . '</span></td>';
	}
	?>
</tr>
</table>

<h3 class="aligncenter"><?php echo $page_title ?></h3>

<table class="header">

<tr>
	<td width="25%"><span class="box_label">Purchase Order No</span><br /><?php echo $purchase_order['id2_format']; ?></td>
	<td width="25%"><span class="box_label">Date</span><br /><?php echo $purchase_order['date']; ?></td>
	<td colspan="2"><span class="box_label">Party</span><br /><?php echo $party ?></td>
</tr>

<tr>
	<td colspan="4"><span class="box_label">Remarks</span><br /><?php echo $purchase_order['remarks'] ?></td>
</tr>
</table>

<table class="details">
<tr>
	<th width="14px">No</th>
	<th>Product</th>
	<th>Category</th>
	<th>Part No</th>
	<th>Make</th>
	<th width="40px">Quantity</th>
	<th width="40px">Received</th>
	<th width="100px">Challan No</th>
</tr>

<?php
	$i = 1;
	foreach ($purchase_order_details as $r) {
		echo '<tr>
			<td class="aligncenter">' . $i++ . '</td>
			<td>' . $r['product_name'] . '</td>
			<td>' . $r['category'] . '</td>
			<td>' . $r['part_no'] . '</td>
			<td>' . $r['make'] . '</td>
			<td class="alignright">' . $r['quantity'] . '</td>
			<td class="alignright">' . $r['received_quantity'] . '</td>
			<td>' . $r['challan_no'] . '</td>
		</tr>';
	}
?>
</table>

<table class="details">
<tr>
	<td width="30%"><span class="box_label">Service Tax No</span><br /><?php echo $company['service_tax_no']; ?></td>
	<td width="30%"><span class="box_label">PAN No</span><br /><?php echo $company['pan_no']; ?></td>
	<td class="alignright tiny nowrap" valign="bottom" rowspan="2">For <?php echo $company['name'] ?><br /><br /><br /><br />Authorised Signatory</td>
</tr>
<tr>
	<td width="60%" colspan="2"><p class="tiny">Subject To Gandhidham Jurisdiction<br />E. &amp; O. E.</p></td>
</tr>
</table>

</body>
</html>