<?php
echo form_open($this->uri->uri_string(), 'id="MainForm"');
echo form_hidden($id);
?>

<div class="card card-default">
	<div class="card-header">
		<span class="card--icon"><?php echo anchor($this->_clspath.$this->_class, '<i class="icon-list"></i>') ?></span>
		<span class="card--links"><?php echo anchor($this->_clspath.$this->_class.'/edit/0', '<i class="fa fa-plus"></i> Add', 'class="btn btn-xs btn-success"'); ?></span>
		<h3 class="card-title"><?php echo $page_title ?></h3>
	</div>
	
	<div class="card-body">
		<fieldset>
		<?php
		foreach ($row as $field => $value) :
		if ($field == "id" OR $field == "log_userid" OR $field == "log_ipaddr") continue;
		?>
		<div class="form-group">
			<label class="control-label"><?php echo humanize($field) ?></label>
			<input type="text" class="form-control form-control-sm <?php echo (strlen(form_error($field)) > 0 ? ' has-error' : '') ?>" name="<?php echo $field ?>" value="<?php echo $value ?>" id="<?php echo humanize($field) ?>" />
		</div>
		<?php endforeach; ?>
		</fieldset>
	</div>

	<div class="card-footer">
		<button type="submit" class="btn btn-success" id="Update">Update</button>
	</div>
</div>

<?php if(isset($list)) $this->load->view('list', $list); ?>