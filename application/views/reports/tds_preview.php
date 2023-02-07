<html>
<head>
	<title><?php echo $page_title ?></title>
	<style>
		<?php echo file_get_contents(FCPATH.'assets/css/print.css'); ?>
	
		.details { font-size: 7pt; }
	</style>
</head>

<body>

<h2 class="aligncenter"><?php echo $company['name'] ?></h2>
<h4 class="aligncenter"><?php echo $page_title . ' ' . $page_desc ?></h4>

<h4>Total Summary</h4>
<table class="details">
<thead>
<tr>
	<th>TDS Type</th>
	<th>Category</th>
	<th>Invoice</th>
	<th>Amount</th>
	<th>Surcharge</th>
	<th>Edu Cess</th>
	<th>H.Edu Cess</th>
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
foreach ($tds['detail'] as $ledger => $payments) : 
	foreach ($payments as $deductee => $tds_details) : ?>

<h4><?php echo $ledger . ' - ' . $deductee ?></h4>

<table class="details">
<thead>
<tr>
	<th>Sr No</th>
	<th>Voucher</th>
	<th>Party</th>
	<th>Credit Date</th>
	<th>Invoice</th>
	<th>TDS</th>
	<th>Amount</th>
	<th>Surcharge</th>
	<th>Edu Cess</th>
	<th>H.Edu Cess</th>
	<th>Date</th>
	<th>BSR Code</th>
	<th>Challan No</th>
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
			$total['surcharge'] += $tdr['tds_surcharge'];
			$total['edu_cess']  += $tdr['tds_edu_cess'];
			$total['hedu_cess'] += $tdr['tds_hedu_cess'];

			echo '<tr>
	<td class="aligncenter">' . $i++ . '</td>
	<td class="aligncenter">' . $tdr['id2_format'] . '</td>
	<td>' . $tdr['party_name'] . '</td>
	<td class="aligncenter">' . $tdr['credit_date'] . '</td>
	<td class="alignright">' . inr_format($tdr['invoice_amount']) . '</td>
	<td class="aligncenter">' . $tdr['tds'] . '</td>
	<td class="alignright">' . inr_format($tdr['tds_amount']) . '</td>
	<td class="alignright">' . inr_format($tdr['tds_surcharge']) . '</td>
	<td class="alignright">' . inr_format($tdr['tds_edu_cess']) . '</td>
	<td class="alignright">' . inr_format($tdr['tds_hedu_cess']) . '</td>
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
</tr>
</tfoot>
</tbody>
</table>
';
	endforeach; 
endforeach;
?>
</body>
</html>