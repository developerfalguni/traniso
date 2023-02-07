<style>
.markChanged { background-color: #1BDB1A !important; }
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
			<input type="hidden" class="form-control form-control-sm search-query" name="search" value="<?php echo (isset($search) ? $search : '') ?>" id="Search" />
			<button type="submit" class="btn btn-primary" id="SearchButton"><i class="fa fa-search"></i> Search</button>
			<a href="#modal-search" class="btn btn-primary" data-toggle="modal"><i class="icon-search-plus"></i></a>
			<?php echo anchor($this->_clspath.$this->_class."/excel", 'Excel', 'class="btn btn-warning Popup"') ?>
		</td>
		
		<td>
			<div class="nowrap">
				<button type="button" class="btn btn-group btn-primary" onclick="javascript: clearSearch()"><i class="icon-align-justify"></i></button>

				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-success dropdown-toggle"><?php echo (isset($parsed_search['party']) ? '<i class="icon-filter4"></i>' : '') ?> Party <span class="caret"></span></button>
					<ul class="dropdown-menu nav-menu-scroll pull-right" id="FilterParty"></ul>
				</div>
				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-info dropdown-toggle"><?php echo (isset($parsed_search['category']) ? '<i class="icon-filter4"></i>' : '') ?> Category <span class="caret"></span></button>
					<ul class="dropdown-menu pull-right" id="FilterCategory"></ul>
				</div>
				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-warning dropdown-toggle"><?php echo (isset($parsed_search['product']) ? '<i class="icon-filter4"></i>' : '') ?> Product <span class="caret"></span></button>
					<ul class="dropdown-menu pull-right" id="FilterProduct"></ul>
				</div>
				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-danger dropdown-toggle"><?php echo (isset($parsed_search['pol']) ? '<i class="icon-filter4"></i>' : '') ?> POL <span class="caret"></span></button>
					<ul class="dropdown-menu pull-right" id="FilterPOL"></ul>
				</div>
				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-success dropdown-toggle"><?php echo (isset($parsed_search['pod']) ? '<i class="icon-filter4"></i>' : '') ?> POD <span class="caret"></span></button>
					<ul class="dropdown-menu nav-menu-scroll pull-right" id="FilterPOD"></ul>
				</div>
				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-info dropdown-toggle"><?php echo (isset($parsed_search['line']) ? '<i class="icon-filter4"></i>' : '') ?> Shipping Line <span class="caret"></span></button>
					<ul class="dropdown-menu nav-menu-scroll pull-right" id="FilterLine"></ul>
				</div>
				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-warning dropdown-toggle"><?php echo (isset($parsed_search['stuffing']) ? '<i class="icon-filter4"></i>' : '') ?> Stuffing <span class="caret"></span></button>
					<ul class="dropdown-menu pull-right" id="FilterStuffing">
						<li><a href="javascript: filter('stuffing: CFS')">CFS</a></li>
						<li><a href="javascript: filter('stuffing: Factory')">Factory</a></li>
						<li><a class="red" href="javascript: filter('stuffing:')">Clear Filter</a></li>
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
		<th>Party Name</th>
		<th>Commodity</th>
		<th>C.20</th>
		<th>C.40</th>
		<th>Shipping Line</th>
		<th>POL</th>
		<th>POD</th>
	</tr>
	</thead>
	</table>
</div>

<table class="table table-condensed table-bordered" id="Jobs">
<thead>
<tr>
	<th>No</th>
	<th>Party Name</th>
	<th>Commodity</th>
	<th>C.20</th>
	<th>C.40</th>
	<th>Shipping Line</th>
	<th>POL</th>
	<th>POD</th>
</tr>
</thead>

<tbody>
<?php 
$total = array('container_20' => 0, 'container_40' => 0);
$filter = array(
	'category' => array(),
	'product'  => array(),
	'party'    => array(),
	'line'     => array(),
	'pol'      => array(),
	'pod'      => array()
);
$i = 1;
foreach ($rows as $r) {
	$total['container_20'] += $r['container_20'];
	$total['container_40'] += $r['container_40'];

	$filter['category'][$r['category']]    = 1;
	$filter['party'][$r['party_name']]     = 1;
	$filter['product'][$r['product_name']] = 1;
	$filter['line'][$r['shipping_line']]   = 1;
	$filter['pol'][$r['pol']]              = 1;
	$filter['pod'][$r['pod']]              = 1;

	echo '<tr>
	<td class="aligncenter">' . $i++ . '</td>
	<td>' . $r['party_name'] . '</td>
	<td>' . $r['product_name'] . '</td>
	<td class="aligncenter">' . $r['container_20'] . '</td>
	<td class="aligncenter">' . $r['container_40'] . '</td>
	<td>' . $r['shipping_line'] . '</td>
	<td>' . $r['pol'] . '</td>
	<td>' . $r['pod'] . '</td>
</tr>';
} ?>
</tbody>

<tfoot>
<tr>
	<th colspan="3" class="alignright">Total</th>
	<th class="alignright"><?php echo $total['container_20'] ?></th>
	<th class="alignright"><?php echo $total['container_40'] ?></th>
	<th colspan="3"></th>
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
if (count($filter['category']) > 0) {
	ksort($filter['category']);
	foreach ($filter['category'] as $k => $v) {
		echo '$("ul#FilterCategory").append("<li><a href=\"javascript: filter(\'category:' . $k . '\')\">' . $k . '</a></li>");';
	}
	echo '$("ul#FilterCategory").append("<li><a class=\"red\" href=\"javascript: filter(\'category:\')\">Clear Filter</a></li>");';
}
if (count($filter['party']) > 0) {
	ksort($filter['party']);
	foreach ($filter['party'] as $k => $v) {
		echo '$("ul#FilterParty").append("<li><a href=\"javascript: filter(\'party:' . $k . '\')\">' . $k . '</a></li>");';
	}
	echo '$("ul#FilterParty").append("<li><a class=\"red\" href=\"javascript: filter(\'party:\')\">Clear Filter</a></li>");';
}
if (count($filter['product']) > 0) {
	ksort($filter['product']);
	foreach ($filter['product'] as $k => $v) {
		echo '$("ul#FilterProduct").append("<li><a href=\"javascript: filter(\'product: ' . str_replace('"', '&quot;', $k) . '\')\">' . str_replace('"', '&quot;', $k) . '</a></li>");';
	}
	echo '$("ul#FilterProduct").append("<li><a class=\"red\" href=\"javascript: filter(\'product:\')\">Clear Filter</a></li>");';
}
if (count($filter['line']) > 0) {
	ksort($filter['line']);
	foreach ($filter['line'] as $k => $v) {
		echo '$("ul#FilterLine").append("<li><a href=\"javascript: filter(\'line:' . $k . '\')\">' . $k . '</a></li>");';
	}
	echo '$("ul#FilterLine").append("<li><a class=\"red\" href=\"javascript: filter(\'line:\')\">Clear Filter</a></li>");';
}
if (count($filter['pol']) > 0) {
	ksort($filter['pol']);
	foreach ($filter['pol'] as $k => $v) {
		echo '$("ul#FilterPOL").append("<li><a href=\"javascript: filter(\'pol:' . $k . '\')\">' . $k . '</a></li>");';
	}
	echo '$("ul#FilterPOL").append("<li><a class=\"red\" href=\"javascript: filter(\'pol:\')\">Clear Filter</a></li>");';
}
if (count($filter['pod']) > 0) {
	ksort($filter['pod']);
	foreach ($filter['pod'] as $k => $v) {
		echo '$("ul#FilterPOD").append("<li><a href=\"javascript: filter(\'pod:' . $k . '\')\">' . $k . '</a></li>");';
	}
	echo '$("ul#FilterPOD").append("<li><a class=\"red\" href=\"javascript: filter(\'pod:\')\">Clear Filter</a></li>");';
}
?>
});
</script>