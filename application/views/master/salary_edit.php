<?php
echo start_panel($page_title, '', 'nopadding');
echo form_open($this->uri->uri_string(), 'id="MainForm"');
?>
<fieldset>
	<div class="row">
		<div class="col-md-6">
			<div class="form-group">
				<label class="control-label">Staff Name</label>
				<h5><?php echo $staff['title'] . ' ' . $staff['firstname'] . ' ' . $staff['middlename'] . ' ' . $staff['lastname']; ?></h5>
			</div>
		</div>

		<div class="col-md-6">
			<div class="form-group">
				<label class="control-label">Designation / Category / Location</label>
				<h5><?php echo $staff['designation'] . ' / ' . $staff['category'] . ' / ' . $staff['location']; ?></h5>
			</div>
		</div>
	</div>

	<table class="table table-condensed table-striped">
	<thead>
	<tr>
		<th width="100px">Type</th>
		<th>Name</th>
		<th width="100px">Amount</th>
		<th width="24px" class="aligncenter"><a href="#" class="CheckAll"><i class="icon-trashcan"></i></a></th>
	</tr>
	</thead>

	<tbody>
	<?php 
	$class = array(
		'Salary' => 'green',
		'Deduction' => 'red'
	);
	$total = 0;
	foreach ($rows as $r) {
		echo '
	<tr>
		<td class="' . $class[$r['type']] . '">' . $r['type'] . '</td>
		<td><input type="text" class="form-control form-control-sm ' . $class[$r['type']] . '" name="name[' . $r['id'] . ']" value="' . $r['name'] . '" /></td>
		<td><input type="text" class="form-control form-control-sm Numeric ' . $class[$r['type']] . '" name="amount[' . $r['id'] . ']" value="' . $r['amount'] . '" /></td>
		<td class="aligncenter">' . form_checkbox(array('name' => 'delete_id['.$r['id'].']', 'value' => $r['id'], 'checked' => false, 'class' => 'DeleteCheckbox')) . '</td>
	</tr>';
			if ($r['type'] == 'Salary')
				$total += $r['amount'];
			else
				$total -= $r['amount'];
		}
	?>
	<tr id="1" class="hide">
		<td><?php echo form_dropdown('new_type[]', getEnumSetOptions('salary_details', 'type'), 'Salary', 'class="form-control form-control-sm"'); ?></td>
		<td><input type="text" class="form-control form-control-sm Clear Text col-md-12" name="new_name[]" value="" /></td>
		<td><input type="text" class="form-control form-control-sm Numeric" name="new_amount[]" value="" /></td>
		<td><span id="1"><a href="#" class="btn btn-danger btn-sm"><i class="icon-minus"></i></a></span></td>
	</tr>

	<tr id="Blank">
		<td><?php echo form_dropdown('type', getEnumSetOptions('salary_details', 'type'), 'Salary', 'class="form-control form-control-sm"'); ?></td>
		<td><input type="text" class="form-control form-control-sm" value="" id="ajaxName" /></td>
		<td><input type="text" class="form-control form-control-sm Numeric" value="" /></td>
		<td><a href="javascript:make_copy(1)" class="btn btn-success btn-sm"><i class="fa fa-plus"></i></a></td>
	</tr>
	</tbody>
	</table>

</fieldset>

<div class="form-actions">
	<div class="row">
		<div class="col-md-6">
			<button type="submit" class="btn btn-success" id="Update">Update</button>
			<button type="button" class="btn btn-danger" onclick="javascript: window.close()">Close</button>
		</div>
		<div class="col-md-6 alignright bold">Total: <?php echo inr_format($total) ?></div>
	</div>
</div>
</form>
<?php echo end_panel() ?>

<script>
function make_copy(id) {
	var v0 = $("tr#Blank select:eq(0)").val();
	var v1 = $("tr#Blank input:eq(0)").val();
	var v2 = $("tr#Blank input:eq(1)").val();


	if (!v0 || !v1 || !v2) return;
	
	if (id > 1) {
		$("tr#1").clone().insertBefore("tr#Blank").attr("id", id);
	}

	$("tr#Blank input").each(function(index) {
		$(this).val("");
	});
	$("tr#Blank td a").attr("href", "javascript:make_copy("+(id+1)+")");
	$("#Add").unbind('click');
	$("#Add").on("click", function() {
		make_copy(id+1);
		return false;
	});

	$("tr#"+id+" select:eq(0)").val(v0);
	$("tr#"+id+" input:eq(0)").val(v1);
	$("tr#"+id+" input:eq(1)").val(v2);
		
	$("tr#"+id+" td a").attr("href", "javascript:remove_copy("+id+")");
	$("tr#"+id).removeClass("hide");

	$("tr#Blank input:eq(0)").focus();
}

function remove_copy(id) {
	if (id == 1) {
		$("tr#1 input").each(function(index) {
			$(this).val("");
		});
		$("tr#1").addClass("hide");
	}
	else {
		$("tr#"+id).remove();
	}
}

$(document).ready(function() {
	$("#ajaxName").autocomplete({
		source: "<?php echo site_url($this->_clspath.$this->_class.'/ajaxJson/salary_details/name') ?>",
		minLength: 0
	});
});
</script>