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
			</div>
		</td>

		<td>
			<div class="nowrap">
				<button type="button" class="btn btn-group btn-primary" onclick="javascript: clearSearch()"><i class="icon-align-justify"></i></button>
				
				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-info dropdown-toggle"><?php echo (isset($search['type']) ? '<i class="icon-filter4"></i>' : '') ?> Type <span class="caret"></span></button>
					<ul class="dropdown-menu nav-menu-scroll pull-right" id="FilterType">
						<li><a class="red" href="javascript: filter('type:')">Clear Filter</a></li>
						<li><a href="javascript: filter('type:Import')">Import</a></li>
						<li><a href="javascript: filter('type:Export')">Export</a></li>
					</ul>
				</div>

				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-info dropdown-toggle"><?php echo (isset($search['party']) ? '<i class="icon-filter4"></i>' : '') ?> Party <span class="caret"></span></button>
					<ul class="dropdown-menu nav-menu-scroll pull-right" id="FilterParty">
						<li><a class="red" href="javascript: filter('party:')">Clear Filter</a></li>
					</ul>
				</div>

				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-info dropdown-toggle">Invoice <span class="caret"></span></button>
					<ul class="dropdown-menu nav-menu-scroll pull-right">
						<li><a href="javascript: invoice(0)">All Entries</a></li>
						<li><a href="javascript: invoice(1)">Zero Only</a></li>
						<li><a href="javascript: invoice(2)">Non Zero Only</a></li>
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
		<th>Job No</th>
		<th>Customer</th>
		<th>C.20</th>
		<th>C.40</th>
		<th>Expenses</th>
		<th>Tpt Payment</th>
		<th>Invoice / Debit Note</th>
		<th>Balance</th>
	</tr>
	</thead>
	</table>
</div>

<table class="table table-condensed table-striped table-bordered" id="Result">
<thead>
<tr>
	<th>No</th>
	<th>Type</th>
	<th>Job No</th>
	<th>Customer</th>
	<th>C.20</th>
	<th>C.40</th>
	<th>Expenses</th>
	<th>Tpt Payment</th>
	<th>Invoice / Debit Note</th>
	<th>Balance</th>
</tr>
</thead>

<tbody>
<?php 
$i = 1;
$filter = array(
	'party' => array(),
);
$total = array(
	'container_20'   => 0,
	'container_40'   => 0,
	'expenses'       => 0,
	'transportation' => 0,
	'invoice'        => 0,
	'net_profit'     => 0,
);
foreach ($rows as $r) {
	$filter['party'][$r['party_name']] = $r['party_name'];

	$net_profit = bcsub($r['invoice'], bcadd($r['expenses'], $r['transportation'], 2), 2);

	$total['container_20']   += $r['container_20'];
	$total['container_40']   += $r['container_40'];
	$total['expenses']       += $r['expenses'];
	$total['transportation'] += $r['transportation'];
	$total['invoice']        += $r['invoice'];
	$total['net_profit']     += $net_profit;

	$job_link = $r['type'] == 'Import' ? anchor('/import/jobs/edit/' . $r['job_id'], $r['id2_format'], 'target="_blank"') : anchor('/export/jobs/edit/' . $r['job_id'], $r['id2_format'], 'target="_blank"');

	echo '<tr class="JobRow">
	<td class="aligncenter">' . $i++ . '</td>
	<td>' . $r['type'] . '</td>
	<td class="aligncenter">' . $job_link . '</td>
	<td>' . $r['party_name'] . '</td>
	<td class="aligncenter">' . $r['container_20'] . '</td>
	<td class="aligncenter">' . $r['container_40'] . '</td>
	<td class="alignright ' . ($r['expenses'] == 0 ? 'ExpenseZero' : 'ExpenseNonZero') . '">' . $r['expenses'] . '</td>
	<td class="alignright ' . ($r['transportation'] == 0 ? 'ExpenseZero' : 'ExpenseNonZero') . '">' . $r['transportation'] . '</td>
	<td class="alignright ' . ($r['invoice'] == 0 ? 'PaymentZero' : 'PaymentNonZero') . '">' . $r['invoice'] . '</td>
	<td class="alignright ' . ($net_profit < 0 ? 'red' : 'green') . '">' . $net_profit . '</td>
</tr>';
} 
?>
</tbody>
<tfoot>
	<tr>
		<th class="alignright" colspan="4">Total</th>
		<th class="alignright"><?php echo $total['container_20']; ?></th>
		<th class="alignright"><?php echo $total['container_40']; ?></th>
		<th class="alignright"><?php echo inr_format($total['expenses']); ?></th>
		<th class="alignright"><?php echo inr_format($total['transportation']); ?></th>
		<th class="alignright"><?php echo inr_format($total['invoice']); ?></th>
		<th class="alignright"><?php echo inr_format($total['net_profit']); ?></th>
	</tr>
</tfoot>
</table>
</form>

<script>
function filterStatus(status) {
	$('input#Search').val(status);
	$("#SearchButton").click();
}


function showStatus(id) {
	pending_id = id;
	$('#modal-status').modal();
}

function setStatus(status) {
	switch(status) {
		case 0: $('.Status_'+pending_id).val('Pending');   break;
		case 1: $('.Status_'+pending_id).val('Bills');     break;
		case 2: $('.Status_'+pending_id).val('Completed'); break;
	}
	$("#PendingForm").submit();
}

function Save() {
	$("#PendingForm").submit();
}

function Sort(sortby) {
	$("form#SearchForm input#SortBy").val(sortby);
	$("#SearchButton").click();
}

$(document).ready(function() {
<?php 
if (count($filter['party']) > 0) {
	ksort($filter['party']);
	foreach ($filter['party'] as $k => $v)
		echo '$("ul#FilterParty").append("<li><a href=\"javascript: filter(\'party:' . $k . '\')\">' . $k . '</a></li>");';
}
if (count($filter['line']) > 0) {
	ksort($filter['line']);
	foreach ($filter['line'] as $k => $v)
		echo '$("ul#FilterLine").append("<li><a href=\"javascript: filter(\'line:' . $k . '\')\">' . $k . '</a></li>");';
}
if (count($filter['vessel']) > 0) {
	ksort($filter['vessel']);
	foreach ($filter['vessel'] as $k => $v)
		echo '$("ul#FilterVessel").append("<li><a href=\"javascript: filter(\'vessel:' . $k . '\')\">' . $k . '</a></li>");';
}
if (count($filter['pol']) > 0) {
	ksort($filter['pol']);
	foreach ($filter['pol'] as $k => $v)
		echo '$("ul#FilterPOL").append("<li><a href=\"javascript: filter(\'pol:' . $k . '\')\">' . $k . '</a></li>");';
}
if (count($filter['pod']) > 0) {
	ksort($filter['pod']);
	foreach ($filter['pod'] as $k => $v)
		echo '$("ul#FilterPOD").append("<li><a href=\"javascript: filter(\'pod:' . $k . '\')\">' . $k . '</a></li>");';
}
?>
});
</script>