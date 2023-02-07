<!DOCTYPE html>
<html lang="en">
<head>
	<title><?php echo $page_title ?></title>
	<style>
		<?php echo file_get_contents(FCPATH.'assets/css/print.css'); ?>
	
		body { 
			padding: 2em;
			font-size: 1.2em;
			font-family: "Times New Roman", serif;
		}
		td { padding: 2em; }
	</style>
</head>

<body>
<br />
<h3 class="aligncenter"><?php echo $indian_port ?></h3>
<br />
<table class="header">
<tr>
	<td colspan="2"><span class="box_label">Importer</span><br /><span class="big bold"><?php echo $party_name ?></span></td>
</tr>

<?php foreach ($high_seas AS $index => $hss) : ?>
<tr>
	<td colspan="2"><span class="box_label">High Seas <?php echo ($index+1) ?></span><br /><span class="big bold"><?php echo $hss['name'] ?></span></td>
</tr>
<?php endforeach; ?>

<tr>
	<td colspan="2"><br /><br /></td>
</tr>

<tr>
	<td colspan="2"><span class="box_label">Vessel</span><br /><span class="big bold"><?php echo $vessel_name ?></span></td>
</tr>

<tr>
	<td width="50%"><span class="box_label">BL No &amp; Date</span><br /><span class="big"><?php echo $job['bl_no'] . ($job['bl_date'] == '00-00-0000' ? '<br />' : ' / ' . $job['bl_date']) ?></span></td>
	<td width="50%"><span class="box_label">BE No &amp; Date</span><br /><span class="big"><?php echo $job['be_no'] . ($job['be_date'] == '00-00-0000' ? '<br />' : ' / ' . $job['be_date']) ?></span></td>
</tr>

<tr>
	<td width="50%"><span class="box_label">SCM No &amp; Date</span><br /><span class="big"><br /></span></td>
	<td width="50%"><span class="box_label">IGM No &amp; Date</span><br /><span class="big"><br /></span></td>
</tr>

<tr>
	<?php 
	if ($job['cargo_type'] == 'Container') {
		echo '<td width="50%"><span class="box_label">Containers / Quantity</span><br /><span class="big">' . 
			($job['container_20'] > 0 ? $job['container_20'] . ' x 20, ' : '') . 
			($job['container_40'] > 0 ? $job['container_40'] . ' x 40' : '') . 
			' / ' . $job['packages'] . ' ' . $package['code'] . '</span></td>';
	}
	else {
		echo '<td width="50%"><span class="box_label">Quantity</span><br /><span class="big">' . $job['packages'] . ' ' . $package['code'] . '</span></td>';
	}
	?>
	<td width="50%"><span class="box_label">Nett Weight</span><br /><span class="big"><?php echo $job['net_weight'] . ' ' . $job['net_weight_unit'] ?></span></td>
</tr>

<tr>
	<td colspan="2"><span class="box_label">Description</span><br /><span class="big bold"><?php echo $job['details'] ?></span></td>
</tr>

<tr>
	<td width="50%"><span class="box_label">Invoice Value <?php echo $currency['code'] ?></span><br /><span class="big"><?php echo $import_details['invoice_value']?></span></td>
	<td width="50%"><span class="box_label">C.I.F. Rs.</span><br /><span class="big"></span></td>
</tr>

<tr>
	<td width="50%"><span class="box_label">Accessible Value Rs.</span><br /><span class="big"><?php echo $icegate_be['accessible_value']?></span></td>
	<td width="50%"><span class="box_label">Duty Rs.</span><br /><span class="big"><?php echo $icegate_be['total_duty_amount']?></span></td>
</tr>

<tr>
	<td width="50%"><span class="box_label">S.B. No &amp; Date</span><br /><span class="big"><?php echo $job['sb_no'] . ($job['sb_date'] == '00-00-0000' ? '<br />' : ' / ' . $job['sb_date']) ?></span></td>
	<td width="50%"><span class="box_label">T.B. No &amp; Date</span><br /><span class="big"></span></td>
</tr>

<tr>
	<td width="50%"><span class="box_label">B.G. No &amp; Date</span><br /><span class="big"><br /></span></td>
	<td width="50%"><span class="box_label">W.H.B. No &amp; Date</span><br /><span class="big"><?php echo $import_details['wh_no'] . ($import_details['wh_date'] == '00-00-0000' ? '<br />' : ' / ' . $import_details['wh_date']) ?></span></td>
</tr>

<tr>
	<td width="50%"><span class="box_label">Steamer Agent</span><br /><span class="big"><br /></span></td>
	<td width="50%"></td>
</tr>
</table>

</body>
</html>