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

<div id="FixedToolbar">
	<table class="table toolbar">
	<tr>
		<td class="nowrap">
			<?php echo form_open($this->uri->uri_string(), 'class="form-search" id="SearchForm"'); ?>
			<input type="hidden" name="search_form" value="1" />
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
			</form>
		</td>
		
		<td>
			<div class="nowrap">
				<button type="button" class="btn btn-group btn-primary" onclick="javascript: clearSearch()"><i class="icon-align-justify"></i></button>

				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-success dropdown-toggle"><?php echo (isset($parsed_search['party']) ? '<i class="icon-filter4"></i>' : '') ?> Party <span class="caret"></span></button>
					<ul class="dropdown-menu nav-menu-scroll pull-right" id="FilterParty"></ul>
				</div>
			</div>
		</td>
	</tr>
	</table>

	<table class="table table-condensed table-striped table-bordered hide" id="FixedHeader">
	<thead>
	<tr>
		<th>No</th>
		<th>Party Name</th>
		<th>Invoice No &amp; Date</th>
		<th>SB No &amp; Date</th>
		<th>Containers</th>
		<th>CFS / Factory</th>
		<th>Actual Pakages</th>
		<th>Actual Gross Weight</th>
		<th>Actual Nett Weight</th>
		<th>Short Packages</th>
		<th>Short Gross Wt</th>
		<th>Short Nett Wt</th>
	</tr>
	</thead>
	</table>
</div>

<table class="table table-condensed table-bordered" id="Jobs">
<thead>
<tr>
	<th>No</th>
	<th>Party Name</th>
	<th>Invoice No &amp; Date</th>
	<th>SB No &amp; Date</th>
	<th>Containers</th>
	<th>CFS / Factory</th>
	<th>Actual Pakages</th>
	<th>Actual Gross Weight</th>
	<th>Actual Nett Weight</th>
	<th>Short Packages</th>
	<th>Short Gross Wt</th>
	<th>Short Nett Wt</th>
</tr>
</thead>

<tbody>
<?php 
$filter = array(
	'party' => array(),
);
$i = 1;
foreach ($rows as $r) {

	$filter['party'][$r['party_name']]   = 1;

	echo '<tr>
	<td class="aligncenter">' . $i++ . '</td>
	<td>' . $r['party_name'] . '</td>
	<td>' . (intval($r['id']) > 0 ? anchor('/export/jobs/edit/'.$r['id'], $r['invoice_no'] . ' / ' . $r['invoice_date'], 'target="_blank"') : '') . '</td>
	<td class="aligncenter">' . $r['sb_no'] . ' / ' . $r['sb_date'] . '</td>
	<td class="aligncenter">' . $r['containers'] . '</td>
	<td>' . $r['cfs_factory'] . '</td>
	<td>' . $r['packages'] . '</td>
	<td>' . $r['gross_weight'] . '</td>
	<td>' . $r['net_weight'] . '</td>
	<td>' . $r['short_packages'] . '</td>
	<td>' . $r['short_gross_weight'] . '</td>
	<td>' . $r['short_net_weight'] . '</td>
</tr>';
} ?>
</tbody>
</table>

<script>

$(document).ready(function() {
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
?>
});
</script>