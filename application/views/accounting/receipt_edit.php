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

<div id="modal-detach" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><i class="icon-times"></i></button>
				<h3>Confirm Deletion</h3>
			</div>
			<div class="modal-body"><p>Are you sure, you want to DETACH...?</p></div>
			<div class="modal-footer">
				<?php echo anchor("#", 'Detach', 'class="btn btn-danger" id="DetachUrl"') ?>
			</div>
		</div>
	</div>
</div>

<div id="modal-audit" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><i class="icon-times"></i></button>
				<h3>Audit Confirmation</h3>
			</div>
			<div class="modal-body"><p>Are you sure, you want to AUDIT...?</p></div>
			<div class="modal-footer">
				<?php echo anchor($this->_clspath.$this->_class.'/voucher_audit/'.$row['voucher_book_id'].'/'.$row['id']. '/Yes', 'Audit', 'class="btn btn-success"') ?>
			</div>
		</div>
	</div>
</div>

<div id="modal-unaudit" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><i class="icon-times"></i></button>
				<h3>Un-Audit Confirmation</h3>
			</div>
			<div class="modal-body"><p>Are you sure, you want to UN-AUDIT...?</p></div>
			<div class="modal-footer">
				<?php echo anchor($this->_clspath.$this->_class.'/voucher_audit/'.$row['voucher_book_id'].'/'.$row['id'].'/No', 'Un-Audit', 'class="btn btn-danger"') ?>
			</div>
		</div>
	</div>
</div>

<?php
echo form_open($this->uri->uri_string(), 'id="MainForm"');
echo form_hidden($id);
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
	<<div class="card-header">
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
						<?php // foreach ($row['documents'] as $doc) 
						// 	echo anchor($this->_clspath.'voucher_document/index/'.$doc['voucher_id'].'/'.$doc['id'], '<i class="icon-paperclip"></i>'); 
						?>
						<?php if ($row['id'] > 0) 
							echo '<a href="#modal-document" class="btn btn-success btn-xs" data-toggle="modal"><i class="fa fa-plus"></i></a>';
						?>
					</div>
				</div>
			</div>
		</fieldset>


		<fieldset>
			<div class="row">
				<div class="col-md-6" id="VoucherDetail">
					<div class="card card-default">
						<div class="card-header">
							<span class="card--links"><?php 
								if (($row['id'] > 0) && (Auth::get('username') == 'auditor')) { //Auth::get('username') == 'auditor'
									if ($row['audited'] == 'No')
										echo '<a href="#modal-audit" data-toggle="modal" class="btn btn-xs btn-success">Audit</a> ';
									else	
										echo '<a href="#modal-unaudit" data-toggle="modal" class="btn btn-xs btn-danger">Un-Audit</a> ';
								}
								echo '<a href="' . base_url($this->_clspath.$this->_class.'/edit/'.$row['voucher_book_id']) . '" class="btn btn-xs btn-success" id="NewVoucher"><i class="fa fa-plus"></i> <u>V</u>oucher</a>
								<a href="' . base_url($this->_clspath.$this->_class.'/edit/'.$row['voucher_book_id'].'/0/'.$row['id']) . '" class="btn btn-xs btn-success" id="NewSubVoucher"><i class="fa fa-plus"></i> <u>S</u>ub Voucher</a>'; ?></span>
							<h3 class="card-title">Sub Voucher Entry</h3>
						</div>
						
						<div class="card-body">
							<fieldset>
								<div class="row">
									<div class="col-md-3">
										<div class="form-group<?php echo (strlen(form_error('id3')) > 0 ? ' has-error' : '') ?>">
											<label class="control-label">Sr. No.</label>
											<input type="text" class="form-control form-control-sm Numeric" name="id3" value="<?php echo $row['id3'] ?>" />
										</div>
									</div>

									<div class="col-md-3">
										<div class="form-group<?php echo (strlen(form_error('date')) > 0 ? ' has-error' : '') ?>">
											<label class="control-label">Date</label>
											<div class="input-group input-group-sm">
												<input type="text" class="form-control form-control-sm DatePicker" name="date" value="<?php echo $row['date']; ?>" size="10" id="Date" onblur="javascript: checkVoucherDate();" />
												<div class="input-group-append">
													<div class="input-group-text"><i class="icon-calendar"></i></div>
												</div>
											</div>
										</div>
									</div>
								
									<div class="col-md-3">
										<div class="form-group<?php echo (strlen(form_error('cheque_no')) > 0 ? ' has-error' : '') ?>">
											<label class="control-label">Cheque No.</label>
											<input type="text" class="form-control form-control-sm" name="cheque_no" value="<?php echo $row['cheque_no'] ?>" />
										</div>
									</div>

									<div class="col-md-3">
										<div class="form-group<?php echo (strlen(form_error('cheque_date')) > 0 ? ' has-error' : '') ?>">
											<label class="control-label">Cheque Date</label>
											<div class="input-group input-group-sm">
												<input type="text" class="form-control form-control-sm DatePicker" name="cheque_date" value="<?php echo $row['cheque_date']; ?>" size="10" id="Date" onblur="javascript: checkVoucherDate();" />
												<div class="input-group-append">
													<div class="input-group-text"><i class="icon-calendar"></i></div>
												</div>
											</div>
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-md-9">
										<div class="form-group<?php echo (strlen(form_error('dr_ledger_id')) > 0 ? ' has-error' : '') ?>">
											<label class="control-label">Debit Account</label>
											
												<input type="hidden" name="dr_ledger_id" value="<?php echo $row['dr_ledger_id'] ?>" id="DebitAccountID" />
										<?php if ($voucher_book['default_ledger_id'] > 0 && $voucher_book['dr_cr'] == 'Dr' && substr($row['debit_account'], 0, 4) != 'CASH')
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
										<?php if ($voucher_book['default_ledger_id'] > 0 && $voucher_book['dr_cr'] == 'Cr' && substr($row['credit_account'], 0, 4) != 'CASH')
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
											<div class="input-group">
												<input type="text" class="form-control form-control-sm Numeric big" name="amount" value="<?php echo $row['amount'] ?>" />
												<span class="input-group-addon"><span class="label <?php echo ($diff_amount < 0 ? 'label-danger' : 'label-success') ?>" id="AmountDiff"><?php echo inr_format($diff_amount) ?></span></span>
											</div>
										</div>
									</div>

									<div class="col-md-3">
										<div class="form-group">
											<label class="control-label">Category</label>
											<?php echo form_dropdown('category', getEnumSetOptions('vouchers', 'category'), $row['category'], 'class="form-control form-control-sm"') ?>
										</div>
									</div>

									<div class="col-md-2">
										<?php 
										if ($row['category'] == 'Trips') {
											echo '<div class="form-group"><label class="control-label">Trips</label><br />';
											echo anchor($this->_clspath.$this->_class.'/trips/'.$row['voucher_book_id'].'/'.$row['id'], 'Select (<span class="bold">' . $total_trips . '</span>)', 'class="btn btn-xs ' . ($total_trips > 0 ? 'btn-primary' : 'btn-danger') . ' Popup"') . '</div>';
										}
										else if ($row['category'] == 'Invoices') {
											echo '<div class="form-group"><label class="control-label">Invoices</label><br />';
											echo anchor($this->_clspath.$this->_class.'/invoices/'.$row['cr_ledger_id'].'/'.$row['id'], 'Select (<span class="bold">' . $total_invoices . '</span>)', 'class="btn btn-xs ' . ($total_invoices > 0 ? 'btn-primary' : 'btn-danger') . ' Popup"') . '</div>';
										}
										?>
									</div>
								</div>

								<div class="form-group<?php echo (strlen(form_error('narration')) > 0 ? ' has-error' : '') ?>">
									<label class="control-label">Narration</label>
									<textarea class="form-control form-control-sm Text tiny col-md-12" name="narration" rows="1" id="Narration"><?php echo $row['narration'] ?></textarea>
								</div>

								<table class="table table-condensed table-striped DataEntry">
								<thead>
									<tr>
										<th width="24px">No</th>
										<th width="60px">Job No</th>
										<th>BL / BE-SB</th>
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
											<td class="aligncenter nowrap">' . $vjd['id2_format'] . '</td>
											<td>' . $vjd['bl_no'] . ' / ' . $vjd['be_sb'] . '</td>
											<td>' . $vjd['bill_item_name'] . '</td>
											<td><input type="text" class="form-control form-control-sm Numeric" name="vd_amount[' . $vjd['id'] . ']" value="' . $vjd['amount'] . '" /></td>
											<td class="aligncenter">' . form_checkbox(array('name' => 'delete_id['.$vjd['id'].']', 'value' => $vjd['id'], 'checked' => false, 'class' => 'DeleteCheckbox')) . '</td>
										</tr>';
									} ?>

									<tr class="TemplateRow">
										<td></td>
										<td colspan="2"><input type="hidden" class="form-control form-control-sm Validate" name="new_job_id[]" value="" />
											<input type="text" class="form-control form-control-sm BlankBLSB Validate Focus" value="" /></td>
										<td><input type="hidden" class="form-control form-control-sm Validate" name="new_bill_item_id[]" value="" />
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
									<button type="button" class="btn btn-success UpdateButton" id="Update"><u>U</u>pdate</button>&nbsp;&nbsp;
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
								if ($sv_row['id'] == $row['id'])
									$bgcolor = 'Current';
								echo '<tr>
						<td class="'.$bgcolor.'">' . anchor($this->_clspath.$this->_class.'/edit/'.$row['voucher_book_id'].'/'.$id['id'], 
							str_pad($sv_row['id3'], 3, '0', STR_PAD_LEFT)) . '</td>
						<td class="'.$bgcolor.'">' . $sv_row['date'] . '</td>
						<td class="'.$bgcolor.'">' . $sv_row['dr_code'] . '</td>
						<td class="tiny orange '.$bgcolor.'">' . $sv_row['dr_name'] . '</td>
						<td class="'.$bgcolor.'">' . $sv_row['cr_code'] . '</td>
						<td class="tiny orange '.$bgcolor.'">' . $sv_row['cr_name'] . '</td>
						<td class="alignright '.$bgcolor.'">' . inr_format(number_format($sv_row['amount'], 2, '.', '')) . '</td>
					</tr>';
								$sr_no = $sv_row['id3'];
							}
						}
					?>
					</tbody>
					</table>

					<?php 
					$i = 1;
					if ($row['category'] == 'Trips') : 
					?>
					<div class="card card-default">
						<div class="card-header">
							<h3 class="card-title">Trip(s)</h3>
						</div>
						
						<!-- <div class="card-body"></div> -->
						
						<table class="table table-condensed table-striped table-bordered">
						<thead>
							<th>No</th>
							<th>Container No</th>
							<th>Size</th>
							<th>Vehicle No</th>
							<th>Party Ref. No</th>
							<th>Party</th>
							<th>Transporter</th>
							<th>LR No</th>
							<th>Advance</th>
						</thead>
						
						<tbody>
						<?php 
							foreach($trips as $r) {
							echo '<tr>
								<td class="aligncenter">' . $i++ . '</td>
								<td>' . $r['container_no'] . '</td>
								<td>' . $r['container_size'] . '</td>
								<td>' . ($r['self'] ? '<span class="label label-success">' . $r['registration_no'] . '</span>' : $r['registration_no']) . '</td>
								<td>' . $r['party_reference_no'] . '</td>
								<td>' . $r['party_name'] . '</td>
								<td>' . $r['transporter_name'] . '</td>
								<td>' . anchor('transport/trip/edit/'.$r['id'], $r['lr_no'], 'target="_blank"') . '</td>
								<td><input type="text" class="form-control form-control-sm" name="party_advance[' . $r['voucher_trip_id'] . ']" value="' . $r['advance'] . '" /></td>
							</tr>';
							}
						?>
						</tbody>
						</table>
					</div>
					<?php 
					endif; 
					$i = 1;
					if ($row['category'] == 'Invoices') : 
					?>
					<div class="card card-default">
						<div class="card-header">
							<h3 class="card-title">Invoice(s)</h3>
						</div>
						
						<!-- <div class="card-body"></div> -->
						
						<table class="table table-condensed table-striped table-bordered">
						<thead>
							<th>No</th>
							<th>Invoice No</th>
							<th>Job No</th>
							<th>Date</th>
							<th>Inv. Amt.</th>
							<th>Net Amt.</th>
							<th width="100px">Amt.</th>
						</thead>
						
						<tbody>
						<?php 
							$total = array(
								'invoice_amount' => 0,
								'net_amount'     => 0,
								'amount'         => 0,
							);
							foreach($invoices as $r) {
								$total['invoice_amount'] = bcadd($total['invoice_amount'], $r['invoice_amount'], 2);
								$total['net_amount']     = bcadd($total['net_amount'], $r['net_amount'], 2);
								$total['amount']         = bcadd($total['amount'], $r['amount'], 2);

								echo '<tr>
								<td class="tiny aligncenter">' . $i++ . '</td>
								<td class="tiny">' . $r['id2_format'] . '</td>
								<td class="tiny">' . $r['date'] . '</td>
								<td class="tiny">' . $r['job_no'] . '</td>
								<td class="tiny alignright">' . $r['invoice_amount'] . '</td>
								<td class="tiny alignright">' . $r['balance_amount'] . '</td>
								<td><input type="text" class="form-control form-control-sm Numeric" name="part_receipt[' . $r['id'] . ']" value="' . $r['amount'] . '" /></td>
							</tr>';
							}
						?>
						</tbody>

						<tfoot>
							<tr>
								<th class="alignright" colspan="4">Total</th>
								<th class="alignright"><?php echo $total['invoice_amount'] ?></th>
								<th class="alignright"><?php echo $total['net_amount'] ?></th>
								<th class="alignright"><?php echo $total['amount'] ?></th>
							</tr>
						</tfoot>
						</table>
					</div>
					<?php endif; ?>	
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

$(document).ready(function(){
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
			$("#DebitAccountClosing").text(ui.item.closing);
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
			$("#CreditAccountClosing").text(ui.item.closing);
			// Changing Vessel ID in source of BlankBL to fetch BL of those Vessel ID only
			//$("#BlankBLSB").autocomplete('option','source', "<?php echo site_url($this->_clspath.$this->_class.'/ajaxBL') ?>/" + ui.item.id);
			$("#Reference").autocomplete('option','source', "<?php echo site_url('/accounting/ledger/ajaxReferenceByLedger') ?>/" + ui.item.id);
			return false;
		},
		response: function(event, ui) {
            if (ui.content.length === 0) {
                $("#CreditAccountID").val(0);
				$("#CreditAccount").val('');
				//$("#BlankBLSB").autocomplete('option','source', "<?php echo site_url($this->_clspath.$this->_class.'/ajaxBL') ?>/0");
            }
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

	$('.DataEntry').on('keydown.autocomplete', ".BlankBLSB", function(event, items) {
		var id = $(this).prevAll("input");
		$(this).autocomplete({
			source: "<?php echo site_url($this->_clspath.$this->_class.'/ajaxBL') ?>/" + $("#CreditAccountID").val(),
			minLength: 1,
			open: function(event, ui) {
				$(this).autocomplete('widget').css({
					"width": 600
				});
			},
			focus: function(event, ui) {
				$(this).val(ui.item.id2_format + ' ' + ui.item.bl_no);
				return false;
			},
			select: function(event, ui) {
				$(id).val(ui.item.id);
				$(this).val(ui.item.id2_format + ' ' + ui.item.bl_no);
				if ($("#CreditAccountID").val() == 0) {
					$("#CreditAccountID").val(ui.item.id);
					$("#CreditAccount").val(ui.item.code + ' - ' + ui.item.name);
					// Changing Party ID in source of BlankBL to fetch BL of those Party ID only
					$(this).autocomplete('option','source', "<?php echo site_url($this->_clspath.$this->_class.'/ajaxBL') ?>/" + ui.item.id);
				}
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
			.append('<a><span class="tiny">' + item.type + ' - <span class="blueDark">' + item.bl_no + '</span> <span class="orange">' + item.party + '</span> ' + item.code + ' - ' + item.name + '</span></a>')
			.appendTo(ul);
		};
	});

	$('.DataEntry').on('keydown.autocomplete', ".BlankBillItem", function(event, items) {
		var id = $(this).prevAll("input");
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
