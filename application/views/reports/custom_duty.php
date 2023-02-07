<style>

table#FixedHeader.fixedTop {
    position: fixed;
    top: 40px;
}
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
				<ul class="dropdown-menu pull-right" id="FilterPort"></ul>
			</div>
			<div class="btn-group">
				<button data-toggle="dropdown" class="btn btn-info dropdown-toggle"><?php echo (isset($parsed_search['cha']) ? '<i class="icon-filter4"></i>' : '') ?> CHA <span class="caret"></span></button>
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
			<div class="btn-group">
				<button data-toggle="dropdown" class="btn btn-info dropdown-toggle"><?php echo (isset($parsed_search['bank_name']) ? '<i class="icon-filter4"></i>' : '') ?> Bank <span class="caret"></span></button>
				<ul class="dropdown-menu pull-right" id="FilterBank"></ul>
			</div>
		</div>
	</td>
</tr>
</table>
</form>


<table class="table table-condensed table-striped table-bordered hide" id="FixedHeader">
<thead>
<tr>
	<th>ID</th>
	<th>Type</th>
	<th>Party Name</th>
	<th>BL No / BE No</th>
	<th>Vessel Name</th>
	<th>Challan No</th>
	<th>Date</th>
	<th>Bank</th>
	<th>Transaction No</th>
	<th>Duty Amount</th>
</tr>
</thead>
</table>

<table class="table table-condensed table-striped table-bordered" id="Register">
<thead>
<tr>
	<th>ID</th>
	<th>Type</th>
	<th>Party Name</th>
	<th>BL No / BE No</th>
	<th>Vessel Name</th>
	<th>Challan No</th>
	<th>Date</th>
	<th>Bank</th>
	<th>Transaction No</th>
	<th>Duty Amount</th>
</tr>
</thead>

<tbody>
<?php 
$total = 0;
$filter = array(
	'party'    => array(),
	'category' => array(),
	'product'  => array(),
	'vessel'   => array(),
	'port'     => array(),
	'cha'      => array(),
	'bank'     => array(),
);
foreach ($rows as $r) {
	$filter['party'][$r['party_name']]     = 1;
	$filter['category'][$r['category']]    = 1;
	$filter['product'][$r['product_name']] = 1;
	$filter['vessel'][$r['vessel_voyage']] = 1;
	$filter['port'][$r['indian_port']]     = 1;
	$filter['cha' ][$r['cha_name']]        = 1;
	$filter['bank' ][$r['bank_name']]      = 1;

	echo '<tr>
	<td class="aligncenter">' . anchor('tracking/icegate_be/index/'.$r['id'], $r['id'], 'class="Popup"') . '</td>
	<td>' . $r['cargo_type'] . '</td>
	<td>' . $r['party_name'] . '</td>
	<td>' . $r['bl_no'] . ' / ' . $r['be_no'] . '</td>
	<td>' . $r['vessel_voyage'] . '</td>
	<td class="aligncenter">' . $r['challan_no'] . '</td>
	<td class="aligncenter">' . $r['payment_date'] . '</td>
	<td>' . $r['bank_name'] . '</td>
	<td>' . $r['bank_transaction_no'] . '</td>
	<td class="alignright">' . inr_format($r['duty_amount']) . '</td>
</tr>
';
	$total = bcadd($total, $r['duty_amount'], 2);
} ?>
</tbody>

<thead>
<tr>
	<th colspan="9" class="alignright">Total</th>
	<th class="alignright"><?php echo inr_format($total) ?></th>
</tr>
</thead>
</table>


<script>

$(document).ready(function() {
	<?php echo 'dateRangePicker({
		years:     ['.$years[0].', '.$years[1].'],
		from_date: \''.convDate($from_date).'\',
		to_date:   \''.convDate($to_date).'\'
	});' ?>

	$("#Register").find('thead tr').children().each(function(i, e) {
	    $($("#FixedHeader").find('thead tr').children()[i]).width($(e).width());
	});
	$("#FixedHeader").width($("#Register").width());

	if (!($.browser == "msie" && $.browser.version < 7)) {
        var target = "table#FixedHeader"; //, top = $(target).offset().top - parseFloat($(target).css("margin-top").replace(/auto/, 0));
        $(window).scroll(function(event) {
            if ($(this).scrollTop() > 75) {
                $(target).addClass("fixedTop show");
                $(target).removeClass("hide");
            } else {
                $(target).removeClass("fixedTop show");
                $(target).addClass("hide");
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
		echo '$("ul#FilterCHA").append("<li><a href=\"javascript: filter(\'cha:' . $k . '\')\">' . $k . '</a></li>");';
	}
	echo '$("ul#FilterCHA").append("<li><a class=\"red\" href=\"javascript: filter(\'cha:\')\">Clear Filter</a></li>");';
}
if (count($filter['bank']) > 0) {
	ksort($filter['bank']);
	foreach ($filter['bank'] as $k => $v) {
		echo '$("ul#FilterBank").append("<li><a href=\"javascript: filter(\'bank:' . $k . '\')\">' . $k . '</a></li>");';
	}
	echo '$("ul#FilterBank").append("<li><a class=\"red\" href=\"javascript: filter(\'bank:\')\">Clear Filter</a></li>");';
}
?>
});
</script>
