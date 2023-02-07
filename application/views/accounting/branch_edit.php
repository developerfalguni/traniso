<!-- TinyMCE -->
<script type="text/javascript" src="/vendor/tinymce/tinymce/tinymce.min.js"></script>
<script type="text/javascript">
tinymce.init({
	// General options
	selector : ".TemplateEditor",
	theme : "modern",
	plugins : [
		"advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker",
		"searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
		"save table contextmenu directionality emoticons template paste textcolor",
	],
	toolbar : [
		"cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,cleanup,help,code,|,forecolor,backcolor",
		"fontselect,fontsizeselect,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,sub,sup,|,fullscreen,pagebreak,|,tablecontrols,|,hr,removeformat,|,template",
	],
	content_css : ["/assets/css/print.css"],
});
</script>
<!-- /TinyMCE -->

<div id="modal-logo" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
		<?php echo form_open_multipart($this->_clspath.$this->_class.'/addLogo/'.$id['id'], array('id' => 'LogoForm')); ?>
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><i class="icon-times"></i></button>
				<h3>Upload Logo</h3>
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

<div id="modal-logo-delete" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><i class="icon-times"></i></button>
				<h3>Confirm Deletion</h3>
			</div>
			<div class="modal-body"><p>Are you sure, you want to DELETE...?</p></div>
			<div class="modal-footer">
				<?php echo anchor($this->_clspath.$this->_class.'/delLogo/'.$id['id'], 'Delete', 'class="btn btn-danger"') ?>
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
		<div class="row">
			<div class="col-md-4">
				<div class="form-group<?php echo (strlen(form_error('company_id')) > 0 ? ' has-error' : '') ?>">
					<label class="control-label">Parent Company</label>
					<?php echo form_dropdown('company_id', getSelectOptions('companies'), $row['company_id'], 'class="form-control form-control-sm Selectize"'); ?>
				</div>
			</div>
			<div class="col-md-2">
				<div class="form-group<?php echo (strlen(form_error('series')) > 0 ? ' has-error' : '') ?>">
					<label class="control-label">Branch Series</label>
					<input type="text" class="form-control form-control-sm" name="series" value="<?php echo $row['series'] ?>" />
				</div>
			</div>
			<div class="col-md-2">
				<div class="form-group<?php echo (strlen(form_error('code')) > 0 ? ' has-error' : '') ?>">
					<label class="control-label">Branch Code</label>
					<input type="text" class="form-control form-control-sm" name="code" value="<?php echo $row['code'] ?>" />
				</div>
			</div>
			<div class="col-md-4">
				<div class="form-group<?php echo (strlen(form_error('name')) > 0 ? ' has-error' : '') ?>">
					<label class="control-label">Branch Name</label>
					<input type="text" class="form-control form-control-sm" name="name" value="<?php echo $row['name'] ?>" />
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-8">
				<div class="form-group">
					<label class="control-label">Address</label>
					<input type="text" class="form-control form-control-sm" name="address" value="<?php echo $row['address'] ?>" />
				</div>
			</div>

			<div class="col-md-4">
				<div class="form-group">
					<label class="control-label">City</label>
					<?php echo form_dropdown('city_id', $this->kaabar->getCities(), $row['city_id'], 'class="form-control form-control-sm Selectize" data-placeholder="Choose City..."'); ?>
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
			<div class="col-md-4">
				<div class="form-group">
					<label class="control-label">Pan No</label>
					<input type="text" class="form-control form-control-sm" name="pan_no" value="<?php echo $row['id'] == 0 ? $current_company['pan_no'] : $row['pan_no']  ?>" />
				</div>
			</div>

			<div class="col-md-4">
				<div class="form-group">
					<label class="control-label">Tan No</label>
					<input type="text" class="form-control form-control-sm" name="tan_no" value="<?php echo $row['id'] == 0 ? $current_company['tan_no'] : $row['tan_no'] ?>" />
				</div>
			</div>
		
			<div class="col-md-4">
				<div class="form-group">
					<label class="control-label">Service Tax No</label>
					<input type="text" class="form-control form-control-sm" name="service_tax_no" value="<?php echo $row['id'] == 0 ? $current_company['service_tax_no'] : $row['service_tax_no'] ?>" />
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-4">
				<div class="form-group">
					<label class="control-label">GST No</label>
					<input type="text" class="form-control form-control-sm" name="gst_no" value="<?php echo $row['id'] == 0 ? $current_company['gst_no'] : $row['gst_no'] ?>" id="gst_no" />
				</div>
			</div>

			<div class="col-md-4">
				<div class="form-group">
					<label class="control-label">CHA No</label>
					<input type="text" class="form-control form-control-sm" name="cha_no" value="<?php echo $row['id'] == 0 ? $current_company['cha_no'] : $row['cha_no'] ?>" />
				</div>
			</div>

			<div class="col-md-4">
				<div class="form-group">
					<label class="control-label">CHA License No</label>
					<input type="text" class="form-control form-control-sm" name="cha_license_no" value="<?php echo $row['id'] == 0 ? $current_company['cha_license_no'] : $row['cha_license_no'] ?>" />
				</div>
			</div>
		</div>
		<div class="form-group<?php echo (strlen(form_error('remarks')) > 0 ? ' has-error' : '') ?>">
			<label class="control-label">Remarks</label>
			<textarea class="form-control form-control-sm" name="remarks" rows="2"><?php echo $row['remarks'] ?></textarea>
		</div>
		

	</div>

	<div class="card-footer">
		<button type="submit" class="btn btn-success" id="Update">Update</button>
	</div>
</div>

</form>
