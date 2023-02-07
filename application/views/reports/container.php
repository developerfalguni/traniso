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
				<button data-toggle="dropdown" class="btn btn-success dropdown-toggle"><?php echo (isset($parsed_search['party']) ? '<i class="icon-filter4"></i>' : '') ?> Party <span class="caret"></span></button>
				<ul class="dropdown-menu pull-right nav-menu-scroll" id="FilterParty"></ul>
			</div>
			<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-warning dropdown-toggle"><?php echo (isset($parsed_search['type']) ? '<i class="icon-filter4"></i>' : '') ?> Type <span class="caret"></span></button>
					<ul class="dropdown-menu pull-right" id="FilterType">
						<li><a href="javascript: filter('type: Import')">Import</a></li>
						<li><a href="javascript: filter('type: Export')">Export</a></li>
						<li><a class="red" href="javascript: filter('type:')">Clear Filter</a></li>
					</ul>
				</div>
			<div class="btn-group">
				<button data-toggle="dropdown" class="btn btn-info dropdown-toggle"><?php echo (isset($parsed_search['vessel']) ? '<i class="icon-filter4"></i>' : '') ?> Vessel <span class="caret"></span></button>
				<ul class="dropdown-menu pull-right nav-menu-scroll" id="FilterVessel"></ul>
			</div>
			<div class="btn-group">
				<button data-toggle="dropdown" class="btn btn-danger dropdown-toggle"><?php echo (isset($parsed_search['cfs']) ? '<i class="icon-filter4"></i>' : '') ?> CFS <span class="caret"></span></button>
				<ul class="dropdown-menu pull-right nav-menu-scroll" id="FilterCFS"></ul>
			</div>
			<div class="btn-group">
				<button data-toggle="dropdown" class="btn btn-success dropdown-toggle"><?php echo (isset($parsed_search['cha']) ? '<i class="icon-filter4"></i>' : '') ?> CHA <span class="caret"></span></button>
				<ul class="dropdown-menu pull-right nav-menu-scroll" id="FilterCHA"></ul>
			</div>
			<div class="btn-group">
				<button data-toggle="dropdown" class="btn btn-warning dropdown-toggle"><?php echo (isset($parsed_search['category']) ? '<i class="icon-filter4"></i>' : '') ?> Category <span class="caret"></span></button>
				<ul class="dropdown-menu pull-right nav-menu-scroll" id="FilterCategory"></ul>
			</div>
			<div class="btn-group">
				<button data-toggle="dropdown" class="btn btn-info dropdown-toggle"><?php echo (isset($parsed_search['product']) ? '<i class="icon-filter4"></i>' : '') ?> Product <span class="caret"></span></button>
				<ul class="dropdown-menu pull-right nav-menu-scroll" id="FilterProduct"></ul>
			</div>
			<div class="btn-group">
				<button data-toggle="dropdown" class="btn btn-danger dropdown-toggle"><?php echo (isset($parsed_search['port']) ? '<i class="icon-filter4"></i>' : '') ?> Port <span class="caret"></span></button>
				<ul class="dropdown-menu pull-right" id="FilterPort"></ul>
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
	<th>No</th>
	<th>Invoice No</th>
	<th>Invoice Date</th>
	<th>Party</th>
	<th>Vessel</th>
	<th>BL / SB</th>
	<th>CFS</th>
	<th>CHA</th>
	<th>Product</th>
	<th>Port</th>
	<th>20'</th>
	<th>40'</th>
</tr>
</thead>

<tbody>
<?php 
$total = array('c20' => 0, 'c40' => 0);
$filter = array(
	'group'    => array(),
	'party'    => array(),
	'vessel'   => array(),
	'cfs'      => array(),
	'cha'      => array(),
	'category' => array(),
	'product'  => array(),
	'port'     => array(),
);
$i = 1;
foreach ($rows as $group_name => $group_rows) {
	$group_total = array('c20' => 0, 'c40' => 0);
	$filter['group'][$group_name] = 1;

	echo '<tr class="SubTotal">
	<td class="bold" colspan="12">' . $group_name . '</td>
</tr>';
	foreach ($group_rows as $r) {
		$group_total['c20'] += $r['container_20'];
		$group_total['c40'] += $r['container_40'];

		$total['c20'] += $r['container_20'];
		$total['c40'] += $r['container_40'];

		$filter['party'][$r['party_name']]     = 1;
		$filter['vessel'][$r['vessel_voyage']] = 1;
		$filter['cfs'][$r['cfs_name']]         = 1;
		$filter['cha'][$r['cha_name']]         = 1;
		$filter['category'][$r['category']]    = 1;
		$filter['product'][$r['product_name']] = 1;
		$filter['port'][$r['indian_port']]     = 1;

		echo '<tr>
	<td class="aligncenter">' . $i++ . '</td>
	<td>' . anchor('/accounting/'.underscore($r['url']), $r['id2_format']) . '</td>
	<td class="aligncenter">' . $r['date'] . '</td>
	<td>' . $r['party_name'] . '</td>
	<td>' . $r['vessel_voyage'] . '</td>
	<td>' . $r['bl_no'] . '</td>
	<td>' . $r['cfs_name'] . '</td>
	<td>' . $r['cha_name'] . '</td>
	<td>' . $r['product_name'] . '</td>
	<td>' . $r['indian_port'] . '</td>
	<td class="alignright">' . $r['container_20'] . '</td>
	<td class="alignright">' . $r['container_40'] . '</td>
</tr>';
	}
	echo '<td class="alignright bold" colspan="10">Total</td>
	<td class="alignright bold">' . $group_total['c20'] . '</td>
	<td class="alignright bold">' . $group_total['c40'] . '</td>
</tr>
';
} 
?>
</tbody>

<tfoot>
<tr>
	<th class="alignright" colspan="10">Total</th>
	<th class="alignright"><?php echo $total['c20'] ?></th>
	<th class="alignright"><?php echo $total['c40'] ?></th>
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
if (count($filter['cfs']) > 0) {
	ksort($filter['cfs']);
	foreach ($filter['cfs'] as $k => $v) {
		echo '$("ul#FilterCFS").append("<li><a href=\"javascript: filter(\'cfs:' . $k . '\')\">' . $k . '</a></li>");';
	}
	echo '$("ul#FilterCFS").append("<li><a class=\"red\" href=\"javascript: filter(\'cfs:\')\">Clear Filter</a></li>");';
}
if (count($filter['cha']) > 0) {
	ksort($filter['cha']);
	foreach ($filter['cha'] as $k => $v) {
		echo '$("ul#FilterCHA").append("<li><a href=\"javascript: filter(\'cha:' . $k . '\')\">' . $k . '</a></li>");';
	}
	echo '$("ul#FilterCHA").append("<li><a class=\"red\" href=\"javascript: filter(\'cha:\')\">Clear Filter</a></li>");';
}
if (count($filter['category']) > 0) {
	ksort($filter['category']);
	foreach ($filter['category'] as $k => $v) {
		echo '$("ul#FilterCategory").append("<li><a href=\"javascript: filter(\'category: ' . str_replace('"', '\"', $k) . '\')\">' . str_replace('"', '\"', $k) . '</a></li>");';
	}
	echo '$("ul#FilterCategory").append("<li><a class=\"red\" href=\"javascript: filter(\'category:\')\">Clear Filter</a></li>");';
}
if (count($filter['product']) > 0) {
	ksort($filter['product']);
	foreach ($filter['product'] as $k => $v) {
		echo '$("ul#FilterProduct").append("<li><a href=\"javascript: filter(\'product: ' . str_replace('"', '\"', $k) . '\')\">' . str_replace('"', '\"', $k) . '</a></li>");';
	}
	echo '$("ul#FilterProduct").append("<li><a class=\"red\" href=\"javascript: filter(\'product:\')\">Clear Filter</a></li>");';
}
if (count($filter['port']) > 0) {
	ksort($filter['port']);
	foreach ($filter['port'] as $k => $v) {
		echo '$("ul#FilterPort").append("<li><a href=\"javascript: filter(\'port:' . $k . '\')\">' . $k . '</a></li>");';
	}
	echo '$("ul#FilterPort").append("<li><a class=\"red\" href=\"javascript: filter(\'port:\')\">Clear Filter</a></li>");';
}
?>
});
</script>
