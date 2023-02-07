<style>
td.alt { 
	background-color: #ffc !important;
	background-color: rgba(255, 255, 0, 0.2) !important;
}
</style>

<div id="modal-gatein" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">Edit</h4>
			</div>
			<div class="modal-body">
				<div class="form-group">
					<label class="control-label">Gate In Date &amp; Time</label>
					<div class="input-group date DateTimePicker">
						<span class="input-group-addon"><i class="icon-calendar"></i></span>
						<input type="text" class="form-control form-control-sm" name="datetime" value="" id="DateTime" />
					</div>
				</div>

				<div class="form-group">
					<label class="control-label">Vessel Name</label>
					<input type="hidden" name="vid" value="" id="VesselID" />
					<input type="text" class="form-control form-control-sm" name="vname" value="" id="VesselName" />
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" data-dismiss="modal" id="UpdateLocal">Update</button>
			</div>
		</div>
	</div>
</div>

<div id="FixedToolbar">
	<?php echo form_open($this->uri->uri_string(), 'id="MainForm"'); ?>
	<input type="hidden" name="vouchers" value="1" />
	<table class="table toolbar">
	<tr>
		<td><div class="input-filter-container"><input type="search" id="input-filter" class="form-control form-control-sm" placeholder="Find by Container No, Invoice No, Seal No, Gate In Date" ></div></td>
		<td class="alignright"><button type="submit" class="btn btn-success" id="Update">Update</button></td>
	</tr>
	</table>

	<table class="table table-condensed table-striped table-bordered hide" id="FixedHeader">
	<thead>
	<tr>
		<th>No</th>
		<th>Job No</th>
		<th>Party</th>
		<th>Custom Station</th>
		<th>Invoice No</th>
		<th>Container No</th>
		<th>Seal No</th>
		<th>Pickup Location</th>
		<th>Gate In</th>
		<th>Vessel Name</th>
	</tr>
	</thead>
	</table>
</div>

<table class="table table-condensed table-striped table-bordered" id="Result">
<thead>
<tr>
	<th width="24px">No</th>
	<th>Job No</th>
	<th>Party</th>
	<th>Custom Station</th>
	<th>Invoice No</th>
	<th>Container No</th>
	<th>Seal No</th>
	<th>Pickup Location</th>
	<th>Gate In</th>
	<th width="200px">Vessel Name</th>
</tr>
</thead>

<tbody>
<?php 
	$total = 0;
	$i = 1;
	foreach ($rows as $r) {
		$readonly = 'readonly="true"';
		if ($r['gate_in'] == '00-00-0000 00:00:00')
			$readonly = '';

		echo '<tr id="' . $r['id'] . '">
	<td class="aligncenter">' . $i++ . '</td>
	<td class="tiny">' . $r['id2_format'] . '</td>
	<td class="tiny">' . $r['party_name'] . '</td>
	<td class="tiny">' . $r['custom_port'] . '</td>
	<td class="tiny">' . $r['invoice_no'] . '</td>
	<td>' . $r['container_no'] . '</td>
	<td class="tiny">' . $r['seal_no'] . '</td>
	<td class="tiny">' . $r['pickup_location'] . '</td>
	<td class="tiny GateIn"><input type="hidden" name="gate_in[' . $r['id'] . ']" value="' . $r['gate_in']  . '" /><span>' . $r['gate_in'] . '</span></td>
	<td class="tiny Vessel"><input type="hidden" name="vessel_id[' . $r['id'] . ']" value="' . $r['vessel_id'] . '" />
		<input type="text" class="hidden" value="' . $r['vessel_name'] . '" /><span>' . $r['vessel_name'] . '</span></td>
</tr>';
	}
	echo '</tbody>
</table>
</form>';
?>

<script>
var id = 0;

$(document).ready(function() {
	$("#Result").find('thead tr').children().each(function(i, e) {
		$($("#FixedHeader").find('thead tr').children()[i]).width($(e).width());
	});
	$("#FixedHeader").width($("#Result").width());

	if (!($.browser == "msie" && $.browser.version < 7)) {
		var target = "div#FixedToolbar";
		$(window).scroll(function(event) {
			$(target).css({
				left: ($("#Result").offset().left - $(window).scrollLeft()) + 'px'
			});
			if ($(this).scrollTop() > 25) {
				$(target).addClass("fixedTop show");
				$("table#FixedHeader").removeClass("hide");
			} else {
				$(target).removeClass("fixedTop show");
				$("table#FixedHeader").addClass("hide");
			}
		});
	}

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

	$("#Result tr").on("click", function() {
		id = $(this).attr("id");
		$('#DateTime').val($(this).find('.GateIn input:eq(0)').val());
		$('#VesselID').val($(this).find('.Vessel input:eq(0)').val());
		$('#VesselName').val($(this).find('.Vessel input:eq(1)').val());
		$("#modal-gatein").modal();
	});

	$('#UpdateLocal').on('click', function() {
		$('#Result tr#'+id).find('.GateIn input:eq(0)').val($('#DateTime').val());
		$('#Result tr#'+id).find('.GateIn span:eq(0)').text($('#DateTime').val());
		$('#Result tr#'+id).find('.GateIn span:eq(0)').addClass('red');
		$('#Result tr#'+id).find('.Vessel input:eq(0)').val($('#VesselID').val());
		$('#Result tr#'+id).find('.Vessel input:eq(1)').val($('#VesselName').val());
		$('#Result tr#'+id).find('.Vessel span:eq(0)').text($('#VesselName').val());
		$('#Result tr#'+id).find('.Vessel span:eq(0)').addClass('red');
	});

	$('#VesselName').kaabar_autocomplete({
		source: '<?php echo site_url('master/vessel/ajax') ?>',
		appendTo: '#modal-gatein'
	});
});
</script>