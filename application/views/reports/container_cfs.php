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
				echo anchor($this->_clspath.$this->_class."/preview2", '<i class="icon-file-o"></i>', 'class="btn btn-default Popup"') .
				anchor($this->_clspath.$this->_class."/preview2/1", '<i class="icon-file-pdf"></i>', 'class="btn btn-default Popup"') .
				anchor($this->_clspath.$this->_class."/excel2", '<i class="icon-file-excel"></i>', 'class="btn btn-warning"') 
				?>
				</div>
			</div>
		</div>
	</td>

	<td class="nowrap">
		<div class="nowrap">
			<button type="button" class="btn btn-group btn-primary" onclick="javascript: clearSearch()"><i class="icon-align-justify"></i></button>
			
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
				<button data-toggle="dropdown" class="btn btn-info dropdown-toggle"><?php echo (isset($parsed_search['line']) ? '<i class="icon-filter4"></i>' : '') ?> Line <span class="caret"></span></button>
				<ul class="dropdown-menu pull-right nav-menu-scroll" id="FilterLine"></ul>
			</div>
			<div class="btn-group">
				<button data-toggle="dropdown" class="btn btn-danger dropdown-toggle"><?php echo (isset($parsed_search['cfs']) ? '<i class="icon-filter4"></i>' : '') ?> CFS <span class="caret"></span></button>
				<ul class="dropdown-menu pull-right nav-menu-scroll" id="FilterCFS"></ul>
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
	<th>Voucher No</th>
	<th>Bill No</th>
	<th>Bill Date</th>
	<th>Party</th>
	<th>BL</th>
	<th>BE / SB</th>
	<th>Line</th>
	<th>CFS</th>
	<th>20'</th>
	<th>40'</th>
</tr>
</thead>

<tbody>
<?php 
$total = array('c20' => 0, 'c40' => 0);
$filter = array(
	'party'  => array(),
	'vessel' => array(),
	'line'   => array(),
	'cfs'    => array(),
);
$i = 1;
foreach ($rows as $group_name => $group_rows) {
	foreach ($group_rows as $r) {
		$total['c20'] += $r['container_20'];
		$total['c40'] += $r['container_40'];

		$filter['party'][$r['party_name']]     = 1;
		$filter['vessel'][$r['vessel_voyage']] = 1;
		$filter['line'][$r['line_name']]       = 1;
		$filter['cfs'][$r['cfs_name']]         = 1;

		echo '<tr>
	<td class="aligncenter">' . $i++ . '</td>
	<td>' . anchor('/accounting/'.underscore($r['url']), $r['id2_format']) . '</td>
	<td>' . $r['invoice_no'] . '</td>
	<td class="aligncenter">' . $r['invoice_date'] . '</td>
	<td>' . $r['party_name'] . '</td>
	<td>' . anchor(underscore($r['type']) . '/jobs/edit/' . $r['job_id'], $r['bl_no'], 'target="_blank"') . '</td>
	<td>' . $r['be_sb'] . '</td>
	<td>' . $r['line_name'] . '</td>
	<td>' . $r['cfs_name'] . '</td>
	<td class="alignright">' . $r['container_20'] . '</td>
	<td class="alignright">' . $r['container_40'] . '</td>
</tr>';
	}
} 
?>
</tbody>

<tfoot>
<tr>
	<th class="alignright" colspan="9">Total</th>
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
if (count($filter['party']) > 0) {
	ksort($filter['party']);
	foreach ($filter['party'] as $k => $v) {
		echo '$("ul#FilterParty").append("<li><a href=\"javascript: filter(\'party:' . $k . '\')\">' . $k . '</a></li>");';
	}
	echo '$("ul#FilterParty").append("<li><a class=\"red\" href=\"javascript: filter(\'party:\')\">Clear Filter</a></li>");';
}
if (count($filter['line']) > 0) {
	ksort($filter['line']);
	foreach ($filter['line'] as $k => $v) {
		echo '$("ul#FilterLine").append("<li><a href=\"javascript: filter(\'line:' . $k . '\')\">' . $k . '</a></li>");';
	}
	echo '$("ul#FilterLine").append("<li><a class=\"red\" href=\"javascript: filter(\'line:\')\">Clear Filter</a></li>");';
}
if (count($filter['cfs']) > 0) {
	ksort($filter['cfs']);
	foreach ($filter['cfs'] as $k => $v) {
		echo '$("ul#FilterCFS").append("<li><a href=\"javascript: filter(\'cfs:' . $k . '\')\">' . $k . '</a></li>");';
	}
	echo '$("ul#FilterCFS").append("<li><a class=\"red\" href=\"javascript: filter(\'cfs:\')\">Clear Filter</a></li>");';
}
?>
});
</script>
