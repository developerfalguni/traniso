<!DOCTYPE html>
<html lang="en">
<head>
	<title><?php echo $page_title ?></title>
	<style>
		<?php echo file_get_contents(FCPATH.'assets/css/print.css'); ?>
	
		body { font-family: "Times New Roman", serif; }
		td { min-height: 50px; }
	</style>
</head>

<body>
<h4 class='aligncenter'><?php echo $page_title ?></h4>

<table class="header">
<tbody>
<tr>
	<td width="50%" colspan="2"><span class="box_label">SCAC CODE</span><br /></td>
	<td width="50%" colspan="2"><span class="box_label">BL No</span><br /><?php echo $bl_no ?></td>
</tr>

<tr>
	<td colspan="2"><span class="box_label">SHIPPER</span><br /><?php echo $consignor ?></td>
	<td colspan="2"><span class="box_label">DESTINATION AGENT ADDRESS</span><br /><?php echo $agent ?></td>
</tr>

<tr>
	<tr>
	<td colspan="2"><span class="box_label">CONSIGNEE</span><br /><?php echo $consignee ?></td>
	<td colspan="2"></td>
</tr>

<tr>
	<td colspan="2"><span class="box_label">NOTIFY PARTY</span><br /><?php echo $notify ?></td>
	<td colspan="2"><span class="box_label">2ND NOTIFY PARTY</span><br /><?php echo $notify2 ?></td>
</tr>

<tr>
	<td width="25%"><span class="box_label">PLACE OF RECEIPT</span><br /><?php echo $port_of_loading ?></td>
	<td width="25%"><span class="box_label">PORT OF LOADING</span><br /><?php echo $port_of_loading ?></td>
	<td colspan="2"></td>
</tr>

<tr>
	<td width="25%"><span class="box_label">PORT OF DISCHARGE</span><br /><?php echo $port_of_discharge ?></td>
	<td width="25%"><span class="box_label">PLACE OF DELIVERY</span><br /><?php echo $place_of_delivery ?></td>
	<td colspan="2"></td>
</tr>

<tr>
	<td colpsan="2"><span class="box_label">VESSEL / VOYAGE</span><br /><?php echo $vessel_name ?></td>
	<td colspan="2"></td>
</tr>
</tbody>
</table>

<table class="details">
<tbody>
<tr>
	<th width="15%">MARKS AND NUMBERS / CONTAINER NOS</th>
	<th width="15%">NUMBER AND TYPE OF PACKAGE</th>
	<th width="60%">DESCRIPTION OF GOODS</th>
</tr>

<tr>
	<td><?php echo $marks ?></td>
	<td><?php echo $packages ?></td>
	<td><?php echo $details ?></td>
</tr>
</table>
</body>
</html>