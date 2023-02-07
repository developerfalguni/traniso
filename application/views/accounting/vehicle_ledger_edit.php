
<div id="modal-delete" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><i class="icon-times"></i></button>
				<h3>Confirm Deletion</h3>
			</div>
			<div class="modal-body"><p>Are you sure, you want to DELETE...?</p></div>
			<div class="modal-footer">
				<?php echo anchor($this->_clspath.$this->_class."/delete/".$id['id'], 'Delete', 'class="btn btn-danger"') ?>
			</div>
		</div>
	</div>
</div>

<?php
echo form_open($this->uri->uri_string(), 'id="MainForm"');
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
		<div class="row">
			<div class="col-md-6">
				<fieldset>
					<div class="form-group<?php echo (strlen(form_error('vehicle_id')) > 0 ? ' has-error' : '') ?>">
						<label class="control-label">Choose Vehicle</label>
						<input type="hidden" name="vehicle_id" value="<?php echo $row['vehicle_id'] ?>" id="VehicleID" />
						<input type="text" class="form-control form-control-sm" value="<?php echo $registration_no ?>" id="ajaxVehicle" />
					</div>

					<div class="form-group<?php echo (strlen(form_error('code')) > 0 ? ' has-error' : '') ?>">
						<label class="control-label">Code</label>
						<input type="text" class="form-control form-control-sm" name="code" value="<?php echo $row['code'] ?>" size="6" maxlength="5" id="Code" />
					</div>
					
					<div class="form-group<?php echo (strlen(form_error('name')) > 0 ? ' has-error' : '') ?>">
						<label class="control-label">Name</label>
						<input type="text" class="form-control form-control-sm" name="name" value="<?php echo $row['name'] ?>" />
					</div>

					<div class="form-group<?php echo (strlen(form_error('category')) > 0 ? ' has-error' : '') ?>">
						<label class="control-label">Category</label>
						<?php echo form_dropdown('category', $categories, $row['category'], 'class="form-control form-control-sm"') ?>
					</div>

					<div class="form-group<?php echo (strlen(form_error('account_group_id')) > 0 ? ' has-error' : '') ?>">
						<label class="control-label">Accounting Head</label>
						<?php echo form_dropdown('account_group_id', getSelectOptions('account_groups', 'id', 'name'), $row['account_group_id'], 'class="form-control form-control-sm"') ?>
					</div>

					<div class="form-group<?php echo (strlen(form_error('group_name')) > 0 ? ' has-error' : '') ?>">
						<label class="control-label">Group Name</label>
						<input type="text" class="form-control form-control-sm" name="group_name" value="<?php echo $row['group_name'] ?>" id="GroupName" />
					</div>
					
					<div class="form-group<?php echo (strlen(form_error('parent_ledger_id')) > 0 ? ' has-error' : '') ?>">
						<label class="control-label">Parent</label>
						<input type="hidden" name="parent_ledger_id" value="<?php echo $row['parent_ledger_id'] ?>" id="Parent_ID" />
						<input type="text" class="form-control form-control-sm" value="<?php echo $parent_code_name ?>" id="ajaxParent" />
					</div>

					<div class="form-group<?php echo (strlen(form_error('opening_balance')) > 0 ? ' has-error' : '') ?>">
						<label class="control-label">Opening Balance</label>
						<div class="form-inline">
							<input type="text" class="form-control form-control-sm Numeric" name="opening_balance" value="<?php echo $row['opening_balance'] ?>" />
							<?php echo form_dropdown('dr_cr', getEnumSetOptions($this->_table, 'dr_cr'), $row['dr_cr'], 'class="form-control form-control-sm"') ?>
						</div>
					</div>

					<div class="form-group<?php echo (strlen(form_error('tds_class_id')) > 0 ? ' has-error' : '') ?>">
						<label class="control-label">TDS Deductee Class</label>
						<?php echo form_dropdown('tds_class_id', array(0=>'')+getSelectOptions('tds_classes', 'id', 'name', 'WHERE type = "Deductee"'), $row['tds_class_id'], 'class="form-control form-control-sm"') ?>
					</div>
				</fieldset>
			</div>
		</div>
	</div>

	<div class="card-footer">
		<button type="submit" class="btn btn-success" id="Update">Update</button>
		<a href="#modal-delete" data-toggle="modal" class="btn btn-danger pull-right">Delete</a>
	</div>
</div>

</form>

<script>
$(document).ready(function() {
	$("#ajaxVehicle").autocomplete({
		source: '<?php echo site_url('master/vehicle/ajaxVehicle') ?>',
		minLength: 2,
		focus: function(event, ui) {
			$("#ajaxVehicle").val( ui.item.registration_no);
			return false;
		},
		select: function(event, ui) {
			$("#ajaxVehicle").val(ui.item.registration_no);
			$("#VehicleID").val(ui.item.id);
	
			return false;
		},
		response: function(event, ui) {
            if (ui.content.length === 0) {
                $("#ajaxVehicle").val('');
				$("#VehicleID").val(0);
            }
        }
	})
	.data('ui-autocomplete')._renderItem = function(ul, item) {
		return $('<li></li>')
			.data('item.autocomplete', item)
			.append("<a>" + item.registration_no + "</a>")
			.appendTo(ul);
	};

	$("#GroupName").autocomplete({
		source: '<?php echo site_url($this->_clspath.$this->_class.'/ajaxJson/ledgers/group_name') ?>',
		minLength: 0
	});

	$("#ajaxParent").autocomplete({
		source: '<?php echo site_url($this->_clspath.$this->_class.'/ajaxParent/' . $category . '/'.$id['id']) ?>',
		minLength: 2,
		focus: function(event, ui) {
			$("#Parent_ID").val(ui.item.id);
			$("#ajaxParent").val(ui.item.code + " - " + ui.item.name);
			$("#GroupName").val(ui.item.group_name);
			return false;
		},
		select: function(event, ui) {
			$("#Parent_ID").val(ui.item.id);
			$("#ajaxParent").val(ui.item.code + " - " + ui.item.name);
			$("#GroupName").val(ui.item.group_name);
			return false;
		},
		response: function(event, ui) {
            if (ui.content.length === 0) {
				$("#Parent_ID").val(0);
                $("#ajaxParent").val('');
                $("#GroupName").val('');
            }
        }
	})
	.data('ui-autocomplete')._renderItem = function(ul, item) {
		return $('<li></li>')
			.data('item.autocomplete', item)
			.append('<a><strong class="blueDark">' + item.code + '</strong> ' + item.name + '</a>')
			.appendTo(ul);
	};
});
</script>