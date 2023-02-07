
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
		<div class="row">
			<div class="col-md-3">
				<div class="form-group">
					<label class="control-label">Vessel - <?php echo $jobs['terminal_code'] ?></label>
					<h5><?php echo $jobs['vessel_name'] ?></h5>
				</div>
			</div>

			<div class="col-md-6">
				<div class="form-group">
					<label class="control-label">Consignee</label>
					<h5><?php echo $jobs['consignee_name'] ?></h5>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-3">
				<div class="form-group">
					<label class="control-label">Cargo</label>
					<h5><?php echo $jobs['cargo_name'] ?></h5>
				</div>
			</div>

			<div class="col-md-2">
				<div class="form-group">
					<label class="control-label">Packaging</label>
					<h5><?php echo $jobs['unit_code'] ?></h5>
				</div>
			</div>

			<div class="col-md-2">
				<div class="form-group">
					<label class="control-label"><?php echo $jobs['cargo_type'] ?></label>
					<h5><?php echo $jobs['containers'] ?></h5>
				</div>
			</div>
		</div>
	</div>
</div>

<?php $this->load->view($job_page) ?>
