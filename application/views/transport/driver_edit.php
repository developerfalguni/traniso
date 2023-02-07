
<div id="modal-photo" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
		<?php echo form_open_multipart($this->_clspath.$this->_class.'/attach_photo/'.$id['id'], array('id' => 'ImageForm')); ?>
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
				<?php echo anchor($this->_clspath.$this->_class.'/detach_photo/'.$id['id'], 'Delete', 'class="btn btn-danger"') ?>
			</div>
		</div>
	</div>
</div>

<div id="modal-document" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
		<?php echo form_open_multipart($this->_clspath.$this->_class.'/attach_document/'.$id['id'], 'id="LicenseForm"'); ?>
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><i class="icon-times"></i></button>
				<h3>Attach License</h3>
			</div>
			<div class="modal-body">
				<div class="form-group">
					<label class="control-label">Document Name</label>
					<input type="text" class="form-control form-control-sm" name="name" value="" id="ajaxDocumentName" />
				</div>

				<div class="form-group">
					<label class="control-label">Document File</label>
					<input type="file" name="userfile" size="40" />
				</div>
			</div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-success">Upload</button>
			</div>
		</form>
		</div>
	</div>
</div>

<div id="modal-document-delete" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><i class="icon-times"></i></button>
				<h3>Confirm Deletion</h3>
			</div>
			<div class="modal-body"><p>Are you sure, you want to DELETE...?</p></div>
			<div class="modal-footer">
				<?php echo anchor("#", 'Delete', 'class="btn btn-danger" id="DeleteUrl"') ?>
			</div>
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
		<fieldset>
			<div class="row">
				<div class="col-md-8">
					<div class="form-group<?php echo (strlen(form_error('name')) > 0 ? ' has-error' : '') ?>">
						<label class="control-label">Name</label>
						<input type="text" class="form-control form-control-sm Focus" name="name" value="<?php echo $row['name'] ?>" />
					</div>

					<div class="form-group">
						<label class="control-label">Address</label>
						<input type="text" class="form-control form-control-sm" name="address" value="<?php echo $row['address'] ?>" />
					</div>

					<div class="form-group">
						<label class="control-label">Contact</label>
						<input type="text" class="form-control form-control-sm" name="contact" value="<?php echo $row['contact'] ?>" />
					</div>

					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								<label class="control-label">License No</label>
								<input type="text" class="form-control form-control-sm" name="license_no" value="<?php echo $row['license_no'] ?>" />
							</div>
						</div>

						<div class="col-md-2">
							<div class="form-group">
								<label class="control-label">Issue Date</label>
								<div class="input-group date DatePicker">
								<span class="input-group-addon"><i class="icon-calendar"></i></span>
								<input type="text" class="form-control form-control-sm AutoDate" name="license_issue_date" value="<?php echo $row['license_issue_date'] ?>" />
							</div>
							</div>
						</div>

						<div class="col-md-2">
							<div class="form-group">
								<label class="control-label">Expiry Date</label>
								<div class="input-group date DatePicker">
								<span class="input-group-addon"><i class="icon-calendar"></i></span>
								<input type="text" class="form-control form-control-sm AutoDate" name="license_expiry_date" value="<?php echo $row['license_expiry_date'] ?>" />
							</div>
							</div>
						</div>

						<div class="col-md-4">
							<div class="form-group">
								<label class="control-label">Issue State</label>
								<?php echo form_dropdown('license_state_id', getSelectOptions('states'), $row['license_state_id'], 'class="SelectizeKaabar"'); ?>
							</div>
						</div>
					</div>
				</div>

				<div class="col-md-4">
					<fieldset>
						<legend><div class="row">
							<div class="col-md-8">Photo</div>
							<div class="col-md-4"><div class="btn-group pull-right">
								<a href="#modal-photo" data-toggle="modal" class="btn btn-sm btn-success"><i class="fa fa-plus"></i></a>
								<a href="#modal-del-photo" data-toggle="modal" class="btn btn-sm btn-danger"><i class="icon-minus"></i></button></a></div>
							</div>
						</div></legend>
						<img src="<?php echo $photo ?>" alt="Driver Image" width="150" /><br />&nbsp;<br />
					</fieldset>

					<?php 
					if ($id['id'] > 0) :
					echo start_panel_tabs('<ul class="nav nav-tabs">
						<li class="active"><a href="#License" data-toggle="tab">Documents</a></li>
						</ul>', '', 'nopadding');
					?>

					<div class="tab-content">
						<div class="tab-pane active" id="License">
							<table class="table table-condensed table-striped">
							<tbody>
							<?php
								foreach($documents as $doc) {
									echo '<tr>
										<td>' . anchor($document_url.$doc['file'], $doc['name'], 'class="Popup"') . '</td>
										<td><a href="javascript: deleteDocument(\'' . $doc['id'] . '\', \'' . $doc['file'] . '\')" class="btn btn-danger btn-sm"><i class="icon-trash"></i></a></td>
									</tr>';
								}
							?>
							<tr>
								<td>Add New License</td>
								<td width="24px" class="aligncenter"><button type="button" class="btn btn-sm btn-success AddDocument"><i class="icon-paperclip"></i></button></td>
							</tr>
							</tbody>
							</table>
						</div>
					</div>
					<?php 
					echo end_panel(); 
					endif;
					?>
					
				</div>
			</div>
		</fieldset>
	</div>

	<div class="card-footer">
		<button type="submit" class="btn btn-success" id="Update">Update</button>
	</div>
</div>

</form>

<script>
function deleteDocument(id, file) {
	$("a#DeleteUrl").attr("href", '<?php echo base_url($this->_clspath.$this->_class.'/detach_document/'.$id['id']) ?>/'+id+'/'+file);
	$("#modal-document-delete").modal();
}

$(document).ready(function() {
	$('.AddDocument').on('click', function() {
		var uid = $(this).attr('uid');
		$("#LicenseForm").attr('action', '<?php echo site_url($this->_clspath.$this->_class.'/attach_document/'.$id['id']) ?>');
		$("#modal-document").modal('show');
	});

});
</script>