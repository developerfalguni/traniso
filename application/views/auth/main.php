<div class="row">
	<div class="col-md-5">
<table class="table table-striped">
<thead>
<tr>
	<th>Count</th>
	<th>Detail</th>
</tr>
</thead>

<tbody>
<tr>
	<td class="count color-dkorange"><?php echo $counts['new'] ?></td>
	<td class="color-dkorange">New Users Awaiting Activation</td>
</tr>

<tr>
	<td class="count color-green"><?php echo $counts['active'] ?></td>
	<td class="color-green">Total Active Users</td>
</tr>

<tr>
	<td class="count color-blue"><?php echo $counts['logged'] ?></td>
	<td class="color-blue">Logged In Users</td>
</tr>

<tr>
	<td class="count color-orange"><?php echo $counts['suspended'] ?></td>
	<td class="color-orange">Suspended Users</td>
</tr>

<tr>
	<td class="count color-red"><?php echo $counts['disabled'] ?></td>
	<td class="color-red">Disabled Users</td>
</tr>
</tbody>
</table>
	</div>
</div>