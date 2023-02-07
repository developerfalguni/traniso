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
					echo anchor($this->_clspath.$this->_class."/preview", '<i class="icon-file-o"></i>', 'class="btn btn-default Preview Popup"') .
						anchor($this->_clspath.$this->_class."/preview/1", '<i class="icon-file-pdf"></i>', 'class="btn btn-default Preview Popup"') .
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
					<ul class="dropdown-menu nav-menu-scroll" id="FilterBillNo">
						<li><a class="red" href="javascript: filter('bill_no:')">Clear Filter</a></li>
						<li><a href="javascript: filter('bill_no:0')">Pending</a></li>
						<li><a href="javascript: filter('bill_no:1')">Attached</a></li>
					</ul>
				</div>
				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-info dropdown-toggle"><?php echo (isset($parsed_search['job_no']) ? '<i class="icon-filter4"></i>' : '') ?> Job No <span class="caret"></span></button>
					<ul class="dropdown-menu nav-menu-scroll" id="FilterJobNo">
						<li><a class="red" href="javascript: filter('job_no:')">Clear Filter</a></li>
						<li><a href="javascript: filter('job_no:0')">Pending</a></li>
						<li><a href="javascript: filter('job_no:1')">Attached</a></li>
					</ul>
				</div>
				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-warning dropdown-toggle"><?php echo (isset($parsed_search['debit']) ? '<i class="icon-filter4"></i>' : '') ?> Debit <span class="caret"></span></button>
					<ul class="dropdown-menu nav-menu-scroll pull-right" id="FilterDebit"></ul>
				</div>
				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-danger dropdown-toggle"><?php echo (isset($parsed_search['credit']) ? '<i class="icon-filter4"></i>' : '') ?> Credit <span class="caret"></span></button>
					<ul class="dropdown-menu nav-menu-scroll pull-right" id="FilterCredit"></ul>
				</div>
				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-success dropdown-toggle"><?php echo (isset($parsed_search['cleared']) ? '<i class="icon-filter4"></i>' : '') ?> Clearing <span class="caret"></span></button>
					<ul class="dropdown-menu nav-menu-scroll" id="FilterCleared">
						<li><a class="red" href="javascript: filter('cleared:')">Clear Filter</a></li>
						<li><a href="javascript: filter('cleared:0')">Pending</a></li>
						<li><a href="javascript: filter('cleared:1')">Realized</a></li>
					</ul>
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
		<th>Voucher No</th>
		<th>Date</th>
		<th>Job No</th>
		<th>Debit Account</th>
		<th>Bill Nos</th>
		<th>Credit Account</th>
		<th>Cheque No</th>
		<th>Cheque Date</th>
		<th>Realization Date</th>
		<th>Amount</th>
	</tr>
	</thead>
	</table>
</div>

<table class="table table-condensed table-striped table-bordered" id="Result">
<thead>
<tr>
	<th>No</th>
	<th>Voucher No</th>
	<th>Date</th>
	<th>Job No</th>
	<th>Debit Account</th>
	<th>Bill Nos</th>
	<th>Credit Account</th>
	<th>Cheque No</th>
	<th>Cheque Date</th>
	<th>Realization Date</th>
	<th>Amount</th>
</tr>
</thead>

<tbody>
<?php 
$filter = array(
	'debit'   => array(),
	'credit'  => array(),
);
$total = 0;
$i = 1;
foreach ($rows as $r) {
	$filter['debit'][$r['debit_name']]   = 1;
	$filter['credit'][$r['credit_name']] = 1;

	$total = bcadd($total, $r['amount'], 2);

	echo '<tr>
	<td class="aligncenter">' . $i++ . '</td>
	<td class="aligncenter">' . anchor('accounting/payment/edit/'.$r['voucher_book_id'].'/'.$r['id'], $r['id2_format'], 'target="_blank"') . '</td>
	<td class="aligncenter">' . $r['date'] . '</td>
	<td>' . $r['job_no'] . '</td>
	<td>' . $r['debit_name'] . '</td>
	<td>' . $r['bill_nos'] . '</td>
	<td>' . $r['credit_name'] . '</td>
	<td>' . $r['cheque_no'] . '</td>
	<td>' . $r['cheque_date'] . '</td>
	<td>' . $r['realization_date'] . '</td>
	<td class="alignright">' . inr_format($r['amount']) . '</td>
</tr>';
} 
?>
</tbody>

<tfoot>
<tr>
	<th colspan="10" class="alignright">Total</th>
	<th class="alignright"><?php echo inr_format($total) ?></th>
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
if (count($filter['debit']) > 0) {
	ksort($filter['debit']);
	echo '$("ul#FilterDebit").append("<li><a class=\"red\" href=\"javascript: filter(\'debit:\')\">Clear Filter</a></li>");';
	foreach ($filter['debit'] as $k => $v) {
		echo '$("ul#FilterDebit").append("<li><a href=\"javascript: filter(\'debit:' . $k . '\')\">' . $k . '</a></li>");';
	}
}
if (count($filter['credit']) > 0) {
	ksort($filter['credit']);
	echo '$("ul#FilterCredit").append("<li><a class=\"red\" href=\"javascript: filter(\'credit:\')\">Clear Filter</a></li>");';
	foreach ($filter['credit'] as $k => $v) {
		echo '$("ul#FilterCredit").append("<li><a href=\"javascript: filter(\'credit:' . $k . '\')\">' . $k . '</a></li>");';
	}
}
?>
});
</script>