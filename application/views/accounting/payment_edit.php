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

<div class="row">
	<div class="col-md-6" id="VoucherDetail">
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
				<?php echo '<a href="' . base_url($this->_clspath.$this->_class.'/edit/'.$row['voucher_book_id']) . '" class="btn btn-xs btn-success" id="NewVoucher"><i class="fa fa-plus"></i> <u>V</u>oucher</a>
					<a href="' . base_url($this->_clspath.$this->_class.'/edit/'.$row['voucher_book_id'].'/0/'.$row['id']) . '" class="btn btn-xs btn-success" id="NewSubVoucher"><i class="fa fa-plus"></i> <u>S</u>ub Voucher</a>'; ?>
			</div>
			
			<div class="card-body">
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label class="control-label">Company (FY)</label><br />
							<span class="big"><?php echo $default_company['code'] . ' (' . str_replace('_', '-', $default_company['financial_year']) .  ')' ?></span>
						</div>
					</div>

					<div class="col-md-3">
						<div class="form-group">
							<label class="control-label">Voucher No</label><br />
							<span class="big"><?php echo $row['id2_format'] ?></span>
						</div>
					</div>

					<div class="col-md-3">
						<div class="form-group">
							<label class="control-label">Amount</label><br />
							<span class="big"><?php echo inr_format(number_format($amount, 2, '.', '')) ?></span>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-2">
						<div class="form-group<?php echo (strlen(form_error('id3')) > 0 ? ' has-error' : '') ?>">
							<label class="control-label">Sr. No.</label>
							<input type="text" class="form-control form-control-sm Numeric" name="id3" value="<?php echo $row['id3'] ?>" />
						</div>
					</div>

					<div class="col-md-4">
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
						<div class="form-group">
							<label class="control-label">Category</label>
							<?php echo form_dropdown('category', getEnumSetOptions('vouchers', 'category'), $row['category'], 'class="form-control form-control-sm"') ?>
						</div>
					</div>

					<div class="col-md-2">
						<?php 
						if ($row['category'] == 'Journal') {
							echo '<div class="form-group"><label class="control-label">Journal</label><br />';
							echo anchor($this->_clspath.$this->_class.'/journals/'.$row['dr_ledger_id'].'/'.$row['id'], 'Select (<span class="bold">' . $voucher_vouchers . '</span>)', 'class="btn btn-xs ' . ($voucher_vouchers > 0 ? 'btn-primary' : 'btn-danger') . ' Popup"') . '</div>';
						}
						else if ($row['category'] == 'Invoices') {
							echo '<div class="form-group"><label class="control-label">Invoices</label><br />';
							echo anchor($this->_clspath.$this->_class.'/invoices/'.$row['voucher_book_id'].'/'.$row['id'], 'Select (<span class="bold">' . $voucher_vouchers . '</span>)', 'class="btn btn-xs ' . ($voucher_vouchers > 0 ? 'btn-primary' : 'btn-danger') . ' Popup"') . '</div>';
						}
						else if ($row['category'] == 'Pumps') {
							echo '<div class="form-group"><label class="control-label">Pumps</label><br />';
							echo anchor($this->_clspath.$this->_class.'/pumps/'.$row['dr_ledger_id'].'/'.$row['id'], 'Select (<span class="bold">' . count($pumps) . '</span>)', 'class="btn btn-xs ' . (count($pumps) > 0 ? 'btn-primary' : 'btn-danger') . ' Popup"') . '</div>';
						}
						else if ($row['category'] == 'Trips') {
							echo '<div class="form-group"><label class="control-label">Trips</label><br />';
							echo anchor($this->_clspath.$this->_class.'/trips/'.$row['dr_ledger_id'].'/'.$row['id'], 'Select (<span class="bold">' . count($trips) . '</span>)', 'class="btn btn-xs ' . (count($trips) > 0 ? 'btn-primary' : 'btn-danger') . ' Popup"') . '</div>';
						}
						?>
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
					<div class="col-md-4">
						<div class="form-group<?php echo (strlen(form_error('amount')) > 0 ? ' has-error' : '') ?>">
							<label class="control-label">Amount</label>
							<input type="text" class="form-control form-control-sm Numeric big col-md-12" name="amount" value="<?php echo $row['amount'] ?>" id="Amount" />
						</div>
					</div>

					<div class="col-md-2">
						<div class="form-group">
							<label class="control-label">Difference</label><br />
							<span class="label <?php echo ($diff_amount < 0 ? 'label-danger' : 'label-success') ?>" id="AmountDiff"><?php echo inr_format($diff_amount) ?></span>
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
								<input type="text" class="form-control form-control-sm DatePicker" name="cheque_date" value="<?php echo $row['cheque_date']; ?>">
								<div class="input-group-append">
									<div class="input-group-text"><i class="icon-calendar"></i></div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-3">
						<div class="form-group<?php echo (strlen(form_error('not_over_amount')) > 0 ? ' has-error' : '') ?>">
							<label class="control-label">Not Over Amount</label>
							<input type="text" class="form-control form-control-sm Numeric" name="not_over_amount" value="<?php echo $row['not_over_amount'] ?>" />
						</div>
					</div>

					<div class="col-md-3">
						<div class="form-group">
							<label class="control-label">Security Cheque</label>
							<?php echo form_dropdown('security_cheque', getEnumSetOptions('vouchers', 'security_cheque'), $row['security_cheque'], 'class="form-control form-control-sm"') ?>
						</div>
					</div>

					<div class="col-md-3">
						<div class="form-group<?php echo (strlen(form_error('reconciliation_date')) > 0 ? ' has-error' : '') ?>">
							<label class="control-label">Reconciliation Date</label>
							<div class="input-group input-group-sm">
								<input type="text" class="form-control form-control-sm DatePicker" name="reconciliation_date" value="<?php echo $row['reconciliation_date']; ?>">
								<div class="input-group-append">
									<div class="input-group-text"><i class="icon-calendar"></i></div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-5 TaxPaymentDetails <?php echo ($row['dr_tds_class_id'] > 0 || $row['dr_stax_category_id'] > 0 ? 'show' : 'hide') ?>">
						<div class="form-group<?php echo (strlen(form_error('tds_stax_bsr_code')) > 0 ? ' has-error' : '') ?>">
							<label class="control-label">BSR Code</label>
							<input type="text" class="form-control form-control-sm" name="tds_stax_bsr_code" value="<?php echo $row['tds_stax_bsr_code'] ?>" />
						</div>
					</div>

					<div class="col-md-4 TaxPaymentDetails <?php echo ($row['tds_stax_challan_no'] > 0 || $row['dr_stax_category_id'] > 0 ? 'show' : 'hide') ?>">
						<div class="form-group<?php echo (strlen(form_error('tds_stax_challan_no')) > 0 ? ' has-error' : '') ?>">
							<label class="control-label">Challan No</label>
							<input type="text" class="form-control form-control-sm" name="tds_stax_challan_no" value="<?php echo $row['tds_stax_challan_no'] ?>">
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
						<th width="60px">Job No</th>
						<th>BL No</th>
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
							<td>' . $vjd['bl_no'] . '</td>
							<td>' . $vjd['bill_item_name'] . '</td>
							<td><input type="text" class="form-control form-control-sm Numeric" name="vd_amount[' . $vjd['id'] . ']" value="' . $vjd['amount'] . '" /></td>
							<td class="aligncenter">' . form_checkbox(array('name' => 'delete_id['.$vjd['id'].']', 'value' => $vjd['id'], 'checked' => false, 'class' => 'DeleteCheckbox')) . '</td>
						</tr>';
					} ?>

					<tr class="TemplateRow">
						<td></td>
						<td colspan="2"><input type="hidden" class="form-control form-control-sm Validate" name="new_job_id[]" value="" />
							<input type="text" class="form-control form-control-sm BlankBL Validate Focus" value="" /></td>
						<td><input type="hidden" class="form-control form-control-sm Validate" name="new_bill_item_id[]" value="" />
							<input type="text" class="form-control form-control-sm BlankBillItem Validate" value="" /></td>
						<td><input type="text" class="form-control form-control-sm Numeric Validate" name="new_amount[]" value="" /></td>
						<td class="aligncenter"><button type="submit" class="btn btn-success btn-sm AddButton"><i class="icon-white icon-plus"></i></button></td>
					</tr>
				</tbody>
				</table>
			</div>
					
			<div class="card-footer">
				<div class="row">
					<div class="col-md-9">
						<button type="button" class="btn btn-success UpdateButton" id="Update"><u>U</u>pdate</button>&nbsp;&nbsp;
						<a href="#modal-delete" data-toggle="modal" class="btn btn-danger pull-right">Delete</a>&nbsp;&nbsp;
						<div class="btn-group">
							<?php 
							echo anchor($this->_clspath.$this->_class."/preview/".$row['voucher_book_id'] . "/" . $row['id'] . "/0", 'Preview', 'class="btn btn-default Popup"'), 
							anchor($this->_clspath.$this->_class."/preview/".$row['voucher_book_id'] . "/" . $row['id'] . "/1", 'PDF', 'class="btn btn-default Popup"'); ?>
						</div>
					</div>
					<div class="col-md-3 alignright big">
						<?php echo inr_format($amount) ?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-md-6">
		<?php
		$document_link = '';
		foreach ($documents as $did => $r) 
			$document_link .= '<li><a href="#Document_' . $did . '" data-toggle="tab"><i class="icon-paperclip"></i> Document</a></li>'; 
		?>

		<div class="card card-default">
			<div class="card-heading panel-tabs">
				<h3 class="card-title"><ul class="nav nav-tabs">
					<li class="active"><a href="#SubVouchers" data-toggle="tab">Sub Vouchers</a></li><?php echo  $document_link . ($row['id'] > 0 ? '<li class="pull-right"><a href="#modal-document" class="green" data-toggle="modal"><i class="fa fa-plus"></i> Attach Bill</a></li>' : '') ?></ul></h3>
			</div>

			<div class="tab-content">
				<div class="tab-pane active" id="SubVouchers">
					<table class="table table-condensed table-striped tiny" id="SubVoucher">
					<thead>
					<tr>
						<th>Sr No</th>
						<th>Date</th>
						<th>Debit</th>
						<th>Name</th>
						<th>Credit</th>
						<th>Name</th>
						<th>Amount</th>
						<th width="24px" class="aligncenter"><a href="#" class="CheckAll" checkbox-class="VoucherDelete"><i class="icon-trashcan"></i></a></th>
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
						<td class="'.$bgcolor.'">' . anchor($this->_clspath.$this->_class.'/edit/'.$row['voucher_book_id'].'/'.$sv_row['id'], 
							$sv_row['id3']) . '</td>
						<td class="'.$bgcolor.'">' . $sv_row['date'] . '</td>
						<td class="'.$bgcolor.'">' . $sv_row['dr_code'] . '</td>
						<td class="tiny orange '.$bgcolor.'">' . $sv_row['dr_name'] . '</td>
						<td class="'.$bgcolor.'">' . $sv_row['cr_code'] . '</td>
						<td class="tiny orange '.$bgcolor.'">' . $sv_row['cr_name'] . '</td>
						<td class="alignright '.$bgcolor.'">' . inr_format(number_format($sv_row['amount'], 2, '.', '')) . '</td>
						<td class="aligncenter NoClick '.$bgcolor.'">' . form_checkbox(array('name' => 'delete_voucher_id['.$sv_row['id'].']', 'value' => $sv_row['id'], 'checked' => false, 'class' => 'VoucherDelete')) . '</td>
					</tr>';
								$sr_no = $sv_row['id3'];
							}
						}
					?>
					</tbody>
					</table>
				</div>

				<?php 
				foreach ($documents as $did => $r) {
					echo '
				<div class="tab-pane" id="Document_' . $did . '">
					<div class="row">
						<div class="col-md-4">
							<h6>' . $r['name'] . '</h6>
						</div>
						<div class="col-md-8">
							 <div id="PageLinks_' . $did . '"><button type="button" class="btn btn-danger btn-sm" onclick="javascript: detachDocument(' . $did . ')"><i class="icon-trash"></i> Detach</button>&nbsp;
							 </div>
						</div>
					</div>';
					
					if (strtolower($r['type']) == 'pdf') {
						echo "
					<canvas id=\"Canvas_" . $did . "\">
					<script>
					'use strict';
					PDFJS.workerSrc = '" . base_url('assets/pdfjs/build/pdf.worker.js') . "';
					PDFJS.getDocument('" . str_replace("'", "\'", $r['url']) . "').then(function(pdf) {

						var pages = pdf.numPages;
						var i;
						for(i = 1; i <= pages; i++) {
							$('#PageLinks_" . $did . "').append('<a href=\"javascript: getPage(' + i + ')\" class=\"btn btn-sm btn-info\">' + i + '</a>');
						}

						pdf.getPage(1).then(function(page) {
							var scale = 1.5;
							var viewport = page.getViewport(scale);
							var canvas = document.getElementById('Canvas_" . $did . "');
							var context = canvas.getContext('2d');
							canvas.height = viewport.height;
							canvas.width = viewport.width;
							var renderContext = {
								canvasContext: context,
								viewport: viewport
							};
							page.render(renderContext);
							$('#Loading').addClass('hide');
						});
					});

					function getPage(page_no) {
	$('.page_btns').removeClass('btn-primary').addClass('btn-info');
	$('#page_btn_'+page_no).removeClass('btn-info').addClass('btn-primary');
						PDFJS.getDocument('" . str_replace("'", "\'", $r['url']) . "').then(function(pdf) {
							pdf.getPage(page_no).then(function(page) {
								var scale = 1.5;
								var viewport = page.getViewport(scale);
								var canvas = document.getElementById('Canvas_" . $did . "');
								var context = canvas.getContext('2d');
								canvas.height = viewport.height;
								canvas.width = viewport.width;
								var renderContext = {
									canvasContext: context,
									viewport: viewport
								};
								page.render(renderContext);
							});
						});
					}
					</script>
					</canvas>";
					}
					else {
						echo '<img src="' . $r['url'] . '" />';
					}
				echo "</div>";
				}
				?>
			</div>
		</div>


		<?php if ($row['category'] == 'Journal') : ?>
		<table class="table table-condensed table-striped">
		<thead>
		<tr>
			<th>Voucher No</th>
			<th>Date</th>
			<th>Bill No</th>
			<th>Bill Date</th>
			<th>Amount</th>
			<th>TDS Amount</th>
			<th width="100px">Due Amount</th>
		</tr>
		</thead>

		<tbody>
		<?php 
			$total = array(
				'due_amount'  => 0,
				'paid_amount' => 0,
			);
			foreach ($journals as $r) {
				$total['due_amount']  = bcadd($total['due_amount'], $r['due_amount'], 2);
				$total['paid_amount'] = bcadd($total['paid_amount'], $r['paid_amount'], 2);

				echo '<tr>
			<td class="aligncener tiny">' . anchor('accounting'.'/'.underscore($r['url']), $r['id2_format'], 'target="_blank"') . '</td>
			<td class="aligncener tiny">' . $r['voucher_date'] . '</td>
			<td class="aligncener tiny">' . $r['invoice_no'] . '</td>
			<td class="aligncener tiny">' . $r['invoice_date'] . '</td>
			<td class="tiny">' . $r['due_amount'] . '</td>
			<td class="tiny">' . $r['tds_amount'] . '</td>
			<td><input type="text" class="form-control form-control-sm Numeric" name="paid_amount[' . $r['voucher_id2'] . ']" value="' . $r['paid_amount'] . '" /></td>
		</tr>';
			}

			echo '</tbody>

		<tfoot>
		<tr>
			<th colspan="4" class="alignright">Total</th>
			<th>' . $total['due_amount'] . '</th>
			<th></th>
			<th>' . $total['paid_amount'] . '</th>
		</tr>
		</tfoot>
		</table>
		</form>';
		endif;

		
		if ($row['category'] == 'Invoices') : ?>
		<table class="table table-condensed table-striped tiny">
		<thead>
		<tr>
			<th>Invoice No</th>
			<th>Date</th>
			<th>Party</th>
			<th>Job No</th>
			<th>Amount</th>
			<th width="100px">Paid Amount</th>
		</tr>
		</thead>

		<tbody>
		<?php 
			$total = array(
				'amount'      => 0,
				'paid_amount' => 0,
			);
			foreach ($invoices as $r) {
				$total['amount']      = bcadd($total['amount'], $r['amount'], 2);
				$total['paid_amount'] = bcadd($total['paid_amount'], $r['paid_amount'], 2);

				echo '<tr>
			<td class="aligncener tiny">' . anchor('accounting'.'/'.underscore($r['url']), $r['id2_format'], 'target="_blank"') . '</td>
			<td class="aligncener tiny">' . $r['voucher_date'] . '</td>
			<td class="aligncener tiny">' . $r['party_name'] . '</td>
			<td class="aligncener tiny">' . $r['job_no'] . '</td>
			<td class="alignright tiny">' . $r['amount'] . '</td>
			<td><input type="text" class="form-control form-control-sm Numeric" name="paid_amount[' . $r['voucher_id2'] . ']" value="' . $r['paid_amount'] . '" /></td>
		</tr>';
			}

			echo '</tbody>

		<tfoot>
		<tr>
			<th colspan="4" class="alignright">Total</th>
			<th class="alignright">' . $total['amount'] . '</th>
			<th class="alignright">' . $total['paid_amount'] . '</th>
		</tr>
		</tfoot>
		</table>
		</form>';
		endif;


		if ($row['category'] == 'Pumps') : ?>
		<table class="table table-condensed table-striped tiny">
		<thead>
		<tr>
			<th>Date</th>
			<th>Job No</th>
			<th>Container No</th>
			<th>Size</th>
			<th>Vehicle No</th>
			<th>Party / <span class="orange">Party Ref. No</span></th>
			<th>Transporter</th>
			<th>Rate</th>
			<th>LR No</th>
			<th>Pump</th>
			<th>Slip No</th>
			<th>Pump Adv.</th>
		</tr>
		</thead>

		<tbody>
		<?php 
			$total = array(
				'pump_advance'  => 0,
			);
			foreach ($pumps as $r) {
				$total['pump_advance']  = bcadd($total['pump_advance'], $r['pump_advance'], 2);

				echo '<tr>
			<td class="tiny">' . anchor('transport/trip/edit/Container/'.$r['trip_id'], $r['date'], 'target="_blank"') . '</td>
			<td class="tiny">' . $r['job_no'] . '</td>
			<td class="tiny">' . $r['container_no'] . '</td>
			<td class="tiny">' . $r['container_size'] . '</td>
			<td class="tiny">' . ($r['self'] ? '<span class="label label-success">' . $r['registration_no'] . '</span>' : $r['registration_no']) . '</td>
			<td class="tiny">' . $r['party_name'] . '<br /><span class="orange">' . $r['party_reference_no'] . '</span></td>
			<td class="tiny">' . $r['transporter_name'] . '</td>
			<td class="tiny">' . $r['transporter_rate'] . '</td>
			<td class="tiny">' . $r['lr_no'] . '</td>
			<td class="tiny">' . $r['pump_agent'] . '</td>
			<td class="tiny">' . $r['slip_no'] . '</td>
			<td class="tiny">' . $r['pump_advance'] . '</td>
		</tr>';
			}

			echo '</tbody>

		<tfoot>
		<tr>
			<th colspan="11" class="alignright">Total</th>
			<th class="alignright">' . $total['pump_advance'] . '</th>
		</tr>
		</tfoot>
		</table>
		</form>';
		endif;


		if ($row['category'] == 'Trips') : ?>
		<table class="table table-condensed table-striped tiny">
		<thead>
		<tr>
			<th>Date</th>
			<th>Job No</th>
			<th>Container No</th>
			<th>Size</th>
			<th>Vehicle No</th>
			<th>Party / <span class="orange">Party Ref. No</span></th>
			<th>Transporter</th>
			<th>Rate</th>
			<th>LR No</th>
			<th>Trip Adv.</th>
			<th>Pump Adv.</th>
			<th>Paid</th>
			<th>Balance</th>
		</tr>
		</thead>

		<tbody>
		<?php 
			$total = array(
				'trip_advance' => 0,
				'pump_advance' => 0,
				'paid_amount'  => 0,
				'balacne'      => 0,
			);
			foreach ($trips as $r) {
				$total['trip_advance'] = bcadd($total['trip_advance'], $r['trip_advance'], 2);
				$total['pump_advance'] = bcadd($total['pump_advance'], $r['pump_advance'], 2);
				$total['paid_amount']  = bcadd($total['paid_amount'], $r['paid_amount'], 2);
				$total['balance']      = bcadd($total['balance'], $r['balance'], 2);

				echo '<tr>
			<td class="tiny">' . anchor('transport/trip/edit/Container/'.$r['trip_id'], $r['date'], 'target="_blank"') . '</td>
			<td class="tiny">' . $r['job_no'] . '</td>
			<td class="tiny">' . $r['container_no'] . '</td>
			<td class="tiny">' . $r['container_size'] . '</td>
			<td class="tiny">' . ($r['self'] ? '<span class="label label-success">' . $r['registration_no'] . '</span>' : $r['registration_no']) . '</td>
			<td class="tiny">' . $r['party_name'] . '<br /><span class="orange">' . $r['party_reference_no'] . '</span></td>
			<td class="tiny">' . $r['transporter_name'] . '</td>
			<td class="tiny">' . $r['transporter_rate'] . '</td>
			<td class="tiny">' . $r['lr_no'] . '</td>
			<td class="tiny">' . $r['trip_advance'] . '</td>
			<td class="tiny">' . $r['pump_advance'] . '</td>
			<td><input type="text" class="form-control form-control-sm Numeric" name="paid_amount[' . $r['id'] . ']" value="' . $r['paid_amount'] . '" /></td>
			<td class="tiny">' . $r['balance'] . '</td>
		</tr>';
			}

			echo '</tbody>

		<tfoot>
		<tr>
			<th colspan="9" class="alignright">Total</th>
			<th class="alignright">' . $total['trip_advance'] . '</th>
			<th class="alignright">' . $total['pump_advance'] . '</th>
			<th class="alignright">' . $total['paid_amount'] . '</th>
			<th class="alignright">' . $total['balance'] . '</th>
		</tr>
		</tfoot>
		</table>
		</form>';
		endif;
		?>
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

	$("#DebitAccount").typeahead({
		hint: false,
		highlight: true,
		minLength: 3
	}, {
		name: 'tt_debit',
		displayKey: 'name',
		source: function(query, process) {
			return $.ajax({ 
				url: '<?php echo site_url('accounting/ledger/ajaxLedger') ?>',
				type: 'post',
				data: {
					term: query,
					date: $('#Date').val(),
				},
				dataType: 'json',
				success: function (result) {
					return process(result);
				}
			});
		},
		templates: {
			empty: ['<div class="tt-no-result">Unable to find any results that match the current query</div>'],
			suggestion: Handlebars.compile('<p class="tiny"><span class="blueDark">{{code}}</span> - {{name}}<br /><span class="tiny red">{{group_name}}</span></p>')
		}
	}).on('typeahead:selected', function(obj, datum) {
		$("#DebitAccountID").val(datum.id);
		$("#DebitAccount").val(datum.code + ' - ' + datum.name);
		$("#DebitAccountClosing").text(datum.closing);

		if (datum.id == 0)
			$("#DebitAccount").parent().addClass('has-error');
		else
			$("#DebitAccount").parent().removeClass('has-error');
		// Changing Vessel ID in source of BlankBL to fetch BL of those Vessel ID only
		if (datum.vessel_id > 0)
			vessel_ledger_id = datum.id;
		else {
			vessel_ledger_id = 0;
		}
		$("#TaxPaymentDetails").removeClass('hide');
	});

	$("#CreditAccount").typeahead({
		hint: false,
		highlight: true,
		minLength: 3
	}, {
		name: 'tt_credit',
		displayKey: 'name',
		source: function(query, process) {
			return $.ajax({ 
				url: '<?php echo site_url('accounting/ledger/ajaxLedger') ?>',
				type: 'post',
				data: {
					term: query,
					date: $('#Date').val(),
				},
				dataType: 'json',
				success: function (result) {
					return process(result);
				}
			});
		},
		templates: {
			empty: ['<div class="tt-no-result">Unable to find any results that match the current query</div>'],
			suggestion: Handlebars.compile('<p class="tiny"><span class="blueDark">{{code}}</span> - {{name}}<br /><span class="tiny red">{{group_name}}</span></p>')
		}
	}).on('typeahead:selected', function(obj, datum) {
		$("#CreditAccountID").val(datum.id);
		$("#CreditAccount").val(datum.code + ' - ' + datum.name);
		$("#CreditAccountClosing").text(datum.closing);

		if (datum.id == 0)
			$("#CreditAccount").parent().addClass('has-error');
		else
			$("#CreditAccount").parent().removeClass('has-error');
		// Changing Vessel ID in source of BlankBL to fetch BL of those Vessel ID only
		if (datum.vessel_id > 0)
			vessel_ledger_id = datum.id;
	});

	$('#Narration').typeahead({
		hint: false,
		highlight: true,
		minLength: 1
	}, {
		name: 'tt_narration',
		displayKey: 'name',
		source: function(query, process) {
			return $.ajax({ 
				url: '<?php echo site_url('/master/narration/getJSON') ?>',
				type: 'get',
				data: {
					term: query,
					invoice_no: $("#InvoiceNo").val(),
				},
				dataType: 'json',
				success: function (result) {
					return process(result);
				}
			});
		},
		templates: {
			empty: ['<div class="tt-no-result">Unable to find any results that match the current query</div>'],
			suggestion: Handlebars.compile('<p>{{name}}</p>')
		}
	});

	$('.DataEntry').on('keydown.autocomplete', ".BlankBL", function(event, items) {
		var id = $(this).prevAll("input");
		$(this).autocomplete({
			source: "<?php echo site_url($this->_clspath.$this->_class.'/ajaxBL') ?>",
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
			.append('<a><span class="tiny">' + item.id2_format + ' - <span class="blueDark">' + item.bl_no + '</span> ' + item.party + '</span></a>')
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
