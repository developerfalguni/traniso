<style>
.GroupTotal { background-color: #eee !important; }
</style>

<div id="modal-document" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
		<?php echo form_open_multipart($this->_clspath.$this->_class.'/attach/'.$row['id']); ?>
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><i class="icon-times"></i></button>
				<h3>Attach Bill</h3>
			</div>
			<div class="modal-body">
				<input type="file" name="userfile" size="40" />
			</div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-success">Upload</button>
			</div>
		</form>
		</div>
	</div>
</div>

<div id="modal-detach" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal"><i class="icon-times"></i></button>
			<h3>Confirm Deletion</h3>
			</div>
			<div class="modal-body"><p>Are you sure, you want to DETACH...?</p></div>
			<div class="modal-footer">
			<?php echo anchor("#", 'Detach', 'class="btn btn-danger" id="DetachUrl"') ?>
			</div>
		</div>
	</div>
</div>

<?php
echo form_open($this->uri->uri_string(), 'id="MainForm"');
echo form_hidden($id);
?>

<div class="card card-default">
	<div class="card-header">
		<h3 class="card-title"><span class=""><?php echo anchor($this->_clspath.$this->_class, '<i class="fa fa-list pr-1"></i>'. strtoupper($page_title)) ?></span></h3>
		<div class="card-tools">
  			<ol class="breadcrumb float-sm-right m-0">
      			<li class="breadcrumb-item"><a href="#"><?php echo anchor('main','Dashboard') ?></a></li>
      			<li class="breadcrumb-item"><?php echo humanize(clean($this->_clspath)) ?></li>
      			<li class="breadcrumb-item active mr-1"><?php echo humanize($this->_class) ?> edit</li>
    		</ol>
		</div>
	</div>
	
	<div class="card-body">
		<fieldset>
			<div class="row">
				<div class="col-md-2">
					<div class="form-group">
						<label class="control-label">Bill Type</label>
						<?php echo form_dropdown('type', getEnumSetOptions('trip_inwards', 'type'), $row['type'], 'class="form-control form-control-sm" id="BillType"' . ($id['id'] > 0 ? 'disabled' : null)); ?>
					</div>
				</div>

				<div class="col-md-2">
					<div class="form-group">
						<label class="control-label">Rishi Bill No</label>
						<div class="form-group<?php echo (strlen(form_error('rishi_bill_no')) > 0 ? ' has-error' : '') ?>">
							<input type="text" class="form-control form-control-sm" name="rishi_bill_no" value="<?php echo $row['rishi_bill_no'] ?>" id="BillNo" />
						</div>
					</div>
				</div>

				<div class="col-md-2">
					<div class="form-group <?php echo (strlen(form_error('date')) > 0 ? 'has-error' : '') ?>">
						<label class="control-label">Bill Date</label>
						<div class="input-group date DatePicker">
							<span class="input-group-addon"><i class="icon-calendar"></i></span>
							<input type="text" class="form-control form-control-sm AutoDate" name="date" value="<?php echo ($row['date'] != '00-00-0000' ? $row['date'] : '') ?>" />
						</div>
					</div>
				</div>

				<div class="col-md-2">
					<div class="form-group">
						<label class="control-label">Transporter Bill No</label>
						<div class="form-group<?php echo (strlen(form_error('bill_no')) > 0 ? ' has-error' : '') ?>">
						<input type="text" class="form-control form-control-sm" name="bill_no" value="<?php echo $row['bill_no'] ?>" />
						</div>
					</div>
				</div>

				<div class="col-md-2">
					<div class="form-group<?php echo (strlen(form_error('bill_date')) > 0 ? ' has-error' : '') ?>">
						<label class="control-label">Transporter Bill Date</label>
						<div class="input-group date DatePicker">
							<span class="input-group-addon"><i class="icon-calendar"></i></span>
							<input type="text" class="form-control form-control-sm AutoDate" name="bill_date" value="<?php echo ($row['bill_date'] != '00-00-0000' ? $row['bill_date'] : '') ?>" />
						</div>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-md-4">
					<div class="form-group">
						<label class="control-label">Transporter Name</label>
						<div class="form-group<?php echo (strlen(form_error('transporter_ledger_id')) > 0 ? ' has-error' : '') ?>">
							<input type="hidden" name="transporter_ledger_id" value="<?php echo $row['transporter_ledger_id'] ?>" id="TransporterLedgerID" />
							<input type="text" class="form-control form-control-sm" value="<?php echo $transporter_name ?>" id="TransporterName" />
						</div>
					</div>
				</div>


				<div class="col-md-2">
					<div class="form-group">
						<label class="control-label">Cheque No</label>
						<div class="form-group<?php echo (strlen(form_error('cheque_no')) > 0 ? ' has-error' : '') ?>">
						<input type="text" class="form-control form-control-sm" name="cheque_no" value="<?php echo $row['cheque_no'] ?>" />
						</div>
					</div>
				</div>

				<div class="col-md-2">
					<div class="form-group <?php echo (strlen(form_error('cheque_date')) > 0 ? 'has-error' : '') ?>">
						<label class="control-label">Cheque Date</label>
						<div class="input-group date DatePicker">
							<span class="input-group-addon"><i class="icon-calendar"></i></span>
							<input type="text" class="form-control form-control-sm AutoDate" name="cheque_date" value="<?php echo ($row['cheque_date'] != '00-00-0000' ? $row['cheque_date'] : '') ?>" />
						</div>
					</div>
				</div>

				<div class="col-md-2">
					<div class="form-group <?php echo (strlen(form_error('processed_date')) > 0 ? 'has-error' : '') ?>">
						<label class="control-label">Processed Date</label>
						<div class="input-group date DatePicker">
							<span class="input-group-addon"><i class="icon-calendar"></i></span>
							<input type="text" class="form-control form-control-sm AutoDate" name="processed_date" value="<?php echo ($row['processed_date'] != '00-00-0000' ? $row['processed_date'] : '') ?>" />
						</div>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-md-2">
					<div class="form-group">
						<label class="control-label">From</label>
						<div class="input-group date DatePicker">
							<span class="input-group-addon"><i class="icon-calendar"></i></span>
							<input type="text" class="form-control form-control-sm AutoDate" name="from_date" value="<?php echo $from_date ?>" id="FromDate" />
						</div>
					</div>
				</div>

				<div class="col-md-2">
					<div class="form-group">
						<label class="control-label">To</label>
						<div class="input-group date DatePicker">
							<span class="input-group-addon"><i class="icon-calendar"></i></span>
							<input type="text" class="form-control form-control-sm AutoDate" name="to_date" value="<?php echo $to_date ?>" id="ToDate" />
						</div>
					</div>
				</div>

				<div class="col-md-2">
					<div class="form-group">
						<label class="control-label">Party Ref.</label>
						<input type="text" class="form-control form-control-sm" name="party_reference_no" value="" id="PartyReference" />
					</div>
				</div>

				<div class="col-md-2">
					<div class="form-group">
						<label class="control-label">From Location</label>
						<input type="hidden" id="FromLocationID" />
						<input type="text" class="form-control form-control-sm" value="" id="FromLocation" />
					</div>
				</div>

				<div class="col-md-2">
					<div class="form-group">
						<label class="control-label">To Location</label>
						<input type="hidden" id="ToLocationID" />
						<input type="text" class="form-control form-control-sm" value="" id="ToLocation" />
					</div>
				</div>

				<div class="col-md-2">
					<br />
					<button type="button" class="btn btn-primary" id="LoadTrips"><i class="icon icon-refresh"></i> Load Trips</button>
				</div>
			</div>

			<div class="row">
				<div class="col-md-10">
					<div class="form-group">
						<label class="control-label">Remarks</label>
						<div class="form-group<?php echo (strlen(form_error('remarks')) > 0 ? ' has-error' : '') ?>">
						<input type="text" class="form-control form-control-sm" name="remarks" value="<?php echo $row['remarks'] ?>" />
						</div>
					</div>
				</div>

				<div class="col-md-2">
					<div class="form-group">
						<label class="control-label">Document</label><br />
						<?php foreach ($documents as $r) {
								echo '<a href="' . $r['url'] . '" class="Popup"><i class="icon-paperclip"></i></a>&nbsp;';
							}
							echo '<a href="#modal-document" class="btn btn-xs btn-success" data-toggle="modal"><i class="fa fa-plus"></i></a>';
						?>
					</div>
				</div>
			</div>

			<table class="table table-condensed table-bordered hide" id="FixedHeader">
			<thead>
			<tr>
				<th width="34px">Sr No</th>
				<th>Container No</th>
				<th>Qty</th>
				<th>Job No</th>
				<th>Trailer No</th>
				<th>Trip Date</th>
				<th>Party Name</th>
				<th>Cargo</th>
				<th>From</th>
				<th>To</th>
				<th width="60px">Fuel Slip</th>
				<th width="60px">Fuel</th>
				<th class="aligncenter"><a href="#" class="CheckAll"><i class="icon-trashcan"></i></a></th>
			</tr>
			</thead>
			</table>

			<table class="table table-condensed table-bordered DataEntry" id="Trips">
			<thead>
			<tr>
				<th width="34px">Sr No</th>
				<th>Container No</th>
				<th>Qty</th>
				<th>Job No</th>
				<th>Trailer No</th>
				<th>Transporter</th>
				<th>Trip Date</th>
				<th>Party Name</th>
				<th>Cargo</th>
				<th>From</th>
				<th>To</th>
				<th width="60px">Fuel Slip</th>
				<th width="60px">Fuel</th>
				<th width="24px" class="aligncenter"><a href="#" class="CheckAll"><i class="icon-trashcan"></i></a></th>
			</tr>
			</thead>

			<tbody>
			<?php
				$sr_no = 1;
				$total = array('fuel' => 0, 'amount' => 0, 'advance' => 0);
				foreach ($rows as $r) {
					$total['fuel']      += $r['fuel'];
					
					// echo '<tr class="Details tiny hide">
					echo '<tr class="Details">
					<td class="aligncenter">' . anchor('/transport/trip/edit/container/'.$r['trip_id'], $sr_no++, 'target="_blank"') . '</td>
					<td>' . $r['container_no'] . '</td>
					<td>' . $r['qty'] . '</td>
					<td>' . $r['job_no'] . '</td>
					<td>' . ($r['self'] ? '<span class="label label-success">' . $r['registration_no'] . '</span>' : $r['registration_no']) . '</td>
					<td>' . $r['transporter_name'] . '</td>
					<td>' . $r['date'] . '</td>
					<td>' . $r['party_name'] . '</td>
					<td>' . $r['product_name'] . '</td>
					<td>' . $r['from_location'] . '</td>
					<td>' . $r['to_location'] . '</td>
					<td class="alignright FuelSlipNo">' . $r['slip_no'] . '</td>
					<td class="alignright">' . $r['fuel'] . '</td>
					<td class="aligncenter ignoreClicks">' . form_checkbox(array('name' => 'delete_id['.$r['id'].']', 'value' => $r['id'], 'checked' => false, 'class' => 'DeleteCheckbox')) . '</td>
				</tr>';			
				}
			?>

			<tr class="TemplateRow">
				<td><input type="text" class="form-control form-control-sm Numeric Validate Unchanged Increment" name="new_sr_no[]" value="<?php echo $sr_no ?>" /></td>
				<td><input type="hidden" class="form-control form-control-sm Validate" name="new_trip_id[]" value="" />
					<input type="text" class="form-control form-control-sm ContainerNo Validate Focus" value="" /></td>
				<td class="Qty"></td>
				<td class="JobNo"></td>
				<td class="VehicleNo"></td>
				<td class="TripDate"></td>
				<td class="TransporterName"></td>
				<td class="PartyName"></td>
				<td class="ProductName"></td>
				<td class="FromLocation"></td>
				<td class="ToLocation"></td>
				<td><input type="hidden" class="form-control form-control-sm PumpAdvanceID" name="new_pump_advance_id[]" value="" />
					<input type="text" class="form-control form-control-sm FuelSlipNo" value="" readonly="true" /></td>
				<td class="alignright Fuel"></td>
				<td class="aligncenter"><button type="submit" class="btn btn-success btn-sm AddButton"><i class="icon-white icon-plus"></i></button></td>
			</tr>
			
			<?php
				echo '<tr>
					<th colspan="12" class="alignright">Total</th>
					<th class="alignright">'. $total['fuel'] .'</th>
					<th width="24px" class="aligncenter"><a href="#" class="CheckAll"><i class="icon-trashcan"></i></a></th>
				</tr>';
			?>
			</tbody>

			</table>
		</fieldset>		
	</div>

	<div class="card-footer">
		<button type="submit" class="btn btn-success" id="Update">Update</button>
		<?php if($row['id'] > 0) {
			echo '<div class="btn-group">' . 
			anchor($this->_clspath.$this->_class."/fuel_preview/".$row['id'], "Preview", 'class="btn btn-default Popup"') . 
			anchor($this->_clspath.$this->_class."/fuel_preview/".$row['id']."/1", "PDF", 'class="btn btn-default Popup"') . 
			'</div>';
		}?>
	</div>
</div>
</form>

<script>
var details = 1;

$(document).ready(function() {
	$("#Delivery").find('thead tr').children().each(function(i, e) {
	    $($("#FixedHeader").find('thead tr').children()[i]).width($(e).width());
	});
	$("#FixedHeader").width($("#Delivery").width());

	if (!($.browser == "msie" && $.browser.version < 7)) {
        var target = "table#FixedHeader"; //, top = $(target).offset().top - parseFloat($(target).css("margin-top").replace(/auto/, 0));
        $(window).scroll(function(event) {
            if ($(this).scrollTop() > 155) {
                $(target).addClass("fixedTop show");
                $(target).removeClass("hide");
            } else {
                $(target).removeClass("fixedTop show");
                $(target).addClass("hide");
            }
        });
    }

	$('#TransporterName').autocomplete({
		source: '<?php echo site_url('/accounting/ledger/ajax/Agent/Pump') ?>',
		minLength: 1,
		focus: function(event, ui) {
			$(this).val(ui.item.name);
			return false;
		},
		select: function(event, ui) {
			$(this).prevAll('input').val(ui.item.id);
			$(this).val(ui.item.name);
			return false;
		},
		response: function(event, ui) {
			if (ui.content.length === 0) {
				$(this).prevAll('input').val(0);
				$(this).val('');
			}
		}
	})
	.data('ui-autocomplete')._renderItem = function(ul, item) {
		return $('<li></li>')
		.data('item.autocomplete', item)
		.append('<a><strong>'  + item.code + '</strong> ' + item.name + '</a>')
		.appendTo(ul);
	};

	$('#FromLocation').kaabar_autocomplete({
		source: '<?php echo base_url($this->_clspath.$this->_class."/json/locations/id/name") ?>',
	});

	$('#ToLocation').kaabar_autocomplete({
		source: '<?php echo base_url($this->_clspath.$this->_class."/json/locations/id/name") ?>',
	});

	$('#LoadTrips').click(function(event) {
		var from_date        = $('#FromDate').val();
		var to_date          = $('#ToDate').val();
		var party_ref        = $('#PartyReference').val();
		var transporter_ledger_id = $('#TransporterLedgerID').val();
		var from_location_id = $('#FromLocationID').val();
		var to_location_id   = $('#ToLocationID').val();

		$.ajax({
			url: '<?php echo base_url($this->_clspath.$this->_class."/ajaxFuelTrips") ?>',
			type: 'POST',
			dataType: 'html',
			data: {
				from_date: from_date,
				to_date: to_date,
				party_reference_no: party_ref,
				transporter_ledger_id: transporter_ledger_id,
				from_location_id: from_location_id,
				to_location_id: to_location_id,
			},
		})
		.done(function(data) {
			$(data).insertBefore('#Trips tr.TemplateRow');
		})
		.fail(function() {
			console.log('Error');
		})
	});

	$('.DataEntry').on('keydown.autocomplete', ".ContainerNo", function(event, items) {
		var id              = $(this).prevAll('input');
		var job_no          = $(this).parent('td').parent('tr').find('.JobNo');
		var qty             = $(this).parent('td').parent('tr').find('.Qty');
		var registration_no = $(this).parent('td').parent('tr').find('.VehicleNo');
		var trip_date       = $(this).parent('td').parent('tr').find('.TripDate');
		var party           = $(this).parent('td').parent('tr').find('.PartyName');
		var product         = $(this).parent('td').parent('tr').find('.ProductName');
		var from            = $(this).parent('td').parent('tr').find('.FromLocation');
		var to              = $(this).parent('td').parent('tr').find('.ToLocation');
		var advance         = $(this).parent('td').parent('tr').find('.Allowance');
		var fuel            = $(this).parent('td').parent('tr').find('.Fuel');
		var slip_no         = $(this).parent('td').parent('tr').find('.FuelSlipNo');
		var pump_advance_id = $(this).parent('td').parent('tr').find('.PumpAdvanceID');
		var amount          = $(this).parent('td').parent('tr').find('.Amount');
		$(this).autocomplete({
			source: '<?php echo site_url($this->_clspath.$this->_class.'/ajaxContainer') ?>/'+$("#BillType").val(),
			minLength: 2,
			focus: function(event, ui) {
				$(id).val(ui.item.id);
				$(this).val(ui.item.container_no);
				$(job_no).text(ui.item.job_no);
				$(qty).text(ui.item.qty);
				$(registration_no).text(ui.item.registration_no);
				$(trip_date).text(ui.item.trip_date);
				$(party).text(ui.item.party_name);
				$(product).text(ui.item.product_name);
				$(from).text(ui.item.from_location);
				$(to).text(ui.item.to_location);
				$(fuel).text(ui.item.fuel);
				$(slip_no).val(ui.item.slip_no);
				$(pump_advance_id).val(ui.item.pump_advance_id);
				$(amount).val(ui.item.transporter_rate);
				return false;
			},
			select: function(event, ui) {
				$(id).val(ui.item.id);
				$(this).val(ui.item.container_no);
				$(job_no).text(ui.item.job_no);
				$(qty).text(ui.item.qty);
				$(registration_no).text(ui.item.registration_no);
				$(trip_date).text(ui.item.trip_date);
				$(party).text(ui.item.party_name);
				$(product).text(ui.item.product_name);
				$(from).text(ui.item.from_location);
				$(to).text(ui.item.to_location);
				$(fuel).text(ui.item.fuel);
				$(slip_no).val(ui.item.slip_no);
				$(pump_advance_id).val(ui.item.pump_advance_id);
				$(advance).text(ui.item.advance);
				$(amount).val(ui.item.transporter_rate);
				return false;
			},
			response: function(event, ui) {
				if (ui.content.length === 0) {
					$(id).val(0);
					$(this).val('');
					$(qty).text('');
					$(job_no).text('');
					$(registration_no).text('');
					$(trip_date).text('');
					$(party).text('');
					$(product).text('');
					$(from).text('');
					$(to).text('');
					$(fuel).text('');
					$(slip_no).val(0);
					$(pump_advance_id).val(0);
					$(advance).text('');
					$(amount).val(0);
				}
			}
		})
		.data('ui-autocomplete')._renderItem = function(ul, item) {
			return $('<li></li>')
			.data('item.autocomplete', item)
			.append('<a><strong>' + item.slip_no + '</strong> <span class="tiny">' + item.container_no + ' <span class="orange">' + item.party_name + '</span> (' + item.from_location + ' - ' + item.to_location + ')</span> ' + item.registration_no + '</a>')
			.appendTo(ul);
		}
	});
});
</script>