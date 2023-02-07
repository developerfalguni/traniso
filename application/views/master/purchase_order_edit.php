<?php
echo start_panel($page_title, anchor($this->_clspath.$this->_class, '<span class="icon"><i class="icon-list"></i></span>'), 'nopadding', '<div class="buttons">
		<a href="' . base_url($this->_clspath.$this->_class.'/edit/0') . '" class="btn btn-sm btn-success" id="NewVoucher"><i class="fa fa-plus"></i> Add New</a>
		</div>');
echo form_open($this->uri->uri_string(), 'id="MainForm"');
?>

<fieldset>
	<div class="row">
		<div class="col-md-2">
			<div class="form-group">
				<label class="control-label">No</label>
				<input type="text" class="form-control form-control-sm Text" name="id2" value="<?php echo $row['id2'] ?>" />
			</div>
		</div>

		<div class="col-md-2">
			<div class="form-group <?php echo (strlen(form_error('date')) > 0 ? 'has-error' : '') ?>">
				<label class="control-label">Date</label>
				<div class="input-group date DatePicker">
					<span class="input-group-addon"><i class="icon-calendar"></i></span>
					<input type="text" class="form-control form-control-sm AutoDate" name="date" value="<?php echo ($row['date'] != '00-00-0000' ? $row['date'] : '') ?>" />
				</div>
			</div>
		</div>

		<div class="col-md-8">
			<div class="form-group">
				<label class="control-label">Party Name</label>
				<input type="hidden" name="party_id" value="<?php echo $party_id ?>" id="PartyID" />
				<input type="text" class="form-control form-control-sm" name="party_name" value="<?php echo $party_name ?>" id="PartyName" />
			</div>
		</div>
	</div>

	<div class="form-group<?php echo (strlen(form_error('remarks')) > 0 ? ' has-error' : '') ?>">
		<label class="control-label">Remarks</label>
		
		<input type="text" class="form-control form-control-sm" name="remarks" value="<?php echo $row['remarks'] ?>" />
		
	</div>
	<br />

	<table class="table table-condensed table-striped table-bordered DataEntry">
	<thead>
	<tr>
		<th width="32px">Sr No</th>
		<th>Particulars</th>
		<th>Part No</th>
		<th>Make</th>
		<th>Quantity</th>
		<th>Remarks</th>
		<th width="24px" class="aligncenter"><a href="#" class="CheckAll"><i class="icon-trashcan"></i></a></th>
	</tr>
	</thead>

	<tbody>
	<?php
		$total = array('pieces' => 0, 'cbm' => 0);
		$i = 1;
		foreach ($details as $d) {
			echo '<tr>
		<td class="aligncenter">' . $i++ . '</td>
		<td class="aligncenter">' . $d['log_no'] . '</td>
		<td class="aligncenter">' . $d['cha_code'] . '</td>
		<td class="aligncenter">' . $d['bl_no'] . '</td>
		<td class="aligncenter">' . $d['length'] . '</td>
		<td class="aligncenter">' . $d['dia'] . '</td>
		<td class="aligncenter">' . $d['hollow'] . '</td>
		<td class="aligncenter">' . number_format($d['volume'], 3, '.', '') . '</td>
		<td class="aligncenter">' . $d['species'] . '</td>
		<td class="aligncenter">' . $d['mark'] . '</td>
		<td class="aligncenter">' . form_checkbox(array('name' => 'delete_id['.$d['id'].']', 'value' => $d['id'], 'checked' => false, 'class' => 'DeleteCheckbox')) . '</td>
	</tr>';
		}
	?>
	
	<tr class="TemplateRow">
		<td></td>
		<td><input type="hidden" name="new_llid[]" value="" />
			<input type="text" class="form-control form-control-sm" name="new_log_no[]" value="" /></td>
		<td><input type="text" class="form-control form-control-sm Numeric" name="new_cha_code[]" value="" /></td>
		<td><input type="text" class="form-control form-control-sm Numeric" name="new_bl_no[]" value="" /></td>
		<td><input type="text" class="form-control form-control-sm Numeric Length" name="new_length[]" value="" /></td>
		<td><input type="text" class="form-control form-control-sm Numeric Dia" name="new_dia[]" value="" /></td>
		<td><input type="text" class="form-control form-control-sm Numeric Hollow" name="new_hollow[]" value="" /></td>
		<td><input type="text" class="form-control form-control-sm Numeric CBM" name="new_volume[]" value="" /></td>
		<td><input type="text" class="form-control form-control-sm" name="new_species[]" value="" /></td>
		<td><input type="text" class="form-control form-control-sm" name="new_mark[]" value="" /></td>
		<td><button type="submit" class="btn btn-success btn-sm AddButton"><i class="fa fa-plus"></i></button></td>
	</tr>
	</tbody>
	</table>
</fieldset>

<div class="form-actions">
	<button type="button" class="btn btn-success" id="Update">Update</button>
</div>
</form>

<?php echo end_panel(); ?>

<script>
$(document).ready(function() {
	$(".Particulars").autocomplete({
		source: '<?php echo site_url($this->_clspath.$this->_class.'/ajaxJson/particualrs') ?>',
		minLength: 0
	});

	$("#PartyName").autocomplete({
		source: '<?php echo site_url($this->_clspath.$this->_class.'/json/parties/id/name') ?>',
		minLength: 1,
		focus: function(event, ui) {
			$(this).val(ui.item.name);
			return false;
		},
		select: function(event, ui) {
			$(this).prevAll('input').val(ui.item.id);
			$(this).val(ui.item.name);
			return false;
		},
		response: function(event, ui) {
			if (ui.content.length === 0) {
				$(this).prevAll('input').val(0);
				$(this).val('');
			}
		}
	})
	.data('ui-autocomplete')._renderItem = function(ul, item) {
		return $('<li></li>')
			.data('item.autocomplete', item)
			.append('<a>' + item.name + '</a>')
			.appendTo(ul);
	};
});
</script>