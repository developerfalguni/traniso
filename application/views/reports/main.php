
<div class="row">
	<div class="col-md-12">

<?php 
echo start_panel($page_title, '', 'nopadding');
echo form_open($this->uri->uri_string(), 'class="form-horizontal" target="_blank"'); 
?>
<div class="row">
	<div class="col-md-8">

<fieldset>
	<div class="row">
		<div class="col-md-8">
			<div class="form-group">
				<label class="control-label">Date Range</label>
				<input type="hidden" name="from_date" value="<?php echo $from_date ?>" id="FromDate" />
				<input type="hidden" name="to_date"   value="<?php echo $to_date ?>" id="ToDate" />
				<div id="ReportRange" class="pull-left" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc">
					<i class="icon-calendar icon-large"></i> <span></span> <b class="caret"></b>
				</div>
			</div>
		</div>

		<div class="col-md-4">
			<div class="form-group">
				<label class="control-label">Type</label>
				<?php echo form_dropdown('type', array('Summary' => 'Summary', 'Detail' => 'Detail'), 'Summary', 'class="form-control form-control-sm" id="Type"'); ?>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-12">
			<div class="form-group">
				<label class="control-label">Search</label>
				<input type="text" class="form-control form-control-sm" name="search" value="<?php echo (isset($search) ? $search : '') ?>" id="Search" />
			</div>
		</div>
	</div>
</fieldset>

<div class="form-actions">
	<button type="submit" class="btn btn-primary" id="SearchButton"><i class="fa fa-search"></i> Search</button>&nbsp;
	<div class="btn-group">
		<?php echo anchor($this->_clspath.$this->_class."/preview", 'Preview', 'class="form-control form-control-sm"') ?>
		<?php echo anchor($this->_clspath.$this->_class."/excel", 'Excel', 'class="btn btn-warning Popup"') ?>
	</div>
</div>
</form>
	</div>

	<div class="col-md-4">
		<table class="table table-condensed table-striped ">
		<thead>
		<tr>
			<th width="24px" class="aligncenter"><a href="#" class="CheckAll"><i class="icon-check"></i></a></th>
			<th>Field Name</th>
		</tr>
		</thead>

		<tfoot>
		<tr>
			<th width="24px" class="aligncenter"><a href="#" class="CheckAll"><i class="icon-check"></i></a></th>
			<th>Field Name</th>
		</tr>
		</tfoot>

		<tbody id="Fields">
		<?php foreach ($criteria['Summary'] as $f => $v) {
			echo '<tr>
			<td class="aligncenter ignoreClicks">' . form_checkbox(array('name' => 'fields[]', 'value' => $f, false, 'class' => 'Checkbox')) . '</td>
			<td>' . $v . '</td>
		</tr>';
		} ?>
		</tbody>
		</table>
	</div>

</div>

<?php echo end_panel(); ?>



<script>
var received = 1;

function CheckAll() {
	if(received) {
		$("input.Checkbox").attr("checked", "checked");
		received = 0;
	} else {
		$("input.Checkbox").removeAttr("checked");
		received = 1;
	}
}

function make_copy(id) {
	var v0 = $("tr#Blank select:eq(0)").val();
	var v1 = $("tr#Blank input:eq(0)").val();

	if (!v0 || !v1) return;
	
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

function ReloadFields() {
	var unit = $("#Type").val();
	if (unit == "Summary") {
		var newFields = '<tbody id="Fields"><?php foreach ($criteria['Summary'] as $f => $v) {
			echo '<tr><td class="aligncenter ignoreClicks"><input type="checkbox" name="fields[]" value="'.$f.'" class="Checkbox"></td><td>' . $v . '</td></tr>';
		} ?></tbody>';
	}
	else {
		var newFields = '<tbody id="Fields"><?php foreach ($criteria['Detail'] as $f => $v) {
			echo '<tr><td class="aligncenter ignoreClicks"><input type="checkbox" name="fields[]" value="'.$f.'" class="Checkbox"></td><td>' . $v . '</td></tr>';
		} ?></tbody>';
	}
	$('#Fields').replaceWith(newFields);
}

$(document).ready(function() {
	$("#Type").on("change", function(){
		ReloadFields();
	});

	<?php echo 'dateRangePicker({
		years:     ['.$years[0].', '.$years[1].'],
		from_date: \''.convDate($from_date).'\',
		to_date:   \''.convDate($to_date).'\'
	});' ?>});
</script>