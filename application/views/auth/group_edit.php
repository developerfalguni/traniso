<?php
echo form_open($this->uri->uri_string(), 'id="MainForm"');
echo form_hidden($id);
?>

<div class="row">
	<div class="col-md-8">

<div class="card card-default">
	<div class="card-header">
		<span class="card--icon"><?php echo anchor($this->_clspath.$this->_class, '<i class="icon-list"></i>') ?></span>
		<span class="card--links"><?php echo anchor($this->_clspath.$this->_class.'/edit/0', '<i class="fa fa-plus"></i> Add', 'class="btn btn-xs btn-success"'); ?></span>
		<h3 class="card-title"><?php echo $page_title ?></h3>
	</div>
	
	<div class="card-body">
		<fieldset>
			<div class="form-group<?php echo (strlen(form_error('name')) > 0 ? ' has-error' : '') ?>">
				<label class="control-label">Name</label>
				
					<input type="text" class="form-control form-control-sm" name="name" value="<?php echo $row['name'] ?>" id="Name" />
				
			</div>
		</fieldset>

		<div class="form-actions">
			<button type="submit" class="btn btn-success" id="Update">Update</button>
		</div>
		</form>

		<?php
		if ($id['id'] > 0) :
			echo form_open($this->_clspath.'/group/permission', 'class="form-horizontal"');
			echo form_hidden($id);
		?>

		<fieldset>
			<legend>Permissions</legend>
			<table class="table table-condensed table-striped">
			<thead>
			<tr>
				<th>ID</th>
				<th width="200">Content</th>
				<th style="text-align:center;"><span id="ReadAll" rel="tooltip" data-original-title="Click to switch all On / Off" style="cursor:pointer;">Read</span></th>
				<th style="text-align:center;"><span class="aligncenter" id="CreateAll" rel="tooltip" data-original-title="Click to switch all On / Off" style="cursor:pointer;">Create</span></th>
				<th style="text-align:center;"><span class="aligncenter" id="UpdateAll" rel="tooltip" data-original-title="Click to switch all On / Off" style="cursor:pointer;">Update</span></th>
				<th style="text-align:center;"><span class="aligncenter" id="DeleteAll" rel="tooltip" data-original-title="Click to switch all On / Off" style="cursor:pointer;">Delete</span></th>
				<th style="text-align:center;"><input type="checkbox" name="CheckAll" id="CheckAll" rel="tooltip" title="Tick all, Permissions will be removed after Update..." /></th>
			</tr>
			</thead>

			<tbody>
			<?php
				foreach($list['data'] as $perm_row) {
					echo "<tr>
						<td>" . $perm_row['gcid'] . "</td>
						<td>".$perm_row['name']."</td>
						<td style=\"text-align:center;\"><input type=\"checkbox\" name=\"perm[" . $perm_row['gcid'] . "][1]\" value=\"2\" class=\"OnOff Read\" " . ($perm_row['can_read'] == TRUE ? 'checked="checked"' : null) . " /></td>
						<td style=\"text-align:center;\"><input type=\"checkbox\" name=\"perm[" . $perm_row['gcid'] . "][2]\" value=\"1\" class=\"OnOff Create\" " . ($perm_row['can_create'] == TRUE ? 'checked="checked"' : null) . " /></td>
						<td style=\"text-align:center;\"><input type=\"checkbox\" name=\"perm[" . $perm_row['gcid'] . "][4]\" value=\"4\" class=\"OnOff Update\" " . ($perm_row['can_update'] == TRUE ? 'checked="checked"' : null) . " /></td>
						<td style=\"text-align:center;\"><input type=\"checkbox\" name=\"perm[" . $perm_row['gcid'] . "][8]\" value=\"8\" class=\"OnOff Delete\" " . ($perm_row['can_delete'] == TRUE ? 'checked="checked"' : null) . " /></td>
						<td style=\"text-align:center;\">", form_checkbox(array('name' => 'delete_id['.$perm_row['gcid'].']', 'value' => $perm_row['gcid'], 'checked' => false, 'class' => 'DeleteCheckbox', 'rel' => 'tooltip', 'data-original-title' => 'If ticked, Permission will be removed after Update...')), "</td>
					</tr>";
				}
			?>
			</tbody>
			</table>
		</fieldset>
	</div>

	<div class="card-footer">
		<button class="btn btn-success" type="submit">Update Permissions</button>
	</div>
</div>

	</div>

<?php endif; ?>

	<div class="col-md-4"><?php 
		if($id['id'] > 0 && isset($formselect)) { $this->load->view($this->_clspath.'formselect', $formselect); } ?>
	</div>
</div>

</form>

<script type="text/javascript" language="JavaScript">
<!--
$("input#CheckAll").on('click', function(event){
	var checked = this.checked;
	if(checked) {
		$("input.DeleteCheckbox").attr("checked", "checked");
	} else {
		$("input.DeleteCheckbox").removeAttr("checked");
	}
});

$("#ReadAll").toggle(
	function(event){
		$(".Read").attr("checked", "checked");
	},
	function(event){
		$(".Read").removeAttr("checked");
});

$("#CreateAll").toggle(
	function(event){
		$(".Create").attr("checked", "checked");
	},
	function(event){
		$(".Create").removeAttr("checked");
});

$("#UpdateAll").toggle(
	function(event){
		$(".Update").attr("checked", "checked");
	},
	function(event){
		$(".Update").removeAttr("checked");
});

$("#DeleteAll").toggle(
	function(event){
		$(".Delete").attr("checked", "checked");
	},
	function(event){
		$(".Delete").removeAttr("checked");
});

$(document).ready(function() {
	$('table.List tr td').unbind('click');
	
	$('input:checkbox.OnOff').checkbox();
});

// -->
</script>
