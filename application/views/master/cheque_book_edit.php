<div class="row">
	<div class="col-md-7">

<?php
echo start_panel('Cheque Book', anchor($this->_clspath.$this->_class, '<span class="icon"><i class="icon-list"></i></span>'), 'nopadding',
	'<div class="buttons">' . anchor($this->_clspath.$this->_class."/edit/0", '<i class="fa fa-plus"></i>  Add', 'class="btn btn-sm btn-success"') . '</div>');
echo form_open($this->uri->uri_string(), 'id="MainForm"');
echo form_hidden($id);
?>
<fieldset>

	<div class="form-group<?php echo (strlen(form_error('bank_name')) > 0 ? ' has-error' : '') ?>">
		<label class="control-label">Bank</label>
		<input type="hidden" name="bank_id" value="<?php echo $row['bank_id'] ?>" id="BankID" />
		<input type="text" class="form-control form-control-sm" name="bank_name" value="<?php echo $bank_name ?>" id="BankName" />
	</div>

	<div class="form-group<?php echo (strlen(form_error('starting_cheque_no')) > 0 ? ' has-error' : '') ?>">
		<label class="control-label">Starting Cheque No</label>
		<input type="text" class="form-control form-control-sm" name="starting_cheque_no" value="<?php echo sprintf('%06d', $row['starting_cheque_no']) ?>" />
	</div>

	<div class="form-group<?php echo (strlen(form_error('ending_cheque_no')) > 0 ? ' has-error' : '') ?>">
		<label class="control-label">Ending Cheque No</label>
		<input type="text" class="form-control form-control-sm" name="ending_cheque_no" value="<?php echo sprintf('%06d', $row['ending_cheque_no']) ?>" />
	</div>

	<div class="form-group<?php echo (strlen(form_error('account_no')) > 0 ? ' has-error' : '') ?>">
		<label class="control-label">Account No</label>
		<input type="text" class="form-control form-control-sm" name="account_no" value="<?php echo $row['account_no'] ?>" />
	</div>

</fieldset>

<div class="form-actions">
	<button type="submit" class="btn btn-success" id="Update">Update</button>
</div>
</form>
<?php echo end_panel(); ?>


<script>
$(document).ready(function() {
	
	$("#BankName").autocomplete({
		source: '<?php echo site_url($this->_clspath.$this->_class.'/ajaxBanks') ?>',
		minLength: 1,
		focus: function(event, ui) {
			$("#BankName").val(ui.item.name);
			$("#BankID").val(ui.item.id);
			return false;
		},
		select: function(event, ui) {
			$("#BankName").val(ui.item.name);
			$("#BankID").val(ui.item.id);
			return false;
		},
		response: function(event, ui) {
            if (ui.content.length === 0) {
                $("#BankName").val('');
				$("#BankID").val(0);
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