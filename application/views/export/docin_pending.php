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
			<?php echo form_open($this->_clspath.$this->_class.'/updateRow', 'id="formRow"'); ?>
				<input type="hidden" name="row_id" value="0" id="rowID" />
				<div class="modal-body">
					<div class="form-group">
						<label class="control-label">Gate In Date &amp; Time</label>
						<div class="input-group date DateTimePicker">
							<span class="input-group-addon"><i class="icon-calendar"></i></span>
							<input type="text" class="form-control form-control-sm" name="datetime" value="" id="DateTime" />
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary" data-dismiss="modal" id="UpdateRow">Update</button>
				</div>
			</form>
		</div>
	</div>
</div>

<div id="FixedToolbar">
	<?php echo form_open($this->uri->uri_string(), 'id="MainForm"'); ?>
	<input type="hidden" name="vouchers" value="1" />
	<table class="table toolbar">
	<tr>
		<td><div class="input-filter-container"><input type="search" id="input-filter" class="form-control form-control-sm" placeholder="Find by Container No, Invoice No, Seal No, Doc Handover Date" ></div></td>
		<td class="alignright"><button type="submit" class="btn btn-success" id="Update">Update</button></td>
	</tr>
	</table>

	<table class="table table-condensed table-striped table-bordered hide" id="FixedHeader">
	<thead>
	<tr>
		<th width="24px">No</th>
		<th>Job No</th>
		<th>Party</th>
		<th>Custom Station</th>
		<th>Invoice No</th>
		<th>SB No</th>
		<th>Doc Handover</th>
		<th>Vessel</th>
		<th>Pickup Location</th>
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
	<th>SB No</th>
	<th>Doc Handover</th>
	<th>Vessel</th>
	<th>Pickup Location</th>
</tr>
</thead>

<tbody>
<?php 
	$total = 0;
	$i = 1;
	foreach ($rows as $r) {
		echo '<tr id="' . $r['id'] . '">
	<td class="aligncenter">' . $i++ . '</td>
	<td class="nowrap">' . $r['id2_format'] . '</td>
	<td>' . $r['party_name'] . '</td>
	<td class="tiny">' . $r['custom_port'] . '</td>
	<td class="nowrap">' . $r['invoice_no'] . '</td>
	<td>' . $r['sb_no'] . '</td>
	<td class="nowrap DocIn"><input type="hidden" name="doc_handover[' . $r['id'] . ']" value="' . $r['doc_handover']  . '" /><span>' . $r['doc_handover'] . '</span></td>
	<td class="tiny">' . $r['vessel_name'] . '</td>
	<td class="tiny">' . $r['pickup_location'] . '</td>
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

	$("#Result tr td").on("click", function() {
		id = parseInt($(this).parent('tr').attr("id"));
		$('#rowID').val(id);
		$('#DateTime').val($(this).find('.DocIn input:eq(0)').val());
		$("#modal-gatein").modal();
	});

	/*$('#UpdateLocal').on('click', function() {
		$('#Result tr#'+id).find('.DocIn input:eq(0)').val($('#DateTime').val());
		$('#Result tr#'+id).find('.DocIn span:eq(0)').text($('#DateTime').val());
		$('#Result tr#'+id).find('.DocIn span:eq(0)').addClass('red');
	});*/

	$('#UpdateRow').on('click', function(e) {
		e.preventDefault();
		form = $('#formRow');
		$.ajax({
			url: form.attr('action'),
			type: 'POST',
			dataType: 'json',
			data: form.serialize(),
		})
		.done(function(data) {
			$('tr#'+data.id+' td.DocIn').empty();
			$('tr#'+data.id+' td.DocIn').text(data.doc_handover);
			$('tr#'+data.id+' td.DocIn').addClass('green');
		})
		.fail(function() {
			
		});
	});
});
</script>