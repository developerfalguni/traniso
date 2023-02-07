
<div id="modal-deleteall" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
		<?php echo form_open($this->_clspath.$this->_class.'/delete/' . $row['job_id']); ?>
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><i class="icon-times"></i></button>
				<h3>Delete All Containers</h3>
			</div>
			<div class="modal-body">
				<p>Are you sure you want to DELETE ALL CONTAINERS...?</p>
				</div>
				<div class="modal-footer">
				<button type="submit" class="btn btn-danger">Delete</button>
			</div>
		</form>
		</div>
	</div>
</div>

<div id="modal-delete" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
		<?php echo form_open($this->_clspath.$this->_class.'/delete/' . $row['job_id'] . '/' . $row['id']); ?>
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><i class="icon-times"></i></button>
				<h3>Delete Container</h3>
			</div>
			<div class="modal-body">
				<p>Are you sure you want to DELETE This Container...?</p>
				</div>
				<div class="modal-footer">
				<button type="submit" class="btn btn-danger">Delete</button>
			</div>
		</form>
		</div>
	</div>
</div>

<?php 
echo form_open($this->uri->uri_string(), array('class' => 'form-horizontal'));
echo form_hidden($id);
?>

<fieldset>
	<div class="row">
		<div class="col-md-3">
			<div class="form-group<?php echo (strlen(form_error('container_type_id')) > 0 ? ' has-error' : '') ?>">
				<label class="control-label">Container Type</label>
				
					<?php echo form_dropdown('container_type_id', getSelectOptions('container_types', 'id', 'CONCAT(size, " ", code, " - ", name)'), $row['container_type_id'], 'class="form-control form-control-sm"') ?>
				
			</div>
		</div>

		<div class="col-md-3">
			<div class="form-group<?php echo (strlen(form_error('number')) > 0 ? ' has-error' : '') ?>">
				<label class="control-label">Number</label>
				
					<input type="text" class="form-control form-control-sm big input-medium" name="number" value="<?php echo $row['number'] ?>" />
				
			</div>
		</div>

		<div class="col-md-3">
			<div class="form-group<?php echo (strlen(form_error('seal')) > 0 ? ' has-error' : '') ?>">
				<label class="control-label">Seal No</label>
				
					<input type="text" class="form-control form-control-sm big input-medium" name="seal" value="<?php echo $row['seal'] ?>" />
				
			</div>
		</div>

		<div class="col-md-3">
			<div class="form-group<?php echo (strlen(form_error('seal_date')) > 0 ? ' has-error' : '') ?>">
				<label class="control-label">Seal Date</label>
				
					<div class="input-group date DatePicker">
						<span class="input-group-addon"><i class="icon-calendar"></i></span>
						<input type="text" class="form-control form-control-sm AutoDate" name="seal_date" value="<?php echo ($row['seal_date'] != '00-00-0000' ? $row['seal_date'] : '') ?>" />
					
				</div>
			</div>
		</div>
	</div>
</fieldset>

<div class="form-actions">
	<button type="submit" class="btn btn-success" id="Update">Update</button>
	<div class="btn-group">
		<a href="#modal-delete" data-toggle="modal" class="btn btn-danger">Delete</a>
		<a href="#modal-deleteall" data-toggle="modal" class="btn btn-danger">Delete All</a>
	</div>
</div>

</form>

<?php echo end_panel(); ?>

<script>
function make_copy(id) {
	var v0 = $("tr#Blank select:eq(0)").val();
	var v1 = $("tr#Blank input:eq(0)").val();
	var v2 = $("tr#Blank input:eq(1)").val();
	var v3 = $("tr#Blank input:eq(2)").val();
	var v4 = $("tr#Blank input:eq(3)").val();
	var v5 = $("tr#Blank input:eq(4)").val();
	var v6 = $("tr#Blank input:eq(5)").val();
	var v7 = $("tr#Blank input:eq(6)").val();
	var v8 = $("tr#Blank input:eq(7)").val();
	var v9 = $("tr#Blank input:eq(8)").val();

	if (!v0 || !v1 || !v2 || !v3) return;
	
	if (id > 1) {
		$("tr#1").clone().insertBefore("tr#Blank").attr("id", id);
	}

	$("tr#Blank input").each(function(index) {
		$(this).val("");
	});
	$("tr#Blank td a").attr("href", "javascript:make_copy("+(id+1)+")");
	$("#Add").unbind('click');
	$("#Add").on('click', function() {
		make_copy(id+1);
		return false;
	});

	$("tr#"+id+" select:eq(0)").val(v0);
	$("tr#"+id+" input:eq(0)").val(v1);
	$("tr#"+id+" input:eq(1)").val(v2);
	$("tr#"+id+" input:eq(2)").val(v3);
	$("tr#"+id+" input:eq(3)").val(v4);
	$("tr#"+id+" input:eq(4)").val(v5);
	$("tr#"+id+" input:eq(5)").val(v6);
	$("tr#"+id+" input:eq(6)").val(v7);
	$("tr#"+id+" input:eq(7)").val(v8);
	$("tr#"+id+" input:eq(8)").val(v9);
	
	$("tr#"+id+" td a").attr("href", "javascript:remove_copy("+id+")");
	$("tr#"+id).removeClass("hide");
	$("tr#Blank input:eq(2)").val(v3);
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
	$("#Add").on('click', function() {
		make_copy(1);
		return false;
	});

	$("#Update").on('click', function() {
		$('form').submit();
	});
});
</script>