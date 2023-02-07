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
			<div class="form-group">
				<label class="control-label">Company Type</label>
				<?php echo form_dropdown('deductee_id', getSelectOptions('tds_classes', 'id', 'name', 'WHERE type="Deductee"'), $row['deductee_id'], 'class="form-control form-control-sm Focus" id="CompanyType"') ?>
			</div>
		
			<div class="form-group">
				<label class="control-label">Document List</label>
				<table class="table table-condensed table-striped DataEntry">
				<thead>
				<tr>
					<th width="80px">Code</th>
					<th>Name</th>
					<th width="24px" class="aligncenter"><a href="#" class="CheckAll"><i class="icon-trashcan"></i></a></th>
				</tr>
				</thead>

				<tbody>
				<?php
					foreach ($row['documents'] as $did => $doc) {
						echo '<tr>
					<td><input type="text" class="form-control form-control-sm" name="code[' . $did . ']" value="' . $doc['code'] . '" /></td>
					<td><input type="text" class="form-control form-control-sm" name="name[' . $did . ']" value="' . $doc['name'] . '" /></td>
					<td class="aligncenter">' . form_checkbox(array('name' => 'delete_id['.$did.']', 'value' => $did, 'checked' => false, 'class' => 'DeleteCheckbox')) . '</td>
				</tr>';
					}
				?>
				<tr class="TemplateRow">
					<td><input type="text" class="form-control form-control-sm ajaxCode Validate Focus" name="new_code[]" value="" /></td>
					<td><input type="text" class="form-control form-control-sm ajaxName Validate" name="new_name[]" value="" /></td>
					<td><button type="submit" class="btn btn-success btn-sm AddButton"><i class="fa fa-plus"></i></button></td>
				</tr>
				</tbody>
				</table>
			</div>
		</fieldset>
	</div>

	<div class="card-footer">
		<button type="submit" class="btn btn-success" id="Update">Update</button>
	</div>
</div>
	
</form>

<script language="JavaScript">
$(document).ready(function() {
	$('.DataEntry').on('keydown.autocomplete', '.ajaxCode', function(event, items) {
		$(this).autocomplete({
			source: "<?php echo site_url($this->_clspath.$this->_class.'/ajaxJson/kyc_document_types/code') ?>",
			minLength: 0
		});
	});

	$('.DataEntry').on('keydown.autocomplete', '.ajaxName', function(event, items) {
		$(this).autocomplete({
			source: "<?php echo site_url($this->_clspath.$this->_class.'/ajaxJson/kyc_document_types/name') ?>",
			minLength: 0
		});
	});
});
</script>
