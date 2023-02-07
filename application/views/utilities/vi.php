<?php echo form_open($this->_clspath.$this->_class.'/importJobs'); ?>

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
		<div class="form-group">
			<label class="label-control">Import VisualImpex Records of Financial Year</label>
			<?php echo form_dropdown('financial_year', $financial_year, '', 'class="form-control form-control-sm" id="FinancialYear"') ?>
		</div>

		<h3 id="Progress" class="hide">Please wait, Importing Jobs... <img src="/assets/css/images/loading.gif" id="Loading" /> <span class="green hide" id="Completed">Completed.</span></h3>
	</div>

	<div class="card-footer">
		<button type="button" class="btn btn-success" onclick="javascript: startImport()" id="ImportButton">Import Now</button>
	</div>
</div>

</form>

<script>
function startImport() {
	var fy = $("#FinancialYear").val();
	$("#ImportButton").attr('disabled', true);
	$("#Completed").addClass('hide');
	$("#Progress").removeClass('hide');
	$("#Loading").removeClass('hide');
	$.ajax({
		type: 'POST',
		url:  '<?php echo site_url($this->_clspath.$this->_class) ?>/importJobs/'+fy,
		async:true
	}).done(function(msg) {
		if (msg == 'OK') {
			$("#Loading").addClass('hide');
			$("#Completed").removeClass('hide');
			$("#ImportButton").removeAttr('disabled');
		}
	});
}
</script>