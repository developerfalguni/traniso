
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


<?php
echo form_open($this->uri->uri_string(), 'id="MainForm"');
echo form_hidden($id);
echo form_hidden(array('cargo_type' => $row['cargo_type']));
echo form_hidden(array('be_type' => $row['be_type']));
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
						<div class="col-md-2">
							<div class="form-group">
								<label class="control-label">Job No</label>
								<input type="text" class="form-control form-control-sm" value="<?php echo $row['id2_format']; ?>" readonly="true" />
							</div>
						</div>

						<div class="col-md-3">
							<div class="form-group<?php echo (strlen(form_error('date')) > 0 ? ' has-error' : '') ?>">
								<label class="control-label">Date</label>
								<div class="input-group date DatePicker">
									<span class="input-group-addon"><i class="icon-calendar"></i></span>
									<input type="text" class="form-control form-control-sm AutoDate" name="date" value="<?php echo $row['date']; ?>" id="Date" maxlength="10" />
								</div>
							</div>
						</div>

						<div class="col-md-1">
							<div class="form-group">
								<label class="control-label">Coastal</label><br />
								<?php echo form_checkbox(array('name' => 'is_coastal', 'value' => 'Yes', 'checked' => ($row['is_coastal'] == 'Yes' ? true : false))) ?> Yes
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">Indian Port</label>
								<div class="form-group<?php echo (strlen(form_error('indian_port_id')) > 0 ? ' has-error' : '') ?>">
									<?php echo form_dropdown('indian_port_id', getSelectOptions('indian_ports', 'id', 'name'), $row['indian_port_id'], 'class="form-control form-control-sm"') ?>
								</div>
							</div>
						</div>

						<!-- <div class="col-md-3">
							<div class="form-group">
								<label class="control-label">Cargo Type</label>
								<div class="form-group<?php echo (strlen(form_error('cargo_type')) > 0 ? ' has-error' : '') ?>">
									<?php echo form_dropdown('cargo_type', $this->import->getCargoTypes(), $row['cargo_type'], 'class="form-control form-control-sm" id="CargoType"') ?>
								</div>
							</div>
						</div> -->
					</div>

					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">Party Name</label>
								<div class="form-group<?php echo (strlen(form_error('party_id')) > 0 ? ' has-error' : '') ?>">
									<input type="hidden" name="party_id" value="<?php echo $row['party_id'] ?>" id="PartyID" />
									<input type="text" class="form-control form-control-sm" value="<?php echo $party_name ?>" size="30" id="ajaxParty" />
								</div>
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">Vessel Name</label>
								<div class="form-group <?php echo (strlen(form_error('vessel_id')) > 0 ? ' has-error' : '') ?>">
									<input type="hidden" name="vessel_id" value="<?php echo $row['vessel_id'] ?>" id="VesselID" />
									<input type="text" class="form-control form-control-sm" name="vessel_name" value="<?php echo $vessel_name ?>" size="30" id="ajaxVessel" />
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="col-md-4">
					<table class="table table-condensed table-striped DataEntry" id="HighSeas">
					<thead>
					<tr>
						<th width="24px">No</th>
						<th>HSS Party Name</th>
						<th width="24px" class="aligncenter"><a href="#" class="CheckAll"><i class="icon-trashcan"></i></a></th>
					</tr>
					</thead>

					<tbody>
						<?php 
							$i = 1;
							foreach ($hss_parties as $hss) {
								echo '<tr>
						<td>' . $i++ . '</td>
						<td class="tiny orange">' . $hss['name'] . '</td>
						<td class="aligncenter">' . form_checkbox(array('name' => 'hss_delete_id['.$hss['id'].']', 'value' => $hss['id'], 'checked' => false, 'class' => 'DeleteCheckbox')) . '</td>
					</tr>';
							}
						?>

					<tr class="TemplateRow">
						<td></td>
						<td><input type="hidden" name="new_hss_id[]" value="" />
							<input type="text" class="form-control form-control-sm BlankHSS Validate Focus" value="" />
						<td><button type="submit" class="btn btn-success btn-sm AddButton"><i class="fa fa-plus"></i></button></td>
					</tr>
					</tbody>
					</table>
				</div>
			</div>

			<div class="row">
				<div class="col-md-8">
					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								<label class="control-label">BL No</label>
								<div class="form-group<?php echo (strlen(form_error('bl_no')) > 0 ? ' error' : '')  ?>">
									<input type="text" class="form-control form-control-sm" name="bl_no" value="<?php echo $row['bl_no']; ?>" id="BLNo" onchange="javascript: checkDuplicateBL(" />
								</div>
							</div>
						</div>

						<div class="col-md-2">
							<div class="form-group<?php echo (strlen(form_error('bl_date')) > 0 ? ' has-error' : '') ?>">
								<label class="control-label">BL Date</label>
								<div class="input-group date DatePicker">
									<span class="input-group-addon"><i class="icon-calendar"></i></span>
									<input type="text" class="form-control form-control-sm AutoDate" name="bl_date" value="<?php echo $row['bl_date']; ?>" id="BLDate" onchange="javascript: checkDuplicateBL(" />
								</div>
							</div>
						</div>

						<div class="col-md-2">
							<div class="form-group">
								<label class="control-label">Handling Loss %</label>
								<input type="text" class="form-control form-control-sm" name="handling_loss" value="<?php echo $row['handling_loss'] ?>" />
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								<label class="control-label">BE No</label>
								<div class="form-group<?php echo (strlen(form_error('be_no')) > 0 ? ' error' : '')  ?>">
									<input type="text" class="form-control form-control-sm" name="be_no" value="<?php echo $row['be_no']; ?>" />
								</div>
							</div>
						</div>

						<div class="col-md-4">
							<div class="form-group<?php echo (strlen(form_error('be_date')) > 0 ? ' has-error' : '') ?>">
								<label class="control-label">BE Date</label>
								<div class="input-group date DatePicker">
									<span class="input-group-addon"><i class="icon-calendar"></i></span>
									<input type="text" class="form-control form-control-sm AutoDate" name="be_date" value="<?php echo $row['be_date']; ?>" />
								</div>
							</div>
						</div>

						<div class="col-md-4">
							<div class="form-group<?php echo (strlen(form_error('original_bl_received')) > 0 ? ' has-error' : '') ?>">
								<label class="control-label">Original B/L Rcvd</label>
								<div class="input-group date DatePicker">
									<span class="input-group-addon"><i class="icon-calendar"></i></span>
									<input type="text" class="form-control form-control-sm AutoDate" name="original_bl_received" value="<?php echo $import_details['original_bl_received']; ?>" />
								</div>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								<label class="control-label">Product</label>
								<div class="form-group<?php echo (strlen(form_error('product_id')) > 0 ? ' has-error' : '') ?>">
									<?php echo form_dropdown('product_id', array(0=>'')+getSelectOptions('products'), $row['product_id'], 'class="form-control form-control-sm"') ?>
								</div>
							</div>
						</div>

						<div class="col-md-2">
							<div class="form-group<?php echo (strlen(form_error('invoice_no')) > 0 ? ' has-error' : '') ?>">
								<label class="control-label">Invoice No</label>
								
									<input type="text" class="form-control form-control-sm" name="invoice_no" value="<?php echo $row['invoice_no']; ?>" />
								
							</div>
						</div>

						<div class="col-md-2">
							<div class="form-group<?php echo (strlen(form_error('invoice_date')) > 0 ? ' has-error' : '') ?>">
								<label class="control-label">Invoice Date</label>
								<div class="input-group date DatePicker">
									<span class="input-group-addon"><i class="icon-calendar"></i></span>
									<input type="text" class="form-control form-control-sm AutoDate" name="invoice_date" value="<?php echo $row['invoice_date']; ?>" />
								</div>
							</div>
						</div>

						<div class="col-md-2">
							<div class="form-group">
								<label class="control-label">Gross Weight</label>
								<div class="form-group<?php echo (strlen(form_error('gross_weight')) > 0 ? ' has-error' : '') ?>">
									<input type="text" class="form-control form-control-sm Numeric" name="gross_weight" value="<?php echo $row['gross_weight'] ?>" size="12" />
								</div>
							</div>
						</div>

						<div class="col-md-2">
							<div class="form-group">
								<label class="control-label">Unit</label>
								<?php echo form_dropdown('gross_weight_unit', getSelectOptions('units', 'code', 'code'), $row['gross_weight_unit'], 'class="form-control form-control-sm"'); ?>
							</div>
						</div>
					</div>


					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								<label class="control-label">Package Description</label>
								<div class="form-group<?php echo (strlen(form_error('details')) > 0 ? ' has-error' : '') ?>">
									<input type="text" class="form-control form-control-sm" name="details" value="<?php echo $row['details'] ?>" />
								</div>
							</div>
						</div>

						<div class="col-md-2">
							<div class="form-group">
								<label class="control-label">No. Packages</label>
								<div class="form-group<?php echo (strlen(form_error('packages')) > 0 ? ' has-error' : '') ?>">
									<input type="text" class="form-control form-control-sm Numeric" name="packages" value="<?php echo $row['packages'] ?>" size="12" />
								</div>
							</div>
						</div>

						<div class="col-md-2">
							<div class="form-group">
								<label class="control-label">Unit</label>
								<?php echo form_dropdown('package_type_id', getSelectOptions('package_types', 'id', 'name'), $row['package_type_id'], 'class="form-control form-control-sm"'); ?>
							</div>
						</div>

						<div class="col-md-2">
							<div class="form-group">
								<label class="control-label">Net Weight</label>
								<div class="form-group<?php echo (strlen(form_error('net_weight')) > 0 ? ' has-error' : '') ?>">
									<input type="text" class="form-control form-control-sm Numeric" name="net_weight" value="<?php echo $row['net_weight'] ?>" size="12" />
								</div>
							</div>
						</div>

						<div class="col-md-2">
							<div class="form-group">
								<label class="control-label">Unit</label>
								<?php echo form_dropdown('net_weight_unit', getSelectOptions('units', 'code', 'code'), $row['net_weight_unit'], 'class="form-control form-control-sm"'); ?>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">Remarks</label>
								<div class="form-group<?php echo (strlen(form_error('remarks')) > 0 ? ' has-error' : '') ?>">
									<input type="text" class="form-control form-control-sm" name="remarks" value="<?php echo $import_details['remarks'] ?>"  />
								</div>
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">Status</label>
								<?php echo form_dropdown('status', $this->import->getStatus(), $row['status'], 'class="form-control form-control-sm"'.(($job['lock'])?' disabled="disabled"':'') ); ?>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-2">
							<div class="form-group">
								<label class="control-label">Website</label><br />
								<?php echo form_checkbox(array('name' => 'website', 'value' => 'Yes', 'checked' => ($row['website'] == 'Yes' ? true : false))) ?> Yes
							</div>
						</div>

						<div class="col-md-10">
							<div class="form-group">
								<label class="control-label">Link to Party</label>
								<input type="hidden" name="web_party_id" value="<?php echo $row['web_party_id'] ?>" id="WebPartyID" />
								<input type="text" class="form-control form-control-sm" value="<?php echo $web_party_name ?>" id="ajaxWebParty" />
							</div>
						</div>
					</div>
				</div>

				<div class="col-md-4">
					<div class="form-group">
						<label class="control-label">Shipment Port</label>
						<div class="form-group<?php echo (strlen(form_error('shipment_port_id')) > 0 ? ' has-error' : '') ?>">
							<input type="hidden" name="shipment_port_id" value="<?php echo $row['shipment_port_id'] ?>" id="ShipmentPortID" />
							<input type="text" class="form-control form-control-sm" value="<?php echo $shipment_port ?>" id="ajaxSPort" />
						</div>
					</div>

					<div class="form-group">
						<label class="control-label">Country of Origin</label>
						<div class="form-group<?php echo (strlen(form_error('origin_country_id')) > 0 ? ' has-error' : '') ?>">
							<input type="hidden" name="origin_country_id" value="<?php echo $row['origin_country_id'] ?>" id="OriginCountryID" />
							<input type="text" class="form-control form-control-sm" value="<?php echo $origin_country ?>" id="ajaxOCountry" />
						</div>
					</div>

					<div class="form-group">
						<label class="control-label">CHA Name</label>
						<div class="form-group<?php echo (strlen(form_error('cha_id')) > 0 ? ' has-error' : '') ?>">
							<input type="hidden" name="cha_id" value="<?php echo $row['cha_id'] ?>" id="CHAID" />
							<input type="text" class="form-control form-control-sm" name="cha_name" value="<?php echo $cha_name ?>" id="ajaxCHA" />
						</div>
					</div>

					<div class="form-group">
						<label class="control-label">Shipper Name</label>
						<div class="form-group<?php echo (strlen(form_error('shipper_id')) > 0 ? ' has-error' : '') ?>">
							<input type="hidden" name="shipper_id" value="<?php echo $row['shipper_id'] ?>" id="ShipperID" />
							<input type="text" class="form-control form-control-sm" name="shipper_name" value="<?php echo $shipper_name ?>" id="ajaxShipper" />
						</div>
						<span class="tiny orange">VisualImpex: <?php echo $row['vi_shipper_name'] ?></span>
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

<script language="JavaScript">
var hss_checked = 1;

function CheckAllHSS() {
	if(hss_checked) {
		$("#HighSeas input.DeleteCheckbox").attr("checked", "checked");
		hss_checked = 0;
	} else {
		$("#HighSeas input.DeleteCheckbox").removeAttr("checked");
		hss_checked = 1;
	}
}

function checkDuplicateBL() {
	var bl_no = $("#BLNo").val();
	var bl_date = $("#BLDate").val();
	$.get("<?php echo base_url($this->_clspath.$this->_class.'/checkDuplicateBL') ?>/"+bl_no+"/"+bl_date);
}

function make_copy_hss(id) {
	var v0 = $("#HighSeas tr#Blank input:eq(0)").val();
	var v1 = $("#HighSeas tr#Blank input:eq(1)").val();

	if (!v0) return;
	
	if (id > 1) {
		$("#HighSeas tr#H_1").clone().insertBefore("#HighSeas tr#Blank").attr("id", "H_"+id);
	}

	$("#HighSeas tr#Blank input").each(function(index) {
		$(this).val("");
	});
	$("#HighSeas tr#Blank td a").attr("href", "javascript:make_copy_hss("+(id+1)+")");

	$("#HighSeas tr#H_"+id+" input:eq(0)").val(v0);
	$("#HighSeas tr#H_"+id+" input:eq(1)").val(v1);
	$("#HighSeas tr#H_"+id+" td a").attr("href", "javascript:remove_copy_hss("+id+")");
	$("#HighSeas tr#H_"+id).removeAttr("style");

	$("#HighSeas tr#Blank input:eq(1)").focus();
}

function remove_copy_hss(id) {
	if (id == 1) {
		$("#HighSeas tr#H_1 input").each(function(index) {
			$(this).val("");
		});
		$("#HighSeas tr#H_1").attr("style", "display: none");
	}
	else {
		$("#HighSeas tr#H_"+id).remove();
	}
}

$(document).ready(function() {
	// $('#ajaxParty').kaabar_autocomplete({source: '<?php echo site_url('/master/party/ajax') ?>'});
	//$('#ajaxWebParty').kaabar_autocomplete({source: '<?php echo site_url('/master/party/ajax') ?>'});
	$('.BlankHSS').kaabar_autocomplete({source: '<?php echo site_url('/master/party/ajax') ?>'});

	// $("#ajaxVessel").autocomplete({
	// 	source: '<?php echo site_url('/master/vessel/ajax') ?>',
	// 	minLength: 2,
	// 	focus: function(event, ui) {
	// 		$("#ajaxVessel").val(ui.item.prefix + ' ' + ui.item.name + ' ' + ui.item.voyage_no);
	// 		return false;
	// 	},
	// 	select: function(event, ui) {
	// 		$("#ajaxVessel").val(ui.item.prefix + ' ' + ui.item.name + ' ' + ui.item.voyage_no);
	// 		$("#VesselID").val(ui.item.id);
	
	// 		return false;
	// 	},
	// 	response: function(event, ui) {
 //            if (ui.content.length === 0) {
 //                $("#ajaxVessel").val('');
	// 			$("#VesselID").val(0);
 //            }
 //        }
	// })
	// .data('ui-autocomplete')._renderItem = function(ul, item) {
	// 	return $('<li></li>')
	// 		.data('item.autocomplete', item)
	// 		.append("<a>" + item.prefix + " " + item.name + " <span class='tiny orange'>" + item.voyage_no + "</span></a>")
	// 		.appendTo(ul);
	// };

	$('#ajaxVessel').kaabar_typeahead_complex({
		name: 'tt_vessel',
		displayKey: 'vessel',
		url: '<?php echo site_url('/master/vessel/ajax') ?>',
		suggestion: '<p>{{prefix}}{{name}} <span class="tiny"><span class="orange">{{voyage_no}}</span> </span></p></p>',
		fields: [{id: '#VesselID', field: 'id'}]
	});

	// $("#ajaxSPort").autocomplete({
	// 	source: '<?php echo site_url('master/port/ajax') ?>',
	// 	minLength: 3,
	// 	focus: function(event, ui) {
	// 		$("#ajaxSPort").val( ui.item.name);
	// 		$("#ajaxOCountry").val(ui.item.country);
	// 		return false;
	// 	},
	// 	select: function(event, ui) {
	// 		$("#ajaxSPort").val(ui.item.name);
	// 		$("#ShipmentPortID").val(ui.item.id);
	// 		$("#ajaxOCountry").val(ui.item.country);
	// 		$("#OriginCountryID").val(ui.item.country_id);
	// 		return false;
	// 	},
	// 	response: function(event, ui) {
 //            if (ui.content.length === 0) {
 //                $("#ajaxSPort").val('');
	// 			$("#ShipmentPortID").val(0);
	// 			$("#ajaxOCountry").val('');
	// 			$("#OriginCountryID").val(0);
 //            }
 //        }
	// })
	// .data('ui-autocomplete')._renderItem = function(ul, item) {
	// 	return $('<li></li>')
	// 		.data('item.autocomplete', item)
	// 		.append("<a>" + item.name + " <span class='tiny'><span class='orange'>" + item.unece_code + "</span> " + item.country + "</span></a>")
	// 		.appendTo(ul);
	// };

	$("#ajaxSPort").kaabar_typeahead_complex({
		hint: false,
		name: 'tt_shipment_port',  
		minLength: 3,                                     
		displayKey: 'name',
		url: '<?php echo site_url('master/port/ajax') ?>',
		suggestion: '<p>{{name}} <span class="tiny"><span class="orange">{{unece_code}}</span> {{country}}</span></p>',
		fields: [
			{id: '#ShipmentPortID', field: 'id'},
			{id: '#ajaxOCountry', field: 'country'},
			{id: '#OriginCountryID', field: 'country_id'}
		]
	});

	$("#ajaxContainerType").autocomplete({
		source: '<?php echo site_url('/master/container_type/ajax') ?>',
		minLength: 0
	});

	//$('#ajaxOCountry').kaabar_autocomplete({source: '<?php echo site_url('/master/country/ajax') ?>'});
	// $('#ajaxLine').kaabar_autocomplete({source: '<?php echo site_url('/master/agent/ajax/Line') ?>'});
	//$('#ajaxShipper').kaabar_autocomplete({source: '<?php echo site_url('/master/agent/ajax/Shipper') ?>'});
	//$('#ajaxCFS').kaabar_autocomplete({source: '<?php echo site_url('/master/agent/ajax/CFS') ?>'});
	//$('#ajaxCHA').kaabar_autocomplete({source: '<?php echo site_url('/master/agent/ajax/CHA') ?>'});

	// $(".BlankHSS").kaabar_typeahead({
	// 	name: 'tt_blank_hss',
	// 	displayKey: 'name',
	// 	url: '<?php echo site_url('/master/party/ajax') ?>',
	// 	suggestion: '<p>{{name}}</p>',
	// });

	// $('.BlankHSS').typeahead({
	// 		hint: false,
	// 		highlight: true,
	// 		minLength: 1
	// 	}, {
	// 		name: 't_blank_hss',
	// 		displayKey: 'name',
	// 		source: function(query, process) {
	// 			return $.ajax({ 
	// 				url: '<?php echo site_url('/master/party/ajax') ?>',
	// 				type: 'POST',
	// 				data: { term: query },
	// 				dataType: 'json',
	// 				success: function (result) {
	// 					return process(result);
	// 				}
	// 			});
	// 		},
	// 		templates: {
	// 			empty: ['<div class="tt-no-result">Unable to find any results that match the current query</div>'],
	// 			suggestion: Handlebars.compile('<p><span class="orange">{{name}}</span></p>')
	// 		}
	// 	}).on('typeahead:selected', function(obj, datum) {
	// 		$(this).parent('span').parent('td').find('.BlankHSS').val(datum.name);
	// });
	
	$('#ajaxWebParty').kaabar_typeahead_complex({
		name: 'tt_party',
		displayKey: 'name',
		url: '<?php echo site_url('/master/party/ajax') ?>',
		suggestion: '<p>{{name}}</p>',
		fields: [{id: '#WebPartyID', field: 'id'}]
	});

	$('#ajaxParty').kaabar_typeahead_complex({
		name: 'tt_party',
		displayKey: 'name',
		url: '<?php echo site_url('/master/party/ajax') ?>',
		suggestion: '<p>{{name}}</p>',
		fields: [{id: '#PartyID', field: 'id'}]
	});

	$('#ajaxOCountry').kaabar_typeahead_complex({
		name: 'tt_country',
		displayKey: 'name',
		url: '<?php echo site_url('/master/country/ajax') ?>',
		suggestion: '<p>{{name}}</p>',
		fields: [{id: '#OriginCountryID', field: 'id'}]
	});

	$('#ajaxLine').kaabar_typeahead_complex({
		name: 'tt_line',
		displayKey: 'name',
		url: '<?php echo site_url('/master/agent/ajax/Line') ?>',
		suggestion: '<p>{{name}}</p>',
		fields: [{id: '#LineID', field: 'id'}]
	});

	$('#ajaxShipper').kaabar_typeahead_complex({
		name: 'tt_shipper',
		displayKey: 'name',
		url: '<?php echo site_url('/master/agent/ajax/Shipper') ?>',
		suggestion: '<p>{{name}}</p>',
		fields: [{id: '#ShipperID', field: 'id'}]
	});

	$('#ajaxCFS').kaabar_typeahead_complex({
		name: 'tt_cfs',
		displayKey: 'name',
		url: '<?php echo site_url('/master/agent/ajax/CFS') ?>',
		suggestion: '<p>{{name}}</p>',
		fields: [{id: '#CFSID', field: 'id'}]
	});

	$('#ajaxCHA').kaabar_typeahead_complex({
		name: 'tt_cha',
		displayKey: 'name',
		url: '<?php echo site_url('/master/agent/ajax/CHA') ?>',
		suggestion: '<p>{{name}}</p>',
		fields: [{id: '#CHAID', field: 'id'}]
	});

});
</script>
