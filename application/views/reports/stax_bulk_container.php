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
	<th rowspan="3" class="aligncenter">Service Tax Category</th>
	<th colspan="3" class="aligncenter">Service Charge</th>
	<th colspan="3" class="aligncenter">Debit</th>
	<th colspan="3" class="aligncenter">Credit</th>
</tr>

<tr>
	<th>Bulk</th>
	<th>Container</th>
	<th>Amount</th>
	<th>Bulk</th>
	<th>Container</th>
	<th>Amount</th>
	<th>Bulk</th>
	<th>Container</th>
	<th>Amount</th>
</tr>
</thead>

<tbody>
<?php foreach ($stax as $r) {
	echo '<tr>
	<td>' . $r['name'] . '</td>
	<td class="alignright">' . inr_format($r['bulk_sc']) . '</td>
	<td class="alignright">' . inr_format($r['cont_sc']) . '</td>
	<td class="alignright bold">' . inr_format(($r['bulk_sc'] + $r['cont_sc'])) . '</td>
	<td class="alignright">' . inr_format($r['bulk_debit']) . '</td>
	<td class="alignright">' . inr_format($r['cont_debit']) . '</td>
	<td class="alignright bold">' . inr_format(($r['bulk_debit'] + $r['cont_debit'])) . '</td>
	<td class="alignright">' . inr_format($r['bulk_credit']) . '</td>
	<td class="alignright">' . inr_format($r['cont_credit']) . '</td>
	<td class="alignright bold">' . inr_format(($r['bulk_credit'] + $r['cont_credit'])) . '</td>
</tr>';
}
?>
</tbody>

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