<?php
echo form_open($this->uri->uri_string(), 'id="MainForm"');
echo form_hidden($id);
echo form_hidden($job_id);
?>

<fieldset>
	<div class="row">
		<div class="col-md-2">
			<div class="form-group<?php echo (strlen(form_error('invoice_date')) > 0 ? ' has-error' : '') ?>">
				<label class="control-label">Invoice Date</label>
				<div class="input-group input-group-sm">
					<input type="text" class="form-control form-control-sm DatePicker" name="invoice_date" value="<?php echo $row['invoice_date']; ?>">
					<div class="input-group-append">
						<div class="input-group-text"><i class="icon-calendar"></i></div>
					</div>
				</div>
			</div>
		</div>

		<div class="col-md-4">
			<div class="form-group<?php echo (strlen(form_error('invoice_no')) > 0 ? ' has-error' : '') ?>">
				<label class="control-label">Invoice No</label>				
				<input type="text" class="form-control form-control-sm" name="invoice_no" value="<?php echo $row['invoice_no'] ?>" />				
			</div>
		</div>

		<div class="col-md-2">
			<div class="form-group">
				<label class="control-label">Terms</label>
				<input type="text" class="form-control form-control-sm" name="toi" value="<?php echo $row['toi'] ?>" id="TOI" />
			</div>
		</div>

		<div class="col-md-2">
			<div class="form-group">
				<label class="control-label">Currency</label>
				<?php echo form_dropdown('currency_id', getSelectOptions('currencies', 'id', 'code'), $row['currency_id'], 'class="form-control form-control-sm"'); ?>
			</div>
		</div>

		<div class="col-md-2">
			<div class="form-group">
				<label class="control-label">Invoice Value</label>
				<input type="text" class="form-control form-control-sm Numeric" name="invoice_value" value="<?php echo $row['invoice_value'] ?>" />
			</div>
		</div>
	</div>
	<hr />

	<table class="table table-condensed table-bordered DataEntry">
	<thead>
		<tr>
			<th width="40px">Sr No</th>
			<th width="80px">HS Code</th>
			<th>Product Description</th>
			<th width="100px">Quantity</th>
			<th width="100px">Unit</th>
			<th width="24px" class="aligncenter"><a href="#" class="CheckAll"><i class="icon-trashcan"></i></a></th>
		</tr>
	</thead>

	<tbody>
		<?php
			foreach ($products as $r) {
				echo '<tr>
			<td><input type="text" class="form-control form-control-sm Numeric" name="sr_no[' . $r['id'] . ']" value="' . $r['sr_no'] . '" /></td>
			<td><input type="text" class="form-control form-control-sm" name="hs_code[' . $r['id'] . ']" value="' . $r['hs_code'] . '" /></td>
			<td><input type="text" class="form-control form-control-sm" name="description[' . $r['id'] . ']" value="' . $r['description'] . '" /></td>
			<td><input type="text" class="form-control form-control-sm Numeric" name="quantity[' . $r['id'] . ']" value="' . $r['quantity'] . '" /></td>
			<td><input type="text" class="form-control form-control-sm" name="quantity_unit[' . $r['id'] . ']" value="' . $r['quantity_unit'] . '" /></td>
			<td class="aligncenter">' . form_checkbox(array('name' => 'delete_id['.$r['id'].']', 'value' => $r['id'], 'checked' => false, 'class' => 'DeleteCheckbox')) . '</td>
		</tr>';
			}
		?>

		<tr class="TemplateRow">
			<td><input type="text" class="form-control form-control-sm Numeric Validate Focus" name="new_sr_no[]" value="" /></td>
			<td><input type="text" class="form-control form-control-sm HSCode Validate" name="new_hs_code[]" value="" /></td>
			<td><input type="text" class="form-control form-control-sm Description Validate" name="new_description[]" value="" /></td>
			<td><input type="text" class="form-control form-control-sm Numeric Validate" name="new_quantity[]" value="" /></td>
			<td><input type="text" class="form-control form-control-sm Unit Validate" name="new_quantity_unit[]" value="" /></td>
			<td><button type="submit" class="btn btn-success btn-sm AddButton"><i class="fa fa-plus"></i></button></td>
		</tr>
	</tbody>
	</table>
</fieldset>

<div class="form-actions">
	<button type="button" class="btn btn-success" id="Update">Update</button>
</div>
</form>

<script>
$(document).ready(function() {
	$("#TOI").autocomplete({
		source: "<?php echo site_url($this->_clspath.$this->_class.'/ajaxJson/job_invoices/toi') ?>",
		minLength: 0,
	});

	$('.DataEntry').on('keydown.autocomplete', ".HSCode", function(event, items) {
		$(this).autocomplete({
			source: "<?php echo site_url($this->_clspath.$this->_class.'/ajaxJson/export_product_details/hs_code') ?>",
			minLength: 0,
		});
	});

	$('.DataEntry').on('keydown.autocomplete', ".Unit", function(event, items) {
		$(this).autocomplete({
			source: "<?php echo site_url($this->_clspath.$this->_class.'/ajaxJson/export_product_details/quantity_unit') ?>",
			minLength: 0,
		});
	});

	$('.DataEntry').on('keydown.autocomplete', ".Description", function(event, items) {
		$(this).autocomplete({
			source: "<?php echo site_url($this->_clspath.$this->_class.'/ajaxJson/export_product_details/description') ?>",
			minLength: 3,
		});
	});
});
</script>