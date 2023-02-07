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
			<div class="col-md-9">
				<fieldset class="inputs">
					<div class="row">
						<div class="col-md-2">
							<div class="form-group">
								<label class="control-label">Title</label>
								<?php echo form_dropdown('title', getEnumSetOptions($this->_table, 'title'), $row['title'], 'class="form-control form-control-sm" id="Title"'); ?>
							</div>
						</div>

						<div class="col-md-3">
							<div class="form-group<?php echo (strlen(form_error('firstname')) > 0 ? ' has-error' : '') ?>">
								<label class="control-label">First Name</label>								
									<input type="text" class="form-control form-control-sm big" name="firstname" value="<?php echo $row['firstname'] ?>" />
								
							</div>
						</div>

						<div class="col-md-3">
							<div class="form-group<?php echo (strlen(form_error('middlename')) > 0 ? ' has-error' : '') ?>">
								<label class="control-label">Middle Name</label>								
									<input type="text" class="form-control form-control-sm big" name="middlename" value="<?php echo $row['middlename'] ?>" />
								
							</div>
						</div>

						<div class="col-md-4">
							<div class="form-group<?php echo (strlen(form_error('lastname')) > 0 ? ' has-error' : '') ?>">
								<label class="control-label">Last Name</label>								
									<input type="text" class="form-control form-control-sm big" name="lastname" value="<?php echo $row['lastname'] ?>" />
								
							</div>
						</div>
					</div>
					
					<div class="row">
						<div class="col-md-2">
								<div class="form-group">
								<label class="control-label">Gender</label>								
									<?php echo form_dropdown('gender', getEnumSetOptions($this->_table, 'gender'), $row['gender'], 'class="form-control form-control-sm"'); ?>
								
							</div>
						</div>

						<div class="col-md-2">
							<div class="form-group<?php echo (strlen(form_error('dob')) > 0 ? ' has-error' : '') ?>">
								<label class="control-label">Date of Birth</label>
								<div class="input-group date DatePicker">
									<span class="input-group-addon"><i class="icon-calendar"></i></span>
									<input type="text" class="form-control form-control-sm AutoDate" name="dob" value="<?php echo $row['dob'] ?>" />
								</div>
							</div>
						</div>

						<div class="col-md-4">
							<div class="form-group<?php echo (strlen(form_error('designation')) > 0 ? ' has-error' : '') ?>">
								<label class="control-label">Designation</label>								
									<input type="text" class="form-control form-control-sm" name="designation" value="<?php echo $row['designation'] ?>" id="ajaxDesignation" />
								
							</div>
						</div>

						<div class="col-md-4">
							<div class="form-group<?php echo (strlen(form_error('category')) > 0 ? ' has-error' : '') ?>">
								<label class="control-label">Category</label>								
									<input type="text" class="form-control form-control-sm" name="category" value="<?php echo $row['category'] ?>" id="ajaxCategory" />
								
							</div>
						</div>
					</div>
					
					<div class="row">
						<div class="col-md-2">
							<div class="form-group">
								<label class="control-label">Status</label>								
								<?php echo form_dropdown('status', getEnumSetOptions($this->_table, 'status'), $row['status'], 'class="form-control form-control-sm"'); ?>
								
							</div>
						</div>
					
						<div class="col-md-2">
							<div class="form-group<?php echo (strlen(form_error('date_joined')) > 0 ? ' has-error' : '') ?>">
								<label class="control-label">Joining Date</label>
								<div class="input-group date DatePicker">
									<span class="input-group-addon"><i class="icon-calendar"></i></span>
									<input type="text" class="form-control form-control-sm AutoDate" name="date_joined" value="<?php echo $row['date_joined'] ?>" />
								</div>
							</div>
						</div>

						<div class="col-md-4">				
							<div class="form-group<?php echo (strlen(form_error('date_left')) > 0 ? ' has-error' : '') ?>">
								<label class="control-label">Leaving Date</label>
								<div class="input-group date DatePicker">
									<span class="input-group-addon"><i class="icon-calendar"></i></span>
									<input type="text" class="form-control form-control-sm AutoDate" name="date_left" value="<?php echo $row['date_left'] ?>" />
								</div>
								</div>
						</div>

						<div class="col-md-4">
							<div class="form-group<?php echo (strlen(form_error('location')) > 0 ? ' has-error' : '') ?>">
								<label class="control-label">Location</label>								
									<input type="text" class="form-control form-control-sm" name="location" value="<?php echo $row['location'] ?>" id="ajaxLocation" />
								
							</div>
						</div>
					</div>
					
					<div class="row">
						<div class="col-md-2">
							<div class="form-group">
								<label class="control-label">Company</label>								
									<?php echo form_dropdown('company_id', getSelectOptions('companies', 'id', 'code'), $row['company_id'], 'class="form-control form-control-sm"') ?>
								
							</div>
						</div>

						<div class="col-md-2">
							<div class="form-group">
								<label class="control-label">Login Username</label>								
									<?php echo form_dropdown('user_id', array(0=>'')+getSelectOptions('users', 'id', 'username', 'WHERE id NOT IN (SELECT user_id FROM staffs WHERE user_id != ' . $row['user_id'] . ')'), $row['user_id'], 'class="form-control form-control-sm"') ?>
								
							</div>
						</div>

						<div class="col-md-4">
							<div class="form-group">
								<label class="control-label">Permission</label>								
									<?php echo form_dropdown('permission[]', getEnumSetOptions($this->_table, 'permission'), explode(',', $row['permission']), 'class="SelectizeKaabar" multiple data-placeholder="Choose permissions..."') ?>
								
							</div>
						</div>

						<div class="col-md-4">
							<div class="form-group">
								<label class="control-label">Reports To</label>
								<input type="hidden" name="parent_id" value="<?php echo $row['parent_id'] ?>" id="ParentID" />
								<input type="text" class="form-control form-control-sm" name="reports_to" value="<?php echo $reports_to ?>" id="ReportsTo" />
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">Address</label>
								<input type="text" class="form-control form-control-sm" name="address" value="<?php echo $row['address'] ?>" />
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">City</label>
								<?php echo form_dropdown('city_id', $this->kaabar->getCities(), $row['city_id'], 'class="form-control form-control-sm"'); ?>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">Contact</label>
								<input type="text" class="form-control form-control-sm" name="contact" value="<?php echo $row['contact'] ?>" />
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">Email</label>
								<input type="text" class="form-control form-control-sm" name="email" value="<?php echo $row['email'] ?>" />
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">Bank Branch</label>
								<input type="hidden" name="bank_branch_id" value="<?php echo $row['bank_branch_id'] ?>" id="BankBranchID" />
								<input type="text" class="form-control form-control-sm" name="bank_branch" value="<?php if ($row['bank_branch_id'] > 0) echo $bank_branch['bank_name'] . ' - ' . $bank_branch['ifsc'] ?>" id="BankBranch" placeholder="IFSC Code or Bank Branch e.g.: ICICI Gandhidham" />
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">Account No</label>
								<input type="text" class="form-control form-control-sm" name="bank_account_no" value="<?php echo $row['bank_account_no'] ?>" />
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">Traces PAN Name</label>
								<div class="form-group<?php echo (strlen(form_error('traces_name')) > 0 ? ' has-error' : '') ?>">
									<input type="text" class="form-control form-control-sm" value="<?php echo $row['traces_name'] ?>" readonly="true" />
								</div>
							</div>
						</div>

						<div class="col-md-4">
							<div class="form-group">
								<label class="control-label">PAN No</label>
								<input type="text" class="form-control form-control-sm" name="pan_no" value="<?php echo $row['pan_no'] ?>" />
							</div>
						</div>

						<div class="col-md-2">
							<div class="form-group">
								<label class="control-label">Verified</label>
								<input type="checkbox" class="form-control form-control-sm Text" name="pan_no_verified" value="1" <?php echo $row['pan_no_verified'] == 'Yes' ? 'checked="true"' : null ?> /> Yes
							</div>
						</div>
					</div>

					<div class="form-group">
						<label class="control-label">Remarks</label>
						<textarea class="form-control form-control-sm" name="remarks" rows="2" cols="50"><?php echo $row['remarks'] ?></textarea>
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
					<img src="<?php echo $photo ?>" alt="Staff Image" width="150" /><br />&nbsp;<br />
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
						<th>Resources</th>
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
		<button type="submit" class="btn btn-success" id="Update">Update</button>
	</div>
</div>
</form>

<script>
function uploadDocument(did, dtid) {
	$("#StaffDocumentID").val(did);
	$("#StaffDocumentTypeID").val(dtid);
	$("#modal-document").modal();
}

$(document).ready(function() {
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
</script>