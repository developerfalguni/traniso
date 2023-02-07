

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
			<div class="input-group">
				<input type="text" class="form-control form-control-sm search-query input-medium" name="search" value="<?php echo (isset($search) ? $search : '') ?>" id="Search" />
				<button type="submit" class="btn btn-primary" id="SearchButton"><i class="fa fa-search"></i> Search</button>
			</div>
			<a href="#modal-search" class="btn btn-primary" data-toggle="modal"><i class="icon-search-plus"></i></a>
			<?php echo anchor($this->_clspath.$this->_class."/excel", '<i class="icon-file-excel"></i>', 'class="btn btn-warning"') ?>
		</td>
		
		<td>
			<div class="nowrap">
				<button type="button" class="btn btn-group btn-primary" onclick="javascript: clearSearch()"><i class="icon-align-justify"></i></button>

				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-success dropdown-toggle"><?php echo (isset($parsed_search['type']) ? '<i class="icon-filter4"></i>' : '') ?> Type <span class="caret"></span></button>
					<ul class="dropdown-menu pull-right nav-menu-scroll" id="FilterType">
						<li><a href="javascript: filter('type:Bulk')">Bulk</a></li>
						<li><a href="javascript: filter('type:Container')">Container</a></li>
						<li><a class="red" href="javascript: filter('type:')">Clear Filter</a></li>
					</ul>
				</div>

				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-info dropdown-toggle"><?php echo (isset($parsed_search['vessel']) ? '<i class="icon-filter4"></i>' : '') ?> Vessel <span class="caret"></span></button>
					<ul class="dropdown-menu nav-menu-scroll" id="FilterVessel"></ul>
				</div>

				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-warning dropdown-toggle"><?php echo (isset($parsed_search['port']) ? '<i class="icon-filter4"></i>' : '') ?> Port <span class="caret"></span></button>
					<ul class="dropdown-menu nav-menu-scroll" id="FilterPort"></ul>
				</div>

				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-danger dropdown-toggle"><?php echo (isset($parsed_search['category']) ? '<i class="icon-filter4"></i>' : '') ?> Category <span class="caret"></span></button>
					<ul class="dropdown-menu nav-menu-scroll" id="FilterCategory"></ul>
				</div>

				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-success dropdown-toggle"><?php echo (isset($parsed_search['product']) ? '<i class="icon-filter4"></i>' : '') ?> Product <span class="caret"></span></button>
					<ul class="dropdown-menu nav-menu-scroll" id="FilterProduct"></ul>
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
		<th>Cargo Type</th>
		<th>Vessel</th>
		<th>Port</th>
		<th>Product</th>
		<th>Pieces</th>
		<th>CBM</th>
		<th class="Container">C.20</th>
		<th class="Container">C.40</th>
		<th>Reimbersment</th>
		<th>Invoice</th>
		<th>Expense</th>
		<th>Total</th>
		<th class="Average hide">Average</th>
	</tr>
	</thead>
	</table>
</div>

<table class="table table-condensed table-striped table-bordered" id="Jobs">
<thead>
<tr>
	<th width="48px">No</th>
	<th>Cargo Type</th>
	<th>Vessel</th>
	<th>Port</th>
	<th>Product</th>
	<th width="100px">Pieces</th>
	<th width="100px">CBM</th>
	<th width="60px" class="Container">C.20</th>
	<th width="60px" class="Container">C.40</th>
	<th width="120px">Reimbersment</th>
	<th width="120px">Invoice</th>
	<th width="120px">Expense</th>
	<th width="120px">Total</th>
	<th width="120px" class="Average hide">Average</th>
</tr>
</thead>

<tbody>
<?php 
$i = 1;
$prev_vessel = 0;
$total = array(
	'packages'     => 0, 
	'cbm'          => 0, 
	'container_20' => 0,
	'container_40' => 0,
	'reimbersment' => 0,
	'invoice'      => 0,
	'expense'      => 0,
	'total'        => 0,
);
$filter = array(
	'vessel'   => array(),
	'category' => array(),
	'product'  => array(),
	'port'     => array(),
);
$group_row = '';
foreach ($rows as $r) {
	$total['packages']     += $r['packages'];
	$total['cbm']          += $r['cbm'];
	$total['container_20'] += $r['container_20'];
	$total['container_40'] += $r['container_40'];
	$total['reimbersment'] += $r['reimbersment'];
	$total['invoice']      += $r['invoice'];
	$total['expense']      += $r['expense'];
	$row_total              = bcadd(bcadd($r['reimbersment'], $r['invoice'], 2), $r['expense'], 2);
	$average                = abs(round($row_total / $r['cbm'], 2));
	$total['total']         = bcadd($total['total'], $row_total, 2);

	$filter['vessel'][$r['vessel_voyage']] = 1;
	$filter['category'][$r['category']]    = 1;
	$filter['product'][$r['product_name']] = 1;
	$filter['port'][$r['indian_port']]     = 1;

	echo '<tr>
	<td class="aligncenter">' . $i++ . '</td>
	<td>' . $r['cargo_type'] . '</td>
	<td>' . $r['vessel_voyage'] . '</td>
	<td>' . $r['indian_port'] . '</td>
	<td>' . $r['product_name'] . '</td>
	<td class="alignright">' . $r['packages'] . '</td>
	<td class="alignright">' . $r['cbm'] . '</td>
	<td class="alignright Container">' . $r['container_20'] . '</td>
	<td class="alignright Container">' . $r['container_40'] . '</td>
	<td class="alignright bold">' . anchor($this->_clspath.'account/index/'.$r['reimbersment_ledger_id'], 
		inr_format(abs($r['reimbersment'])) . ' ' . $this->accounting->getDrCr($r['reimbersment']) . '</span>', 'class="' . ($r['reimbersment'] >= 0 ? 'red' : 'green') . '" target="_blank"') . '</td>
	<td class="alignright bold">' . anchor($this->_clspath.'account/index/'.$r['invoice_ledger_id'], 
		inr_format(abs($r['invoice'])) . ' ' . $this->accounting->getDrCr($r['invoice']), 'class="' . ($r['invoice'] >= 0 ? 'red' : 'green') . '" target="_blank"') . '</td>
	<td class="alignright bold">' . anchor($this->_clspath.'account/index/'.$r['expense_ledger_id'], 
		inr_format(abs($r['expense'])) . ' ' . $this->accounting->getDrCr($r['expense']), 'class="' . ($r['expense'] >= 0 ? 'red' : 'green') . '" target="_blank"') . '</td>
	<td class="alignright bold"><span class="' . ($row_total >= 0 ? 'red' : 'green') . '">' . inr_format(abs($row_total)) . ' ' . $this->accounting->getDrCr($row_total) . '</span></td>
	<td class="alignright Average hide">' . $average . '</td>
</tr>';
} 
?>
</tbody>

<tfoot>
<tr>
	<th class="alignright" colspan="5">Total</th>
	<th class="alignright"><?php echo $total['packages'] ?></th>
	<th class="alignright"><?php echo $total['cbm'] ?></th>
	<th class="alignright Container"><?php echo $total['container_20'] ?></th>
	<th class="alignright Container"><?php echo $total['container_40'] ?></th>
	<th class="alignright"><?php echo '<span class="' . ($total['reimbersment'] >= 0 ? '' : 'green') . '">' . inr_format(abs($total['reimbersment'])) . ' ' . $this->accounting->getDrCr($total['reimbersment']) . '</span>' ?></th>
	<th class="alignright"><?php echo '<span class="' . ($total['invoice'] >= 0 ? '' : 'green') . '">' . inr_format(abs($total['invoice'])) . ' ' . $this->accounting->getDrCr($total['invoice']) . '</span>' ?></th>
	<th class="alignright"><?php echo '<span class="' . ($total['expense'] >= 0 ? '' : 'green') . '">' . inr_format(abs($total['expense'])) . ' ' . $this->accounting->getDrCr($total['expense']) . '</span>' ?></th>
	<th class="alignright"><?php echo '<span class="' . ($total['total'] >= 0 ? '' : 'green') . '">' . inr_format(abs($total['total'])) . ' ' . $this->accounting->getDrCr($total['total']) . '</span>' ?></th>
	<th class="alignright Average hide"><?php echo abs(round($total['total'] / $total['cbm'], 2)) ?></th>
</tr>
</tfoot>
</table>

<script type="text/javascript">
var details = 1;
function toggleDetails() {
	if(details) {
		$("tr.Details").removeClass("hide");
		details = 0;

		$("#Jobs").find('thead tr').children().each(function(i, e) {
		    $($("#FixedHeader").find('thead tr').children()[i]).width($(e).width());
		});
		$("#FixedHeader").width($("#Jobs").width());

	} else {
		$("tr.Details").addClass("hide");
		details = 1;
	}
}


$(document).ready(function() {
	<?php echo 'dateRangePicker({
		years:     ['.$years[0].', '.$years[1].'],
		from_date: \''.convDate($from_date).'\',
		to_date:   \''.convDate($to_date).'\'
	});' ?>

	<?php if (isset($parsed_search['type']) && $parsed_search['type'] == 'Bulk') 
		echo '
			$(".Container").addClass('hide');
		';
	?>

	listener.simple_combo('alt h', function(e) {
		$('.Average').toggleClass('hide');
	});

	$("#Jobs").find('thead tr').children().each(function(i, e) {
	    $($("#FixedHeader").find('thead tr').children()[i]).width($(e).width());
	});
	$("#FixedHeader").width($("#Jobs").width());

	if (!($.browser == "msie" && $.browser.version < 7)) {
        var target = "div#FixedToolbar"; //, top = $(target).offset().top - parseFloat($(target).css("margin-top").replace(/auto/, 0));
        $(window).scroll(function(event) {
            $(target).css({
			 	left: ($("#Jobs").offset().left - $(window).scrollLeft()) + 'px'
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
if (count($filter['vessel']) > 0) {
	ksort($filter['vessel']);
	foreach ($filter['vessel'] as $k => $v) {
		echo '$("ul#FilterVessel").append("<li><a href=\"javascript: filter(\'vessel:' . $k . '\')\">' . $k . '</a></li>");';
	}
	echo '$("ul#FilterVessel").append("<li><a class=\"red\" href=\"javascript: filter(\'vessel:\')\">Clear Filter</a></li>");';
}
if (count($filter['category']) > 0) {
	ksort($filter['category']);
	foreach ($filter['category'] as $k => $v) {
		echo '$("ul#FilterCategory").append("<li><a href=\"javascript: filter(\'category:' . $k . '\')\">' . $k . '</a></li>");';
	}
	echo '$("ul#FilterCategory").append("<li><a class=\"red\" href=\"javascript: filter(\'category:\')\">Clear Filter</a></li>");';
}
if (count($filter['product']) > 0) {
	ksort($filter['product']);
	foreach ($filter['product'] as $k => $v) {
		echo '$("ul#FilterProduct").append("<li><a href=\"javascript: filter(\'product:' . $k . '\')\">' . $k . '</a></li>");';
	}
	echo '$("ul#FilterProduct").append("<li><a class=\"red\" href=\"javascript: filter(\'product:\')\">Clear Filter</a></li>");';
}
if (count($filter['port']) > 0) {
	ksort($filter['port']);
	foreach ($filter['port'] as $k => $v) {
		echo '$("ul#FilterPort").append("<li><a href=\"javascript: filter(\'port:' . $k . '\')\">' . $k . '</a></li>");';
	}
	echo '$("ul#FilterPort").append("<li><a class=\"red\" href=\"javascript: filter(\'port:\')\">Clear Filter</a></li>");';
}
?>
});
</script>