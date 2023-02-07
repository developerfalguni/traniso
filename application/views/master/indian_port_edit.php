
<div id="modal-delete" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><i class="icon-times"></i></button>
				<h3>Confirm Deletion</h3>
			</div>
			<div class="modal-body"><p>Are you sure, you want to DELETE...?</p></div>
			<div class="modal-footer">
				<?php echo anchor($this->_clspath.$this->_class.'/delete/'.$id['id'], 'Delete', 'class="btn btn-danger"') ?>
			</div>
		</div>
	</div>
</div>

<?php
echo form_open($this->uri->uri_string(), array('name' => 'EditForm'));
echo form_hidden($id);
?>
<div class="row">
	<div class="col-md-10">
		<fieldset>
			<legend>Port Information</legend>
			<div class="clearfix<?php echo (strlen(form_error('code')) > 0 ? ' has-error' : '') ?>">
			<label>Code</label>
			<div class="input">
			<input type="text" name="code" value="<?php echo $row['code']) ?>" size="5" maxlength="5" class="col-md-2 <?php echo (strlen(form_error('code')) > 0 ? 'error' : '' ?>" />
			</div>
			</div><!-- /clearfix -->

			<div class="clearfix<?php echo (strlen(form_error('unece_code')) > 0 ? ' has-error' : '') ?>">
			<label>UNECE Code</label>
			<div class="input">
			<input type="text" name="unece_code" value="<?php echo $row['unece_code']) ?>" size="5" maxlength="5" class="col-md-2 <?php echo (strlen(form_error('code')) > 0 ? 'error' : '' ?>" />
			</div>
			</div><!-- /clearfix -->
			
			<div class="clearfix<?php echo (strlen(form_error('name')) > 0 ? ' has-error' : '') ?>">
			<label>Name</label>
			<div class="input">
			<input type="text" name="name" value="<?php echo $row['name']) ?>" class="col-md-7 <?php echo (strlen(form_error('name')) > 0 ? 'error' : '' ?>" />
			</div>
			</div><!-- /clearfix -->
			
		</fieldset>
		<div class="form-actions">
			<button type="submit" class="btn btn-success" id="Update">Update</button>
		</div>
	</div>
</div>
</form>

<script type="text/javascript" language="JavaScript">
<!--
function delLogo() {
	var res = confirm("Do you want to delete Port... ?");
	if(res) {
		window.location = "<?php echo site_url($this->_clspath.$this->_class.'/delete/'.$id['id']) ?>";
	}
}
// -->
</script>