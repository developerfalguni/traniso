<?php echo form_open($this->uri->uri_string(), 'class="form-inline"'); ?>
As On: <input type="text" class="DateTime" name="date" value="<?php echo $date ?>" size="10" id="Date" />&nbsp;
Type: <?php echo form_dropdown('type', $this->import->getCargoTypes(), $type); ?>&nbsp;
Highlight Days &gt; : <input type="text" class="form-control form-control-sm Numeric" name="days" value="<?php echo $days ?>" size="10" />&nbsp;
Amount &gt; : <input type="text" class="form-control form-control-sm Numeric" name="amount" value="<?php echo $amount ?>" size="10" />&nbsp;
<input type="submit" class="btn btn-primary" name="Submit" value="Search" id="SearchSubmit" />&nbsp;
<div class="btn-group">
	<?php echo anchor($this->_clspath.$this->_class."/preview", 'Preview', 'class="form-control form-control-sm"') ?>
	<?php echo anchor($this->_clspath.$this->_class."/excel", 'Excel', 'class="btn btn-warning Popup"') ?>
</div>
</form>