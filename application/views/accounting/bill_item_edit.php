
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
		<div class="row">
			<div class="col-md-2">
				<div class="form-group">
					<label class="control-label">Code</label>
					<input type="text" class="form-control form-control-sm" name="code" value="<?php echo $row['code'] ?>" size="10" id="Code" />
				</div>
			</div>
			<div class="col-md-10">
				<div class="form-group">
					<label class="control-label">Name</label>
					<input type="text" class="form-control form-control-sm" name="name" value="<?php echo $row['name'] ?>" size="40" id="Name" />
				</div>
			</div>
		</div>
		
		<div class="row">
			<div class="col-md-12">
				<div class="form-group">
					<label class="control-label">S.Tax Category</label>
					<?php echo form_dropdown('stax_category_id', array(0=>'')+getSelectOptions('stax_categories', 'id', 'name'), $row['stax_category_id'], 'class="form-control form-control-sm"'); ?>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-2">
				<div class="form-group">
					<label class="control-label">Type</label>
					<?php echo form_dropdown('type', getEnumSetOptions('ledgers', 'type'), $row['type'], 'class="form-control form-control-sm"'); ?>
				</div>
			</div>
		
			<div class="col-md-2">
				<div class="form-group">
					<label class="control-label">CGST</label>
					<input type="text" class="form-control form-control-sm" name="cgst" value="<?php echo $row['cgst'] ?>" id="cgst" />
				</div>
			</div>

			<div class="col-md-2">
				<div class="form-group">
					<label class="control-label">SGST</label>
					<input type="text" class="form-control form-control-sm" name="sgst" value="<?php echo $row['sgst'] ?>" id="sgst" />
				</div>
			</div>
			
			<div class="col-md-2">
				<div class="form-group">
					<label class="control-label">IGST</label>
					<input type="text" class="form-control form-control-sm" name="igst" value="<?php echo $row['igst'] ?>" id="igst" />
				</div>
			</div>

			<div class="col-md-4">
				<div class="form-group">
					<label class="control-label">SAC / HSN</label>
					<input type="text" class="form-control form-control-sm" name="sac_hsn" value="<?php echo $row['sac_hsn'] ?>" id="sac_hsn" />
				</div>
			</div>
		</div>
		
		<div class="row">
			<div class="col-md-2">
				<div class="form-group">
					<label class="control-label">Reimbusment</label>
					<?php echo form_dropdown('reimbursement', getEnumSetOptions('ledgers', 'reimbursement'), $row['reimbursement'], 'class="form-control form-control-sm"'); ?>
				</div>
			</div>

			<div class="col-md-2">
				<div class="form-group">
					<label class="control-label">Job Required</label>
					<?php echo form_dropdown('job_required', getEnumSetOptions('ledgers', 'job_required'), $row['job_required'], 'class="form-control form-control-sm"'); ?>
				</div>
			</div>

			<div class="col-md-2">
				<div class="form-group">
					<label class="control-label">Active</label>
					<?php echo form_dropdown('active', getEnumSetOptions('ledgers', 'active'), $row['active'], 'class="form-control form-control-sm"'); ?>
				</div>
			</div>
		</div>
			
		<div class="form-group">
			<label class="control-label">Remarks</label>
			<textarea name="remarks" class="form-control form-control-sm"><?php echo $row['remarks'] ?></textarea>
		</div>
	</div>

	<div class="card-footer">
		<button type="submit" class="btn btn-success" id="Update">Update</button>&nbsp;&nbsp;
		<a href="#modal-delete" data-toggle="modal" class="btn btn-danger">Delete</a>
	</div>
</div>
</form>

<script type="text/javascript">
$(document).ready(function() {
	$("#sac_hsn").kaabar_typeahead({
		name: 'tt_sac_hsn',
		displayKey: 'id',
		url: '<?php echo site_url($this->_clspath.$this->_class.'/json/goods_services/id/name') ?>',
		suggestion: '<p><strong>{{id}}</strong> - {{name}}</p>'
	});
});
</script>