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
				<label class="control-group">Name</label>
				<input type="text" class="form-control form-control-sm <?php echo (strlen(form_error('name')) > 0 ? 'error' : '') ?>" name="name" value="<?php echo $row['name'] ?>" size="40" id="Name" />
			</div>
			
			<div class="form-group">
				<label class="control-group">Pincode</label>
				<input type="text" class="form-control form-control-sm" name="pincode" value="<?php echo $row['pincode'] ?>" />
			</div>
			
			<div class="form-group">
				<label class="control-group">State</label>
				<?php echo form_dropdown('state_id', getSelectOptions('states'), $row['state_id'], 'class="SelectizeKaabar"'); ?>
			</div>
		</fieldset>
	</div>

	<div class="card-footer">
		<button type="submit" class="btn btn-success" id="Update">Update</button>
	</div>
</div>

</form>
