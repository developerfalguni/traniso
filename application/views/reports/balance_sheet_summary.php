<?php 
if (isset($pdf)) : 
	$liabilities = '';
	$assets = '';
	$total = array('liabilities' => 0, 'assets' => 0);
	foreach($balance_sheet as $side => $bl) {
		foreach ($bl as $gid => $groups) {
			foreach ($groups as $key => $value) {
				if (! is_array($value)) {
					$$side .= '<tr>
			<td class="bordertop">' . $key . '</td>
			<td class="alignright bordertop">' . inr_format($value) . '</td>
		</tr>';
					$total[$side] += $value;
				}
			}
		}
	}

	$liabilities .= '<tr>
		<td class="bold bordertop borderbottom">Grand Total</td>
		<td class="alignright bold bordertop borderbottom">' . inr_format($total['liabilities']) . '</td>
	</tr>';

	$assets .= '<tr>
		<td class="bold bordertop borderbottom">Grand Total</td>
		<td class="alignright bold bordertop borderbottom">' . inr_format($total['assets']) . '</td>
	</tr>';
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
	<h3><?php echo $company['name'] ?></h3>
	<p><strong><?php echo $page_title; ?></strong> <?php echo $page_desc; ?></p>
<table width="100%">
<tr>
	<td width="50%" valign="top">
		<table class="Details">
		<thead>
		<tr>
			<th>Liabilities</th>
			<th width="100px">Credit</th>
		</tr>
		</thead>

		<tbody>
		<?php echo $liabilities; ?>
		</tbody>
		</table>
	</td>

	<td width="50%" valign="top">
		<table class="Details">
		<thead>
		<tr>
			<th>Assets</th>
			<th width="100px">Debit</th>
		</tr>
		</thead>

		<tbody>
		<?php echo $assets; ?>
		</tbody>
		</table>
	</td>
</tr>
</table>
</body>
</html>
<?php 

else :

	$liabilities = '';
	$assets = '';
	$total = array('liabilities' => 0, 'assets' => 0);
	foreach($balance_sheet as $side => $bl) {
		foreach ($bl as $gid => $groups) {
			foreach ($groups as $key => $value) {
				if (is_array($value)) {
					foreach ($value as $ledger_id => $ledger) {
						$$side .= '<tr class="Details hide">
		<td class="tiny">' . $ledger['name'] . '</td>
		<td class="alignright">' . anchor('reports/account/index/'.$ledger_id, inr_format($ledger['amount']), 'target="_blank"') . '</td>
		<td>&nbsp;</td>
	</tr>';
					}
				}
				else {
					$$side .= '<tr>
		<td class="bold">' . $key . '</td>
		<td>&nbsp;</td>
		<td class="alignright bold">' . inr_format($value) . '</td>
	</tr>';
					$total[$side] += $value;
				}
			}
		}
	}
	$liabilities .= '<tr>
		<td class="bold">Grand Total</td>
		<td>&nbsp;</td>
		<td class="alignright bold">' . inr_format($total['liabilities']) . '</td>
	</tr>';

	$assets .= '<tr>
		<td class="bold">Grand Total</td>
		<td>&nbsp;</td>
		<td class="alignright bold">' . inr_format($total['assets']) . '</td>
	</tr>';
?>

<div id="FixedToolbar">
	<?php echo form_open($this->uri->uri_string(), 'class="form-search" id="SearchForm"'); ?>
	<input type="hidden" name="search_form" value="1" />
	<table class="table toolbar">
	<tr>
		<td class="col-md-3">
			<div class="input-group date DatePicker">
				<span class="input-group-addon">Upto <i class="icon-calendar"></i></span>
				<input type="text" class="form-control form-control-sm AutoDate" name="upto" value="<?php echo ($upto != '00-00-0000' ? $upto : '') ?>" />
			</div>
		</td>

		<td class="nowrap">
			<button type="submit" class="btn btn-primary" id="SearchButton"><i class="fa fa-search"></i> Search</button>
			<a href="#" class="btn btn-info" id="ShowHideDetails"><i class="icon-eye-slash"></i> Show / Hide Details</a>
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

<?php echo start_panel($page_title . ' <small>' . $page_desc . '</small>', '', 'nopadding'); ?>

<div class="row">
	<div class="col-md-6">
		<table class="table table-condensed table-striped">
		<thead>
		<tr>
			<th>Liabilities</th>
			<th width="100px"></th>
			<th width="100px">Credit</th>
		</tr>
		</thead>

		<tbody>
		<?php echo $liabilities; ?>
		</tbody>
		</table>
	</div>

	<div class="col-md-6">
		<table class="table table-condensed table-striped">
		<thead>
		<tr>
			<th>Assets</th>
			<th width="100px"></th>
			<th width="100px">Debit</th>
		</tr>
		</thead>

		<tbody>
		<?php echo $assets; ?>
		</tbody>
		</table>
	</div>
</div>

<?php echo end_panel(); ?>

<script type="text/javascript">
var details = 1;
$(document).ready(function() {
	$('#ShowHideDetails').on('click', function() {
		$('tr.Details').toggleClass('hide');
	});
});
</script>

<?php endif; ?>