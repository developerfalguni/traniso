<html>
<head>
	<title><?php echo $page_title ?></title>
</head>

<body>

<table border="1" cellpadding="5px">
<thead>
	<tr>
		<th colspan="6" align="left">e-Payment Transaction Status Receipt</th>
	</tr>
</thead>

<tbody>
	<tr>
		<th colspan="2" valign="top" width="148">ICEGATE Reference ID</th>
		<td><?php $challan = $challans[0]; 
		echo $challan['reference_id'] ?></td>
		<th colspan="2" valign="top" width="188">Date &amp; Time of Payment</th>
		<td><?php echo $challan['payment_datetime'] ?></td>
	</tr>
	<tr>
		<th colspan="2" valign="top" width="148">IEC</th>
		<td><?php echo $challan['iec_no'] ?></td>
		<th colspan="2" valign="top" width="188">IEC Name</th>
		<td><?php echo $challan['iec_name'] ?></td>
	</tr>
	<tr>
		<th colspan="2" valign="top" width="148">Bank Branch Code</th>
		<td><?php echo $challan['bank_branch_code'] ?></td>
		<th colspan="2" valign="top" width="188">Bank Transaction Number</th>
		<td><?php echo $challan['bank_transaction_no'] ?></td>
	</tr>
	<tr>
		<th colspan="2" valign="top" width="148">Document Type</th>
		<td><?php echo $challan['document_type'] ?></td>
		<th colspan="2" valign="top" width="188">ICES Location Code</th>
		<td><?php echo $challan['ices_location_code'] ?></td>
	</tr>
	<tr>
		<th colspan="2" valign="top" width="148">Bank Name</th>
		<td><?php echo $challan['bank_name'] ?></td>
		<th colspan="2" valign="top" width="188">Receipt Date &amp; Time</th>
		<td><?php echo $challan['receipt_datetime'] ?></td>
	</tr>

	<tr>
		<th>S.No.</th>
		<th>Challan No.</th>
		<th>Document Number</th>
		<th>Document Date</th>
		<th>Duty Amount (INR)</th>
		<th>ICES Status Code</th>
	</tr>

<?php foreach ($challans as $challan) {
	echo '<tr>
		<td>' . $challan['sr_no'] . '</td>
		<td>' . $challan['challan_no'] . '</td>
		<td>' . $challan['be_no'] . '</td>
		<td>' . $challan['be_date'] . '</td>
		<td>' . $challan['duty_amount'] . '</td>
		<td>' . $challan['ices_status_code'] . '</td>
	</tr>';
} ?>
</tbody>
</table>

<div><strong>Disclaimer</strong> : This e-Receipt is system generated. However to verify payment contact concerned officer at your custom location.</div>

<script>
<?php echo $script ?>
</script>

</body>
</html>