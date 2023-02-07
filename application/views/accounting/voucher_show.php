<style>
.table.table-striped tbody tr td.Current {
   background-color: #ff9;
}
</style>

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

				<div class="col-md-3">
					<div class="form-group">
						<label class="control-label">Voucher No</label>
						<h5><?php echo $row['id2_format'] ?></h5>
					</div>
				</div>
			</div>
		</fieldset>

		<fieldset>
		<div class="row">
			<div class="col-md-3">
				<div class="form-group">
					<label class="control-label">Sr. No.</label>
					<h5><?php echo $row['id3'] ?></h5>
				</div>
			</div>

			<div class="col-md-3">
				<div class="form-group">
					<label class="control-label">Date</label>
					<h5><?php echo $row['date'] ?></h5>
				</div>
			</div>

			<div class="col-md-3">
				<div class="form-group">
					<label class="control-label">Cheque No.</label>
					<h5><?php echo $row['cheque_no'] ?></h5>
				</div>
			</div>

			<div class="col-md-3">
				<div class="form-group">
					<label class="control-label">Cheque Date</label>
					<h5><?php echo $row['cheque_date'] ?></h5>
				</div>
			</div>
		</div>

		<div class="form-group">
			<label class="control-label">Debit Account</label>
			<h5><?php echo $row['debit_account'] ?></h5>
		</div>

		<div class="form-group">
			<label class="control-label">Credit Account</label>
			<h5><?php echo $row['credit_account'] ?></h5>
		</div>

		<div class="row">
			<div class="col-md-5">
				<div class="form-group">
					<label class="control-label">Amount</label>
					<h5><?php echo $row['amount'] ?></h5>
				</div>
			</div>

			<div class="col-md-4">
				<div class="form-group">
					<label class="control-label">Bill No</label>
					<h5><?php echo $row['invoice_no'] ?></h5>
				</div>
			</div>

			<div class="col-md-3">
				<div class="form-group">
					<label class="control-label">Reconciliation Date</label>
					<h5><?php echo $row['reconciliation_date'] ?></h5>
				</div>
			</div>
		</div>

		<div class="row <?php echo ($row['dr_tds_class_id'] > 0 || $row['dr_stax_category_id'] > 0 ? 'show' : 'hide') ?>" id="TaxPaymentDetails">
			<div class="col-md-5">
				<div class="form-group<?php echo (strlen(form_error('tds_stax_bsr_code')) > 0 ? ' has-error' : '') ?>">
					<label class="control-label">BSR Code</label>
					
						<input type="text" class="form-control form-control-sm" name="tds_stax_bsr_code" value="<?php echo $row['tds_stax_bsr_code'] ?>" />
					
				</div>
			</div>

			<div class="col-md-4">
				<div class="form-group<?php echo (strlen(form_error('tds_stax_challan_no')) > 0 ? ' has-error' : '') ?>">
					<label class="control-label">Challan No</label>
					
						<input type="text" class="form-control form-control-sm" name="tds_stax_challan_no" value="<?php echo $row['tds_stax_challan_no'] ?>" />
					
				</div>
			</div>

			<div class="col-md-3">
				<div class="form-group">
					<?php 
					if ($row['id'] > 0) {
						if ($row['dr_tds_class_id'] > 0) {
							echo '<label class="control-label">Vouchers</label>';
							echo ($row['dr_tds_class_id'] == 0 ? 
								'<button type="button" class="btn btn-sm btn-info disabled">Select</button>' : 
								anchor($this->_clspath.$this->_class.'/tds_vouchers/'.$row['id'], 'Select', 'class="btn btn-sm btn-info Popup"'));
						}
						else if ($row['dr_stax_category_id'] > 0) {
							echo '<label class="control-label">Months</label>';
							echo form_dropdown('stax_payment_month[]', $months, explode(", ", $row['stax_payment_month']), 'multiple class="SelectizeKaabar-deselect"');
						}
					}
					?>
				</div>
			</div>
		</div>

		<div class="form-group">
			<label class="control-label">Narration</label>
			<?php echo $row['narration'] ?>
		</div>

		<table class="table table-condensed table-striped">
		<thead>
			<tr>
				<th>No</th>
				<th>BL / SB</th>
				<th>Name</th>
				<th>Amount</th>
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
					<td>' . $vjd['amount'] . '</td>
				</tr>';
			} ?>
		</tbody>
		</table>

		</fieldset>
	</div>
</div>