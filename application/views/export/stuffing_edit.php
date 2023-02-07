
<div id="modal-delete" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
		<?php echo form_open($this->_clspath.$this->_class.'/deleteStuffing/' . $row['job_id'] . '/' . $row['id']); ?>
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
echo form_open_multipart($this->uri->uri_string(), 'id="MainForm"');
echo form_hidden($id);
?>

<div class="row">
	<div class="col-md-8">
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
					<div class="col-md-6">
						<div class="form-group">
							<label class="control-label">Invoice No</label><br />
							<?php echo form_dropdown('job_invoice_id[]', $invoices, $row['job_invoices'], 'multiple class="Selectize" id="job_invoice_id"') ?>
						</div>
					</div>



					<div class="col-md-3">
						<div class="form-group">
							<label class="control-label">LR No</label>
							<input type="text" class="form-control form-control-sm" name="lr_no" value="<?php echo $row['lr_no'] ?>" id="lr_no" />
						</div>
					</div>

					<div class="col-md-3">
						<div class="form-group">
							<label class="control-label">Vehicle No</label>
							<input type="text" class="form-control form-control-sm" name="vehicle_no" value="<?php echo $row['vehicle_no'] ?>" id="vehicle_no" />
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-6">
						<div class="form-group<?php echo (strlen(form_error('container_no')) > 0 ? ' has-error' : '') ?>">
							<label class="control-label">Container No</label>
							<input type="text" class="form-control form-control-sm Focus big" name="container_no" value="<?php echo $row['container_no'] ?>" id="container_no" />
						</div>
					</div>

					<div class="col-md-6">
						<div class="form-group">
							<label class="control-label">Cont. Size</label>
							<?php echo form_dropdown('container_type_id', getSelectOptions('container_types', 'id', 'CONCAT(size, code)'), $row['container_type_id'], 'class="form-control form-control-sm" id="container_type_id"') ?>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-4">
						<div class="form-group">
							<label class="control-label">Line Seal No</label>
							<input type="text" class="form-control form-control-sm" name="seal_no" value="<?php echo $row['seal_no'] ?>" id="seal_no" />
						</div>
					</div>

					<div class="col-md-4">
						<div class="form-group">
							<label class="control-label">Wire Seal No</label>
							<input type="text" class="form-control form-control-sm" name="wire_seal_no" value="<?php echo $row['wire_seal_no'] ?>" id="wire_seal_no" />
						</div>
					</div>

					<div class="col-md-4">
						<div class="form-group">
							<label class="control-label">Ex/Cu Seal No</label>
							<input type="text" class="form-control form-control-sm" name="excise_seal_no" value="<?php echo $row['excise_seal_no'] ?>" id="excise_seal_no" />
						</div>
					</div>
				</div>

				<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label">Pickup Date</label>
									<div class="input-group date DateTimePicker">
										<span class="input-group-addon"><i class="icon-calendar"></i></span>
										<input type="text" class="form-control form-control-sm AutoDate" name="pickup_date" value="<?php echo $row['pickup_date'] ?>" id="pickup_date" />
									</div>
								</div>
							</div>

							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label">Stuffing Date</label>
									<div class="input-group date DatePicker">
										<span class="input-group-addon"><i class="icon-calendar"></i></span>
										<input type="text" class="form-control form-control-sm AutoDate" name="stuffing_date" value="<?php echo $row['stuffing_date'] ?>" id="stuffing_date" />
									</div>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label">Units</label>
									<input type="text" class="form-control form-control-sm Numeric" name="units" value="<?php echo $row['units'] ?>" id="units" />
								</div>
							</div>

							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label">Unit</label>
									<?php echo form_dropdown('unit_id', getSelectOptions('units', 'id', 'code'), $row['unit_id'], 'class="form-control form-control-sm" id="unit_id"') ?>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label">Gross Weight</label>
									<input type="text" class="form-control form-control-sm Numeric" name="gross_weight" value="<?php echo $row['gross_weight'] ?>" id="gross_weight" />
								</div>
							</div>

							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label">Nett Weight</label>
									<input type="text" class="form-control form-control-sm Numeric" name="nett_weight" value="<?php echo $row['nett_weight'] ?>" id="nett_weight" />
									<!-- 	<input type="hidden" class="form-control form-control-sm Numeric" name="flexi_tank_no[' . $r['id'] . ']" 	value="' . $r['flexi_tank_no'] . '" /> -->
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-12">
								<div class="form-group">
									<label class="control-label">Remarks</label>
									<input type="text" class="form-control form-control-sm" name="remarks" value="<?php echo $row['remarks'] ?>" id="remarks" />
								</div>
							</div>
						</div>
					</div>
		
			<div class="card-footer">
				<button type="submit" class="btn btn-success">Update</button>
				<a href="#modal-delete" data-toggle="modal" class="btn btn-danger">Delete</a>
			</div>
		</div>
	</div>

	<div class="col-md-4">
		<div class="card card-default">
			<div class="card-header">
				<h3 class="card-title">Photos</h3>
			</div>
			
			<table class="table table-condensed table-striped table-bordered DataEntry">
				<tbody>
				<tr class="TemplateRow">
					<td>
						<div class="form-group">
							<label class="control-label">Type</label>
							<?php echo form_dropdown('type[]', [
								'Seal'      => 'Seal', 
								'Door'      => 'Door', 
								'Lashing'   => 'Lashing', 
								'Choking'   => 'Choking', 
								'Damage'    => 'Damage',
								'Weighment' => 'Weighment',
								'VGM'       => 'VGM',
								], 'Seal', 'class="form-control form-control-sm"') ?>
						</div>

						<div class="form-group">
							<label class="control-label">Photo</label>
							<input type="file" name="image[]" value="" />
						</div>
					</td>
					<td class="aligncenter"><button type="submit" class="btn btn-success btn-sm AddButton"><i class="fa fa-plus"></i></button></td>
				</tr>
				</tbody>
				</table>

				<div class="card-body">
					<ul class="list-group" style="margin-left: 0px">
					<?php
						foreach ($photos as $r) {
							echo '<li class="list-group-item">
								<span>' . $r['type'] . '</span>
								<span class="pull-right">' . anchor($this->_clspath.$this->_class.'/deletePhoto/'.$job_id['id'].'/'.$r['id'], '<i class="icon-cancel"></i>', 'class="red"') . '</span><br />
								<img src="' . $path_url.$r['file'] . '" height="100px" />
							</li>';
						}
					?>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>

</form>

<script>
$(document).ready(function() {
	$('#container_no').on('keyup', function() {
		if (ISO6346Check($(this).val())) 
			$(this).removeClass('red');
		else
			$(this).addClass('red');
	});
});
</script>