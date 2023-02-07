<?php
echo form_open($this->uri->uri_string(), 'class="form-horizontal"');
echo form_hidden($vehicle_id);
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
						<label class="control-label">Vehicle No</label>
						<?php echo form_dropdown('vehicle_id', getSelectOptions('vehicles', 'id', 'registration_no'), $vehicle_id['vehicle_id'], 'class="form-control form-control-sm"') ?>
					</div>
				</div>

				<div class="col-md-2">
					<div class="form-group">
						<label class="control-label">Apply to these Groups</label>
						<?php echo form_dropdown('vehicle_groups[]', getSelectOptions('vehicles', 'group_name', 'group_name', 'GROUP BY group_name'), '', 'multiple class="SelectizeKaabar"') ?>
					</div>
				</div>

				<div class="col-md-8">
					<div class="form-group">
						<label class="control-label">Also Apply to these Vehicles</label>
						<?php echo form_dropdown('vehicle_ids[]', getSelectOptions('vehicles', 'id', 'registration_no', 'WHERE company_id = ' . $company_id . ' AND category = "TRAILORS"'), 0, 'multiple class="SelectizeKaabar"') ?>
					</div>
				</div>
			</div>
		</fieldset>

		<fieldset>
			<legend>Trip Master</legend>
			<table class="table table-condensed table-striped table-bordered DataEntry">
			<thead>
			<tr>
				<th>From Location</th>
				<th>To Location</th>
				<th>Product</th>
				<th>Type</th>
				<th width="80px">Fuel</th>
				<th width="80px">Allowance</th>
				<th width="24px" class="aligncenter"><a href="#" class="CheckAll"><i class="icon-trashcan"></i></a></th>
			</tr>
			</thead>

			<tbody>
			<?php
				foreach ($row['trips'] as $from_location => $trips) {
					echo '<tr>
				<td rowspan="' . count($trips) . '" class="aligntop">' . $from_location . '</td>';
					$first_row = 1;
					foreach ($trips as $r) {
						if ($first_row)
							$first_row = 0;
						else
							echo '<tr>';
						echo '
				<td><input type="hidden" name="from_location_id[' . $r['id'] . ']" value="' . $r['from_location_id'] . '" />
					<input type="hidden" name="to_location_id[' . $r['id'] . ']" value="' . $r['to_location_id'] . '" />
					' . $r['to_location'] . '</td>
				<td><input type="hidden" name="product_id[' . $r['id'] . ']" value="' . $r['product_id'] . '" />
					' . $r['product_name'] . '</td>
				<td>' . form_dropdown('type[' . $r['id'] . ']', getEnumSetOptions('trip_masters', 'type'), $r['type'], 'class="form-control form-control-sm"') . '</td>
				<td><input type="text" class="form-control form-control-sm Numeric" name="fuel[' . $r['id'] . ']" value="' . $r['fuel'] . '" /></td>
				<td><input type="text" class="form-control form-control-sm Numeric" name="allowance[' . $r['id'] . ']" value="' . $r['allowance'] . '" /></td>
				<td class="aligncenter">' . form_checkbox(array('name' => 'delete_id[' . $r['id'] . ']', 'value' => $r['id'], 'checked' => false, 'class' => 'DeleteCheckbox')) . '</td>
			</tr>';
					}
				}
			?>

			<tr class="TemplateRow">
				<td><input type="hidden" name="new_from_location_id[]" value="" />
					<input type="text" class="form-control form-control-sm FromLocation Validate Focus" value="" /></td>
				<td><input type="hidden" name="new_to_location_id[]" value="" />
					<input type="text" class="form-control form-control-sm ToLocation Validate Focus" value="" /></td>
				<td><input type="hidden" name="new_product_id[]" value="" />
					<input type="text" class="form-control form-control-sm ProductName Validate Focus" value="" /></td>
				<td><?php echo form_dropdown('new_type[]', getEnumSetOptions('trip_masters', 'type'), 'Bulk', 'class="form-control form-control-sm"') ?></td>
				<td><input type="text" class="form-control form-control-sm Numeric" name="new_fuel[]" value="" /></td>
				<td><input type="text" class="form-control form-control-sm Numeric" name="new_allowance[]" value="" /></td>
				<td><button type="submit" class="btn btn-success btn-sm AddButton"><i class="fa fa-plus"></i></button></td>
			</tr>
			</tbody>
			</table>
		</fieldset>
	</div>

	<div class="card-footer">
		<button type="submit" class="btn btn-success" id="Update">Update</button>
	</div>
</div>

</form>

<script>
$(document).ready(function() {
	$('#ajaxParty').kaabar_autocomplete({source: '<?php echo site_url('/master/party/ajax') ?>'});
	$('.FromLocation').kaabar_autocomplete({source: '<?php echo site_url($this->_clspath.$this->_class.'/json/locations/id/name') ?>'});
	$('.ToLocation').kaabar_autocomplete({source: '<?php echo site_url($this->_clspath.$this->_class.'/json/locations/id/name') ?>'});
	$('.ProductName').kaabar_autocomplete({source: '<?php echo site_url($this->_clspath.$this->_class.'/json/products/id/name') ?>'});
});
</script>
