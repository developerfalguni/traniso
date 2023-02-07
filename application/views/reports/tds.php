
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

<h4>Total Summary</h4>
<table class="table table-condensed table-striped table-bordered">
<thead>
<tr>
	<th>TDS Type</th>
	<th>Category</th>
	<th width="100px">Invoice</th>
	<th width="100px">Amount</th>
	<th width="100px">Surcharge</th>
	<th width="100px">Edu Cess</th>
	<th width="100px">H.Edu Cess</th>
</tr>
</thead>

<tbody>
<?php 
$total = array(
	'invoice'   => 0,
	'tds'       => 0,
	'surcharge' => 0,
	'edu_cess'  => 0,
	'hedu_cess' => 0,
);
foreach ($tds['summary'] as $deductee_type => $ledgers) {
	foreach ($ledgers as $ledger_name => $dr) {

		$total['invoice']   = bcadd($total['invoice'], $dr['invoice_amount'], 2);
		$total['tds']       = bcadd($total['tds'], $dr['tds_amount'], 2);
		$total['surcharge'] = bcadd($total['surcharge'], $dr['surcharge'], 2);
		$total['edu_cess']  = bcadd($total['edu_cess'], $dr['edu_cess'], 2);
		$total['hedu_cess'] = bcadd($total['hedu_cess'], $dr['hedu_cess'], 2);

		echo '<tr>
	<td>' . $deductee_type . '</td>
	<td>' . $ledger_name . '</td>
	<td class="alignright">' . inr_format($dr['invoice_amount']) . '</td>
	<td class="alignright">' . inr_format($dr['tds_amount']) . '</td>
	<td class="alignright">' . inr_format($dr['surcharge']) . '</td>
	<td class="alignright">' . inr_format($dr['edu_cess']) . '</td>
	<td class="alignright">' . inr_format($dr['hedu_cess']) . '</td>
</tr>';
	}
} 
?>
<tfoot>
<tr>
	<th colspan="2" class="alignright">Total</th>
	<th class="alignright"><?php echo $total['invoice'] ?></th>
	<th class="alignright"><?php echo $total['tds'] ?></th>
	<th class="alignright"><?php echo $total['surcharge'] ?></th>
	<th class="alignright"><?php echo $total['edu_cess'] ?></th>
	<th class="alignright"><?php echo $total['hedu_cess'] ?></th>
</tr>
</tfoot>
</tbody>
</table>

<?php 
if (isset($tds['detail'])) :
	foreach ($tds['detail'] as $ledger => $payments) : 
		foreach ($payments as $deductee => $tds_details) : ?>

<h4><?php echo $ledger . ' - ' . $deductee ?></h4>

<table class="table table-condensed table-striped table-bordered">
<thead>
<tr>
	<th width="48px">Sr No</th>
	<th width="100px">Voucher</th>
	<th>Party</th>
	<th width="80px">Credit Date</th>
	<th width="100px">Invoice</th>
	<th width="48px">TDS %</th>
	<th width="80px">TDS</th>
	<th width="80px">Surcharge</th>
	<th width="80px">Edu Cess</th>
	<th width="80px">H.Edu Cess</th>
	<th width="80px">Date</th>
	<th width="80px">BSR Code</th>
	<th width="80px">Challan No</th>
</tr>
</thead>

<tbody>
<?php 
			$i = 1;
			$total = array(
				'invoice'   => 0,
				'tds'       => 0,
				'surcharge' => 0,
				'edu_cess'  => 0,
				'hedu_cess' => 0,
			);
			foreach ($tds_details as $tdr) {
				
				$total['invoice']   += $tdr['invoice_amount'];
				$total['tds']       += $tdr['tds_amount'];
				$total['surcharge'] += $tdr['tds_surcharge_amount'];
				$total['edu_cess']  += $tdr['tds_edu_cess_amount'];
				$total['hedu_cess'] += $tdr['tds_hedu_cess_amount'];

				echo '<tr>
	<td class="aligncenter">' . $i++ . '</td>
	<td class="aligncenter">' . anchor('/accounting/'.underscore($tdr['url']), $tdr['id2_format'], 'target="_blank"') . '</td>
	<td>' . $tdr['party_name'] . '</td>
	<td class="aligncenter">' . $tdr['credit_date'] . '</td>
	<td class="alignright">' . inr_format($tdr['invoice_amount']) . '</td>
	<td class="aligncenter">' . $tdr['tds'] . '</td>
	<td class="alignright">' . inr_format($tdr['tds_amount']) . '</td>
	<td class="alignright">' . inr_format($tdr['tds_surcharge_amount']) . '</td>
	<td class="alignright">' . inr_format($tdr['tds_edu_cess_amount']) . '</td>
	<td class="alignright">' . inr_format($tdr['tds_hedu_cess_amount']) . '</td>
	<td class="aligncenter">' . $tdr['tds_stax_date'] . '</td>
	<td>' . $tdr['tds_stax_bsr_code'] . '</td>
	<td>' . $tdr['tds_stax_challan_no'] . '</td>
</tr>
	';
			}
		echo '
<tfoot>
<tr>
	<th colspan="4" class="alignright">Total</th>
	<th class="alignright">' . $total['invoice'] . '</th>
	<th></th>
	<th class="alignright">' . $total['tds'] . '</th>
	<th class="alignright">' . $total['surcharge'] . '</th>
	<th class="alignright">' . $total['edu_cess'] . '</th>
	<th class="alignright">' . $total['hedu_cess'] . '</th>
	<th></th>
	<th></th>
	<th></th>
</tr>
</tfoot>
</tbody>
</table>
';
		endforeach; 
	endforeach;
endif;
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