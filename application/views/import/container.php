<?php
echo form_open($this->uri->uri_string(), array('class' => 'form-horizontal'));
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
		<th>ID</th>
		<th>Size</th>
		<th>Code</th>
		<th>Name</th>
		<th>Number</th>
		<th>Seal No</th>
		<th width="100px">Rcvd. Nett Weight</th>
	</tr>
	</thead>

	<tbody>
	<?php
	$total = array(
		'net_weight' => 0,
	);
	foreach ($rows as $r) {
		$total['net_weight'] += $r['net_weight'];

		echo '<tr>
			<td>' . anchor($this->_clspath.$this->_class."/edit/".$r['job_id'].'/'.$r['id'], $r['id']) . '</td>
			<td>' . $r['size'] . '</td>
			<td>' . $r['code'] . '</td>
			<td>' . $r['name'] . '</td>
			<td>' . $r['number'] . '</td>
			<td>' . $r['seal'] . '</td>
			<td><input type="text" class="form-control form-control-sm Numeric" name="net_weight[' . $r['id'] . ']" value="' . $r['net_weight'] . '" /></td>
		</tr>';
	} ?>
	</tbody>

	<tfoot>
	<tr>
		<th class="alignright" colspan="6">Total</th>
		<th class="alignright"><?php echo $total['net_weight'] ?></th>
	</tr>
	</tfoot>
	</table>

	<div class="card-footer">
		<button type="submit" class="btn btn-success" id="Update">Update</button>
		<?php echo anchor($this->_clspath.$this->_class.'/edit/'.$job_id.'/0', '<i class="fa fa-plus"></i> Add', 'class="btn btn-success"') ?>
		<div class="btn-group">
			<?php 
				echo anchor($this->_clspath.$this->_class."/preview/".$job_id."/0/1", '<i class="icon-file-o"></i> Preview', 'class="btn btn-default Popup"') .
				anchor($this->_clspath.$this->_class."/preview/".$job_id."/1/1", '<i class="icon-file-pdf"></i> PDF', 'class="btn btn-default Popup"') 
			?>
		</div>
	</div>
</div>

</form>