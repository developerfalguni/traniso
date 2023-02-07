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
		<span class="aligncenter">
		<p>Click here to <?php echo anchor('main/download', '<span class="label label-info">Download</span>') ?></b> the backup file.<br /><br />
		File Size: (<?php echo $file_size ?>)<br /><br />
		Last Backup was taken <b><?php echo $last_backup ?></b> ago.</p>
		<br />
		<?php
		echo form_open($this->uri->uri_string(), array('name' => 'Backup'));
		if ($next_backup > 0) {
			echo form_submit('Button', 'Wait for '.$next_backup.' minutes for next Backup...', "class='btn disabled' disabled='true'");
		}
		else {
			echo form_submit('Submit', 'Start New Backup', "class='btn btn-success' onclick=''");
		}
		echo form_close();
		?>
		<br />
		<p>While the backup is being done, Kindly wait for a while...</p>
		</span>
	</div>
</div>