<?php
echo form_open($this->uri->uri_string(), 'id="MainForm"');
echo form_hidden($job_id);
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
		<fieldset>
			<div class="row">
				<div class="col-md-3">
					<div class="form-group">
						<label class="control-label">Party Name</label>
						<h5><?php echo $jobs['party_name'] ?></h5>
					</div>
				</div>

				<div class="col-md-3">
					<div class="form-group">
						<label class="control-label"><?php echo $jobs['stuffing_type'] ?> Stuffing</label>
						<h5><?php echo $jobs['stuffing_place'] ?></h5>
					</div>
				</div>

				<div class="col-md-6">
					<div class="form-group">
						<label class="control-label">Vessel</label>
						<h5><?php echo $jobs['vessel_name'] ?></h5>
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

				<div class="col-md-3">
					<div class="form-group">
						<label class="control-label">Cargo Unit</label>
						<h5><?php echo $jobs['unit_code'] ?></h5>
					</div>
				</div>

				<div class="col-md-2">
					<div class="form-group">
						<label class="control-label"><?php echo $jobs['cargo_type'] ?></label>
						<h5><?php echo $jobs['containers'] ?></h5>
					</div>
				</div>

				<div class="col-md-4">
					<div class="form-group">
						<label class="control-label">FPD</label>
						<h5><?php echo $jobs['fpod'] ?></h5>
					</div>
				</div>
			</div>
			<hr />

			<table class="table table-condensed table-striped">
			<thead>
				<tr>
					<th width="24px">No</th>
					<th>LR No</th>
					<th>Vehicle No</th>
					<th>Driver's Mobile No</th>
					<th>Cont. Type</th>
					<th>Container No</th>
					<th>Line Seal No</th>
					<th width="210px">Office Out Date &amp; Time</th>
					<th>Remarks</th>
				</tr>
			</thead>

			<tbody>
				<?php 
				$i = 1;
				foreach ($rows['stuffing'] as $r) {
					echo '<tr>
						<td class="aligncenter">' . $i++ . '</td>
						<td><input type="text" class="form-control form-control-sm big col-md-12" name="lr_no[' . $r['id'] . ']" value="' . $r['lr_no'] . '" /></td>
						<td><input type="hidden" name="stuffing_date[' . $r['id'] . ']" value="' . substr($r['stuffing_date'], 0, 10) . '" />
							<input type="text" class="form-control form-control-sm big col-md-12" name="vehicle_no[' . $r['id'] . ']" value="' . $r['vehicle_no'] . '" /></td>
						<td><input type="text" class="form-control form-control-sm" name="driver_contact_no[' . $r['id'] . ']" value="' . $r['driver_contact_no'] . '" /></td>
						<td class="aligncenter">' . $r['container_type'] . '</td>
						<td><input type="text" class="form-control form-control-sm big col-md-12" name="container_no[' . $r['id'] . ']" value="' . $r['container_no'] . '" /></td>
						<td><input type="text" class="form-control form-control-sm big col-md-12" name="seal_no[' . $r['id'] . ']" value="' . $r['seal_no'] . '" /></td>
						<td><div class="input-group date DateTimePicker">
								<span class="input-group-addon"><i class="icon-calendar"></i></span>
								<input type="text" class="form-control form-control-sm" name="pickup_date[' . $r['id'] . ']" value="' . ($r['pickup_date'] != '00-00-0000 00:00:00' ? $r['pickup_date'] : '') . '" />
							</div></td>
						<td><input type="text" class="form-control form-control-sm" name="remarks[' . $r['id'] . ']" value="' . $r['remarks'] . '" /></td>
					</tr>';
				}
				
				foreach ($rows['containers'] as $container_type_id => $container_type) {
					for($index = 0; $index < $container_type['count']; $index++) {
						echo '<tr>
							<td class="aligncenter big">' . $i++ . '</td>
							<td><input type="text" class="form-control form-control-sm big col-md-12" name="new_lr_no[]" value="" /></td>
							<td><input type="hidden" name="new_stuffing_date[]" value="' . substr($container_type['stuffing_date'], 0, 10) . '" />
								<input type="text" class="form-control form-control-sm big col-md-12" name="new_vehicle_no[]" value="" /></td>
							<td><input type="text" class="form-control form-control-sm" name="new_driver_contact_no[]" value="" /></td>
							<td class="aligncenter"><input type="hidden" class="form-control form-control-sm big col-md-12" name="new_container_type_id[]" value="' . $container_type_id . '" />' . $container_type['container_type'] . '</td>
							<td><input type="text" class="form-control form-control-sm big col-md-12" name="new_container_no[]" value="" /></td>
							<td><input type="text" class="form-control form-control-sm big col-md-12" name="new_seal_no[]" value="" /></td>
							<td><div class="input-group date DateTimePicker">
								<span class="input-group-addon"><i class="icon-calendar"></i></span>
								<input type="text" class="form-control form-control-sm" name="new_pickup_date[]" value="" />
							</div></td>
							<td><input type="text" class="form-control form-control-sm" name="new_remarks[]" value="" /></td>
						</tr>';
					}
				}
				?>
			</tbody>

			<!-- <tfoot>
				<tr>
					<th colspan="<?php echo (($cfs_stuffing OR ! $is_flexi) ? '8' : '8') ?>"></th>
					<th class="alignright"><?php echo $total['units'] ?></th>
					<th></th>
					<th class="alignright"><?php echo $total['gross_weight'] ?></th>
					<th class="alignright"><?php echo $total['nett_weight'] ?></th>
					<?php echo (($cfs_stuffing OR ! $is_flexi) ? '' : '<th></th>') ?>
					<th></th>
				</tr>
			</tfoot> -->
			</table>
			</fieldset>
	</div>

	<div class="card-footer">
		<button type="submit" class="btn btn-success" id="Update">Update</button>
	</div>
</div>

</form>
