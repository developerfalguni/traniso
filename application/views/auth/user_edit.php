<?php 
echo form_open($this->uri->uri_string(), 'id="MainForm"'); 
echo form_hidden($id);
?>

<div class="card card-default">
	<div class="card-header">
		<span class="card--icon"><?php echo anchor($this->_clspath.$this->_class, '<i class="icon-list"></i>') ?></span>
		<h3 class="card-title"><?php echo $page_title ?></h3>
	</div>
	
	<div class="card-body">
		<fieldset>
			<div class="row">
				<div class="col-md-8">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group<?php echo (strlen(form_error('username')) > 0 ? ' has-error' : '') ?>">
								<label class="control-label">Username</label>
								<input type="text" class="form-control form-control-sm<?php echo (strlen(form_error('username')) > 0 ? ' has-error' : '') ?>" name="username" value="<?php echo $row['username'] ?>" id="Username" />
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group<?php echo (strlen(form_error('password')) > 0 ? ' has-error' : '') ?>">
								<label class="control-label">Password</label>
								<input type="text" class="form-control form-control-sm" name="password" value="Hidden" id="Password" disabled="disabled" />
							</div>
						</div>
					</div>

					<div class="form-group<?php echo (strlen(form_error('fullname')) > 0 ? ' has-error' : '') ?>">
						<label class="control-label">Full Name</label>
						<input type="text" class="form-control form-control-sm" name="fullname" value="<?php echo $row['fullname'] ?>" id="Fullname" />
					</div>

					<div class="form-group<?php echo (strlen(form_error('email')) > 0 ? ' has-error' : '') ?>">
						<label class="control-label">Email</label>
						<input type="text" class="form-control form-control-sm" name="email" value="<?php echo $row['email'] ?>" id="Email" />
					</div>

					<div class="row">
						<div class="col-md-6">
							<div class="form-group<?php echo (strlen(form_error('internet_access')) > 0 ? ' has-error' : '') ?>">
								<label class="control-label">Access from Internet</label>
								<?php echo form_dropdown('internet_access', getEnumSetOptions('users', 'internet_access'), $row['internet_access'],  'class="form-control form-control-sm"') ?>
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group<?php echo (strlen(form_error('status')) > 0 ? ' has-error' : '') ?>">
								<label class="control-label">Status</label>
								<?php echo form_dropdown('status', getEnumSetOptions('users', 'status'), $row['status'],  'class="form-control form-control-sm"') ?>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">Last Modified Date</label>
								<input type="text" class="form-control form-control-sm" name="modified" value="<?php echo $row['last_login'] ?>" disabled="disabled" />
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">Last Login Date</label>
								<input type="text" class="form-control form-control-sm" name="last_login" value="<?php echo $row['last_login'] ?>" disabled="disabled" />
							</div>
						</div>
					</div>
				</div>

				<div class="col-md-4">
					<table class="table table-striped">
					<thead>
					<tr>
						<th>Member Of</th>
						<th></th>
						<th>Groups</th>
					</tr>
					</thead>

					<tbody>
					<tr>
						<td width="40%" class="aligncenter aligntop"><?php echo form_dropdown('member_of[]', $member_of, null, "id='MemberOf' size='12' multiple='multiple' class='form-control'"); ?></td>
						
						<td width="20%" class="aligncenter alignmiddle"><a href="#" id="AddGroup" class="btn btn-success" rel="tooltip" data-original-title="Add Group to User" class="btn btn-success btn-sm"><i class="icon-arrow-left"></i></a><br /><br />
						<a href="#" id="DelGroup" class="btn btn-danger" data-placement="bottom" rel="tooltip" data-original-title="Remove Group from User" class="btn btn-danger btn-sm"><i class="icon-arrow-right"></i></a></td>
						
						<td width="40%" class="aligncenter aligntop"><?php echo form_dropdown('available_group[]', $available_group, null, "id='AvailableGroups' size='12' multiple='multiple' class='form-control'"); ?></td>
					</tr>
					</tbody>
					</table>
				</div>
			</div>
		</fieldset>
	</div>

	<div class="card-footer">
		<?php echo form_submit("Submit", "Update", "class=\"btn btn-success\" id=\"Update\" tabindex='11' onClick=\"return SelectValues();\"") ?>
		<button type='button' class='btn btn-default' data-placement="right" rel="tooltip" data-original-title="New password will be set as '<?php echo $random_password ?>'" onclick="javascript: resetPassword()">Reset Password</button>
	</div>
</div>
</form>

<script type="text/javascript" language="JavaScript">
function resetPassword(){
	var res = confirm("Reset Password ???");
	if (res) {
		$("form").attr("action", "<?php echo site_url($reset_url) ?>").submit();
	}
}

function SelectValues() {
	$("select#MemberOf option").attr("selected", "true");
}

$('#AddGroup').on('click', function(event){
	return !$('#AvailableGroups option:selected').appendTo('#MemberOf');
});

$('#DelGroup').on('click', function(event){
	return !$('#MemberOf option:selected').appendTo('#AvailableGroups');
});
</script>
