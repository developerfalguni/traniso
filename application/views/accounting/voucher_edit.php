<style>
.table.table-striped tbody tr td.Current {
   background-color: #ff9;
}
</style>

<div id="modal-sverror" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><i class="icon-times"></i></button>
				<h3>Date Error</h3>
			</div>
			<div class="modal-body">
				<ul>
					<li>Date is not in Financial year range or</li>
					<li>Voucher date is less than Lock Date.</li>
					<li>Voucher exists on selected date, Create Subvoucher.</li>
				</ul>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

<div id="modal-document" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
		<?php echo form_open_multipart($this->_clspath.'voucher_document/attach/'.$row['id']); ?>
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><i class="icon-times"></i></button>
				<h3>Attach Bill</h3>
			</div>
			<div class="modal-body">
				<input type="file" name="userfile" size="40" />
			</div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-success">Upload</button>
			</div>
		</form>
		</div>
	</div>
</div>

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
echo form_hidden($id2);
echo '<input type="hidden" name="update_action" value="0" id="UpdateAction" />';

$amount = 0;
foreach ($sub_vouchers as $sv_row)
	$amount += $sv_row['amount'];

$vd_amount = 0;
foreach ($voucher_details as $vjd)
	$vd_amount += $vjd['amount'];

$diff_amount = $row['amount'] - $vd_amount;
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
				<div class="col-md-3">
					<div class="form-group">
						<label class="control-label">Company (FY)</label>
						<h5><?php echo $default_company['code'] . ' (' . str_replace('_', '-', $default_company['financial_year']) .  ')' ?></h5>
					</div>
				</div>

				<!-- <div class="col-md-2">
					<div class="form-group">
						<label class="control-label">Voucher Type</label>
						<h5><?php echo $voucher_book['code'] ?></h5>
					</div>
				</div> -->

				<div class="col-md-3">
					<div class="form-group">
						<label class="control-label">Voucher No</label>
						<h5><?php echo $row['id2_format'] ?></h5>
					</div>
				</div>

				<div class="col-md-3">
					<div class="form-group">
						<label class="control-label">Amount</label>
						<h5><?php echo inr_format(number_format($amount, 2, '.', '')) ?></h5>
					</div>
				</div>

				<div class="col-md-3">
					<div class="form-group">
						<label class="control-label">Attach Bill</label>
						<?php foreach ($row['documents'] as $doc) 
							echo anchor($this->_clspath.'voucher_document/index/'.$doc['voucher_id'].'/'.$doc['id'], '<i class="icon-paperclip"></i>'); 
						?>
						<?php if ($row['id2'] > 0) 
							echo '<a href="#modal-document" class="btn btn-success btn-sm" data-toggle="modal"><i class="fa fa-plus"></i></a>';
						?>
					</div>
				</div>
			</div>
		</fieldset>


		<fieldset>
			<div class="row">
				<div class="col-md-6" id="VoucherDetail">
					<?php echo start_panel('Sub Voucher Entry', '', 'nopadding alert', '<div class="buttons">
				<a href="' . base_url($this->_clspath.$this->_class.'/edit/'.$row['voucher_book_id']) . '" class="btn btn-sm btn-success" id="NewVoucher"><i class="fa fa-plus"></i> Voucher</a>
				<a href="' . base_url($this->_clspath.$this->_class.'/edit/'.$row['voucher_book_id'].'/'.$id2['id2']) . '" class="btn btn-sm btn-success" id="NewSubVoucher"><i class="fa fa-plus"></i> Sub Voucher</a>
				</div>') ?>
					<div class="card card-default">
						<div class="card-header">
							<span class="card--icon"><?php echo anchor($this->_clspath.$this->_class, '<i class="icon-list"></i>') ?></span>
							<span class="card--links"><?php echo anchor($this->_clspath.$this->_class.'/edit/0', '<i class="fa fa-plus"></i> Add', 'class="btn btn-xs btn-success"'); ?></span>
							<h3 class="card-title"><?php echo $page_title ?></h3>
						</div>
						
						<div class="card-body">
							<fieldset>
								<div class="row">
									<div class="col-md-4">
										<div class="form-group<?php echo (strlen(form_error('id3')) > 0 ? ' has-error' : '') ?>">
											<label class="control-label">Sr. No.</label>
											
												<input type="text" class="form-control form-control-sm Numeric" name="id3" value="<?php echo $row['id3'] ?>" />
											
										</div>
									</div>

									<div class="col-md-4">
										<div class="form-group<?php echo (strlen(form_error('date')) > 0 ? ' error' : '') ?>" id="DateCG">
										<label class="control-label">Date</label>
										<div class="input-group date DatePicker">
											<span class="input-group-addon"><i class="icon-calendar"></i></span>
											<input type="text" class="form-control form-control-sm AutoDate" name="date" value="<?php echo $row['date'] ?>" size="10" id="Date" onblur="javascript: checkVoucherDate();" />
										</div>
									</div>
									</div>
								</div>

								<div class="row">
									<div class="col-md-9">
										<div class="form-group<?php echo (strlen(form_error('dr_ledger_id')) > 0 ? ' has-error' : '') ?>">
											<label class="control-label">Debit Account</label>
											
												<input type="hidden" name="dr_ledger_id" value="<?php echo $row['dr_ledger_id'] ?>" id="DebitAccountID" />
												<input type="hidden" value="<?php echo $row['dr_tds_class_id'] ?>" id="DebitTDS" />
												<input type="hidden" value="<?php echo $row['dr_stax_category_id'] ?>" id="DebitSTAX" />
										<?php if ($voucher_book['default_ledger_id'] > 0 && 
												  $row['dr_ledger_id'] == $voucher_book['default_ledger_id'] && 
												  $voucher_book['dr_cr'] == 'Dr' && 
												  substr($row['debit_account'], 0, 4) != 'CASH')
											echo '<input type="hidden" name="debit_account" value="' . $row['debit_account'] . '" />
												<input type="text" class="form-control form-control-sm" value="' . $row['debit_account'] . '" id="DebitAccount" disabled />';
										else
											echo '<input type="text" class="form-control form-control-sm" name="debit_account" value="' . $row['debit_account'] . '" id="DebitAccount" />';
										?>
											
										</div>
									</div>

									<div class="col-md-3">
										<div class="form-group">
											<label class="control-label">Closing</label><br />
											<span class="label label-warning" id="DebitAccountClosing"><?php echo inr_format($row['dr_closing']) . ($row['dr_closing'] > 0 ? ' Dr' : ' Cr'); ?></span>
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-md-9">
										<div class="form-group<?php echo (strlen(form_error('cr_ledger_id')) > 0 ? ' has-error' : '') ?>">
											<label class="control-label">Credit Account</label>
											
												<input type="hidden" name="cr_ledger_id" value="<?php echo $row['cr_ledger_id'] ?>" id="CreditAccountID" />
												<input type="hidden" value="<?php echo $row['cr_tds_class_id'] ?>" id="CreditTDS" />
												<input type="hidden" value="<?php echo $row['cr_stax_category_id'] ?>" id="CreditSTAX" />
										<?php if ($voucher_book['default_ledger_id'] > 0 && 
												  $row['cr_ledger_id'] == $voucher_book['default_ledger_id'] && 
												  $voucher_book['dr_cr'] == 'Cr' && 
												  substr($row['credit_account'], 0, 4) != 'CASH')
												echo '<input type="hidden" name="credit_account" value="' . $row['credit_account'] . '" />
													<input type="text" class="form-control form-control-sm" value="' . $row['credit_account'] . '" id="CreditAccount" disabled />';
											else
												echo '<input type="text" class="form-control form-control-sm" name="credit_account" value="' . $row['credit_account'] . '" id="CreditAccount" />';
											?>
											
										</div>
									</div>

									<div class="col-md-3">
										<div class="form-group">
											<label class="control-label">Closing</label><br />
											<span class="label label-warning" id="CreditAccountClosing"><?php echo inr_format($row['cr_closing']) . ($row['cr_closing'] > 0 ? ' Dr' : ' Cr'); ?></span>
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-md-6">
										<div class="form-group<?php echo (strlen(form_error('amount')) > 0 ? ' has-error' : '') ?>">
											<label class="control-label">Amount</label>
											
												<input type="text" class="form-control form-control-sm Numeric big input-medium" name="amount" value="<?php echo $row['amount'] ?>" />
												<span class="label <?php echo ($diff_amount < 0 ? 'label-danger' : 'label-success') ?>" id="AmountDiff"><?php echo inr_format($diff_amount) ?></span>
											
										</div>
									</div>

									<div class="col-md-6">
										<div class="form-group<?php echo (strlen(form_error('invoice_no')) > 0 ? ' has-error' : '') ?>">
											<label class="control-label">Bill No</label>
											
												<input type="text" class="form-control form-control-sm" name="invoice_no" value="<?php echo $row['invoice_no'] ?>" id="InvoiceNo" />
											
										</div>
									</div>
								</div>

								<div class="row hide" id="TDS">
									<div class="col-md-2">
										<div class="form-group<?php echo (strlen(form_error('invoice_amount')) > 0 ? ' has-error' : '') ?>">
											<label class="control-label">Inv. Amt</label>
											
												<input type="text" class="form-control form-control-sm Numeric" name="invoice_amount" value="<?php echo $row['invoice_amount'] ?>" />
											
										</div>
									</div>

									<div class="col-md-2">
										<div class="form-group">
											<label class="control-label">TDS %</label>
											<div class="form-group<?php echo (strlen(form_error('tds')) > 0 ? ' has-error' : '') ?>">
												<input type="text" class="form-control form-control-sm Numeric" name="tds" value="<?php echo $row['tds'] ?>" />
											</div>
										</div>
									</div>

									<div class="col-md-2">
										<div class="form-group<?php echo (strlen(form_error('tds_amount')) > 0 ? ' has-error' : '') ?>">
											<label class="control-label">TDS Amt</label>
											
												<input type="text" class="form-control form-control-sm Numeric" name="tds_amount" value="<?php echo $row['tds_amount'] ?>" />
											
										</div>
									</div>

									<div class="col-md-2">
										<div class="form-group<?php echo (strlen(form_error('tds_surcharge')) > 0 ? ' has-error' : '') ?>">
											<label class="control-label">SC Amt</label>
											
												<input type="text" class="form-control form-control-sm Numeric" name="tds_surcharge" value="<?php echo $row['tds_surcharge'] ?>" />
											
										</div>
									</div>

									<div class="col-md-2">
										<div class="form-group<?php echo (strlen(form_error('tds_edu_cess')) > 0 ? ' has-error' : '') ?>">
											<label class="control-label">EC Amt</label>
											
												<input type="text" class="form-control form-control-sm Numeric" name="tds_edu_cess" value="<?php echo $row['tds_edu_cess'] ?>" />
											
										</div>
									</div>

									<div class="col-md-2">
										<div class="form-group<?php echo (strlen(form_error('tds_hedu_cess')) > 0 ? ' has-error' : '') ?>">
											<label class="control-label">HEC Amt</label>
											
												<input type="text" class="form-control form-control-sm Numeric" name="tds_hedu_cess" value="<?php echo $row['tds_hedu_cess'] ?>" />
											
										</div>
									</div>
								</div>

								<div class="row hide" id="STAX">
									<div class="col-md-6">
										<div class="form-group<?php echo (strlen(form_error('invoice_date')) > 0 ? ' has-error' : '') ?>">
											<label class="control-label">Bill Date</label>
											<div class="input-group date DatePicker">
												<span class="input-group-addon"><i class="icon-calendar"></i></span>
												<input type="text" class="form-control form-control-sm AutoDate" name="invoice_date" value="<?php echo $row['invoice_date'] ?>" />
											</div>
										</div>
									</div>

									<div class="col-md-6">
										<div class="form-group<?php echo (strlen(form_error('stax_on_amount')) > 0 ? ' has-error' : '') ?>">
											<label class="control-label">Service Tax On</label>
											
												<input type="text" class="form-control form-control-sm Numeric" name="stax_on_amount" value="<?php echo $row['stax_on_amount'] ?>" />
											
										</div>
									</div>
								</div>

								<div class="form-group<?php echo (strlen(form_error('narration')) > 0 ? ' has-error' : '') ?>">
									<label class="control-label">Narration</label>
									
										<textarea class="form-control form-control-sm Text tiny col-md-12" name="narration" rows="1" id="Narration"><?php echo $row['narration'] ?></textarea>
									
								</div>

								<br />
								<table class="table table-condensed table-striped DataEntry">
								<thead>
									<tr>
										<th width="24px">No</th>
										<th width="140px">BL / SB</th>
										<th>Name</th>
										<th width="100px">Amount</th>
										<th width="24px" class="aligncenter"><a href="#" class="CheckAll"><i class="icon-trashcan"></i></a></th>
									</tr>
								</thead>

								<tbody>
									<?php 
									$i = 1;
									foreach ($voucher_details as $vjd) {
										echo '<tr>
											<td class="aligncenter">' . $i++ . '</td>
											<td>' . $vjd['bl_no'] . '</td>
											<td>' . $vjd['bill_item_name'] . '</td>
											<td><input type="text" class="form-control form-control-sm Numeric" name="vd_amount[' . $vjd['id'] . ']" value="' . $vjd['amount'] . '" /></td>
											<td class="aligncenter">' . form_checkbox(array('name' => 'delete_id['.$vjd['id'].']', 'value' => $vjd['id'], 'checked' => false, 'class' => 'DeleteCheckbox')) . '</td>
										</tr>';
									} ?>

									<tr class="TemplateRow">
										<td></td>
										<td><input type="hidden" class="form-control form-control-sm Validate BlankJobID" name="new_job_id[]" value="" />
											<input type="text" class="form-control form-control-sm BlankBLSB Validate Focus" value="" /></td>
										<td><input type="hidden" class="form-control form-control-sm BlankBillItemID" name="new_bill_item_id[]" value="" />
											<input type="text" class="form-control form-control-sm BlankBillItem Validate" value="" /></td>
										<td><input type="text" class="form-control form-control-sm Numeric Validate" name="new_amount[]" value="" /></td>
										<td class="aligncenter"><button type="submit" class="btn btn-success btn-sm AddButton"><i class="icon-white icon-plus"></i></button></td>
									</tr>
								</tbody>
								</table>
							</fieldset>
						</div>
					
						<div class="card-footer">
							<div class="row">
								<div class="col-md-8">
									<button type="button" class="btn btn-success" id="Update">Update</button>&nbsp;&nbsp;
									<a href="#modal-delete" data-toggle="modal" class="btn btn-danger pull-right">Delete</a>
								</div>
								<div class="col-md-4 alignright big">
									<?php echo inr_format($amount) ?>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="col-md-6">
					<!-- <legend>Sub Vouchers</legend> -->
			    	<table class="table table-condensed table-striped" id="SubVoucher">
					<thead>
					<tr>
						<th>Sr No</th>
						<th>Date</th>
						<th>Debit</th>
						<th>Name</th>
						<th>Credit</th>
						<th>Name</th>
						<th>Amount</th>
					</tr>
					</thead>
					
					<tbody>
					<?php 
						$sr_no = 0;
						foreach ($sub_vouchers as $sv_row) {
							if ($sv_row['id'] > 0) {
								$bgcolor = '';
								if ($sv_row['id3'] == $row['id3'])
									$bgcolor = 'Current';
								echo '<tr>
						<td class="'.$bgcolor.'">' . anchor($this->_clspath.$this->_class.'/edit/'.$row['voucher_book_id'].'/'.$id2['id2'].'/'.$sv_row['id3'], 
							str_pad($sv_row['id3'], 3, '0', STR_PAD_LEFT)) . '</td>
						<td class="'.$bgcolor.'">' . $sv_row['date'] . '</td>
						<td class="'.$bgcolor.'">' . $sv_row['dr_code'] . '</td>
						<td class="tiny orange '.$bgcolor.'">' . $sv_row['dr_name'] . '</td>
						<td class="'.$bgcolor.'">' . $sv_row['cr_code'] . '</td>
						<td class="tiny orange '.$bgcolor.'">' . $sv_row['cr_name'] . '</td>
						<td class="alignright '.$bgcolor.'">' . inr_format($sv_row['amount']) . '</td>
					</tr>';
								$sr_no = $sv_row['id3'];
							}
						}
					?>
					</tbody>
					</table>
				</div>
			</div>
		</fieldset>
	</div>
</div>

</form>

<script>
function checkVoucherDate() {
	var date = $("#Date").val();
	$.get("<?php echo base_url($this->_clspath.$this->_class.'/checkVoucherDate/'.$row['voucher_book_id'].'/'.$row['id2']) ?>/"+date);
}

function showTaxes() {
	var drTds  = $("#DebitTDS").val();
	var drStax = $("#DebitSTAX").val();
	var crTds  = $("#CreditTDS").val();
	var crStax = $("#CreditSTAX").val();
	
	if (drStax > 0 || crStax > 0)
		$("#STAX").removeClass('hide');
	else
		$("#STAX").addClass('hide');

	if (drTds > 0 || crTds > 0)
		$("#TDS").removeClass('hide');
	else
		$("#TDS").addClass('hide');

}

$(document).ready(function(){
	showTaxes();

	$('#SubVoucher tr td').on("click", function() {
		if ($(this).children('a').size() == 1) {
			window.location = $(this).children('a').attr('href');
		}
		else if ($(this).children('a').size() == 0 &&
			$(this).parents('tr').children('td').children('a:first').size() == 1) {
			window.location = $(this).parents('tr').children('td').children('a:first').attr('href');
		}
	});

	$("#DebitAccount").autocomplete({
		source: "<?php echo site_url('accounting/ledger/ajax') ?>",
		minLength: 1,
		open: function(event, ui) {
            $(this).autocomplete('widget').css({
                "width": 600
            });
        },
		focus: function(event, ui) {
			$("#DebitAccount").val(ui.item.code);
			return false;
		},
		select: function(event, ui) {
			$("#DebitAccountID").val(ui.item.id);
			$("#DebitAccount").val(ui.item.code + ' - ' + ui.item.name);
			$("#DebitTDS").val(ui.item.tds_class_id);
			$("#DebitSTAX").val(ui.item.stax_category_id);
			$("#DebitAccountClosing").text(ui.item.closing);
			// Changing Vessel ID in source of BlankBL to fetch BL of those Vessel ID only
			$("#BlankBLNo").autocomplete('option','source', "<?php echo site_url($this->_clspath.$this->_class.'/ajaxBL') ?>/" + ui.item.id);
			showTaxes();
			return false;
		}
	})
	.data('ui-autocomplete')._renderItem = function(ul, item) {
		return $('<li></li>')
			.data('item.autocomplete', item)
			.append('<a><span class="tiny"><strong class="blueDark">' + item.code + '</strong> ' + item.name + ' <span class="orange">' + item.group_name + '</span> <span class="blue">' + item.closing + '</span></span></a>')
			.appendTo(ul);
	};

	$("#CreditAccount").autocomplete({
		source: "<?php echo site_url('accounting/ledger/ajax') ?>",
		minLength: 1,
		open: function(event, ui) {
            $(this).autocomplete('widget').css({
                "width": 600
            });
        },
		focus: function(event, ui) {
			$("#CreditAccount").val(ui.item.code);
			return false;
		},
		select: function(event, ui) {
			$("#CreditAccountID").val(ui.item.id);
			$("#CreditAccount").val(ui.item.code + ' - ' + ui.item.name);
			$("#CreditTDS").val(ui.item.tds_class_id);
			$("#CreditSTAX").val(ui.item.stax_category_id);
			$("#CreditAccountClosing").text(ui.item.closing);
			showTaxes();
			return false;
		}
	})
	.data('ui-autocomplete')._renderItem = function(ul, item) {
		return $('<li></li>')
			.data('item.autocomplete', item)
			.append('<a><span class="tiny"><strong class="blueDark">' + item.code + '</strong> ' + item.name + ' <span class="orange">' + item.group_name + '</span> <span class="blue">' + item.closing + '</span></span></a>')
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

	$('.DataEntry').on('keydown.autocomplete', '.BlankBLNo', function(event, items) {
		var id = $(this).parent('td').parent('tr').find('.BlankJobID');
		$(this).autocomplete({
			source: "<?php echo site_url($this->_clspath.$this->_class.'/ajaxBL') ?>/" + $("#DebitAccountID").val(),
			minLength: 1,
			open: function(event, ui) {
	            $(this).autocomplete('widget').css({
	                "width": 600
	            });
	        },
			focus: function(event, ui) {
				$(this).val(ui.item.bl_no);
				return false;
			},
			select: function(event, ui) {
				$(id).val(ui.item.id);
				$(this).val(ui.item.bl_no);
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
				.append('<a><span class="blueDark">' + item.bl_no + '</span> ' + item.party + '</a>')
				.appendTo(ul);
		};
	});

	$('.DataEntry').on('keydown.autocomplete', '.BlankBillItem', function(event, items) {
		var id = $(this).parent('td').parent('tr').find('.BlankBillItemID');
		$(this).autocomplete({
			source: "<?php echo site_url('accounting/ledger/ajaxLedgers/Bill Items') ?>",
			minLength: 1,
			focus: function(event, ui) {
				$(this).val(ui.item.name);
				return false;
			},
			select: function(event, ui) {
				$(id).val(ui.item.id);
				$(this).text(ui.item.name);
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
