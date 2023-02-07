<div id="modal-photo" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
		<?php echo form_open_multipart($this->_clspath.$this->_class.'/photoadd/'.$id['id'], array('id' => 'PhotoForm')); ?>
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><i class="icon-times"></i></button>
				<h3>Upload Photo</h3>
			</div>
			<div class="modal-body">
				<input type="file" name="userfile" size="40" />
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
		<?php echo form_open_multipart($this->_clspath.$this->_class.'/documentadd/'.$id['id'], array('id' => 'DocumentForm')); ?>
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><i class="icon-times"></i></button>
				<h3>Upload Document</h3>
			</div>
			<div class="modal-body">
				<input type="hidden" name="vehicle_document_id" value="0" id="AddVDID" />
				<input type="file" name="userfile" size="40" />
			</div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-success">Upload</button>
			</div>
		</form>
		</div>
	</div>
</div>

<div id="modal-del-document" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
		<?php echo form_open($this->_clspath.$this->_class.'/documentdel/'.$id['id'], array('id' => 'DocumentForm')); ?>
			<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal"><i class="icon-times"></i></button>
			<h3>Confirm Deletion</h3>
			</div>
			<div class="modal-body">
				<input type="hidden" name="vehicle_document_id" value="0" id="DelVDID" />
				Are you sure, you want to DELETE...?
			</div>
			<div class="modal-footer">
			<input type="submit" value="Delete" class="btn btn-danger" />
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
			<div class="col-md-8">
				<fieldset>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">Type</label>
								<input type="text" class="form-control form-control-sm" name="type" value="<?php echo $row['type'] ?>" id="Type" />
							</div>
						</div>

						<div class="col-md-3">			
							<div class="form-group">
								<label class="control-label">Mfg. Year</label>
								<input type="text" class="form-control form-control-sm Numeric" name="mfg_year" value="<?php echo $row['mfg_year'] ?>" />
							</div>
						</div>

						<div class="col-md-3">			
							<div class="form-group">
								<label class="control-label">Purchase Date</label>
								<div class="input-group date DatePicker">
								<span class="input-group-addon"><i class="icon-calendar"></i></span>
								<input type="text" class="form-control form-control-sm AutoDate" name="purchase_date" value="<?php echo $row['purchase_date'] ?>" />
							</div>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-3">
							<div class="form-group">
								<label class="control-label">Registration No</label>
								<input type="text" class="form-control form-control-sm Text big col-md-12" name="registration_no" value="<?php echo $row['registration_no'] ?>" />
							</div>
						</div>

						<div class="col-md-3">
							<div class="form-group">
								<label class="control-label">Group Name</label>
								<input type="text" class="form-control form-control-sm" name="group_name" value="<?php echo $row['group_name'] ?>" id="GroupName" />
							</div>
						</div>

						<div class="col-md-3">
							<div class="form-group">
								<label class="control-label">Make</label>
								<input type="text" class="form-control form-control-sm" name="make" value="<?php echo $row['make'] ?>" />
							</div>
						</div>

						<div class="col-md-3">
							<div class="form-group">
								<label class="control-label">Model No</label>
								<input type="text" class="form-control form-control-sm" name="model_no" value="<?php echo $row['model_no'] ?>" />
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">Seller Name</label>
								<input type="text" class="form-control form-control-sm" name="seller_name" value="<?php echo $row['seller_name'] ?>" />
							</div>

							<div class="form-group">
								<label class="control-label">Seller Address</label>
								<input type="text" class="form-control form-control-sm" name="seller_address" value="<?php echo $row['seller_address'] ?>" />
							</div>

							<div class="form-group">
								<label class="control-label">Seller Contact</label>
								<input type="text" class="form-control form-control-sm" name="seller_contact" value="<?php echo $row['seller_contact'] ?>" />
							</div>

							<div class="row">
								<div class="col-md-4">
									<div class="form-group">
										<label class="control-label">Track Data</label>
										<input type="checkbox" class="form-control form-control-sm " name="track_data" value="<?php echo $row['track_data'] ?>" <?php echo ($row['track_data'] == 'Yes' ? 'checked="checked"' : null) ?> /> Yes
									</div>
								</div>
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">Dealer Name</label>
								<input type="text" class="form-control form-control-sm" name="dealer_name" value="<?php echo $row['dealer_name'] ?>" />
							</div>

							<div class="form-group">
								<label class="control-label">Dealer Address</label>
								<input type="text" class="form-control form-control-sm" name="dealer_address" value="<?php echo $row['dealer_address'] ?>" />
							</div>

							<div class="form-group">
								<label class="control-label">Dealer Contact</label>
								<input type="text" class="form-control form-control-sm" name="dealer_contact" value="<?php echo $row['dealer_contact'] ?>" />
							</div>

							<div class="form-group">
								<label class="control-label">Dealer Person</label>
								<input type="text" class="form-control form-control-sm" name="dealer_person" value="<?php echo $row['dealer_person'] ?>" />
							</div>
						</div>
					</div>
				</fieldset>

				<fieldset>
					<legend>Documents</legend>
					<table class="table table-condensed table-striped DataEntry">
					<thead>
					<tr>
						<th width="150px">Date</th>
						<th>Document</th>
						<th>Document Details</th>
						<th width="150px">Validity</th>
						<th width="48px">Alarm(/days)</th>
						<th width="24px" class="aligncenter"><i class="icon-paperclip"></i></th>
						<th width="24px" class="aligncenter"><i class="icon-trash"></i></th>
					</tr>
					</thead>

					<tbody>
					<?php
						foreach ($documents as $d) {
							echo '<tr>
						<td>' . $d['date'] . '</td>
						<td>' . $d['name'] . '</td>
						<td>' . $d['document_details'] . '</td>
						<td>' . $d['validity'] . '</td>
						<td>' . $d['alarm'] . '</td>
						<td class="aligncenter">' . (strlen($d['file']) > 0 ? anchor($this->_clspath.$this->_class.'/show/'.$id['id'].'/'.$d['id'], '<i class="icon-paperclip"></i>', 'class="Popup"') : '<a href="#" class="btn btn-warning btn-sm" onclick="javascript: addDocument(' . $d['id'] . ')"><i class="fa fa-plus"></i></a>') . '</td>
						<td><a href="#" class="btn btn-danger btn-sm" onclick="javascript: delDocument(' . $d['id'] . ')"><i class="icon-trash"></i></a></td>
					</tr>';
						}
					?>
					<tr class="TemplateRow">
						<td><div class="input-group date DatePicker"><span class="input-group-addon"><i class="icon-calendar"></i></span><input type="text" class="form-control form-control-sm AutoDate" name="new_date[]" value="" /></div></td>
						<td><input type="text" class="form-control form-control-sm Validate DocumentName" name="new_name[]" value="" /></td>
						<td><input type="text" class="form-control form-control-sm Validate" name="new_document_details[]" value="" /></td>
						<td><div class="input-group date DatePicker"><span class="input-group-addon"><i class="icon-calendar"></i></span><input type="text" class="form-control form-control-sm AutoDate" name="new_validity[]" value="" /></td>
						<td><input type="text" class="form-control form-control-sm Validate" name="new_alarm[]" value="" /></div></td>
						<td></td>
						<td><button type="button" class="btn btn-success btn-sm AddButton"><i class="fa fa-plus"></i></button></td>
					</tr>
					</tbody>
					</table>
				</fieldset>
			</div>

		<?php if ($row['id'] > 0 ) : ?>
			<div class="col-md-4">
				<fieldset>
					<legend><div class="row">
						<div class="col-md-8">Photo</div>
						<div class="col-md-4"><div class="btn-group pull-right">
							<a href="#modal-photo" data-toggle="modal" class="btn btn-sm btn-success"><i class="fa fa-plus"></i></a>
							<a href="#modal-del-photo" data-toggle="modal" class="btn btn-sm btn-danger"><i class="icon-minus"></i></button></a></div>
						</div>
					</div></legend>
				<img src="<?php echo $photo ?>" alt="Vehicle Image" width="280" />
				</fieldset>
			</div>
		<?php endif; ?>

		</div>		
	</div>

	<div class="card-footer">
		<button type="submit" class="btn btn-success" id="Update">Update</button>
	</div>
</div>
</form>

<script>
function addDocument(id) {
	$("#AddVDID").val(id);
	$("#modal-document").modal();
}

function delDocument(id) {
	$("#DelVDID").val(id);
	$("#modal-del-document").modal();
}

$(document).ready(function() {
	$("#GroupName").autocomplete({
		source: "<?php echo site_url($this->_clspath.$this->_class.'/json/'.$this->_table.'/group_name/group_name') ?>",
		minLength: 0
	});

	$("#Type").autocomplete({
		source: "<?php echo site_url($this->_clspath.$this->_class.'/json/'.$this->_table.'/type/type') ?>",
		minLength: 0
	});

	$(".DocumentName").autocomplete({
		source: "<?php echo site_url($this->_clspath.$this->_class.'/json/'.$this->_table2.'/name') ?>",
	});
});
</script>