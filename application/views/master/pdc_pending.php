<style>

.DayTotal { background-color: #eee !important; }
.MonthTotal { background-color: #ddd !important; }
.BankTotal { background-color: #ccc !important; }
.markRealized { background-color: #1BDB1A !important; }

</style>

<div id="modal-search" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><i class="icon-times"></i></button>
				<h3>Advanced Search</h3>
			</div>
			<div class="modal-body">
				<table class="table table-condensed table-striped">
				<tbody>
				<?php 
				$search_tags = [];
				foreach($search_fields as $f => $n) {
					$search_tags[] = '<li><a href="#" class="SearchTags" data-field="' . $f . ':">' . humanize($f) . '</a></li>';
					echo '<tr>
					<td class="alignright alignmiddle">' . humanize($f) . '</td>
					<td><input type="text" class="form-control form-control-sm AdvancedSearch" name="' . $f . '" value="' . (isset($parsed_search[$f]) ? $parsed_search[$f] : '') . '" /></td>
				</tr>';
				} ?>
				</tbody>
				</table>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" onclick="javascript: combineSearch()"><i class="fa fa-search"></i> Search</button>
			</div>
		</div>
	</div>
</div>


<div id="modal-excel" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
		<?php echo form_open_multipart($this->_clspath.$this->_class.'/excel', 'class="form-horizontal"'); ?>
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><i class="icon-times"></i></button>
				<h3>Import from Excel</h3>
			</div>
			<div class="modal-body">
				<fieldset>
					<div class="form-group">
						<label class="control-label">Excel File</label>
						<input type="file" name="userfile" value="" size="40" />
					</div>

					<div class="row">
						<div class="col-md-9">
							<div class="form-group">
								<label class="control-label">Sheet Name</label>
								<input type="text" class="form-control form-control-sm big" name="sheet_name" value="Sheet1" />
							</div>
						</div>

						<div class="col-md-3">
							<div class="form-group">
								<label class="control-label">Starting Row</label>
								<input type="text" class="form-control form-control-sm" name="starting_row" value="1" />
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-3">
							<div class="form-group">
								<label class="control-label">Account No</label>
								<input type="text" class="form-control form-control-sm" name="account_col" value="A" />
							</div>
						</div>

						<div class="col-md-3">
							<div class="form-group">
								<label class="control-label">Realization Date</label>
								<input type="text" class="form-control form-control-sm" name="realization_col" value="B" />
							</div>
						</div>

						<div class="col-md-3">
							<div class="form-group">
								<label class="control-label">Favor</label>
								<input type="text" class="form-control form-control-sm" name="favor_col" value="C" />
							</div>
						</div>

						<div class="col-md-3">
							<div class="form-group">
								<label class="control-label">Cheque No</label>
								<input type="text" class="form-control form-control-sm" name="cheque_no_col" value="E" />
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-3">
							<div class="form-group">
								<label class="control-label">Amount</label>
								<input type="text" class="form-control form-control-sm" name="amount_col" value="H" />
							</div>
						</div>
					</div>
				</fieldset>
			</div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-success" id="ExcelImport">Start Importing</button>
			</div>
			</form>
		</div>
	</div>
</div>


<div id="FixedToolbar">
	<table class="table toolbar">
	<tr>
		<td class="nowrap">
			<?php echo form_open($this->uri->uri_string(), 'class="form-search" id="SearchForm"'); ?>
			<input type="hidden" name="search_form" value="1" />
			<div class="row">
				<div class="col-md-8">
					<div class="input-group">
						<div class="input-group-btn">
							<button type="button" data-toggle="dropdown" class="btn btn-primary dropdown-toggle"><span class="caret"></span></button>
							<ul class="dropdown-menu nav-menu-scroll">
								<?php echo join("\n", $search_tags) ?>
							</ul>
						</div>
						<input type="text" class="form-control form-control-sm search-query" name="search" value="<?php echo (isset($search) ? $search : '') ?>" id="Search" />
						<span class="input-group-btn">
							<button type="submit" class="btn btn-primary" id="SearchButton"><i class="icon-search icon-white"></i> Search</button>
				      	</span>
					</div>
				</div>
				<div class="col-md-4">
					<a href="#modal-search" class="btn btn-primary" data-toggle="modal"><i class="icon-search-plus"></i></a>
					<div class="btn-group">
					<?php echo anchor($this->_clspath.$this->_class."/excel", '<i class="icon-file-excel"></i>', 'class="btn btn-warning"')	?>
					</div>
				</div>
			</div>
			</form>
		</td>

		<td>
			<div class="nowrap">
				<button type="button" class="btn btn-group btn-primary" onclick="javascript: clearSearch()"><i class="icon-align-justify"></i></button>

				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-info dropdown-toggle"><?php echo (isset($parsed_search['bank']) ? '<i class="icon-filter4"></i>' : '') ?> Bank <span class="caret"></span></button>
					<ul class="dropdown-menu nav-menu-scroll" id="FilterBank"></ul>
				</div>
				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-warning dropdown-toggle"><?php echo (isset($parsed_search['cheque']) ? '<i class="icon-filter4"></i>' : '') ?> Cheque No <span class="caret"></span></button>
					<ul class="dropdown-menu nav-menu-scroll" id="FilterCheque"></ul>
				</div>
				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-danger dropdown-toggle"><?php echo (isset($parsed_search['favor']) ? '<i class="icon-filter4"></i>' : '') ?> Favor <span class="caret"></span></button>
					<ul class="dropdown-menu pull-right nav-menu-scroll" id="FilterFavor"></ul>
				</div>
			</div>
		</td>

		<td><button type="button" class="btn btn-success" id="Update">Update</button></td>
	</tr>
	</table>

	<table class="table table-condensed table-striped table-bordered hide" id="FixedHeader">
	<thead>
	<tr>
		<th>No</th>
		<th width="120px">Cheque No</th>
		<th>Favoring</th>
		<th width="120px">Amount</th>
		<th width="120px">Realization Date</th>
	</tr>
	</thead>
	</table>
</div>

<?php echo form_open($this->uri->uri_string(), 'id="MainForm"'); ?>
<table class="table table-condensed table-bordered" id="Result">
<thead>
<tr>
	<th>No</th>
	<th width="120px">Cheque No</th>
	<th>Favoring</th>
	<th width="120px">Amount</th>
	<th width="120px">Realization Date</th>
</tr>
</thead>

<tbody>
<?php 
$i           = 1;
$grand_total = 0;
$month_total = 0;
$bank_total  = 0;
$day_total   = 0;
$prevbank    = 0;
$prevmonth   = 0;
$prevdate    = 0;
$filter      = array(
	'bank'   => array(),
	'cheque' => array(),
	'favor'  => array()
);
foreach ($rows as $key => $r) {
	$filter['bank'][$r['bank']]        = 1;
	$filter['cheque'][$r['cheque_no']] = 1;
	$filter['favor'][$r['favor']]      = 1;

	$bg_color = '';
	if ($r['realization_date'] != '00-00-0000')
		$bg_color = 'markRealized';
	
	if ($prevbank == 0) {
		echo '<tr>
		<td colspan="5" class="blueDark bold">' . $r['bank'] . '</td>
	</tr>';
	}

	if ($prevdate == 0) {
		echo '<tr>
		<td colspan="5" class="bold">' . $r['cheque_date'] . '</td>
	</tr>';
	}

	if ($prevbank != 0 && $prevbank != $r['bank_ledger_id']) {
		echo '<tr>
		<td colspan="3" class="alignright bold DayTotal">Day Total (' . $prevdate . ') </td>
		<td class="alignright bold DayTotal">' . inr_format($day_total) . '</td>
		<td class="DayTotal"></td>
	</tr>';

		$month_total += $day_total;
		echo '<tr>
		<td colspan="3" class="alignright bold MonthTotal">Month Total (' . $prevmonth . ') </td>
		<td class="alignright bold MonthTotal">' . inr_format($month_total) . '</td>
		<td class="MonthTotal"></td>
	</tr>';
		$month_total = 0;
		$bank_total += $day_total;
		$day_total   = 0;

		echo '<tr>
		<td colspan="3" class="alignright bold BankTotal">Bank Total</td>
		<td class="alignright bold BankTotal">' . inr_format($bank_total) . '</td>
		<td class="BankTotal"></td>
	</tr>

	<tr>
		<td colspan="5">&nbsp;</td>
	</tr>

	<tr>
		<td colspan="8" class="blueDark bold">' . $r['bank'] . '</td>
	</tr>';
		$grand_total += $bank_total;
		$bank_total = 0;
	}

	if ($prevmonth != 0 && $prevmonth != $r['year_month']) {
		echo '<tr>
		<td colspan="3" class="alignright bold DayTotal">Day Total (' . $prevdate . ') </td>
		<td class="alignright bold DayTotal">' . inr_format($day_total) . '</td>
		<td class="DayTotal"></td>
	</tr>';

		$month_total += $day_total;
		echo '<tr>
		<td colspan="3" class="alignright bold MonthTotal">Month Total (' . $prevmonth . ') </td>
		<td class="alignright bold MonthTotal">' . inr_format($month_total) . '</td>
		<td class="MonthTotal"></td>
	</tr>';
		$month_total = 0;
		$bank_total  += $day_total;
		$day_total    = 0;
		$prevdate = $r['cheque_date'];
	}

	if ($prevdate != 0 && $prevdate != $r['cheque_date']) {
		echo '<tr>
		<td colspan="3" class="alignright bold DayTotal">Day Total (' . $prevdate . ') </td>
		<td class="alignright bold DayTotal">' . inr_format($day_total) . '</td>
		<td class="DayTotal"></td>
	</tr>';
		$month_total += $day_total;
		$bank_total  += $day_total;
		$day_total    = 0;
	}

	if ($r['realization_date'] == '00-00-0000')
		$day_total += $r['amount'];

	echo '<tr>
		<td class="aligncenter '. $bg_color.'">' . $i++ . '</td>
		<td class="aligncenter '. $bg_color.'">' . anchor('master/issued_cheque/edit/'.$r['id'], $r['cheque_no'], 'target="_blank"') . '</td>
		<td class="'. $bg_color.'">' . $r['favor'] . '</td>
		<td class="alignright '. $bg_color.'">' . inr_format($r['amount']) . '</td>
		<td class="aligncenter '. $bg_color.'">' . ($r['realization_date'] == '00-00-0000' ? '<input type="text" class="DateTime" name="realization_date[' . $r['id'] . ']" value="" />' : $r['realization_date']) . '</td>
		</tr>';

	$prevbank  = $r['bank_ledger_id'];
	$prevmonth = $r['year_month'];
	$prevdate  = $r['cheque_date'];
}
$month_total += $day_total;
$bank_total  += $day_total;
$grand_total += $bank_total;
echo '<tr>
		<td colspan="3" class="alignright bold DayTotal">Day Total (' . $prevdate . ') </td>
		<td class="alignright bold DayTotal">' . inr_format($day_total) . '</td>
		<td class="DayTotal"></td>
	</tr>

	<tr>
		<td colspan="3" class="alignright bold MonthTotal">Month Total (' . $prevmonth . ') </td>
		<td class="alignright bold MonthTotal">' . inr_format($month_total) . '</td>
		<td class="MonthTotal"></td>
	</tr>

	<tr>
		<td colspan="3" class="alignright bold BankTotal">Bank Total</td>
		<td class="alignright bold BankTotal">' . inr_format($bank_total) . '</td>
		<td class="BankTotal"></td>
	</tr>

	<tr>
		<td colspan="3" class="alignright bold">Grand Total</td>
		<td class="alignright bold">' . inr_format($grand_total) . '</td>
		<td></td>
	</tr>';
?>
</tbody>
</table>
</form>

<script>

$(document).ready(function() {
	$("#Result").find('thead tr').children().each(function(i, e) {
	    $($("#FixedHeader").find('thead tr').children()[i]).width($(e).width());
	});
	$("#FixedHeader").width($("#Result").width());

	if (!($.browser == "msie" && $.browser.version < 7)) {
        var target = "div#FixedToolbar"; //, top = $(target).offset().top - parseFloat($(target).css("margin-top").replace(/auto/, 0));
        $(window).scroll(function(event) {
            $(target).css({
			 	left: ($("#Result").offset().left - $(window).scrollLeft()) + 'px'
			});
            if ($(this).scrollTop() > 25) {
                $(target).addClass("fixedTop show");
                $("table#FixedHeader").removeClass("hide");
            } else {
                $(target).removeClass("fixedTop show");
                $("table#FixedHeader").addClass("hide");
            }
        });
    }<?php 
if (count($filter['bank']) > 0) {
	ksort($filter['bank']);
	foreach ($filter['bank'] as $k => $v) {
		echo '$("ul#FilterBank").append("<li><a href=\"javascript: filter(\'bank:' . $k . '\')\">' . $k . '</a></li>");';
	}
	echo '$("ul#FilterBank").append("<li><a class=\"red\" href=\"javascript: filter(\'bank:\')\">Clear Filter</a></li>");';
}
if (count($filter['cheque']) > 0) {
	ksort($filter['cheque']);
	foreach ($filter['cheque'] as $k => $v) {
		echo '$("ul#FilterCheque").append("<li><a href=\"javascript: filter(\'cheque:' . $k . '\')\">' . $k . '</a></li>");';
	}
	echo '$("ul#FilterCheque").append("<li><a class=\"red\" href=\"javascript: filter(\'cheque:\')\">Clear Filter</a></li>");';
}
if (count($filter['favor']) > 0) {
	ksort($filter['favor']);
	foreach ($filter['favor'] as $k => $v) {
		echo '$("ul#FilterFavor").append("<li><a href=\"javascript: filter(\'favor:' . $k . '\')\">' . $k . '</a></li>");';
	}
	echo '$("ul#FilterFavor").append("<li><a class=\"red\" href=\"javascript: filter(\'favor:\')\">Clear Filter</a></li>");';
}
?>
});
</script>