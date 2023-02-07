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
			<div class="form-group<?php echo (strlen(form_error('code')) > 0 ? ' has-error' : '') ?>">
				<label class="control-label">Code</label>
				<input type="text" class="form-control form-control-sm input-small" name="code" value="<?php echo $row['code'] ?>" id="Code" />
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
			
			<div class="form-group<?php echo (strlen(form_error('account_no')) > 0 ? ' has-error' : '') ?>">
				<label class="control-label">Account No</label>
				<input type="text" class="form-control form-control-sm" name="account_no" value="<?php echo $row['account_no'] ?>" />
			</div>
					
			<div class="form-group<?php echo (strlen(form_error('address')) > 0 ? ' has-error' : '') ?>">
				<label class="control-label">Address</label>
				<input type="text" class="form-control form-control-sm" name="address" value="<?php echo $row['address'] ?>" />
			</div>
			
			<div class="form-group">
				<label class="control-label">City</label>
				<?php echo form_dropdown('city_id', $this->kaabar->getCities(), $row['city_id'], 'class="form-control form-control-sm" data-placeholder="Choose City Name..."'); ?>
			</div>

			<div class="form-group<?php echo (strlen(form_error('contact')) > 0 ? ' has-error' : '') ?>">
				<label class="control-label">Contact</label>
				<input type="text" class="form-control form-control-sm" name="contact" value="<?php echo $row['contact'] ?>" />
					<span class="help-block">Separate numbers by comma</span>
			</div>
			
			<div class="form-group<?php echo (strlen(form_error('fax')) > 0 ? ' has-error' : '') ?>">
				<label class="control-label">Fax</label>
				<input type="text" class="form-control form-control-sm" name="fax" value="<?php echo $row['fax'] ?>" />
					<span class="help-block">Separate numbers by comma</span>
			</div>
			
			<div class="form-group<?php echo (strlen(form_error('opening_balance')) > 0 ? ' has-error' : '') ?>">
				<label class="control-label">Opening Balance</label>
				<div class="form-inline">
					<input type="text" class="form-control form-control-sm Numeric" name="opening_balance" value="<?php echo $row['opening_balance'] ?>" />
					<?php echo form_dropdown('dr_cr', getEnumSetOptions($this->_table, 'dr_cr'), $row['dr_cr'], 'class="form-control form-control-sm"') ?>
				</div>
			</div>
		</fieldset>

	</div>

	<div class="card-footer">
		<button type="submit" class="btn btn-success" id="Update">Update</button>
	</div>
</div>
	
</form>

<?php echo end_panel(); ?>
