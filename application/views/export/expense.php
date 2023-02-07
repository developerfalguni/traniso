<?php
echo form_open($this->uri->uri_string(), 'id="MainForm"');
echo form_hidden($job_id);
?>

<fieldset>
<table class="table table-condensed table-striped DataEntry">
<thead>
	<tr>
		<th>Sr No</th>
		<th>Vendor</th>
		<th>Particular</th>
		<th>Quantity</th>
		<th>Remarks</th>
		<th width="24px" class="aligncenter"><a href="#" class="CheckAll"><i class="icon-trashcan"></i></a></th>
	</tr>
</thead>

<tbody>
	<?php 
	$i = 1;
	foreach ($rows as $r) {
		echo '<tr>
			<td class="nowrap">' . $i++ . '</td>
			<td class="nowrap"><input type="text" class="form-control form-control-sm" name="vendor[' . $r['id'] . ']" value="' . $r['vendor'] . '" /></td>
			<td class="nowrap"><input type="text" class="form-control form-control-sm" name="particulars[' . $r['id'] . ']" value="' . $r['particulars'] . '" /></td>
			<td class="nowrap"><input type="text" class="form-control form-control-sm" name="quantity[' . $r['id'] . ']" value="' . $r['quantity'] . '" /></td>
			<td class="nowrap"><input type="text" class="form-control form-control-sm" name="remarks[' . $r['id'] . ']" value="' . $r['remarks'] . '" /></td>
			<td class="aligncenter">' . form_checkbox(array('name' => 'delete_id['.$r['id'].']', 'value' => $r['id'], 'checked' => false, 'class' => 'DeleteCheckbox')) . '</td>
		</tr>';
	} ?>

	<tr class="TemplateRow">
		<td></td>
		<td><input type="text" class="form-control form-control-sm ajaxVendor Validate Focus" name="new_vendor[]" /></td>
		<td><input type="text" class="form-control form-control-sm ajaxParticular Validate" name="new_particulars[]" /></td>
		<td><input type="text" class="form-control form-control-sm Validate" name="new_quantity[]" /></td>
		<td><input type="text" class="form-control form-control-sm" name="new_remarks[]" /></td>
		<td class="aligncenter"><button type="submit" class="btn btn-success btn-sm AddButton"><i class="icon-white icon-plus"></i></button></td>
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
	$(".ajaxVendor").autocomplete({
		source: '<?php echo site_url($this->_clspath.$this->_class.'/ajaxJson/expenses/vendor') ?>',
		minLength: 1,
	});

	$(".ajaxParticulars").autocomplete({
		source: '<?php echo site_url($this->_clspath.$this->_class.'/ajaxJson/expenses/particulars') ?>',
		minLength: 1,
	});

	$(".ajaxQuantity").autocomplete({
		source: '<?php echo site_url($this->_clspath.$this->_class.'/ajaxJson/expenses/quantity') ?>',
		minLength: 1,
	});

	$(".ajaxRemarks").autocomplete({
		source: '<?php echo site_url($this->_clspath.$this->_class.'/ajaxJson/expenses/remarks') ?>',
		minLength: 1,
	});
});
</script>