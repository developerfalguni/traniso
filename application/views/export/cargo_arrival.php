<?php
echo form_open($this->uri->uri_string(), array('class' => 'form-horizontal'));
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
	
	<!-- <div class="card-body"></div> -->

	<table class="table table-condensed table-striped DataEntry">
	<thead>
		<tr>
			<th>Sr No</th>
			<th>Invoice No</th>
			<th width="120px">Date</th>
			<th width="120px">Vehicle No</th>
			<th width="80px">Units</th>
			<th width="80px">Unit</th>
			<th width="80px">Dispatch Weight</th>
			<th width="80px">Received Weight</th>
			<th>Supplier Name</th>
			<th>Marks</th>
			<th class="aligncenter"><a href="#" class="CheckAll"><i class="icon-trash"></i></th>
		</tr>
	</thead>

	<tbody>
		<?php
		$total = array(
			'units'           => 0,
			'dispatch_weight' => 0,
			'received_weight' => 0,
		);
		$i = 1;
		foreach ($rows as $r) {
			$total['units']           += $r['units'];
			$total['dispatch_weight'] += $r['dispatch_weight'];
			$total['received_weight'] += $r['received_weight'];

			echo '<tr>
				<td class="aligncenter">' . $i++ . '</td>
				<td>' . form_dropdown('job_invoice_id[' . $r['id'] . ']', $invoices, $r['job_invoice_id'], 'class="form-control form-control-sm"') . '</td>
				<td><input type="text" class="form-control form-control-sm AutoDate DatePicker" name="date[' . $r['id'] . ']" value="' . ($r['date'] != '00-00-0000' ? $r['date'] : '') . '" /></td>
				<td><input type="text" class="form-control form-control-sm" name="vehicle_no[' . $r['id'] . ']" value="' . $r['vehicle_no'] . '" /></td>
				<td><input type="text" class="form-control form-control-sm Numeric" name="units[' . $r['id'] . ']" value="' . $r['units'] . '" /></td>
				<td>' . form_dropdown('unit_id[' . $r['id'] . ']', getSelectOptions('units', 'id', 'code'), $r['unit_id'], 'class="form-control form-control-sm"') . '</td>
				<td><input type="text" class="form-control form-control-sm Numeric" name="dispatch_weight[' . $r['id'] . ']" value="' . $r['dispatch_weight'] . '" /></td>
				<td><input type="text" class="form-control form-control-sm Numeric" name="received_weight[' . $r['id'] . ']" value="' . $r['received_weight'] . '" /></td>
				<td><input type="text" class="form-control form-control-sm" name="supplier_name[' . $r['id'] . ']" value="' . $r['supplier_name'] . '" /></td>
				<td><input type="text" class="form-control form-control-sm" name="remarks[' . $r['id'] . ']" value="' . $r['remarks'] . '" /></td>
				<td class="aligncenter">' . form_checkbox(array('name' => 'delete_id[]', 'value' => $r['id'], 'checked' => false, 'class' => 'DeleteCheckbox')) . '</td>
			</tr>';
		}?>
		<tr class="TemplateRow">
			<td></td>
			<td><?php echo form_dropdown('new_job_invoice_id[]', $invoices, 0, 'class="form-control form-control-sm"') ?></td>
			<td><input type="text" class="form-control form-control-sm AutoDate DatePicker Focus Validate" name="new_date[]" value="" /></td>
			<td><input type="text" class="form-control form-control-sm Validate" name="new_vehicle_no[]" value="" /></td>
			<td><input type="text" class="form-control form-control-sm Numeric Validate" name="new_units[]" value="" /></td>
			<td><?php echo form_dropdown('new_unit_id[]', getSelectOptions('units', 'id', 'code'), 0, 'class="form-control form-control-sm"') ?></td>
			<td><input type="text" class="form-control form-control-sm Numeric Validate" name="new_dispatch_weight[]" value="" /></td>
			<td><input type="text" class="form-control form-control-sm Numeric Validate" name="new_received_weight[]" value="" /></td>
			<td><input type="text" class="form-control form-control-sm" name="new_supplier_name[]" value="" /></td>
			<td><input type="text" class="form-control form-control-sm" name="new_remarks[]" value="" /></td>
			<td class="aligncenter"><button type="submit" class="btn btn-success btn-sm AddButton"><i class="icon-white icon-plus"></i></button></td>
		</tr>
	</tbody>

	<tfoot>
		<tr>
			<th class="alignright" colspan="4">Total</th>
			<th class="alignright"><?php echo $total['units'] ?></th>
			<th></th>
			<th class="alignright"><?php echo $total['dispatch_weight'] ?></th>
			<th class="alignright"><?php echo $total['received_weight'] ?></th>
			<th colspan="6"></th>
		</tr>
	</tfoot>
	</table>

	<div class="card-footer">
		<button type="submit" class="btn btn-success" id="Update">Update</button>
	</div>
</div>

</form>