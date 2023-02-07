<style>
tr.New td { background-color: #efe !important; }
</style>

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
echo start_panel($page_title, anchor($this->_clspath.$this->_class.'/index/'.$job_id['job_id'], '<span class="icon"><i class="icon-list"></i></span>'), 'nopadding', '<div class="buttons">
	<a href="' . base_url($this->_clspath.$this->_class.'/edit/'.$job_id['job_id'].'/0') . '" class="btn btn-sm btn-success" id="NewVoucher"><i class="fa fa-plus"></i> Add New</a>
	</div>');
echo form_open($this->uri->uri_string(), 'id="MainForm"');
echo form_hidden($id);
?>
<fieldset>
	<div class="row">
		<div class="col-md-2">
			<div class="form-group <?php echo (strlen(form_error('date')) > 0 ? 'has-error' : '') ?>">
				<label class="control-label">Date</label>
				<div class="input-group date DatePicker">
					<span class="input-group-addon"><i class="icon-calendar"></i></span>
					<input type="text" class="form-control form-control-sm AutoDate" name="date" value="<?php echo ($row['date'] != '00-00-0000' ? $row['date'] : '') ?>" />
				</div>
			</div>
		</div>

		<div class="col-md-4">
			<div class="form-group<?php echo (strlen(form_error('supplier_name')) > 0 ? ' has-error' : '') ?>">
				<label class="control-label">Name</label>
				
					<input type="hidden" name="supplier_id" value="<?php echo $row['supplier_id'] ?>" id="SupplierID" />
					<input type="text" class="form-control form-control-sm" name="supplier_name" value="<?php echo $supplier_name ?>" id="SupplierName" />
				
			</div>
		</div>

		<div class="col-md-6">
			<div class="form-group">
				<label class="control-label">Remarks</label>
				<input type="text" class="form-control form-control-sm" name="remarks" value="<?php echo $row['remarks'] ?>" />
			</div>
		</div>		
	</div>

	<div class="row">		
		<div class="col-md-2">
			<div class="form-group">
				<label class="control-label">Mode</label>
				<?php echo form_dropdown('mode', getEnumSetOptions('payments', 'mode'), $row['mode'], 'class="form-control form-control-sm"') ?>
			</div>
		</div>

		<div class="col-md-4">
			<div class="form-group">
				<label class="control-label">Bank</label>
				<?php echo form_dropdown('bank', array(0=>'')+getSelectOptions('banks'), $row['bank_id'], 'class="form-control form-control-sm"') ?>
			</div>
		</div>

		<div class="col-md-2">
			<div class="form-group">
				<label class="control-label">Cheque No</label>
				<input type="text" class="form-control form-control-sm" name="cheque_no" value="<?php echo $row['cheque_no'] ?>" />
			</div>
		</div>

		<div class="col-md-2">
			<div class="form-group">
				<label class="control-label">Amount</label>
				<input type="text" class="form-control form-control-sm Numeric" name="amount" value="<?php echo $row['amount'] ?>" />
			</div>
		</div>
	</div>
	<br />

	<div class="row">
		<div class="col-md-12">
			<table class="table table-condensed table-striped table-bordered DataEntry" id="Result">
			<thead>
			<tr>
				<th width="24px">No</th>
				<th>Bill No</th>
				<th>Bill Date</th>
				<th width="100px">Bill Amount</th>
				<th width="100px">Paid</th>
				<th width="24px" class="aligncenter"><a href="#" class="CheckAll"><i class="icon-trashcan"></i></a></th>
			</tr>
			</thead>

			<tbody>
			<?php 
			$i = 1;
			foreach($details as $r) {
				echo '<tr>
				<td class="aligncenter">' .  $i++ . '</td>
				<td><input type="hidden" name="expense_id[' . $r['id'] . ']" value="' . $r['expense_id'] . '" />' . $r['bill_no'] . '</td>
				<td class="aligncenter">' . $r['date'] . '</td>
				<td class="alignright">' . $r['amount'] . '</td>
				<td><input type="text" class="form-control form-control-sm Numeric Calculate Paid" name="paid[' . $r['id'] . ']" value="' . $r['paid'] . '" /></td>
				<td class="aligncenter">' . form_checkbox(array('name' => 'delete_id['.$r['id'].']', 'value' => $r['id'], 'checked' => false, 'class' => 'DeleteCheckbox')) . '</td>
			</tr>';
			}

			foreach($pending as $r) {
				echo '<tr class="New">
				<td class="aligncenter"><input type="checkbox" name="new_expense_id[]" value="' . $r['id'] . '" /></td>
				<td>' . $r['bill_no'] . '</td>
				<td class="aligncenter">' . $r['date'] . '</td>
				<td class="alignright">' . $r['amount'] . '</td>
				<td><input type="text" class="form-control form-control-sm Numeric" name="new_paid[]" value="' . $r['amount'] . '" /></td>
				<td></td>
			</tr>';
			}
			?>
			</tbody>
			</table>
		</div>
	</div>
</fieldset>

<div class="form-actions">
	<button type="button" class="btn btn-success" id="Update">Update</button>
</div>
</form>

<?php echo end_panel() ?>

<script>
$(document).ready(function(){
	$('#SupplierName').autocomplete({
		source: '<?php echo site_url('master/supplier/ajax') ?>',		
		minLength: 0,
		focus: function(event, ui) {
			$(this).val(ui.item.name);
			return false;
		},
		select: function(event, ui) {
			$(this).prevAll('input').val(ui.item.id);
			$(this).val(ui.item.name);
			$('.DataEntry').find('tr.New').remove();
			$.get('<?php echo base_url($this->_clspath.$this->_class."/loadPendingExpense/".$job_id['job_id']) ?>/'+ui.item.id);
			return false;
		},
		response: function(event, ui) {
			if (ui.content.length === 0) {
				$(this).prevAll('input').val(0);
			}
		}
	})
	.data('ui-autocomplete')._renderItem = function(ul, item) {
		return $('<li></li>')
			.data('item.autocomplete', item)
			.append('<a>' + item.name + ' - ' + item.pan_no + '</a>')
			.appendTo(ul);
	};
});
</script>