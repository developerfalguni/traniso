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

<table class="table table-condensed table-striped table-bordered" id="Jobs">
<thead>
<tr>
	<th colspan="16">
		<div class="row">
			<div class="col-md-4">
				<?php echo form_open($this->uri->uri_string(), 'class="form-search" id="SearchForm"'); ?>
				<input type="hidden" name="search_form" value="1" />
				<div class="row">
					<div class="col-xs-8">
						<div class="input-group">
							<input type="text" class="form-control form-control-sm search-query" name="search" value="<?php echo (isset($search) ? $search : '') ?>" id="Search" />
							<span class="input-group-btn">
								<button type="submit" class="btn btn-primary" id="SearchButton"><i class="fa fa-search"></i> Search</button>
								<a href="#modal-search" class="btn btn-primary" data-toggle="modal"><i class="icon-search-plus"></i></a>
							</span>
						</div>
					</div>
					<div class="col-xs-4">
						<?php echo anchor($this->_clspath.$this->_class."/excel", '<i class="icon-file-excel"></i>', 'class="btn btn-warning Popup"') ?>
					</div>
				</div>
				</form>
			</div>

			<div class="col-md-6">
				<button type="button" class="btn btn-group btn-primary" onclick="javascript: clearSearch()"><i class="icon-align-justify"></i></button>
				
				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-info dropdown-toggle"><?php echo (isset($parsed_search['party']) ? '<i class="icon-filter4"></i>' : '') ?> Party <span class="caret"></span></button>
					<ul class="dropdown-menu nav-menu-scroll" id="FilterParty">
						<li><a class="red" href="javascript: filter('party:')">Clear Filter</a></li>
					</ul>
				</div>
				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-success dropdown-toggle"><?php echo (isset($parsed_search['vessel']) ? '<i class="icon-filter4"></i>' : '') ?> Vessel <span class="caret"></span></button>
					<ul class="dropdown-menu" id="FilterVessel">
						<li><a class="red" href="javascript: filter('vessel:')">Clear Filter</a></li>
					</ul>
				</div>
				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-warning dropdown-toggle"><?php echo (isset($parsed_search['category']) ? '<i class="icon-filter4"></i>' : '') ?> Category <span class="caret"></span></button>
					<ul class="dropdown-menu" id="FilterCategory">
						<li><a class="red" href="javascript: filter('category:')">Clear Filter</a></li>
					</ul>
				</div>
				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-danger dropdown-toggle"><?php echo (isset($parsed_search['product']) ? '<i class="icon-filter4"></i>' : '') ?> Product <span class="caret"></span></button>
					<ul class="dropdown-menu" id="FilterProduct">
						<li><a class="red" href="javascript: filter('product:')">Clear Filter</a></li>
					</ul>
				</div>
				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-info dropdown-toggle"><?php echo (isset($parsed_search['port']) ? '<i class="icon-filter4"></i>' : '') ?> Port <span class="caret"></span></button>
					<ul class="dropdown-menu" id="FilterPort">
						<li><a class="red" href="javascript: filter('port:')">Clear Filter</a></li>
					</ul>
				</div>
				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-success dropdown-toggle"><?php echo (isset($parsed_search['be_type']) ? '<i class="icon-filter4"></i>' : '') ?> BE Type <span class="caret"></span></button>
					<ul class="dropdown-menu" id="FilterBEType">
						<li><a class="red" href="javascript: filter('be_type:')">Clear Filter</a></li>
						<li><a href="javascript: filter('be_type:Home')">Home</a></li>
						<li><a href="javascript: filter('be_type:In-Bond')">In-Bond</a></li>
						<li><a href="javascript: filter('be_type:Ex-Bond')">Ex-Bond</a></li>
					</ul>
				</div>
				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-warning dropdown-toggle"><?php echo (isset($parsed_search['appraisement']) ? '<i class="icon-filter4"></i>' : '') ?> Appr. <span class="caret"></span></button>
					<ul class="dropdown-menu" id="FilterBEType">
						<li><a class="red" href="javascript: filter('appraisement:')">Clear Filter</a></li>
						<li><a href="javascript: filter('appraisement:SYSTEM')">RMS</a></li>
						<li><a href="javascript: filter('appraisement:OFFICER')">APP</a></li>
					</ul>
				</div>
				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-danger dropdown-toggle"><?php echo (isset($parsed_search['status']) ? '<i class="icon-filter4"></i>' : '') ?> Icegate <span class="caret"></span></button>
					<ul class="dropdown-menu" id="FilterIcegate">
						<li><a class="red" href="javascript: filter('status:')">Clear Filter</a></li>
						<?php foreach ($icegate_class as $k => $v)
							echo '<li><a href="javascript: filter(\'status: ' . $k . '\')">' . $k . '</a></li>';
						?>
					</ul>
				</div>
			</div>

			<div class="col-md-2">
				<?php echo anchor('tracking/icegate_be/captcha', '<i class="icon-refresh"></i>', 'class="btn btn-info Popup" data-placement="bottom" rel="tooltip" data-original-title="IceGate Captcha"'); ?>
			</div>
		</div>
	</th>
</tr>

<tr>
	<th>No</th>
	<th>Job No</th>
	<th>Party Name</th>
	<th>Weight</th>
	<th>Vessel</th>
	<th>BL No / BE No</th>
	<th>Shipper</th>
	<th>IceGate</th>
	<th>C. Duty</th>
	<th>S. Duty</th>
</tr>
</thead>

<tbody>
<?php
	$filter = array(
		'party'    => array(),
		'category' => array(),
		'product'  => array(),
		'vessel'   => array(),
		'port'     => array(),
		'icegate'  => array()
	);
	$prev_vessel = 0;
	$alt_color = '#f0f0f0';
	$totals = array('paid' => 0, 'unpaid' => 0, 'total' => 0);
	$grand  = array(
		'weight'      => 0,
		'cd_total' => 0, 'sd_total' => 0,
		'paid'     => 0, 'unpaid'   => 0, 'total'    => 0
	);
	$i = 1;
	foreach ($rows as $p) {

		$filter['category'][$p['category']]      = 1;
		$filter['party'][$p['party_name']]       = 1;
		$filter['product'][$p['product']]        = 1;
		$filter['vessel'][$p['vessel_name']]     = 1;
		$filter['port'][$p['port']]              = 1;
		$filter['icegate'][$p['current_status']] = 1;

		if ($prev_vessel != $p['vessel_id'])
			$alt_color = ($alt_color == '#fff' ? '#f0f0f0' : '#fff');

		$paid = 0;
		$unpaid = 0;
		$total = 0;
		
		$grand['weight']   += $p['net_weight'];
		$grand['cd_total'] += $p['custom_duty'];
		$grand['sd_total'] += $p['stamp_duty'];
		$grand['paid']     += $paid;
		$grand['unpaid']   += $unpaid;
		$grand['total']    += $total;

		echo '<tr style="background-color: ' . $alt_color . ';">
	<td class="aligncenter">' . $i++ . '</td>
	<td class="aligncenter '.$p['status'].'">' . $p['id2_format'] . '</td>
	<td>' . $p['party_name'] . ($p['high_seas'] == null ? '' : '<br /><span class="orange">' . $p['high_seas'] . '</span>') . '</td>
	<td class="alignright">' . number_format($p['net_weight'], 4, '.', '') . '</td>
	<td>' . $p['vessel_name'] . ' ' . $p['voyage_no'] . '</td>
	<td class="blue">' . anchor('/import/import_detail/edit/' . $p['job_id'], $p['bl_no'], 'target="_blank"') . '<br /><span class="tiny orange">' . $p['be_no'] . '</span> <span class="label ' . $label_class[$p['be_type']] . '">' . $p['be_type'] . '</span></td>
	<td>' . word_limiter($p['shipper_name'], 2) . '</td>

	<td class="' . ($p['last_status'] != $p['current_status'] ? 'markChanged nowrap' : 'nowrap') . '">';
	if (strlen($p['be_no']) > 0) {
		echo anchor('/tracking/icegate_be/index/'. $p['job_id'], 
			($p['query_raised'] != 'N.A.' && strlen($p['query_raised']) > 0 ? '<span class="label label-red">' . (stripos($p['query_raised'], '#') !== false ? '??' : '?') . '</span> ' : '') . 
			($p['section_48'] == 'Y' ? '<span class="label label-danger">SEC48</span> ' : '') . 
			'<span class="label ' . $icegate_class[$p['appraisement']] . '">' . $p['appraisement'] . '</span><br />
			<span class="label ' . $icegate_class[$p['current_status']] . '">' . $p['current_status'] . '</span> ', 
			'target="_blank"');
		}
	if (! is_null($p['challan_id']))
		echo '<span class="label label-default">C</span><br />';
	echo '<div class="pink tiny">' . moment($p['last_fetched']) . '</div></td>

	<td class="alignright">' . $p['custom_duty'] . '</td>
	<td class="alignright">' . $p['stamp_duty'] . '</td>
	</tr>';
		$prev_vessel = $p['vessel_id'];
	}
?>

<tr>
	<td></td>
	<td></td>
	<td></td>
	<td class="alignright bold tiny"><?php echo $grand['weight'] ?></td>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
	<td class="alignright bold tiny"><?php echo $grand['cd_total'] ?></td>
	<td class="alignright bold tiny"><?php echo $grand['sd_total'] ?></td>
</tr>
</tbody>
</table>

<script>
var payment_id = 0;


function showDeliveryOrder(id) {
	$("#DOPaymentID").val(id);
	$("#modal-delivery-order").modal();
}

$(document).ready(function() {

<?php 
ksort($filter['party']);
foreach ($filter['party'] as $k => $v)
	echo '$("ul#FilterParty").append("<li><a href=\"javascript: filter(\'party:' . $k . '\')\">' . $k . '</a></li>");';

ksort($filter['category']);
foreach ($filter['category'] as $k => $v)
	echo '$("ul#FilterCategory").append("<li><a href=\"javascript: filter(\'category:' . $k . '\')\">' . $k . '</a></li>");';

ksort($filter['product']);
foreach ($filter['product'] as $k => $v)
	echo '$("ul#FilterProduct").append("<li><a href=\"javascript: filter(\'product:' . $k . '\')\">' . $k . '</a></li>");';

ksort($filter['vessel']);
foreach ($filter['vessel'] as $k => $v)
	echo '$("ul#FilterVessel").append("<li><a href=\"javascript: filter(\'vessel:' . $k . '\')\">' . $k . '</a></li>");';

ksort($filter['port']);
foreach ($filter['port'] as $k => $v)
	echo '$("ul#FilterPort").append("<li><a href=\"javascript: filter(\'port:' . $k . '\')\">' . $k . '</a></li>");';

?>
});
</script>