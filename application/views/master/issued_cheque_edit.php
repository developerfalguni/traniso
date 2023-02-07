<?php
echo form_open($this->uri->uri_string(), 'class="form-horizontal" id="MainForm"');
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
			<div class="form-group">
				<label class="control-label col-md-2">Company</label>
				<div class="col-md-10">
					<?php echo form_dropdown('company_id', getSelectOptions('companies', 'id', 'code'), $row['company_id'], 'class="form-control form-control-sm" id="CompanyID"'); ?>
				</div>
			</div>

			<div class="form-group">
				<label class="control-label col-md-2">Bank</label>
				<div class="col-md-10">
					<input type="hidden" name="bank_ledger_id" value="<?php echo $row['bank_ledger_id'] ?>" id="BankID" />
					<input type="text" class="form-control form-control-sm" name="bank_name" value="<?php echo $bank_name ?>" id="ajaxBank" />
				</div>
			</div>	

			<div class="form-group <?php echo (strlen(form_error('cheque_date')) > 0 ? 'has-error' : '') ?>">
				<label class="control-label col-md-2">Cheque Date</label>
				<div class="col-md-10">
					<div class="input-group date DatePicker">
						<span class="input-group-addon"><i class="icon-calendar"></i></span>
						<input type="text" class="form-control form-control-sm AutoDate" name="cheque_date" value="<?php echo ($row['cheque_date'] != '00-00-0000' ? $row['cheque_date'] : '') ?>" id="ChequeDate"/>
					</div>
				</div>
			</div>
				
			<div class="form-group<?php echo (strlen(form_error('cheque_no')) > 0 ? ' has-error' : '') ?>">
				<label class="control-label col-md-2">Cheque No</label>
				<div class="col-md-10">
					<input type="text" class="form-control form-control-sm Text input-small" name="cheque_no" value="<?php echo sprintf('%06d', $row['cheque_no']) ?>" id="ChequeNo" />
				</div>
			</div>

			<div class="form-group<?php echo (strlen(form_error('favor')) > 0 ? ' has-error' : '') ?>">
				<label class="control-label col-md-2">Favor</label>
				<div class="col-md-10">
					<input type="hidden" name="favor_ledger_id" value="<?php echo $row['favor_ledger_id'] ?>" id="FavorID" />
					<input type="text" class="form-control form-control-sm" name="favor" value="<?php echo $row['favor'] ?>" id="ajaxFavor" />
				</div>
			</div>

			<div class="form-group<?php echo (strlen(form_error('amount')) > 0 ? ' has-error' : '') ?>">
				<label class="control-label col-md-2">Amount</label>
				<div class="col-md-10">
					<input type="text" class="form-control form-control-sm Numeric" name="amount" value="<?php echo $row['amount'] ?>" />
				</div>
			</div>

			<div class="form-group <?php echo (strlen(form_error('realization_date')) > 0 ? 'has-error' : '') ?>">
				<label class="control-label col-md-2">Realization Date</label>
				<div class="col-md-10">
					<div class="input-group date DatePicker">
						<span class="input-group-addon"><i class="icon-calendar"></i></span>
						<input type="text" class="form-control form-control-sm AutoDate" name="realization_date" value="<?php echo ($row['realization_date'] != '00-00-0000' ? $row['realization_date'] : '') ?>" id="RealizationDate"/>
					</div>
				</div>
			</div>

			<div class="form-group">
				<label class="control-label col-md-2">Cancelled</label>
				<div class="col-md-10">
					<?php echo form_dropdown('cancelled', getEnumSetOptions('issued_cheques', 'cancelled'), $row['cancelled'], 'class="form-control form-control-sm"'); ?>
				</div>
			</div>

			<div class="form-group">
				<label class="control-label col-md-2">Remarks</label>
				<div class="col-md-10">
					<input type="text" class="form-control form-control-sm" name="remarks" value="<?php echo $row['remarks'] ?>" />
				</div>
			</div>
		</fieldset>
	</div>

	<div class="card-footer">
		<button type="submit" class="btn btn-success" id="Update">Update</button>
	</div>
</div>
</form>

<script>
$(document).ready(function() {
	$("#CompanyID").on("change", function() {
		$("#ajaxBank").autocomplete('option','source', '<?php echo site_url($this->_clspath.$this->_class.'/ajaxBank') ?>/'+$(this).val());
	});

	$("#ajaxBank").autocomplete({
		source: '<?php echo site_url($this->_clspath.$this->_class.'/ajaxBank') ?>/'+$("#CompanyID").val(),
		minLength: 1,
		autoFocus: true,
		focus: function(event, ui) {
			$("#BankID").val(ui.item.id);
			return false;
		},
		select: function(event, ui) {
			$("#BankID").val(ui.item.id);
			$("#ajaxBank").val(ui.item.name);
			return false;
		},
		response: function(event, ui) {
            if (ui.content.length === 0) {
				$("#BankID").val(0);
                $("#ajaxBank").val('');
            }
        }
	})
	.data('ui-autocomplete')._renderItem = function(ul, item) {
		return $('<li></li>')
			.data('item.autocomplete', item)
			.append('<a>' + item.name + '</a>')
			.appendTo(ul);
	};

	$("#ajaxFavor").autocomplete({
		source: '<?php echo site_url($this->_clspath.$this->_class.'/ajaxFavor') ?>/'+$("#CompanyID").val(),
		minLength: 3,
		autoFocus: true,
		focus: function(event, ui) {
			$("#FavorID").val(ui.item.id);
			return false;
		},
		select: function(event, ui) {
			$("#FavorID").val(ui.item.id);
			$("#ajaxFavor").val(ui.item.name);
			return false;
		},
		response: function(event, ui) {
            if (ui.content.length === 0) {
				$("#FavorID").val(0);
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
</script>