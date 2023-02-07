<style>
td.alt { 
	background-color: #ffc !important;
	background-color: rgba(255, 255, 0, 0.2) !important;
}
</style>

<?php 
echo form_open($this->uri->uri_string(), 'id="MainForm"');	
echo form_hidden($voucher_id);
?>

<input type="hidden" name="vouchers" value="1" />
<table class="table toolbar">
<tr>
	<td><div class="input-filter-container"><input type="search" id="input-filter" class="form-control form-control-sm" placeholder="Find by Vehicle No, Gatepass" /></div></td>
	<td class="big" id="TripCount"></td>
	<td class="alignright"><button type="submit" class="btn btn-success" id="Update">Update</button>
		<button type="button" class="btn btn-danger" id="Close">Close</button></td>
</tr>
</table>

<table class="table table-condensed table-striped table-bordered" id="Result">
<thead>
<tr>
	<th>Date</th>
	<th>Job No</th>
	<th>Container No</th>
	<th>Size</th>
	<th>Vehicle No</th>
	<th>Party / <span class="orange">Party Ref. No</span></th>
	<th>Transporter</th>
	<th>Rate</th>
	<th>LR No</th>
	<th>Trip Adv.</th>
	<th>Pump Adv.</th>
	<th>Paid</th>
	<th>Balance</th>
	<th width="24px" class="aligncenter"><a href="#" class="CheckAll"><i class="icon-check"></i></a></th>
</tr>
</thead>

<tbody>
<?php 
	$total = 0;
	foreach ($rows as $r) {
		echo '<tr>
	<td class="tiny">' . anchor('transport/trip/edit/Container/'.$r['trip_id'], $r['date'], 'target="_blank"') . '</td>
	<td class="tiny">' . $r['job_no'] . '</td>
	<td class="tiny">' . $r['container_no'] . '</td>
	<td class="tiny">' . $r['container_size'] . '</td>
	<td class="tiny">' . ($r['self'] ? '<span class="label label-success">' . $r['registration_no'] . '</span>' : $r['registration_no']) . '</td>
	<td class="tiny">' . $r['party_name'] . '<br /><span class="orange">' . $r['party_reference_no'] . '</span></td>
	<td class="tiny">' . $r['transporter_name'] . '</td>
	<td class="tiny">' . $r['transporter_rate'] . '</td>
	<td class="tiny">' . $r['lr_no'] . '</td>
	<td class="tiny">' . $r['trip_advance'] . '</td>
	<td class="tiny">' . $r['pump_advance'] . '</td>
	<td class="tiny">' . $r['paid_amount'] . '</td>
	<td class="tiny">' . $r['balance'] . '</td>
	<td class="aligncenter">' . form_checkbox(array(
		'name'    => 'trip_id['.$r['trip_id'].']', 
		'value'   => $r['trip_id'], 
		'checked' => ($r['id'] > 0 ? true : false), 
		'class'   => 'DeleteCheckbox'
	)) . '</td>
</tr>';
	}

	echo '</tbody>
</table>
</form>';
?>

<script>
$(document).ready(function() {
	var stripeTable = function(table) {
		table.find('tr').removeClass('striped').filter(':visible:even').addClass('striped');
	};
	$("#Result").filterTable({
		callback: function(term, table) { 
			stripeTable(table); 
		},
		inputSelector: '#input-filter'
	});
	stripeTable($("#Result"));

	$("#Close").on("click", function() {
		top.opener.location.reload(true);
		self.close();
	});

	$('.CheckAll').on('hover', function() {
		$('#TripCount').text($('.DeleteCheckbox:checked').length);
	});

	$('.CheckAll').on('hover', function() {
		$('#TripCount').text($('.DeleteCheckbox:checked').length);
	});

	$('.DeleteCheckbox').on('change', function() {
		$('#TripCount').text($('.DeleteCheckbox:checked').length);
	});

	$('#TripCount').text($('.DeleteCheckbox:checked').length);
});
</script>