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
	 	<fieldset>
			<div class="form-group">
				<label class="control-label">User ID</label>
				<input type="text" class="form-control form-control-sm Text big input-normal" name="username" value="" id="Username" />
			</div>

			<div class="form-group">
				<label class="control-label">Password</label>
				<input type="password" class="form-control form-control-sm Text big input-normal" name="j_password" value="" />
			</div>

			<div class="form-group">
				<label class="control-label">TAN for Deductor / PAN for Tax Payer AIN for PAO</label>
				<input type="text" class="form-control form-control-sm Text big input-normal" name="j_tanPan" value="" />
			</div>

			<div class="form-group">
				<label class="control-label">Captcha</label>
				<input type="text" class="form-control form-control-sm Text big input-normal" name="j_captcha" value="" />
				<img src="<?php echo base_url('tmp/'.$image) ?>" />
			</div>

			<div class="alert">After clicking on Fetch All, this window will close automatically once all the parties are fetched.</div>
		</fieldset>
	</div>

	<div class="card-footer">
		<button type="button" class="btn btn-success btn-large" id="Update">Fetch All</button>
	</div>
</div> 

</form>