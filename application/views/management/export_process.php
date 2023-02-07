<style>
.Date, .BLDate { width: 70px; }
td.markRed { background-color: #F33 !important;}
td.markYellow { background-color: #ffc !important;}
td.Pending	 { }
td.Bills { background-color: #7BD57B !important; }
td.Completed { background-color: #FFD5B0 !important; }
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

<table class="table toolbar">
<tr>
	<td class="nowrap">
		<?php echo form_open($this->uri->uri_string(), 'class="form-search" id="SearchForm"'); ?>
		<input type="hidden" name="search_form" value="1" />
		<input type="hidden" name="sortby" value="<?php echo $sortby ?>" id="SortBy" />
		<div class="input-group">
			<input type="text" class="form-control form-control-sm search-query" name="search" value="<?php echo (isset($search) ? $search : '') ?>" id="Search" />
			<span class="input-group-btn">
				<button type="submit" class="btn btn-primary" id="SearchButton"><i class="icon-search icon-white"></i> Search</button>
				<a href="#modal-search" class="btn btn-primary" data-toggle="modal"><i class="icon-search-plus"></i></a>
	      	</span>
		</div>
		</form>
	</td>

	<td>
		<div class="nowrap">
			<button type="button" class="btn btn-group btn-primary" onclick="javascript: clearSearch()"><i class="icon-align-justify"></i></button>
			
			<div class="btn-group">
				<button data-toggle="dropdown" class="btn btn-warning dropdown-toggle"><?php echo (isset($parsed_search['status']) ? '<i class="icon-filter4"></i>' : '') ?> Status <span class="caret"></span></button>
				<ul class="dropdown-menu">
					<li><a href="javascript: filterStatus('status: Pending')">Pending</a></li>
					<li><a href="javascript: filterStatus('status: Bills')">Bills</a></li>
					<li><a href="javascript: filterStatus('status: Completed')">Completed</a></li>
					<li><a href="javascript: filterStatus('status:')" class="red">Clear Filter</a></li>
				</ul>
			</div>
			<div class="btn-group">
				<button data-toggle="dropdown" class="btn btn-success dropdown-toggle"><?php echo (isset($parsed_search['party']) ? '<i class="icon-filter4"></i>' : '') ?> Party <span class="caret"></span></button>
				<ul class="dropdown-menu nav-menu-scroll" id="FilterParty">
					<li><a class="red" href="javascript: filter('party:')">Clear Filter</a></li>
				</ul>
			</div>
			<div class="btn-group">
				<button data-toggle="dropdown" class="btn btn-info dropdown-toggle"><?php echo (isset($parsed_search['line']) ? '<i class="icon-filter4"></i>' : '') ?> Line <span class="caret"></span></button>
				<ul class="dropdown-menu nav-menu-scroll" id="FilterLine">
					<li><a class="red" href="javascript: filter('line:')">Clear Filter</a></li>
				</ul>
			</div>
			<div class="btn-group">
				<button data-toggle="dropdown" class="btn btn-danger dropdown-toggle"><?php echo (isset($parsed_search['vessel']) ? '<i class="icon-filter4"></i>' : '') ?> Vessel <span class="caret"></span></button>
				<ul class="dropdown-menu nav-menu-scroll" id="FilterVessel">
					<li><a class="red" href="javascript: filter('vessel:')">Clear Filter</a></li>
				</ul>
			</div>
			<div class="btn-group">
				<button data-toggle="dropdown" class="btn btn-success dropdown-toggle"><?php echo (isset($parsed_search['pol']) ? '<i class="icon-filter4"></i>' : '') ?> POL <span class="caret"></span></button>
				<ul class="dropdown-menu nav-menu-scroll" id="FilterPOL">
					<li><a class="red" href="javascript: filter('pol:')">Clear Filter</a></li>
				</ul>
			</div>
			<div class="btn-group">
				<button data-toggle="dropdown" class="btn btn-warning dropdown-toggle"><?php echo (isset($parsed_search['pod']) ? '<i class="icon-filter4"></i>' : '') ?> POD <span class="caret"></span></button>
				<ul class="dropdown-menu nav-menu-scroll" id="FilterPOD">
					<li><a class="red" href="javascript: filter('pod:')">Clear Filter</a></li>
				</ul>
			</div>
		</div>
	</td>
</tr>
</table>


<?php echo form_open($this->uri->uri_string(), array('class' => 'form-horizontal', 'id' => 'PendingForm')); ?>
<table class="table table-condensed table-striped table-bordered" id="Jobs">
<thead>
<tr>
	<th>No</th>
	<th>Job No</th>
	<th>Party Name</th>
	<th>Invoice No / Date</th>
	<th>SB No / Date</th>
	<th>Cont.</th>
	<th>Shipping Line</th>
	<th>Vessel / <span class="green">ETD</span></th>
	<th>POL</th>
	<th>POD</th>
	<th>IceGate</th>
</tr>
</thead>

<tbody>
<?php
$filter = array(
	'party'    => array(),
	'line'     => array(),
	'vessel'   => array(),
	'pol'      => array(),
	'pod'      => array(),
);
$i = 1;
foreach ($rows as $r) {
	
	$filter['party'][$r['party_name']]   = 1;
	$filter['line'][$r['line_name']]     = 1;
	$filter['vessel'][$r['vessel_name']] = 1;
	$filter['pol'][$r['pol']]            = 1;
	$filter['pod'][$r['pod']]            = 1;

	echo '<tr>
	<td class="aligncenter">' . $i++ . '</td>
	<td class="aligncenter">' . anchor('/export/jobs/edit/' . $r['job_id'], $r['id2_format'], 'target="_blank"') . '</td>
	<td>' . $r['party_name'] . '</td>
	<td class="red">' . $r['invoice_no'] . '<br />' . $r['invoice_date'] . '</td>
	<td class="blue">' . $r['sb_no'] . '<br />' . $r['sb_date'] . '</td>
	<td>' . $r['containers'] . '</td>
	<td>' . character_limiter($r['line_name'], 20) . '</td>
	<td>' . $r['vessel_name'] . '<br /><span class="green">' . $r['etd_date'] . '</span></td>
	<td>' . $r['pol'] . '</td>
	<td>' . $r['pod'] . '</td>
	<td class="nowrap">';
	if (strlen($r['leo_date']) > 0)
		echo anchor('/tracking/icegate_sb/index/'. $r['child_job_id'], '<span class="label label-success">' . $r['leo_date'] . '</span><span class="label label-warning">' . $r['ep_copy_print_status'] . '</span><span class="label label-info">' . $r['print_status'] . '</span>', 'target="_blank"') . '<br />';
	else
		echo '<span class="label label-red">N/A</span><br />';

	echo '<div class="pink alignright">' . moment($r['last_fetched']) . '</div></td>
	</tr>';
}
?>
</tbody>
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