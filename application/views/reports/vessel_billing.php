<style>
.Average { background-color: #eee !important; }
.SubTotal { background-color: #eee !important; }
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

	<td width="200px">
		<?php echo form_dropdown('bill_items[]', getSelectOptions('ledgers', 'code', 'code', 'WHERE company_id IN (1,2) AND category IN ("General", "Bill Items")'), $bill_items, 'multiple class="SelectizeKaabar"'); ?>
	</td>

	<td>
		<div class="form-group">
			<label class="control-label">Hide SSSTP to SSSPL</label>
			<input type="checkbox" name="hide_stos" value="1" <?php echo ($hide_stos ? 'checked="checked"' : '') ?> />
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
		
	<td class="nowrap">
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
				<button data-toggle="dropdown" class="btn btn-info dropdown-toggle"><?php echo (isset($parsed_search['group']) ? '<i class="icon-filter4"></i>' : '') ?> Group <span class="caret"></span></button>
				<ul class="dropdown-menu pull-right nav-menu-scroll" id="FilterGroup"></ul>
			</div>
			<div class="btn-group">
				<button data-toggle="dropdown" class="btn btn-warning dropdown-toggle"><?php echo (isset($parsed_search['party']) ? '<i class="icon-filter4"></i>' : '') ?> Party <span class="caret"></span></button>
				<ul class="dropdown-menu pull-right nav-menu-scroll" id="FilterParty"></ul>
			</div>
			<div class="btn-group">
				<button data-toggle="dropdown" class="btn btn-danger dropdown-toggle"><?php echo (isset($parsed_search['vessel']) ? '<i class="icon-filter4"></i>' : '') ?> Vessel <span class="caret"></span></button>
				<ul class="dropdown-menu pull-right nav-menu-scroll" id="FilterVessel"></ul>
			</div>
			<div class="btn-group">
				<button data-toggle="dropdown" class="btn btn-success dropdown-toggle"><?php echo (isset($parsed_search['type']) ? '<i class="icon-filter4"></i>' : '') ?> Type <span class="caret"></span></button>
				<ul class="dropdown-menu pull-right" id="FilterType">
					<li><a href="javascript: filter('type: Bulk')">Bulk</a></li>
					<li><a href="javascript: filter('type: Container')">Container</a></li>
					<li><a class="red" href="javascript: filter('type:')">Clear Filter</a></li>
				</ul>
			</div>
			<div class="btn-group">
				<button data-toggle="dropdown" class="btn btn-info dropdown-toggle"><?php echo (isset($parsed_search['indian_port']) ? '<i class="icon-filter4"></i>' : '') ?> Port <span class="caret"></span></button>
				<ul class="dropdown-menu pull-right nav-menu-scroll" id="FilterPort"></ul>
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

<table class="table table-condensed table-striped table-bordered">
<thead>
<tr>
	<th>No</th>
	<th>Type</th>
	<th>Cargo Type</th>
	<th>Party</th>
	<th>Vessel</th>
	<th>BL / SB No</th>
	<th>CHA</th>
	<th>Packages</th>
	<th>Net Weight</th>
	<th>Containers</th>
	<th>Reimbersment</th>
	<th class="Average">Average</th>
	<th>Invoice</th>
	<th class="Average">Average</th>
	<th>ShivShakti</th>
	<th class="Average">Average</th>
	<th>Total</th>
	<th class="Average">Average</th>
</tr>
</thead>

<tbody>
<?php 
$prev_vessel = 0;
$group = array(
	'pieces'       => 0,
	'cbm'          => 0,
	'containers'   => 0,
	'reimbersment' => 0,
	'invoice'      => 0,
	'shivshakti'   => 0,
	'total'        => 0,
);
$total = array(
	'pieces'       => 0,
	'cbm'          => 0,
	'containers'   => 0,
	'reimbersment' => 0,
	'invoice'      => 0,
	'shivshakti'   => 0,
	'total'        => 0,
);
$filter = array(
	'group'  => array(),
	'party'  => array(),
	'vessel' => array(),
	'cha'    => array(),
	'port'   => array(),
);
$i = 1;
foreach ($jobs as $r) {
	$total['pieces']       = bcadd($total['pieces'], $r['packages'], 2);
	$total['cbm']          = bcadd($total['cbm'], $r['net_weight'], 2);
	$total['containers']   = bcadd($total['containers'], $r['containers'], 2);
	$total['reimbersment'] = bcadd($total['reimbersment'], $r['reimbersment'], 2);
	$total['invoice']      = bcadd($total['invoice'], $r['invoice'], 2);
	$total['shivshakti']   = bcadd($total['shivshakti'], $r['shivshakti'], 2);
	$total['total']       += bcadd(bcadd($r['reimbersment'], $r['invoice'], 2), $r['shivshakti'], 2);

	$reimbersment_avg = round($r['reimbersment'] / ($r['containers'] == 0 ? $r['net_weight'] : $r['containers']), 2);
	$invoice_avg      = round($r['invoice'] / ($r['containers'] == 0 ? $r['net_weight'] : $r['containers']), 2);
	$shivshakti_avg   = round($r['shivshakti'] / ($r['containers'] == 0 ? $r['net_weight'] : $r['containers']), 2);

	$filter['group'][$r['reimbersment_group']] = 1;
	$filter['group'][$r['invoice_group']]      = 1;
	$filter['group'][$r['shivshakti_group']]   = 1;
	$filter['party'][$r['reimbersment_name']]  = 1;
	$filter['party'][$r['invoice_name']]       = 1;
	$filter['party'][$r['shivshakti_name']]    = 1;
	$filter['vessel'][$r['vessel_name']]       = 1;
	$filter['cha'][$r['cha_name']]             = 1;
	$filter['port'][$r['indian_port']]         = 1;

	if ($prev_vessel != 0 AND $prev_vessel != $r['vessel_id']) {
		echo '<tr class="SubTotal">
	<th colspan="7" class="alignright">Group Total</th>
	<th class="alignright">' . $group['pieces'] . '</th>
	<th class="alignright">' . $group['cbm'] . '</th>
	<th class="alignright">' . $group['containers'] . '</th>
	<th class="alignright">' . inr_format($group['reimbersment']) . '</th>
	<th class="Average aligncenter"></th>
	<th class="alignright">' . inr_format($group['invoice']) . '</th>
	<th class="Average aligncenter"></th>
	<th class="alignright">' . inr_format($group['shivshakti']) . '</th>
	<th class="Average aligncenter"></th>
	<th class="alignright">' . inr_format($group['total']) . '</th>
	<th class="Average aligncenter"></th>
</tr>';
		$group = array(
			'pieces'       => 0,
			'cbm'          => 0,
			'containers'   => 0,
			'reimbersment' => 0,
			'invoice'      => 0,
			'shivshakti'   => 0,
			'total'        => 0,
		);
	}

	$group['pieces']       = bcadd($group['pieces'], $r['packages'], 2);
	$group['cbm']          = bcadd($group['cbm'], $r['net_weight'], 2);
	$group['containers']   = bcadd($group['containers'], $r['containers'], 2);
	$group['reimbersment'] = bcadd($group['reimbersment'], $r['reimbersment'], 2);
	$group['invoice']      = bcadd($group['invoice'], $r['invoice'], 2);
	$group['shivshakti']   = bcadd($group['shivshakti'], $r['shivshakti'], 2);
	$group['total']       += bcadd(bcadd($r['reimbersment'], $r['invoice'], 2), $r['shivshakti'], 2);
	
	echo '<tr>
	<td class="aligncenter">' . $i++ . '</td>
	<td>' . $r['type'] . '</td>
	<td>' . $r['cargo_type'] . '</td>
	<td>';
	if (strlen($r['reimbersment_name']) > 0) {
		echo $r['reimbersment_name'];
	}
	else if (strlen($r['invoice_name']) > 0) {
		echo $r['invoice_name'];
	}
	else  {
		echo $r['shivshakti_name'];
	}
	echo '<br /><span class="orange tiny">' . $r['party_name'] . '</span></td>
	<td>' . $r['vessel_name'] . ' (<span class="red">B-' . $r['berth_no'] . '</span>)</td>
	<td>' . anchor('import/jobs/edit/'.$r['id'], $r['bl_no'], 'target="_blank"') . '</td>
	<td>' . $r['cha_name'] . '</td>
	<td class="aligncenter">' . $r['packages'] . '</td>
	<td class="aligncenter">' . $r['net_weight'] . '</td>
	<td class="aligncenter">' . $r['containers'] . '</td>
	<td class="alignright">' . anchor('/accounting/'.underscore($r['reimbersment_url']), $r['reimbersment'], 'target="_blank"') . '</td>
	<td class="Average alignright">' . $reimbersment_avg . '</td>
	<td class="alignright">' . anchor('/accounting/'.underscore($r['invoice_url']), $r['invoice'], 'target="_blank"') . '</td>
	<td class="Average alignright">' . $invoice_avg . '</td>
	<td class="alignright">' . anchor('/accounting/'.underscore($r['shivshakti_url']), $r['shivshakti'], 'target="_blank"') . '</td>
	<td class="Average alignright">' . $shivshakti_avg . '</td>
	<td class="alignright">' . bcadd(bcadd($r['reimbersment'], $r['invoice'], 2), $r['shivshakti'], 2) . '</td>
	<td class="Average alignright">' . bcadd(bcadd($reimbersment_avg, $invoice_avg, 0), $shivshakti_avg, 0) . '</td>
</tr>';
	
	$prev_vessel = $r['vessel_id'];
} 
?>
</tbody>

<tfoot>
<tr>
	<th colspan="7" class="alignright">Total</th>
	<th class="alignright"><?php echo $total['pieces'] ?></th>
	<th class="alignright"><?php echo $total['cbm'] ?></th>
	<th class="alignright"><?php echo $total['containers'] ?></th>
	<th class="alignright"><?php echo inr_format($total['reimbersment']) ?></th>
	<th class="Average aligncenter"></th>
	<th class="alignright"><?php echo inr_format($total['invoice']) ?></th>
	<th class="Average aligncenter"></th>
	<th class="alignright"><?php echo inr_format($total['shivshakti']) ?></th>
	<th class="Average aligncenter"></th>
	<th class="alignright"><?php echo inr_format($total['total']) ?></th>
	<th class="Average aligncenter"></th>
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

	$(".Average").addClass('hide');

	listener.simple_combo('alt h', function(e) {
		$('.Average').toggleClass('hide');
	});

<?php 
if (count($filter['group']) > 0) {
	ksort($filter['group']);
	foreach ($filter['group'] as $k => $v) {
		echo '$("ul#FilterGroup").append("<li><a href=\"javascript: filter(\'group:' . $k . '\')\">' . $k . '</a></li>");';
	}
	echo '$("ul#FilterGroup").append("<li><a class=\"red\" href=\"javascript: filter(\'group:\')\">Clear Filter</a></li>");';
}
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
if (count($filter['cha']) > 0) {
	ksort($filter['cha']);
	foreach ($filter['cha'] as $k => $v) {
		echo '$("ul#FilterCHA").append("<li><a href=\"javascript: filter(\'cha:' . $k . '\')\">' . $k . '</a></li>");';
	}
}
if (count($filter['port']) > 0) {
	ksort($filter['port']);
	foreach ($filter['port'] as $k => $v) {
		echo '$("ul#FilterPort").append("<li><a href=\"javascript: filter(\'port:' . $k . '\')\">' . $k . '</a></li>");';
	}
}
?>
});
</script>