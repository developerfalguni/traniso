<table class="table toolbar">
<tr>
	<td>
<?php echo form_open($this->uri->uri_string(), 'id="Report"'); ?>
<input type="hidden" name="from_date" value="<?php echo $from_date ?>" id="FromDate" />
<input type="hidden" name="to_date"   value="<?php echo $to_date ?>" id="ToDate" />
<div id="ReportRange" class="pull-left" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc">
	<i class="icon-calendar icon-large"></i> <span></span> <b class="caret"></b>
</div>&nbsp;
<button type="submit" class="btn btn-primary" id="SearchButton"><i class="fa fa-search"></i> Search</button>&nbsp;
<div class="btn-group">
<?php echo anchor($this->_clspath.$this->_class."/preview", '<i class="icon-file-o"></i>', 'class="btn btn-default Popup"') ?>
<?php echo anchor($this->_clspath.$this->_class."/preview/1", '<i class="icon-file-pdf"></i>', 'class="btn btn-default Popup"') ?>
<?php echo anchor($this->_clspath.$this->_class."/excel", '<i class="icon-file-excel"></i>', 'class="btn btn-warning Popup"') ?>
</div>
</form>
	</td>
</tr>
</table>


<?php 
$totals = array('amount' => 0);
foreach ($reimbersment['heading'] as $index => $code) {
	$totals[$code] = 0;
}
foreach ($reimbersment['reimbersment'] as $s) {
	$totals['amount'] = bcadd($totals['amount'], $s['amount'], 2);
	foreach ($reimbersment['heading'] as $index => $code)
		$totals[$code] = bcadd($totals[$code], $s[$code], 2);
}
?>

<table class="table table-condensed table-striped table-bordered">
<thead>
<tr>
	<th>Voucher No</th>
	<th>Date</th>
	<th>Party</th>
	<th>Amount</th>
<?php 
foreach ($reimbersment['heading'] as $index => $code) {
	if ($totals[$code] > 0)
		echo '<th>' . $code . '</th>';
}
?>
</tr>
</thead>

<tbody>
<?php 
	foreach ($reimbersment['reimbersment'] as $s) {
		echo '<tr>
	<td>' . $s['id2_format'] . '</td>
	<td>' . $s['date'] . '</td>
	<td>' . $s['party_name'] . '</td>
	<td class="alignright">' . number_format($s['amount'], 0, '.', '') . '</td>';
	foreach ($reimbersment['heading'] as $index => $code) {
		if ($totals[$code] > 0)
			echo '<td class="alignright">' . number_format($s[$code], 0, '.', '') . '</td>';
	}
	echo '</tr>';
}
?>
</tbody>

<tfoot>
<tr>
	<th colspan="3">Total</th>
<?php 
foreach ($totals as $code => $amount) {
	if ($amount > 0)
		echo '<th class="alignright">' . number_format($amount, 0, '.', '') . '</th>';
}
?>
</tr>
</tfoot>
</table>


<script type="text/javascript">
$(document).ready(function() {
	<?php echo 'dateRangePicker({
		years:     ['.$years[0].', '.$years[1].'],
		from_date: \''.convDate($from_date).'\',
		to_date:   \''.convDate($to_date).'\'
	});' ?>
});
</script>