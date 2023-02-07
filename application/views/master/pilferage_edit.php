<div class="row">
	<div class="col-md-7">
	<?php
	echo start_panel( $page_desc , anchor($this->_clspath.$this->_class, '<span class="icon"><i class="icon-list"></i></span>'), 'nopadding',
	'<div class="buttons">' . anchor($this->_clspath.$this->_class."/edit/0", '<i class="fa fa-plus"></i>  Add', 'class="btn btn-sm btn-success"') . '</div>');
	echo form_open($this->uri->uri_string(), 'id="MainForm"');
	echo form_hidden($id);
	?>
		<fieldset>
		<div class="form-group<?php echo (strlen(form_error('registration_no')) > 0 ? ' has-error' : '') ?>">
			<label class="control-label">Registration No</label>
			
				<input type="hidden" name="vehicle_id" value="<?php echo $row['vehicle_id'] ?>" id="VehicleID" />
				<input type="text" class="form-control form-control-sm" name="registration_no" value="<?php echo $registration_no ?>" id="RegistrationNo"/>
			
		</div>
		</fieldset>

		<fieldset>
		<table class="table table-condensed table-bordered DataEntry">
		<thead>
		<tr>
			<th>Date &amp; Time</th>
			<th>Liters</th>
			<th width="24px" class="aligncenter"><a href="#" class="CheckAll"><i class="icon-trashcan"></i></a></th>
		</tr>
		</thead>

		<tbody>
		<?php
		foreach ($row['pilferages'] as $r) {
			echo '<tr>
			<td><div class="input-group date DatePicker"><span class="input-group-addon"><i class="icon-calendar"></i></span><input type="text" class="form-control form-control-sm AutoDate" name="date[' . $r['id'] . ']" value="' . $r['date'] . '" />&nbsp;&nbsp;
				<input type="text" class="form-control form-control-sm Time" name="time[' . $r['id'] . ']" value="' . $r['time'] . '" /></div></td>
			<td><input type="text" class="form-control form-control-sm Numeric" name="liters[' . $r['id'] . ']" value="' . $r['liters'] . '" /></td>
			<td class="aligncenter">' . form_checkbox(array('name' => 'delete_id['.$r['id'].']', 'value' => $r['id'], 'checked' => false, 'class' => 'DeleteCheckbox')) . '</td>
		</tr>';
			}
		?>

		<tr class="TemplateRow">
			<td><input type="text" class="DateTime Validate Unchanged" name="new_date[]" />&nbsp;&nbsp;
				<input type="text" class="form-control form-control-sm Time Validate Unchanged" name="new_time[]" /></td>
			<td><input type="text" class="form-control form-control-sm Numeric Validate Focus" name="new_liters[]" /></td>
			<td class="aligncenter"><button type="submit" class="btn btn-success btn-sm AddButton"><i class="icon-white icon-plus"></i></button></td>
		</tr>
		</tbody>
		</table>
		</fieldset>

		<div class="form-actions">
			<button type="button" class="btn btn-success" id="Update">Update</button>			
		</div>
		</form>
	<?php echo end_panel() ?>
	</div>
</div>

<script>
$(document).ready(function() {
	$("#RegistrationNo").autocomplete({
		source: '<?php echo site_url($this->_clspath.$this->_class.'/ajaxRegistrationNos') ?>',
		minLength: 1,
		focus: function(event, ui) {
			$("#RegistrationNo").val(ui.item.registration_no);
			$("#VehicleID").val(ui.item.id);
			return false;
		},
		select: function(event, ui) {
			$("#RegistrationNo").val(ui.item.registration_no);
			$("#VehicleID").val(ui.item.id);
			return false;
		},
		response: function(event, ui) {
            if (ui.content.length === 0) {
                $("#RegistrationNo").val('');
				$("#VehicleID").val(0);
            }
        }
	})
	.data('ui-autocomplete')._renderItem = function(ul, item) {
		return $('<li></li>')
			.data('item.autocomplete', item)
			.append('<a>' + item.registration_no + ' <span class="tiny"><span class="orange">' + item.category + '</span> ' + item.type + '</span></a>')
			.appendTo(ul);
	};
});
</script>

