<div class="row">
	<div class="col-md-8 col-md-offset-2">
	<h3 class="aligncenter"><?php echo Settings::get('company_name') ?></h3>

<?php echo form_open($this->uri->uri_string(), 'class="form-horizontal" id="MainForm"'); ?>

<div class="card card-default">
	<div class="card-header">
		<h3 class="card-title">Registration Form</h3>
	</div>
	
	<div class="card-body">
		<fieldset>
			<div class="form-group<?php echo (strlen(form_error('username')) > 0 ? ' has-error' : '') ?>">
				<label class="control-label col-md-4">Username</label>
				<div class="col-md-8">
					<input type="text" class="form-control form-control-sm" name="username" value="<?php echo set_value('username') ?>" />
				</div>
			</div>

			<div class="form-group<?php echo (strlen(form_error('password')) > 0 ? ' has-error' : '') ?>">
				<label class="control-label col-md-4">Password</label>
				<div class="col-md-8">
					<input type="password" class="form-control form-control-sm" name="password" value="<?php echo set_value('password') ?>" />
				</div>
			</div>
					
			<div class="form-group<?php echo (strlen(form_error('retype_password')) > 0 ? ' has-error' : '') ?>">
				<label class="control-label col-md-4">Retype Password</label>
				<div class="col-md-8">
					<input type="password" class="form-control form-control-sm" name="retype_password" value="<?php echo set_value('retype_password') ?>" />
				</div>
			</div>

			<div class="form-group<?php echo (strlen(form_error('fullname')) > 0 ? ' has-error' : '') ?>">
				<label class="control-label col-md-4">Fullname</label>
				<div class="col-md-8">
					<input type="text" class="form-control form-control-sm" name="fullname" value="<?php echo set_value('fullname') ?>" />
				</div>
			</div>

			<div class="form-group<?php echo (strlen(form_error('email')) > 0 ? ' has-error' : '') ?>">
				<label class="control-label col-md-4">Email</label>
				<div class="col-md-8">
					<input type="text" class="form-control form-control-sm" name="email" value="<?php echo set_value('email') ?>" />
				</div>
			</div>

			<div class="form-group<?php echo (strlen(form_error('captcha')) > 0 ? ' has-error' : '') ?>">
				<label class="control-label col-md-4">Captcha Code</label>
				<div class="col-md-8">
					<div class="form-inline">
						<input type="text" class="form-control form-control-sm input-small" name="captcha" value="<?php echo set_value('captcha') ?>" />
						<?php echo $captcha_image['captcha_image'] ?>
					</div>
				</div>				
			</div>
		</fieldset>
	</div>

	<div class="card-footer">
		<?php echo anchor('main/login', '<i class="icon-arrow-left"></i> Back to Login', 'class="btn btn-default"') ?>
		<button type="submit" name="commit" class="btn btn-primary pull-right">Register</button>
	</div>
</div>

</form>

	</div>
</div>