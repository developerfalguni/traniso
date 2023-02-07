<?php 
if (isset($pdf)) : 
	$expenses = '';
	$incomes  = '';
	$last_dp  = '';
	$last_group = '';
	$expenses_total = 0;
	$incomes_total = 0;
	$expenses_grand_total = 0;
	$incomes_grand_total = 0;
	foreach($profit_loss as $pl) {
		if ($last_group == '') {
			if ($pl['deemed_positive'])
				$expenses .= '<tr><td class="bold bordertop">' . $pl['group_name'] . '</td><td class="bordertop">&nbsp;</td>';
			else
				$incomes .= '<tr><td class="bold bordertop">' . $pl['group_name'] . '</td><td class="bordertop">&nbsp;</td>';
		}
		else if ($last_group != $pl['group_name']) {
			if ($last_dp == 1) {
				$expenses .= '<td class="alignright bold">' . inr_format($expenses_total) . '</td></tr>';
				$expenses_total = 0;
			}
			else {
				$incomes .= '<td class="alignright bold">' . inr_format($incomes_total) . '</td></tr>';
				$incomes_total = 0;
			}

			if ($pl['deemed_positive'])
				$expenses .= '<tr><td class="bold bordertop">' . $pl['group_name'] . '</td><td class="bordertop">&nbsp;</td>';
			else
				$incomes .= '<tr><td class="bold bordertop">' . $pl['group_name'] . '</td><td class="bordertop">&nbsp;</td>';
			
			$last_dp = $pl['deemed_positive'];
			$last_group = $pl['group_name'];
		}

		if ($pl['deemed_positive']) {
			$amount = $pl['closing'];
			$expenses .= '<td'. ($expenses_total == 0 ? ' class="bordertop"' : '') .'>&nbsp;</td></tr>
	<tr>
		<td><small>' . $pl['name'] . '</small></td>
		<td class="alignright">' . inr_format($amount) . '</td>
	';
			$expenses_total       = bcadd($expenses_total, $amount, 2);
			$expenses_grand_total = bcadd($expenses_grand_total, $amount, 2);
		}
		else {
			$amount = ($pl['closing'] < 0 ? abs($pl['closing']) : -$pl['closing']);
			$incomes .= '<td'. ($incomes_total == 0 ? ' class="bordertop"' : '') .'>&nbsp;</td></tr>
	<tr>
		<td><small>' . $pl['name'] . '</small></td>
		<td class="alignright">' . inr_format($amount) . '</td>
	';
			$incomes_total        = bcadd($incomes_total, $amount, 2);
			$incomes_grand_total  = bcadd($incomes_grand_total, $amount, 2);
		}

		$last_dp = $pl['deemed_positive'];
		$last_group = $pl['group_name'];
	}

	if ($last_dp == 1) {
		$expenses .= '<td class="alignright bold">' . inr_format($expenses_total) . '</td></tr>';
		$expenses_total = 0;
	}
	else {
		$incomes .= '<td class="alignright bold">' . inr_format($incomes_total) . '</td></tr>';
		$incomes_total = 0;
	}
	$netprofit = $incomes_grand_total - $expenses_grand_total;
	if ($netprofit > 0) {
		$expenses .= '<tr><td colspan="2" class="bordertop">Net Profit</td><td class="alignright bold bordertop">' . inr_format($netprofit) . '</td></tr>';
		$expenses_grand_total = bcadd($expenses_grand_total, $netprofit, 2);
	}
	else {
		$incomes  .= '<tr><td colspan="2" class="bordertop">Net Loss</td><td class="alignright bold bordertop">' . inr_format(abs($netprofit)) . '</td></tr>';
		$incomes_grand_total = bcadd($incomes_grand_total, abs($netprofit), 2);
	}

	$expenses .= '<tr><td colspan="2" class="bold bordertop borderbottom">Grand Total</td><td class="alignright bold bordertop borderbottom">' . inr_format($expenses_grand_total) . '</td></tr>';
	$incomes  .= '<tr><td colspan="2" class="bold bordertop borderbottom">Grand Total</td><td class="alignright bold bordertop borderbottom">' . inr_format($incomes_grand_total) . '</td></tr>';

?>
<html>
<head>
<title><?php echo $page_title ?></title>
<style>
body {
	font-family: sans-serif;
}

.Details {
	width: 100%;
	border-spacing: 0;
	border-top: solid 1px #999;
	border-left: solid 1px #999;
}

.Details th, .Details td {
	padding: 2px;
	vertical-align: top;
	border-right: solid 1px #999;
	border-bottom: solid 1px #999;
}
.Details { border-top: solid 1px #999; }
.Details td { 
	font-size: 0.8em;
	border-bottom: none; 
}
.Details th {
	vertical-align: middle;
	font-weight: normal;
	font-size: 0.8em;
	color: #555;
}
.bordertop { border-top: solid 1px #999; }
.borderbottom { border-bottom: solid 1px #999 !important; }
.alignright { text-align: right; }
.bold { font-weight: bold; }
</style>
</head>

<body>
<h2 class="aligncenter"><?php echo $company['name'] ?></h2>
<h4 class="aligncenter"><?php echo $page_title ?></h4>
<table width="100%">
<tr>
	<td width="50%" valign="top">
		<table class="Details">
		<thead>
		<tr>
			<th>Expenses</th>
			<th width="100px">&nbsp;</th>
			<th width="100px">Debit</th>
		</tr>
		</thead>

		<tbody>
		<?php echo $expenses; ?>
		</tbody>
		</table>
	</td>

	<td width="50%" valign="top">
		<table class="Details">
		<thead>
		<tr>
			<th>Incomes</th>
			<th width="100px">&nbsp;</th>
			<th width="100px">Credit</th>
		</tr>
		</thead>

		<tbody>
		<?php echo $incomes; ?>
		</tbody>
		</table>
	</td>
</tr>
</table>
</body>
</html>
<?php 

else :

	$expenses             = '';
	$incomes              = '';
	$last_dp              = '';
	$last_group           = '';
	$expenses_total       = 0;
	$incomes_total        = 0;
	$expenses_grand_total = 0;
	$incomes_grand_total  = 0;

	foreach($profit_loss as $pl) {
		if ($last_group == '') {
			if ($pl['deemed_positive'])
				$expenses .= '<tr><td colspan="2" class="bold">' . $pl['group_name'] . '</td>';
			else
				$incomes .= '<tr><td colspan="2" class="bold">' . $pl['group_name'] . '</td>';
		}
		else if ($last_group != $pl['group_id']) {
			if ($last_dp == 1) {
				$expenses .= '<td class="alignright bold">' . anchor('reports/group/index/'.date('d-m-Y').'/'.$last_group.'/'.$company_id, inr_format($expenses_total), 'target="_blank"') . '</td></tr>';
				$expenses_total = 0;
			}
			else {
				$incomes .= '<td class="alignright bold">' . anchor('reports/group/index/'.date('d-m-Y').'/'.$last_group.'/'.$company_id, inr_format($incomes_total), 'target="_blank"') . '</td></tr>';
				$incomes_total = 0;
			}

			if ($pl['deemed_positive'])
				$expenses .= '<tr><td colspan="2" class="bold">' . $pl['group_name'] . '</td>';
			else
				$incomes .= '<tr><td colspan="2" class="bold">' . $pl['group_name'] . '</td>';
			
			$last_dp = $pl['deemed_positive'];
			$last_group = $pl['group_id'];
		}

		if ($pl['deemed_positive']) {
			$amount = $pl['closing'];
			$expenses .= '<td></td></tr>
	<tr>
		<td>' . $pl['name'] . '</td>
		<td class="alignright">' . anchor('reports/account/index/'.$pl['ledger_id'].'/'.$company_id, inr_format($amount), 'target="_blank"') . '</td>
	';
			$expenses_total       = bcadd($expenses_total, $amount, 2);
			$expenses_grand_total = bcadd($expenses_grand_total, $amount, 2);
		}
		else {
			$amount = ($pl['closing'] < 0 ? abs($pl['closing']) : -$pl['closing']);
			$incomes .= '<td></td></tr>
	<tr>
		<td>' . $pl['name'] . '</td>
		<td class="alignright">' . anchor('reports/account/index/'.$pl['ledger_id'].'/'.$company_id, inr_format($amount), 'target="_blank"') . '</td>
	';
			$incomes_total        = bcadd($incomes_total, $amount, 2);
			$incomes_grand_total  = bcadd($incomes_grand_total, $amount, 2);
		}

		$last_dp = $pl['deemed_positive'];
		$last_group = $pl['group_id'];
	}

	if ($last_dp == 1) {
		$expenses .= '<td class="alignright bold">' . anchor('reports/group/index/'.date('d-m-Y').'/'.$last_group.'/'.$company_id, inr_format($expenses_total), 'target="_blank"') . '</td></tr>';
		$expenses_total = 0;
	}
	else {
		$incomes .= '<td class="alignright bold">' . anchor('reports/group/index/'.date('d-m-Y').'/'.$last_group.'/'.$company_id, inr_format($incomes_total), 'target="_blank"') . '</td></tr>';
		$incomes_total = 0;
	}
	$netprofit = $incomes_grand_total - $expenses_grand_total;
	if ($netprofit > 0) {
		$expenses .= '<tr><td colspan="2">Net Profit</td><td class="alignright bold">' . inr_format($netprofit) . '</td></tr>';
		$expenses_grand_total = bcadd($expenses_grand_total, $netprofit, 2);
	}
	else {
		$incomes  .= '<tr><td colspan="2">Net Loss</td><td class="alignright bold">' . inr_format(abs($netprofit)) . '</td></tr>';
		$incomes_grand_total = bcadd($incomes_grand_total, abs($netprofit), 2);
	}

	$expenses .= '<tr><td colspan="2" class="bold">Grand Total</td><td class="alignright bold">' . inr_format($expenses_grand_total) . '</td></tr>';
	$incomes  .= '<tr><td colspan="2" class="bold">Grand Total</td><td class="alignright bold">' . inr_format($incomes_grand_total) . '</td></tr>';
?>

<div id="FixedToolbar">
	<?php echo form_open($this->uri->uri_string(), 'class="form-search" id="SearchForm"'); ?>
	<input type="hidden" name="search_form" value="1" />
	<table class="table toolbar">
	<tr>
		<td class="nowrap" width="250px">
			<div class="form-group">
				<input type="hidden" name="from_date" value="<?php echo $from_date ?>" id="FromDate" />
				<input type="hidden" name="to_date"   value="<?php echo $to_date ?>" id="ToDate" />
				<div id="ReportRange" class="pull-left" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc">
					<i class="icon-calendar icon-large"></i> <span></span> <b class="caret"></b>
				</div>
			</div>
		</td>

		<td class="nowrap">
			<button type="submit" class="btn btn-primary" id="SearchButton"><i class="fa fa-search"></i> Search</button>
			<div class="btn-group">
			<?php 
			echo anchor($this->_clspath.$this->_class."/preview", '<i class="icon-file-o"></i>', 'class="btn btn-default Preview Popup"') .
				anchor($this->_clspath.$this->_class."/preview/1", '<i class="icon-file-pdf"></i>', 'class="btn btn-default Preview Popup"') .
				anchor($this->_clspath.$this->_class."/excel", '<i class="icon-file-excel"></i>', 'class="btn btn-warning"')
			?>
			</div>
			<div class="btn-group">
			<?php 
			echo anchor($this->_clspath.$this->_class."/preview/0/1", '<i class="icon-file-o"></i>', 'class="btn btn-default Preview Popup"') .
				anchor($this->_clspath.$this->_class."/preview/1/1", '<i class="icon-file-pdf"></i>', 'class="btn btn-default Preview Popup"')
			?>
			</div>
		</td>
	</tr>
	</table>
	</form>
</div>

<script type="text/javascript">
$(document).ready(function() {
	<?php echo 'dateRangePicker({
		years:     ['.$years[0].', '.$years[1].'],
		from_date: \''.convDate($from_date).'\',
		to_date:   \''.convDate($to_date).'\'
	});' ?>
});
</script>

<?php echo start_panel($page_title . ' <small>' . $page_desc . '</small>', '', 'nopadding'); ?>
<div class="row">
	<div class="col-md-6">
		<table class="table table-condensed table-striped">
		<thead>
		<tr>
			<th>Expenses</th>
			<th width="100px"></th>
			<th width="100px">Debit</th>
		</tr>
		</thead>

		<tbody>
		<?php echo $expenses; ?>
		</tbody>
		</table>
	</div>

	<div class="col-md-6">
		<table class="table table-condensed table-striped">
		<thead>
		<tr>
			<th>Incomes</th>
			<th width="100px"></th>
			<th width="100px">Credit</th>
		</tr>
		</thead>

		<tbody>
		<?php echo $incomes; ?>
		</tbody>
		</table>
	</div>
</div>

<?php 
	echo end_panel(); 
endif;
?>