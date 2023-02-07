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
<h4 class="aligncenter"><?php echo $page_title ?></h4>
<h5 class="aligncenter"><?php echo $page_desc ?></h5>
<table class="details tiny">
<thead>
<tr>
	<th>No</th>
	<th>Type</th>
	<th>Bill No</th>
	<th>Date</th>
	<th>Party Bill No</th>
	<th>Party Name</th>
	<th>PAN No</th>
	<th>Trips</th>
	<th>Cheque No</th>
	<th>Cheque Date</th>
	<th>Processed On</th>
</tr>
</thead>

<tbody>
<?php 
$i = 1;
foreach ($rows as $r) {
	echo '<tr>
	<td class="aligncenter">' . $i++ . '</td>
	<td>' . $r['type'] . '</td>
	<td class="nowrap">' . $r['rishi_bill_no'] . '</td>
	<td class="nowrap">' . $r['date'] . '</td>
	<td class="nowrap">' . $r['bill_no'] . '</td>
	<td >' . $r['party_name'] . '</td>
	<td>' . $r['pan_no'] . '</td>
	<td class="aligncenter">' . $r['trips'] . '</td>
	<td>' . $r['cheque_no'] . '</td>
	<td class="nowrap">' . $r['cheque_date'] . '</td>
	<td class="nowrap">' . $r['processed_date'] . '</td>
</tr>';
} 
?>
</tbody>
</table>

</body>
</html>