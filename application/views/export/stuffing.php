
<table class="table table-striped table-rowselect" id="Stuffing">
<thead>
	<tr>
		<th>No</th>
		<th>Invoice No</th>
		<th>LR No</th>
		<th>Vehicle No</th>
		<th>Container No</th>
		<th>Cont. Type</th>
		<th>Line Seal No</th>
		<th>Ex/Cu Seal No</th>
		<th width="120px">Pickup Date</th>
		<th width="120px">Stuffing Date</th>
		<th width="60px">Units</th>
		<th width="80px">Unit</th>
		<th width="60px">Gross Weight</th>
		<th width="60px">Nett Weight</th>
	</tr>
</thead>

<tbody>
<?php 
	$stuffing_ids = array();
	$total = array(
		'units'        => 0,
		'gross_weight' => 0,
		'nett_weight'  => 0,
	);
	$i = 1;
	foreach ($rows['stuffing'] as $r) {
		$stuffing_ids[] = $r['id'];

		$total['units']        += $r['units'];
		$total['gross_weight'] += $r['gross_weight'];
		$total['nett_weight']  += $r['nett_weight'];
		
		echo '<tr>
			<td>' . anchor($this->_clspath.$this->_class.'/edit/'.$job_id['id'].'/'.$r['id'], $i++) . '</td>
			<td>' . $r['invoice_nos'] . '</td>
			<td>' . $r['lr_no'] . '</td>
			<td>' . $r['vehicle_no'] . '</td>
			<td>' . $r['container_no'] . '</td>
			<td class="aligncenter">' . $r['container_type'] . '</td>
			<td>' . $r['seal_no'] . '</td>
			<td>' . $r['excise_seal_no'] . '</td>
			<td>' . $r['pickup_date'] . '</td>
			<td>' . $r['stuffing_date'] . '</td>
			<td class="alignright">' . $r['units'] . '</td>
			<td>' . $r['unit'] . '</td>
			<td class="alignright">' . $r['gross_weight'] . '</td>
			<td class="alignright">' . $r['nett_weight'] . '</td>
		</tr>';
	}
	
	foreach ($rows['containers'] as $container_type_id => $container_type) {
		for($index = 0; $index < $container_type['count']; $index++) {
			echo '<tr>
				<td>' . anchor($this->_clspath.$this->_class.'/edit/'.$job_id['id'].'/0', $i++) . '</td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td class="aligncenter">' . $container_type['container_type'] . '</td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
			</tr>';
		}
	}
	?>
</tbody>

<tfoot>
	<tr>
		<th colspan="10"></th>
		<th class="alignright"><?php echo $total['units'] ?></th>
		<th></th>
		<th class="alignright"><?php echo $total['gross_weight'] ?></th>
		<th class="alignright"><?php echo $total['nett_weight'] ?></th>
	</tr>
</tfoot>
</table>
