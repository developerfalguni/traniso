<div id="modal-deleteall" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
		<?php echo form_open($this->_clspath.$this->_class.'/deleteContainer/' . $row['job_id']); ?>
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><i class="icon-times"></i></button>
				<h3>Delete All Containers</h3>
			</div>
			<div class="modal-body">
				<p>Are you sure you want to DELETE ALL CONTAINERS...?</p>
				</div>
				<div class="modal-footer">
				<button type="submit" class="btn btn-danger">Delete</button>
			</div>
		</form>
		</div>
	</div>
</div>

<div id="modal-delete" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
		<?php echo form_open($this->_clspath.$this->_class.'/deleteContainer/' . $row['job_id'] . '/' . $row['id']); ?>
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><i class="icon-times"></i></button>
				<h3>Delete Container</h3>
			</div>
			<div class="modal-body">
				<p>Are you sure you want to DELETE This Container...?</p>
				</div>
				<div class="modal-footer">
				<button type="submit" class="btn btn-danger">Delete</button>
			</div>
		</form>
		</div>
	</div>
</div>


<?php
echo form_open($this->uri->uri_string(), 'id="MainForm"');
echo form_hidden($id);
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
		<div class="form-group<?php echo (strlen(form_error('container_type_id')) > 0 ? ' has-error' : '') ?>">
			<label class="control-label">Container Type</label>
			
				<?php echo form_dropdown('container_type_id', getSelectOptions('container_types', 'id', 'CONCAT(size, " ", code, " - ", name)'), $row['container_type_id'], 'class="form-control form-control-sm"') ?>
			
		</div>

	<?php if ($row['id'] > 0) : ?>
		<div class="row">
			<div class="col-md-4">
				<div class="form-group<?php echo (strlen(form_error('number')) > 0 ? ' has-error' : '') ?>">
					<label class="control-label">Number</label>
					
						<input type="text" class="form-control form-control-sm Text big input-medium" name="number" value="<?php echo $row['number'] ?>" />
					
				</div>
			</div>

			<div class="col-md-4">
				<div class="form-group<?php echo (strlen(form_error('seal_no')) > 0 ? ' has-error' : '') ?>">
					<label class="control-label">Seal No</label>
					
						<input type="text" class="form-control form-control-sm Text big input-medium" name="seal" value="<?php echo $row['seal'] ?>" />
					
				</div>
			</div>
		</div>
	</div>

	<div class="card-footer">
		<button type="submit" class="btn btn-success" id="Update">Update</button>
		<div class="pull-right">
			<a href="#modal-delete" data-toggle="modal" class="btn btn-danger">Delete</a>
			<a href="#modal-deleteall" data-toggle="modal" class="btn btn-danger">Delete All</a>
		</div>
	</div>

<?php else : ?>

	<div class="form-group">
		<label class="control-label">Container No &lt;space&gt; Seal No</label>
		<textarea class="form-control form-control-sm" name="number_seal" rows="10"></textarea>
		<span class="help-block">Enter One Container / Seal No per line.</span>
	</div>

	<div class="card-footer">
		<button type="submit" class="btn btn-success" id="Update">Update</button>
	</div>

<?php endif; ?>
</div>

</form>
