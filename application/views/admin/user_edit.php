<?php 
echo form_open($this->_clspath.$this->_class.'/ajaxEdit', 'id="MainForm"'); 
echo form_hidden($id);
?>

<div class="card card-default">
	<div class="card-header">
		<h3 class="card-title"><span class=""><?php echo anchor($this->_clspath.$this->_class, '<i class="fa fa-list pr-1"></i>'. strtoupper($page_title)) ?></span></h3>
		<div class="card-tools w-50">
			<div class="form-group mb-0 row">
				<label class="col-md-3 col-form-label col-form-label-sm">Select User : </label>
				<div class="col-sm-9">
				<?php echo form_dropdown('userList', [0 =>'Select User'], '', 'class="form-control mb-0" id="userSelect2" '); ?>
				</div>
			</div>
		</div>
	</div>
	<div class="card-body">
		<fieldset>
			<div class="row">
				<div class="col-md-8">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group<?php echo (strlen(form_error('username')) > 0 ? ' has-error' : '') ?>">
								<label class="control-label">Username</label>
								<input type="text" class="form-control form-control-sm<?php echo (strlen(form_error('username')) > 0 ? ' has-error' : '') ?>" name="username" value="" id="Username" />
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
						<input type="text" class="form-control form-control-sm" name="fullname" value="" id="Fullname" />
					</div>

					<div class="form-group<?php echo (strlen(form_error('email')) > 0 ? ' has-error' : '') ?>">
						<label class="control-label">Email</label>
						<input type="text" class="form-control form-control-sm" name="email" value="" id="Email" />
					</div>

					<div class="row">
						<div class="col-md-6">
							<div class="form-group<?php echo (strlen(form_error('internet_access')) > 0 ? ' has-error' : '') ?>">
								<label class="control-label">Access from Internet</label>
								<?php echo form_dropdown('internet_access', getEnumSetOptions('users', 'internet_access'), '',  'class="form-control form-control-sm" id="InternetAccess"') ?>
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group<?php echo (strlen(form_error('status')) > 0 ? ' has-error' : '') ?>">
								<label class="control-label">Status</label>
								<?php echo form_dropdown('status', getEnumSetOptions('users', 'status'), '',  'class="form-control form-control-sm" id="Status"') ?>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">Last Modified Date</label>
								<input type="text" class="form-control form-control-sm" name="modified" id="Modified" value="" disabled="disabled" />
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">Last Login Date</label>
								<input type="text" class="form-control form-control-sm" name="last_login" id="LastLogin" value="" disabled="disabled" />
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
		<button type="button" class="btn btn-success" id="Update" tabindex='11' onClick="return SelectValues();">Update</button>
		<button type='button' class='btn btn-default' data-placement="right" rel="tooltip" data-original-title="New password will be set as '<?php echo $random_password ?>'" onclick="javascript: resetPassword()">Reset Password</button>
	</div>
</div>
</form>

<script type="text/javascript" language="JavaScript">

	$(function() {
	    updateUser('0');
	});

	$(document).ready(function() {
		$('#userSelect2').select2({
			ajax: { 
				url: '<?php echo site_url($this->_clspath.$this->_class) ?>/userList/',
				type: "GET",
				dataType: 'json',
				delay: 250,
				beforeSend: function(){
		            $("#loader").show();
		        },
				processResults: function (response) {
					$("#loader").hide();
			  		return {
			     		results: response
			  		};
				},
				cache: true
			},
			
	    }).on("change", function () {
	    	//console.log($(this).val())
	    	var user_id = $(this).val();
	    	// $('#updateUser').html();
	    	updateUser(user_id);
	    });
	});

	function updateUser(id = null)
    {   
    	if(! id){
        	return false;
        }
        
        if(id){
        	$.ajax({
                url: '<?php echo base_url($this->_clspath.$this->_class) ?>/getUser/'+id,
                type: 'post',
                dataType: 'json',
                beforeSend: function(){
		            $("#loader").show();
		        },
                success:function(response) {

                	if(response.success == false){
                		$("#loader").hide();
                		$('#MainForm')[0].reset();
					}
                	else
                	{
						$('#MainForm')[0].reset();
	                	
	                	$("input[name=id]").val(response.row.id);
			    		$("#Username").val(response.row.username);
			    		$("#Fullname").val(response.row.fullname);
			    		$("#Email").val(response.row.email);
			    		$("#InternetAccess").val(response.row.internet_access);
			    		$("#Status").val(response.row.status);
			    		$("#Modified").val(response.row.last_modified);
			    		$("#LastLogin").val(response.row.last_login);

			    		$("#loader").hide();
            		}

            		$("#MainForm").unbind('submit').on('submit', function(e) {
            				e.preventDefault();
				         	// console.log($("#Name").val())
				         	// return false

			            	//$(this).validate();
			            	var formData = new FormData(this);

							$(".text-danger").remove();

							$.ajax({
							  	url: $(this).attr('action'),
							  	type: $(this).attr('method'),
							  	data: formData, // /converting the form data into array and sending it to server
							  	dataType: 'json',
							  	contentType: false,
								processData: false,
								beforeSend: function(){
						            $("#loader").show();
						        },
							  	success:function(response) {
							  		
							  		if(response.success === true) {
							  			$("#loader").hide();
							  			Swal.fire({
									        title: 'Wow...!',
									        html:  ""+response.messages+"",
									        icon: "success",
									    });
									} 
									else 
									{

										if(response.messages instanceof Object) {
										$.each(response.messages, function(index, value) {
											var id = $("#"+index);

											id.removeClass('is-invalid')
											.removeClass('is-valid')
											.addClass(value.length > 0 ? 'is-invalid' : 'is-valid');

											id.after(value);

											});
											$("#loader").hide();
										} 
										else 
										{
											$("#loader").hide();
											Swal.fire({
										        title: 'Opps...!',
										        html:  ""+response.messages+"",
										        icon: "error",
										    });
										}
									}
									return false;
								}
							}); 
							return false;
					});
            	}
        	});
        }
    }

	function resetPassword(){
		var res = confirm("Reset Password ???");
		if (res) {
			$("form").attr("action", "<?php //echo site_url($this->_clspath.$this->_class."/reset_password/".$row['id']) ?>").submit();
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
