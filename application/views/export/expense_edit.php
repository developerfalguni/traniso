<div id="modal-delete" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><i class="icon-times"></i></button>
				<h3>Confirm Deletion</h3>
			</div>
			<div class="modal-body"><p>Are you sure, you want to DELETE...?</p></div>
			<div class="modal-footer">
				<?php echo anchor($this->_clspath.$this->_class.'/deleteJob/'.$job_id['job_id'].'/'.$id['id'], 'Delete', 'class="btn btn-danger"') ?>
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

		<div class="col-md-2">
			<div class="form-group<?php echo (strlen(form_error('bill_no')) > 0 ? ' has-error' : '') ?>">
				<label class="control-label">Bill No</label>				
				<input type="text" class="form-control form-control-sm" name="bill_no" value="<?php echo $row['bill_no'] ?>" />				
			</div>
		</div>

		<div class="col-md-4">
			<div class="form-group<?php echo (strlen(form_error('supplier_name')) > 0 ? ' has-error' : '') ?>">
				<label class="control-label">Name</label>				
				<input type="hidden" name="supplier_id" value="<?php echo $row['supplier_id'] ?>" id="SupplierID" />
					<input type="text" class="form-control form-control-sm" name="supplier_name" value="<?php echo $supplier['name'] ?>" id="SupplierName" />				
			</div>
		</div>

		<div class="col-md-2">
			<div class="form-group<?php echo (strlen(form_error('pan_no')) > 0 ? ' has-error' : '') ?>">
				<label class="control-label">PAN No</label>				
				<input type="text" class="form-control form-control-sm" name="pan_no" value="<?php echo $supplier['pan_no'] ?>" id="PanNo" />				
			</div>
		</div>

		<div class="col-md-1">
			<div class="form-group<?php echo (strlen(form_error('exchange_rate')) > 0 ? ' has-error' : '') ?>">
				<label class="control-label">Ex. Rate</label>				
				<input type="text" class="form-control form-control-sm Numeric Calculate" name="exchange_rate" value="<?php echo $row['exchange_rate'] ?>" id="ExchangeRate" />				
			</div>
		</div>

		<div class="col-md-1">
			<div class="form-group">
			<label class="control-label">Audited</label>
			<?php echo form_dropdown('audited', getEnumSetOptions($this->_table, 'audited'), $row['audited'], 'class="form-control form-control-sm"');?>
			</div>
		</div>
	</div>	
	<br />

	<div class="row">
		<div class="col-md-12">
			<table class="table table-condensed table-striped table-bordered DataEntry" id="Jobs">
			<thead>
			<tr>
				<th width="24px">No</th>
				<th>Bill Item</th>
				<th>Particulars</th>
				<th>Currency</th>
				<th width="80px">Quantity</th>
				<th width="80px">Rate</th>
				<th width="100px">INR Amount</th>
				<th width="24px" class="aligncenter"><a href="#" class="CheckAll"><i class="icon-trashcan"></i></a></th>
			</tr>
			</thead>

			<tbody>
			<?php 
			$i = 1;
			foreach($details as $r) {
				echo '<tr>
				<td class="aligncenter">' .  $i++ . '</td>
				<td><input type="hidden" name="bill_item_id[' . $r['id'] . ']" value="' . $r['bill_item_id'] . '" />
					<input type="text" class="form-control form-control-sm BillItem" name="bill_item[' . $r['id'] . ']" value="' . $r['bill_item'] . '" /></td>
				<td><input type="text" class="form-control form-control-sm Particulars" name="particulars[' . $r['id'] . ']" value="' . $r['particulars'] . '" /></td>
				<td class="aligncenter Currency">' . $r['currency_code'] . '</td>
				<td><input type="text" class="form-control form-control-sm Numeric Calculate Quantity" name="quantity[' . $r['id'] . ']" value="' . $r['quantity'] . '" /></td>
				<td><input type="text" class="form-control form-control-sm Numeric Calculate Rate" name="rate[' . $r['id'] . ']" value="' . $r['rate'] . '" /></td>
				<td><input type="hidden" class="form-control form-control-sm Taxable" name="taxable[' . $r['id'] . ']" value="' . $r['taxable'] . '" />
					<input type="text" class="form-control form-control-sm Numeric Calculate Amount" name="ib_amount[' . $r['id'] . ']" value="' . $r['amount'] . '" /></td>
				<td class="aligncenter">' . form_checkbox(array('name' => 'delete_id['.$r['id'].']', 'value' => $r['id'], 'checked' => false, 'class' => 'DeleteCheckbox')) . '</td>
			</tr>';
			}
			?>

			<tr class="TemplateRow">
				<td></td>
				<td><input type="hidden" class="form-control form-control-sm Validate" name="new_bill_item_id[]" value="" />
					<input type="text" class="form-control form-control-sm BillItem Validate" value="" /></td>
				<td><input type="text" class="form-control form-control-sm Particulars" name="new_particulars[]" value="" /></td>
				<td class="aligncenter Currency"></td>
				<td><input type="text" class="form-control form-control-sm Numeric Calculate Quantity" name="new_quantity[]" value="0" /></td>
				<td><input type="text" class="form-control form-control-sm Numeric Calculate Rate" name="new_rate[]" value="0" /></td>
				<td><input type="hidden" class="form-control form-control-sm hidden Taxable" name="new_taxable[]" value="" />
					<input type="text" class="form-control form-control-sm Numeric Calculate Amount" name="new_amount[]" value="0" /></td>
				<td class="aligncenter"><button type="submit" class="btn btn-success btn-sm AddButton"><i class="fa fa-plus"></i></button></td>
			</tr>
			</tbody>
			</table>

			<table class="table table-condensed">
			<tr>
				<td colspan="2" rowspan="3" class="aligntop">
					<div class="form-group">
					<label class="control-label">Remarks</label>
					<textarea type="text" class="form-control form-control-sm" name="remarks" rows="3"><?php echo $row['remarks'] ?></textarea>
					</div>
				</td>
				<td class="alignright">Service Tax @: </td>
				<td width="100px"><input type="text" class="form-control form-control-sm Numeric Calculate" name="service_tax" value="<?php echo $row['service_tax'] ?>" id="ServiceTax"/> %</td>
				<td width="80px"><input type="text" class="form-control form-control-sm Numeric" name="service_tax_amount" value="<?php echo $row['service_tax_amount'] ?>" readonly="true" id="ServiceTaxAmount"/></td>
				<td width="24px">Rs.</td>
			</tr>

			<tr>
				<td class="alignright">TDS @: </td>
				<td><input type="text" class="form-control form-control-sm Numeric Calculate" name="tds" value="<?php echo $row['tds'] ?>" id="Tds"/>%</td>
				<td><input type="text" class="form-control form-control-sm Numeric" name="tds_amount" value="<?php echo $row['tds_amount'] ?>" readonly="ture" id="TdsAmount" /></td>
				<td>Rs.</td>
			</tr>

			<tr>
				<td class="alignright">Total :</td>
				<td></td>
				<td><input type="text" class="form-control form-control-sm Numeric" name="amount" value="<?php echo $row['amount'] ?>" readonly="true" id="NetAmount"/></td>
				<td>Rs.</td>
			</tr>
			</table>
		</div>
	</div>
</fieldset>

<div class="form-actions">
	<button type="button" class="btn btn-success" id="Update">Update</button>
	<?php if($row['id'] > 0) {
		// echo '<div class="btn-group">' . 
		// anchor($this->_clspath.$this->_class."/preview/".$row['id'], "Preview", 'class="btn btn-default Popup"') . 
		// anchor($this->_clspath.$this->_class."/preview/" . $row['id'] . "/1", "PDF", 'class="btn btn-default Popup"') . 
		// '</div>&nbsp;
		echo '<a href="#modal-delete" data-toggle="modal" class="btn btn-danger">Delete</a>'; 
	}
	?>
</div>
</form>

<?php echo end_panel() ?>

<script>
function reCalculate() {
	var service_tax = parseFloat($('#ServiceTax').val());
	var tds         = parseFloat($('#Tds').val());
	var ntax_amount = 0;
	var tax_amount  = 0;
	var stax_amount = 0;
	var tds_amount  = 0;
	var net_amount  = 0;

	$('.DataEntry tbody tr').each(function() {
		ex_rate  = parseFloat($('#ExchangeRate').val());
		currency = $(this).find('.Currency').text();
		rate     = parseFloat($(this).find('input.Rate').val());
		quantity = parseFloat($(this).find('input.Quantity').val());
		amount   = parseFloat($(this).find('input.Amount').val());
		taxable  = $(this).find('input.Taxable').val();

		if (quantity > 0 && rate > 0) {
			if (currency === 'INR')
				amount = roundOff(parseFloat(quantity) * parseFloat(rate), 0);
			else
				amount = roundOff((parseFloat(quantity) * parseFloat(rate)) * parseFloat(ex_rate), 0);
		}

		if (taxable === 'Yes')
			tax_amount  = parseFloat(tax_amount) + parseFloat(amount);
		else
			ntax_amount = parseFloat(ntax_amount) + parseFloat(amount);
		$(this).find('input.Amount').val(amount)
	});
	stax_amount = roundOff((tax_amount * service_tax) / 100, 2);
	tds_amount  = roundOff((tax_amount * tds) / 100, 2);
	net_amount  = parseFloat(ntax_amount) + parseFloat(tax_amount) + parseFloat(stax_amount) - parseFloat(tds_amount);

	$('#ServiceTaxAmount').val(stax_amount);
	$('#TdsAmount').val(tds_amount);
	$('#NetAmount').val(roundOff(net_amount, 0));
}

$(document).ready(function(){
	$('body').on('change', '.Calculate', function() {
		reCalculate();
	});

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
			$('#PanNo').val(ui.item.pan_no);
			return false;
		},
		response: function(event, ui) {
			if (ui.content.length === 0) {
				$(this).prevAll('input').val(0);
				$('#PanNo').val('');
			}
		}
	})
	.data('ui-autocomplete')._renderItem = function(ul, item) {
		return $('<li></li>')
			.data('item.autocomplete', item)
			.append('<a>' + item.name + ' - ' + item.pan_no + '</a>')
			.appendTo(ul);
	};

	$('.DataEntry').on('keydown.autocomplete', '.BillItem', function(event, items) {
		var id       = $(this).prevAll('input');
		var pa       = $(this).parent('td').parent('tr').find('.Particulars');
		var currency = $(this).parent('td').parent('tr').find('.Currency');
		var taxable  = $(this).parent('td').parent('tr').find('.Taxable');
		$(this).autocomplete({
			source: '<?php echo site_url('master/bill_item/ajax/Export') ?>',
			minLength: 1,
			focus: function(event, ui) {
				$(id).val(ui.item.id);
				$(this).val(ui.item.name);
				$(pa).val(ui.item.name);
				$(currency).text(ui.item.currency_code);
				$(taxable).text(ui.item.taxable);
				return false;
			},
			select: function(event, ui) {
				$(id).val(ui.item.id);
				$(this).val(ui.item.name);
				$(pa).val(ui.item.name);
				$(currency).text(ui.item.currency_code);
				$(taxable).text(ui.item.taxable);
				return false;
			},
			response: function(event, ui) {
				if (ui.content.length === 0) {
					$(id).val(0);
					$(this).val('');
					$(pa).val('');
					$(currency).text('');
					$(taxable).text('');
				}
			}
		})
		.data('ui-autocomplete')._renderItem = function(ul, item) {
			return $('<li></li>')
				.data('item.autocomplete', item)
				.append('<a>' + item.name + '</a>')
				.appendTo(ul);
		};
	});

	$('.DataEntry').on('keydown.autocomplete', '.Particulars', function(event, items) {
		$(this).autocomplete({
			source: "<?php echo site_url($this->_clspath.$this->_class.'/ajaxJson/expense_details/particulars') ?>",
			minLength: 0
		});
	});
});
</script>