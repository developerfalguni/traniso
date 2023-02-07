<div id="modal-delete" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><i class="icon-times"></i></button>
				<h3>Confirm Deletion</h3>
			</div>
			<div class="modal-body"><p>Are you sure, you want to DELETE...?</p></div>
			<div class="modal-footer">
			<?php echo anchor($this->_clspath.$this->_class."/delete/".$id['id'], 'Delete', 'class="btn btn-danger"') ?>
			</div>
		</div>
	</div>
</div>

<div id="modal-voucher-exists" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><i class="icon-times"></i></button>
				<h3>Bill Found</h3>
			</div>
			<div class="modal-body">
				<h1 class="red">Bill already exists.</h1>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
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
						<label class="control-label">Date</label>
						<div class="input-group date DatePicker">
						<span class="input-group-addon"><i class="icon-calendar"></i></span>
						<input type="text" class="form-control form-control-sm AutoDate Focus" name="date" value="<?php echo $row['date'] ?>" id="Date" />
					</div>
							</div>
				</div>
				
				<div class="col-md-2">
					<div class="form-group">
						<label class="control-label">LR No</label>
						<input type="text" class="form-control form-control-sm" name="lr_no" value="<?php echo $row['lr_no'] ?>" />
					</div>
				</div>	

				<div class="col-md-2">
					<div class="form-group">
						<label class="control-label">Party Ref No</label>
						<input type="text" class="form-control form-control-sm" name="party_reference_no" value="<?php echo $row['party_reference_no'] ?>" id="PartyRefNo" />
					</div>
				</div>	
							
				<div class="col-md-4">
					<div class="form-group<?php echo (strlen(form_error('registration_no')) > 0 ? ' has-error' : '') ?>">
						<label class="control-label">Vehicle No</label>
						<input type="text" class="form-control form-control-sm" name="registration_no" value="<?php echo $row['registration_no'] ?>" id="VehicleNo" />
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label class="control-label">From Location</label>
						<input type="hidden" name="from_location_id" value="<?php echo $row['from_location_id'] ?>" id="FromLocationID" />
						<input type="text" class="form-control form-control-sm" value="<?php echo $from_location ?>" id="ajaxFromLocation" />
					</div>
				</div>

				<div class="col-md-6">
					<div class="form-group">
						<label class="control-label">To Location</label>
						<input type="hidden" name="to_location_id" value="<?php echo $row['to_location_id'] ?>" id="ToLocationID" />
						<input type="text" class="form-control form-control-sm" value="<?php echo $to_location ?>" id="ajaxToLocation" />
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-md-2">
					<div class="form-group<?php echo (strlen(form_error('container_no')) > 0 ? ' has-error' : '') ?>">
						<label class="control-label">Container No</label>
							<input type="hidden" name="job_id" value="<?php echo $row['job_id'] ?>" id="JobID" />
							<input type="hidden" name="container_id" value="<?php echo $row['container_id'] ?>" id="ContainerID" />
							<input type="text" class="form-control form-control-sm" name="container_no" value="<?php echo $row['container_no'] ?>" id="ContainerNo" />
					</div>
				</div>

				<div class="col-md-2">
					<div class="form-group<?php echo (strlen(form_error('container_no2')) > 0 ? ' has-error' : '') ?>">
						<label class="control-label">Container No 2</label>
						<input type="hidden" name="job_id2" value="<?php echo $row['job_id2'] ?>" id="JobID2" />
						<input type="hidden" name="container_id2" value="<?php echo $row['container_id2'] ?>" id="ContainerID2" />
						<input type="text" class="form-control form-control-sm" name="container_no2" value="<?php echo $row['container_no2'] ?>" id="ContainerNo2" />
					</div>
				</div>

				<div class="col-md-1">
					<div class="form-group">
						<label class="control-label">Container Size</label>
						<?php echo form_dropdown('container_size', getEnumSetOptions('trips', 'container_size'), $row['container_size'], 'class="form-control form-control-sm" id="ContainerSize"'); ?>
					</div>
				</div>

				<div class="col-md-1">
					<div class="form-group">
						<label class="control-label">Weight</label>
						<input type="text" class="form-control form-control-sm Numeric" name="weight" value="<?php echo $row['weight'] ?>" />
					</div>
				</div>
				
				<div class="col-md-4">
					<div class="form-group">
						<label class="control-label">Party Ledger Name</label>
						<input type="hidden" name="party_ledger_id" value="<?php echo $row['party_ledger_id'] ?>" id="PartyLedgerID" />
						<input type="text" class="form-control form-control-sm" value="<?php echo $party_name ?>" id="PartyName" />
					</div>
				</div>
				
				<div class="col-md-2">
					<div class="form-group">
						<label class="control-label">Party Rate</label>
						<input type="text" class="form-control form-control-sm Numeric" name="party_rate" value="<?php echo $row['party_rate'] ?>" id="PartyRate" readonly="true" />
					</div>
				</div>
			</div>

			<div class="row">				
				<div class="col-md-5">
					<div class="form-group">
						<label class="control-label">Product Name</label>
						<input type="text" class="form-control form-control-sm" name="product_name" value="<?php echo $row['product_name'] ?>" id="ProductName" />
					</div>
				</div>

				<div class="col-md-1">
					<div class="form-group">
						<label class="control-label">CBM</label>
						<input type="text" class="form-control form-control-sm Numeric" name="cbm" value="<?php echo $row['cbm'] ?>" />
					</div>
				</div>

				<div class="col-md-4">
					<div class="form-group">
						<label class="control-label">Transporter Ledger Name</label>
						<input type="hidden" name="transporter_ledger_id" value="<?php echo $row['transporter_ledger_id'] ?>" id="TransporterLedgerID" />
						<input type="text" class="form-control form-control-sm" value="<?php echo $transporter_name ?>" id="TransporterName" />
					</div>
				</div>
				
				<div class="col-md-2">
					<div class="form-group<?php echo (strlen(form_error('transporter_rate')) > 0 ? ' has-error' : '') ?>">
						<label class="control-label">Transporter Rate</label>
						<input type="text" class="form-control form-control-sm Numeric" name="transporter_rate" value="<?php echo $row['transporter_rate'] ?>" id="TransporterRate" readonly="true" />
					</div>
				</div>	
			</div>

			<div class="form-group">
				<label class="control-label">Remarks</label>
				<textarea name="remarks" class="form-control form-control-sm"><?php echo $row['remarks'] ?></textarea>
			</div>


			<div class="row">
				<div class="col-md-6">
					<div class="card card-default">
						<div class="card-header">
							<h3 class="card-title">Trip Advance</h3>
						</div>
						
						<!-- <div class="card-body"></div> -->
					
						<table class="table table-condensed table-striped DataEntry">
						<thead>
							<tr>
								<th width="150px">Date</th>
								<th>Advance By</th>
								<th>RTO Challan</th>
								<th width="80px">Amount</th>
								<th width="24px" class="aligncenter">R</th>
								<th width="24px" class="aligncenter">P</th>
								<th width="24px" class="aligncenter"><a href="#" class="CheckAll"><i class="icon-trashcan"></i></a></th>
							</tr>
						</thead>

						<tbody>
							<?php
								$i = 1;
								foreach ($trip_advances as $r) {
									echo '<tr>
								<td><div class="input-group date DatePicker"><span class="input-group-addon"><i class="icon-calendar"></i></span><input type="text" class="form-control form-control-sm AutoDate" name="ta_date[' . $r['id'] . ']" value="' . $r['date'] . '" /></div></td>
								<td>' . form_dropdown('ta_advance_by[' . $r['id'] . ']', getEnumSetOptions('trip_advances', 'advance_by'), $r['advance_by'], 'class="form-control form-control-sm"') . '</td>
								<td><input type="text" class="form-control form-control-sm" name="ta_rto_challan[' . $r['id'] . ']" value="' . $r['rto_challan'] . '" /></td>
								<td><input type="text" class="form-control form-control-sm Numeric Amount" name="ta_amount[' . $r['id'] . ']" value="' . $r['amount'] . '" /></td>
								<td class="aligncenter">' . ($r['receipt_voucher_id'] ? anchor('accounting/'.underscore($r['receipt_voucher_url']), '<i class="icon-check"></i>', 'target="_blank"') : '') . '</td>
								<td class="aligncenter">' . ($r['voucher_id'] ? anchor('accounting/'.underscore($r['voucher_url']), '<i class="icon-check"></i>', 'target="_blank"') : '') . '</td>
								<td class="aligncenter">' . form_checkbox(array('name' => 'ta_delete_id['.$r['id'].']', 'value' => $r['id'], 'checked' => false, 'class' => 'DeleteCheckbox')) . '</td>
							</tr>';
								}
							?>

							<tr class="TemplateRow">
								<td><div class="input-group date DatePicker"><span class="input-group-addon"><i class="icon-calendar"></i></span><input type="text" class="form-control form-control-sm AutoDate" name="ta_new_date[]" value="" /></div></td>
								<td><?php echo form_dropdown('ta_new_advance_by[]', getEnumSetOptions('trip_advances', 'advance_by'), 'Self', 'class="form-control form-control-sm"') ?></td>
								<td><input type="text" class="form-control form-control-sm" name="ta_new_rto_challan[]" value="" /></td>
								<td><input type="text" class="form-control form-control-sm Numeric Validate Amount" name="ta_new_amount[]" value="" /></td>
								<td></td>
								<td></td>
								<td class="aligncenter"><button type="submit" class="btn btn-success btn-sm AddButton"><i class="icon-white icon-plus"></i></button></td>
							</tr>
						</tbody>
						</table>
					</div>

					<div class="card card-default">
						<div class="card-header">
							<h3 class="card-title">Receipt Voucher</h3>
						</div>
						
						<table class="table table-condensed table-striped">
							<thead>
								<tr>
									<th>Receipt</th>
									<th>Date</th>
									<th>Amount</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($trip_receipt as $r): ?>
									<tr>
										<td><a href="<?php echo base_url('accounting/receipt/edit/'.$r['voucher_book_id'].'/'.$r['voucher_id']) ?>" target="_blank"><?php echo $r['id2_format'] ?></a></td>
										<td class="aligncenter"><?php echo $r['date'] ?></td>
										<td class="alignright"><?php echo $r['amount'] ?></td>
									</tr>
								<?php endforeach ?>
							</tbody>
						</table>
					</div>
				</div>

				<div class="col-md-6">
					<div class="form-group">
						<div class="card card-default">
							<div class="card-header">
								<h3 class="card-title">Pump Details</h3>
							</div>
							
							<!-- <div class="card-body"></div> -->
						
							<table class="table table-condensed table-striped DataEntry" >
							<thead>
								<tr>
									<th width="150px">Date</th>	
									<th width="80px">Slip No</th>
									<th>Pump Name</th>
									<th width="80px">Amount</th>
									<th width="24px" class="aligncenter">J</th>
									<th width="24px" class="aligncenter"><a href="#" class="CheckAll"><i class="icon-trashcan"></i></a></th>
								</tr>
							</thead>

							<tbody>
								<?php
									$i = 1;
									foreach ($pump_advances as $r) {
										echo '<tr>
									<td><div class="input-group date DatePicker"><span class="input-group-addon"><i class="icon-calendar"></i></span><input type="text" class="form-control form-control-sm AutoDate" name="pa_date[' . $r['id'] . ']" value="' . $r['date'] . '" /></div></td>
									<td><input type="text" class="form-control form-control-sm" name="pa_slip_no[' . $r['id'] . ']" value="' . $r['slip_no'] . '" /></td>
									<td><input type="hidden" class="AgentID" name="pa_agent_id[' . $r['id'] . ']" value="' . $r['agent_id'] . '" />
										<input type="text" class="form-control form-control-sm AgentName Validate" value="' . $r['agent_name'] . '" /></td>
									<td><input type="text" class="form-control form-control-sm Numeric Validate Fuel FuelAmount" name="pa_amount[' . $r['id'] . ']" value="' . $r['amount'] . '" /></td>
									<td class="aligncenter">' . ($r['voucher_id'] ? anchor('accounting/'.underscore($r['voucher_url']), '<i class="icon-check"></i>', 'target="_blank"') : '') . '</td>
									<td class="aligncenter">' . form_checkbox(array('name' => 'pa_delete_id['.$r['id'].']', 'value' => $r['id'], 'checked' => false, 'class' => 'DeleteCheckbox')) . '</td>
								</tr>';
									}
								?>

								<tr class="TemplateRow">
									<td><div class="input-group date DatePicker"><span class="input-group-addon"><i class="icon-calendar"></i></span><input type="text" class="form-control form-control-sm AutoDate" name="pa_new_date[]" value="" /></div></td>
									<td><input type="text" class="form-control form-control-sm" name="pa_new_slip_no[]" value="" /></td>
									<td><input type="hidden" name="pa_new_agent_id[]" value="" />
										<input type="text" class="form-control form-control-sm AgentName Validate" name="pa_new_agent_name[]" value="" /></td>
									<td><input type="text" class="form-control form-control-sm Numeric Validate Fuel FuelAmount" name="pa_new_amount[]" value="" /></td>
									<td></td>
									<td class="aligncenter"><button type="submit" class="btn btn-success btn-sm AddButton"><i class="icon-white icon-plus"></i></button></td>
								</tr>
							</tbody>
							</table>
						</div>
					</div>

					<div class="card card-default">
						<div class="card-header">
							<h3 class="card-title">Payment Voucher</h3>
						</div>
						
						<table class="table table-condensed table-striped">
							<thead>
								<tr>
									<th>Payment</th>
									<th>Date</th>
									<th>Amount</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($trip_payment as $r): ?>
									<tr>
										<td><a href="<?php echo base_url('accounting/receipt/edit/'.$r['voucher_book_id'].'/'.$r['voucher_id']) ?>" target="_blank"><?php echo $r['id2_format'] ?></a></td>
										<td class="aligncenter"><?php echo $r['date'] ?></td>
										<td class="alignright"><?php echo $r['amount'] ?></td>
									</tr>
								<?php endforeach ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</fieldset>
	</div>

	<div class="card-footer">
		<button type="submit" class="btn btn-success" id="Update">Update</button>
		<a href="#modal-delete" data-toggle="modal" class="btn btn-danger pull-right">Delete</a>
	</div>
</div>	

</form>

<?php if(isset($list)) $this->load->view('list', $list); ?>

<script>
$(document).ready(function() {

	$("#ProductName").kaabar_typeahead({
		name: 'tt_name',
		displayKey: 'name',
		url: '<?php echo site_url($this->_clspath.$this->_class.'/getJSON/products/name') ?>',
		suggestion: '<p>{{name}}</p>'
	});

	$("#ajaxFromLocation").kaabar_typeahead_complex({
		name: 'tt_name',
		displayKey: 'name',
		url: '<?php echo site_url($this->_clspath.$this->_class."/json/locations/id/name") ?>',
		suggestion: '<p>{{name}}</p>',
		fields: [{id: '#FromLocationID', field: 'id'}]
	});

	$("#ajaxToLocation").kaabar_typeahead_complex({
		name: 'tt_name',
		displayKey: 'name',
		url: '<?php echo site_url($this->_clspath.$this->_class."/json/locations/id/name") ?>',
		suggestion: '<p>{{name}}</p>',
		fields: [{id: '#ToLocationID', field: 'id'}]
	});

	$('#PartyName').typeahead({
		hint: false,
		highlight: true,
		minLength: 1
	}, {
		name: 'tt_name',
		displayKey: 'name',
		source: function(query, process) {
			return $.ajax({ 
				url: '<?php echo site_url($this->_clspath.$this->_class.'/ajaxParty') ?>',
				type: "POST",
				dataType: "json",
				data: {
					term: query,
					from_location_id: $('#FromLocationID').val(),
					to_location_id: $('#ToLocationID').val(),
					container_size: $('#ContainerSize').val(),
				},
				dataType: 'json',
				success: function (result) {
					return process(result);
				}
			});
		},
		templates: {
			empty: ['<div class="tt-no-result">Unable to find any results that match the current query</div>'],
			suggestion: Handlebars.compile('<p><strong>{{code}}</strong> {{name}} - {{rate}}</p>')
		}
	}).on('typeahead:selected', function(obj, datum) {
		$('#PartyLedgerID').val(datum.id);
		$('#PartyRate').val(datum.rate);
	});

	$('#TransporterName').typeahead({
		hint: false,
		highlight: true,
		minLength: 1
	}, {
		name: 'tt_name',
		displayKey: 'name',
		source: function(query, process) {
			return $.ajax({ 
				url: '<?php echo site_url($this->_clspath.$this->_class.'/ajaxTransporter') ?>',
				type: "POST",
				dataType: "json",
				data: {
					term: query,
					from_location_id: $('#FromLocationID').val(),
					to_location_id: $('#ToLocationID').val(),
					container_size: $('#ContainerSize').val(),
				},
				success: function (result) {
					return process(result);
				}
			});
		},
		templates: {
			empty: ['<div class="tt-no-result">Unable to find any results that match the current query</div>'],
			suggestion: Handlebars.compile('<p><strong>{{code}}</strong> {{name}} - {{rate}}</p>')
		}
	}).on('typeahead:selected', function(obj, datum) {
		$('#TransporterLedgerID').val(datum.id);
		$('#TransporterRate').val(datum.rate);
	});

	$("#VehicleNo").kaabar_typeahead({
		name: 'tt_vehicle',
		displayKey: 'registration_no',
		url: '<?php echo site_url($this->_clspath.$this->_class.'/ajaxVehicle') ?>',
		suggestion: '<p>{{registration_no}} <span class="orange">{{type}}</span></p>',
	});

	$("#ContainerNo").kaabar_typeahead_complex({
		hint: false,
		minLength: 5,
		name: 'tt_container_no',                                       
		displayKey: 'container_no',
		url: '<?php echo site_url($this->_clspath.$this->_class.'/ajaxContainer/'.$cargo_type.'/'.$row['id']) ?>',
		suggestion: '<p><span class="tiny"><span class="bold">{{job_no}}</span> <span class="orange">{{container_no}}</span> {{party_name}} ({{bl_no}})</span></p>',
		fields: [
			{id: '#ContainerID', field: 'id'},
			{id: '#JobID', field: 'job_id'},
			{id: '#ContainerSize', field: 'container_size'},
			{id: '#PartyLedgerID', field: 'party_ledger_id'},
			{id: '#PartyName', field: 'party_ledger_name'},
			{id: '#ProductName', field: 'product_name'},
			{id: '#PartyRefNo', field: 'job_no'},
		]
	});

	$("#ContainerNo2").kaabar_typeahead_complex({
		hint: false,
		minLength: 5,
		name: 'tt_container_no',                                       
		displayKey: 'container_no',
		url: '<?php echo site_url($this->_clspath.$this->_class.'/ajaxContainer/'.$cargo_type.'/'.$row['id']) ?>',
		suggestion: '<p><span class="tiny"><span class="bold">{{job_no}}</span> <span class="orange">{{container_no}}</span> {{party_name}} ({{bl_no}})</span></p>',
		fields: [
			{id: '#ContainerID2', field: 'id'},
			{id: '#JobID2', field: 'job_id'},
			{id: '#ContainerSize2', field: 'container_size'},
		]
	});

	$('.AgentName').typeahead({
		hint: false,
		highlight: true,
		minLength: 1
	}, {
		name: 'tt_agent',
		displayKey: 'name',
		source: function(query, process) {
			var type = $(this.$el).parent().parent().parent().parent().find('.Type').val();
			return $.ajax({ 
				url: '<?php echo site_url('/master/agent/ajax/Pump') ?>',
				type: 'POST',
				data: { term: query },
				dataType: 'json',
				success: function (result) {
					return process(result);
				}
			});
		},
		templates: {
			empty: function(context) {
				$('.AgentID').val(0);
				$('.AgentName').val('');
				return ['<div class="tt-no-result">Unable to find any results that match the current query</div>'];
			},
			suggestion: Handlebars.compile('<p>{{name}}</p>')
		}
	}).on('typeahead:selected', function(obj, datum) {
		$(this).parent().parent().find('.AgentID').val(datum.id);
	});
});
</script>