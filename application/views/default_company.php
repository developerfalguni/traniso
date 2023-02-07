<?php echo form_open($this->uri->uri_string(), 'class="form-horizontal"') ?>

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
				<label class="control-label col-md-2">Select Company :</label>
				<div class="col-md-10">
					<?php echo form_dropdown('company_id', $companies, $company_id, 'class="form-control form-control-sm"') ?>
				</div>
			</div>

			<div class="form-group">
				<label class="control-label col-md-2">Select Financial Year :</label>
				<div class="col-md-10">
					<?php echo form_dropdown('financial_year', $years, $financial_year, 'class="form-control form-control-sm"') ?>
				</div>
			</div>
		</fieldset>
	</div>	

	<div class="card-footer">
		<button type="submit" class="btn btn-success" id="Update">Change</button>
	</div>
</div>

<form>