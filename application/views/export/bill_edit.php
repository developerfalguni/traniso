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

<div id="modal-templates" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><i class="icon-times"></i></button>
				<h3>Add Templates</h3>
			</div>
			<div class="modal-body">
				<select name="bill_template_id" size="10" class="form-control form-control-sm" id="TemplateID">
				<?php foreach ($bill_templates as $r)
					echo "<option value=\"" . $r['id'] . "\">" . $r['name'] . "</option>";
				?>
				</select>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-success" id="Template">Load Template</button>
			</div>
		</div>
	</div>
</div>

<?php
echo start_panel($page_title, anchor($this->_clspath.$this->_class.'/index/'.$job_id['job_id'], '<span class="icon"><i class="icon-list"></i></span>'), 'nopadding', '<div class="buttons">
	<a href="' . base_url($this->_clspath.$this->_class.'/edit/'.$job_id['job_id'].'/0') . '" class="btn btn-xs btn-success"><i class="fa fa-plus"></i> Add New</a>
	</div>');
echo form_open($this->uri->uri_string(), 'id="MainForm"');
echo form_hidden($id);
?>
<fieldset>
	<div class="row">
		<div class="col-md-1">
			<div class="form-group">
				<label class="control-label">Type</label>
				<?php echo form_dropdown('type', getEnumSetOptions('bills', 'type'), $row['type'], 'class="form-control form-control-sm" id="Type"'); ?>
			</div>
		</div>

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
			<div class="form-group<?php echo (strlen(form_error('id2_format')) > 0 ? ' has-error' : '') ?>">
				<label class="control-label">Bill No</label>				
				<input type="text" class="form-control form-control-sm" name="id2_format" value="<?php echo $row['id2_format'] ?>" />				
			</div>
		</div>

		<div class="col-md-4">
			<div class="form-group<?php echo (strlen(form_error('party_name')) > 0 ? ' has-error' : '') ?>">
				<label class="control-label">Party Name</label>				
				<input type="hidden" name="party_id" value="<?php echo $row['party_id'] ?>" id="PartyID" />
				<input type="text" class="form-control form-control-sm" name="party_name" value="<?php echo $party_name ?>" id="PartyName" />				
			</div>
		</div>

		<div class="col-md-2">
			<div class="form-group">
				<label class="control-label">Site</label>
				<?php echo form_dropdown('party_site_id', array(0=>'')+getSelectOptions('party_sites', 'id', 'name', 'WHERE party_id = '.$row['party_id']), $row['party_site_id'], 'class="form-control form-control-sm"'); ?>
			</div>
		</div>

		<div class="col-md-1">
			<div class="form-group<?php echo (strlen(form_error('exchange_rate')) > 0 ? ' has-error' : '') ?>">
				<label class="control-label">Ex. Rate</label>				
				<input type="text" class="form-control form-control-sm Numeric Calculate" name="exchange_rate" value="<?php echo $row['exchange_rate'] ?>" id="ExchangeRate" />				
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-11">
			<div class="form-group">
				<label class="control-label">Product</label>
				<input type="text" class="form-control form-control-sm" name="product_name" value="<?php echo $row['product_name'] ?>" id="ProductName" />
			</div>
		</div>
		
		<div class="col-md-1">
			<div class="form-group">
			<label class="control-label">Audited</label>
			<?php echo form_dropdown('audited', getEnumSetOptions($this->_table, 'audited'), $row['audited'], 'class="form-control form-control-sm"');?>
			</div>
		</div>
	<br />

	<?php if($row['id'] > 0) : ?>
		<?php if($row['type'] == 'Transportation') {
			echo '<table class="table table-condensed table-striped table-bordered DataEntry Sortable" id="Jobs">
			<thead>
			<tr>
				<th width="24px"></th>
				<th width="40px">SrNo</th>
				<th width="110px">Date</th>
				<th>Vehicle No</th>
				<th>LR No</th>
				<th>Cont. No</th>
				<th>From</th>
				<th>To</th>
				<th>Cargo</th>
				<th>Qty.</th>
				<th>U.Qty.</th>
				<th>Rate</th>
				<th>Adv.</th>
				<th>Amount</th>
				<th width="24px" class="aligncenter"><a href="#" class="CheckAll"><i class="icon-trashcan"></i></a></th>
			</tr>
			</thead>

			<tbody>';
				$i = 1;
				$sr_no = 0;
				$total = array(
					'amount'  => 0,
					'advance' => 0,
					'total'   => 0,
				);
				foreach ($bill_detail_transportation as $r) {
					$total['amount']  += $r['amount'];
					$total['advance'] += $r['advance'];
					$total['total']   = $total['amount'] + $total['advance'];

					echo '<tr>
				<td class="aligncenter SortHandle"><i class="icon-bars"></i></td>
				<td class="aligncenter"><input type="text" class="form-control form-control-sm Numeric Validate Focus" name="sr_no[' . $r['id'] . ']" value="' . $r['sr_no'] . 
				'" /></td>
				<td>' . $r['date']  . '</td>
				<td>' . $r['vehicle_no'] . '</td>
				<td>' . $r['lr_no'] . '</td>
				<td>' . $r['container_no'] . '</td>
				<td>' . $r['from_location'] . '</td>
				<td>' . $r['to_location'] . '</td>
				<td>' . $r['cargo'] . '</td>
				<td>' . $r['quantity'] . '</td>
				<td>' . $r['quantity_unit'] . '</td>
				<td><input type="text" class="form-control form-control-sm Numeric" name="rate[' . $r['id'] . ']" value="' . $r['rate'] . '" /></td>
				<td class="Numeric">' . $r['advance'] . '</td>
				<td><input type="hidden" class="form-control form-control-sm Taxable" name="taxable[' . $r['id'] . ']" value="' . $r['taxable'] . '" />
					<input type="text" class="form-control form-control-sm Numeric Amount" name="amount[' . $r['id'] . ']" value="' . $r['amount'] . '" readonly="readonly"/></td>
				<td class="aligncenter">' . form_checkbox(array('name' => 'delete_id['.$r['id'].']', 'value' => $r['id'], 'checked' => false, 'class' => 'DeleteCheckbox')) . '</td>
			</tr>';
					$sr_no = $r['sr_no'];
				}
			
			echo '<tr class="TemplateRow">
				<td class="aligncenter SortHandle"><i class="icon-bars"></i></td>
				<td><input type="text" class="form-control form-control-sm Numeric Validate Unchanged Increment" name="new_sr_no[]" value="'. ($sr_no + 1) .'" /></td>
				<td><div class="input-group date DatePicker"><span class="input-group-addon"><i class="icon-calendar"></i></span><input type="text" class="form-control form-control-sm AutoDate" name="new_date[]" value="'.date('d-m-Y').'" /></div></td>
				<td><input type="text" class="form-control form-control-sm Validate" name="new_vehicle_no[]" value="" /></td>
				<td><input type="text" class="form-control form-control-sm" name="new_lr_no[]" value="" /></td>
				<td><input type="text" class="form-control form-control-sm Validate" name="new_container_no[]" value="" /></td>
				<td><input type="hidden" class="form-control form-control-sm Validate FromLocationID" name="new_from_location_id[]" value="" />
					<input type="text" class="form-control form-control-sm Validate FromLocation" name="new_from_location[]" value="" /></td>
				<td><input type="hidden" class="form-control form-control-sm Validate ToLocationID" name="new_to_location_id[]" value="" />
					<input type="text" class="form-control form-control-sm Validate ToLocation" name="new_to_location[]" value="" /></td>
				<td><input type="text" class="form-control form-control-sm Validate" name="new_cargo[]" value="" /></td>
				<td><input type="text" class="form-control form-control-sm Numeric" name="new_quantity[]" value="0" /></td>
				<td><input type="text" class="form-control form-control-sm Numeric" name="new_quantity_unit[]" value="0" /></td>
				<td><input type="text" class="form-control form-control-sm Numeric" name="new_rate[]" value="0" /></td>
				<td><input type="text" class="form-control form-control-sm Numeric" name="new_advance[]" value="0" /></td>
				<td><input type="hidden" class="form-control form-control-sm hidden Taxable" name="new_taxable[]" value="" />
					<input type="text" class="form-control form-control-sm Numeric Amount" name="new_amount[]" value="0" /></td>
				<td class="aligncenter"><button type="submit" class="btn btn-success btn-sm AddButton"><i class="fa fa-plus"></i></button></td>
			</tr>
			</tbody>
			</table>

			<table class="table table-condensed">
			<tr>
				<td colspan="2" rowspan="4" class="aligntop">
					<div class="form-group">
					<label class="control-label">Remarks</label>
					<textarea type="text" class="form-control form-control-sm" name="remarks" rows="4">'. $row['remarks'] .'</textarea>
					</div>
				</td>
				
				<td class="alignright">Total : </td>
				<td width="100px">'. inr_format($total['total']) .'</td>				
			</tr>
			<tr>
				<td class="alignright">Advance : </td>
				<td width="100px">'. inr_format($total['advance']) .'</td>
			</tr>
			<tr>
				<td class="alignright">Net Amount : </td>				
				<td width="100px">'. inr_format($total['amount']) .'</td>
			</tr>
			</table>';
		}
		else {			
			echo '<table class="table table-condensed table-striped table-bordered DataEntry Sortable" id="Jobs">
			<thead>
			<tr>
				<th width="24px"></th>
				<th width="32px">Sr No</th>
				<th>Bill Item</th>
				<th>Particulars</th>
				<th width="60px">Currency</th>
				<th width="60px">Units</th>
				<th width="60px">Price</th>
				<th width="80px">Amount</th>
				<th width="24px" class="aligncenter"><a href="#" class="CheckAll"><i class="icon-trashcan"></i></a></th>
			</tr>
			</thead>

			<tbody>';
			
			$i = 1;
			$total = 0;
			$sr_no = 0;
			foreach ($details as $r) {
				echo '<tr>
				<td class="aligncenter SortHandle"><i class="icon-bars"></i></td>
				<td class="aligncenter"><input type="text" class="form-control form-control-sm Numeric Validate Focus" name="sr_no[' . $r['id'] . ']" value="' . $r['sr_no'] . 
				'" /></td>
				<td><input type="hidden" name="bill_item_id[' . $r['id'] . ']" value="' . $r['bill_item_id'] . '" />
					<input type="text" class="form-control form-control-sm BillItem" name="bill_item[' . $r['id'] . ']" value="' . $r['bill_item'] . '" /></td>
				<td><textarea class="form-control form-control-sm Particulars" name="particulars[' . $r['id'] . ']">' . $r['particulars'] . '</textarea></td>
				<td>' . form_dropdown('currency_id[' . $r['id'] . ']', getSelectOptions('currencies', 'id', 'code'), $r['currency_id'], 'class="col-md-12 Calculate Currency"') . '</td>
				<td><input type="text" class="form-control form-control-sm Numeric Calculate Units" name="units[' . $r['id'] . ']" value="' . $r['units'] . '" /></td>
				<td><input type="text" class="form-control form-control-sm Numeric Calculate Price" name="price[' . $r['id'] . ']" value="' . $r['price'] . '" /></td>
				<td><input type="hidden" class="form-control form-control-sm Taxable" name="taxable[' . $r['id'] . ']" value="' . $r['taxable'] . '" />
					<input type="text" class="form-control form-control-sm Numeric Calculate Amount" name="bd_amount[' . $r['id'] . ']" value="' . $r['amount'] . '" /></td>
				<td class="aligncenter">' . form_checkbox(array('name' => 'delete_id['.$r['id'].']', 'value' => $r['id'], 'checked' => false, 'class' => 'DeleteCheckbox')) . '</td>
				</tr>';
				$sr_no = $r['sr_no'];
			}
			
			echo '<tr class="TemplateRow">
				<td class="aligncenter SortHandle"><i class="icon-bars"></i></td>
				<td><input type="text" class="form-control form-control-sm Numeric Validate Unchanged Increment" name="new_sr_no[]" value="'. ($sr_no + 1) .'" /></td>
				<td><input type="hidden" class="form-control form-control-sm Validate" name="new_bill_item_id[]" value="" />
					<input type="text" class="form-control form-control-sm BillItem Validate" value="" /></td>
				<td><textarea class="form-control form-control-sm Particulars" name="new_particulars[]"></textarea></td>
				<td>'. form_dropdown('new_currency_id[]', getSelectOptions('currencies', 'id', 'code'), 1, 'class="col-md-12 Calculate Currency"') .'</td>
				<td><input type="text" class="form-control form-control-sm Numeric Calculate Units" name="new_units[]" value="0" /></td>
				<td><input type="text" class="form-control form-control-sm Numeric Calculate Price" name="new_price[]" value="0" /></td>
				<td><input type="hidden" class="form-control form-control-sm hidden Taxable" name="new_taxable[]" value="" />
					<input type="text" class="form-control form-control-sm Numeric Calculate Amount" name="new_amount[]" value="0" /></td>
				<td class="aligncenter"><button type="submit" class="btn btn-success btn-sm AddButton"><i class="fa fa-plus"></i></button></td>
			</tr>
			</tbody>
			</table>

			<table class="table table-condensed">
			<tr>
				<td colspan="2" rowspan="4" class="aligntop">
					<div class="form-group">
					<label class="control-label">Remarks</label>
					<textarea type="text" class="form-control form-control-sm" name="remarks" rows="4">'. $row['remarks'] .'</textarea>
					</div>
				</td>
				
				<td class="alignright">Service Tax @: </td>
				<td width="100px"><input type="text" class="form-control form-control-sm Numeric Calculate" name="service_tax" value="'. $row['service_tax'] .'" id="ServiceTax" /> %</td>
				<td width="80px"><input type="text" class="form-control form-control-sm Numeric" value="0" readonly="true" id="ServiceTaxAmount" /></td>
				<td width="24px" class="aligncenter"><i class="icon-rupee"></i></td>
			</tr>

			<tr>
				<td class="alignright">EDU Cess @: </td>
				<td width="100px"><input type="text" class="form-control form-control-sm Numeric Calculate" name="edu_cess" value="'. $row['edu_cess'] .'" id="EduCess" /> %</td>
				<td width="80px"><input type="text" class="form-control form-control-sm Numeric" value="0" readonly="true" id="EduAmount" /></td>
				<td class="aligncenter"><i class="icon-rupee"></i></td>
			</tr>

			<tr>
				<td class="alignright">HEDU Cess @: </td>
				<td width="100px"><input type="text" class="form-control form-control-sm Numeric Calculate" name="hedu_cess" value="'. $row['hedu_cess'] .'" id="HeduCess" /> %</td>
				<td width="80px"><input type="text" class="form-control form-control-sm Numeric" value="0" readonly="true" id="HeduAmount" /></td>
				<td class="aligncenter"><i class="icon-rupee"></i></td>
			</tr>

			<tr>
				<td class="alignright bold">Total :</td>
				<td colspan="2"><input type="text" class="form-control form-control-sm Numeric big col-md-12" name="amount" value="'. $row['amount'] .'" id="NetAmount"/></td>
				<td class="aligncenter"><i class="icon-rupee"></i></td>
			</tr>
			</table>';
		} ?>
	<?php endif ?>		
</fieldset>

<div class="form-actions">
	<button type="button" class="btn btn-success" id="Update">Update</button>
	<div class="btn-group">
		<button type="button" class="btn btn-info" id="Expenses">Expenses</button>
		<a href="#modal-templates" class="btn btn-info" data-toggle="modal">Bill Template</a>
	</div>
	<?php if ($row['id'] > 0) {
		echo '<div class="btn-group">' .
			anchor($this->_clspath.$this->_class."/preview/".$row['id'].'/0/1', "Preview", 'class="btn btn-default Popup"') .
			anchor($this->_clspath.$this->_class."/preview/".$row['id'].'/1/1', "PDF", 'class="btn btn-default Popup"') .
			anchor($this->_clspath.$this->_class."/preview/".$row['id'].'/1/0', "PDF Plain", 'class="btn btn-default Popup"') .
			'</div>&nbsp;
		<a href="#modal-delete" data-toggle="modal" class="btn btn-danger">Delete</a>'; 
		}
	?>
</div>
</form>

<?php echo end_panel() ?>

<script>
function reCalculate() {
	var service_tax = parseFloat($('#ServiceTax').val());
	var edu_cess    = parseFloat($('#EduCess').val());
	var hedu_cess   = parseFloat($('#HeduCess').val());
	var total       = 0;
	var stax_amount = 0;
	var edu_amount  = 0;
	var hedu_amount = 0;
	var net_amount  = 0;

	$('.DataEntry tbody tr').each(function() {
		var ex_rate     = parseFloat($('#ExchangeRate').val());
		var currency    = $(this).find('.Currency option:selected').text();
		var rate        = parseFloat($(this).find('input.Price').val());
		var units       = parseFloat($(this).find('input.Units').val());
		var amount      = parseFloat($(this).find('input.Amount').val());
		var taxable     = $(this).find('input.Taxable').val();
		var ntax_amount = 0;
		var tax_amount  = 0;

		if (units > 0 && rate > 0) {
			if (currency === 'INR')
				amount = roundOff(parseFloat(units) * parseFloat(rate), 0);
			else
				amount = roundOff((parseFloat(units) * parseFloat(rate)) * parseFloat(ex_rate), 0);
		}

		if (taxable === 'Yes')
			tax_amount  = parseFloat(amount);
		else
			ntax_amount = parseFloat(amount);

		$(this).find('input.Amount').val(amount)

		if (amount > 0) {
			stax        = roundOff((tax_amount * service_tax) / 100, 0);
			edu         = roundOff((stax * edu_cess) / 100, 0);
			hedu        = roundOff((stax * hedu_cess) / 100, 0);
			net_amount  += (parseFloat(ntax_amount) + parseFloat(tax_amount) + parseFloat(stax) + parseFloat(edu) + parseFloat(hedu));
			stax_amount += parseFloat(stax);
			edu_amount  += parseFloat(edu);
			hedu_amount += parseFloat(hedu);
		}
	});
	// stax_amount = roundOff((total * service_tax) / 100, 0);
	// edu_amount  = roundOff((stax_amount * edu_cess) / 100, 0);
	// hedu_amount = roundOff((stax_amount * hedu_cess) / 100, 0);
	// net_amount  = (parseFloat(total) + parseFloat(stax_amount) + parseFloat(edu_amount) + parseFloat(hedu_amount));

	if (net_amount > 0) {
		$('#ServiceTaxAmount').val(stax_amount);
		$('#EduAmount').val(edu_amount);
		$('#HeduAmount').val(hedu_amount);
		$('#NetAmount').val(roundOff(net_amount, 0));
	}
}

$(document).ready(function(){
	$('body').on('change', '.Calculate', function() {
		reCalculate();
	});

	reCalculate();

	<?php if ($row['id'] > 0) : ?>
		$('#Type').attr('readonly', true);
	<?php endif ?>

	$('#Expenses').on('click', function() {
		if($('#Type').val() === 'Invoice') {
			$('#ServiceTax').removeAttr('readonly');
			$('#EduCess').removeAttr('readonly');
			$('#HeduCess').removeAttr('readonly');
			$('#ServiceTax').val(<?php echo Settings::get('service_tax') ?>);
			$('#EduCess').val(<?php echo Settings::get('edu_cess') ?>);
			$('#HeduCess').val(<?php echo Settings::get('hedu_cess') ?>);
		}
		else {
			$('#ServiceTax').val(0);
			$('#ServiceTaxAmount').val(0);
			$('#EduCess').val(0);
			$('#EduAmount').val(0);
			$('#HeduCess').val(0);
			$('#HeduAmount').val(0);
			$('#ServiceTax').attr('readonly', true);
			$('#EduCess').attr('readonly', true);
			$('#HeduCess').attr('readonly', true);
		}
		$('.DataEntry').find('.DelButton').each(function(){ $(this).click(); });
		$.get('<?php echo base_url($this->_clspath.$this->_class."/loadPendingExpense/".$job_id['job_id']) ?>/'+$("#Type").val());
	});

	$('#Template').on('click', function() {
		$.get('<?php echo base_url($this->_clspath.$this->_class."/loadTemplate/".$job_id['job_id']) ?>/'+$("#TemplateID").val());
		$("#modal-templates").modal('hide');
	});

	$('#PartyName').autocomplete({
		source: '<?php echo site_url($this->_clspath.$this->_class.'/json/parties/id/name') ?>',
		minLength: 0,
		focus: function(event, ui) {
			$(this).val(ui.item.name);
			return false;
		},
		select: function(event, ui) {
			$(this).prevAll('input').val(ui.item.id);
			$(this).val(ui.item.name);
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
			.append('<a>' + item.name + '</a>')
			.appendTo(ul);
	};

	$('.DataEntry').on('keydown.autocomplete', '.BillItem', function(event, items) {
		var id       = $(this).prevAll('input');
		var pa       = $(this).parent('td').parent('tr').find('.Particulars');
		var currency = $(this).parent('td').parent('tr').find('.Currency');
		var calc     = $(this).parent('td').parent('tr').find('.CalcType');
		var ct       = $(this).parent('td').parent('tr').find('.ContainerType');
		var price    = $(this).parent('td').parent('tr').find('.Price');
		$(this).autocomplete({
			source: '<?php echo site_url('master/bill_item/ajax/Export') ?>',
			minLength: 1,
			focus: function(event, ui) {
				$(id).val(ui.item.id);
				$(this).val(ui.item.name);
				$(pa).val(ui.item.name);
				$(currency).val(ui.item.currency_id);
				$(calc).text(ui.item.calc_type);
				$(ct).text(ui.item.container_type);
				$(price).val(ui.item.price);
				return false;
			},
			select: function(event, ui) {
				$(id).val(ui.item.id);
				$(this).val(ui.item.name);
				$(pa).val(ui.item.name);
				$(currency).val(ui.item.currency_id);
				$(calc).text(ui.item.calc_type);
				$(ct).text(ui.item.container_type);
				$(price).val(ui.item.price);
				return false;
			},
			response: function(event, ui) {
				if (ui.content.length === 0) {
					$(id).val(0);
					$(this).val('');
					$(pa).val('');
					$(currency).val('');
					$(calc).text('');
					$(ct).text('');
					$(price).val(0);
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
			source: "<?php echo site_url($this->_clspath.$this->_class.'/ajaxJson/bill_narrations/name') ?>",
			minLength: 0
		});
	});

	$('.DataEntry').on('keydown.autocomplete', '.FromLocation', function(event, items) {
		var id = $(this).prevAll('input');
		
		$(this).autocomplete({
			source: "<?php echo site_url($this->_clspath.$this->_class.'/ajaxLocations') ?>",
			minLength: 1,
			focus: function(event, ui) {
				$(id).val(ui.item.id);
				$(this).val(ui.item.name);
				return false;
			},
			select: function(event, ui) {
				$(id).val(ui.item.id);
				$(this).val(ui.item.name);
				return false;
			},
			response: function(event, ui) {
				if (ui.content.length === 0) {
					$(id).val(0);
					$(this).val('');
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

	$('.DataEntry').on('keydown.autocomplete', '.ToLocation', function(event, items) {
		var id = $(this).prevAll('input');
		
		$(this).autocomplete({
			source: "<?php echo site_url($this->_clspath.$this->_class.'/ajaxLocations') ?>",
			minLength: 1,
			focus: function(event, ui) {
				$(id).val(ui.item.id);
				$(this).val(ui.item.name);
				return false;
			},
			select: function(event, ui) {
				$(id).val(ui.item.id);
				$(this).val(ui.item.name);
				return false;
			},
			response: function(event, ui) {
				if (ui.content.length === 0) {
					$(id).val(0);
					$(this).val('');
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
});
</script>