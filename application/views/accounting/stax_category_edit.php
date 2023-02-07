
<div id="modal-delete" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><i class="icon-times"></i></button>
				<h3>Confirm Deletion</h3>
			</div>
			<div class="modal-body"><p>Are you sure, you want to DELETE...?</p></div>
			<div class="modal-footer">
				<?php echo anchor($this->_clspath.$this->_class.'/delete/'.$row['id'], 'Delete', 'class="btn btn-danger"') ?>
			</div>
		</div>
	</div>
</div>

<?php
echo form_open($this->uri->uri_string(), 'class="form-horizontal" id="MainForm"');
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
		<fieldset>
			<div class="col-md-10">
				<div class="form-group<?php echo (strlen(form_error('applicable_date')) > 0 ? ' has-error' : '') ?>">
					<label class="control-label">Applicable Date</label>
					<div class="input-group input-group-sm">
						<input type="text" class="form-control form-control-sm DatePicker" name="applicable_date" value="<?php echo $row['applicable_date']; ?>">
						<div class="input-group-append">
							<div class="input-group-text"><i class="icon-calendar"></i></div>
						</div>
					</div>
				</div>
			</div>
			

			<div class="form-group">
				<label class="control-label col-md-2">Name</label>
				<div class="col-md-10">
					<input type="text" class="form-control form-control-sm" name="name" value="<?php echo $row['name'] ?>" />
				</div>
			</div>

			<div class="form-group">
				<label class="control-label col-md-2">Tax Code</label>
				<div class="col-md-10">
					<input type="text" class="form-control form-control-sm" name="tax_code" value="<?php echo $row['tax_code'] ?>" />
				</div>
			</div>

			<div class="form-group">
				<label class="control-label col-md-2">Other Code</label>
				<div class="col-md-10">
					<input type="text" class="form-control form-control-sm" name="other_code" value="<?php echo $row['other_code'] ?>" />
				</div>
			</div>

			<table class="table table-condensed table-striped DataEntry">
			<thead>
			<tr>
				<th>WEF Date</th>
				<th>Service Tax</th>
				<th>Edu Cess</th>
				<th>HEdu Cess</th>
				<th>Swachh Cess</th>
				<th>Krishi Cess</th>
				<th width="24px" class="aligncenter"><a href="#" class="CheckAll"><i class="icon-trashcan"></i></a></th>
			</tr>
			</thead>

			<tbody>
				<?php
				foreach ($stax_rates as $sr) {
					echo '
				<tr>
					<td><div class="input-group date DatePicker"><span class="input-group-addon"><i class="icon-calendar"></i></span><input type="text" class="form-control form-control-sm AutoDate" name="wef_date[' . $sr['id'] . ']" value="' . $sr['wef_date'] . '" /></div></td>
					<td><input type="text" class="form-control form-control-sm Numeric" name="stax[' . $sr['id'] . ']" value="' . $sr['stax'] . '" /></td>
					<td><input type="text" class="form-control form-control-sm Numeric" name="edu_cess[' . $sr['id'] . ']" value="' . $sr['edu_cess'] . '" /></td>
					<td><input type="text" class="form-control form-control-sm Numeric" name="hedu_cess[' . $sr['id'] . ']" value="' . $sr['hedu_cess'] . '" /></td>
					<td><input type="text" class="form-control form-control-sm Numeric" name="swachh_cess[' . $sr['id'] . ']" value="' . $sr['swachh_cess'] . '" /></td>
					<td><input type="text" class="form-control form-control-sm Numeric" name="krishi_cess[' . $sr['id'] . ']" value="' . $sr['krishi_cess'] . '" /></td>
					<td></td>
				</tr>
				';
				}
				?>
				<tr class="TemplateRow">
					<td><div class="input-group date DatePicker"><span class="input-group-addon"><i class="icon-calendar"></i></span><input type="text" class="form-control form-control-sm AutoDate" name="new_wef_date[]" value="" /></div></td>
					<td><input type="text" class="form-control form-control-sm Numeric Validate" name="new_stax[]" value="" /></td>
					<td><input type="text" class="form-control form-control-sm Numeric Validate" name="new_edu_cess[]" value="" /></td>
					<td><input type="text" class="form-control form-control-sm Numeric Validate" name="new_hedu_cess[]" value="" /></td>
					<td><input type="text" class="form-control form-control-sm Numeric Validate" name="new_swachh_cess[]" value="" /></td>
					<td><input type="text" class="form-control form-control-sm Numeric Validate" name="new_krishi_cess[]" value="" /></td>
					<td><button type="submit" class="btn btn-success btn-sm AddButton"><i class="fa fa-plus"></i></button></td>
				</tr>
			</tbody>
			</table>
		</fieldset>
	</div>

	<div class="card-footer">
		<button type="submit" class="btn btn-success" id="Update">Update</button>
		<a href="#modal-delete" class="btn btn-danger pull-right" data-toggle="modal">Delete</a>
	</div>
</div>

</form>