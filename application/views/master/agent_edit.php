<!-- <div id="modal-delete" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><i class="icon-times"></i></button>
				<h3>Confirm Deletion</h3>
			</div>
			<div class="modal-body"><p>Are you sure, you want to DELETE...?</p></div>
			<div class="modal-footer">
			<?php echo anchor($this->_clspath.$this->_class."/delete/".$id['id'], 'Delete', 'class="btn btn-danger"') ?>
			</div>
		</div>
	</div>
</div> -->

<div class="modal fade" id="modal-delete">
	<div class="modal-dialog">
		<div class="modal-content">
	    	<div class="modal-header bg-danger">
	      		<h5 class="modal-title">Delete Confirmation</h5>
	      		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
	        		<span aria-hidden="true">&times;</span>
	      		</button>
	    	</div>
	    	<div class="modal-body">
	      		<p>One fine body&hellip;</p>
	    	</div>
	    	<div class="modal-footer justify-content-between">
	      		<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	      		<?php echo anchor($this->_clspath.$this->_class."/delete/".$id['id'], 'Delete', 'class="btn btn-danger"') ?>
	    	</div>
	  	</div>
	  	<!-- /.modal-content -->
	</div>
<!-- /.modal-dialog -->
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
			<div class="form-group">
				<label class="control-label">Agent Type</label>
				<div class="form-group<?php echo (strlen(form_error('type')) > 0 ? ' has-error' : '') ?>">
					<?php echo form_dropdown('type[]', getEnumSetOptions('agents', 'type'), explode(',', $row['type']), 'multiple class="form-control form-control-sm Selectize"') ?>
				</div>
			</div>

			<div class="row">
				<div class="col-md-2">
					<div class="form-group<?php echo (strlen(form_error('code')) > 0 ? ' has-error' : '') ?>">
						<label class="control-label">Code</label>
						<input type="text" class="form-control form-control-sm" name="code" value="<?php echo $row['code'] ?>" id="Code" />
					</div>
				</div>

				<div class="col-md-6">
					<div class="form-group<?php echo (strlen(form_error('name')) > 0 ? ' has-error' : '') ?>">
						<label class="control-label">Name</label>
						<input type="text" class="form-control form-control-sm" name="name" value="<?php echo $row['name'] ?>" id="Name" />
					</div>
				</div>

				<div class="col-md-4">
					<div class="form-group<?php echo (strlen(form_error('traces_name')) > 0 ? ' has-error' : '') ?>">
						<label class="control-label">Traces PAN Name</label>
						<input type="text" class="form-control form-control-sm" value="<?php echo $row['traces_name'] ?>" readonly="true" />
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-md-8">
					<div class="form-group<?php echo (strlen(form_error('address')) > 0 ? ' has-error' : '') ?>">
						<label class="control-label">Address</label>
						<input type="text" class="form-control form-control-sm" name="address" value="<?php echo $row['address'] ?>" />
					</div>
				</div>

				<div class="col-md-4">			
					<div class="form-group<?php echo (strlen(form_error('city_id')) > 0 ? ' has-error' : '') ?>">
						<label class="control-label">City</label>
						<?php echo form_dropdown('city_id', $this->kaabar->getCities(), $row['city_id'], 'id="CityID" data-placeholder="Choose City Name..."'); ?>
					</div>
				</div>
			</div>
					
			<div class="row">
				<div class="col-md-4">
					<div class="form-group<?php echo (strlen(form_error('person')) > 0 ? ' has-error' : '') ?>">
						<label class="control-label">Contact Person</label>
						<input type="text" class="form-control form-control-sm" name="person" value="<?php echo $row['person'] ?>" />
					</div>
				</div>

				<div class="col-md-4">
					<div class="form-group<?php echo (strlen(form_error('contact')) > 0 ? ' has-error' : '') ?>">
						<label class="control-label">Contact</label>
						<input type="text" class="form-control form-control-sm" name="contact" value="<?php echo $row['contact'] ?>" />
					</div>
				</div>

				<div class="col-md-4">
					<div class="form-group<?php echo (strlen(form_error('fax')) > 0 ? ' has-error' : '') ?>">
						<label class="control-label">Fax</label>
						<input type="text" class="form-control form-control-sm" name="fax" value="<?php echo $row['fax'] ?>" />
					</div>
				</div>
			</div>

			<div class="form-group<?php echo (strlen(form_error('email')) > 0 ? ' has-error' : '') ?>">
				<label class="control-label">Email</label>
				<input type="text" class="form-control form-control-sm" name="email" value="<?php echo $row['email'] ?>" />
			</div>

			<div class="row">
				<div class="col-md-2">
					<div class="form-group<?php echo (strlen(form_error('pan_no')) > 0 ? ' has-error' : '') ?>">
						<label class="control-label">PAN No</label>
						<input type="text" class="form-control form-control-sm" name="pan_no" value="<?php echo $row['pan_no'] ?>" />
					</div>
				</div>

				<div class="col-md-1">
					<div class="form-group">
						<label class="control-label">Verified</label>
						<input type="checkbox" class="form-control form-control-sm Text" name="pan_no_verified" value="1" <?php echo $row['pan_no_verified'] == 'Yes' ? 'checked="true"' : null ?> /> Yes
					</div>
				</div>

				<div class="col-md-2">
					<div class="form-group<?php echo (strlen(form_error('tan_no')) > 0 ? ' has-error' : '') ?>">
						<label class="control-label">TAN No</label>
						<input type="text" class="form-control form-control-sm" name="tan_no" value="<?php echo $row['tan_no'] ?>" />
					</div>
				</div>

				<div class="col-md-1">
					<div class="form-group">
						<label class="control-label">Verified</label>
						<input type="checkbox" class="form-control form-control-sm Text" name="tan_no_verified" value="1" <?php echo $row['tan_no_verified'] == 'Yes' ? 'checked="true"' : null ?> /> Yes
					</div>
				</div>

				<div class="col-md-2">
					<div class="form-group<?php echo (strlen(form_error('gst_no')) > 0 ? ' has-error' : '') ?>">
						<label class="control-label">GST No</label>
						<input type="text" class="form-control form-control-sm" name="gst_no" value="<?php echo $row['gst_no'] ?>" id="gst_no" />
					</div>
				</div>

				<div class="col-md-4">
					<div class="form-group<?php echo (strlen(form_error('cha_no')) > 0 ? ' has-error' : '') ?>">
						<label class="control-label">CHA No</label>
						<input type="text" class="form-control form-control-sm" name="cha_no" value="<?php echo $row['cha_no'] ?>" />
					</div>
				</div>
			</div>

			<div class="form-group<?php echo (strlen(form_error('remarks')) > 0 ? ' has-error' : '') ?>">
				<label class="control-label">Remarks</label>
				<textarea class="form-control form-control-sm" name="remarks" rows="2"><?php echo $row['remarks'] ?></textarea>
			</div>
		</fieldset>		
	</div>

	<div class="card-footer">
		<button type="submit" class="btn btn-success" id="Update">Update</button>
		<a href="#modal-delete" data-toggle="modal" class="btn btn-danger pull-right">Delete</a>
	</div>
</div>
</form>

