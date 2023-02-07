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
			<div class="form-group">
				<label class="control-label col-md-2">Type</label>
				<div class="col-md-10">
					<?php echo form_dropdown('type', getEnumSetOptions('tds_classes', 'type'), $row['type'], 'class="form-control form-control-sm"'); ?>
				</div>
			</div>

			<div class="form-group">
				<label class="control-label col-md-2">Name</label>
				<div class="col-md-10">
					<input type="text" class="form-control form-control-sm" name="name" value="<?php echo $row['name'] ?>" />
				</div>
			</div>

		<?php if ($row['type'] == 'Payment') : ?>
			<div class="form-group">
				<label class="control-label col-md-2">Section</label>
				<div class="col-md-10">
					<input type="text" class="form-control form-control-sm" name="section" value="<?php echo $row['section'] ?>" />
				</div>
			</div>

			<div class="form-group">
				<label class="control-label col-md-2">Payment Code</label>
				<div class="col-md-10">
					<input type="text" class="form-control form-control-sm" name="payment_code" value="<?php echo $row['payment_code'] ?>" />
				</div>
			</div>
		</fieldset>
		<?php else : ?>
		</fieldset>

			<table class="table table-condensed table-striped DataEntry">
			<thead>
			<tr>
				<th>Nature of Payment</th>
				<th width="120px">Applicable From</th>
				<th width="80px">TDS</th>
				<th width="80px">Surcharge</th>
				<th width="80px">Ed. Cess</th>
				<th width="80px">HEd. Cess</th>
				<th width="24px" class="aligncenter"><a href="#" class="CheckAll"><i class="icon-trashcan"></i></a></th>
			</tr>
			</thead>

			<tbody>
				<?php foreach ($details as $d) {
					echo '<tr>
					<td>' . form_dropdown('payment_id[' . $d['id'] . ']', getSelectOptions('tds_classes', 'id', 'name', 'WHERE type="Payment"'), $d['payment_id'], 'class="form-control form-control-sm"') . '</td>
					<td><div class="input-group input-group-sm">
					<input type="text" class="form-control form-control-sm DatePicker" name="applicable_date" value="<?php echo $row[\'applicable_date\']; ?>"><i class="icon-calendar"></i><div class="input-group-append"><div class="input-group-text"></div></div></div></td>
					<td><input type="text" class="form-control form-control-sm Numeric" name="tds[' . $d['id'] . ']" value="' . $d['tds'] . '" /></td>
					<td><input type="text" class="form-control form-control-sm Numeric" name="surcharge[' . $d['id'] . ']" value="' . $d['surcharge'] . '" /></td>
					<td><input type="text" class="form-control form-control-sm Numeric" name="edu_cess[' . $d['id'] . ']" value="' . $d['edu_cess'] . '" /></td>
					<td><input type="text" class="form-control form-control-sm Numeric" name="hedu_cess[' . $d['id'] . ']" value="' . $d['hedu_cess'] . '" /></td>
					<td class="aligncenter">' . form_checkbox(array('name' => 'delete_id['.$d['id'].']', 'value' => $d['id'], 'checked' => false, 'class' => 'DeleteCheckbox')) . '</td>
				</tr>';
				} ?>

				<tr class="TemplateRow">
					<td><?php echo form_dropdown('new_payment_id[]', getSelectOptions('tds_classes', 'id', 'name', 'WHERE type="Payment"'), 0, 'class="form-control form-control-sm"') ?></td>
					<td><div class="input-group date DatePicker"><span class="input-group-addon"><i class="icon-calendar"></i></span><input type="text" class="form-control form-control-sm AutoDate" name="new_applicable_date[]" value="" /></td>
					<td><input type="text" class="form-control form-control-sm Numeric" name="new_tds[]" value="" /></div></td>
					<td><input type="text" class="form-control form-control-sm Numeric" name="new_surcharge[]" value="" /></td>
					<td><input type="text" class="form-control form-control-sm Numeric" name="new_edu_cess[]" value="" /></td>
					<td><input type="text" class="form-control form-control-sm Numeric" name="new_hedu_cess[]" value="" /></td>
					<td><button type="button" class="btn btn-success btn-sm AddButton"><i class="fa fa-plus"></i></button></td>
				</tr>
			</tbody>
			</table>
		<?php endif; ?>
	</div>

	<div class="card-footer">
		<button type="submit" class="btn btn-success" id="Update">Update</button>
	</div>
</div>

</form>