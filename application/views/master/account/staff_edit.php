<div id="modal-photo" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
		<?php echo form_open_multipart($this->_clspath.$this->_class.'/photoadd/'.$id['id'], array('id' => 'ImageForm')); ?>
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><i class="icon-times"></i></button>
				<h3>Upload Photo</h3>
			</div>
			<div class="modal-body">
				<p><input type="file" name="userfile" size="40" /></p>
			</div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-success">Upload</button>
			</div>
		</form>
		</div>
	</div>
</div>

<div id="modal-del-photo" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><i class="icon-times"></i></button>
				<h3>Confirm Deletion</h3>
			</div>
			<div class="modal-body"><p>Are you sure, you want to DELETE...?</p></div>
			<div class="modal-footer">
				<?php echo anchor($this->_clspath.$this->_class.'/photodel/'.$id['id'], 'Delete', 'class="btn btn-danger"') ?>
			</div>
		</div>
	</div>
</div>


<div id="modal-document" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
		<?php echo form_open_multipart($this->_clspath.$this->_class.'/documentadd/'.$id['id'], array('id' => 'ImageForm')); ?>
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><i class="icon-times"></i></button>
				<h3>Upload Document</h3>
			</div>
			<div class="modal-body">
				<input type="hidden" name="staff_document_id" value="0" id="StaffDocumentID" />
				<input type="hidden" name="staff_document_type_id" value="0" id="StaffDocumentTypeID" />
				<input type="file" name="userfile" size="40" />
			</div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-success">Upload</button>
			</div>
		</form>
		</div>
	</div>
</div>

<?php
echo form_open($this->_clspath.$this->_class.'/ajaxEdit', 'id="MainForm"');
echo form_hidden($id);
?>

<div class="card card-default">
	<div class="card-header">
		<h3 class="card-title"><span class=""><?php echo anchor($this->_clspath.$this->_class, '<i class="fa fa-list pr-1"></i>'. strtoupper($page_title)) ?></span></h3>
		<div class="card-tools w-50">
      			<div class="form-group mb-0 row">
					<label class="col-md-3 col-form-label col-form-label-sm">Select Staff : </label>
					<div class="col-sm-9">
						<?php echo form_dropdown('staffList', [], '', 'class="form-control mb-0" id="staffSelect2" '); ?>
					</div>
				</div>
		</div>
	</div>
	
	<div class="card-body">
		<label class="control-label h5 pt-1 text-center" >Total Count: <?php echo $count ?></label>
		<div class="row">
			<div class="col-md-9">
				<fieldset class="inputs">
					<div class="row">
						<div class="col-md-2">
							<div class="form-group">
								<label class="control-label">Title</label>
								<?php echo form_dropdown('title', getEnumSetOptions($this->_table, 'title'), '', 'class="form-control form-control-sm" id="title"'); ?>
							</div>
						</div>

						<div class="col-md-3">
							<div class="form-group">
								<label class="control-label">First Name</label>								
									<input type="text" class="form-control form-control-sm" name="firstname" value="" id="firstname" />
								
							</div>
						</div>

						<div class="col-md-3">
							<div class="form-group">
								<label class="control-label">Middle Name</label>								
									<input type="text" class="form-control form-control-sm" name="middlename" value="" id="middlename" />
								
							</div>
						</div>

						<div class="col-md-4">
							<div class="form-group">
								<label class="control-label">Last Name</label>								
									<input type="text" class="form-control form-control-sm" name="lastname" value="" id="lastname" />
								
							</div>
						</div>
					</div>
					
					<div class="row">
						<div class="col-md-2">
								<div class="form-group">
								<label class="control-label">Gender</label>								
									<?php echo form_dropdown('gender', getEnumSetOptions($this->_table, 'gender'), '', 'class="form-control form-control-sm" id="gender"'); ?>
								
							</div>
						</div>

						<div class="col-md-2">
							<div class="form-group">
								<label class="control-label">Date of Birth</label>
								<div class="input-group input-group-sm">
									<input type="text" class="form-control form-control-sm DatePicker" name="dob" value="<?php echo date('d-m-Y') ?>" id="dob">
									<div class="input-group-append">
										<span class="input-group-text"><i class="icon-calendar"></i></span>
									</div>
								</div>
							</div>
						</div>

						<div class="col-md-4">
							<div class="form-group">
								<label class="control-label">Designation</label>								
									<input type="text" class="form-control form-control-sm" name="designation" value="" id="designation" />
								
							</div>
						</div>

						<div class="col-md-4">
							<div class="form-group<">
								<label class="control-label">Category</label>								
									<input type="text" class="form-control form-control-sm" name="category" value="" id="category" />
								
							</div>
						</div>
					</div>
					
					<div class="row">
						<div class="col-md-2">
							<div class="form-group">
								<label class="control-label">Status</label>								
								<?php echo form_dropdown('status', getEnumSetOptions($this->_table, 'status'), '', 'class="form-control form-control-sm" id="status"'); ?>
								
							</div>
						</div>
					
						<div class="col-md-2">
							<div class="form-group">
								<label class="control-label">Joining Date</label>
								<div class="input-group input-group-sm">
									<input type="text" class="form-control form-control-sm DatePicker" name="date_joined" value="<?php echo date('d-m-Y') ?>" id="date_joined">
									<div class="input-group-append">
										<span class="input-group-text"><i class="icon-calendar"></i></span>
									</div>
								</div>
							</div>
						</div>

						<div class="col-md-4">				
							<div class="form-group">
								<label class="control-label">Leaving Date</label>
								<div class="input-group input-group-sm">
									<input type="text" class="form-control form-control-sm DatePicker" name="date_left" value="<?php echo date('d-m-Y') ?>" id="date_left">
									<div class="input-group-append">
										<span class="input-group-text"><i class="icon-calendar"></i></span>
									</div>
								</div>
							</div>
						</div>

						<div class="col-md-4">
							<div class="form-group">
								<label class="control-label">Location</label>								
									<input type="text" class="form-control form-control-sm" name="location" value="" id="location" />
								
							</div>
						</div>
					</div>
					
					<div class="row">
						<div class="col-md-2">
							<div class="form-group">
								<label class="control-label">Company</label>								
									<?php echo form_dropdown('company_id', getSelectOptions('companies', 'id', 'code'), '', 'class="form-control form-control-sm" id="company_id"') ?>
								
							</div>
						</div>

						<div class="col-md-2">
							<div class="form-group">
								<label class="control-label">Login Username</label>								
									<?php echo form_dropdown('user_id', array(0=>'')+getSelectOptions('users', 'id', 'username', ''), '', 'class="form-control form-control-sm" id="user_id"') ?>
								
							</div>
						</div>

						<div class="col-md-4">
							<div class="form-group">
								<label class="control-label">Permission</label>								
									<?php echo form_dropdown('permission[]', getEnumSetOptions($this->_table, 'permission'), explode(',', ''), 'class="SelectizeKaabar" multiple data-placeholder="Choose permissions..." id="permission"') ?>
								
							</div>
						</div>

						<div class="col-md-4">
							<div class="form-group">
								<label class="control-label">Reports To</label>
								<input type="hidden" name="parent_id" value="" id="parent_id" />
								<input type="text" class="form-control form-control-sm" name="reports_to" value="" id="reports_to" />
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">Address</label>
								<input type="text" class="form-control form-control-sm" name="address" value="" id="address"/>
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">City</label>
								<input type="text" class="form-control form-control-sm" name="city_id" id="ajaxCity" value="" />
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">Contact</label>
								<input type="text" class="form-control form-control-sm" name="contact" value="" id="contact" maxlength="10" onkeypress="return isNumber(event)"/>
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">Email</label>
								<input type="text" class="form-control form-control-sm" name="email" value="" id="email"/>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">Bank Branch</label>
								<input type="hidden" name="bank_branch_id" value="" id="bank_branch_id" />
								<input type="text" class="form-control form-control-sm" name="bank_branch" value="" id="bank_branch" placeholder="IFSC Code or Bank Branch e.g.: ICICI Gandhidham" />
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">Account No</label>
								<input type="text" class="form-control form-control-sm" name="bank_account_no" value="" id="bank_account_no"/>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">Traces PAN Name</label>
								<div class="form-group">
									<input type="text" class="form-control form-control-sm" name="traces_name" value="" id="traces_name" readonly="true" />
								</div>
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">PAN No</label>
								<input type="text" class="form-control form-control-sm" name="pan_no" value="" id="pan_no"/>
							</div>
						</div>

						<div class="col-md-2 d-none">
							<div class="form-group">
								<label class="control-label">Verified</label>
								<input type="checkbox" class="form-control form-control-sm Text" name="pan_no_verified" value="1" /> Yes
							</div>
						</div>
					</div>

					<div class="form-group">
						<label class="control-label">Remarks</label>
						<textarea class="form-control form-control-sm" name="remarks" rows="2" cols="50" id="remarks"></textarea>
					</div>
				</fieldset>
			</div>
			
			<div class="col-md-3">
				<fieldset>
					<legend><div class="row">
						<div class="col-md-8">Photo</div>
						<div class="col-md-4"><div class="btn-group pull-right">
							<a href="#modal-photo" data-toggle="modal" class="btn btn-sm btn-success"><i class="fa fa-plus"></i></a>
							<a href="#modal-del-photo" data-toggle="modal" class="btn btn-sm btn-danger"><i class="icon-minus"></i></button></a></div>
						</div>
					</div></legend>
					<img src="<?php  ?>" alt="Staff Image" width="150" /><br />&nbsp;<br />
				</fieldset>

				<fieldset>
					<table class="table table-condensed table-striped">
					<thead>
					<tr>
						<th>Documents</th>
					</tr>
					</thead>

					<tbody>
					<?php
						foreach ($documents as $d) {
							echo '<tr>';
							if (strlen($d['file']) > 0)
								echo '<td>' . anchor($this->_clspath.'staff_document/index/'.$d['staff_id'].'/'.$d['id'], '<span class="green">' . $d['name'] . '</span>', 'class="Popup"') . '</td>';
							else
								echo '<td><a href="#" onclick="javascript: uploadDocument(' . $d['id'] . ', ' . $d['staff_document_type_id'] . ')"><span class="red">' . $d['name'] . '</span></a></td>';
						echo '</tr>';
						}
					?>
					</tbody>
					</table>
				</fieldset>
				<br />

				<fieldset>
					<table class="table table-condensed table-striped DataEntry">
					<thead>
					<tr>
						<th>Assets</th>
						<th width="24px" class="aligncenter"><a href="javascript: CheckAll()"><i class="icon-arrow-forward"></i></a></th>
					</tr>
					</thead>

					<tbody>
					<?php
						foreach ($resources as $r) {
							echo '<tr>
							<td>' . $r['type'] . ' - ' . $r['model_no'] . '</td>
							<td class="aligncenter">' . form_checkbox(array('name' => 'delete_id['.$r['id'].']', 'value' => $r['id'], 'checked' => false, 'class' => 'DeleteCheckbox', 'data-placement' => 'left', 'rel' => 'tooltip', 'data-original-title'=>'Selected Items will be returned after Update...')) . '</td>
						</tr>';
						}
					?>

					<tr class="TemplateRow">
						<td><input type="hidden" name="new_resource_id[]" value="" />
							<input type="text" class="form-control form-control-sm ResourceModel Validate Focus" value="" /></td>
						<td><button type="submit" class="btn btn-success btn-sm AddButton"><i class="fa fa-plus"></i></button></td>
					</tr>
					</tbody>
					</table>
				</fieldset>
			</div>
		</div>		
	</div>

	<div class="card-footer">
		<div class="row">
			<div class="col-lg-12">
				<div class="float-right pt-2">
					<button type="button" class="btn btn-success" id="Add" onclick="updateStaff('0')"><i class="fa fa-plus"></i> Add</button>
					<button type="button" class="btn btn-info" id="Update"><i class="fa fa-save"></i> Update</button>
					<button class="btn btn-danger" id="Delete"><i class="fa fa-trash-alt"></i> Clear</button>
				</div>
			</div>
		</div>
	</div>	
</div>
</form>



<script>

	$(function() {
	    updateStaff('0');
	});
	function uploadDocument(did, dtid) {
		$("#StaffDocumentID").val(did);
		$("#StaffDocumentTypeID").val(dtid);
		$("#modal-document").modal();
	}

	$(document).ready(function() {
		$('#ajaxCity').kaabar_autocomplete_full({source: '<?php echo site_url($this->_clspath.$this->_class.'/json/staffs/id/city_id') ?>'});

		$('#staffSelect2').select2({
				ajax: { 
					url: '<?php echo site_url($this->_clspath.$this->_class) ?>/staffList/',
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
		    	var staff_id = $(this).val();
		    	// $('#updateParty').html();
		    	updateStaff(staff_id);

		    });

		$("#ajaxDesignation").autocomplete({
			source: '<?php echo site_url($this->_clspath.$this->_class.'/ajaxJson/staffs/designation') ?>',
			minLength: 0
		});

		$("#ajaxCategory").autocomplete({
			source: '<?php echo site_url($this->_clspath.$this->_class.'/ajaxJson/staffs/category') ?>',
			minLength: 0
		});

		$("#ajaxLocation").autocomplete({
			source: '<?php echo site_url($this->_clspath.$this->_class.'/ajaxJson/staffs/location') ?>',
			minLength: 0
		});

		$("#ReportsTo").autocomplete({
			source: '<?php echo site_url($this->_clspath.$this->_class.'/ajaxParent') ?>',
			minLength: 1,
			focus: function(event, ui) {
				$("#ReportsTo").val(ui.item.designation + ' - ' + ui.item.name);
				return false;
			},
			select: function(event, ui) {
				$("#ReportsTo").val(ui.item.designation + ' - ' + ui.item.name);
				$("#ParentID").val(ui.item.id);
				return false;
			},
			response: function(event, ui) {
	            if (ui.content.length === 0) {
	                $("#ReportsTo").val('');
					$("#ParentID").val(0);
	            }
	        }
		})
		.data('ui-autocomplete')._renderItem = function(ul, item) {
			return $('<li></li>')
				.data('item.autocomplete', item)
				.append('<a><span class="orange">' + item.designation + '</span> ' + item.name + '</a>')
				.appendTo(ul);
		};

		$("#BankBranch").autocomplete({
			source: '<?php echo site_url($this->_clspath.$this->_class.'/ajaxBanks') ?>',
			minLength: 1,
			focus: function(event, ui) {
				$("#BankBranch").val(ui.item.name + ' - ' + ui.item.ifsc);
				return false;
			},
			select: function(event, ui) {
				$("#BankBranch").val(ui.item.name + ' - ' + ui.item.ifsc);
				$("#BankBranchID").val(ui.item.id);
				return false;
			}
		})
		.data('ui-autocomplete')._renderItem = function(ul, item) {
			return $('<li></li>')
				.data('item.autocomplete', item)
				.append('<a>' + item.name + ' <span class="orange">' + item.ifsc + '</span> <span class="blueDark">' + item.branch + '</span><br /><span class="tiny">' + item.address + '</span></a>')
				.appendTo(ul);
		};

		$('.DataEntry').on('keydown.autocomplete', ".ResourceModel", function(event, items) {
			id = $(this).prevAll('input');
			$(this).autocomplete({
				source: '<?php echo site_url($this->_clspath.$this->_class.'/ajaxResource') ?>',
				minLength: 1,
				focus: function(event, ui) {
					$(this).val(ui.item.type + ' - ' + ui.item.model_no);
					return false;
				},
				select: function(event, ui) {
					$(id).val(ui.item.id);
					$(this).val(ui.item.type + ' - ' + ui.item.model_no);
					return false;
				},
				response: function(event, ui) {
					if (ui.content.length === 0) {
						$(id).val(0);
						$(this).val('');
					}
				}
			})
			.data('ui-autocomplete')._renderItem = function(ul, item) {
				return $('<li></li>')
				.data('item.autocomplete', item)
				.append('<a><span class="orange">' + item.type + '</span> ' + item.model_no + '</a>')
				.appendTo(ul);
			};
		});
	});

	function updateStaff(id = null)
    {   
    	if(id){
        	$.ajax({
                url: '<?php echo base_url($this->_clspath.$this->_class) ?>/getStaff/'+id,
                type: 'post',
                dataType: 'json',
                beforeSend: function(){
		            $("#loader").show();
		        },
                success:function(response) {

                	if(response.success == false){
                		$("#loader").hide();
                		$('#MainForm')[0].reset();
                		$("input[name=id]").attr('value', '0');
   						
					}
                	else
                	{
                		$('#MainForm')[0].reset();
	                	
	                	$("input[name=id]").val(response.row.id);
	            		$("#title").val(response.row.title);
	            		$("#firstname").val(response.row.firstname);
	            		$("#middlename").val(response.row.middlename);
	            		$("#lastname").val(response.row.lastname);
	            		$("#gender").val(response.row.gender);
	            		$("#dob").val(response.row.dob);
	            		$("#designation").val(response.row.designation);
	            		$("#category").val(response.row.category);
	            		$("#status").val(response.row.status);
	            		$("#date_joined").val(response.row.date_joined);
	            		$("#date_left").val(response.row.date_left);
	            		$("#location").val(response.row.location);
	            		$("#company_id").val(response.row.company_id);
	            		$("#user_id").val(response.row.user_id);
	            		$("#permission").val(response.row.permission);
	            		$("#reports_to").val(response.row.reports_to);
	            		$("#address").val(response.row.address);
	            		$("#ajaxCity").val(response.row.city_id);
	            		$("#contact").val(response.row.contact);
	            		$("#email").val(response.row.email);
	            		$("#bank_branch_id").val(response.row.bank_branch_id);
	            		$("#bank_account_no").val(response.row.bank_account_no);
	            		$("#traces_name").val(response.row.traces_name);
	            		$("#pan_no").val(response.row.pan_no);
	            		$("#remarks").val(response.row.remarks);

	            		$("#loader").hide();
	            	}

				    $('#MainForm').unbind('submit').bind('submit', function(e) {

			        	e.preventDefault();
    					
                    	var formData = new FormData(this);
                    	// remove the text-danger
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
</script>