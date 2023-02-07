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

	<table class="table table-condensed table-striped">
	<thead>
		<tr>
			<th width="24px">No</th>
			<th>POL</th>
			<th>Invoice No</th>
			<th>SB No</th>
			<th>Containers</th>
			<th>Doc Handover</th>
			<th>Vessel</th>
		</tr>
	</thead>

	<tbody>
	<?php 
		$i = 1;
		foreach ($rows as $r) {
			echo '<tr>
		<td>' . $i++ . '</td>
		<td>' . $r['pol'] . '</td>
		<td class="nowrap">' . $r['invoice_no'] . '</td>
		<td class="big">' . $r['sb_no'] . '</td>
		<td class="big">' . $r['containers'] . '</td>
		<td class="nowrap"><div class="input-group date DateTimePicker">
				<span class="input-group-addon"><i class="icon-calendar"></i></span>
				<input type="text" class="form-control form-control-sm" name="doc_handover[' . $r['id'] . ']" value="' . ($r['doc_handover'] != '00-00-0000' ? $r['doc_handover'] : '') . '" />
			</div></td>
		<td class="nowrap">' . $r['vessel_name'] . '</td>
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