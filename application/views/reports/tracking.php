<html>
<head>
<title>Container Tracking</title>
<style>
body, p, h2 {
	margin: 0;
	padding: 0;
}
body {
	margin: 10px;
	font-family: sans-serif;
	font-size: <?php echo (defined('WINDOWS') ? '1.4em' : '1em') ?>;
}

#Details {
	width: 100%;
	border-spacing: 0;
	border-top: solid 1px #999;
	border-left: solid 1px #999;
}

#Details th, #Details td {
	font-size: 8pt;
	padding: 2px;
	vertical-align: top;
	border-right: solid 1px #999;
	border-bottom: solid 1px #999;
}

#Details { border-top: solid 1px #999; }
#Details th {
	vertical-align: middle;
	color: #555;
}
</style>
</head>

<body>
<table id="Details">
<tr>
	<th>Sr No</th>
	<th>Customer Name</th>
	<th>High Seas</th>
	<th>Bl No</th>
	<th>Containers</th>
	<th>Shipping Line</th>
	<th>Vessel Name</th>
	<th>Place Of Discharge</th>
	<th>Eta</th>
	<th>Doc. Rcvd.</th>
	<th>Current Status</th>
</tr>

<?php 
	$i = 1;
	foreach ($rows as $r) {
	echo '<tr ' . (strlen($r['eta_date']) != 0 && 
		$r['eta_date'] != '00-00-0000' && 
		daysDiff(date('d-m-Y'), $r['eta_date'], 'd-m-Y') <= 1 ? ' style="background-color: #ffa;"' : null) . 
		'>
		<td>' . $i++ . ' </td>
		<td>' . $r['customer_name'] . '&nbsp;</td>
		<td>' . $r['high_seas'] . '&nbsp;</td>
		<td nowrap="nowrap">' . $r['bl_no'] . '&nbsp;</td>
		<td nowrap="nowrap">' . $r['containers'] . '&nbsp;</td>
		<td>' . $r['shipping_line'] . '&nbsp;</td>
		<td>' . $r['vessel_name'] . '&nbsp;</td>
		<td>' . $r['place_of_discharge'] . '&nbsp;</td>
		<td nowrap="nowrap">' . $r['eta_date'] . '&nbsp;</td>
		<td align="center">' . $r['original_doc_rcvd'] . '&nbsp;</td>
		<td>' . $r['current_status'] . '&nbsp;</td>
	</tr>';
}
?>
</table>
</body>
</html>