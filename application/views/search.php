<?php echo form_open($this->uri->uri_string()); ?>
<span class="bold">From : <input type="text" class="DateTime" name="from_date" value="<?php echo $from_date ?>" size="10" />
To : <input type="text" class="DateTime" name="to_date" value="<?php echo $to_date ?>" size="10" />
<?php echo form_dropdown('search_in', $search_list, $search_in) ?>&nbsp;
<input type="text" name="search" value="<?php echo $search ?>" size="10" id="Search" /></span>
<button type="submit" class="btn btn-primary" id="SearchButton"><i class="fa fa-search"></i> Search</button>&nbsp;
<?php echo anchor($this->_clspath.$this->_class.'/preview/', 'Preview', 'class="form-control form-control-sm"') ?>&nbsp;
<?php echo anchor($this->_clspath.$this->_class.'/excel', 'Excel', 'class="btn btn-default"') ?>
</form>