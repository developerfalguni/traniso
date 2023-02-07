<?php
echo form_open($this->uri->uri_string(), 'class="form-horizontal" id="MainForm"');
$tab_general = '';
$tab_smtp = '';

foreach ($settings as $row) {
	if (substr($row['name'], 0, 4) == 'smtp')
		$tab_smtp .= '<div class="form-group">
			<label class="control-label col-md-2">' . humanize(str_replace('smtp_', '', $row['name'])) . '</label>
			<div class="col-md-10">
				<input type="' . (strstr($row['name'], 'password') ? 'password' : 'text') . '" class="form-control form-control-sm" name="value[' . $row['id'] . ']" value="' . $row['value'] . '" />
			</div>
		</div>';
	else 
		$tab_general .= '<div class="form-group">
			<label class="control-label col-md-2">' . humanize($row['name']) . '</label>
			<div class="col-md-10">' . 
				($row['name'] == 'default_company' ? 
					form_dropdown('value[' . $row['id'] . ']', $companies, $row['value'], 'class="form-control form-control-sm"') : 
					'<input type="' . (strstr($row['name'], 'password') ? 'password' : 'text') . '" class="form-control form-control-sm" name="value[' . $row['id'] . ']" value="' . $row['value'] . '" />') . 
			'</div>
		</div>';
		
}
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
			<div class="tabbable">
				<ul class="nav nav-tabs">
					<li class="active"><a href="#tab1" data-toggle="tab">General</a></li>
					<li><a href="#tab2" data-toggle="tab">SMTP</a></li>
				</ul>

				<div class="tab-content">
					<div class="tab-pane active" id="tab1">
						<?php echo $tab_general ?>
					</div>
					
					<div class="tab-pane" id="tab2">
						<?php echo $tab_smtp ?>
					</div>
				</div>
			</div>
		</fieldset>
		</div>

	<div class="card-footer">
		<button type="submit" class="btn btn-success" id="Update">Update</button>
	</div>
</div>

</form>