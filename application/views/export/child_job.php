<div id="modal-delete" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><i class="icon-times"></i></button>
				<h3>Confirm Deletion</h3>
			</div>
			<div class="modal-body"><p>Are you sure, you want to DELETE...?</p></div>
			<div class="modal-footer">
				<?php echo anchor($this->_clspath.$this->_class."/delete/".$job_id['id'].'/'.$id['id'], 'Delete', 'class="btn btn-danger"') ?>
			</div>
		</div>
	</div>
</div>

<?php
echo form_open($this->uri->uri_string());
echo form_hidden($id);
echo form_hidden($job_id);
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
				<div class="col-md-8">
					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								<label class="control-label">VisualImpex Job</label>
								<input type="text" class="form-control form-control-sm" name="vi_job_no" value="<?php echo $row['vi_job_no'] ?>" />
							</div>
						</div>

						<div class="col-md-4">
							<div class="form-group">
								<label class="control-label">Shipment Type</label>
								<?php echo form_dropdown('shipment_type', getEnumSetOptions('child_jobs', 'shipment_type'), $row['shipment_type'], 'class="form-control form-control-sm" id="ShipmentType"'); ?>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-8">
							<div class="form-group">
								<label class="control-label">SB No</label>
								<input type="text" class="form-control form-control-sm" name="sb_no" value="<?php echo $row['sb_no']; ?>" />
							</div>
						</div>

						<div class="col-md-4">
							<div class="form-group">
								<label class="control-label">SB Date</label>
								<div class="input-group date DatePicker">
									<span class="input-group-addon"><i class="icon-calendar"></i></span>
									<input type="text" class="form-control form-control-sm AutoDate" name="sb_date" value="<?php echo ($row['sb_date'] != '00-00-0000' ? $row['sb_date'] : '') ?>" />
								</div>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-8">
							<div class="form-group">
								<label class="control-label">BL No</label>
								<input type="text" class="form-control form-control-sm" name="bl_no" value="<?php echo $row['bl_no']; ?>" />
							</div>
						</div>

						<div class="col-md-4">
							<div class="form-group">
								<label class="control-label">BL Date</label>
								<div class="input-group date DatePicker">
									<span class="input-group-addon"><i class="icon-calendar"></i></span>
									<input type="text" class="form-control form-control-sm AutoDate" name="bl_date" value="<?php echo ($row['bl_date'] != '00-00-0000' ? $row['bl_date'] : '') ?>" />
								</div>
							</div>
						</div>
					</div>

					<div class="form-group<?php echo (strlen(form_error('vessel_id')) > 0 ? ' has-error' : '') ?>">
						<label class="control-label">GateIn Vessel Name</label>
						<input type="hidden" name="vessel_id" value="<?php echo $row['vessel_id'] ?>" id="VesselID" />
						<input type="text" class="form-control form-control-sm" name="vessel_name" value="<?php echo $vessel_name ?>" id="ajaxVessel" />
					</div>

					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								<label class="control-label">Stuffing Type</label>
								<?php echo form_dropdown('stuffing_type', $this->export->getStuffingTypes(), $row['stuffing_type'], 'class="form-control form-control-sm" id="StuffingType"'); ?>
							</div>
						</div>

						<div class="col-md-8">
							<div class="form-group">
								<label class="control-label">Stuffing Place</label>
								<?php echo form_dropdown('shipper_site_id', array('0'=>'--- Select Factory ---')+getSelectOptions('party_sites', 'id', 'name', 'WHERE party_id = '.$shipper_id), $row['shipper_site_id'], 'class="form-control form-control-sm ShipperSiteID"'); ?>

								<?php echo form_dropdown('godown_id', array('0'=>'--- Select Godown ---')+getSelectOptions('godowns', 'id', 'name'), $row['godown_id'], 'class="form-control form-control-sm GodownID"'); ?>

								<?php //echo form_dropdown('cfs_id', array('0'=>'--- Select CFS ---')+getSelectOptions('cfs', 'id', 'name', 'WHERE type = "CFS"'), $row['cfs_id'], 'class="form-control form-control-sm CFSID"'); ?>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label class="control-label">Shipping Line</label>
								<div class="form-group<?php echo (strlen(form_error('shipping_line_id')) > 0 ? ' has-error' : '') ?>">
									<input type="hidden" name="shipping_line_id" value="<?php echo $shipping_line['id'] ?>" id="ShippingLineID" />
									<input type="text" class="form-control form-control-sm" value="<?php echo $shipping_line['name'] ?>" id="ajaxShippingLine" />
								</div>
							</div>
						</div>
					</div>

					<div class="form-group">
						<label class="control-label">Marks &amp; No</label>
						<div class="form-group<?php echo (strlen(form_error('marks')) > 0 ? ' has-error' : '') ?>">
							<input type="text" class="form-control form-control-sm" name="marks" value="<?php echo $row['marks'] ?>" />
						</div>
					</div>
				</div>

				<div class="col-md-4">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">No. Packages</label>
								<div class="form-group<?php echo (strlen(form_error('packages')) > 0 ? ' has-error' : '') ?>">
									<input type="text" class="form-control form-control-sm Numeric" name="packages" value="<?php echo $row['packages'] ?>" size="12" />
								</div>
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">Unit</label>
								
									<?php echo form_dropdown('package_type_id', getSelectOptions('units', 'id', 'code'), $row['package_type_id'], 'class="form-control form-control-sm"'); ?>
								
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">Net Weight</label>
								<div class="form-group<?php echo (strlen(form_error('net_weight')) > 0 ? ' has-error' : '') ?>">
									<input type="text" class="form-control form-control-sm Numeric" name="net_weight" value="<?php echo number_format($row['net_weight'], 3, '.', '') ?>" size="12" />
								</div>
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">Net Weight Unit</label>
								<div class="form-group<?php echo (strlen(form_error('net_weight_unit')) > 0 ? ' has-error' : '') ?>">
									<input type="text" class="form-control form-control-sm" name="net_weight_unit" value="<?php echo $row['net_weight_unit'] ?>" size="12" />
								</div>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">Gross Weight</label>
								<div class="form-group<?php echo (strlen(form_error('gross_weight')) > 0 ? ' has-error' : '') ?>">
									<input type="text" class="form-control form-control-sm Numeric" name="gross_weight" value="<?php echo number_format($row['gross_weight'], 3, '.', '') ?>" size="12" />
								</div>
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">Gross Weight Unit</label>
								<div class="form-group<?php echo (strlen(form_error('gross_weight_unit')) > 0 ? ' has-error' : '') ?>">
									<input type="text" class="form-control form-control-sm" name="gross_weight_unit" value="<?php echo $row['gross_weight_unit'] ?>" size="12" />
								</div>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">FOB Value</label>
								<input type="text" class="form-control form-control-sm Numeric" name="fob_value" value="<?php echo number_format($row['fob_value'], 3, '.', '') ?>" size="12" />
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">FOB Currency</label>
								<input type="text" class="form-control form-control-sm" name="fob_currency" value="<?php echo $row['fob_currency'] ?>" size="12" />
							</div>
						</div>
					</div>
					<hr />

					<legend>
						<div class="row">
							<div class="col-md-8">Invoices</div>
							<div class="col-md-4 alignright"><?php echo anchor($this->_clspath.'invoice/edit/'.$job_id['id'].'/'.$id['id'].'/0', '<i class="fa fa-plus"></i>', 'class="btn btn-success btn-sm"') ?></div>
						</div>
					</legend>
					<table class="table table-striped">
					<thead>
						<tr>
							<th>Invoice No</th>
							<th>Date</th>
							<th>INR Value</th>
						</tr>
					</thead>

					<tbody>
					<?php
					foreach ($invoices as $r) {
						echo '<tr>
							<td>' . anchor($this->_clspath.'invoice/edit/'.$job_id['id'].'/'.$id['id'].'/'.$r['id'], $r['invoice_no']) . '</td>
							<td>' . $r['invoice_date'] . '</td>
							<td class="alignright">' . $r['invoice_value'] . '</td>
						</tr>';
					}
					?>
					</tbody>
					</table>
				</div>
			</div>

			<div class="form-group">
				<label class="control-label">Remarks</label>
				<textarea class="form-control form-control-sm" name="remarks" rows="2" cols="80"><?php echo $row['remarks'] ?></textarea>
			</div>
		</fieldset>
	</div>

	<div class="card-footer">
		<button type="submit" class="btn btn-success" id="Update">Update</button>
		<a href="#modal-delete" data-toggle="modal" class="btn btn-danger pull-right">Delete</a>
	</div>
</div>

</form>

<script>
function changeStuffing() {
	var st = $('#StuffingType').val();
	if (st == 'Factory') {
		$('.ShipperSiteID').removeClass('hide');
		$('.GodownID').addClass('hide').val(0);
		$('.CFSID').addClass('hide').val(0);
	}
	else if (st == 'Godown') {
		$('.GodownID').removeClass('hide');
		$('.ShipperSiteID').addClass('hide').val(0);
		$('.CFSID').addClass('hide').val(0);
	}
	else {
		$('.CFSID').removeClass('hide');
		$('.ShipperSiteID').addClass('hide').val(0);
		$('.GodownID').addClass('hide').val(0);
	}
}

$(document).ready(function() {
	$("#StuffingType").on("change", function() {
		changeStuffing();
	});

	changeStuffing();

	$("#ajaxVessel").autocomplete({
		source: '<?php echo site_url('/master/vessel/ajax') ?>',
		minLength: 1,
		focus: function(event, ui) {
			$(this).val( ui.item.name);
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
			.append('<a>' + item.name + ' <span class="orange">' + item.voyage_no + '</span> ' + item.port_name + '</a>')
			.appendTo(ul);
	};

	$('#ajaxShippingLine').kaabar_autocomplete({source: '<?php echo site_url('master/shipping_line/ajax') ?>'});
});
</script>