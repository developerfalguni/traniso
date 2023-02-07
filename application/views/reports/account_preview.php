<!DOCTYPE html>
<html lang="en">
<head>
	<title><?php echo $page_title ?></title>
	<style>
		<?php echo file_get_contents(FCPATH.'assets/css/print.css'); ?>
		
		body { font-family: "Times New Roman", serif; }
		.tiny { font-size: 0.7em !important; }
	</style>
</head>

<body>
<h2 class="aligncenter"><?php echo $company['name'] ?></h2>
<h4 class="aligncenter"><?php echo $page_title ?></h4>

<?php if ($monthly) : ?>

<table class="details">
<thead>
<tr>
	<th>Month</th>
	<th>Debit</th>
	<th>Credit</th>
	<th>Balance</th>
</tr>
</thead>

<tbody>
<?php 
$total   = array('debit' => 0, 'credit' => 0, 'balance' => 0);
foreach ($rows['vouchers'] as $i => $e) {
	if ($i === 'pdc_cheques') continue;

	if ($i == 0) {
		$total['balance'] += $e['amount'];
		echo '<tr>
	<td class="big">Opening Balance</td>
	<td></td>
	<td></td>
	<td class="big alignright ' . ($total['balance'] >= 0 ? 'green' : 'red') . '">' . inr_format(abs($total['balance'])) . $this->accounting->getDrCr($total['balance']) . '</td>
</tr>';
		continue;
	}
	$total['debit']   += $e['debit'];
	$total['credit']  += $e['credit'];
	$total['balance'] += ($e['debit'] + $e['credit']);

	echo '<tr>
	<td>' . $e['month'] . '</td>
	<td class="alignright">' . ($e['debit'] >= 0 ? inr_format(number_format($e['debit'], 2, '.', '')) : '') . '</td>
	<td class="alignright">' . ($e['credit'] < 0 ? inr_format(number_format(abs($e['credit']), 2, '.', '')) : '') . '</td>
	<td class="alignright ' . ($total['balance'] >= 0 ? 'green' : 'red') . '">' . inr_format(number_format(abs($total['balance']), 2, '.', '')) . $this->accounting->getDrCr($total['balance']) . '</td>
</tr>';
} ?>

<tr>
	<td class="big">Closing Balance</td>
	<td class="big alignright"><?php echo inr_format(abs($total['debit'])) ?></td>
	<td class="big alignright"><?php echo inr_format(abs($total['credit'])) ?></td>
	<td class="big alignright <?php echo ($total['balance'] >= 0 ? 'green' : 'red') ?>"><?php echo inr_format(abs($total['balance'])) . $this->accounting->getDrCr($total['balance']) ?></td>
</tr>
</tbody>
</table>


<?php else : ?>

<table class="details">
<thead>
<tr>
	<th>Date</th>
	<th>No</th>
	<th>Description</th>
	<th>Debit</th>
	<th>Credit</th>
	<th>Balance</th>
</tr>
</thead>

<tbody>
<?php 
$filters = array();
$total   = array('debit' => 0, 'credit' => 0, 'balance' => 0);
foreach ($rows['vouchers'] as $i => $e) {
	if ($i < 0) {
		foreach ($e as $pdc) {
			$amount           = $pdc->amount;
			$total['debit']   = bcadd($total['debit'], $pdc->amount, 2);
			$total['balance'] = bcadd($total['balance'], $amount, 2);

			echo '<tr>
	<td class="purple aligncenter aligntop tiny">' . $pdc->cheque_date . '</td>
	<td class="purple aligntop tiny">' . $pdc->cheque_no . '</td>
	<td class="purple tiny">PDC Cheque</td>
	<td class="purple alignright">' . inr_format($pdc->amount) . '</td>
	<td></td>
	<td class="alignright aligntop ' . ($total['balance'] >= 0 ? 'green' : 'red') . '">' . inr_format(number_format(abs($total['balance']), 2, '.', '')) . $this->accounting->getDrCr($total['balance']) . '</td>
</tr>';
		}
		continue;
	}

	if ($i == 0) {
		$total['balance'] = bcadd($total['balance'], $e['amount'], 2);

		echo '<tr>
	<td></td>
	<td></td>
	<td class="big">Opening Balance</td>
	<td></td>
	<td></td>
	<td class="big alignright ' . ($total['balance'] >= 0 ? 'green' : 'red') . '">' . inr_format(abs($total['balance'])) . $this->accounting->getDrCr($total['balance']) . '</td>
</tr>';
		continue;
	}

	$amount           = bcadd($e['debit'], $e['credit'], 2);
	$total['debit']   = bcadd($total['debit'], $e['debit'], 2);
	$total['credit']  = bcadd($total['credit'], $e['credit'], 2);
	$total['balance'] = bcadd($total['balance'], $amount, 2);

	$filters[$e['ledger2_id']] = $e['ledger2'];

	echo '<tr>
	<td class="aligncenter aligntop tiny">' . $e['date'] . '</td>
	<td class="aligntop tiny">' . $e['no'].'-'.$e['id3'] . '</td>
	<td class="aligntop tiny">' . $this->accounting->getToBy($amount) . ' ' . $e['ledger2'] . 
		((strlen($e['cheque_no_date']) == 0 OR $e['cheque_no_date'] == '0 / 00-00-0000') ? '' : '&nbsp;&nbsp;&nbsp; Chq No: ' . $e['cheque_no_date']) . 
		((strlen($e['invoice_no_date']) == 0 OR $e['invoice_no_date'] == '0 / 00-00-0000') ? '' : '&nbsp;&nbsp;&nbsp; Bill No: ' . $e['invoice_no_date']);

	if ($show_desc)
		 echo '<br />' . $e['narration'];

	if ($show_desc && isset($e['details']) && count($e['details']) > 0) {
		if ($e['voucher_type_id'] == 2 OR $e['voucher_type_id'] == 3 OR $e['voucher_type_id'] == 4) {
			if (isset($rows['jobs'][$e['job_id']]) && count($rows['jobs'][$e['job_id']]) > 0)
				echo $rows['jobs'][$e['job_id']]->party_name . ' / ' . $rows['jobs'][$e['job_id']]->bl_no . ' / ' . $rows['jobs'][$e['job_id']]->packages . ' ' . $rows['jobs'][$e['job_id']]->package_unit . ' / ' . $rows['jobs'][$e['job_id']]->net_weight . ' ' . $rows['jobs'][$e['job_id']]->net_weight_unit . ' / ' . $rows['jobs'][$e['job_id']]->containers;
		}
		echo '<table class="details">
		<thead>
		<tr>
			<th>Particulars</th>
			<th width="80px">Amount</th>
		</tr>
		</thead>
		<tbody>';
		foreach ($e['details'] as $d) {
			echo '<tr>';
			if ($e['voucher_type_id'] == 2 OR $e['voucher_type_id'] == 3 OR $e['voucher_type_id'] == 4)
				if ($d->category == "General")
					echo '<td class="tiny">' . $d->bill_item_name . '</td>';
				else
					echo '<td class="tiny">' . $d->particulars . '</td>';
			else 
				echo '<td class="tiny">' . $d->party_name . ' / ' . $d->bl_no . ' / ' . $d->packages . ' ' . $d->package_unit . ' / ' . $d->net_weight . ' ' . $d->net_weight_unit . ' / ' . $d->containers . '</td>';
			
			echo '<td class="alignright aligntop">' . number_format($d->amount, 2, '.', '') . '</td>
			</tr>';
		}
		echo '</tbody></table>';
	}

	echo '</td>
	<td class="alignright aligntop">' . ($e['debit'] > 0 ? inr_format(number_format($e['debit'], 2, '.', '')) : '') . '</td>
	<td class="alignright aligntop">' . ($e['credit'] < 0 ? inr_format(number_format(abs($e['credit']), 2, '.', '')) : '') . '</td>
	<td class="alignright aligntop ' . ($total['balance'] >= 0 ? 'green' : 'red') . '">' . inr_format(number_format(abs($total['balance']), 2, '.', '')) . $this->accounting->getDrCr($total['balance']) . '</td>
</tr>';
} ?>

<tr>
	<td></td>
	<td></td>
	<td>Closing Balance</td>
	<td class="alignright"><?php echo inr_format(abs($total['debit'])) ?></td>
	<td class="alignright"><?php echo inr_format(abs($total['credit'])) ?></td>
	<td class="alignright <?php echo ($total['balance'] >= 0 ? 'green' : 'red') ?>"><?php echo inr_format(abs($total['balance'])) . $this->accounting->getDrCr($total['balance']) ?></td>
</tr>
</tbody>
</table>

<?php endif; ?>
</body>
</html>