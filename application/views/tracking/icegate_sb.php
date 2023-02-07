<div class="row">
	<div class="col-md-4">
		<table class="table toolbar"><tr><td>
		<?php echo '<strong>' . $page_title . '</strong> ' . $page_desc ?></td>
		<td class="alignright">
			<div class="btn-group">
				<button type="button" class="btn btn-danger" onclick="window.close();" data-placement="bottom" rel='tooltip' data-original-title='Close'><i class="icon-power-off"></i></button>
				<?php echo anchor($this->_clspath.$this->_class."/captcha/$child_job_id", '<i class="icon-refresh"></i>', 'class="btn btn-info Popup" data-placement="bottom" rel="tooltip" data-original-title="IceGate Captcha"') ?>
			</div>
		</td>
		</tr>
		</table>
	</div>

	<div class="col-md-8">
	<?php 
		echo '<table class="table toolbar">
		<tbody>
		<tr>
			<td nowrap="nowrap" width="100"><span class="bold">' . $jobs['id2_format'] . '</span></td>
			<td><span class="bold">' . $jobs['party_name'] . '</span></td>';
		$total = 0;
		if (isset($bill_info)) {
			foreach ($bill_info as $bid => $row) {
				echo '<td width="120" align="center" nowrap="true">' . anchor($this->_clspath."bill/edit/$id/$bid", $row['id2_format']) . '<br /><span class="label label-info" style="font-size: 8pt;">' . $row['date'] . '</span> <span class="label label-warning" style="font-size: 8pt;">' . $row['amount'] . '</span></td>';
				if ($row['type'] != 'Proforma Invoice') {
					$total += $row['amount'];
				}
			}
			echo '<td align="center" nowrap="true"><strong>Total</strong><br /><span class="label label-danger" style="font-size: 8pt;">' . number_format($total, 2, '.', '') . '</span></td>';
		}
		echo "</tr>
		</tbody>
		</table>";
	?>
	</div>
</div>

<table class="table table-condensed table-bordered" id="Table1">
<thead>
<tr>
	<th>IEC</th>
	<th>CHA No.</th>
	<th>Job No.</th>
	<th>Job Date</th>
	<th>Port of Discharge</th>
	<th>Total Package</th>
	<th>Gross Weight</th>
	<th>FOB(INR)</th>
	<th>Total Cess</th>
	<th>Drawback</th>
	<th>STR</th>
	<th>Total (DBK+STR)</th>
</tr>
</thead>

<tbody>
<tr>
	<td class="bold"><?php echo $icegate['iec_no'] ?></td>
	<td class="bold"><?php echo $icegate['cha_no'] ?></td>
	<td class="bold"><?php echo $icegate['job_no'] ?></td>
	<td class="bold"><?php echo $icegate['job_date'] ?></td>
	<td class="bold"><?php echo $icegate['port_of_discharge'] ?></td>
	<td class="bold"><?php echo $icegate['total_package'] ?></td>
	<td class="bold"><?php echo $icegate['gross_weight'] ?></td>
	<td class="bold"><?php echo $icegate['fob_inr'] ?></td>
	<td class="bold"><?php echo $icegate['total_cess'] ?></td>
	<td class="bold"><?php echo $icegate['drawback'] ?></td>
	<td class="bold"><?php echo $icegate['str'] ?></td>
	<td class="bold"><?php echo $icegate['total_dbk_str'] ?></td>
</tr>
</tbody>
</table>

<table class="table table-condensed table-bordered" id="Table2">
<thead>
<tr>
	<th>Warehouse Code</th>
	<th>Warehouse Name</th>
	<th>Current Que</th>
	<th>Current Status</th>
	<th>Appraising Date</th>
	<th>A.C(APR)</th>
	<th>A.C(APR) Date</th>
	<th>Exam Mark ID</th>
	<th>Mark Date</th>
</tr>
</thead>

<tbody>
<tr>
	<td class="bold aligntop"><?php echo $icegate['wharehouse_code'] ?></td>
	<td class="bold aligntop"><?php echo $icegate['wharehouse_name'] ?></td>
	<td class="bold aligntop"><?php echo $icegate['current_queue'] ?></td>
	<td class="bold aligntop"><?php echo $icegate['current_status'] ?></td>
	<td class="bold aligntop"><?php echo $icegate['appraising_date'] ?></td>
	<td class="bold aligntop"><?php echo $icegate['ac_apr'] ?></td>
	<td class="bold aligntop"><?php echo $icegate['ac_apr_date'] ?></td>
	<td class="bold aligntop"><?php echo $icegate['exam_mark_id'] ?></td>
	<td class="bold aligntop"><?php echo $icegate['mark_date'] ?></td>
</tr>
</tbody>
</table>

<table class="table table-condensed table-bordered" id="Table2">
<thead>
<tr>
	<th>Insp/E.O</th>
	<th>EXAM DATE</th>
	<th>Supdt/A.O.Id</th>
	<th>DBK A.C ID</th>
	<th>DBK A.C ID Date</th>
	<th>DBK Supdt. ID</th>
	<th>DBK Supdt. Date</th>
	<th>DEPB Supdt</th>
	<th>DEPB Supdt Date</th>
</tr>
</thead>

<tbody>
<tr>
	<td class="bold aligntop"><?php echo $icegate['insp_eo'] ?></td>
	<td class="bold aligntop"><?php echo $icegate['exam_date'] ?></td>
	<td class="bold aligntop"><?php echo $icegate['supdt_ao_id'] ?></td>
	<td class="bold aligntop"><?php echo $icegate['dbk_ac_id'] ?></td>
	<td class="bold aligntop"><?php echo $icegate['dbk_ac_id_date'] ?></td>
	<td class="bold aligntop"><?php echo $icegate['dbk_supdt_id'] ?></td>
	<td class="bold aligntop"><?php echo $icegate['dbk_supdt_date'] ?></td>
	<td class="bold aligntop"><?php echo $icegate['depd_supdt'] ?></td>
	<td class="bold aligntop"><?php echo $icegate['depb_supdt_date'] ?></td>
</tr>
</tbody>
</table>

<table class="table table-condensed table-bordered" id="Table2">
<thead>
<tr>
	<th>DEPB Lic</th>
	<th>DEPB Lic Date</th>
	<th>Sample Drawn</th>
	<th>Test Report</th>
	<th>LEO Date</th>
	<th>EP Copy Print Status</th>
	<th>Print Status</th>
	<th>DBK Scroll No</th>
	<th>Scroll Date</th>
</tr>
</thead>

<tbody>
<tr>
	<td class="bold aligntop"><?php echo $icegate['depb_lic'] ?></td>
	<td class="bold aligntop"><?php echo $icegate['depb_lic_date'] ?></td>
	<td class="bold aligntop"><?php echo $icegate['sample_drawn'] ?></td>
	<td class="bold aligntop"><?php echo $icegate['test_report'] ?></td>
	<td class="bold aligntop"><?php echo $icegate['leo_date'] ?></td>
	<td class="bold aligntop"><?php echo $icegate['ep_copy_print_status'] ?></td>
	<td class="bold aligntop"><?php echo $icegate['print_status'] ?></td>
	<td class="bold aligntop"><?php echo $icegate['dbk_scroll_no'] ?></td>
	<td class="bold aligntop"><?php echo $icegate['dbk_scroll_date'] ?></td>
</tr>
</tbody>
</table>

<table class="table table-condensed table-bordered" id="Table3">
<thead>
<tr>
	<th>EGM No.</th>
	<th>EGM Date</th>
	<th>Container No.</th>
	<th>Seal No.</th>
	<th>Error Message</th>
</tr>
</thead>

<tbody>
<tr>
	<td class="bold"><?php echo $icegate['egm_no'] ?></td>
	<td class="bold"><?php echo $icegate['egm_date'] ?></td>
	<td class="bold"><?php echo $icegate['container_no'] ?></td>
	<td class="bold"><?php echo $icegate['seal_no'] ?></td>
	<td class="bold"><?php echo $icegate['error_message'] ?></td>
</tr>
</tbody>
</table>

<table class="table table-condensed table-bordered" id="Table3">
<thead>
<tr>
	<th>Query No.</th>
	<th>Query Date</th>
	<th>Query Text</th>
	<th>Pending With</th>
	<th>Officer Name</th>
	<th>Reply Date</th>
</tr>
</thead>

<tbody>
<tr>
	<td class="bold"><?php echo $icegate['query_no'] ?></td>
	<td class="bold"><?php echo $icegate['query_date'] ?></td>
	<td class="bold"><?php echo $icegate['query_text'] ?></td>
	<td class="bold"><?php echo $icegate['pending_with'] ?></td>
	<td class="bold"><?php echo $icegate['officer_name'] ?></td>
	<td class="bold"><?php echo $icegate['reply_date'] ?></td>
</tr>
</tbody>
</table>

<form action="https://enquiry.icegate.gov.in/epayment/multiChallanAction" method="POST" id="IcegateForm" target="IceGate">
	<input type="hidden" name="iec" value="<?php echo $icegate['iec_no'] ?>" />
	<input type="hidden" name="locationName" value="<?php echo $icegate_port ?>" />
</form>

<form action="https://enquiry.icegate.gov.in/epayment/multiChallanAction" method="POST" id="IcegateForm" target="IceGate">
	<input type="hidden" name="iec" value="<?php echo $icegate['iec_no'] ?>" />
	<input type="hidden" name="locationName" value="<?php echo $icegate_port ?>" />
</form>

<iframe name="IceGate" width="100%" id="IceGateFrame" style="display: none;"></iframe>

<script>
$(document).ready(function(){
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
	$(".form-actions").addClass('hide');
	$('#IceGateFrame').removeClass('hide');
	$('#IcegateForm').submit();
}

function ePaymentSeparate() {
	$('#IcegateForm').attr('target', '_blank');
	$('#IcegateForm').submit();
}
</script>