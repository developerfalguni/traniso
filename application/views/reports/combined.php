<?php 
foreach ($rows as $company_id => $ledgers) :
	foreach ($ledgers as $ledger) :
		$total   = array('debit' => 0, 'credit' => 0, 'balance' => 0);
		echo '
<table class="table table-condensed table-bordered">
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
';
		foreach ($ledger as $i => $e) :
		if ($i == 0) {
			$total['balance'] += $e['amount'];

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
		$amount = ($e['debit'] + $e['credit']);
		$total['debit']   += $e['debit'];
		$total['credit']  += $e['credit'];
		$total['balance'] += $amount;

		$filters[$e['ledger2_id']] = $e['ledger2'];

		echo '<tr>
	<td class="aligncenter aligntop tiny">' . $e['date'] . '</td>
	<td class="aligntop tiny">' . $e['no']. '-' . $e['id3'] . '</td>
	<td class="aligntop tiny">' . $this->accounting->getToBy($amount) . ' ' . $e['ledger2'] . 
		((strlen($e['cheque_no_date']) == 0 OR $e['cheque_no_date'] == '0 / 00-00-0000') ? '' : '&nbsp;&nbsp;&nbsp; Chq No: ' . $e['cheque_no_date'] . '</span>');

		echo '</td>
	<td class="alignright aligntop">' . ($e['debit'] > 0 ? inr_format(number_format($e['debit'], 2, '.', '')) : '') . '</td>
	<td class="alignright aligntop">' . ($e['credit'] < 0 ? inr_format(number_format(abs($e['credit']), 2, '.', '')) : '') . '</td>
	<td class="alignright aligntop ' . ($total['balance'] >= 0 ? 'green' : 'red') . '">' . inr_format(number_format(abs($total['balance']), 2, '.', '')) . $this->accounting->getDrCr($total['balance']) . '</td>
</tr>';
		endforeach;
		echo '
<tr>
	<td></td>
	<td></td>
	<td class="big">Closing Balance</td>
	<td class="big alignright">' . inr_format(abs($total['debit'])) . '</td>
	<td class="big alignright">' . inr_format(abs($total['credit'])) . '</td>
	<td class="big alignright ' . ($total['balance'] >= 0 ? 'green' : 'red') . '">' . inr_format(abs($total['balance'])) . $this->accounting->getDrCr($total['balance']) . '</td>
</tr>
</tbody>
</table>
';
	endforeach;
endforeach;
?>
