<div class="card card-default">
	<div class="card-header">
		<h3 class="card-title"><?php echo $page_title ?> <span class="label label-default" id="Moment"></span>&nbsp;&nbsp;&nbsp;
		<div class="btn-group">
			<a href="javascript: window.close();" class="btn btn-xs btn-danger"><i class="icon-power-off"></i> Close</a>
			<?php 
				// if ($icegate['ooc_date'] == '' || $icegate['ooc_date'] == 'N.A.')
				// 	echo anchor($this->_clspath.$this->_class."/track/$job_id", '<i class="icon-refresh"></i>', 'class="btn btn-primary" data-placement="bottom" rel="tooltip" data-original-title="Fetch Status" id="FetchStatus"');
				// else
				// 	echo '<button type="button" class="btn btn-primary" disabled="disabled"><i class="icon-refresh"></i></button>';
			echo anchor($this->_clspath.$this->_class."/captcha/$job_id", '<i class="icon-refresh"></i> Fetch Status', 'class="btn btn-xs btn-info Popup"') . 
				// anchor('javascript://', '<i class="icon-arrow-forward"></i> ePayment', 'class="btn btn-xs btn-info" onclick="ePayment()"') . 
				anchor('javascript://', '<i class="icon-resize"></i> ePayment Window', 'class="btn btn-xs btn-info" onclick="ePaymentSeparate()"') .
				//anchor($this->_clspath.$this->_class."/get_challan/$job_id", '<i class="icon-refresh"></i> Fetch Challan', 'class="btn btn-xs btn-success" target="_blank" id="FetchChallan"');
				anchor($this->_clspath.$this->_class."/captchaChallan/$job_id", '<i class="icon-refresh"></i> Fetch Challan', 'class="btn btn-xs btn-success Popup"');
			?>
		</div>
		</h3>
	</div>
	
	<!-- <div class="card-body"></div> -->

	<?php $this->load->view('/import/index'); ?>

	<table class="table table-condensed table-bordered" id="Table1">
	<thead>
	<tr>
		<th>IEC No</th>
		<th>Total Value</th>
		<th>Type</th>
		<th>CHA No</th>
		<th>First Check</th>
		<th>Prior BE</th>
		<th>Section 48</th>
		<th>Appraising Group</th>
		<th>Accessible Value</th>
		<th>Total Package</th>
		<th>Gross Weight</th>
		<th>Total Duty</th>
		<th>Fine Penalty</th>
		<th>WBE No</th>
	</tr>
	</thead>

	<tbody>
	<tr>
		<td class="bold"><?php echo $icegate['iec_no'] ?></td>
		<td class="bold"><?php echo $icegate['total_value'] ?></td>
		<td class="bold"><?php echo $icegate['type'] ?></td>
		<td class="bold"><?php echo $icegate['cha_no'] ?></td>
		<td class="bold"><?php echo $icegate['first_check'] ?></td>
		<td class="bold"><?php echo $icegate['prior_be'] ?></td>
		<td class="bold"><?php echo $icegate['section_48'] ?></td>
		<td class="bold"><?php echo $icegate['appraising_group'] ?></td>
		<td class="bold"><?php echo $icegate['accessible_value'] ?></td>
		<td class="bold"><?php echo $icegate['total_value'] ?></td>
		<td class="bold"><?php echo $icegate['gross_weight'] ?></td>
		<td class="bold"><?php echo $icegate['total_duty'] ?></td>
		<td class="bold"><?php echo $icegate['fine_penalty'] ?></td>
		<td class="bold"><?php echo $icegate['wbe_no'] ?></td>
	</tr>
	</tbody>
	</table>

	<table class="table table-condensed table-bordered" id="Table2">
	<thead>
	<tr>
		<th>Appraisement</th>
		<th>Current Queue</th>
		<th>Query Raised</th>
		<th>Query Reply</th>
		<th>Reply Date</th>
		<th>Reply Status</th>
		<th>Appraisement Date</th>
		<th>Assessment Date</th>
		<th>Payment Date</th>
		<th>Exam Date</th>
		<th>OOC Date</th>
	</tr>
	</thead>

	<tbody>
	<tr>
		<td class="bold aligntop"><?php echo $icegate['appraisement'] ?></td>
		<td class="bold aligntop"><?php echo $icegate['current_queue'] ?></td>
		<td class="bold aligntop"><?php echo str_replace('#', '<br />', $icegate['query_raised']) ?></td>
		<td class="bold aligntop"><?php echo str_replace('#', '<br />', $icegate['query_reply']) ?></td>
		<td class="bold aligntop"><?php echo $icegate['reply_date'] ?></td>
		<td class="bold aligntop"><?php echo $icegate['reply_status'] ?></td>
		<td class="bold aligntop"><?php echo $icegate['appraisement_date'] ?></td>
		<td class="bold aligntop"><?php echo $icegate['assessment_date'] ?></td>
		<td class="bold aligntop"><?php echo $icegate['payment_date'] ?></td>
		<td class="bold aligntop"><?php echo $icegate['exam_date'] ?></td>
		<td class="bold aligntop"><?php echo $icegate['ooc_date'] ?></td>
	</tr>
	</tbody>
	</table>

	<table class="table table-condensed table-bordered" id="Table3">
	<thead>
	<tr>
		<th>Challan No</th>
		<th>Duty Amount</th>
		<th>Fine Amount</th>
		<th>Interest Amount</th>
		<th>Penalty Amount</th>
		<th>Total Duty Amount</th>
		<th>Duty Paid</th>
		<th>Payment Mode</th>
	</tr>
	</thead>

	<tbody>
	<tr>
		<td class="bold"><?php echo $icegate['challan_no'] ?></td>
		<td class="bold"><?php echo $icegate['duty_amount'] ?></td>
		<td class="bold"><?php echo $icegate['fine_amount'] ?></td>
		<td class="bold"><?php echo $icegate['interest_amount'] ?></td>
		<td class="bold"><?php echo $icegate['penalty_amount'] ?></td>
		<td class="bold"><?php echo $icegate['total_duty_amount'] ?></td>
		<td class="bold"><?php echo $icegate['duty_paid'] ?></td>
		<td class="bold"><?php echo $icegate['payment_mode'] ?></td>
	</tr>
	</tbody>
	</table>
</div>

<?php 
if (count($challans) > 0) : 
	foreach ($challans as $challan) :
?>
<div class="card card-default" id="Challan">
	<div class="card-header">
		<span class="card--links"><a href="<?php echo site_url($this->_clspath.$this->_class."/print_challan/".$challan['id']) ?>" class="btn btn-xs btn-default" ><i class="icon-print"></i> Print</a></span>
		<h3 class="card-title">e-Payment Transaction Status Receipt</h3>
	</div>
	
	<!-- <div class="card-body"></div> -->

	<table class="table table-bordered" id="Table4">
	<tbody>
		<tr>
			<th colspan="2" valign="top" width="148">ICEGATE Reference ID</th>
			<td><?php echo $challan['reference_id'] ?></td>
			<th colspan="2" valign="top" width="188">Date &amp; Time of Payment</th>
			<td><?php echo $challan['payment_datetime'] ?></td>
		</tr>
		<tr>
			<th colspan="2" valign="top" width="148">IEC</th>
			<td><?php echo $challan['iec_no'] ?></td>
			<th colspan="2" valign="top" width="188">IEC Name</th>
			<td><?php echo $challan['iec_name'] ?></td>
		</tr>
		<tr>
			<th colspan="2" valign="top" width="148">Bank Branch Code</th>
			<td><?php echo $challan['bank_branch_code'] ?></td>
			<th colspan="2" valign="top" width="188">Bank Transaction Number</th>
			<td><?php echo $challan['bank_transaction_no'] ?></td>
		</tr>
		<tr>
			<th colspan="2" valign="top" width="148">Document Type</th>
			<td><?php echo $challan['document_type'] ?></td>
			<th colspan="2" valign="top" width="188">ICES Location Code</th>
			<td><?php echo $challan['ices_location_code'] ?></td>
		</tr>
		<tr>
			<th colspan="2" valign="top" width="148">Bank Name</th>
			<td><?php echo $challan['bank_name'] ?></td>
			<th colspan="2" valign="top" width="188">Receipt Date &amp; Time</th>
			<td><?php echo $challan['receipt_datetime'] ?></td>
		</tr>

		<tr>
			<th>S.No.</th>
			<th>Challan No.</th>
			<th>Document Number</th>
			<th>Document Date</th>
			<th>Duty Amount (INR)</th>
			<th>ICES Status Code</th>
		</tr>
	<?php foreach ($challan['details'] as $cd) {
		echo '<tr>
			<td>' . $cd['sr_no'] . '</td>
			<td>' . $cd['challan_no'] . '</td>
			<td>' . $cd['be_no'] . '</td>
			<td>' . $cd['be_date'] . '</td>
			<td>' . $cd['duty_amount'] . '</td>
			<td>' . $cd['ices_status_code'] . '</td>
		</tr>';
	} ?>
	</tbody>
	</table>
</div>

<?php
	endforeach;
endif; 
?>

<form action="https://epayment.icegate.gov.in/epayment/multiChallanAction" method="POST" id="IcegateForm" target="IceGate">
	<input type="hidden" name="iec" value="<?php echo $icegate['iec_no'] ?>" />
	<input type="hidden" name="locationName" value="<?php echo $icegate_port ?>" />
</form>

<iframe name="IceGate" width="100%" class="hide" id="IceGateFrame"></iframe>

<script>
$(document).ready(function(){
	<?php 
	if ($icegate['last_fetched'] == '00-00-0000 00:00:00') {
		echo '$("#Moment").text("Fetch is Pending.");';
	}
	else {
		echo 'var m = moment("' . $icegate['last_fetched'] . '", "DD-MM-YYYY hh:mm:ss").fromNow();
		$("#Moment").text(m);';
	}
	?>
	
	$('#IceGateFrame').height($(window).height()-100).width($(window).width()-45);

	$('#FetchStatus').one("click", function() {
	    $(this).attr('disabled','disabled');
	});
	$('#FetchChallan').one("click", function() {
	    $(this).attr('disabled','disabled');
	});
});

function ePayment() {
	$("#Table1").addClass('hide');
	$("#Table2").addClass('hide');
	$("#Table3").addClass('hide');
	$("#Table4").addClass('hide');
	$("#Challan").addClass('hide');
	$(".form-actions").addClass('hide');
	$('#IceGateFrame').removeClass('hide');
	$('#IcegateForm').submit();
}

function ePaymentSeparate() {
	$('#IcegateForm').attr('target', '_blank');
	$('#IcegateForm').submit();
}
</script>