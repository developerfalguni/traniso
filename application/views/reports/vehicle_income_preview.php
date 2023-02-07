<!DOCTYPE html>
<html lang="en">
<head>
	<title><?php echo $page_title ?></title>
	<style>
		<?php 
			echo file_get_contents(FCPATH.'assets/css/print.css'); 
			$image_data = file_get_contents(FCPATH.'php_uploads/'.$company['logo']);
		?>
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
			echo '<td width="60%"><img src="data:image/png;base64,' . base64_encode($image_data) . '" height="80px" /></td>';
			echo '<td class="alignright" valign="top"><span class="tiny">' . $company['address'] . '<br />' . (isset($city['name']) ? $city['name'] . '-' . $city['pincode'] . ' ' . $state['name'] . '<br />' : '') . $company['contact'] . '<br />' . $company['email'] . '</span></td>';
		}
	}
	?>
</tr>
</table>

<table class="details" id="Result">
<thead>
<tr>
	<th width="24px">No</th>
	<th>Date</th>
	<th>Container</th>
	<th>Size</th>
	<th>From</th>
	<th>To</th>
	<th>Party</th>
	<th>Vehicle No</th>
	<th>LR No</th>
	<th>Party Rate</th>
	<th>Trans Rate</th>
	<th>Self Adv</th>
	<th>Party Adv</th>
	<th>Pump Adv</th>
	<th>Advance</th>
	<th>Balance</th>
</tr>
</thead>

<tbody>
<?php 
$total = array(
	'transporter_rate' => 0,
	'party_rate'       => 0,
	'self_adv'         => 0,
	'party_adv'        => 0,
	'pump_adv'         => 0,
	'allowance'        => 0,
	'balance'          => 0,
	'expenses'         => 0,
);
foreach ($rows as $ledger_id => $vehicles) {
	$i = 1;
	$group = array(
		'transporter_rate' => 0,
		'party_rate'       => 0,
		'self_adv'         => 0,
		'party_adv'        => 0,
		'pump_adv'         => 0,
		'allowance'        => 0,
		'balance'          => 0,
	);
	$registration_no = '';
	foreach ($vehicles['trips'] as $r) {

		$total['transporter_rate'] = bcadd($total['transporter_rate'], $r['transporter_rate']);
		$total['party_rate']       = bcadd($total['party_rate'], $r['party_rate']);
		$total['self_adv']         = bcadd($total['self_adv'], $r['self_adv']);
		$total['party_adv']        = bcadd($total['party_adv'], $r['party_adv']);
		$total['pump_adv']         = bcadd($total['pump_adv'], $r['pump_adv']);
		$total['allowance']        = bcadd($total['allowance'], $r['allowance']);
		$total['balance']          = bcadd($total['balance'], $r['balance']);

		$group['transporter_rate'] = bcadd($group['transporter_rate'], $r['transporter_rate']);
		$group['party_rate']       = bcadd($group['party_rate'], $r['party_rate']);
		$group['self_adv']         = bcadd($group['self_adv'], $r['self_adv']);
		$group['party_adv']        = bcadd($group['party_adv'], $r['party_adv']);
		$group['pump_adv']         = bcadd($group['pump_adv'], $r['pump_adv']);
		$group['allowance']        = bcadd($group['allowance'], $r['allowance']);
		$group['balance']          = bcadd($group['balance'], $r['balance']);

		echo '<tr>
		<td class="aligncenter tiny">' . $i++ . '</td>
		<td class="aligncenter tiny">' . $r['date'] . '</td>
		<td class="tiny">' . $r['container_no'] . '</td>
		<td class="tiny">' . $r['container_size'] . '</td>
		<td class="tiny">' . $r['from_location'] . '</td>
		<td class="tiny">' . $r['to_location'] . '</td>
		<td class="tiny">' . $r['party_name'] . '</td>
		<td class="tiny">' . $r['registration_no'] . '</td>
		<td class="tiny">' . $r['lr_no'] . '</td>
		<td class="alignright tiny">' . inr_format($r['party_rate']) . '</td>
		<td class="alignright tiny">' . inr_format($r['transporter_rate']) . '</td>
		<td class="alignright tiny">' . inr_format($r['self_adv']) . '</td>
		<td class="alignright tiny">' . inr_format($r['party_adv']) . '</td>
		<td class="alignright tiny">' . inr_format($r['pump_adv']) . '</td>
		<td class="alignright tiny">' . inr_format($r['allowance']) . '</td>
		<td class="alignright tiny">' . inr_format($r['balance']) . '</td>
	</tr>';
		$registration_no = $r['registration_no'];
	}

	$total['expenses'] = bcadd($total['expenses'], (isset($vehicles['closing']) ? $vehicles['closing'] : 0));

	echo '<tr>
		<td class="alignright bold" colspan="8">(' . $registration_no . ') Total</td>
		<td></td>
		<td class="alignright bold">' . inr_format($group['party_rate']) . '</td>
		<td class="alignright bold">' . inr_format($group['transporter_rate']) . '</td>
		<td class="alignright bold">' . inr_format($group['self_adv']) . '</td>
		<td class="alignright bold">' . inr_format($group['party_adv']) . '</td>
		<td class="alignright bold">' . inr_format($group['pump_adv']) . '</td>
		<td class="alignright bold">' . inr_format($group['allowance']) . '</td>
		<td class="alignright bold">' . inr_format($group['balance']) . '</td>
	</tr>

	<tr>
		<td class="alignright bold" colspan="15">(' . $registration_no . ') Expenses</td>
		<td class="alignright bold">' . inr_format((isset($vehicles['closing']) ? $vehicles['closing'] : 0)) . '</td>
	</tr>

	<tr>
		<td class="alignright bold" colspan="15">(' . $registration_no . ') Net Amount</td>
		<td class="alignright bold">' . inr_format($group['balance'] - (isset($vehicles['closing']) ? $vehicles['closing'] : 0)) . '</td>
	</tr>';
} 
?>
</tbody>

<tfoot>
<tr>
	<th class="alignright" colspan="8">Grand Total</th>
	<th></th>
	<th class="alignright"><?php echo inr_format($total['party_rate']) ?></th>
	<th class="alignright"><?php echo inr_format($total['transporter_rate']) ?></th>
	<th class="alignright"><?php echo inr_format($total['self_adv']) ?></th>
	<th class="alignright"><?php echo inr_format($total['party_adv']) ?></th>
	<th class="alignright"><?php echo inr_format($total['pump_adv']) ?></th>
	<th class="alignright"><?php echo inr_format($total['allowance']) ?></th>
	<th class="alignright"><?php echo inr_format($total['balance']) ?></th>
</tr>

<tr>
	<th class="alignright bold" colspan="15">Total Expenses</th>
	<th class="alignright bold"><?php echo inr_format($total['expenses']) ?></th>
</tr>

<tr>
	<th class="alignright bold" colspan="15">Net Amount</th>
	<th class="alignright bold"><?php echo inr_format($total['balance'] - $total['expenses']) ?></th>
</tr>
</tfoot>
</table>

</body>
</html>