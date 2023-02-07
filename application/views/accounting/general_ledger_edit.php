
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
			<div class="col-md-8">
				<div class="row">
					<div class="col-md-3">
						<div class="form-group<?php echo (strlen(form_error('code')) > 0 ? ' has-error' : '') ?>">
							<label class="control-label">Code</label>
							<input type="text" class="form-control form-control-sm" name="code" value="<?php echo $row['code'] ?>" id="Code" />
						</div>
					</div>

					<div class="col-md-3"></div>

					<div class="col-md-3">
					</div>

					<div class="col-md-3">
					</div>
				</div>
				
				<div class="form-group<?php echo (strlen(form_error('name')) > 0 ? ' has-error' : '') ?>">
					<label class="control-label">Name</label>
					<input type="text" class="form-control form-control-sm" name="name" value="<?php echo $row['name'] ?>" />
				</div>

				<div class="form-group<?php echo (strlen(form_error('category')) > 0 ? ' has-error' : '') ?>">
					<label class="control-label">Category</label>
					<?php echo form_dropdown('category', $categories, $row['category'], 'class="form-control form-control-sm"') ?>
				</div>

				<div class="form-group<?php echo (strlen(form_error('account_group_id')) > 0 ? ' has-error' : '') ?>">
					<label class="control-label">Accounting Head</label>
					<?php echo form_dropdown('account_group_id', getSelectOptions('account_groups', 'id', 'name'), $row['account_group_id'], 'class="form-control form-control-sm"') ?>
				</div>

				<div class="row">
					<div class="col-md-4">
						<div class="form-group<?php echo (strlen(form_error('opening_balance')) > 0 ? ' has-error' : '') ?>">
							<label class="control-label">Opening Balance</label>
							<div class="input-group">
								<input type="text" class="form-control form-control-sm Numeric" name="opening_balance" value="<?php echo $row['opening_balance'] ?>" />
								<div class="input-group-btn">
									<?php echo form_dropdown('dr_cr', getEnumSetOptions($this->_table, 'dr_cr'), $row['dr_cr'], 'class="btn"') ?>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="form-group<?php echo (strlen(form_error('tds_class_id')) > 0 ? ' has-error' : '') ?>">
					<label class="control-label">TDS Deductee Class</label>
					<?php echo form_dropdown('tds_class_id', array(0=>'')+getSelectOptions('tds_classes', 'id', 'CONCAT(type, " - ", name)'), $row['tds_class_id'], 'class="form-control form-control-sm"') ?>
				</div>

				<div class="row">
					<div class="col-md-2">
						<div class="form-group">
							<label class="control-label">GST</label>
							<?php echo form_dropdown('gst', getEnumSetOptions('ledgers', 'gst'), $row['gst'], 'class="form-control form-control-sm"'); ?>
						</div>
					</div>

					<div class="col-md-2">
						<div class="form-group">
							<label class="control-label">CGST</label>
							<input type="text" class="form-control form-control-sm" name="cgst" value="<?php echo $row['cgst'] ?>" />
						</div>
					</div>

					<div class="col-md-2">
						<div class="form-group">
							<label class="control-label">SGST</label>
							<input type="text" class="form-control form-control-sm" name="sgst" value="<?php echo $row['sgst'] ?>" />
						</div>
					</div>

					<div class="col-md-2">
						<div class="form-group">
							<label class="control-label">IGST</label>
							<input type="text" class="form-control form-control-sm" name="igst" value="<?php echo $row['igst'] ?>" />
						</div>
					</div>
					
					<div class="col-md-4">
						<div class="form-group">
							<label class="control-label">S.Tax Category</label>
							<?php echo form_dropdown('stax_category_id', array(0=>'')+getSelectOptions('stax_categories', 'id', 'name'), $row['stax_category_id'], 'class="form-control form-control-sm"'); ?>
						</div>
					</div>
				</div>
			</div>

			<div class="col-md-4">
			</div>
		</div>
	</div>

	<div class="card-footer">
		<button type="submit" class="btn btn-success" id="Update">Update</button>&nbsp;&nbsp;
		<a href="#modal-delete" data-toggle="modal" class="btn btn-danger">Delete</a>
	</div>
</div>

</form>
