<?php echo form_open($this->uri->uri_string(), 'id="MainForm"'); ?>

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
				<label class="control-label">Import Ledgers From Company :</label>
				<?php echo form_dropdown('company_id', $companies, 0, 'class="form-control form-control-sm"') ?>
			</div>
			<br />

			<?php if(isset($message)) echo '<div class="alert alert-error">' . $message . '</div>'; ?>

			<h3 id="Progress" style="display: none;">Please wait, Importing Ledgers... <img src="/assets/css/images/loading.gif" id="Loading" /></h3>
		</fieldset>
	</div>

	<div class="card-footer">
		<button type="submit" class="btn btn-success" id="Update">Start Importing</button>
	</div>
</div>

</form>

<script>
$(document).ready(function() {
	$("#Update").addClass(".onEventAttached").one("click", function() {
	    $(this).attr('disabled','disabled');
		$("#Progress").removeClass('hide');
		$("#Loading").removeClass('hide');
	});
});
</script>