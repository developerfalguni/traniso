<style>
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
				echo anchor($this->_clspath.$this->_class."/preview", '<i class="icon-file-o"></i>', 'class="btn btn-default Popup"') .
				anchor($this->_clspath.$this->_class."/preview/1", '<i class="icon-file-pdf"></i>', 'class="btn btn-default Popup"') .
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
				<button data-toggle="dropdown" class="btn btn-primary dropdown-toggle"><?php echo (isset($parsed_search['group']) ? '<i class="icon-filter4"></i>' : '') ?> Group <span class="caret"></span></button>
				<ul class="dropdown-menu pull-right nav-menu-scroll" id="FilterGroup"></ul>
			</div>
			<div class="btn-group">
				<button data-toggle="dropdown" class="btn btn-info dropdown-toggle"><?php echo (isset($parsed_search['party']) ? '<i class="icon-filter4"></i>' : '') ?> Party <span class="caret"></span></button>
				<ul class="dropdown-menu pull-right nav-menu-scroll" id="FilterParty"></ul>
			</div>
			<div class="btn-group">
				<button data-toggle="dropdown" class="btn btn-success dropdown-toggle"><?php echo (isset($parsed_search['vessel']) ? '<i class="icon-filter4"></i>' : '') ?> Vessel <span class="caret"></span></button>
				<ul class="dropdown-menu pull-right nav-menu-scroll" id="FilterVessel"></ul>
			</div>
			<div class="btn-group">
				<button data-toggle="dropdown" class="btn btn-warning dropdown-toggle"><?php echo (isset($parsed_search['type']) ? '<i class="icon-filter4"></i>' : '') ?> Type <span class="caret"></span></button>
				<ul class="dropdown-menu" id="FilterType">
					<li><a href="javascript: filter('type: Import')">Import</a></li>
					<li><a href="javascript: filter('type: Export')">Export</a></li>
					<li><a class="red" href="javascript: filter('type:')">Clear Filter</a></li>
				</ul>
			</div>
			<div class="btn-group">
				<button data-toggle="dropdown" class="btn btn-danger dropdown-toggle"><?php echo (isset($parsed_search['cargo']) ? '<i class="icon-filter4"></i>' : '') ?> Cargo <span class="caret"></span></button>
				<ul class="dropdown-menu" id="FilterType">
					<li><a href="javascript: filter('cargo: Bulk')">Bulk</a></li>
					<li><a href="javascript: filter('cargo: Container')">Container</a></li>
					<li><a class="red" href="javascript: filter('cargo:')">Clear Filter</a></li>
				</ul>
			</div>
			<div class="btn-group">
				<button data-toggle="dropdown" class="btn btn-info dropdown-toggle"><?php echo (isset($parsed_search['port']) ? '<i class="icon-filter4"></i>' : '') ?> Port <span class="caret"></span></button>
				<ul class="dropdown-menu" id="FilterPort"></ul>
			</div>
		</div>
	</td>
</tr>
</table>
</form>


<h4><?php echo $page_title ?></h4>
<table class="table table-condensed table-bordered">
<thead>
<tr>
	<th width="48px">No</th>
	<th>Type</th>
	<th>Cargo</th>
	<th>Vessel</th>
	<th>Party Name</th>
	<th>BL No</th>
	<th>CHA Name</th>
	<th width="100px">Quantity</th>
	<th>Unit</th>
	<th width="100px">Weight</th>
	<th>Unit</th>
</tr>
</thead>

<tbody>
<?php 
$i = 1;
$total = array('pieces' => 0, 'cbm' => 0);
$filter = array(
	'group'  => array(),
	'party'  => array(),
	'vessel' => array(),
	'port'   => array(),
	'cha'    => array(),
);
foreach ($rows as $r) {
	$total['pieces'] += $r['pieces'];
	$total['cbm']    += $r['cbm'];

	$filter['group'][$r['group_name']]     = 1;
	$filter['party'][$r['party_name']]     = 1;
	$filter['vessel'][$r['vessel_voyage']] = 1;
	$filter['port'][$r['indian_port']]     = 1;
	$filter['cha'][$r['cha_name']]         = 1;

	echo '<tr>
	<td class="aligncenter">' . $i++ . '</td>
	<td>' . $r['type'] . '</td>
	<td>' . $r['cargo_type'] . '</td>
	<td>' . $r['vessel_voyage'] . '</td>
	<td>' . $r['party_name'] . '</td>
	<td>' . $r['bl_no'] . '</td>
	<td>' . $r['cha_name'] . '</td>
	<td class="alignright">' . $r['pieces'] . '</td>
	<td class="tiny">' . $r['package_unit'] . '</td>
	<td class="alignright">' . $r['cbm'] . '</td>
	<td class="tiny">' . $r['net_weight_unit'] . '</td>
</tr>';
} 
?>
</tbody>

<tfoot>
<tr>
	<th></th>
	<th class="alignright" colspan="6">Total</th>
	<th class="alignright"><?php echo $total['pieces'] ?></th>
	<th></th>
	<th class="alignright"><?php echo $total['cbm'] ?></th>
	<th></th>
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
		echo '$("ul#FilterCha").append("<li><a href=\"javascript: filter(\'cha:' . $k . '\')\">' . $k . '</a></li>");';
	}
	echo '$("ul#FilterCha").append("<li><a class=\"red\" href=\"javascript: filter(\'cha:\')\">Clear Filter</a></li>");';
}
?>
});
</script>
