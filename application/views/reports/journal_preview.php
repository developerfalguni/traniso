<html>
<head>
<title><?php echo ""; ?></title>
	<style>
		<?php echo file_get_contents(FCPATH.'assets/css/print.css'); ?>
	</style>
</head>

<style type="text/css">
.extTiny {
	font-size: 7pt;
}
.small {
	font-size: 10pt;
}
.extBig {
	font-size: 16pt;
}
</style>

<body>
<?php $voucher_header = '
<table width="100%">
	<tr>
		<td valign="top" align="center">
		<table>
		<tr>
			<td>
				<span class="bold extBig">' . strtoupper($company_details['name']) . '</span><br />
				<span class="small">' . $company_details['address'] .', '. strtoupper($city_name) . ' - '. $pincode  . '</span>	
			</td>
		</tr>
		</table><br />
			
		<strong>';
			if ($voucher_type == 'Payment') {
				$voucher_header .= $voucher_type . ' Voucher - ' . $voucher_book['name'];
			}
			else {
				$voucher_header .= $voucher_type . ' Voucher';
			}
		$voucher_header .= '</strong>
		</td>
	</tr>
</table>';
?>

<?php if ($sub_voucher) :
	echo $voucher_header; ?>
	<table class="details" width="100%">
		<thead>
			<tr class="tiny" bgcolor="#EBEBEB">
				<th><strong>Sr No</strong></th>
				<th><strong>Date</strong></th>
				<th><strong>Debit</strong></th>
				<th><strong>Name</strong></th>
				<th><strong>Credit</strong></th>
				<th><strong>Name</strong></th>
				<th><strong>Amount</strong></th>
			</tr>
		</thead>

		<tbody>
		<?php
			$total_amount = $sv_row_id3 = 0;
			foreach ($sub_vouchers as $sv_row) {					
				$total_amount += $sv_row['amount'];
				$sv_row_id3 = $sv_row['id3'];
			echo '<tr class="tiny">
				<td>' . str_pad($sv_row['id3'], 3, '0', STR_PAD_LEFT) . '</td>
				<td class="nowrap">' . $sv_row['date'] . '</td>
				<td class="nowrap">' . $sv_row['dr_code'] . '</td>
				<td>' . $sv_row['dr_name'] . '</td>
				<td class="nowrap">' . $sv_row['cr_code'] . '</td>
				<td>' . $sv_row['cr_name'] . '</td>
				<td class="alignright nowrap">' . inr_format($sv_row['amount']) . '</td>
			</tr>';
			}
		?>	
			
		<tr>
			<td colspan="6" class="alignright">Total Amount :</td>
			<td class="alignright nowrap"><img src="<?php echo base_url('/images/rupee.png')  ?>"/><?php echo inr_format($total_amount) ?></td>
		</tr>
		</tbody>
	</table>	
	
<?php endif; 
echo '<div class="page-break"></div>';
?>
	
<?php
$count = 0;
foreach ($sub_voucher_list as $sub_voucher) {
	
	if (($count % 2) == 0) {
		echo '<div class="page-break"></div>';
	}

	echo $voucher_header;
	
	echo '<table class="header" id="PartyInfo">
		<tr>
			<td width="50%"><span class="BoxLabel tiny">VOUCHER NO: </span><span class="bold tiny">' . $sub_voucher['id2_format'] . '</span></td>
			<td><span class="BoxLabel tiny">DATE: </span><span class="bold tiny">' . $sub_voucher['date'] . '</span></td>
		</tr>

		<tr>
			<td><span class="BoxLabel tiny">SUB VOUCHER NO: </span><span class="bold tiny">' . $sub_voucher['id3'] . '</span></td>
			<td><span class="BoxLabel tiny">BILL NO: </span><span class="bold tiny">' . $sub_voucher['invoice_no'] . '</span></td>		
		</tr>';
		
		if ($voucher_type == 'Payment') {
			echo '<tr>
			<td><span class="BoxLabel tiny">CHEQUE NO: </span><span class="bold tiny">' . $sub_voucher['cheque_no'] . '</span></td>
			<td><span class="BoxLabel tiny">CHEQUE DATE: </span><span class="bold tiny">' . $sub_voucher['cheque_date'] . '</span></td>	
		</tr>';
		}
		
		echo '<tr>
			<td><span class="BoxLabel tiny">DEBIT TO: </span><span class="bold tiny">' . $sub_voucher['debit_account'] . '</span></td>
			<td><span class="BoxLabel tiny">CREDIT TO: </span><span class="bold tiny">' . $sub_voucher['credit_account'] . '</span></td>
		</tr>

		<tr>
			<td colspan="2"><span class="BoxLabel tiny">NARRATION: </span><span class="bold tiny">' . $sub_voucher['narration'] . '</span></td>
		</tr>
	</table><br />';

	if($voucher_details != null) {

	echo '<table class="details" id="Details">
		<tr class="tiny" bgcolor="#EBEBEB">
			<th width="24px"><strong>No</strong></th>
			<th width="120px"><strong>BL/SB</strong></th>
			<th align="left"><strong>Name</strong></th>
			<th><strong>Amount</strong></th>
		</tr>';
		
		$i = 1;
		foreach ($voucher_details as $vd) {
		echo '<tr class="tiny">
			<td>'. $i++ .'</td>
			<td>'. $vd['bl_no'] .'</td>
			<td>'. strtoupper($vd['bill_item_name']) .'</td>
			<td class="alignright">'. inr_format($vd['amount']) .'</td>
		</tr>';
		}

	echo '</table><br/>';
	}
		
	echo '<hr>
	<table width="100%" class="noborder">
	<tr>
		<td class="small"><b>Rs. '. numberToWords($sub_voucher['amount']) . '</b></td>
		<td class="alignright"><img src="'. base_url('/images/rupee.png') . '"/><strong>'. inr_format($sub_voucher['amount']) . '</strong></td>
	</tr>
	</table>
	<hr>

	<table width="100%" class="noborder">';
		if ($voucher_type == 'Journal') {
			echo '<tr class="tiny BoxLabel">
				<td>ACCOUNTANT</td>
				<td class="alignright">AUTHORISED BY</td>
			</tr>';
		}
		else{
			echo '<tr class="tiny BoxLabel">
				<td class="col-md-4 ">PREPARED BY</td>
				<td class="col-md-4 aligncenter">ACCOUNTANT</td>
				<td class="col-md-4 alignright">AUTHORISED BY</td>
				<td class="col-md-4 alignright">RECEIVER\'S SIGNATURE</td>
			</tr>';
		}
	'</table>';
	$count++;	
}
?>

</body>
</html>