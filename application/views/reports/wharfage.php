<html>
<head>
	<title>Wharfage Report</title>
	<style>
echo file_get_contents(FCPATH.'assets/css/print.css');
	
table.Wharfage, table.WharfageDetail {
	font-size: 0.7em;
	width: 100%;
}
table.Wharfage td, table.WharfageDetail td {
	vertical-align: top;
}

.Bold {
	font-weight: bold;
}

.Italic {
	font-style: italic;
}

table.WharfageDetail {
	border-spacing: 0;
	border-left: solid 1px gray;
	border-top: solid 1px gray;
}
table.WharfageDetail tr {
}
table.WharfageDetail th, table.WharfageDetail td {
	border-right: solid 1px gray;
	border-bottom: solid 1px gray;
}
</style>
</head>

<body>
<p>&nbsp;<br /></p>

<?php
if (! isset($cha['name'])) {
	echo '<p>CHA Name is Missing. <br />First enter the CHA name on Job Information Page.</p></body></html>';
	return; 
}
if (strlen($charter['name']) == 0) {
	echo '<p>Charter Name is Missing. <br />First enter the Charter / Vessel Agent name on Vessel Master Page.</p></body></html>';
	return; 
}
?>

<table class="Wharfage">
<tr>
	<td width="20%" align="left"></td>
	<td width="50%" align="center"><h2>PORT OF KANDLA</h2></td>
	<td width="20%" class="tiny alignright">Original<br />Duplicate<br />Triplicate<br />Quadruplicate</td>
</tr>
</table>

<table class="Wharfage">
<tr>
	<td class="Bold" width="128px">To,<br />The Traffic Manager,<br />Kandla Port Trust,</td>
	<td class="alignright">Import Application No: <br /><br />
		Date: </td>
	<td>________________<br /><br />________________</td>
</tr>

<tr>
	<td colspan="3">&nbsp;<br /></td>
</tr>

<tr>
	<td colspan="3" style="line-height: 24px">Sir,<br />Please permit us to remove from the port premises the undermentioned goods landed Ex. <span class="Bold"><?php echo $vessel['name'], ' ', $vessel['voyage_no'] ?></span> of __________________________________ line under Agency of / Charters <span class="Bold"><?php echo $charter['name'] ?></span> and under _______________ flag arrived on ___________________ under Customs B/E No. __________________ of ____________.</td>
</tr>

<tr>
	<td colspan="3">&nbsp;<br /></td>
</tr>

<tr>
	<td>Name of Importer :</td>
	<td class="Bold"><?php echo $party['name'] ?></td>
	<td class="alignright">Date of completion of discharge _____________________</td>
</tr>

<tr>
	<td>Address :</td>
	<td class="Italic"><?php if(isset($party['details']['address'])) echo wordwrap($party['details']['address'], 30, '<br />') ?></td>
	<td class="alignright">Date of expiry of free period ________________________</td>
</tr>
</table>
<br /><br />

<table class="WharfageDetail">
<tr>
	<th>Sr. No.</th>
	<th>Main Line No.</th>
	<th>No. &amp; Description Packages</th>
	<th>Marks</th>
	<th>Name of Commodity</th>
	<th>Port of Shipment</th>
	<th>Country of Shipment</th>
	<th>Country of Origin</th>
	<th>Freight Tonnes</th>
	<th colspan="2">Measure ment</th>
	<th>Total</th>
	<th>Gross Weight M. Tonnes</th>
	<th>Liquid Unit (Liters)</th>
	<th>Rates</th>
	<th>Wharfage</th>
	<th>Remarks</th>
</tr>

<tr>
	<td colspan="2">As per BL</td>
	<td><?php echo number_format($job['packages'], 0) . ' ' . $package_type ?></td>
	<td><?php echo $job['marks'] ?></td>
	<td><?php echo $job['details'] ?></td>
	<td><?php echo $job['shipment_port'] ?></td>
	<td><?php echo $job['origin_country'] ?></td>
	<td><?php echo $job['origin_country'] ?></td>
	<td><?php if ($job['net_weight_unit'] == 'MTS') 
		echo $job['net_weight'] . $job['net_weight_unit'] . ' x 1.42 = ' . ($wharfage['cbm']);
		else 
		echo $job['net_weight']; 
	?></td>
	<td>CBM</td>
	<td><?php echo $job['cargo_type'] ?></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td><?php echo $wharfage['rate'] . '/- per CBM' ?></td>
	<td align="right"><?php echo $wharfage['wharfage'] ?></td>
	<td>&nbsp;</td>
</tr>

<tr>
	<td colspan="12" rowspan="4" align="center">Checked &amp; Verified By<br /><br /><br /><br />ATM / CDC</td>
	<td colspan="3">&nbsp;</td>
	<td align="right" class="Bold"><?php echo $wharfage['wharfage'] ?></td>
	<td>&nbsp;</td>
</tr>

<tr>
	<td colspan="3" align="right">Service Tax @ <?php echo $wharfage['stax'] ?> : </td>
	<td align="right"><?php echo $wharfage['stax_amount'] ?></td>
	<td>&nbsp;</td>
</tr>

<tr>
	<td colspan="3">&nbsp;</td>
	<td align="right" class="Bold"><?php echo ($wharfage['wharfage']+$wharfage['stax_amount']) ?></td>
	<td>&nbsp;</td>
</tr>

<tr>
	<td colspan="3" align="right">Less TDS @ <?php echo $wharfage['tds'] ?> : </td>
	<td align="right"><?php echo $wharfage['tds_amount'] ?></td>
	<td>&nbsp;</td>
</tr>


<tr>
	<td colspan="15" class="Bold alignright">Rupees <?php echo numberToWords($wharfage['amount'] - $wharfage['tds_amount']) ?></td>
	<td align="right" class="Bold"><?php echo ($wharfage['amount'] - $wharfage['tds_amount']) ?></td>
	<td>&nbsp;</td>
</tr>

<tr>
	<td colspan="4">Compared with Bill of Lading<br /><br />Invoice Deliver Order<br /><br /><span class="Bold">B/L No. <?php echo $job['bl_no'], '<br />', $job['bl_date'] ?><br /><br />Examier</td>
	<td colspan="4">(To be used by Accounts Department Only)<br />Address Bu :<br /><br />Received the sum of Rs.__________ Ps.______<br />Rupees<br /><br />Cashier<br />Accountant</td>
	<td colspan="6" align="center">(For use at Shed only)<br /><br /><br />Checked and found correct<br /><br /><br />Shed Master</td>
	<td colspan="4">I/We hereby declare that the contents of this Wharfage entry are truly stated<br /><br />Signature of authorised agent.<br /><br /><span class="Bold"><?php echo 'M/S ', $cha['name'] ?></span><br />As CHA</td>
</tr>
</table>

</body>
</html>