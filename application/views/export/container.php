<table class="table table-condensed table-striped">
<thead>
	<tr>
		<th>No</th>
		<th>ID</th>
		<th>Container Type</th>
		<th>Number</th>
		<th>Seal</th>
		<th>Seal Date</th>
	</tr>
</thead>

<tbody>
	<?php 
	$i = 1;
	foreach ($rows as $r) {
		echo '<tr>
		<td>' . $i++ . '</td>
		<td>' . $r['id'] . '</td>
		<td>' . $r['container_type'] . '</td>
		<td>' . $r['container_no'] . '</td>
		<td>' . $r['seal_no'] . '</td>
		<td>' . $r['seal_date'] . '</td>
		</tr>';
	} ?>
</tbody>
</table>