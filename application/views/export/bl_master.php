<?php
echo form_open($this->uri->uri_string(), array('class' => 'form-horizontal'));
echo form_hidden($job_id);
?>

<div class="card card-default">
	<div class="card-header">
		<h3 class="card-title"><?php echo $page_title ?></h3>
	</div>
	
	<table class="table table-condensed table-striped">
	<thead>
		<tr>
			<th>VisualImpex Job No</th>
			<th>Invoice No</th>
			<th>SB No &amp; Date</th>
			<th>MR No &amp; Date</th>
			<th>BL No &amp; Date</th>
		</tr>
	</thead>

	<tbody>
		<?php 
		foreach ($rows as $r) {
			echo '<tr>
				<td class="tiny"><input type="hidden" name="child_job_id[' . $r['child_job_id'] . ']" value="' . $r['child_job_id'] . '" />' . $r['vi_job_no'] . '</td>
				<td class="tiny"><input type="hidden" name="job_invoice_id[' . $r['child_job_id'] . ']" value="' . $r['job_invoice_id'] . '" />' . $r['invoice_no'] . '</td>
				<td><input type="text" class="form-control form-control-sm" name="sb_no[' . $r['child_job_id'] . ']" value="' . $r['sb_no'] . '" /><br />
					<input type="text" class="form-control form-control-sm DatePicker" name="sb_date[' . $r['child_job_id'] . ']" value="' . $r['sb_date'] . '" /></td>
				<td><input type="text" class="form-control form-control-sm" name="mr_no[' . $r['child_job_id'] . ']" value="' . $r['mr_no'] . '" /><br />
					<input type="text" class="form-control form-control-sm DatePicker" name="mr_date[' . $r['child_job_id'] . ']" value="' . $r['mr_date'] . '" /></td>
				<td><input type="text" class="form-control form-control-sm" name="bl_no[' . $r['child_job_id'] . ']" value="' . $r['bl_no'] . '" /><br />
					<input type="text" class="form-control form-control-sm DatePicker" name="bl_date[' . $r['child_job_id'] . ']" value="' . $r['bl_date'] . '" /></td>
			</tr>';
		} ?>
	</tbody>
	</table>
<div class="card-footer">
		<button type="submit" class="btn btn-success" id="Update">Update</button>
	</div>
</div>

</form>