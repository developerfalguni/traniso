<style>
.color1  { background-color: #FF7AA3 !important; }
.color2  { background-color: #FF7B7A !important; }
.color3  { background-color: #FFB97A !important; }
.color4  { background-color: #FFEA7A !important; }
.color5  { background-color: #E7FF7A !important; }
.color6  { background-color: #B3FF7A !important; }
.color7  { background-color: #7AFF91 !important; }
.color8  { background-color: #7AFFE4 !important; }
.color9  { background-color: #7AEAFF !important; }
.color10 { background-color: #7ABBFF !important; }
.color11 { background-color: #7A88FF !important; }
.color12 { background-color: #917AFF !important; }
.color13 { background-color: #C77AFF !important; }
</style>

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
<?php echo anchor($this->_clspath.$this->_class."/preview", 'Preview', 'class="btn btn-default Popup"') ?>
<?php echo anchor($this->_clspath.$this->_class."/preview/1", 'PDF', 'class="btn btn-default Popup"') ?>
<?php echo anchor($this->_clspath.$this->_class."/excel", 'Excel', 'class="btn btn-warning Popup"') ?>
<?php echo anchor($this->_clspath.$this->_class."/excel2", 'Summary', 'class="btn btn-warning Popup"') ?>
</div>
</form>
	</td>
</tr>
</table>

<table class="table table-condensed table-striped table-bordered">
<thead>
<tr>
	<th>S.Tax Category</th>
	<th width="110px">Service Charge</th>
	<th width="110px">SC Cr. Note</th>
	<th width="110px">Payment</th>
	<th width="110px">Opening</th>
	<th width="110px">Credit</th>
	<th width="110px">Credit Note</th>
	<th width="110px">Debit</th>
	<th width="110px">Cr - Dr</th>
	<th width="110px">To Pay</th>
</th>
</thead>

<tbody>
<?php 
	$i = 1;
	$color = array();
	$total = array(
		'service_charge' => 0, 
		'credit_note_sc' => 0, 
		'payment'        => 0, 
		'opening'        => 0, 
	    'credit'         => 0, 
	    'credit_note'    => 0, 
	    'debit'          => 0, 
	    'cr_dr'          => 0, 
	    'to_pay'         => 0
	);
	foreach ($category as $id => $c) {
		$color[$id] = 'color'.$i++;
		$to_pay = bcsub(bcadd(bcadd($c['payment'], $c['opening'], 2), $c['debit'], 2), $c['credit'], 2);

		$total['service_charge'] = bcadd($total['service_charge'], $c['service_charge'], 2);
		$total['credit_note_sc'] = bcadd($total['credit_note_sc'], $c['credit_note_sc'], 2);
		$total['payment']        = bcadd($total['payment'], $c['payment'], 2);
		$total['opening']        = bcadd($total['opening'], $c['opening'], 2);
		$total['credit']         = bcadd($total['credit'], $c['credit'], 2);
		$total['credit_note']    = bcadd($total['credit_note'], $c['credit_note'], 2);
		$total['debit']          = bcadd($total['debit'], $c['debit'], 2);
		$total['cr_dr']          = bcsub($total['credit'], $c['debit'], 2);
		$total['to_pay']         = bcadd($total['to_pay'], $to_pay, 2);

		echo '<tr>
	<td class="' . $color[$id] . '">' . $c['name'] . '</td>
	<td class="alignright ' . $color[$id] . '">' . inr_format($c['service_charge']) . '</td>
	<td class="alignright ' . $color[$id] . '">' . inr_format($c['credit_note_sc']) . '</td>
	<td class="alignright ' . $color[$id] . '">' . inr_format($c['payment']) . ' ' . ($c['payment'] >= 0 ? 'Dr' : 'Cr') . '</td>
	<td class="alignright ' . $color[$id] . '">' . inr_format(abs($c['opening'])) . ' ' . ($c['opening'] >= 0 ? 'Dr' : 'Cr') . '</td>
	<td class="alignright ' . $color[$id] . '">' . inr_format($c['credit']) . '</td>
	<td class="alignright ' . $color[$id] . '">' . inr_format($c['credit_note']) . '</td>
	<td class="alignright ' . $color[$id] . '">' . inr_format($c['debit']) . '</td>
	<td class="alignright ' . $color[$id] . '">' . inr_format(bcsub(bcadd($c['credit'], $c['credit_note'], 2), $c['debit'], 2)) . '</td>
	<td class="alignright ' . $color[$id] . '">' . inr_format(abs($to_pay)) . ' ' . ($to_pay >= 0 ? 'Dr' : 'Cr') . '</td>
</tr>';
}

$totals = array('amount' => 0);
foreach ($stax['heading'] as $index => $row) {
	$totals[$row['code']] = 0;
}
foreach ($stax['stax'] as $s) {
	$totals['amount'] = bcadd($totals['amount'], $s['amount'], 2);
	foreach ($stax['heading'] as $row)
		$totals[$row['code']] = bcadd($totals[$row['code']], $s[$row['code']], 2);
}

?>
</tbody>

<tfoot>
<tr>
	<th>Total</th>
	<th class="alignright"><?php echo inr_format($total['service_charge']) ?></th>
	<th class="alignright"><?php echo inr_format($total['credit_note_sc']) ?></th>
	<th class="alignright"><?php echo inr_format(abs($total['payment'])) . ' ' . ($total['payment'] >= 0 ? 'Dr' : 'Cr') ?></th>
	<th class="alignright"><?php echo inr_format(abs($total['opening'])) . ' ' . ($total['opening'] >= 0 ? 'Dr' : 'Cr') ?></th>
	<th class="alignright"><?php echo inr_format($total['credit']) ?></th>
	<th class="alignright"><?php echo inr_format($total['credit_note']) ?></th>
	<th class="alignright"><?php echo inr_format($total['debit']) ?></th>
	<th class="alignright"><?php echo inr_format($total['cr_dr']) ?></th>
	<th class="alignright"><?php 
		$to_pay = bcsub(bcadd(bcadd($total['payment'], $total['opening'], 2), $total['debit'], 2), $total['credit'], 2);
		echo inr_format(abs(round($to_pay))) . ' ' . ($to_pay >= 0 ? 'Dr' : 'Cr') 
	?></th>
</tr>
</tfoot>
</table>


<table class="table table-condensed table-striped table-bordered">
<thead>
<tr>
	<th>Voucher No</th>
	<th>Date</th>
	<th>Party</th>
	<th>Invoice Amount</th>
<?php 
foreach ($stax['heading'] as $index => $row) {
	if ($totals[$row['code']] > 0)
		echo '<th>' . $row['code'] . '</th>';
}
?>
</tr>
</thead>

<tbody>
<?php 
	foreach ($stax['stax'] as $s) {
		echo '<tr>
	<td>' . anchor('accounting/'.underscore($s['url']), $s['id2_format'], 'target="_blank"') . '</td>
	<td>' . $s['date'] . '</td>
	<td>' . $s['party_name'] . '</td>
	<td class="alignright">' . $s['amount'] . '</td>';
	foreach ($stax['heading'] as $row) {
		if ($totals[$row['code']] > 0)
			echo '<td class="alignright ' . $color[$row['stax_category_id']] . '">' . $s[$row['code']] . '</td>';
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
		echo "<th class='alignright'>$amount</th>";
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