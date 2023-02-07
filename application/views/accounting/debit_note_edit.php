<style>
td.NonSTax {
   background-color: #FCC !important;
}
</style>

<?php
$amount = 0;
foreach ($voucher_details as $vjd) {
	$amount += round($vjd['amount'], 0);
}
?>

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

<div id="modal-voucher-exists" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><i class="icon-times"></i></button>
				<h3>Bill Found</h3>
			</div>
			<div class="modal-body">
				<h1 class="red">Bill already exists.</h1>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

<div id="modal-email" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><i class="icon-times"></i></button>
				<h3>Send Email</h3>
			</div>
			<div class="modal-body">
				<p>Are you sure you want to send Email...?</p>
			</div>
			<div class="modal-footer">
				<?php echo anchor($this->_clspath.$this->_class."/preview/".$row['voucher_book_id'].'/'.$row['id2'].'/1/1', "Send Email", 'class="btn btn-success"') ?>
			</div>
		</div>
	</div>
</div>

<div id="modal-error" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><i class="icon-times"></i></button>
				<h3>Missing Details</h3>
			</div>
			<div class="modal-body">
				<p>Cannot load the Invoice details.</p>
				<ul>
					<li>Check if correct Indian port is selected in Job.</li>
					<li>Check if correct Product Name is selected or missing in Job.</li>
					<li>Check if Importer / HSS Party Ledger account is missing.</li>
					<li>Check if Importer / HSS Party Ledger account is connected to Party Master.</li>
					<li>Check if Importer / HSS Party Rate is missing.</li>
				</ul>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
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
echo form_hidden(array('job_id' => $row['job_id']));
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
			<div class="col-md-8">
				<div class="row">
					<div class="col-md-2">
						<div class="form-group">
							<label class="control-label">Company (FY)</label><br />
							<h5><?php echo $default_company['code'] . ' (' . str_replace('_', '-', $default_company['financial_year']) .  ')' ?></h5>
						</div>
					</div>

					<div class="col-md-2">
						<div class="form-group">
							<label class="control-label">Voucher No</label><br />
							<input type="text" class="form-control form-control-sm" name="id2_format" value="<?php echo $row['id2_format'] ?>" />
						</div>
					</div>

					<div class="col-md-4">
						<div class="form-group<?php echo (strlen(form_error('date')) > 0 ? ' has-error' : '') ?>">
							<label class="control-label">Date</label>
							<div class="input-group input-group-sm">
								<input type="text" class="form-control form-control-sm DatePicker" name="date" value="<?php echo $row['date']; ?>">
								<div class="input-group-append">
									<div class="input-group-text"><i class="icon-calendar"></i></div>
								</div>
							</div>
						</div>
					</div>

					<!-- <div class="col-md-2">
						<div class="form-group">
							<label class="control-label">Amount</label><br />
							<span class="big"><?php echo inr_format($amount) ?></span>
						</div>
					</div> -->

					<div class="col-md-4">
						<div class="form-group">
							<label class="control-label">Currency / Ex. Rate</label>
							<div class="input-group">
								<div class="input-group-btn">
									<?php echo form_dropdown('currency_id', getSelectOptions('currencies', 'id', 'code'), $row['currency_id'], 'class="btn"') ?>
								</div>
								<input type="text" class="form-control form-control-sm Numeric" name="exchange_rate" value="<?php echo $row['exchange_rate'] ?>" id="ExchangeRate" />
							</div>
						</div>
					</div>
				</div>

			<?php if($voucher_book['job_type'] != 'N/A') : ?>
				<div class="row">
					<div class="col-md-4">
						<div class="form-group">
							<label class="control-label">Job No / BL / SB No</label>
							<?php if (count($voucher_details) > 0) : ?>
								<h5><?php echo anchor(strtolower($row['type']).'/jobs/edit/'.$row['job_id'], $row['job_no'] . ' - ' . $row['bl_no'], 'target="_blank"') ?></h5>
							<?php else : ?>
								<div class="form-group<?php echo (strlen(form_error('bl_no')) > 0 ? ' has-error' : '') ?>">
									<div class="input-group col-md-12">
										<input type="hidden" name="job_id" value="<?php echo $row['job_id'] ?>" id="JobID" />
										<input type="text" class="form-control form-control-sm" name="bl_no" value="<?php echo $row['bl_no'] ?>" id="ajaxBLNo" />
										<div class="input-group-btn">
											<button class="btn btn-primary" type="button" onclick="javascript: loadJobDetails()" class="Popup" data-placement="right" rel="tooltip" data-original-title="Load Job Details..."><i class="icon-refresh"></i></button>
										</div>
									</div>
								</div>
							<?php endif; ?>
						</div>
					</div>

					<div class="col-md-2">
						<div class="form-group">
							<label class="control-label" id="#PiecesUnit">Pieces</label>
							<input type="text" class="form-control form-control-sm Numeric" name="pieces" value="<?php echo $row['pieces'] ?>" id="Pieces" />
						</div>
					</div>

					<div class="col-md-2">
						<div class="form-group">
							<label class="control-label" id="#PiecesUnit">CBM</label>
							<input type="text" class="form-control form-control-sm Numeric" name="cbm" value="<?php echo ($row['cargo_type'] == 'Bulk' ? $row['cbm'] : $row['net_weight']) ?>" id="CBM" />
						</div>
					</div>

					<div class="col-md-1">
						<div class="form-group">
							<label class="control-label">Cont. 20/40</label>
							<h5 id="Containers"><?php echo $row['container_20'] ?> / <?php echo $row['container_40'] ?></h5>
						</div>
					</div>

					<div class="col-md-3">
						<div class="form-group">
							<label class="control-label">Product Description</label>
							<input type="text" class="form-control form-control-sm" name="product_details" value="<?php echo $row['product_details'] ?>" id="ProductName" />
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-4">
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

					<div class="col-md-3">
						<div class="form-group">
							<label class="control-label">Importer</label><br />
							<span id="Importer"><?php echo '<span class="label label-info">' . (isset($importer['code']) ? $importer['code'] . " - " . $importer['name'] : '') . '</span> '; ?></span>
						</div>
					</div>

					<div class="col-md-3">
						<div class="form-group">
							<label class="control-label">HSS Seller</label><br />
							<span id="HighSeas"><?php foreach ($high_seas as $hss) {
								echo '<span class="label label-warning">' . $hss['name'] . '</span> ';
							} ?></span>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-4">
						<div class="form-group<?php echo (strlen(form_error('cr_ledger_id')) > 0 ? ' has-error' : '') ?>">
							<label class="control-label">Credit Account</label>
							<input type="hidden" name="cr_ledger_id" value="<?php echo $row['cr_ledger_id'] ?>" id="CreditAccountID" />
							<input type="text" class="form-control form-control-sm" name="credit_account" value="<?php echo $row['credit_account'] ?>" id="CreditAccount" />
						</div>
					</div>

					<div class="col-md-2">
						<div class="form-group">
							<label class="control-label">Closing</label><br />
							<span class="label label-warning" id="CreditAccountClosing"><?php echo inr_format($row['cr_closing']) . ($row['cr_closing'] > 0 ? ' Dr' : ' Cr'); ?></span>
						</div>
					</div>

					<div class="col-md-6">
						<div class="form-group">
							<label class="control-label">Vessel Ledger A/c</label><br />
							<span id="VesselLedgers"><?php
							if (isset($vessel_ledgers))
								foreach ($vessel_ledgers as $vl) {
									echo '<span class="label label-info">' . $vl['code'] . " - " . $vl['name'] . '</span> ';
								}
							?></span>
						</div>
					</div>
				</div>

			<?php elseif ($voucher_type == 'Credit Note') : ?>

				<div class="row">
					<div class="col-md-4">
						<div class="form-group<?php echo (strlen(form_error('cr_ledger_id')) > 0 ? ' has-error' : '') ?>">
							<label class="control-label">Credit Account</label>
							<input type="hidden" name="cr_ledger_id" value="<?php echo $row['cr_ledger_id'] ?>" id="CreditAccountID" />
							<input type="text" class="form-control form-control-sm" name="credit_account" value="<?php echo $row['credit_account'] ?>" id="CreditAccount" />
						</div>
					</div>

					<div class="col-md-2">
						<div class="form-group">
							<label class="control-label">Closing</label><br />
							<span class="label label-warning" id="CreditAccountClosing"><?php echo inr_format($row['cr_closing']) . ($row['cr_closing'] > 0 ? ' Dr' : ' Cr'); ?></span>
						</div>
					</div>

					<div class="col-md-4">
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

			<?php else : ?>

				<div class="row">
					<div class="col-md-4">
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

					<div class="col-md-4">
						<div class="form-group<?php echo (strlen(form_error('cr_ledger_id')) > 0 ? ' has-error' : '') ?>">
							<label class="control-label">Credit Account</label>
							<input type="hidden" name="cr_ledger_id" value="<?php echo $row['cr_ledger_id'] ?>" id="CreditAccountID" />
							<input type="text" class="form-control form-control-sm" name="credit_account" value="<?php echo $row['credit_account'] ?>" id="CreditAccount" />
						</div>
					</div>

					<div class="col-md-2">
						<div class="form-group">
							<label class="control-label">Closing</label><br />
							<span class="label label-warning" id="CreditAccountClosing"><?php echo inr_format($row['cr_closing']) . ($row['cr_closing'] > 0 ? ' Dr' : ' Cr'); ?></span>
						</div>
					</div>
				</div>

			<?php endif; ?>


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
			</div>

			<div class="col-md-4">
				<div class="card card-default">
					<div class="card-header">
						<h3 class="card-title">Vouchers Found</h3>
					</div>
					
					<table class="table table-condensed table-striped" id="VouchersFound">
					<thead>
					<tr>
						<th width="70px">Voucher</th>
						<th width="70px">Bill No</th>
						<th>Particular</th>
						<th width="80px">Amount</th>
					</tr>
					</thead>

					<tbody class="tiny">
						<?php 
							foreach ($vouchers['vouchers'] as $v) {
								echo '<tr>
						<td class="alignmiddle">' . anchor('/accounting/' . underscore($v['url']), $v['id2_format'], 'target="_blank"') . '</td>
						<td class="alignmiddle">' . $v['invoice_no'] . '</td>
						<td class="alignmiddle">' . $v['name'] . '</td>
						<td class="alignright">';
							if (! isset($vouchers['bills'][$v['code']]))
								echo '<span class="red">' . inr_format($v['amount'], 2) . '</span>';
							else if ($v['amount'] <= $vouchers['bills'][$v['code']]['amount'])
								echo anchor('/accounting/' . underscore($vouchers['bills'][$v['code']]['url']), '<span class="green">' . inr_format($v['amount'], 2) . '</span>', 'target="_blank"');
							else
								echo anchor('/accounting/' . underscore($vouchers['bills'][$v['code']]['url']), '<span class="orange">' . inr_format($v['amount'], 2) . '</span>', 'target="_blank"');
						echo '</td>
					</tr>';
							}
						?>
					</tbody>
					</table>
				</div>

				<div class="card card-default">
					<div class="card-header">
						<h3 class="card-title">Extra Activities</h3>
					</div>
					
					<table class="table table-condensed table-striped" id="ExtraActivity">
					<tbody class="tiny">
					<?php
						$hide_cols =  array('id', 'job_id', 'log_userid', 'log_ipaddr');
						$hide_vals =  array('0', 'N/A', 'No');
						if($export_activities) {
							foreach($export_activities as $field => $value) {
								if (!in_array($field, $hide_cols) && strlen($value) > 0 && !in_array($value, $hide_vals))
									echo '<tr><td>' . humanize($field) . '</td><td>' . $value . '</td></tr>';
							}
						}
					?>
					</tbody>
					</table>
				</div>
			</div>
		</div>

		<table class="table table-condensed table-striped DataEntry Sortable">
		<thead>
		<tr>
			<th width="24px"></th>
			<th width="60px">Sr No</th>
			<th width="100px">Account</th>
			<th width="100px">SAC / HSN</th>
			<th>Particulars</th>
			<th width="80px">Is INR</th>
			<th width="80px">Unit</th>
			<th width="80px">Rate</th>
			<th width="100px">Currency</th>
			<th width="80px">INR Amount</th>
			<th width="60px">CGST</th>
			<th width="60px">SGST</th>
			<th width="60px">IGST</th>
			<th width="100px">Total Amount</th>
			<th width="24px" class="aligncenter"><a href="#" class="CheckAll"><i class="icon-trashcan"></i></a></th>
		</tr>
		</thead>
		
		<tbody>
		<?php 
			$sr_no = 0;
			foreach ($voucher_details as $vjd) {
				if ($vjd['id'] > 0) {
					$non_stax = '';
					if (strlen($vjd['stax_code']) == 0)
						$non_stax = 'NonSTax';

					$cgst        = (($vjd['cgst'] > 0 AND $vjd['sgst'] > 0) ? $vjd['cgst'] : 0);
					$cgst_amount = round($vjd['amount'] * $cgst / 100, 2);
					$sgst        = (($vjd['cgst'] > 0 AND $vjd['sgst'] > 0) ? $vjd['sgst'] : 0);
					$sgst_amount = round($vjd['amount'] * $sgst / 100, 2);
					$igst        = (($vjd['cgst'] == 0 AND $vjd['sgst'] == 0) ? $vjd['igst'] : 0);
					$igst_amount = round($vjd['amount'] * $igst / 100, 2);
					$total       = round($vjd['amount'] + $cgst_amount + $sgst_amount + $igst_amount, 2);
					
					echo '<tr>
			<td class="aligncenter grayLight ' . ($vjd['category'] == 'Bill Items' ? 'SortHandle' : 'ui-state-disabled') . '"><i class="icon-bars"></i></td>';
			if ($vjd['category'] == 'Bill Items') 
				echo '<td class="aligncenter ' . $non_stax . '"><input type="text" class="form-control form-control-sm Numeric Validate Focus" name="vjd_sr_no[' . $vjd['id'] . ']" value="' . $vjd['sr_no'] . '" /></td>';
			else
				echo '<td class="aligncenter ' . $non_stax . '"><input type="hidden" class="form-control form-control-sm Numeric Validate Focus" name="vjd_sr_no[' . $vjd['id'] . ']" value="0" /></td>';
			echo '<td class="' . $non_stax . '">' . $vjd['bill_item_code'] . '</td>
			<td class="' . $non_stax . '"><input type="text" class="form-control form-control-sm" name="vjd_sac_hsn[' . $vjd['id'] . ']" value="' . $vjd['sac_hsn'] . '" /></td>
			<td class="' . $non_stax . '"><input type="text" class="form-control form-control-sm" name="vjd_particulars[' . $vjd['id'] . ']" value="' . $vjd['particulars'] . '" /></td>
			<td class="' . $non_stax . '">' . form_dropdown('is_inr', array('Yes'=>'Yes', 'No'=>'No'),($vjd['currency_amount'] > 0 ? 'No' : 'Yes'), 'class="form-control form-control-sm IsINR"') . '</td>
			<td class="' . $non_stax . '"><input type="text" class="form-control form-control-sm Numeric Units" name="vjd_units[' . $vjd['id'] . ']" value="' . $vjd['units'] . '" /></td>
			<td class="' . $non_stax . '"><input type="text" class="form-control form-control-sm Numeric Rate" name="vjd_rate[' . $vjd['id'] . ']" value="' . $vjd['rate'] . '" /></td>
			<td class="' . $non_stax . '"><input type="text" class="form-control form-control-sm Numeric Currency" name="vjd_currency_amount[' . $vjd['id'] . ']" value="' . $vjd['currency_amount'] . '" /></td>
			<td class="' . $non_stax . '"><input type="text" class="form-control form-control-sm Numeric Amount" name="vjd_amount[' . $vjd['id'] . ']" value="' . $vjd['amount'] . '" /></td>
			<td class="' . $non_stax . '"><input type="text" class="form-control form-control-sm CGST Numeric" name="vjd_cgst[' . $vjd['id'] . ']" value="' . $cgst . '" /></td>
			<td class="' . $non_stax . '"><input type="text" class="form-control form-control-sm SGST Numeric" name="vjd_sgst[' . $vjd['id'] . ']" value="' . $sgst . '" /></td>
			<td class="' . $non_stax . '"><input type="text" class="form-control form-control-sm IGST Numeric" name="vjd_igst[' . $vjd['id'] . ']" value="' . $igst . '" /></td>
			<td class="' . $non_stax . '"><input type="text" class="form-control form-control-sm Total Numeric" name="vjd_total[' . $vjd['id'] . ']" value="' . $total . '" /></td>
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
			<td><input type="text" class="form-control form-control-sm SAC_HSN Validate" name="new_sac_hsn[]" value="" /></td>
			<td><input type="text" class="form-control form-control-sm Validate Particular" name="new_particulars[]" value="" /></td>
			<td><?php echo form_dropdown('is_inr', array('Yes'=>'Yes', 'No'=>'No'), 'Yes', 'class="form-control form-control-sm Unchanged IsINR"') ?></td>
			<td><input type="text" class="form-control form-control-sm Numeric Units" name="new_units[]" value="" /></td>
			<td><input type="text" class="form-control form-control-sm Numeric Rate" name="new_rate[]" value="" /></td>
			<td><input type="text" class="form-control form-control-sm Numeric Currency" name="new_currency_amount[]" value="" /></td>
			<td><input type="text" class="form-control form-control-sm Numeric Amount Validate" name="new_amount[]" value="" /></td>
			<td><input type="text" class="form-control form-control-sm CGST Numeric" name="new_cgst[]" value="" /></td>
			<td><input type="text" class="form-control form-control-sm SGST Numeric" name="new_sgst[]" value="" /></td>
			<td><input type="text" class="form-control form-control-sm IGST Numeric" name="new_igst[]" value="" /></td>
			<td><input type="text" class="form-control form-control-sm Total Numeric" name="new_total[]" value="" /></td>
			<td class="aligncenter"><button type="submit" class="btn btn-success btn-sm AddButton"><i class="icon-white icon-plus"></i></button></td>
		</tr>
		</tbody>
		</table>
	</div>

	<div class="card-footer">
		<div class="row">
			<div class="col-md-8">
				<button type="button" class="btn btn-success UpdateButton" id="Update">Update</button>
				<a href="#modal-delete" data-toggle="modal" class="btn btn-danger pull-right">Delete</a>
				
				<?php if ($row['id'] > 0 AND strtotime($row['date']) < strtotime('2017-07-01')) {
					echo '<div class="btn-group">' .
						anchor($this->_clspath.$this->_class."/preview/".$row['voucher_book_id'].'/'.$row['id'].'/0/1', "Preview", 'class="btn btn-default Popup"') .
						anchor($this->_clspath.$this->_class."/preview/".$row['voucher_book_id'].'/'.$row['id'].'/1/1', "PDF", 'class="btn btn-default Popup"') .
						anchor($this->_clspath.$this->_class."/preview/".$row['voucher_book_id'].'/'.$row['id'].'/1/0', "PDF Plain", 'class="btn btn-default Popup"') .
						'</div>'; 
					}
					else if ($row['id'] > 0 AND strtotime($row['date']) >= strtotime('2017-07-01')) {
						echo anchor($this->_clspath.$this->_class."/pdf/".$row['voucher_book_id'].'/'.$row['id'].'/1', '<i class="icon-file-pdf"></i> PDF', 'class="btn btn-primary Popup"') .
						anchor($this->_clspath.$this->_class."/pdf/".$row['voucher_book_id'].'/'.$row['id'].'/0', '<i class="icon-file-pdf"></i> PDF Plain', 'class="btn btn-primary Popup"');
					}
				?>
			</div>
			<div class="col-md-4 alignright big">
				<?php echo inr_format($amount) ?>
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

	$('.DataEntry').on('change', '.Units', function() {
		var ex_rate  = $('#ExchangeRate').val();
		var unit     = $(this).val();
		var is_inr   = $(this).parents('tr').children('td').find('.IsINR').val();
		var rate     = $(this).parents('tr').children('td').find('.Rate').val();
		var currency = $(this).parents('tr').children('td').find('.Currency');
		if (is_inr   == 'No') {
			var c_amount = roundOff((unit * rate), 0);
			var amount   = roundOff((unit * rate * ex_rate), 0);
			$(currency).val(c_amount);
		}
		else {
			$(currency).val('0');
			var amount = roundOff((unit * rate), 0);
		}
		$(this).parents('tr').children('td').find('.Amount').val(amount);
	});

	$('.DataEntry').on('change', '.Rate', function() {
		var ex_rate = $('#ExchangeRate').val();
		var rate    = $(this).val();
		var is_inr  = $(this).parents('tr').children('td').find('.IsINR').val();
		var unit    = $(this).parents('tr').children('td').find('.Units').val();
		var currency = $(this).parents('tr').children('td').find('.Currency');
		if (is_inr  == 'No') {
			var c_amount = roundOff((unit * rate), 0);
			var amount   = roundOff((unit * rate * ex_rate), 0);
			$(currency).val(c_amount);
		}
		else {
			$(currency).val('0');
			var amount = roundOff((unit * rate), 0);
		}
		$(this).parents('tr').children('td').find('.Amount').val(amount);
	});

<?php if($voucher_book['job_type'] != 'N/A' && count($voucher_details) == 0) : ?>
	$('#ajaxBLNo').typeahead({
		hint: false,
		highlight: true,
		minLength: 1
	}, {
		name: 'tt_bl_no',
		displayKey: 'bl_sb',
		source: function(query, process) {
			return $.ajax({ 
				url: '<?php echo site_url($this->_clspath.$this->_class."/ajaxInvoiceBL/".$voucher_book['id'].'/'.$voucher_book['job_type']) ?>',
				type: 'get',
				data: { term: query },
				dataType: 'json',
				success: function (result) {
					return process(result);
				}
			});
		},
		templates: {
			empty: ['<div class="tt-no-result">Unable to find any results that match the current query</div>'],
			suggestion: Handlebars.compile('<p><span class="tiny"><strong>{{bl_sb}}</strong> <span class="blueDark">{{type}}:{{be_type}}</span>  {{vessel_voyage}} <span class="orange">{{party}}</span></span></p>')
		}
	}).on('typeahead:selected', function(obj, datum) {
		$('#JobID').val(datum.id);
		$('#ajaxBLNo').val(datum.bl_sb);
		$('#Pieces').val(datum.pieces);
		$('#CBM').val(datum.cbm);
		if (datum.voucher_id > 0)
			$("#modal-voucher-exists").modal('show');
	});
<?php endif; ?>
	
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
	});

	$("#CreditAccount").typeahead({
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
		$("#CreditAccountID").val(datum.id);
		$("#CreditAccount").val(datum.code + ' - ' + datum.name);
		$("#CreditAccountClosing").text(datum.closing);

		if (datum.id == 0)
			$("#CreditAccount").parent().addClass('has-error');
		else
			$("#CreditAccount").parent().removeClass('has-error');
	});

	$('.BillItemCode').typeahead({
		hint: false,
		highlight: true,
		minLength: 1
	}, {
		name: 'tt_bill_item',
		displayKey: 'name',
		source: function(query, process) {
			return $.ajax({ 
				url: '<?php echo site_url("accounting/ledger/ajaxLedgers/Bill Items") ?>',
				type: 'POST',
				data: { term: query },
				dataType: 'json',
				success: function (result) {
					return process(result);
				}
			});
		},
		templates: {
			empty: ['<div class="tt-no-result">Unable to find any results that match the current query</div>'],
			suggestion: Handlebars.compile('<p><span class="tiny"><strong class="blueDark">{{code}}</strong> <span class="bold green">{{sac_hsn}}</span> - {{name}}</span></p>')
		}
	}).on('typeahead:selected', function(obj, datum) {
		$row = $(this).parent('span').parent('td').parent('tr');
		$(this).parent('span').parent('td').find('.BillItemID').val(datum.id);
		$row.find('.SAC_HSN').val(datum.sac_hsn);
		$row.find('.Particular').val(datum.name);
		$row.find('.Currency').val(1);
		$row.find('.Units').val(datum.units);
		$row.find('.CGST').val(datum.cgst);
		$row.find('.SGST').val(datum.sgst);
		$row.find('.IGST').val(datum.igst);
	}).on('typeahead:opened', function(){
		$('.tt-dropdown-menu').css('width', '500px');
	});
});
</script>