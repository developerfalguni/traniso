<?php 
if ($formselect['data']) : 
	echo form_open($formselect['url'], 'class="form-horizontal"');
	echo form_hidden($id);
?>

<div class="card card-default">
	<div class="card-header">
		<h3 class="card-title"><?php echo $formselect['title'] ?></h3>
	</div>
	
	<div class="card-body">
		<?php echo form_dropdown($formselect['postvar'], $formselect['data'], null, 'size=20 multiple class="form-control form-control-sm"'); ?>
	</div>

	<div class="card-footer">
		<button type="submit" class="btn btn-success">Add Contents</button>
	</div>
</div>

</form>

<?php endif; ?>