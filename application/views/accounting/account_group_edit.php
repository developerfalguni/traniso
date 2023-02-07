<?php echo form_open($this->uri->uri_string(), 'class="form-horizontal" id="MainForm"'); ?>

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
			<div class="form-group<?php echo (strlen(form_error('id')) > 0 ? ' has-error' : '') ?>">
				<label class="control-label col-md-2">Code</label>
				<div class="col-md-10">
					<input type="text" class="form-control form-control-sm" name="id" value="<?php echo $row['id'] ?>" />
				</div>
			</div>
			
			<div class="form-group<?php echo (strlen(form_error('name')) > 0 ? ' has-error' : '') ?>">
				<label class="control-label col-md-2">Name</label>
				<div class="col-md-10">
					<input type="text" class="form-control form-control-sm" name="name" value="<?php echo $row['name'] ?>" size="40" id="Name" />
				</div>
			</div>
				
			<div class="form-group<?php echo (strlen(form_error('account_id')) > 0 ? ' has-error' : '') ?>">
				<label class="control-label col-md-2">Primary Account</label>
				<div class="col-md-10">
					<?php echo form_dropdown('account_id', getSelectOptions('accounts'), $row['account_id'], 'class="form-control form-control-sm"'); ?>
				</div>
			</div>
		</fieldset>
	</div>

	<div class="card-footer">
		<button type="submit" class="btn btn-success" id="Update">Update</button>
	</div>
</div>

</form>