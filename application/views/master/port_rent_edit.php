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
				<div class="col-md-6">
					<div class="form-group<?php echo (strlen(form_error('port_id')) > 0 ? ' has-error' : '') ?>">
						<label class="control-label">Port Name</label>
						<?php echo form_dropdown('port_id', getSelectOptions('indian_ports'), $row['port_id'], 'class="SelectizeKaabar Focus" id="PortID"'); ?>
					</div>

					<div class="form-group<?php echo (strlen(form_error('berth_no')) > 0 ? ' has-error' : '') ?>">
						<label class="control-label">Berth No</label>
						<?php echo form_dropdown('berth_no[]', $this->office->getBerthNo(), explode(',', $row['berth_no']), 'class="SelectizeKaabar" multiple data-placeholder="Choose Berth No..."') ?>				
					</div>

					<div class="form-group<?php echo (strlen(form_error('product_id')) > 0 ? ' has-error' : '') ?>">
						<label class="control-label">Product</label>
						<?php echo form_dropdown('product_id', getSelectOptions('products'), $row['product_id'], 'class="SelectizeKaabar" id="PortID"'); ?>				
					</div>

					<div class="row">
						<div class="col-md-3">
							<div class="form-group<?php echo (strlen(form_error('handling_charges')) > 0 ? ' has-error' : '') ?>">
								<label class="control-label">Handling Charges</label>
								<input type="text" class="form-control form-control-sm Numeric" name="handling_charges" value="<?php echo $row['handling_charges'] ?>" />				
							</div>
						</div>

						<div class="col-md-3">
							<div class="form-group<?php echo (strlen(form_error('wharfage')) > 0 ? ' has-error' : '') ?>">
								<label class="control-label">Wharfage Rate</label>
								<input type="text" class="form-control form-control-sm Numeric" name="wharfage" value="<?php echo $row['wharfage'] ?>" />						
							</div>
						</div>

						<div class="col-md-3">
							<div class="form-group<?php echo (strlen(form_error('service_tax')) > 0 ? ' has-error' : '') ?>">
								<label class="control-label">Service Tax</label>
								<input type="text" class="form-control form-control-sm Numeric" name="service_tax" value="<?php echo $row['service_tax'] ?>" />						
							</div>
						</div>

						<div class="col-md-3">
							<div class="form-group<?php echo (strlen(form_error('tds')) > 0 ? ' has-error' : '') ?>">
								<label class="control-label">TDS</label>
								<input type="text" class="form-control form-control-sm Numeric" name="tds" value="<?php echo $row['tds'] ?>" />						
							</div>
						</div>
					</div>
				</div>

				<div class="col-md-6">
					<div class="card card-default">
						<div class="card-header">
							<h3 class="card-title">Port Ground Rent</h3>
						</div>
						
						<div class="card-body">
						Make sure last entry contains <strong>0</strong> in <strong>To Day</strong> column.
						</div>
							
						<table class="table table-condensed table-striped DataEntry">
						<thead>
						<tr>
							<th>WEF Date</th>
							<th>From Day</th>
							<th>To Day</th>
							<th>Rate</th>
							<th width="24px" class="aligncenter"><a href="#" class="CheckAll"><i class="icon-trashcan"></i></a></th>
						</tr>
						</thead>

						<tbody>
						<?php
							foreach ($ground_rents as $r) {
								echo '<tr>
							<td><div class="input-group date DatePicker"><span class="input-group-addon"><i class="icon-calendar"></i></span><input type="text" class="form-control form-control-sm AutoDate" name="wef_date[' . $r['id'] . ']" value="' . $r['wef_date'] . '" /></div></td>
							<td><input type="text" class="form-control form-control-sm Numeric" name="from_day[' . $r['id'] . ']" value="' . $r['from_day'] . '" /></td>
							<td><input type="text" class="form-control form-control-sm Numeric" name="to_day[' . $r['id'] . ']" value="' . $r['to_day'] . '" /></td>
							<td><input type="text" class="form-control form-control-sm Numeric" name="rate[' . $r['id'] . ']" value="' . $r['rate'] . '" size="5" /></td>
							<td class="aligncenter">' . form_checkbox(array('name' => 'delete_id['.$r['id'].']', 'value' => $r['id'], 'checked' => false, 'class' => 'DeleteCheckbox')) . '</td>
						</tr>';
							}
						?>
						<tr class="TemplateRow">
							<td><div class="input-group date DatePicker"><span class="input-group-addon"><i class="icon-calendar"></i></span><input type="text" class="form-control form-control-sm AutoDate" name="new_wef_date[]" value="" /></div></td>
							<td><input type="text" class="form-control form-control-sm Numeric" name="new_from_day[]" value="" /></td>
							<td><input type="text" class="form-control form-control-sm Numeric" name="new_to_day[]" value="" /></td>
							<td><input type="text" class="form-control form-control-sm Numeric" name="new_rate[]" value="" size="5" /></td>
							<td><button type="button" class="btn btn-success btn-sm AddButton"><i class="fa fa-plus"></i></button></td>
						</tr>
						</tbody>
						</table>
					</div>
				</div>
			</div>
		</fieldset>
	</div>

	<div class="card-footer">
		<button type="submit" class="btn btn-success" id="Update">Update</button>
	</div>
</div>

</form>