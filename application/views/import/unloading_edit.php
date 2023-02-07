<?php echo form_open($this->uri->uri_string(), 'id="MainForm"'); ?>

<table class="InputForm">
<tr>
	<th></th>
	<th>Date</th>
	<th>Bill No</th>
	<th colspan="7">Transporter</th>
</tr>

<tr>
	<td></td>
	<td><input type="text" name="date" value="<?php echo $row['date']) ?>" id="Date" class="DateTime" /><?php echo form_error('date'; ?></td>
	<td><input type="text" name="bill_no" value="<?php echo $row['bill_no']) ?>" id="bill_no" class="Text" /><?php echo form_error('bill_no'; ?></td>
	<td colspan="7"><input type="text" name="name" value="<?php echo $row['name']) ?>" size="50" maxlength="255" id="ajaxTransporter" class="Text" /><?php echo form_error('name'; ?></td>
</tr>
<!--</table>

<table class="InputForm">-->
<tr>
	<th>No.</th>
	<th>Date</th>
	<th>From - To</th>
	<th>Job No</th>
	<th>Container No</th>
	<th>Size</th>
	<th>Vehicle No</th>
	<th>Amount</th>
	<th>Remarks</th>
	<th><input type="checkbox" name="CheckAll" id="CheckAll" /></th>

<?php
	$sr_no = 1;
	$net_total = 0;
	foreach ($details as $detail) {
?>
<tr>
	<td align="center"><?php echo $sr_no++ ?></td>
	<td><input class="DateTime" type='text' name="tdate[<?php echo $detail['id'] ?>]" size="10" value="<?php echo $detail['tdate'] ?>" maxlength="10" /></td>
	<td><input type="text" class="form-control form-control-sm Text" name="from_to[<?php echo $detail['id'] ?>]" value="<?php echo $detail['from_to'] ?>" size="15" maxlength="50" /></td>
	<td><input type="text" class="form-control form-control-sm Text ReadOnly" value="<?php echo $detail['id2_format'] ?>" size="15" readonly="true" /></td>
	<td><input type="text" class="form-control form-control-sm Text ReadOnly" value="<?php echo $detail['container_no'] ?>" size="10" readonly="true" /></td>
	<td><input type="text" class="form-control form-control-sm Numeric ReadOnly" value="<?php echo $detail['size'] ?>" size="2" disabled="true" /></td>
	<td><input type="text" class="form-control form-control-sm Text" name="vehicle_no[<?php echo $detail['id'] ?>]" value="<?php echo $detail['vehicle_no'] ?>" size="10" maxlength="50" /></td>
	<td><input type="text" class="form-control form-control-sm Numeric" name="amount[<?php echo $detail['id'] ?>]" value="<?php echo $detail['amount'] ?>" size="10" maxlength="12" /></td>
	<td><input type="text" class="form-control form-control-sm Text" name="remarks[<?php echo $detail['id'] ?>]" value="<?php echo $detail['remarks'] ?>" size="20" maxlength="255" /></td>
	<td><?php echo form_checkbox(array('name' => 'delete_id['.$detail['id'].']', 'value' => $detail['id'], 'checked' => false, 'class' => 'DeleteCheckbox')); ?></td>
</tr>
<?php
	$net_total += $detail['amount'];
	}
?>

<tr id="1" class="hide">
	<td><input type="hidden" name="new_id[]" /></td>
	<td><input class="DateTime" type='text' name="new_tdate[]" size="10" value="<?php echo date('d-m-Y') ?>" maxlength="10" /></td>
	<td><input type="text" class="form-control form-control-sm Text" name="new_from_to[]" size="15" maxlength="50" /></td>
	<td><input type="hidden" name="new_job_id[]" /><input type="text" class="form-control form-control-sm Text" size="15" readonly="true" /></td>
	<td><input type="text" class="form-control form-control-sm Text" name="new_container_no[]" size="10" readonly="true" /></td>
	<td><input type="text" class="form-control form-control-sm Numeric ReadOnly" size="2" disabled="true" /></td>
	<td><input type="text" class="form-control form-control-sm Text" name="new_vehicle_no[]" size="10" maxlength="50" /></td>
	<td><input type="text" class="form-control form-control-sm Numeric" name="new_amount[]" size="10" maxlength="12" /></td>
	<td><input type="text" class="form-control form-control-sm Text" name="new_remarks[]" size="20" maxlength="255" /></td>
	<td id="1"><a href="#" class="btn btn-danger btn-sm"><i class="icon-minus icon-white"></i></a></td>
</tr>

<tr id="Blank">
	<td><input type="hidden" name="blank_id" /></td>
	<td><input id="BlankDate" class="DateTime" type='text' name="blank_tdate" size="10" value="<?php echo date('d-m-Y') ?>" maxlength="10" /></td>
	<td><input type="text" class="form-control form-control-sm Text" name="blank_from_to" size="15" maxlength="50" id="ajaxFromTo"  /></td>
	<td><input type="hidden" name="blank_job_id" /><input type="text" class="form-control form-control-sm Text" size="15" readonly="true" /></td>
	<td><input type="text" class="form-control form-control-sm Text" name="blank_container_no" size="10" maxlength="30" id="ajaxContainerNo" /></td>
	<td><input type="text" class="form-control form-control-sm Numeric ReadOnly" size="2" disabled="true" /></td>
	<td><input type="text" class="form-control form-control-sm Text" name="blank_vehicle_no" size="10" maxlength="50" /></td>
	<td><input type="text" class="form-control form-control-sm Numeric" name="blank_amount" size="10" maxlength="12" /></td>
	<td><input type="text" class="form-control form-control-sm Text" name="blank_remarks" size="20" maxlength="255" id="ajaxRemarks" /></td>
	<td><button type="submit" class="btn btn-success btn-sm" id="Add"><i class="icon-white icon-plus"></i></button></td>
</tr>

<tr>
	<td colspan="7" align="right"><span class="Bold">Total :</span></td>
	<td><input type="text" class="form-control form-control-sm Numeric" value="<?php echo number_format($net_total, 2, '.', '') ?>" size="10" disabled="true" /></td>
	<td colspan="2"></td>
</tr>

<tr>
	<td colspan="7" align="right"><span class="Bold">Tax Amount :</span></td>
	<td><input type="text" name="tax_amount" value="<?php echo $row['tax_amount']) ?>" id="tax_amount" class="Numeric" /><?php echo form_error('tax_amount'; ?></td>
</tr>

<tr>
	<td colspan="7" align="right"><span class="Bold">Net Total :</span></td>
	<td><input type="text" class="form-control form-control-sm Numeric" value="<?php echo number_format($net_total+$row['tax_amount'], 2, '.', '') ?>" size="10" disabled="true" /></td>
	<td colspan="2"></td>
</tr>

<tr class="Footer">
	<td colspan="10"><button type="button" class="btn btn-success" id="Update">Update</button></td>
</tr>
</table>
</form>

<script type="text/javascript">
function make_copy(id) {
	var tdid = $("tr#Blank input:eq(0)").val();
	var date = $("tr#Blank input:eq(1)").val();
	var from_to = $("tr#Blank input:eq(2)").val();
	var job_id = $("tr#Blank input:eq(3)").val();
	var id2 = $("tr#Blank input:eq(4)").val();
	var container = $("tr#Blank input:eq(5)").val();
	var size = $("tr#Blank input:eq(6)").val();
	var vehicle = $("tr#Blank input:eq(7)").val();
	var amount = $("tr#Blank input:eq(8)").val();
	var remarks = $("tr#Blank input:eq(9)").val();

	if (id > 1) {
		$("tr#1").clone().insertBefore("tr#Blank").attr("id", id);
	}

	$("tr#Blank input").each(function(index) {
		$(this).val("");
	});
	
	$("#Add").unbind('click');
	$("#Add").on('click', function() {
		make_copy(id+1);
		return false;
	});

	$("tr#"+id+" input:eq(0)").val(tdid);
	
	$("tr#"+id+" input:eq(1)").attr("name", 'tdate['+tdid+']');
	$("tr#"+id+" input:eq(1)").val(date);
	$("tr#"+id+" input:eq(2)").attr("name", 'from_to['+tdid+']');
	$("tr#"+id+" input:eq(2)").val(from_to);
	$("tr#"+id+" input:eq(3)").attr("name", 'job_id['+tdid+']');
	$("tr#"+id+" input:eq(3)").val(job_id);
	$("tr#"+id+" input:eq(4)").val(id2);
	$("tr#"+id+" input:eq(5)").attr("name", 'container_no['+tdid+']');
	$("tr#"+id+" input:eq(5)").val(container);
	$("tr#"+id+" input:eq(6)").val(size);
	$("tr#"+id+" input:eq(7)").attr("name", 'vehicle_no['+tdid+']');
	$("tr#"+id+" input:eq(7)").val(vehicle);
	$("tr#"+id+" input:eq(8)").attr("name", 'amount['+tdid+']');
	$("tr#"+id+" input:eq(8)").val(amount);
	$("tr#"+id+" input:eq(9)").attr("name", 'remarks['+tdid+']');
	$("tr#"+id+" input:eq(9)").val(remarks);

	$("tr#"+id).attr("id", id);
	$("tr#"+id+" a").attr("href", "javascript:remove_copy("+id+")");
	$("tr#"+id).removeClass("hide");
	
	$("tr#Blank input:eq(1)").val(date);
	$("tr#Blank input:eq(4)").focus();
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

$("input#CheckAll").on('click', function(event){
	var checked = this.checked;
	if(checked) {
		$("input.DeleteCheckbox").attr("checked", "checked");
	} else {
		$("input.DeleteCheckbox").removeAttr("checked");
	}
});

$(window).on('beforeunload', function() {
      return 'Do you really want to leave?' ;
});

$(document).ready(function() {
	$("#Add").on('click', function() {
 		make_copy(1);
 		return false;
 	});

 	$("#ajaxTransporter").autocomplete({
		source: '<?php echo site_url($this->_clspath.$this->_class.'/getTransporter') ?>',
		minLength: 0
	});
	
	$("#ajaxFromTo").autocomplete({
		source: "<?php echo site_url($this->_clspath.$this->_class.'/getFromTo') ?>",
		minLength: 0
	});

	$("#ajaxRemarks").autocomplete({
		source: "<?php echo site_url($this->_clspath.$this->_class.'/getRemarks') ?>",
		minLength: 0
	});
	
	$("#ajaxContainerNo").autocomplete({
		source: "<?php echo site_url($this->_clspath.$this->_class.'/getUnloadingContainers') ?>",
		minLength: 4,
		focus: function(event, ui) {
			$("#ajaxContainerNo").val(ui.item.number);
			return false;
		},
		select: function(event, ui) {
			$("#ajaxContainerNo").val(ui.item.number);
			$("tr#Blank input:eq(0)").val(ui.item.id);
		 	$("tr#Blank input:eq(3)").val(ui.item.job_id);
		 	$("tr#Blank input:eq(4)").val(ui.item.id2_format);
		 	$("tr#Blank input:eq(6)").val(ui.item.size);
		 	$("tr#Blank input:eq(7)").val(ui.item.vehicle_no);
			return false;
		}
	})
	.data('ui-autocomplete')._renderItem = function(ul, item) {
		return $('<li></li>')
			.data('item.autocomplete', item)
			.append("<a><span class='blue'>" + item.number + "</span> - <span class='red'>Size: " + item.size + " ft</span> - <i>" + item.id2_format + "</i><br /><b>" + item.vehicle_no + "</b></a>")
			.appendTo(ul);
	};
});
</script>