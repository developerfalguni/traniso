<div id="modal-search" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><i class="icon-times"></i></button>
				<h3>Advanced Search</h3>
			</div>
			<div class="modal-body form-horizontal">
				<table class="table table-condensed table-striped">
				<tbody>
				<?php foreach($search_fields as $f => $n) {
				echo "<tr>
					<td class=\"alignright alignmiddle\">" . humanize($f) . " :</td>
					<td><input type=\"text\" class=\"form-control AdvancedSearch\" name=\"$f\" value=\"" . (isset($parsed_search[$f]) ? $parsed_search[$f] : '') . "\" /></td>
				</tr>";
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

<div id="FixedToolbar">
	<?php echo form_open($this->uri->uri_string(), 'class="form-search" id="SearchForm"'); ?>
	<input type="hidden" name="search_form" value="1" />
	<table class="table toolbar">
	<tr>
		<td class="nowrap" width="250px">
			<input type="hidden" name="from_date" value="<?php echo $from_date ?>" id="FromDate" />
			<input type="hidden" name="to_date"   value="<?php echo $to_date ?>" id="ToDate" />
			<div id="ReportRange" class="pull-left" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc">
				<i class="icon-calendar icon-large"></i> <span></span> <b class="caret"></b>
			</div>
		</td>

		<td class="nowrap">
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
					<?php 
					echo //anchor($this->_clspath.$this->_class."/preview", '<i class="icon-file-o"></i>', 'class="btn btn-default Preview Popup"') .
						//anchor($this->_clspath.$this->_class."/preview/1", '<i class="icon-file-pdf"></i>', 'class="btn btn-default Popup"') .
						anchor($this->_clspath.$this->_class."/excel", '<i class="icon-file-excel"></i>', 'class="btn btn-warning"')
					?>
					</div>
				</div>
			</div>
		</td>

		<td>
			<div class="nowrap">
				<button type="button" class="btn btn-group btn-primary" onclick="javascript: clearSearch()"><i class="icon-align-justify"></i></button>

				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-success dropdown-toggle"><?php echo (isset($parsed_search['bill_no']) ? '<i class="icon-filter4"></i>' : '') ?> Bill No <span class="caret"></span></button>
					<ul class="dropdown-menu nav-menu-scroll" id="FilterBillNo"></ul>
				</div>
				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-info dropdown-toggle"><?php echo (isset($parsed_search['job_no']) ? '<i class="icon-filter4"></i>' : '') ?> Job No <span class="caret"></span></button>
					<ul class="dropdown-menu nav-menu-scroll" id="FilterJobNo"></ul>
				</div>
				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-warning dropdown-toggle"><?php echo (isset($parsed_search['debit']) ? '<i class="icon-filter4"></i>' : '') ?> Debit <span class="caret"></span></button>
					<ul class="dropdown-menu nav-menu-scroll" id="FilterDebit"></ul>
				</div>
				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-danger dropdown-toggle"><?php echo (isset($parsed_search['credit']) ? '<i class="icon-filter4"></i>' : '') ?> Credit <span class="caret"></span></button>
					<ul class="dropdown-menu nav-menu-scroll" id="FilterCredit"></ul>
				</div>
			</div>	
		</td>
	</tr>
	</table>
	</form>

	<table class="table table-condensed table-striped table-bordered hide" id="FixedHeader">
	<thead>
	<tr>
		<th>No</th>
		<th>Bill No</th>
		<th>Date</th>
		<th>Job No</th>
		<th>Debit Account</th>
		<th>Credit Account</th>
		<th>Amount</th>
	</tr>
	</thead>
	</table>
</div>

<table class="table table-condensed table-striped table-bordered" id="Result">
<thead>
<tr>
	<th>No</th>
	<th>Bill No</th>
	<th>Date</th>
	<th>Job No</th>
	<th>Debit Account</th>
	<th>Credit Account</th>
	<th>Amount</th>
</tr>
</thead>

<tbody>
<?php 
$filter = array(
	'bill_no' => array(),
	'job_no'  => array(),
	'debit'   => array(),
	'credit'  => array(),
);
$total = $total_advance = 0;
$i = 1;
foreach ($rows as $r) {
	$filter['bill_no'][$r['id2_format']] = 1;
	$filter['job_no'][$r['job_no']]      = 1;
	$filter['debit'][$r['debit_name']]   = 1;
	$filter['credit'][$r['credit_name']] = 1;

	$total         = bcadd($total, $r['amount'], 2);
	$total_advance = bcadd($total_advance, $r['advance'], 2);

	echo '<tr>
	<td class="aligncenter">' . $i++ . '</td>
	<td class="aligncenter">' . anchor('accounting/invoice/edit/'.$r['voucher_book_id'].'/'.$r['id'], $r['id2_format'], 'target="_blank"') . '</td>
	<td class="aligncenter">' . $r['date'] . '</td>
	<td class="aligncenter">' . ($r['job_id'] > 0 ? anchor(strtolower($r['type']).'/jobs/edit/'.$r['job_id'].'/'.$r['cargo_type'], $r['job_no'], 'target="_blank"') : '') . '</td>
	<td>' . $r['debit_name'] . '</td>
	<td>' . $r['credit_name'] . '</td>
	<td class="alignright">' . inr_format($r['amount'] - $r['advance']) . '</td>
</tr>';
} 
?>
</tbody>

<tfoot>
<tr>
	<th colspan="5" class="alignright">Total</th>
	<th class="alignright"><?php echo inr_format($total - $total_advance) ?></th>
</tr>
</tfoot>
</table>

<script>

$(document).ready(function() {
	<?php echo 'dateRangePicker({
		years:     ['.$years[0].', '.$years[1].'],
		from_date: \''.convDate($from_date).'\',
		to_date:   \''.convDate($to_date).'\'
	});' ?>

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
    }

<?php 
if (count($filter['bill_no']) > 0) {
	ksort($filter['bill_no']);
	foreach ($filter['bill_no'] as $k => $v) {
		echo '$("ul#FilterBillNo").append("<li><a href=\"javascript: filter(\'bill_no:' . $k . '\')\">' . $k . '</a></li>");';
	}
	echo '$("ul#FilterBillNo").append("<li><a class=\"red\" href=\"javascript: filter(\'bill_no:\')\">Clear Filter</a></li>");';
}
if (count($filter['job_no']) > 0) {
	ksort($filter['job_no']);
	foreach ($filter['job_no'] as $k => $v) {
		echo '$("ul#FilterJobNo").append("<li><a href=\"javascript: filter(\'job_no:' . $k . '\')\">' . $k . '</a></li>");';
	}
	echo '$("ul#FilterJobNo").append("<li><a class=\"red\" href=\"javascript: filter(\'job_no:\')\">Clear Filter</a></li>");';
}
if (count($filter['debit']) > 0) {
	ksort($filter['debit']);
	foreach ($filter['debit'] as $k => $v) {
		echo '$("ul#FilterDebit").append("<li><a href=\"javascript: filter(\'debit:' . $k . '\')\">' . $k . '</a></li>");';
	}
	echo '$("ul#FilterDebit").append("<li><a class=\"red\" href=\"javascript: filter(\'debit:\')\">Clear Filter</a></li>");';
}
if (count($filter['credit']) > 0) {
	ksort($filter['credit']);
	foreach ($filter['credit'] as $k => $v) {
		echo '$("ul#FilterCredit").append("<li><a href=\"javascript: filter(\'credit:' . $k . '\')\">' . $k . '</a></li>");';
	}
	echo '$("ul#FilterCredit").append("<li><a class=\"red\" href=\"javascript: filter(\'credit:\')\">Clear Filter</a></li>");';
}
?>
});
</script>