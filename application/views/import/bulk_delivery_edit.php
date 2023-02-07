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
		<div class="row">
			<div class="col-md-3">
				<div class="form-group<?php echo (strlen(form_error('date')) > 0 ? ' has-error' : '') ?>">
					<label class="control-label">Date</label>
					<div class="input-group input-group-sm">
						<input type="text" class="form-control form-control-sm DatePicker" name="date" value="<?php echo $row['date']; ?>" id="Date">
						<div class="input-group-append">
							<div class="input-group-text"><i class="icon-calendar"></i></div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<table class="table table-condensed table-striped table-bordered DataEntry">
			<thead>
				<tr>
					<th width="24px">Sr</th>
					<th>BL/BE No</th>
					<th>Vehicle No</th>
					<th>Gatepass No</th>
					<th width="150px">Gatepass Date</th>
					<th>Net Weight</th>
					<th width="24px" class="aligncenter"><a href="#" class="CheckAll"><i class="icon-trashcan"></i></a></th>
				</tr>
			</thead>

			<tbody>
				<?php
				$i = 1;
				if($job_id > 0)	{
					foreach ($row['delivery'] as $r) {
						echo '<tr>
							<td>'.$i++.'</td>
							<td>' . $r['bl_no'] . '</td>
							<td>' . $r['vehicle_no'] . '</td>
							<td>' . $r['gatepass_no'] . '</td>
							<td>' . $r['gatepass_date'] . '</td>
							<td><input type="text" class="form-control form-control-sm Numeric" name="nett_weight[' . $r['id'] . ']" value="' . $r['nett_weight'] . '" /></td>
							<td class="aligncenter">' . form_checkbox(array('name' => 'delete_id['.$r['id'].']', 'value' => $r['id'], 'checked' => false, 'class' => 'DeleteCheckbox', 'data-placement' => 'left', 'rel' => 'tooltip', 'data-original-title'=>'Selected Items will be deleted after Update...')) . '</td>
						</tr>';
					}
				}
				?>
				<tr class="TemplateRow">
					<td></td>
					<td><input type="hidden" class="form-control form-control-sm Validate JobID" name="new_job_id[]" value="" />
						<input type="text" class="form-control form-control-sm Validate BLNo Focus" value="" /></td>
					<td><input type="text" class="form-control form-control-sm" name="new_vehicle_no[]" value="" /></td>
					<td><input type="text" class="form-control form-control-sm" name="new_gatepass_no[]" value="" /></td>
					<td><div class="input-group date DatePicker"><span class="input-group-addon"><i class="icon-calendar"></i></span><input type="text" class="form-control form-control-sm AutoDate" name="new_gatepass_date[]" value="" /></div></td>
					<td><input type="text" class="form-control form-control-sm Numeric" name="new_nett_weight[]" value="" /></td>
					<td class="aligncenter" width="24px"><button type="submit" class="btn btn-success btn-sm AddButton"><i class="fa fa-plus"></i></button></td>
				</tr>
			</tbody>
		</table>
	</div>

	<div class="card-footer">
		<button type="button" class="btn btn-success" id="Update">Update</button>
	</div>
</div>
</form>

<script>
$(document).ready(function() {
	$('.BLNo').typeahead({
		hint: false,
		highlight: true,
		minLength: 1
	}, {
		name: 'tt_bl_no',
		displayKey: 'bl_no',
		source: function(query, process) {
			return $.ajax({ 
				url: '<?php echo site_url($this->_clspath.$this->_class.'/ajaxJobs') ?>',
				type: 'POST',
				data: { term: query },
				dataType: 'json',
				success: function (result) {
					return process(result);
				}
			});
		},
		templates: {
			empty: ['<div class="tt-no-result">Unable to find any results that match the current query</div>'],
			suggestion: Handlebars.compile('<p><span class="blueDark">BL:{{bl_no}}</span><span class="tiny pink"> BE:{{be_no}}</span></p>')
		}
	}).on('typeahead:selected', function(obj, datum) {
		$row = $(this).parent('span').parent('td').parent('tr');
		$(this).parent('span').parent('td').find('.JobID').val(datum.id);
		$row.find('.BLNo').typeahead('val', datum.bl_no);
	});
});
</script>
