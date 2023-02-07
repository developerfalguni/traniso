<div id="modal-copy" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><i class="icon-times"></i></button>
				<h3>Copy Documents From</h3>
			</div>
			<div class="modal-body">
				<select class="form-control form-control-sm" id="CopyDocuments">
				<?php foreach ($document_types as $dt) {
					echo '<option value="' . $dt['id'] . '">' . $dt['product_name'] . ' - ' . $dt['type'] . ' - ' . $dt['cargo_type'] . '</option>';
				} ?>
				</select>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-success" onclick="javascript: copyDocuments()">Copy Now</button>
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
				<div class="col-md-4">
					<div class="form-group">
						<label class="control-label">Product</label>
						<?php echo form_dropdown('product_id', getSelectOptions('products'), $row['product_id'], 'class="form-control form-control-sm" id="Product"') ?>
					</div>
				</div>

				<div class="col-md-4">
					<div class="form-group">
						<label class="control-label">Type</label>
						<?php echo form_dropdown('type', getEnumSetOptions($this->_table, 'type'), $row['type'], 'class="form-control form-control-sm"') ?>
					</div>
				</div>

				<div class="col-md-4">
					<div class="form-group">
						<label class="control-label">Cargo Type</label>
						<?php echo form_dropdown('cargo_type', getEnumSetOptions($this->_table, 'cargo_type'), $row['cargo_type'], 'class="form-control form-control-sm"') ?>
					</div>
				</div>
			</div>
		</fieldset>

		<fieldset>
			<legend>Document List</legend>
			<table class="table table-condensed table-striped DataEntry Sortable">
			<thead>
			<tr>
				<th width="24px"></th>
				<th width="54px">No</th>
				<th width="80px">Code</th>
				<th>Name</th>
				<th width="80px"><i class="icon-eye"></i> Pending</th>
				<th width="80px">Compulsory</th>
				<th width="24px" class="aligncenter"><a href="#" class="CheckAll"><i class="icon-trashcan"></i></a></th>
			</tr>
			</thead>

			<tbody>
			<?php
				$sr_no = 1;
				foreach ($row['documents'] as $did => $doc) {
					echo '<tr>
				<td class="aligncenter grayLight SortHandle"><i class="icon-bars"></i></th>
				<td><input type="text" class="form-control form-control-sm Numeric" name="sr_no[' . $did . ']" value="' . $doc['sr_no'] . '" size="3" /></td>
				<td><input type="text" class="form-control form-control-sm" name="code[' . $did . ']" value="' . $doc['code'] . '" /></td>
				<td><input type="text" class="form-control form-control-sm" name="name[' . $did . ']" value="' . $doc['name'] . '" /></td>
				<td class="aligncenter"><label><input type="checkbox" name="is_pending[' . $did . ']" value="Yes" ' . ($doc['is_pending'] == 'Yes' ? 'checked="true"' : '') . ' /> Yes</label></td>
				<td class="aligncenter"><label><input type="checkbox" name="is_compulsory[' . $did . ']" value="Yes" ' . ($doc['is_compulsory'] == 'Yes' ? 'checked="true"' : '') . ' /> Yes</label></td>
				<td class="aligncenter">' . form_checkbox(array('name' => 'delete_id['.$did.']', 'value' => $did, 'checked' => false, 'class' => 'DeleteCheckbox')) . '</td>
			</tr>';
					$sr_no = $doc['sr_no'];
				}
			?>
			<tr class="TemplateRow">
				<td class="aligncenter grayLight SortHandle"><i class="icon-bars"></i></th>
				<td><input type="text" class="form-control form-control-sm Numeric Validate Unchanged Increment" name="new_sr_no[]" value="<?php echo ($sr_no + 1) ?>" size="3" /></td>
				<td><input type="text" class="form-control form-control-sm ajaxCode Validate Focus" name="new_code[]" value="" /></td>
				<td><input type="text" class="form-control form-control-sm ajaxName Validate" name="new_name[]" value="" /></td>
				<td class="aligncenter"><label><input type="checkbox" name="new_is_pending[]" value="Yes" checked="true" /> Yes</label></td>
				<td class="aligncenter"><label><input type="checkbox" name="new_is_compulsory[]" value="Yes" checked="true" /> Yes</label></td>
				<td><button type="submit" class="btn btn-success btn-sm AddButton"><i class="fa fa-plus"></i></button></td>
			</tr>
			</tbody>
			</table>
		</fieldset>		
	</div>
	
	<div class="card-footer">
		<button type="button" class="btn btn-success" id="Update">Update</button></td>
	</div>	
</div>

</form>

<script language="JavaScript">
function copyDocuments() {
	$("#modal-copy").modal('hide');
	var id = $("#CopyDocuments").val();
	$.get('<?php echo base_url($this->_clspath.$this->_class."/loadDocuments") ?>/'+id);
}

$(document).ready(function() {
	$('.DataEntry').on('keydown.autocomplete', '.ajaxCode', function(event, items) {
		$(this).autocomplete({
			source: "<?php echo site_url($this->_clspath.$this->_class.'/ajaxJson/document_types/code') ?>",
			minLength: 0
		});
	});

	$('.DataEntry').on('keydown.autocomplete', '.ajaxName', function(event, items) {
		$(this).autocomplete({
			source: "<?php echo site_url($this->_clspath.$this->_class.'/ajaxJson/document_types/name') ?>",
			minLength: 0
		});
	});
});
</script>
