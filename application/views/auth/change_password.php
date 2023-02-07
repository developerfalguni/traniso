<?php echo form_open($this->uri->uri_string(), 'id="MainForm"'); ?>

<div class="card card-default">
	<div class="card-header">
		<span class="card--icon"><?php echo anchor($this->_clspath.$this->_class, '<i class="icon-list"></i>') ?></span>
		<h3 class="card-title"><?php echo $page_title ?></h3>
	</div>
	
	<div class="card-body">
		<fieldset>
			<div class="form-group<?php echo (strlen(form_error('current_password')) > 0 ? ' has-error' : '') ?>">
				<label class="control-label">Current Password</label>
				
					<input type="password" class="form-control form-control-sm input-large" name="current_password" value="" id="CPassword" />
				
			</div>

			<div class="form-group<?php echo (strlen(form_error('new_password')) > 0 ? ' has-error' : '') ?>">
				<label class="control-label">New Password</label>
				
					<input type="password" class="form-control form-control-sm input-large" name="new_password" value="" id="NPassword" />
				
			</div>

			<div class="form-group<?php echo (strlen(form_error('retype_password')) > 0 ? ' has-error' : '') ?>">
				<label class="control-label">Retype Password</label>
				
					<input type="password" class="form-control form-control-sm input-large" name="retype_password" value="" id="RPassword" />
				
			</div>
		</fieldset>
	</div>

	<div class="card-footer">
		<input type="submit" value="Change" name="commit" class="btn btn-success">
	</div>
</div>

</form>
