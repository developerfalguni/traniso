

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
				</div>
			</div>
		</td>
		
		<td>
			<div class="nowrap">
				<button type="button" class="btn btn-group btn-primary" onclick="javascript: clearSearch()"><i class="icon-align-justify"></i></button>

				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-info dropdown-toggle"><?php echo (isset($parsed_search['party']) ? '<i class="icon-filter4"></i>' : '') ?> Party <span class="caret"></span></button>
					<ul class="dropdown-menu nav-menu-scroll" id="FilterParty"></ul>
				</div>
				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-success dropdown-toggle"><?php echo (isset($parsed_search['vessel']) ? '<i class="icon-filter4"></i>' : '') ?> Vessel <span class="caret"></span></button>
					<ul class="dropdown-menu nav-menu-scroll" id="FilterVessel"></ul>
				</div>
				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-warning dropdown-toggle"><?php echo (isset($parsed_search['category']) ? '<i class="icon-filter4"></i>' : '') ?> Category <span class="caret"></span></button>
					<ul class="dropdown-menu nav-menu-scroll" id="FilterCategory"></ul>
				</div>
				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-warning dropdown-toggle"><?php echo (isset($parsed_search['product']) ? '<i class="icon-filter4"></i>' : '') ?> Product <span class="caret"></span></button>
					<ul class="dropdown-menu nav-menu-scroll" id="FilterProduct"></ul>
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
					<button data-toggle="dropdown" class="btn btn-primary dropdown-toggle"><?php echo (isset($parsed_search['port']) ? '<i class="icon-filter4"></i>' : '') ?> Port <span class="caret"></span></button>
					<ul class="dropdown-menu" id="FilterPort"></ul>
				</div>
				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-info dropdown-toggle"><?php echo (isset($parsed_search['cha']) ? '<i class="icon-filter4"></i>' : '') ?> CHA <span class="caret"></span></button>
					<ul class="dropdown-menu" id="FilterCHA"></ul>
				</div>
				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-info dropdown-toggle"><?php echo (isset($parsed_search['cfs']) ? '<i class="icon-filter4"></i>' : '') ?> CFS <span class="caret"></span></button>
					<ul class="dropdown-menu" id="FilterCFS"></ul>
				</div>
				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-warning dropdown-toggle"><?php echo (isset($parsed_search['appraisement']) ? '<i class="icon-filter4"></i>' : '') ?> Appr <span class="caret"></span></button>
					<ul class="dropdown-menu" id="FilterAppraisment">
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
		<th>No</th>
		<th>Type</th>
		<th>Party</th>
		<th>Product</th>
		<th>Vessel</th>
		<th>Pieces</th>
		<th>CBM</th>
		<th>C.20</th>
		<th>C.40</th>
		<th>BL No &amp; Date</th>
		<th>BE No &amp; Date</th>
		<th>IEC No</th>
		<th>Port</th>
		<th>CHA</th>
		<th>CFS</th>
		<th>Prior BE</th>
		<th>Section 48</th>
		<th>WBE No</th>
		<th>Appraisement</th>
		<th>Appraisement Date</th>
		<th>Assessment Date</th>
		<th>Payment Date</th>
		<th>Exam Date</th>
		<th>OOC Date</th>
		<th>Duty Amount</th>
	</tr>
	</thead>
	</table>
</div>

<table class="table table-condensed table-striped table-bordered" id="Jobs">
<thead>
<tr>
	<th>No</th>
	<th>Type</th>
	<th>Party</th>
	<th>Product</th>
	<th>Vessel</th>
	<th>Pieces</th>
	<th>CBM</th>
	<th>C.20</th>
	<th>C.40</th>
	<th>BL No &amp; Date</th>
	<th>BE No &amp; Date</th>
	<th>IEC No</th>
	<th>Port</th>
	<th>CHA</th>
	<th>CFS</th>
	<th>Prior BE</th>
	<th>Section 48</th>
	<th>WBE No</th>
	<th>Appraisement</th>
	<th>Appraisement Date</th>
	<th>Assessment Date</th>
	<th>Payment Date</th>
	<th>Exam Date</th>
	<th>OOC Date</th>
	<th>Duty Amount</th>
</tr>
</thead>

<tbody>
<?php 
$total = array(
	'pieces'     => 0,
	'net_weight' => 0,
	'c20'        => 0,
	'c40'        => 0,
	'duty'       => 0
);
$filter = array(
	'party'    => array(),
	'category' => array(),
	'product'  => array(),
	'vessel'   => array(),
	'port'     => array(),
	'cha'      => array(),
	'cfs'      => array(),
);
$i = 1;
foreach ($icegate as $r) {
	$total['pieces']     += $r['packages'];
	$total['net_weight'] = bcadd($total['net_weight'], $r['net_weight'], 2);
	$total['c20']        += $r['container_20'];
	$total['c40']        += $r['container_40'];
	$total['duty']       = bcadd($total['duty'], $r['duty_amount'], 2);

	$filter['party'][$r['party_name']]   = 1;
	$filter['category'][$r['category']]  = 1;
	$filter['product'][$r['product']]    = 1;
	$filter['vessel'][$r['vessel_name']] = 1;
	$filter['port'][$r['indian_port']]   = 1;
	$filter['cha' ][$r['cha_no']]        = $r['cha_name'];
	$filter['cfs' ][$r['cfs_name']]      = $r['cfs_name'];

	echo '<tr>
	<td class="aligncenter">' . $i++ . '</td>
	<td>' . $r['cargo_type'] . '</td>
	<td>' . $r['party_name'] . '</td>
	<td>' . $r['product'] . '</td>
	<td>' . $r['vessel_name'] . '</td>
	<td class="alignright">' . $r['packages'] . '</td>
	<td class="alignright">' . $r['net_weight'] . '</td>
	<td class="aligncenter">' . $r['container_20'] . '</td>
	<td class="aligncenter">' . $r['container_40'] . '</td>
	<td>' . anchor('import/jobs/edit/'.$r['id'], $r['bl_no'], 'target="_blank"') . ' / ' . $r['bl_date'] . '</td>
	<td>' . $r['be_no'] . ' / ' . $r['be_date'] . '</td>
	<td>' . $r['iec_no'] . '</td>
	<td>' . $r['indian_port'] . '</td>
	<td>' . $r['cha_name'] . '</td>
	<td>' . $r['cfs_name'] . '</td>
	<td>' . $r['prior_be'] . '</td>
	<td>' . $r['section_48'] . '</td>
	<td>' . $r['wbe_no'] . '</td>
	<td>' . $r['appraisement'] . '</td>
	<td>' . $r['appraisement_date'] . '</td>
	<td>' . $r['assessment_date'] . '</td>
	<td>' . $r['payment_date'] . '</td>
	<td>' . $r['exam_date'] . '</td>
	<td>' . $r['ooc_date'] . '</td>
	<td class="alignright">' . inr_format($r['duty_amount']) . '</td>
</tr>
';
} ?>
</tbody>

<tfoot>
<tr>
	<th colspan="5" class="alignright">Total</th>
	<th class="alignright"><?php echo $total['pieces'] ?></th>
	<th class="alignright"><?php echo $total['net_weight'] ?></th>
	<th class="alignright"><?php echo $total['c20'] ?></th>
	<th class="alignright"><?php echo $total['c40'] ?></th>
	<th colspan="14"></th>
	<th class="alignright"><?php echo inr_format($total['duty']) ?></th>
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
if (count($filter['cfs']) > 0) {
	ksort($filter['cfs']);
	foreach ($filter['cfs'] as $k => $v) {
		echo '$("ul#FilterCFS").append("<li><a href=\"javascript: filter(\'cfs: ' . $k . '\')\">' . $v . '</a></li>");';
	}
	echo '$("ul#FilterCFS").append("<li><a class=\"red\" href=\"javascript: filter(\'cfs:\')\">Clear Filter</a></li>");';
}
?>
});
</script>