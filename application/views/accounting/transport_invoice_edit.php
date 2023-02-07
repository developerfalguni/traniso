<style>
td.NonSTax {
   background-color: #FCC !important;
}
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
				<?php echo anchor($this->_clspath.$this->_class."/deleteVoucher/".$row['voucher_book_id'].'/'.$row['id'], 'Delete', 'class="btn btn-danger"') ?>
			</div>
		</div>
	</div>
</div>

<?php
echo form_open($this->uri->uri_string(), 'id="MainForm"');
echo form_hidden($id);
?>
<input type="hidden" name="create_id" value="0" id="CreateID" />

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
			<div class="col-md-4">
				<div class="form-group">
					<label class="control-label">Company (FY)</label>
					<h5><?php echo $default_company['code'] . ' (' . str_replace('_', '-', $default_company['financial_year']) .  ')' ?></h5>
				</div>
			</div>

			<div class="col-md-2">
				<div class="form-group">
					<label class="control-label">Voucher No</label>
					<input type="text" class="form-control form-control-sm" name="id2_format" value="<?php echo $row['id2_format'] ?>" />
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

			<div class="col-md-2"></div>

			<div class="col-md-2">
				<div class="form-group">
					<label class="control-label">Reverse Charge</label>
					<?php echo form_dropdown('reverse_charge', getEnumSetOptions('vouchers', 'reverse_charge'), $row['reverse_charge'], 'class="form-control form-control-sm"') ?>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-6">
				<div class="row">
					<div class="col-md-8">
						<div class="form-group<?php echo (strlen(form_error('dr_ledger_id')) > 0 ? ' has-error' : '') ?>">
							<label class="control-label">Debit Account</label>
							<input type="hidden" name="dr_ledger_id" value="<?php echo $row['dr_ledger_id'] ?>" id="DebitAccountID" />
							<input type="text" class="form-control form-control-sm" name="debit_account" value="<?php echo $row['debit_account'] ?>" id="DebitAccount" />
						</div>
					</div>

					<div class="col-md-2">
						<div class="form-group">
							<label class="control-label">Closing</label><br />
							<span class="label label-warning" id="DebitAccountClosing"><?php echo inr_format($row['dr_closing']) . ($row['dr_closing'] > 0 ? ' Dr' : ' Cr'); ?></span>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-8">
						<div class="form-group<?php echo (strlen(form_error('cr_ledger_id')) > 0 ? ' has-error' : '') ?>">
							<label class="control-label">Credit Account</label>
							<input type="hidden" name="cr_ledger_id" value="<?php echo $row['cr_ledger_id'] ?>" id="CreditAccountID" />
							<input type="text" class="form-control form-control-sm" name="credit_account" value="<?php echo $row['credit_account'] ?>" id="CreditAccount" />
						</div>
					</div>

					<div class="col-md-4">
						<div class="form-group">
							<label class="control-label">Closing</label><br />
							<span class="label label-warning" id="CreditAccountClosing"><?php echo inr_format($row['cr_closing']) . ($row['cr_closing'] > 0 ? ' Dr' : ' Cr'); ?></span>
						</div>
					</div>
				</div>
			</div>

			<div class="md-col-6">
				<div class="col-md-2">
					<div class="form-group">
						<label class="control-label">From</label>
						<div class="input-group date DatePicker">
							<span class="input-group-addon"><i class="icon-calendar"></i></span>
							<input type="text" class="form-control form-control-sm AutoDate" name="from_date" value="<?php echo $from_date ?>" id="FromDate" />
						</div>
					</div>
				</div>

				<div class="col-md-2">
					<div class="form-group">
						<label class="control-label">To</label>
						<div class="input-group date DatePicker">
							<span class="input-group-addon"><i class="icon-calendar"></i></span>
							<input type="text" class="form-control form-control-sm AutoDate" name="to_date" value="<?php echo $to_date ?>" id="ToDate" />
						</div>
					</div>
				</div>

				<div class="col-md-2">
					<div class="form-group">
						<label class="control-label">Party Ref.</label>
						<input type="text" class="form-control form-control-sm" name="party_reference_no" value="" id="PartyReference" />
					</div>
				</div>

				<div class="col-md-2">
					<div class="form-group">
						<label class="control-label">From Location</label>
						<input type="hidden" id="FromLocationID" />
						<input type="text" class="form-control form-control-sm" id="FromLocation" value="" />
					</div>
				</div>

				<div class="col-md-2">
					<div class="form-group">
						<label class="control-label">To Location</label>
						<input type="hidden" id="ToLocationID" />
						<input type="text" class="form-control form-control-sm" id="ToLocation" value="" />
					</div>
				</div>

				<div class="col-md-2">
					<br />
					<button type="button" class="btn btn-primary" id="LoadTrips"><i class="icon icon-refresh"></i> Load Trips</button>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-5">
				<div class="form-group<?php echo (strlen(form_error('narration')) > 0 ? ' has-error' : '') ?>">
					<label class="control-label">Narration</label>
					<textarea class="form-control form-control-sm" name="narration" rows="2" id="Narration"><?php echo $row['narration'] ?></textarea>
				</div>
			</div>

			<div class="col-md-5">
				<div class="form-group<?php echo (strlen(form_error('remarks')) > 0 ? ' has-error' : '') ?>">
					<label class="control-label">Remarks</label>
					<textarea class="form-control form-control-sm Monospace" name="remarks" rows="3"><?php echo $row['remarks'] ?></textarea>
				</div>
			</div>

			<div class="col-md-2">
				<div class="form-group">
					<label class="control-label">Place of Supply</label>
					<textarea class="form-control form-control-sm" name="place_of_supply" rows="2"><?php echo $row['place_of_supply'] ?></textarea>
				</div>
			</div>
		</div>

		<table class="table table-condensed table-striped DataEntry Sortable" id="InvoiceTrips">
		<thead>
		<tr>
			<th width="24px"></th>
			<th width="48px" class="aligncenter"><a href="#" class="CheckAll" checkbox-class="SelectCheckBox"><i class="icon icon-check"></i></a></th>
			<th width="60px">Account</th>
			<th>Particulars</th>
			<th>Date</th>
			<th>LR No. / <span class="green">Registration No</span></th>
			<th>Ref. No.</th>
			<th>Container No</th>
			<th>From Location / <span class="green">To Location</span></th>
			<th width="110px">CBM / <span class="green">Weight</span></th>
			<th width="110px">Units</th>
			<th width="110px">Rate</th>
			<th width="110px">Advance</th>
			<th width="110px">Amount</th>
			<th width="60px">CGST</th>
			<th width="60px">SGST</th>
			<th width="60px">IGST</th>
			<th width="110px">Net</th>
			<th width="24px" class="aligncenter"><a href="#" class="CheckAll"><i class="icon-trashcan"></i></a></th>
		</tr>
		</thead>
		
		<tbody>
		<?php 
			$total = array(
				'net'     => 0,
				'advance' => 0,
				'amount'  => 0,
			);
			$sr_no = 0;
			foreach ($voucher_details as $vjd) {
				if ($vjd['id'] > 0) {
					$non_stax = '';
					if (strlen($vjd['stax_code']) == 0)
						$non_stax = 'NonSTax';

					$total['advance'] = bcadd($total['advance'], $vjd['advance']);
					$total['amount']  = bcadd($total['amount'], $vjd['amount']);
					if ($vjd['trip_id'] == 0)
						$total['net'] = bcadd($total['net'], $vjd['amount']);
					else
						$total['net'] = bcadd($total['net'], bcsub($vjd['rate'], $vjd['advance']));

					$cgst        = (($vjd['cgst'] > 0 AND $vjd['sgst'] > 0) ? $vjd['cgst'] : 0);
					$cgst_amount = round($vjd['amount'] * $cgst / 100, 2);
					$sgst        = (($vjd['cgst'] > 0 AND $vjd['sgst'] > 0) ? $vjd['sgst'] : 0);
					$sgst_amount = round($vjd['amount'] * $sgst / 100, 2);
					$igst        = (($vjd['cgst'] == 0 AND $vjd['sgst'] == 0) ? $vjd['igst'] : 0);
					$igst_amount = round($vjd['amount'] * $igst / 100, 2);
					// $total       = round($vjd['amount'] + $cgst_amount + $sgst_amount + $igst_amount, 2);

					echo '<tr>
						<td class="aligncenter grayLight ' . ($vjd['category'] == 'Bill Items' ? 'SortHandle' : 'ui-state-disabled') . '"><i class="icon-bars"></i>
						<input type="hidden" name="vjd_trip_id['.$vjd['id'].']" value="'.$vjd['trip_id'].'" /></td>';
						if ($vjd['category'] == 'Bill Items') 
							echo '<td class="aligncenter ' . $non_stax . '"><input type="text" class="form-control form-control-sm Numeric Validate Focus" name="vjd_sr_no[' . $vjd['id'] . ']" value="' . $vjd['sr_no'] . '" /></td>';
						else
							echo '<td class="aligncenter ' . $non_stax . '"><input type="hidden" class="form-control form-control-sm Numeric Validate Focus" name="vjd_sr_no[' . $vjd['id'] . ']" value="0" /></td>';
						echo '<td class="' . $non_stax . '">' . $vjd['bill_item_code'] . '</td>
						<td class="' . $non_stax . '"><input type="text" class="form-control form-control-sm" name="vjd_particulars[' . $vjd['id'] . ']" value="' . $vjd['particulars'] . '" /></td>
						<td class="tiny ' . $non_stax . '">' . $vjd['date'] . '</td>
						<td class="tiny ' . $non_stax . '">' . (strlen($vjd['lr_no']) > 0 ? anchor('/transport/trip/edit/'.$vjd['cargo_type'].'/'.$vjd['trip_id'], $vjd['lr_no'], 'target="_blank"') : '') . '<br /><span class="green">' . $vjd['registration_no'] . '</span></td>
						<td class="tiny ' . $non_stax . '">' . $vjd['party_reference_no'] . '</td>
						<td class="tiny ' . $non_stax . '">' . $vjd['container_no'] . '</td>
						<td class="tiny ' . $non_stax . '">' . $vjd['from_location'] . '<br /><span class="green">' . $vjd['to_location'] . '</span></td>
						<td class="tiny ' . $non_stax . '">' . $vjd['cbm'] . '<br /><span class="green">' . $vjd['weight'] . '</span></td>
						<td class="' . $non_stax . '"><input type="text" class="form-control form-control-sm Numeric Units" name="vjd_units[' . $vjd['id'] . ']" value="' . $vjd['units'] . '" ' . (($vjd['units'] != 0 AND $vjd['container_id'] > 0) ? 'readonly="true"' : null) . '/></td>
						<td class="' . $non_stax . '"><input type="text" class="form-control form-control-sm Numeric Rate" name="vjd_rate[' . $vjd['id'] . ']" value="' . $vjd['rate'] . '" ' . (($vjd['rate'] != 0 AND $vjd['container_id'] > 0) ? 'readonly="true"' : null) . '/></td>
						<td class="' . $non_stax . '"><input type="text" class="form-control form-control-sm Numeric Advance" name="vjd_advance[' . $vjd['id'] . ']" value="' . $vjd['advance'] . '" ' . ($vjd['advance'] == 0 ? null : 'readonly="true"') . ' /></td>
						<td class="' . $non_stax . '"><input type="text" class="form-control form-control-sm Numeric Amount" name="vjd_amount[' . $vjd['id'] . ']" value="' . $vjd['amount'] . '" ' . (($vjd['amount'] == 0 OR $vjd['trip_id'] == 0) ? null : 'readonly="true"') . ' /></td>
						<td class="' . $non_stax . '"><input type="text" class="form-control form-control-sm CGST Numeric" name="vjd_cgst[' . $vjd['id'] . ']" value="' . $cgst . '" /></td>
						<td class="' . $non_stax . '"><input type="text" class="form-control form-control-sm SGST Numeric" name="vjd_sgst[' . $vjd['id'] . ']" value="' . $sgst . '" /></td>
						<td class="' . $non_stax . '"><input type="text" class="form-control form-control-sm IGST Numeric" name="vjd_igst[' . $vjd['id'] . ']" value="' . $igst . '" /></td>
						<td class="alignright Net ' . $non_stax . '">' . ($vjd['trip_id'] == 0 ? $vjd['amount'] : ($vjd['rate'] - $vjd['advance'])) . '</td>
						<td class="aligncenter ' . $non_stax . '">' . form_checkbox(array('name' => 'delete_id['.$vjd['id'].']', 'value' => $vjd['id'], 'checked' => false, 'class' => 'DeleteCheckbox')) . '</td>
					</tr>';
				}
				$sr_no = $vjd['sr_no'];
			}
		?>

		<tr class="TemplateRow">
			<td class="aligncenter SortHandle"><i class="icon-bars"></i></th>
			<td><input type="text" class="form-control form-control-sm Numeric Validate Unchanged Increment" name="new_sr_no[]" value="<?php echo ($sr_no + 1) ?>" /></td>
			<td><input type="hidden" class="form-control form-control-sm BillItemID Validate" name="new_bill_item_id[]" value="" />
				<input type="text" class="form-control form-control-sm BillItemCode Validate Focus" value="" /></td>
			<td colspan="8"><input type="text" class="form-control form-control-sm Validate Particular" name="new_particulars[]" value="" /></td>
			<td><input type="text" class="form-control form-control-sm Numeric Units" name="new_units[]" value="" /></td>
			<td><input type="text" class="form-control form-control-sm Numeric Rate" name="new_rate[]" value="" /></td>
			<td><input type="text" class="form-control form-control-sm Numeric Amount Validate" name="new_amount[]" value="" /></td>
			<td><input type="text" class="form-control form-control-sm CGST Numeric" name="new_cgst[]" value="" /></td>
			<td><input type="text" class="form-control form-control-sm SGST Numeric" name="new_sgst[]" value="" /></td>
			<td><input type="text" class="form-control form-control-sm IGST Numeric" name="new_igst[]" value="" /></td>
			<td class="Net"></td>
			<td class="aligncenter"><button type="submit" class="btn btn-success btn-sm AddButton"><i class="icon-white icon-plus"></i></button></td>
		</tr>
		</tbody>

		<tfoot>
		<tr>
			<th colspan="12" class="alignright">Total</th>
			<th class="alignright"><?php echo inr_format($total['advance']) ?></th>
			<th class="alignright"><?php echo inr_format($total['amount']) ?></th>
			<th></th>
			<th></th>
			<th></th>
			<th class="alignright"><?php echo inr_format($total['net']) ?></th>
			<th></th>
		</tr>
		</tfoot>
		</table>
	</div>

	<div class="card-footer">
		<div class="row">
			<div class="col-md-8">
				<button type="button" class="btn btn-success UpdateButton" id="Update">Update</button>
				<a href="#modal-delete" data-toggle="modal" class="btn btn-danger pull-right">Delete</a>
				<?php if ($row['id'] > 0) {
					if (strtotime($row['date']) >= strtotime('2017-07-01')) {
						echo '<div class="btn-group">' .
							anchor($this->_clspath.$this->_class."/pdf/".$row['voucher_book_id'].'/'.$row['id'].'/1', "PDF", 'class="btn btn-default Popup"') .
							anchor($this->_clspath.$this->_class."/pdf/".$row['voucher_book_id'].'/'.$row['id'].'/0', "PDF Plain", 'class="btn btn-default Popup"') .
						'</div>'; 
					}
					else {
					echo '<div class="btn-group">' .
						anchor($this->_clspath.$this->_class."/preview/".$row['voucher_book_id'].'/'.$row['id'].'/0/1', "HTML", 'class="btn btn-default Popup"') .
						anchor($this->_clspath.$this->_class."/preview/".$row['voucher_book_id'].'/'.$row['id'].'/1/1', "PDF", 'class="btn btn-default Popup"') .
						anchor($this->_clspath.$this->_class."/preview/".$row['voucher_book_id'].'/'.$row['id'].'/1/0', "PDF Plain", 'class="btn btn-default Popup"') .
						'</div>'; 
					}
					}
				?>
			</div>
			<div class="col-md-4 alignright big">
				<?php echo inr_format($total['amount']) ?>
			</div>
		</div>
	</div>
</div>

</form>

<script language="JavaScript">
var units = <?php echo ceil($row['net_weight']) ?>;

function loadJobDetails() {
	var job_id    = $('#JobID').val();
	var ledger_id = $('#DebitAccountID').val();
	$.get('<?php echo base_url($this->_clspath.$this->_class."/loadJobDetails/".$row['voucher_book_id']) ?>/'+job_id+'/'+ledger_id);
}

$(document).ready(function() {
	$('#Update').addClass('onEventAttached').on('click', function() {
		var crid = $('#CreditAccountID').val();
		if (crid == 0)
			alert('Credit Account Missing');
		else
			$('form#MainForm').submit();
	});

	$('#UpdateID').on('click', function() {
		var crid = $('#CreditAccountID').val();
		if (crid == 0)
			alert('Credit Account Missing');
		else {
			$('#CreateID').val('1');
			$('form#MainForm').submit();
		}
	});

	$('.Units').on('change', function() {
		var unit   = $(this).val();
		var rate   = $(this).parents('tr').children('td').find('.Rate').val();
		var amount = roundOff((unit * rate), 0);
		$(this).parents('tr').children('td').find('.Amount').val(amount);
	});

	$('.Rate').on('change', function() {
		var rate   = $(this).val();
		var unit   = $(this).parents('tr').children('td').find('.Units').val();
		var amount = roundOff((unit * rate), 0);
		$(this).parents('tr').children('td').find('.Amount').val(amount);
	});

	$('#DebitAccount').autocomplete({
		source: '<?php echo site_url("accounting/ledger/ajax") ?>',
		minLength: 1,
		open: function(event, ui) {
			$(this).autocomplete('widget').css({
				'width': 400
			});
		},
		focus: function(event, ui) {
			$('#DebitAccount').val(ui.item.code);
			return false;
		},
		select: function(event, ui) {
			$('#DebitAccount').val(ui.item.code + ' - ' + ui.item.name);
			$('#DebitAccountID').val(ui.item.id);
			$('#DebitAccountClosing').text(ui.item.closing);
			return false;
		}
	})
	.data('ui-autocomplete')._renderItem = function(ul, item) {
		return $('<li></li>')
			.data('item.autocomplete', item)
			.append('<a><span class="tiny"><strong class="blueDark">' + item.code + '</strong> ' + item.name + ' - <span class="blue">' + item.closing + '</span></span></a>')
			.appendTo(ul);
	};

	$('#CreditAccount').autocomplete({
		source: '<?php echo site_url("accounting/ledger/ajax") ?>',
		minLength: 1,
		open: function(event, ui) {
			$(this).autocomplete('widget').css({
				'width': 400
			});
		},
		focus: function(event, ui) {
			$('#CreditAccount').val(ui.item.code);
			return false;
		},
		select: function(event, ui) {
			$('#CreditAccount').val(ui.item.code + ' - ' + ui.item.name);
			$('#CreditAccountID').val(ui.item.id);
			$('#CreditAccountClosing').text(ui.item.closing);
			return false;
		}
	})
	.data('ui-autocomplete')._renderItem = function(ul, item) {
		return $('<li></li>')
			.data('item.autocomplete', item)
			.append('<a><span class="tiny"><strong class="blueDark">' + item.code + '</strong> ' + item.name + ' - <span class="blue">' + item.closing + '</span></span></a>')
			.appendTo(ul);
	};

	$("#Narration").autocomplete({
		source: "<?php echo site_url($this->_clspath.$this->_class.'/ajaxJson/narrations/name') ?>",
		minLength: 0,
		open: function(event, ui) {
            $(this).autocomplete('widget').css({
                "width": 800
            });
        },
		select: function(event, ui) {
			$("#Narration").val(ui.item.value.replace("[[bill_no]]", $("#InvoiceNo").val()));
			return false;
		}
	});


	$('.DataEntry').on('keydown.autocomplete', '.BillItemCode', function(event, items) {
		var id   = $(this).parent('td').parent('tr').find('.BillItemID');
		var pa   = $(this).parent('td').parent('tr').find('.Particular');
		var unit = $(this).parent('td').parent('tr').find('.Units');
		$(this).autocomplete({
			source: '<?php echo site_url("accounting/ledger/ajaxLedgers/Bill Items") ?>',
			minLength: 1,
			open: function(event, ui) {
				$(this).autocomplete('widget').css({
					'width': 400
				});
			},
			focus: function(event, ui) {
				$(id).val(ui.item.id);
				$(this).val(ui.item.code);
				$(pa).val(ui.item.name);
				$(unit).val(units);
				return false;
			},
			select: function(event, ui) {
				if ($(this).parent('td').parent('tr').index() === 0) {
					$('.BillItemID').val(ui.item.id);
					$('.BillItemCode').val(ui.item.code);
					$('.Particular').val(ui.item.name);
				}
				else {
					$(id).val(ui.item.id);
					$(this).val(ui.item.code);
					$(pa).val(ui.item.name);
					$(unit).val(units);
				}
				return false;
			}
		})
		.data('ui-autocomplete')._renderItem = function(ul, item) {
			return $('<li></li>')
				.data('item.autocomplete', item)
				.append('<a><span class="tiny"><strong class="blueDark">' + item.code + '</strong> ' + item.name + '</span></a>')
				.appendTo(ul);
		};
	});

	$("#FromLocation").kaabar_typeahead_complex({
		name: 'tt_name',
		displayKey: 'name',
		url: '<?php echo site_url($this->_clspath.$this->_class.'/json/locations/id/name') ?>',
		suggestion: '<p>{{name}}</p>',
		fields: [{id: '#FromLocationID', field: 'id'}]
	});

	$("#ToLocation").kaabar_typeahead_complex({
		name: 'tt_name',
		displayKey: 'name',
		url: '<?php echo site_url($this->_clspath.$this->_class.'/json/locations/id/name') ?>',
		suggestion: '<p>{{name}}</p>',
		fields: [{id: '#ToLocationID', field: 'id'}]
	});

	$("#PartyReference").kaabar_typeahead({
		name: 'tt_party_reference_no',
		displayKey: 'party_reference_no',
		url: '<?php echo site_url($this->_clspath.$this->_class.'/getJSON/trips/party_reference_no') ?>',
		suggestion: '<p>{{party_reference_no}}</p>'
	});

	$('#Product').kaabar_autocomplete({
		source: '<?php echo base_url($this->_clspath.$this->_class."/json/products/id/name") ?>',
	});

	$('#Vessel').kaabar_autocomplete({
		source: '<?php echo base_url($this->_clspath.$this->_class."/ajaxVessels") ?>',
	});

	$('#LoadTrips').click(function(event) {
		/* Act on the event */
		var from_date        = $('#FromDate').val();
		var to_date          = $('#ToDate').val();
		var party_ref        = $('#PartyReference').val();
		var party_ledger_id  = $('#DebitAccountID').val();
		var from_location_id = $('#FromLocationID').val();
		var to_location_id   = $('#ToLocationID').val();

		$.ajax({
			url: '<?php echo base_url($this->_clspath.$this->_class."/ajaxTrips") ?>',
			type: 'POST',
			dataType: 'html',
			data: {
				from_date: from_date,
				to_date: to_date,
				party_reference_no: party_ref,
				party_ledger_id: party_ledger_id,
				from_location_id: from_location_id,
				to_location_id: to_location_id,
			},
		})
		.done(function(data) {
			$(data).insertBefore('#InvoiceTrips tbody tr:eq(0)');
		})
		.fail(function() {
			console.log('Error');
		})
	});
});
</script>