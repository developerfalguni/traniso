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

<?php foreach ($stax as $rows) : ?>
<h4><?php echo $rows[0]['stax_category'] ?></h4>
<table class="table table-condensed table-striped table-bordered">
<thead>
<tr>
	<th width="48px">Sr No</th>
	<th>Date</th>
	<th>Voucher</th>
	<th>Party Name</th>
	<th>Service Tax No</th>
	<th>Invoice No</th>
	<th width="100px">Invoice Date</th>
	<th width="100px">Invoice Amount</th>
	<th width="100px">Stax Amount</th>
</tr>
</thead>

<tbody>
<?php 
	$i = 1;
	$total = array('invoice' => 0, 'stax' => 0);
	foreach ($rows as $s) {
		$total['invoice'] = bcadd($total['invoice'], $s['stax_on_amount'], 2);
		$total['stax']    = bcadd($total['stax'], $s['amount'], 2);

		echo '<tr>
	<td class="aligncenter">' . $i++ . '</td>
	<td>' . $s['date'] . '</td>
	<td>' . anchor('/accounting/'.underscore($s['url']), $s['id2_format'], 'target="_blank"') . '</td>
	<td>' . ($s['party_name'] ? $s['party_name'] : '<span class="red">' . $s['party_ledger'] . '</span>') . '</td>
	<td>' . $s['service_tax_no'] . '</td>
	<td>' . $s['invoice_no'] . '</td>
	<td>' . $s['invoice_date'] . '</td>
	<td class="alignright">' . $s['stax_on_amount'] . '</td>
	<td class="alignright">' . $s['amount'] . '</td>
</tr>';
}
?>
</tbody>

<tfoot>
<tr>
	<th colspan="6" class="alignright">Total</th>
	<th class="alignright"><?php echo $total['invoice'] ?></th>
	<th></th>
	<th class="alignright"><?php echo $total['stax'] ?></th>
</tr>
</tfoot>
</table>
<?php endforeach; ?>

<script type="text/javascript">
$(document).ready(function() {
	<?php echo 'dateRangePicker({
		years:     ['.$years[0].', '.$years[1].'],
		from_date: \''.convDate($from_date).'\',
		to_date:   \''.convDate($to_date).'\'
	});' ?>
});
</script>