<?php if (!Auth::isAdmin() AND $row['action'] != 'Authorised') : ?>
	<fieldset>
		<legend>Request Message</legend>
		<div class="form-group<?php echo (strlen(form_error('to_user_id')) > 0 ? ' has-error' : '') ?>">
			<label class="control-label">To User</label>
			
				<?php echo form_dropdown('to_user_id[]', $this->messages->getUsers($action), set_value('to_user_id'), 'class="SelectizeKaabar" multiple data-placeholder="Select ' . $action . ' User(s)..."') ?>
			
		</div>
		
		<div class="form-group<?php echo (strlen(form_error('message')) > 0 ? ' has-error' : '') ?>">
			<label class="control-label">Message</label>
			
				<input type="text" class="form-control form-control-sm" name="message" value="<?php echo set_value('message') ?>" />
			
		</div>
	</fieldset>
<?php endif; ?>