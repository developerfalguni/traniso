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
				<div class="col-md-7">
					<div class="row">
						<div class="col-md-4">
							<div class="form-group<?php echo (strlen(form_error('invoice_value')) > 0 ? ' has-error' : '') ?>">
								<label class="control-label">Invoice Value</label>
								<div class="input-group">
									<input type="text" class="form-control form-control-sm Numeric" name="invoice_value" value="<?php echo $row['invoice_value']; ?>" />
									<div class="input-group-btn">
										<?php echo form_dropdown('iv_currency_id', getSelectOptions('currencies', 'id', 'code'), $row['iv_currency_id'], 'class="btn"'); ?>
									</div>
								</div>
							</div>
						</div>

						<div class="col-md-4">
							<div class="form-group<?php echo (strlen(form_error('appraisement_date')) > 0 ? ' has-error' : '') ?>">
								<label class="control-label">Appraisement</label>
								<div class="input-group date DatePicker">
									<span class="input-group-addon"><i class="icon-calendar"></i></span>
									<input type="text" class="form-control form-control-sm AutoDate" name="appraisement_date" value="<?php echo $row['appraisement_date']; ?>" />
								</div>
							</div>
						</div>

						<div class="col-md-4">
							<div class="form-group<?php echo (strlen(form_error('assessment_date')) > 0 ? ' has-error' : '') ?>">
								<label class="control-label">Assessment</label>
								<div class="input-group date DatePicker">
									<span class="input-group-addon"><i class="icon-calendar"></i></span>
									<input type="text" class="form-control form-control-sm AutoDate" name="assessment_date" value="<?php echo $row['assessment_date']; ?>" />
								</div>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-4">
							<div class="form-group<?php echo (strlen(form_error('exam_date')) > 0 ? ' has-error' : '') ?>">
								<label class="control-label">Examination</label>
								<div class="input-group date DatePicker">
									<span class="input-group-addon"><i class="icon-calendar"></i></span>
									<input type="text" class="form-control form-control-sm AutoDate" name="exam_date" value="<?php echo $row['exam_date']; ?>" />
								</div>
							</div>
						</div>

						<div class="col-md-4">
							<div class="form-group<?php echo (strlen(form_error('payment_date')) > 0 ? ' has-error' : '') ?>">
								<label class="control-label">Payment</label>
								<div class="input-group date DatePicker">
									<span class="input-group-addon"><i class="icon-calendar"></i></span>
									<input type="text" class="form-control form-control-sm AutoDate" name="payment_date" value="<?php echo $row['payment_date']; ?>" />
								</div>
							</div>
						</div>

						<div class="col-md-4">
							<div class="form-group<?php echo (strlen(form_error('ooc_date')) > 0 ? ' has-error' : '') ?>">
								<label class="control-label">OOC</label>
								<div class="input-group date DatePicker">
									<span class="input-group-addon"><i class="icon-calendar"></i></span>
									<input type="text" class="form-control form-control-sm AutoDate" name="ooc_date" value="<?php echo $row['ooc_date']; ?>" />
								</div>
							</div>
						</div>						
					</div>

					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								<label class="control-label">Custom Duty</label>
								<div class="form-group<?php echo (strlen(form_error('custom_duty')) > 0 ? ' has-error' : '') ?>">
									<input type="text" class="form-control form-control-sm Numeric" name="custom_duty" value="<?php echo $row['custom_duty']; ?>" size="12" />
								</div>
							</div>
						</div>

						<div class="col-md-4">
							<div class="form-group<?php echo (strlen(form_error('cd_date')) > 0 ? ' has-error' : '') ?>">
								<label class="control-label">Duty Date</label>
								<div class="input-group date DatePicker">
									<span class="input-group-addon"><i class="icon-calendar"></i></span>
									<input type="text" class="form-control form-control-sm AutoDate" name="cd_date" value="<?php echo $row['cd_date']; ?>" />
								</div>
							</div>
						</div>	

						<div class="col-md-4">
							<div class="form-group">
								<label class="control-label">Duty Paid Direct</label>
								<div class="form-group<?php echo (strlen(form_error('cd_paid_direct')) > 0 ? ' has-error' : '') ?>">
									<input type="checkbox" name="cd_paid_direct" value="Yes" <?php echo ($row['cd_paid_direct'] == 'Yes' ? 'checked="checked"' : '') ?> /> Yes
								</div>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								<label class="control-label">Stamp Duty</label>
								<div class="form-group<?php echo (strlen(form_error('stamp_duty')) > 0 ? ' has-error' : '') ?>">
									<input type="text" class="form-control form-control-sm Numeric" name="stamp_duty" value="<?php echo $row['stamp_duty']; ?>" size="12" />
								</div>
							</div>
						</div>

		<?php if ($jobs['cargo_type'] == 'Bulk') : ?>
					</div>

					<div class="row">
						<div class="col-md-3">
							<div class="form-group">
								<label class="control-label">Wharfage Rate</label>
								<div class="form-group<?php echo (strlen(form_error('wh_rate')) > 0 ? ' has-error' : '') ?>">
									<input type="text" class="form-control form-control-sm Numeric" name="wh_rate" value="<?php echo $row['wh_rate']; ?>" size="12" />
								</div>
							</div>
						</div>

						<div class="col-md-3">
							<div class="form-group">
								<label class="control-label">Wharfage STax</label>
								<div class="form-group<?php echo (strlen(form_error('wh_stax')) > 0 ? ' has-error' : '') ?>">
									<input type="text" class="form-control form-control-sm Numeric" name="wh_stax" value="<?php echo $row['wh_stax']; ?>" size="12" />
								</div>
							</div>
						</div>

						<div class="col-md-3">
							<div class="form-group">
								<label class="control-label">Wharfage TDS</label>
								<div class="form-group<?php echo (strlen(form_error('wh_tds')) > 0 ? ' has-error' : '') ?>">
									<input type="text" class="form-control form-control-sm Numeric" name="wh_tds" value="<?php echo $row['wh_tds']; ?>" size="12" />
								</div>
							</div>
						</div>

						<div class="col-md-3">
							<div class="form-group">
								<label class="control-label">Wharfage</label>
								<div class="form-group<?php echo (strlen(form_error('wharfage')) > 0 ? ' has-error' : '') ?>">
									<input type="text" class="form-control form-control-sm Numeric" name="wharfage" value="<?php echo $row['wharfage']; ?>" size="12" />
								</div>
							</div>
						</div>
					</div>
		<?php else : ?>
						<div class="col-md-4">
							<div class="form-group<?php echo (strlen(form_error('line_payment')) > 0 ? ' has-error' : '') ?>">
								<label class="control-label">Line Payment</label>
								<div class="input-group">
									<input type="text" class="form-control form-control-sm Numeric" name="line_payment" value="<?php echo $row['line_payment']; ?>" />
									<div class="input-group-btn">
										<?php echo anchor($this->_clspath.$this->_class."/calculate/Line/".$job_id['id'], '<i class="icon-refresh"></i>', 'class="btn btn-info"') ?>
									</div>
								</div>
							</div>
						</div>

						<div class="col-md-4">
							<div class="form-group<?php echo (strlen(form_error('cfs_payment')) > 0 ? ' has-error' : '') ?>">
								<label class="control-label">CFS Payment</label>
								<div class="input-group">
									<input type="text" class="form-control form-control-sm Numeric" name="cfs_payment" value="<?php echo $row['cfs_payment']; ?>" />
									<div class="input-group-btn">
										<?php echo anchor($this->_clspath.$this->_class."/calculate/CFS/".$job_id['id'], '<i class="icon-refresh"></i>', 'class="btn btn-info"') ?>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-3">
							<div class="form-group<?php echo (strlen(form_error('eta_date')) > 0 ? ' has-error' : '') ?>">
								<label class="control-label">ETA Date</label>
								<div class="input-group date DatePicker">
									<span class="input-group-addon"><i class="icon-calendar"></i></span>
									<input type="text" class="form-control form-control-sm AutoDate" name="eta_date" value="<?php echo $eta_date; ?>" />
								</div>
							</div>
						</div>	
						
						<div class="col-md-1">
							<div class="form-group<?php echo (strlen(form_error('free_days')) > 0 ? ' has-error' : '') ?>">
								<label class="control-label">Free Days</label>
								<input type="text" class="form-control form-control-sm Numeric col-md-4" name="free_days" value="<?php echo $row['free_days']; ?>" />
							</div>
						</div>

						<div class="col-md-4">
							<!-- <div class="form-group">
								<label class="control-label">Original B/L Received</label>
								<div class="form-group<?php echo (strlen(form_error('original_bl_received')) > 0 ? ' has-error' : '') ?>">
									<input type="text" class="DateTime" name="original_bl_received" value="<?php echo $row['original_bl_received']; ?>" />
								</div>
							</div> -->

							<div class="form-group">
								<label class="control-label">CFS Name</label>
								<div class="form-group<?php echo (strlen(form_error('cfs_id')) > 0 ? ' has-error' : '') ?>">
									<input type="hidden" name="cfs_id" value="<?php echo $row['cfs_id'] ?>" id="CFSID" />
									<input type="text" class="form-control form-control-sm" name="cfs_name" value="<?php echo $cfs_name ?>" id="ajaxCFS" />
								</div>
							</div>
						</div> 

						<div class="col-md-4">
							<div class="form-group">
								<label class="control-label">Place of Delivery</label>
								<div class="form-group<?php echo (strlen(form_error('place_of_delivery')) > 0 ? ' has-error' : '') ?>">
									<input type="text" class="form-control form-control-sm" name="place_of_delivery" value="<?php echo $row['place_of_delivery']; ?>" id="ajaxPOD" />
								</div>
							</div>
						</div>
					</div>
		<?php endif; ?>

					<div class="form-group">
						<label class="control-label">Remarks</label>
						<div class="form-group<?php echo (strlen(form_error('remarks')) > 0 ? ' has-error' : '') ?>">
							<textarea type="text" class="form-control form-control-sm" name="remarks" rows="2" cols="40"><?php echo $row['remarks']; ?></textarea>
						</div>
					</div>					
				</div>

				<div class="col-md-5">
					<div class="card card-default">
						<div class="card-header">
							<h3 class="card-title">Vouchers Found</h3>
						</div>
					
						<table class="table table-condensed table-striped">
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
								$total = 0;
								foreach ($all_vouchers as $v) {
									$total = bcadd($total, $v['amount'], 0);
									echo '<tr>
							<td class="alignmiddle">' . anchor('/accounting/' . underscore($v['url']), $v['id2_format'], 'target="_blank"') . '</td>
							<td class="alignmiddle">' . $v['invoice_no'] . '</td>
							<td class="alignmiddle">' . $v['name'] . '</td>
							<td class="alignright">' . inr_format($v['amount'], 2) . '</td>
						</tr>';
								}
							?>
						</tbody>

						<tfoot>
						<tr>
							<th colspan="3" class="alignright">Total</th>
							<th><?php echo inr_format($total) ?></th>
						</tr>
						</tfoot>
						</table>
					</div>


					<div class="card card-default">
						<div class="card-header">
							<h3 class="card-title">Transportation Expenses</h3>
						</div>
					
						<table class="table table-condensed table-striped">
						<tbody class="tiny">
							<?php 
								$total = 0;
								foreach ($transportation as $f => $v) {
									echo '<tr>
							<td class="alignmiddle">' . humanize($f) . '</td>
							<td class="alignright">' . inr_format($v, 2) . '</td>
						</tr>';
								}
							?>
						</tbody>
						</table>
					</div>
					

					<div class="card card-default">
						<div class="card-header">
							<h3 class="card-title">Vouchers By Group</h3>
						</div>
					
						<table class="table table-condensed table-striped">
						<thead>
						<tr>
							<th width="70px">Voucher</th>
							<th>Particular</th>
							<th width="80px">Amount</th>
						</tr>
						</thead>

						<tbody class="tiny">
							<?php 
								foreach ($vouchers['vouchers'] as $v) {
									echo '<tr>
							<td class="alignmiddle">' . anchor('/accounting/' . underscore($v['url']), $v['id2_format'], 'target="_blank"') . '</td>
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

								echo '<tr><td colspan="4">&nbsp;</td></tr>';
							?>
						</tbody>
						</table>
					</div>

					<div class="card card-default">
						<div class="card-header">
							<h3 class="card-title">Pending Documents List</h3>
						</div>
						
						<table class="table table-condensed table-striped">
						<thead>
						<tr>
							<th width="140px">Date</th>
							<th>Document</th>
							<th width="24px" class="aligncenter"><i class="icon-paperclip"></i></th>
							<th width="24px" class="aligncenter"><a href="javascript: ReceivedAll()"><i class="icon-check"></i></a></th>
							<th width="24px" class="aligncenter"><a href="javascript: HideAll()"><i class="icon-eye"></i></a></th>
						</tr>
						</thead>

						<tbody>
							<?php foreach ($pending_documents as $pd) {
									echo '<tr>
							<td><div class="input-group date DatePicker"><span class="input-group-addon"><i class="icon-calendar"></i></span><input type="text" class="form-control form-control-sm AutoDate" name="received_date['.$pd['id'].']"  value="'.$pd['received_date'].'" /></td>
							<td><input type="hidden" name="did['.$pd['id'].']" value="' . $pd['id'] . '" />
								<input type="hidden" name="dtid['.$pd['id'].']" value="' . $pd['document_type_id'] . '" />' . 
								anchor('/import/attached_document/'. (strlen($pd['file']) > 0 ? 'edit/'.$job_id['id'].'/'.$pd['id'] : 'attach/' . $job_id['id']), $pd['name']) . ' <span class="tiny orange">' . $pd['remarks'] . '</span></td>
							<td class="aligncenter">' . (strlen($pd['file']) > 0 ? '<i class="icon-paperclip"></i>' : '') . '</td>
							<td class="aligncenter ignoreClicks">' . form_checkbox(array('name' => 'received['.$pd['id'].']', 'value' => $pd['id'], 'checked' => ($pd['received'] == 'Yes' ? true : false), 'class' => 'ReceivedCheckbox', 'data-placement' => 'left', 'rel' => 'tooltip', 'data-original-title'=>'Mark Document as Received...')) . '</td>
							<td class="aligncenter ignoreClicks">' . form_checkbox(array('name' => 'is_pending['.$pd['id'].']', 'value' => $pd['id'], 'checked' => ($pd['is_pending'] == 'Yes' ? true : false), 'class' => 'HideCheckbox', 'data-placement' => 'left', 'rel' => 'tooltip', 'data-original-title'=>'Untick to remove document from Pending List...')) . '</td>
						</tr>';
								} ?>

						<tr id="1" class="hide">
							<td colspan="2"><input type="hidden" name="new_did[]" value="" />
								<input type="hidden" name="new_dtid[]" value="" />
								<input type="text" class="form-control form-control-sm" value="" /></div></td>
							<td></td>
							<td class="aligncenter ignoreClicks"><input type="checkbox" name="new_received[]" value="1" /></td>
							<td><span id="1"><a href="#" class="btn btn-danger btn-sm"><i class="icon-minus"></i></a></span></td>
						</tr>

						<tr id="Blank">
							<td colspan="2"><input type="hidden" name="blank_did" value="" id="DID" />
								<input type="hidden" name="blank_dtid" value="" id="DTID" />
								<input type="text" class="form-control form-control-sm" value="" id="DocumentName" /></td>
							<td></td>
							<td class="aligncenter ignoreClicks"><input type="checkbox" name="blank_received" value="1" /></td>
							<td><button type="submit" class="btn btn-sm btn-success" id="Add"><i class="fa fa-plus"></i></button></td>
						</tr>
						</tbody>
						</table>
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

<script>
var received = 1;
var compulsory = 1;

function ReceivedAll() {
	if(received) {
		$('input.ReceivedCheckbox').attr('checked', 'checked');
		received = 0;
	} else {
		$('input.ReceivedCheckbox').removeAttr('checked');
		received = 1;
	}
}

function HideAll() {
	if(compulsory) {
		$('input.HideCheckbox').attr('checked', 'checked');
		compulsory = 0;
	} else {
		$('input.HideCheckbox').removeAttr('checked');
		compulsory = 1;
	}
}

function make_copy(id) {
	var v0 = $('tr#Blank input:eq(0)').val();
	var v1 = $('tr#Blank input:eq(1)').val();
	var v2 = $('tr#Blank input:eq(2)').val();
	var v3 = $('tr#Blank input:eq(3)').is(':checked');

	if (!v0) return;
	
	if (id > 1) {
		$('tr#1').clone().insertBefore('tr#Blank').attr('id', id);
	}

	$('tr#Blank td a').attr('href', 'javascript:make_copy('+(id+1)+')');
	$('#Add').unbind('click');
	$('#Add').on('click', function() {
		make_copy(id+1);
		return false;
	});
	
	$('tr#'+id+' input:eq(0)').val(v0);
	$('tr#'+id+' input:eq(1)').val(v1);
	$('tr#'+id+' input:eq(2)').val(v2);
	if (v3)
		$('tr#'+id+' input:eq(3)').attr('checked', v3);
	
	$('tr#'+id+' td a').attr('href', 'javascript:remove_copy('+id+')');
	$('tr#'+id).addClass('hide');

	$('tr#Blank input:eq(0)').val('');
	$('tr#Blank input:eq(1)').val('');
	$('tr#Blank input:eq(2)').val('');
	$('tr#Blank input:eq(1)').focus();
}

function remove_copy(id) {
	if (id == 1) {
		$('tr#1 input').each(function(index) {
			$(this).val('');
		});
		$('tr#1').addClass('hide');
	}
	else {
		$('tr#'+id).remove();
	}
}

$(document).ready(function() {
	$('#Add').on('click', function() {
		make_copy(1);
		return false;
	});

	$('#ajaxPOD').autocomplete({
		source: "<?php echo site_url($this->_clspath.$this->_class.'/ajaxJson/import_details/place_of_delivery') ?>",
		minLength: 0,
	});

	$('#DocumentName').autocomplete({
		source: '<?php echo site_url($this->_clspath.$this->_class."/ajaxDocuments/".$job_id["id"]) ?>',
		minLength: 0,
		focus: function(event, ui) {
			$('#DocumentName').val(ui.item.name);
			return false;
		},
		select: function(event, ui) {
			$('#DID').val(ui.item.id);
			$('#DTID').val(ui.item.document_type_id);
			$('#DocumentName').text(ui.item.name);
			return false;
		}
	})
	.data('ui-autocomplete')._renderItem = function(ul, item) {
		return $('<li></li>')
			.data('item.autocomplete', item)
			.append('<a><span class="' + (item.is_pending == 'Yes' ? 'red' : 'orange') + '">' + item.name + '</span> <span class="tiny">' + item.remarks + '</span></a>')
			.appendTo(ul);
	};

<?php if ($jobs['cargo_type'] == 'Container') : ?>
	$('#ajaxCFS').autocomplete({
		source: '<?php echo site_url("/master/agent/ajax/CFS") ?>',
		minLength: 1,
		focus: function(event, ui) {
			$(this).val( ui.item.name);
			return false;
		},
		select: function(event, ui) {
			$(this).val(ui.item.name);
			$('#CFSID').val(ui.item.id);
	
			return false;
		}
	})
	.data('ui-autocomplete')._renderItem = function(ul, item) {
		return $('<li></li>')
			.data('item.autocomplete', item)
			.append('<a><span class="blue">' + item.code + '</span> ' + item.name + '</a>')
			.appendTo(ul);
	};
<?php endif; ?>

});
</script>