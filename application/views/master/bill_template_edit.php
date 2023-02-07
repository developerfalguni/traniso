<div id="modal-copy" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><i class="icon-times"></i></button>
				<h3>Copy Template From</h3>
			</div>
			<div class="modal-body">
				<select class="form-control form-control-sm" id="CopyTemplate">
				<?php foreach ($bill_templates as $bt) {
					echo '<option value="' . $bt['id'] . '">' . $bt['type'] . ' - ' . $bt['cargo_type'] . ' - ' . $bt['product_name'] . ' - ' . $bt['indian_port'] . '</option>';
				} ?>
				</select>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-success" onclick="javascript: copyTemplates()">Copy Now</button>
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
		<fieldset>
			<div class="row">
				<div class="col-md-4">
					<div class="form-group">
						<label class="control-label">Type</label>
						<?php echo form_dropdown('type', getEnumSetOptions($this->_table, 'type'), $row['type'], 'class="form-control form-control-sm" id="Type"') ?>
					</div>
				</div>

				<div class="col-md-4">
					<div class="form-group">
						<label class="control-label">Cargo Type</label>
						<?php echo form_dropdown('cargo_type', getEnumSetOptions($this->_table, 'cargo_type'), $row['cargo_type'], 'class="form-control form-control-sm"') ?>
					</div>
				</div>

				<div class="col-md-4">
					<div class="form-group">
						<label class="control-label">Product</label>
						<?php echo form_dropdown('product_id', array(0=>'')+getSelectOptions('products'), $row['product_id'], 'class="form-control form-control-sm" id="Product"') ?>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-md-4">
					<div class="form-group">
						<label class="control-label">Indian Port</label>
						<?php echo form_dropdown('indian_port_id', getSelectOptions('indian_ports'), $row['indian_port_id'], 'class="form-control form-control-sm" id="Product"') ?>
					</div>
				</div>

				<div class="col-md-8">
					<div class="form-group<?php echo (strlen(form_error('berth_no')) > 0 ? ' has-error' : '') ?>">
						<label class="control-label">Berth No</label>
						
							<?php echo form_dropdown('berth_no[]', $this->office->getBerthNo(), explode(',', $row['berth_no']), 'class="SelectizeKaabar" multiple data-placeholder="Choose Berth No..."') ?>
						
					</div>
				</div>
			</div>

			<div class="form-group">
				<label class="control-label">Remarks</label>
				<textarea class="form-control form-control-sm" name="remarks" rows="2"><?php echo $row['remarks'] ?></textarea>
			</div>

			<table class="table table-condensed table-striped DataEntry">
			<thead>
			<tr>
				<th width="64px">No</th>
				<th width="160px">Code</th>
				<th>Particulars</th>
				<th width="100px">Calc Type</th>
				<th width="140px">Unit Type</th>
				<th width="24px" class="aligncenter"><a href="#" class="CheckAll"><i class="icon-trashcan"></i></a></th>
			</tr>
			</thead>

			<tbody>
			<?php
				$i = 1;
				foreach ($row['bill_items'] as $btid => $bt) {
					echo '<tr>
				<td><input type="text" class="form-control form-control-sm Numeric" name="sr_no[' . $btid . ']" value="' . $bt['sr_no'] . '" size="3" /></td>
				<td><input type="hidden" name="bill_item_id[' . $btid . ']" value="' . $bt['bill_item_id'] . '" />
					<input type="text" class="form-control form-control-sm" value="' . $bt['code'] . '" /></td>
				<td><input type="text" class="form-control form-control-sm" name="particulars[' . $btid . ']" value="' . $bt['particulars'] . '" /></td>
				<td>' . form_dropdown('calc_type[' . $btid . ']', $this->office->getCalcType(), $bt['calc_type'], 'class="form-control form-control-sm"') . '</td>
				<td>' . form_dropdown('unit_type[' . $btid . ']', $this->office->getUnitType(), $bt['unit_type'], 'class="form-control form-control-sm"') . '</td>
				<td class="aligncenter">' . form_checkbox(array('name' => 'delete_id['.$btid.']', 'value' => $btid, 'checked' => false, 'class' => 'DeleteCheckbox')) . '</td>
			</tr>';
					$i = $bt['sr_no'];
				}
			?>
			<tr class="TemplateRow">
				<td><input type="text" class="form-control form-control-sm Numeric Unchanged Increment" name="new_sr_no[]" value="<?php echo $i ?>" /></td>
				<td><input type="hidden" name="new_bill_item_id[]" value="" />
					<input type="text" class="form-control form-control-sm Focus ajaxBillItem" value="" /></td>
				<td><input type="text" class="form-control form-control-sm" name="new_particulars[]" value="" /></td>
				<td><?php echo form_dropdown('new_calc_type[]', $this->office->getCalcType(), 'Fixed', 'class="form-control form-control-sm Unchanged"') ?></td>
				<td><?php echo form_dropdown('new_unit_type[]', $this->office->getUnitType(), 'N/A', 'class="form-control form-control-sm Unchanged"') ?></td>
				<td><button type="submit" class="btn btn-success btn-sm AddButton"><i class="fa fa-plus"></i></button></td>
			</tr>
			</tbody>
			</table>			
		</fieldset>
	</div>

	<div class="card-footer">
		<button type="submit" class="btn btn-success" id="Update">Update</button>
	</div>
</div>
</form>

<script language="JavaScript">
function copyTemplates() {
	$("#modal-copy").modal('hide');
	var id = $("#CopyTemplate").val();
	$.get('<?php echo base_url($this->_clspath.$this->_class."/loadTemplates") ?>/'+id);
}

$(document).ready(function() {
	$('.DataEntry').on('keydown.autocomplete', ".ajaxBillItem", function(event, items) {
		id = $(this).prevAll('input');
		pa = $(this).parent('td').next('td').children('input');
		$(this).autocomplete({
			source: "<?php echo site_url('accounting/ledger/ajaxLedgers/Bill Items') ?>",
			minLength: 1,
			focus: function(event, ui) {
				$(id).val(ui.item.id);
				$(this).val(ui.item.code);
				$(pa).val(ui.item.name);
				return false;
			},
			select: function(event, ui) {
				$(id).val(ui.item.id);
				$(this).val(ui.item.code);
				$(pa).val(ui.item.name);
				return false;
			}
		})
		.data('ui-autocomplete')._renderItem = function(ul, item) {
			return $('<li></li>')
				.data('item.autocomplete', item)
				.append('<a><strong class="blueDark">' + item.code + '</strong> ' + item.name + '</a>')
				.appendTo(ul);
		};
	});
});
</script>