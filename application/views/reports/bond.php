<style>
tr.InBond td { background-color: #efe !important; }
tr.ExBond td { background-color: #fee !important; }
tr.SubTotal td { background-color: #ddd !important; }
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
							<button type="submit" class="btn btn-primary" id="SearchButton"><i class="icon-search icon-white"></i> Search</button>
				      	</span>
					</div>
				</div>
				<div class="col-md-4">
					<a href="#modal-search" class="btn btn-primary" data-toggle="modal"><i class="icon-search-plus"></i></a>
					<button type="button" class="btn btn-info" id="HideZero"><i class="icon-eye-slash"></i></button>
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
					<button data-toggle="dropdown" class="btn btn-success dropdown-toggle"><?php echo (isset($parsed_search['shipment']) ? '<i class="icon-filter4"></i>' : '') ?> Shipment <span class="caret"></span></button>
					<ul class="dropdown-menu pull-right nav-menu-scroll" id="FilterShipment">
						<li><a href="javascript: filter('shipment:Import')">Import</a></li>
						<li><a href="javascript: filter('shipment:Export')">Export</a></li>
						<li><a class="red" href="javascript: filter('shipment:')">Clear Filter</a></li>
					</ul>
				</div>

				
				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-warning dropdown-toggle"><?php echo (isset($parsed_search['party']) ? '<i class="icon-filter4"></i>' : '') ?> Party <span class="caret"></span></button>
					<ul class="dropdown-menu pull-right nav-menu-scroll" id="FilterParty"></ul>
				</div>
				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-info dropdown-toggle"><?php echo (isset($parsed_search['vessel']) ? '<i class="icon-filter4"></i>' : '') ?> Vessel <span class="caret"></span></button>
					<ul class="dropdown-menu pull-right nav-menu-scroll" id="FilterVessel"></ul>
				</div>
				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-danger dropdown-toggle"><?php echo (isset($parsed_search['product']) ? '<i class="icon-filter4"></i>' : '') ?> Product <span class="caret"></span></button>
					<ul class="dropdown-menu pull-right nav-menu-scroll" id="FilterProduct"></ul>
				</div>
				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-success dropdown-toggle"><?php echo (isset($parsed_search['cha']) ? '<i class="icon-filter4"></i>' : '') ?> CHA <span class="caret"></span></button>
					<ul class="dropdown-menu pull-right nav-menu-scroll" id="FilterCHA"></ul>
				</div>
				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-info dropdown-toggle"><?php echo (isset($parsed_search['port']) ? '<i class="icon-filter4"></i>' : '') ?> Port <span class="caret"></span></button>
					<ul class="dropdown-menu pull-right nav-menu-scroll" id="FilterPort"></ul>
				</div>
				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-warning dropdown-toggle"><?php echo (isset($parsed_search['warehouse']) ? '<i class="icon-filter4"></i>' : '') ?> Warehouse <span class="caret"></span></button>
					<ul class="dropdown-menu pull-right nav-menu-scroll" id="FilterWarehouse"></ul>
				</div>
			</div>
		</td>
	</tr>
	</table>
	</form>

	<table class="table table-condensed table-bordered hide" id="FixedHeader">
	<thead>
	<tr>
		<th>No</th>
		<th>Party / HSS</th>
		<th>Vessel</th>
		<th>BL No</th>
		<th>BE No</th>
		<th>BE Date</th>
		<th>Days</th>
		<th>Packages</th>
		<th>Net Weight</th>
		<th>Debit Note</th>
		<th>Invoice</th>
		<th>CHA</th>
		<th>Warehouse</th>
	</tr>
	</thead>
	</table>
</div>

<table class="table table-condensed table-bordered" id="Jobs">
<thead>
<tr>
	<th>No</th>
	<th>Party / HSS</th>
	<th>Vessel</th>
	<th>BL No</th>
	<th>BE No</th>
	<th>BE Date</th>
	<th>Days</th>
	<th>Packages</th>
	<th>Net Weight</th>
	<th>Debit Note</th>
	<th>Invoice</th>
	<th>CHA</th>
	<th>Warehouse</th>
</tr>
</thead>

<tbody>
<?php 
$total = array(
	'pieces' => 0,
	'cbm'    => 0,
);
$group = array(
	'pieces' => 0,
	'cbm'    => 0,
);
$filter = array(
	'party'     => array(),
	'vessel'    => array(),
	'product'   => array(),
	'port'      => array(),
	'cha'       => array(),
	'warehouse' => array(),
);
$i = 1;
foreach ($rows as $r) {
	$group['pieces'] = $r['packages'];
	$group['cbm']    = $r['net_weight'];

	$filter['party'][$r['party_name']]    = 1;
	$filter['vessel'][$r['vessel_name']]  = 1;
	$filter['cha'][$r['cha_name']]        = 1;
	$filter['warehouse'][$r['warehouse']] = 1;

	echo '<tr class="InBond Job_' . $r['id'] . '">
	<td class="aligncenter">' . $i++ . '</td>
	<td>' . $r['party_name'] . '<br /><span class="orange tiny">' . $r['high_seas'] . '</span></td>
	<td>' . $r['vessel_name'] . ' (<span class="red">B-' . $r['berth_no'] . '</span>)</td>
	<td class="aligncenter">' . $r['bl_no'] . '</td>
	<td class="aligncenter">' . anchor('/import/jobs/edit/'.$r['id'], $r['be_no'], 'target="_blank"') . '</td>
	<td class="aligncenter">' . $r['be_date'] . '</td>
	<td></td>
	<td class="alignright">' . $r['packages'] . '</td>
	<td class="alignright">' . $r['net_weight'] . '</td>
	<td class="tiny nowrap">';
	if (isset($r['vouchers'][3]))
		foreach ($r['vouchers'][3] as $v) {
			echo anchor('/accounting/' . underscore($v['url']), $v['company'] . '/' . $v['id2_format'], 'target="_blank"') . '<br />';
		}
	echo '</td>
	<td class="tiny nowrap">';
	if (isset($r['vouchers'][4]))
		foreach ($r['vouchers'][4] as $v) {
			echo anchor('/accounting/' . underscore($v['url']), $v['company'] . '/' . $v['id2_format'], 'target="_blank"') . '<br />';
		}
	echo '</td>
	<td>' . $r['cha_name'] . '</td>
	<td>' . $r['warehouse'] . '</td>
</tr>';
	foreach ($r['ExBond'] as $e) {
		$filter['party'][$e['party_name']]     = 1;
		$filter['product'][$e['product_name']] = 1;
		$filter['port'][$e['indian_port']]     = 1;
		$filter['cha'][$e['cha_name']]         = 1;

		$group['pieces'] = bcsub($group['pieces'], $e['packages']);
		$group['cbm']    = bcsub($group['cbm'], $e['net_weight'], 3);

		echo '<tr class="ExBond ' . ($group['pieces'] == 0 ? 'HideZero' : '') . ' Job_' . $r['id'] . '" job_id="' . $r['id'] . '">
	<td></td>
	<td>' . $e['party_name'] . '<br /><span class="orange tiny">' . $e['high_seas'] . '</span></td>
	<td></td>
	<td></td>
	<td class="aligncenter">' . anchor('/import/jobs/edit/'.$e['id'], $e['be_no'], 'target="_blank"') . '</td>
	<td class="aligncenter">' . $e['be_date'] . '</td>
	<td class="aligncenter">' . daysDiff($r['be_date'], $e['be_date']) . '</td>
	<td class="alignright">' . $e['packages'] . '</td>
	<td class="alignright">' . $e['net_weight'] . '</td>
	<td class="tiny nowrap">';
	if (isset($e['vouchers'][3]))
		foreach ($e['vouchers'][3] as $v) {
			echo anchor('/accounting/' . underscore($v['url']), $v['company'] . '/'. $v['id2_format'], 'target="_blank"') . '<br />';
		}
	echo '</td>
	<td class="tiny nowrap">';
	if (isset($e['vouchers'][4]))
		foreach ($e['vouchers'][4] as $v) {
			echo anchor('/accounting/' . underscore($v['url']), $v['company'] . '/'. $v['id2_format'], 'target="_blank"') . '<br />';
		}
	echo '</td>
	<td>' . $e['cha_name'] . '</td>
	<td></td>
</tr>';
	}
	echo '<tr class="SubTotal Job_' . $r['id'] . '">
	<td colspan="7" class="bold alignright">Balance Total</td>
	<td class="bold alignright">' . $group['pieces'] . '</td>
	<td class="bold alignright">' . $group['cbm'] . '</td>
	<td colspan="4">&nbsp;</td>
</tr>';

	$total['pieces'] = bcadd($total['pieces'], $group['pieces']);
	$total['cbm']    = bcadd($total['cbm'], $group['cbm'], 3);

} 
?>
</tbody>

<tfoot>
<tr>
	<th colspan="7" class="alignright">Total</th>
	<th class="alignright"><?php echo $total['pieces'] ?></th>
	<th class="alignright"><?php echo $total['cbm'] ?></th>
	<th></th>
	<th></th>
	<th></th>
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

	$("#HideZero").on('click', function() {
		$(".HideZero").each(function() {
			var job_id = $(this).attr("job_id");
			$(".Job_"+job_id).toggle();
		});
	});

	$(".Average").addClass('hide');

	listener.simple_combo('alt h', function(e) {
		$('.Average').toggleClass('hide');
	});

<?php 
if (count($filter['party']) > 0) {
	ksort($filter['party']);
	foreach ($filter['party'] as $k => $v) {
		echo '$("ul#FilterParty").append("<li><a href=\"javascript: filter(\'party:' . $k . '\')\">' . $k . '</a></li>");';
	}
	echo '$("ul#FilterParty").append("<li><a class=\"red\" href=\"javascript: filter(\'party:\')\">Clear Filter</a></li>");';
}
if (count($filter['vessel']) > 0) {
	ksort($filter['vessel']);
	foreach ($filter['vessel'] as $k => $v) {
		echo '$("ul#FilterVessel").append("<li><a href=\"javascript: filter(\'vessel:' . $k . '\')\">' . $k . '</a></li>");';
	}
	echo '$("ul#FilterVessel").append("<li><a class=\"red\" href=\"javascript: filter(\'vessel:\')\">Clear Filter</a></li>");';
}
if (count($filter['product']) > 0) {
	ksort($filter['product']);
	foreach ($filter['product'] as $k => $v) {
		echo '$("ul#FilterProduct").append("<li><a href=\"javascript: filter(\'product:' . $k . '\')\">' . $k . '</a></li>");';
	}
	echo '$("ul#FilterProduct").append("<li><a class=\"red\" href=\"javascript: filter(\'product:\')\">Clear Filter</a></li>");';
}
if (count($filter['cha']) > 0) {
	ksort($filter['cha']);
	foreach ($filter['cha'] as $k => $v) {
		echo '$("ul#FilterCHA").append("<li><a href=\"javascript: filter(\'cha:' . $k . '\')\">' . $k . '</a></li>");';
	}
	echo '$("ul#FilterCHA").append("<li><a class=\"red\" href=\"javascript: filter(\'cha:\')\">Clear Filter</a></li>");';
}
if (count($filter['port']) > 0) {
	ksort($filter['port']);
	foreach ($filter['port'] as $k => $v) {
		echo '$("ul#FilterPort").append("<li><a href=\"javascript: filter(\'port:' . $k . '\')\">' . $k . '</a></li>");';
	}
	echo '$("ul#FilterPort").append("<li><a class=\"red\" href=\"javascript: filter(\'port:\')\">Clear Filter</a></li>");';
}
if (count($filter['warehouse']) > 0) {
	ksort($filter['warehouse']);
	foreach ($filter['warehouse'] as $k => $v) {
		echo '$("ul#FilterWarehouse").append("<li><a href=\"javascript: filter(\'warehouse:' . $k . '\')\">' . $k . '</a></li>");';
	}
	echo '$("ul#FilterWarehouse").append("<li><a class=\"red\" href=\"javascript: filter(\'warehouse:\')\">Clear Filter</a></li>");';
}
?>
});
</script>