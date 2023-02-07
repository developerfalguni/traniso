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
						<?php echo anchor($this->_clspath.$this->_class."/preview/0", '<i class="icon-file-o"></i>', 'class="btn btn-default Popup"').
						anchor($this->_clspath.$this->_class."/preview/1", '<i class="icon-file-pdf"></i>', 'class="btn btn-default Popup"').
						anchor($this->_clspath.$this->_class."/excel", '<i class="icon-file-excel"></i>', 'class="btn btn-warning Popup"'); ?>
					</div>	
				</div>
			</div>
		</td>
		
		<td>
			<div class="nowrap">
				<button type="button" class="btn btn-group btn-primary" onclick="javascript: clearSearch()"><i class="icon-align-justify"></i></button>

				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-info dropdown-toggle"><?php echo (isset($parsed_search['party']) ? '<i class="icon-filter4"></i>' : '') ?> Party <span class="caret"></span></button>
					<ul class="dropdown-menu nav-menu-scroll pull-right" id="FilterParty"></ul>
				</div>
				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-success dropdown-toggle"><?php echo (isset($parsed_search['status']) ? '<i class="icon-filter4"></i>' : '') ?> Status <span class="caret"></span></button>
					<ul class="dropdown-menu nav-menu-scroll pull-right" id="FilterStatus">
						<li><a class="red" href="javascript: filter('status:')">Clear Filter</a></li>
						<li><a href="javascript: filter('status:Pending')">Pending</a></li>
						<li><a href="javascript: filter('status:Paid')">Paid</a></li>
					</ul>
				</div>
				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-warning dropdown-toggle"><?php echo (isset($parsed_search['processed_date']) ? '<i class="icon-filter4"></i>' : '') ?> Processed On <span class="caret"></span></button>
					<ul class="dropdown-menu nav-menu-scroll pull-right" id="FilterProcessedDate"></ul>
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
		<th>Type</th>
		<th>Bill No</th>
		<th>Date</th>
		<th>Party Bill No</th>
		<th>Party Name</th>
		<th>PAN No</th>
		<th>Trips</th>
		<th>Cheque No</th>
		<th>Cheque Date</th>
		<th>Advance</th>
		<th>Amount</th>
		<th>Net Amount</th>
		<th>Processed On</th>
	</tr>
	</thead>
	</table>
</div>

<table class="table table-condensed table-striped table-bordered" id="Result">
<thead>
<tr>
	<th>No</th>
	<th>Type</th>
	<th>Bill No</th>
	<th>Date</th>
	<th>Party Bill No</th>
	<th>Party Name</th>
	<th>PAN No</th>
	<th>Trips</th>
	<th>Cheque No</th>
	<th>Cheque Date</th>
	<th>Advance</th>
	<th>Amount</th>
	<th>Net Amount</th>
	<th>Processed On</th>
</tr>
</thead>

<tbody>
<?php 
$total = array(
	'advance' => 0,
	'amount'  => 0,
	'net'     => 0,
);
$filter = array(
	'party'          => array(),
	'processed_date' => array(),
);
$i = 1;
foreach ($list as $r) {
	$total['advance'] = bcadd($total['advance'], bcadd($r['fuel'], $r['advance'], 2), 2);
	$total['amount']  = bcadd($total['amount'], $r['amount']);
	$total['net']     = bcadd($total['net'], bcsub($r['amount'], bcadd($r['fuel'], $r['advance'], 2), 2), 2);

	$filter['party'][$r['party_name']]              = 1;
	$filter['processed_date'][$r['processed_date']] = 1;

	echo '<tr>
	<td class="aligncenter">' . $i++ . '</td>
	<td>' . $r['type'] . '</td>
	<td>' . $r['rishi_bill_no'] . '</td>
	<td>' . $r['date'] . '</td>
	<td>' . anchor('container/container_inward/edit/'.$r['id'], $r['bill_no'], 'target="_blank"') . '</td>
	<td>' . $r['party_name'] . '</td>
	<td>' . $r['pan_no'] . '</td>
	<td class="aligncenter">' . $r['trips'] . '</td>
	<td>' . $r['cheque_no'] . '</td>
	<td>' . $r['cheque_date'] . '</td>
	<td class="alignright">' . bcadd($r['fuel'], $r['advance'], 2) . '</td>
	<td class="alignright">' . $r['amount'] . '</td>
	<td class="alignright">' . bcsub($r['amount'], bcadd($r['fuel'], $r['advance'], 2), 2) . '</td>
	<td>' . $r['processed_date'] . '</td>
</tr>';
}
?>
</tbody>

<tfoot>
<tr>
	<th class="alignright" colspan="10">Total</th>
	<th class="alignright"><?php echo inr_format($total['advance']) ?></th>
	<th class="alignright"><?php echo inr_format($total['amount']) ?></th>
	<th class="alignright"><?php echo inr_format($total['net']) ?></th>
	<th></th>
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
if (count($filter['party']) > 0) {
	ksort($filter['party']);
	echo '$("#FilterParty").append("<li><a class=\"red\" href=\"javascript: filter(\'party:\')\">Clear Filter</a></li>");';
	foreach ($filter['party'] as $k => $v) {
		echo '$("#FilterParty").append("<li><a href=\"javascript: filter(\'party:' . $k . '\')\">' . $k . '</a></li>");';
	}
}
if (count($filter['processed_date']) > 0) {
	ksort($filter['processed_date']);
	echo '$("ul#FilterProcessedDate").append("<li><a class=\"red\" href=\"javascript: filter(\'processed_date:\')\">Clear Filter</a></li>");';
	foreach ($filter['processed_date'] as $k => $v) {
		echo '$("ul#FilterProcessedDate").append("<li><a href=\"javascript: filter(\'processed_date:' . $k . '\')\">' . $k . '</a></li>");';
	}
}
?>
});
</script>