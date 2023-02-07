<style>
.WhiteJob { background-color: #ffffff; }
.GreyJob { background-color: #eeeeee; }
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
							<button type="submit" class="btn btn-primary" id="SearchButton"><i class="fa fa-search"></i> Search</button>
							<a href="#modal-search" class="btn btn-primary" data-toggle="modal"><i class="icon-search-plus"></i></a>
						</span>
					</div>
				</div>
				<div class="col-md-4">
					<button type="button" class="btn btn-info" id="HideZero"><i class="icon-eye-slash"></i></button>
					<div class="btn-group">
						<?php 
						echo anchor($this->_clspath.$this->_class."/preview", '<i class="icon-file"></i>', 'class="btn btn-default Popup"') .
							anchor($this->_clspath.$this->_class."/preview/1", '<i class="icon-file-pdf"></i>', 'class="btn btn-default Popup"') .
							anchor($this->_clspath.$this->_class."/excel", 'Excel', 'class="btn btn-warning"');
						?>
					</div>
				</div>
			</div>
		</td>
		
		<td>
			<div class="nowrap">
				<button type="button" class="btn btn-group btn-primary" onclick="javascript: clearSearch()"><i class="icon-align-justify"></i></button>
				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-success dropdown-toggle"><?php echo (isset($parsed_search['shipment']) ? '<i class="icon-filter4"></i>' : '') ?> Shipment <span class="caret"></span></button>
					<ul class="dropdown-menu pull-right nav-menu-scroll" id="FilterShipment">
						<li><a href="javascript: filter('shipment:Import')">Import</a></li>
						<li><a href="javascript: filter('shipment:Export')">Export</a></li>
						<li><a class="red" href="javascript: filter('shipment:')">Clear Filter</a></li>
					</ul>
				</div>

				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-info dropdown-toggle"><?php echo (isset($parsed_search['party']) ? '<i class="icon-filter4"></i>' : '') ?> Party <span class="caret"></span></button>
					<ul class="dropdown-menu pull-right nav-menu-scroll" id="FilterParty">
						<li><a class="red" href="javascript: filter('party:')">Clear Filter</a></li>
					</ul>
				</div>

				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-warning dropdown-toggle"><?php echo (isset($parsed_search['vessel']) ? '<i class="icon-filter4"></i>' : '') ?> Vessel <span class="caret"></span></button>
					<ul class="dropdown-menu pull-right nav-menu-scroll" id="FilterVessel">
						<li><a class="red" href="javascript: filter('vessel:')">Clear Filter</a></li>
					</ul>
				</div>

				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-danger dropdown-toggle"><?php echo (isset($parsed_search['type']) ? '<i class="icon-filter4"></i>' : '') ?> Type <span class="caret"></span></button>
					<ul class="dropdown-menu pull-right" id="FilterType">
						<li><a class="red" href="javascript: filter('type:')">Clear Filter</a></li>
						<li><a href="javascript: filter('type: Bulk')">Bulk</a></li>
						<li><a href="javascript: filter('type: Container')">Container</a></li>
					</ul>
				</div>

				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-success dropdown-toggle"><?php echo (isset($parsed_search['category']) ? '<i class="icon-filter4"></i>' : '') ?> Category <span class="caret"></span></button>
					<ul class="dropdown-menu pull-right nav-menu-scroll" id="FilterCategory">
						<li><a class="red" href="javascript: filter('category:')">Clear Filter</a></li>
					</ul>
				</div>

				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-info dropdown-toggle"><?php echo (isset($parsed_search['product']) ? '<i class="icon-filter4"></i>' : '') ?> Product <span class="caret"></span></button>
					<ul class="dropdown-menu pull-right nav-menu-scroll" id="FilterProduct">
						<li><a class="red" href="javascript: filter('product:')">Clear Filter</a></li>
					</ul>
				</div>

				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-warning dropdown-toggle"><?php echo (isset($parsed_search['cha']) ? '<i class="icon-filter4"></i>' : '') ?> CHA <span class="caret"></span></button>
					<ul class="dropdown-menu pull-right nav-menu-scroll" id="FilterCHA">
						<li><a class="red" href="javascript: filter('cha:')">Clear Filter</a></li>
					</ul>
				</div>
			</div>
		</td>
	</tr>
	</table>
	</form>

	<table class="table table-condensed table-bordered tiny hide" id="FixedHeader">
	<thead>
	<tr>
		<th>No</th>
		<th>Party / Vessel / (BL - SB No)</th>
		<?php foreach ($jobs['charges'] as $c) {
			echo '<th>'.$c.'</th>';
		} ?>
		<th>Total</th>
	</tr>
	</thead>
	</table>
</div>

<table class="table table-condensed table-bordered tiny" id="Jobs">
<thead>
<tr class="DontHideMe">
	<th>No</th>
	<th>Party / Vessel / (BL - SB No)</th>
	<?php 
	$col_total = array();
	foreach ($jobs['charges'] as $c) {
		echo '<th>'.$c.'</th>';
		$col_total[$c] = array('charged' => 0, 'paid' => 0, 'due' => 0);
	} 
	?>
	<th>Total</th>
</tr>
</thead>

<tbody>
<?php 
$total = 0;
$filter = array(
	'party'    => array(),
	'vessel'   => array(),
	'category' => array(),
	'product'  => array(),
	'cha'      => array(),
);
$i = 1;
$toggle_class = 'GreyJob';
foreach ($jobs['jobs'] as $key => $r) {

	$toggle_class = ($toggle_class == 'WhiteJob' ? 'GreyJob' : 'WhiteJob');

	$filter['party'][$r['party']]          = 1;
	$filter['vessel'][$r['vessel']]        = 1;
	$filter['category'][$r['category']]    = 1;
	$filter['product'][$r['product_name']] = 1;
	$filter['cha'][$r['cha_name']]         = 1;
	
	echo '<tr class="Row_' . $i . '">
	<td rowspan="3" class="aligncenter ' . $toggle_class . '">' . $i . '</td>';

	if ($r['type'] == 'Import')
		echo '<td rowspan="3" class="' . $toggle_class . '">' . $r['party'] . '<br />' . anchor(underscore($r['type']).'/jobs/edit/'.$key, $r['job_no'], 'target="_blank"') . '<br /><span class="orange">' . $r['vessel'] . '</span><br />' . anchor(underscore($r['type']).'/'.underscore($r['type']).'_detail/edit/'.$key, $r['bl_no'] . ' ', 'target="_blank"') . '</td>';
	else
		echo '<td rowspan="3" class="' . $toggle_class . '">' . $r['party'] . '<br />' . anchor(underscore($r['type']).'/jobs/edit/'.$key, $r['job_no'], 'target="_blank"') . '<br /><span class="orange">' . $r['container_20'] . 'x20, ', $r['container_40'] . 'x40</span><br />' . $r['sb_no'] . ' ' . $r['sb_date'] . '</td>';
	$charged = '';
	$due  = '';
	$total = array('charged' => 0, 'paid' => 0, 'due' => 0);
	foreach ($jobs['charges'] as $c) {
		if (isset($r[$c]['paid'])) {
			$total['paid']         += $r[$c]['paid'];
			$col_total[$c]['paid'] += $r[$c]['paid'];
		}
		if (isset($r[$c]['charged'])) {
			$total['charged']         += $r[$c]['charged'];
			$col_total[$c]['charged'] += $r[$c]['charged'];
		}
		if (isset($r[$c]['due'])) {
			$total['due']         += $r[$c]['due'];
			$col_total[$c]['due'] += $r[$c]['due'];
		}

		echo '<td class="alignright ' . $toggle_class . '">' . (isset($r[$c]['paid']) ? anchor('/accounting/' . $r[$c]['paid_url'], $r[$c]['paid'], 'class="red" target="_blank"') : null) . '</td>';
		$charged .= '<td class="alignright ' . $toggle_class . '">' . (isset($r[$c]['charged']) ? anchor('/accounting/' . $r[$c]['charged_url'], $r[$c]['charged'], 'class="green" target="_blank"') : null) . '</td>';
		if (isset($r[$c]['due'])) {
			if($r[$c]['due'] > 0) 
				$due  .= '<td class="alignright ' . $toggle_class . ' label-red white Difference">' . $r[$c]['due'] . '</td>';
			else if($r[$c]['due'] < 0) 
				$due  .= '<td class="alignright ' . $toggle_class . ' green">' . $r[$c]['due'] . '</td>';
			else 
				$due  .= '<td class="alignright ' . $toggle_class . ' bold">' . $r[$c]['due'] . '</td>';
		}
		else
			$due  .= '<td class="' . $toggle_class . '"></td>';
	}
	echo '<td class="alignright ' . $toggle_class . ' bold red">' . $total['paid'] . '</td>';
	$charged .= '<td class="alignright ' . $toggle_class . ' bold green">' . $total['charged'] . '</td>';
	if($total['due'] > 0) 
		$due  .= '<td class="alignright ' . $toggle_class . ' bold label-red white">' . $total['due'] . '</td>';
	else if($total['due'] < 0) 
		$due  .= '<td class="alignright ' . $toggle_class . ' bold green">' . $total['due'] . '</td>';
	else 
		$due  .= '<td class="alignright ' . $toggle_class . ' bold">' . $total['due'] . '</td>';
	
	echo '</tr>';

	echo '<tr class="Row_' . $i . '">' . $charged . '</tr>';
	echo '<tr class="Row_' . $i++ . '">' . $due . '</tr>';
}
$charged  = '';
$due   = '';
$total = array('charged' => 0, 'paid' => 0, 'due' => 0);
echo '<tr><td colspan="2" rowspan="3" class="alignright ' . $toggle_class . '">Total</td>';
foreach ($jobs['charges'] as $c) {
	$total['paid'] += $col_total[$c]['paid'];
	$total['charged']    += $col_total[$c]['charged'];
	$total['due']     += $col_total[$c]['due'];

	echo '<td class="alignright ' . $toggle_class . ' bold red">' . $col_total[$c]['paid'] . '</td>';
	$charged .= '<td class="alignright ' . $toggle_class . ' bold green">' . $col_total[$c]['charged'] . '</td>';
	if($col_total[$c]['due'] > 0) 
		$due .= '<td class="alignright ' . $toggle_class . ' bold label-red white">' . $col_total[$c]['due'] . '</td>';
	else if($col_total[$c]['due'] < 0) 
		$due .= '<td class="alignright ' . $toggle_class . ' bold green">' . $col_total[$c]['due'] . '</td>';
	else 
		$due .= '<td class="alignright ' . $toggle_class . ' bold">' . $col_total[$c]['due'] . '</td>';
}
echo '<td class="alignright ' . $toggle_class . ' bold red">' . $total['paid'] . '</td>';
$charged .= '<td class="alignright ' . $toggle_class . ' bold green">' . $total['charged'] . '</td>';
if($total['due'] > 0) 
	$due  .= '<td class="alignright ' . $toggle_class . ' bold label-red white">' . $total['due'] . '</td>';
else if($total['due'] < 0) 
	$due  .= '<td class="alignright ' . $toggle_class . ' bold green">' . $total['due'] . '</td>';
else 
	$due  .= '<td class="alignright ' . $toggle_class . ' bold">' . $total['due'] . '</td>';

echo '</tr>';

echo "<tr>$charged</tr>";
echo "<tr>$due</tr>";
?>
</tbody>
</table>

<script>

function resizeHeader() {
	$("#Jobs").find('thead tr').children().each(function(i, e) {
		$($("#FixedHeader").find('thead tr').children()[i]).width($(e).width());
	});
	$("#FixedHeader").width($("#Jobs").width());

	if (!($.browser == "msie" && $.browser.version < 7)) {
		var target = "div#FixedToolbar"; 
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
}

$(document).ready(function() {
	<?php echo 'dateRangePicker({
		years:     ['.$years[0].', '.$years[1].'],
		from_date: \''.convDate($from_date).'\',
		to_date:   \''.convDate($to_date).'\'
	});' ?>

	resizeHeader();
	$('td.Difference').each(function(i, e) {
		var c = $(this).parents('tr').attr('class');
    	$("tr."+c).addClass('DontHideMe');
    	resizeHeader();
    });

	$("#HideZero").on('click', function() {
		$("table#Jobs tr:not(.DontHideMe)").toggle();
		resizeHeader();
	});

<?php 
if (count($filter['party']) > 0) {
	ksort($filter['party']);
	foreach ($filter['party'] as $k => $v) {
		echo '$("ul#FilterParty").append("<li><a href=\"javascript: filter(\'party:' . $k . '\')\">' . $k . '</a></li>");';
	}
}
if (count($filter['vessel']) > 0) {
	ksort($filter['vessel']);
	foreach ($filter['vessel'] as $k => $v) {
		echo '$("ul#FilterVessel").append("<li><a href=\"javascript: filter(\'vessel:' . $k . '\')\">' . $k . '</a></li>");';
	}
}
if (count($filter['category']) > 0) {
	ksort($filter['category']);
	foreach ($filter['category'] as $k => $v) {
		echo '$("ul#FilterCategory").append("<li><a href=\"javascript: filter(\'category:' . $k . '\')\">' . $k . '</a></li>");';
	}
}
if (count($filter['product']) > 0) {
	ksort($filter['product']);
	foreach ($filter['product'] as $k => $v) {
		echo '$("ul#FilterProduct").append("<li><a href=\"javascript: filter(\'product:' . $k . '\')\">' . $k . '</a></li>");';
	}
}
if (count($filter['cha']) > 0) {
	ksort($filter['cha']);
	foreach ($filter['cha'] as $k => $v) {
		echo '$("ul#FilterCHA").append("<li><a href=\"javascript: filter(\'cha:' . $k . '\')\">' . $k . '</a></li>");';
	}
}
?>
});
</script>