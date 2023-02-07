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
				<label class="control-label">Company</label>
				<?php echo form_dropdown('company_id', getSelectOptions('companies', 'id', 'CONCAT(code, " - ", name)'), $row['company_id'], 'class="form-control form-control-sm Focus"') ?>				
			</div>

			<div class="form-group">
				<label class="control-label">Category</label>
				<input type="text" class="form-control form-control-sm" name="category" value="<?php echo $row['category'] ?>" size="40" id="Type" />				
			</div>

			<div class="form-group">
				<label class="control-label">Type</label>
				<input type="text" class="form-control form-control-sm" name="type" value="<?php echo $row['type'] ?>" size="40" id="Type" />				
			</div>

			<div class="form-group">
				<label class="control-label">Purchase Date</label>
				<div class="input-group date DatePicker">
					<span class="input-group-addon"><i class="icon-calendar"></i></span>
					<input type="text" class="form-control form-control-sm AutoDate" name="purchase_date" value="<?php echo ($row['purchase_date'] != '00-00-0000' ? $row['purchase_date'] : '') ?>" />
				</div>				
			</div>

			<div class="form-group">
				<label class="control-label">Model No</label>
				<input type="text" class="form-control form-control-sm" name="model_no" value="<?php echo $row['model_no'] ?>" size="40" id="Name" />				
			</div>

			<div class="form-group">
				<label class="control-label"><input type="checkbox" name="active" value="Yes" <?php echo ($row['active'] == 'Yes' ? 'checked="checked"' : '') ?> />	Active</label>			
			</div>
		</fieldset>
	</div>

	<div class="card-footer">
		<button type="submit" class="btn btn-success" id="Update">Update</button>
	</div>
</div>

</form>