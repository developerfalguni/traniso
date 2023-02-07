
<div id="modal-delete" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><i class="icon-times"></i></button>
				<h3>Confirm Deletion</h3>
			</div>
			<div class="modal-body"><p>Are you sure, you want to DELETE...?</p></div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" id="Delete">Delete</button>
			</div>
		</div>
	</div>
</div>


<div id="modal-email" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><i class="icon-times"></i></button>
				<h3>Resend Email</h3>
			</div>
			<div class="modal-body"><p>Are you sure, you want to Resend EMAIL...?</p></div>
			<div class="modal-footer">
				<button type="button" class="btn btn-success" id="Email">Send Email</button>
			</div>
		</div>
	</div>
</div>


<?php echo form_open($this->uri->uri_string(), 'id="NewUser"'); ?>

<div class="card card-default">
	<div class="card-header">
		<h3 class="card-title">Registration Form</h3>
	</div>
	
	<div class="card-body">
		<fieldset>
			<div class="form-group<?php echo (strlen(form_error('username')) > 0 ? ' has-error' : '') ?>">
				<label class="control-label">Username</label>
				<input type="text" class="form-control form-control-sm" name="username" value="<?php echo $row['username'] ?>" />
			</div>

			<div class="form-group<?php echo (strlen(form_error('fullname')) > 0 ? ' has-error' : '') ?>">
				<label class="control-label">Fullname</label>
				<input type="text" class="form-control form-control-sm" name="fullname" value="<?php echo $row['fullname'] ?>" />
			</div>

			<div class="form-group<?php echo (strlen(form_error('email')) > 0 ? ' has-error' : '') ?>">
				<label class="control-label">Email</label>
				<input type="text" class="form-control form-control-sm" name="email" value="<?php echo $row['email'] ?>" />
			</div>

			<div class="form-group<?php echo (strlen(form_error('auth_code')) > 0 ? ' has-error' : '') ?>">
				<label class="control-label">Authorization Code</label>
				<input type="text" class="form-control form-control-sm" name="auth_code" value="<?php echo $row['auth_code'] ?>" />
			</div>
		</fieldset>
	</div>

	<div class="card-footer">
		<a href="#modal-email" data-toggle="modal" class="btn btn-primary" data-placement="right" rel="tooltip" data-original-title="Send Authorization Code for Email Address Confirmation &amp; Account Activation...">Email</a>
		<a href="#modal-delete" class="btn btn-danger" data-toggle="modal">Delete</a>
		<?php echo form_submit('Submit', 'Activate', 'id="Update" class="btn btn-success" data-placement="right" rel="tooltip"  data-original-title="Activate the user without Email confirmation."') ?>
	</div>
</div>

</form>

<script>
$(document).ready(function() {
	$("#Email").on("click", function() {
		$("form#NewUser").attr("action", "<?php echo site_url($this->_clspath.$this->_class.'/send_email/'.$id['id']) ?>").submit();
	});

	$("#Delete").on("click", function() {
		$("form#NewUser").attr("action", "<?php echo site_url($this->_clspath.$this->_class.'/delete/'.$id['id']) ?>").submit();
	});
});
</script>