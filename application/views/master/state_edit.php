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
		<div class="form-group <?php echo (strlen(form_error('code')) > 0 ? 'has-error' : '') ?>">
			<label class="control-group">Code</label>
			<input type="text" class="form-control form-control-sm <?php echo (strlen(form_error('code')) > 0 ? 'error' : '') ?>" name="code" value="<?php echo $row['code'] ?>" placeholder="Only 2 charactors"/>
		</div>

		<div class="form-group <?php echo (strlen(form_error('name')) > 0 ? 'has-error' : '') ?>">
			<label class="control-group">State Name</label>
			<input type="text" class="form-control form-control-sm <?php echo (strlen(form_error('name')) > 0 ? 'error' : '') ?>" name="name" value="<?php echo $row['name'] ?>" />
		</div>
		
		<div class="form-group">
			<label class="control-label">Union Territory</label>
			<?php echo form_dropdown('union_territory', getEnumSetOptions($this->_table, 'union_territory'), $row['union_territory'], 'class="form-control form-control-sm"');	?>
		</div>
	</div>

	<div class="card-footer">
		<button type="button" class="btn btn-success" id="Update">Update</button>
	</div>
</div>

</form>
