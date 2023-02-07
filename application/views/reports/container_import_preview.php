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
		$header = str_replace('{{company_logo}}', '<img src="data:image/png;base64,' . base64_encode($image_data) . '" width="100%" />', $company['letterhead']);
	}
	else  {
		$header = str_replace('{{company_logo}}', '<h2>' . $company['name'] . '</h2>', $company['letterhead']);
	}
	
	$header = str_replace('<p>&nbsp;</p>', '', $header);
	$header = str_replace('{{company_name}}', $company['name'], $header);
	$header = str_replace('{{company_address}}', $company['address'], $header);
	$header = str_replace('{{company_city}}', (isset($city['name']) ? $city['name'] . '-' . $city['pincode'] . ' ' . $state['name'] : ''), $header);
	$header = str_replace('{{company_contact}}', $company['contact'], $header);
	$header = str_replace('{{company_email}}', $company['email'], $header);
	$header = str_replace('{{company_pan_no}}', $company['pan_no'], $header);
	$header = str_replace('{{company_tan_no}}', $company['tan_no'], $header);
	$header = str_replace('{{company_service_tax_no}}', $company['service_tax_no'], $header);
	$header = str_replace('{{company_cha_no}}', $company['cha_no'], $header);
	$header = str_replace('{{company_cha_license_no}}', $company['cha_license_no'], $header);
	$header = str_replace('{{company_cin_no}}', $company['cin_no'], $header);
	echo $header;
	?>
</tr>
</table>
<h3 class="aligncenter"><?php echo $page_title ?></h3>
<table class="details">
<thead>
<tr>
	<th>No</th>
	<th>Size</th>
	<th>Code</th>
	<th>Name</th>
	<th>Number</th>
	<th>Seal No</th>
	<th>Received weight</th>
</tr>
</thead>

<tbody>
 <?php 
	$i = 1;
	$total = array(
		'net_weight' => 0,
	);
	foreach ($rows as $r) {
		$total['net_weight'] += $r['net_weight'];
		echo '<tr>
	<td class="aligncenter">' . $i++ . '</td>
	<td>' . $r['size'] . '</td>
	<td>' . $r['code'] . '</td>
	<td>' . $r['name'] . '</td>
	<td>' . $r['number'] . '</td>
	<td>' . $r['seal'] . '</td>
	<td class="alignright">' . $r['net_weight'] . '</td>
</tr>';
	}
	

?>
</tbody>

<tfoot>
<tr>
<th class="alignright" colspan="6">Total</th>
<th class="alignright"><?php echo $total['net_weight'] ?></th>
	
</tr>
</tfoot>
</table>


</body>
</html>