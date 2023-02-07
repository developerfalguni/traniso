
<div id="modal-detach" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><i class="icon-times"></i></button>
				<h3>Confirm Deletion</h3>
			</div>
			<div class="modal-body"><p>Are you sure, you want to DETACH...?</p></div>
			<div class="modal-footer">
				<?php echo anchor("#", 'Detach', 'class="btn btn-danger" id="DetachUrl"') ?>
			</div>
		</div>
	</div>
</div>

<!-- TinyMCE -->
<script type="text/javascript" src="<?php echo base_url('vendor/tinymce/tinymce/tinymce.min.js') ?>"></script>
<script type="text/javascript">
tinymce.init({
	// General options
	selector : "textarea",
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
			<div class="form-group<?php echo (strlen(form_error('name')) > 0 ? ' has-error' : '') ?>">
				<label class="control-label">Name</label>
				<input type="text" class="form-control form-control-sm<?php echo (strlen(form_error('name')) > 0 ? ' error' : '') ?>" name="name" value="<?php echo $row['name'] ?>" />
			</div>

			<div class="form-group">
				<label class="control-label">Template</label>
				<input type="hidden" name="template" id="Template" value="" />
				<textarea type="text" class="form-control form-control-sm" rows="25" cols="80" id="TemplateEditor"><?php echo $row['template'] ?></textarea>
			</div>
		</fieldset>
	</div>

	<div class="card-footer">
		<button type="submit" class="btn btn-success" id="Update">Update</button>
	</div>
</div>

</form>

<script type="text/javascript">
$('form').submit(function(e) {
	e.preventDefault();
	var content = tinymce.get('TemplateEditor').getContent();
	$("#Template").val($.base64.encode(content));
	this.submit();
});
</script>