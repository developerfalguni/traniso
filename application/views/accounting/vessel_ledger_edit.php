
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
			<div class="form-group<?php echo (strlen(form_error('vessel_id')) > 0 ? ' has-error' : '') ?>">
				<label class="control-label col-md-2">Master Vessel</label>
				<div class="col-md-10">
					<input type="hidden" name="vessel_id" value="<?php echo $row['vessel_id'] ?>" id="VesselID" />
					<input type="text" class="form-control form-control-sm" value="<?php echo $vessel_name ?>" size="30" id="ajaxVessel" />
					<span class="help-block">* Choose Master Vessel</span>
				</div>
			</div>

			<div class="form-group<?php echo (strlen(form_error('code')) > 0 ? ' has-error' : '') ?>">
				<label class="control-label col-md-2">Code</label>
				<div class="col-md-10">
					<input type="text" class="form-control form-control-sm input-small" name="code" value="<?php echo $row['code'] ?>" maxlength="10" id="Code" />
					<span class="help-block">* Vessel Code max 5 chars.</span>
				</div>
			</div>
			
			<div class="form-group<?php echo (strlen(form_error('name')) > 0 ? ' has-error' : '') ?>">
				<label class="control-label col-md-2">Name</label>
				<div class="col-md-10">
					<input type="text" class="form-control form-control-sm" name="name" value="<?php echo $row['name'] ?>" />
					<span class="help-block">* Enter Vessel Fullname</span>
				</div>
			</div>

			<div class="form-group<?php echo (strlen(form_error('category')) > 0 ? ' has-error' : '') ?>">
				<label class="control-label col-md-2">Category</label>
				<div class="col-md-10">
					<?php echo form_dropdown('category', $categories, $row['category'], 'class="form-control form-control-sm"') ?>
				</div>
			</div>

			<div class="form-group<?php echo (strlen(form_error('account_group_id')) > 0 ? ' has-error' : '') ?>">
				<label class="control-label col-md-2">Accounting Head</label>
				<div class="col-md-10">
					<?php echo form_dropdown('account_group_id', getSelectOptions('account_groups', 'id', 'name'), $row['account_group_id'], 'class="form-control form-control-sm"') ?>
				</div>
			</div>

			<div class="form-group<?php echo (strlen(form_error('opening_balance')) > 0 ? ' has-error' : '') ?>">
				<label class="control-label col-md-2">Opening Balance</label>
				<div class="col-md-10">
					<div class="form-inline">
						<input type="text" class="form-control form-control-sm Numeric" name="opening_balance" value="<?php echo $row['opening_balance'] ?>" />
						<?php echo form_dropdown('dr_cr', getEnumSetOptions($this->_table, 'dr_cr'), $row['dr_cr'], 'class="form-control form-control-sm"') ?>
					</div>
				</div>
			</div>
		</fieldset>
	</div>

	<div class="card-footer">
		<button type="submit" class="btn btn-success" id="Update">Update</button>
		<a href="#modal-delete" data-toggle="modal" class="btn btn-danger pull-right">Delete</a>
	</div>
</div>

</form>

<script language="JavaScript">
$(document).ready(function() {

	$("#ajaxVessel").autocomplete({
		source: '<?php echo site_url('/master/vessel/ajax') ?>',
		minLength: 2,
		focus: function(event, ui) {
			$("#VesselID").val(ui.item.id);
			$("#ajaxVessel").val(ui.item.prefix + ' ' + ui.item.name + ' ' + ui.item.voyage_no);
			return false;
		},
		select: function(event, ui) {
			$("#VesselID").val(ui.item.id);
			$("#ajaxVessel").val(ui.item.prefix + ' ' + ui.item.name + ' ' + ui.item.voyage_no);	
			return false;
		},
		response: function(event, ui) {
            if (ui.content.length === 0) {
				$("#VesselID").val(0);
                $("#ajaxVessel").val('');
            }
        }
	})
	.data('ui-autocomplete')._renderItem = function(ul, item) {
		return $('<li></li>')
			.data('item.autocomplete', item)
			.append("<a>" + item.prefix + " " + item.name + " <span class='tiny orange'>" + item.voyage_no + "</span></a>")
			.appendTo(ul);
	};
});
</script>
