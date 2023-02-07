

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
			<div class="btn-group">
				<button type="submit" class="btn btn-primary" id="SearchButton"><i class="fa fa-search"></i></button>
				<a href="#modal-search" class="btn btn-primary" data-toggle="modal"><i class="icon-search-plus"></i></a>
			</div>
		</td>
		
		<td>
			<div class="nowrap">
				<button type="button" class="btn btn-group btn-primary" onclick="javascript: clearSearch()"><i class="icon-align-justify"></i></button>
				
				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-info dropdown-toggle"><?php echo (isset($parsed_search['party']) ? '<i class="icon-filter4"></i>' : '') ?> Party <span class="caret"></span></button>
					<ul class="dropdown-menu pull-right nav-menu-scroll" id="FilterParty"></ul>
				</div>
				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-success dropdown-toggle"><?php echo (isset($parsed_search['vessel']) ? '<i class="icon-filter4"></i>' : '') ?> Vessel <span class="caret"></span></button>
					<ul class="dropdown-menu pull-right nav-menu-scroll" id="FilterVessel"></ul>
				</div>
				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-warning dropdown-toggle"><?php echo (isset($parsed_search['category']) ? '<i class="icon-filter4"></i>' : '') ?> Category <span class="caret"></span></button>
					<ul class="dropdown-menu nav-menu-scroll" id="FilterCategory"></ul>
				</div>
				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-warning dropdown-toggle"><?php echo (isset($parsed_search['product']) ? '<i class="icon-filter4"></i>' : '') ?> Product <span class="caret"></span></button>
					<ul class="dropdown-menu pull-right nav-menu-scroll" id="FilterProduct"></ul>
				</div>
				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-danger dropdown-toggle"><?php echo (isset($parsed_search['type']) ? '<i class="icon-filter4"></i>' : '') ?> Type <span class="caret"></span></button>
					<ul class="dropdown-menu" id="FilterType">
						<li><a href="javascript: filter('type: Bulk')">Bulk</a></li>
						<li><a href="javascript: filter('type: Container')">Container</a></li>
						<li><a class="red" href="javascript: filter('type:')">Clear Filter</a></li>
					</ul>
				</div>
				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-info dropdown-toggle"><?php echo (isset($parsed_search['port']) ? '<i class="icon-filter4"></i>' : '') ?> Port <span class="caret"></span></button>
					<ul class="dropdown-menu pull-right" id="FilterPort"></ul>
				</div>
				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-success dropdown-toggle"><?php echo (isset($parsed_search['cha']) ? '<i class="icon-filter4"></i>' : '') ?> CHA <span class="caret"></span></button>
					<ul class="dropdown-menu pull-right" id="FilterCHA"></ul>
				</div>
				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-warning dropdown-toggle"><?php echo (isset($parsed_search['appraisement']) ? '<i class="icon-filter4"></i>' : '') ?> Appr <span class="caret"></span></button>
					<ul class="dropdown-menu pull-right" id="FilterAppraisment">
						<li><a href="javascript: filter('appraisement: OFFICER')">OFFICER</a></li>
						<li><a href="javascript: filter('appraisement: SYSTEM')">SYSTEM</a></li>
						<li><a class="red" href="javascript: filter('appraisement:')">Clear Filter</a></li>
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
		<th rowspan="2">No</th>
		<th rowspan="2">Type</th>
		<th rowspan="2">Vessel</th>
		<th rowspan="2">Product</th>
		<th rowspan="2">Port</th>
		<th colspan="3" class="aligncenter">CBM</th>
		<th colspan="3" class="aligncenter">Cont. 20</th>
		<th colspan="3" class="aligncenter">Cont. 40</th>
	</tr>

	<tr>
		<th>RMS</th>
		<th>Officer</th>
		<th>Total</th>
		<th>RMS</th>
		<th>Officer</th>
		<th>Total</th>
		<th>RMS</th>
		<th>Officer</th>
		<th>Total</th>
	</tr>
	</thead>
	</table>
</div>

<table class="table table-condensed table-striped table-bordered" id="Jobs">
<thead>
<tr>
	<th rowspan="2">No</th>
	<th rowspan="2">Type</th>
	<th rowspan="2">Vessel</th>
	<th rowspan="2">Product</th>
	<th rowspan="2">Port</th>
	<th colspan="3" class="aligncenter">CBM</th>
	<th colspan="3" class="aligncenter">Cont. 20</th>
	<th colspan="3" class="aligncenter">Cont. 40</th>
</tr>

<tr>
	<th>RMS</th>
	<th>Officer</th>
	<th>Total</th>
	<th>RMS</th>
	<th>Officer</th>
	<th>Total</th>
	<th>RMS</th>
	<th>Officer</th>
	<th>Total</th>
</tr>
</thead>

<tbody>
<?php 
$total = array(
	'system_net_weight'  => 0,
	'officer_net_weight' => 0,
	'total_net_weight'   => 0,
	'system_c20'         => 0,
	'officer_c20'        => 0,
	'total_c20'          => 0,
	'system_c40'         => 0,
	'officer_c40'        => 0,
	'total_c40'          => 0,
);
$filter = array(
	'party'    => array(),
	'category' => array(),
	'product'  => array(),
	'vessel'   => array(),
	'port'     => array(),
	'cha'      => array(),
);
$i = 1;
foreach ($icegate as $r) {
	$total['system_net_weight']  = bcadd($total['system_net_weight'], $r['system_net_weight'], 2);
	$total['officer_net_weight'] = bcadd($total['officer_net_weight'], $r['officer_net_weight'], 2);
	$total['system_c20']        += $r['system_container_20'];
	$total['officer_c20']       += $r['officer_container_20'];
	$total['system_c40']        += $r['system_container_40'];
	$total['officer_c40']       += $r['officer_container_40'];

	$filter['party'][$r['party_name']]   = 1;
	$filter['category'][$r['category']]  = 1;
	$filter['product'][$r['product']]    = 1;
	$filter['vessel'][$r['vessel_name']] = 1;
	$filter['port'][$r['indian_port']]   = 1;
	$filter['cha' ][$r['cha_no']]        = $r['cha_name'];

	echo '<tr>
	<td class="aligncenter">' . $i++ . '</td>
	<td>' . $r['cargo_type'] . '</td>
	<td>' . $r['vessel_name'] . '</td>
	<td>' . $r['product'] . '</td>
	<td>' . $r['indian_port'] . '</td>
	<td class="alignright">' . $r['system_net_weight'] . '</td>
	<td class="alignright">' . $r['officer_net_weight'] . '</td>
	<td class="alignright bold">' . ($r['system_net_weight']+$r['officer_net_weight']) . '</td>
	<td class="aligncenter">' . $r['system_container_20'] . '</td>
	<td class="aligncenter">' . $r['officer_container_20'] . '</td>
	<td class="aligncenter bold">' . ($r['system_container_20']+$r['officer_container_20']) . '</td>
	<td class="aligncenter">' . $r['system_container_40'] . '</td>
	<td class="aligncenter">' . $r['officer_container_40'] . '</td>
	<td class="aligncenter bold">' . ($r['system_container_40']+$r['officer_container_40']) . '</td>
</tr>
';
} ?>
</tbody>

<tfoot>
<tr>
	<th colspan="5" class="alignright">Total</th>
	<th class="alignright"><?php echo $total['system_net_weight'] ?></th>
	<th class="alignright"><?php echo $total['officer_net_weight'] ?></th>
	<th class="alignright"><?php echo ($total['system_net_weight']+$total['officer_net_weight']) ?></th>
	<th class="alignright"><?php echo $total['system_c20'] ?></th>
	<th class="alignright"><?php echo $total['officer_c20'] ?></th>
	<th class="alignright"><?php echo ($total['system_c20']+$total['officer_c20']) ?></th>
	<th class="alignright"><?php echo $total['system_c40'] ?></th>
	<th class="alignright"><?php echo $total['officer_c40'] ?></th>
	<th class="alignright"><?php echo ($total['system_c40']+$total['officer_c40']) ?></th>
</tr>
</tfoot>
</table>

<script type="text/javascript">

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
if (count($filter['party']) > 0) {
	ksort($filter['party']);
	foreach ($filter['party'] as $k => $v) {
		echo '$("ul#FilterParty").append("<li><a href=\"javascript: filter(\'party:' . $k . '\')\">' . $k . '</a></li>");';
	}
	echo '$("ul#FilterParty").append("<li><a class=\"red\" href=\"javascript: filter(\'party:\')\">Clear Filter</a></li>");';
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
if (count($filter['vessel']) > 0) {
	ksort($filter['vessel']);
	foreach ($filter['vessel'] as $k => $v) {
		echo '$("ul#FilterVessel").append("<li><a href=\"javascript: filter(\'vessel:' . $k . '\')\">' . $k . '</a></li>");';
	}
	echo '$("ul#FilterVessel").append("<li><a class=\"red\" href=\"javascript: filter(\'vessel:\')\">Clear Filter</a></li>");';
}
if (count($filter['port']) > 0) {
	ksort($filter['port']);
	foreach ($filter['port'] as $k => $v) {
		echo '$("ul#FilterPort").append("<li><a href=\"javascript: filter(\'port:' . $k . '\')\">' . $k . '</a></li>");';
	}
	echo '$("ul#FilterPort").append("<li><a class=\"red\" href=\"javascript: filter(\'port:\')\">Clear Filter</a></li>");';
}
if (count($filter['cha']) > 0) {
	ksort($filter['cha']);
	foreach ($filter['cha'] as $k => $v) {
		echo '$("ul#FilterCHA").append("<li><a href=\"javascript: filter(\'cha: ' . $k . '\')\">' . $v . '</a></li>");';
	}
	echo '$("ul#FilterCHA").append("<li><a class=\"red\" href=\"javascript: filter(\'cha:\')\">Clear Filter</a></li>");';
}
?>
});
</script>