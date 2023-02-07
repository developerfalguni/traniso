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
				<div class="col-md-2">
					<div class="form-group">
						<label class="control-label">Voucher Type</label>
						<?php echo form_dropdown('voucher_type_id', getSelectOptions('voucher_types', 'id', 'name'), $row['voucher_type_id'], 'class="form-control form-control-sm Focus" id="VoucherType"') ?>
					</div>
				</div>

				<div class="col-md-2">
					<div class="form-group<?php echo (strlen(form_error('date_lock')) > 0 ? ' has-error' : '') ?>">
						<label class="control-label">Date Lock</label>
						<div class="input-group input-group-sm">
							<input type="text" class="form-control form-control-sm DatePicker" name="date_lock" value="<?php echo $row['date_lock']; ?>">
							<div class="input-group-append">
								<div class="input-group-text"><i class="icon-calendar"></i></div>
							</div>
						</div>
					</div>
				</div>

				<div class="col-md-6">
				</div>

				<div class="col-md-2">
					<div class="form-group">
						<label class="control-label"><input type="checkbox" name="auto_numbering" value="Yes" <?php echo set_checkbox('auto_numbering', $row['auto_numbering'], ($row['auto_numbering'] == 'Yes' ? TRUE : FALSE)) ?> /> Auto Numbering</label>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-md-2">
					<div class="form-group<?php echo (strlen(form_error('code')) > 0 ? ' has-error' : '') ?>">
						<label class="control-label">Code</label>
						<input type="text" class="form-control form-control-sm" name="code" value="<?php echo $row['code'] ?>" />
					</div>
				</div>

				<div class="col-md-5">
					<div class="form-group<?php echo (strlen(form_error('name')) > 0 ? ' has-error' : '') ?>">
						<label class="control-label">Name</label>
						<input type="text" class="form-control form-control-sm" name="name" value="<?php echo $row['name'] ?>" />
					</div>
				</div>

				<div class="col-md-5">
					<div class="form-group">
						<label class="control-label">Print Name</label>
						<input type="text" class="form-control form-control-sm" name="print_name" value="<?php echo $row['print_name'] ?>" />
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-md-2">
					<div class="form-group<?php echo (strlen(form_error('default_ledger_id')) > 0 ? ' has-error' : '') ?>">
						<label class="control-label">Dr / Cr</label>
						<?php echo form_dropdown('dr_cr', getEnumSetOptions($this->_table, 'dr_cr'), $row['dr_cr'], 'class="form-control form-control-sm"'); ?>
					</div>
				</div>

				<div class="col-md-10">
					<div class="form-group<?php echo (strlen(form_error('default_ledger_id')) > 0 ? ' has-error' : '') ?>">
						<label class="control-label">Default Ledger</label>
						<input type="text" class="form-control form-control-sm" value="<?php echo $row['default_ledger_name'] ?>" id="DefaultLedger">
						<input type="hidden" name="default_ledger_id" value="<?php echo $row['default_ledger_id'] ?>" id="DefaultLedgerID">
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-md-2">
					<div class="form-group<?php echo (strlen(form_error('job_type')) > 0 ? ' has-error' : '') ?>">
						<label class="control-label">Job Type</label>
						<?php echo form_dropdown('job_type', getEnumSetOptions($this->_table, 'job_type'), $row['job_type'], 'class="form-control form-control-sm"'); ?>
					</div>
				</div>

				<div class="col-md-10">
					<div class="form-group<?php echo (strlen(form_error('id2_format')) > 0 ? ' has-error' : '') ?>">
						<label class="control-label">Numbering Format</label>
						<input type="text" class="form-control form-control-sm" name="id2_format" value="<?php echo $row['id2_format'] ?>" /><br />
							<small>
								<div class="row">
									<div class="col-md-4"><code>[[comp]]</code> Company Code</div>
									<div class="col-md-4"><code>[[book]]</code> Voucher Book Code</div>
									<div class="col-md-4"><code>[[port]]</code> Port Code</div>
								</div>
								<div class="row">
									<div class="col-md-4"><code>[[job]]</code> Job Type</div>
									<div class="col-md-4"><code>[[num]]</code> Voucher Number</div>
									<div class="col-md-4"><code>[[year]]</code> Financial Year</div>
								</div>
							</small>
					</div>
				</div>
			</div>
		</fieldset>
	</div>

	<div class="card-footer">
		<button type="submit" class="btn btn-success" id="Update">Update</button>
	</div>
</div>

</form>

<script language="JavaScript">
$(document).ready(function(){


	$('#DefaultLedger').kaabar_autocomplete({source: '<?php echo site_url('accounting/ledger/ajax') ?>', alertText: 'Default Ledger'});

	// $("#DefaultLedger").kaabar_typeahead_complex({
	// 	name: 'tt_ledger',
	// 	displayKey: 'cr_acc',
	// 	url: '<?php echo site_url('accounting/ledger/ajax') ?>',
	// 	suggestion: '<p><span class="tiny"><strong class="blueDark">{{code}}</strong> {{name}} - <span class="blue">{{closing}}</span></small></p>',
	// 	fields: [{id: '#DefaultLedgerID', field: 'id'}]
	// });
});
</script>
