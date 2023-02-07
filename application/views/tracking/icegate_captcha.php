<?php echo form_open($this->uri->uri_string(), 'id="MainForm"') ?>

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
			<label class="control-label"></label>
			<input name="csrfPreventionSalt" value="<?=$csrfPreventionSalt ?>" type="hidden">
			<input type="text" class="form-control form-control-sm input-lg big Focus" name="captchaResp" value="" id="Captcha" /><br />
				<img src="data:image/jpg;base64,<?=$image ?>">
			
		</div>
		<p>After clicking on Fetch All, this window will close automatically once all the jobs are fetched.</p>
		<p>Please Note: For any reason, if fetch stops working, kindly check on ICEGATE site manually</p>

		<div class="none" id="Progress">
			<h4>Please wait, Fetching Status... <img src="/assets/css/images/loading.gif" id="Loading" /></h4>
			<div class="progress">
				<div class="progress-bar progress-bar-striped active" role="progressbar" style="width: 100%;">0 / 0</div>
			</div>
		</div>
	</div>

	<div class="card-footer">
		<button type="button" class="btn btn-success btn-large" id="Fetch">Fetch All</button>
	</div>
</div>

</form>

<script>
$(document).ready(function() {
	$("button#Fetch").on("click", function() {
		$("div#Progress").removeClass('hide');
		$(this).attr('disabled', true);

		fetchProgress();

		form_data = $('#MainForm').serialize();
		$.ajax({
			type: 'POST',
			url: '<?php echo site_url($this->uri->uri_string()) ?>',
			data: form_data,
			//success: closeSelfAndRefreshParent,
			//fail: closeSelfAndRefreshParent,
		});
	});

	function fetchProgress() {
		$.ajax({
			type: "GET",
			url: '<?php echo site_url($this->_clspath.$this->_class."/getProgress") ?>',
			dataType: "json",
			success: function(data) {
				$("div.progress-bar").attr("style", "width: " + data.percent);
				$("div.progress-bar").text(data.progress + ' / ' + data.total);
			}
		});
		//setTimeout(fetchProgress, 5000);
	}

	function closeSelfAndRefreshParent() {
		top.opener.location.reload(true);
		self.close();
	}
});
</script>