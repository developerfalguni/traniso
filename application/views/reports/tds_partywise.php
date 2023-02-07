
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
</div>
</form>
	</td>
</tr>
</table>

<table class="table table-condensed table-striped table-bordered">
<thead>
<tr>
	<th width="48px">Sr No</th>
	<th>Party</th>
	<th>PAN No</th>
	<th width="100px">Invoice</th>
	<th width="80px">TDS</th>
	<th width="80px">Surcharge</th>
	<th width="80px">Edu Cess</th>
	<th width="80px">H.Edu Cess</th>
</tr>
</thead>

<tbody>
<?php 
$i = 1;
$total = array(
	'invoice'   => 0,
	'surcharge' => 0,
	'edu_cess'  => 0,
	'hedu_cess' => 0,
	'tds'       => 0,
);
foreach ($rows as $pan_no => $r) {
	$total['invoice']   += $r['invoice_amount'];
	$total['tds']       += $r['tds_amount'];
	$total['surcharge'] += $r['tds_surcharge_amount'];
	$total['edu_cess']  += $r['tds_edu_cess_amount'];
	$total['hedu_cess'] += $r['tds_hedu_cess_amount'];

	echo '<tr>
	<td class="aligncenter">' . $i++ . '</td>
	<td>' . $r['party_name'] . '</td>
	<td>' . $pan_no . '</td>
	<td class="alignright">' . inr_format($r['invoice_amount']) . '</td>
	<td class="alignright">' . inr_format($r['tds_amount']) . '</td>
	<td class="alignright">' . inr_format($r['tds_surcharge_amount']) . '</td>
	<td class="alignright">' . inr_format($r['tds_edu_cess_amount']) . '</td>
	<td class="alignright">' . inr_format($r['tds_hedu_cess_amount']) . '</td>
</tr>
	';
}
	echo '
<tfoot>
<tr>
	<th colspan="3" class="alignright">Total</th>
	<th class="alignright">' . $total['invoice'] . '</th>
	<th class="alignright">' . $total['tds'] . '</th>
	<th class="alignright">' . $total['surcharge'] . '</th>
	<th class="alignright">' . $total['edu_cess'] . '</th>
	<th class="alignright">' . $total['hedu_cess'] . '</th>
</tr>
</tfoot>
</tbody>
</table>
';
?>

<script type="text/javascript">
$(document).ready(function() {
	<?php echo 'dateRangePicker({
		years:     ['.$years[0].', '.$years[1].'],
		from_date: \''.convDate($from_date).'\',
		to_date:   \''.convDate($to_date).'\'
	});' ?>
});
</script>