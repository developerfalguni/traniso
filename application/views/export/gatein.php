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
	
	<!-- <div class="card-body"></div> -->

	<table class="table table-condensed table-striped">
	<thead>
		<tr>
			<th width="24px">No</th>
			<th>Party Name</th>
			<th>Invoice No</th>
			<th>Vehicle No</th>
			<th>Container No</th>
			<th>Cont. Type</th>
			<th>Line Seal No</th>
			<th>FPOD</th>
			<th class="150px">GateIn Date</th>
			<th>Gate In Vessel</span></th>
		</tr>
	</thead>

	<tbody>
	<?php 
		$i = 1;
		foreach ($rows as $r) {
			echo '<tr>
				<td class="tiny"><input type="hidden" name="child_job_id[' . $r['id'] . ']" value="' . $r['child_job_id'] . '" />' . $i++ . '</td>
				<td class="tiny">' . $r['party_name'] . '</td>
				<td class="tiny">' . $r['invoice_no'] . '</td>
				<td class="tiny">' . $r['vehicle_no'] . '</td>
				<td class="tiny">' . $r['container_no'] . '</td>
				<td class="aligncenter tiny">' . $r['container_type'] . '</td>
				<td class="tiny">' . $r['seal_no'] . '</td>
				<td class="tiny">' . $r['fpod'] . '</td>
				<td class="tiny"><div class="input-group date DateTimePicker">
						<span class="input-group-addon"><i class="icon-calendar"></i></span>
						<input type="text" class="form-control form-control-sm" name="gate_in[' . $r['id'] . ']" value="' . ($r['gate_in'] != '00-00-0000 00:00:00' ? $r['gate_in'] : '')  . '" />
					</div></td>
				<td><input type="hidden" name="vessel_id[' . $r['id'] . ']" value="' . $r['vessel_id'] . '" />
					<input type="text" class="form-control form-control-sm VesselName" value="' . $r['vessel_name'] . '" /></td>
			</tr>';
		}
	?>
	</tbody>
	</table>

	<div class="card-footer">
		<button type="submit" class="btn btn-success" id="Update">Update</button>
	</div>
</div>

</form>

<script type="text/javascript">
$(document).ready(function() {
	$('.VesselName').kaabar_autocomplete({source: '<?php echo site_url('master/vessel/ajax') ?>'});
});
</script>