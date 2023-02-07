<div class="row">
	 <div class="col-lg-2 col-md-3 col-sm-4">
		<div class="list-group">
			<a href="#" class="list-group-item disabled"><h5>IMPORT</h5></a>
			<?php echo anchor('import/jobs', 'Jobs Master', 'class="list-group-item"'); ?>
			<?php echo anchor('import/attach_document', 'Attach Documents', 'class="list-group-item"'); ?>
			<?php echo anchor('import/pending/index/Container', 'Container Duty List', 'class="list-group-item"'); ?>
			<?php echo anchor('import/pending/index/Tracking', 'Container Pending List', 'class="list-group-item"'); ?>
			<a href="#" class="list-group-item disabled"><h5>EXPORT</h5></a>
			<?php echo anchor('export/booking', 'Bookings', 'class="list-group-item"'); ?>
			<?php echo anchor('export/jobs', 'Jobs Master', 'class="list-group-item"'); ?>
			<?php echo anchor('export/attach_document', 'Attach Documents', 'class="list-group-item"'); ?>
			<?php echo anchor('export/pending/index/Container', 'Shipment Pending List', 'class="list-group-item"'); ?>
		</div>
	</div>

	<div class="col-lg-10 col-md-9 col-sm-8">
		<img src="<?php echo base_url('/images/logo.png') ?>" />
	</div>
</div>