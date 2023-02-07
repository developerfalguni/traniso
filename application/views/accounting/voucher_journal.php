<style>
td.alt { 
	background-color: #ffc !important;
	background-color: rgba(255, 255, 0, 0.2) !important;
}
</style>

<?php 
echo form_open($this->uri->uri_string(), 'id="MainForm"');	
echo form_hidden($voucher_id);
?>

<input type="hidden" name="vouchers" value="1" />
<table class="table toolbar">
<tr>
	<td><div class="input-filter-container"><input type="search" id="input-filter" class="form-control form-control-sm" placeholder="Find by JO No, JO Date, Supplier, Products, Ordered By" ></div></td>
	<td class="alignright"><button type="submit" class="btn btn-success" id="Update">Update</button>
		<button type="button" class="btn btn-danger" id="Close">Close</button></td>
</tr>
</table>

<table class="table table-condensed table-striped table-bordered" id="Result">
<thead>
<tr>
	<th>Voucher No</th>
	<th>Date</th>
	<th>Bill No</th>
	<th>Job No</th>
	<th>Amount</th>
	<th>Paid Amount</th>
	<th>Balance</th>
	<th width="24px" class="aligncenter"><a href="#" class="CheckAll"><i class="icon-check"></i></a></th>
</tr>
</thead>

<tbody>
<?php 
	$total = array(
		'amount'  => 0,
	);
	foreach ($rows as $r) {
		$total['amount']  = bcadd($total['amount'], $r['amount'], 2);

		echo '<tr>
	<td class="aligncener tiny">' . anchor('accounting'.'/'.underscore($r['url']), $r['id2_format'], 'target="_blank"') . '</td>
	<td class="aligncener tiny">' . $r['voucher_date'] . '</td>
	<td class="aligncener tiny">' . $r['invoice_no'] . '</td>
	<td class="aligncener tiny">' . $r['job_no'] . '</td>
	<td class="alignright tiny">' . $r['amount'] . '</td>
	<td class="alignright tiny">' . $r['paid_amount'] . '</td>
	<td class="alignright tiny">' . $r['balance'] . '</td>
	<td class="aligncenter">
	' . form_checkbox(array(
		'name'    => 'voucher_id2['.$r['id'].']', 
		'value'   => $r['id'], 
		'checked' => ($r['voucher_id2'] > 0 ? true : false), 
		'class'   => 'DeleteCheckbox'
	)) . '</td>
</tr>';
	}

	echo '</tbody>

<tfoot>
<tr>
	<th colspan="4" class="alignright">Total</th>
	<th class="alignright">' . $total['amount'] . '</th>
	<th></th>
</tr>
</tfoot>
</table>
</form>';
?>

<script>
$(document).ready(function() {
	var stripeTable = function(table) {
		table.find('tr').removeClass('striped').filter(':visible:even').addClass('striped');
	};
	$("#Result").filterTable({
		callback: function(term, table) { 
			stripeTable(table); 
		},
		inputSelector: '#input-filter'
	});
	stripeTable($("#Result"));

	$("#Close").on("click", function() {
		top.opener.location.reload(true);
		self.close();
	});
});
</script>