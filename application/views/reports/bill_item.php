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
							<button type="submit" class="btn btn-primary" id="SearchButton"><i class="fa fa-search"></i> Search</button>
							<a href="#modal-search" class="btn btn-primary" data-toggle="modal"><i class="icon-search-plus"></i></a>
						</span>
					</div>
				</div>
				<div class="col-md-4">
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

		<td>
			<div class="nowrap">
				<button type="button" class="btn btn-group btn-primary" onclick="javascript: clearSearch()"><i class="icon-align-justify"></i></button>
				
				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-info dropdown-toggle"><?php echo (isset($parsed_search['be_no']) ? '<i class="icon-filter4"></i>' : '') ?> BE <span class="caret"></span></button>
					<ul class="dropdown-menu pull-right nav-menu-scroll" id="FilterBE"></ul>
				</div>
				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-info dropdown-toggle"><?php echo (isset($parsed_search['party']) ? '<i class="icon-filter4"></i>' : '') ?> Party <span class="caret"></span></button>
					<ul class="dropdown-menu pull-right nav-menu-scroll" id="FilterParty"></ul>
				</div>
				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-warning dropdown-toggle"><?php echo (isset($parsed_search['billitem']) ? '<i class="icon-filter4"></i>' : '') ?> BillItem <span class="caret"></span></button>
					<ul class="dropdown-menu pull-right nav-menu-scroll" id="FilterBillItem"></ul>
				</div>
				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-danger dropdown-toggle"><?php echo (isset($parsed_search['credit']) ? '<i class="icon-filter4"></i>' : '') ?> Credit <span class="caret"></span></button>
					<ul class="dropdown-menu pull-right nav-menu-scroll" id="FilterCredit"></ul>
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
		<th>BE</th>
		<th>Importer</th>
		<th>Credit Ledger</th>
		<th>Bill No</th>
		<th>Bill Date</th>
		<th>Bill Items</th>
		<th>Amount</th>
	</tr>
	</thead>
	</table>
</div>

<table class="table table-condensed table-striped table-bordered" id="Jobs">
<thead>
<tr>
	<th>No</th>
	<th>Job No</th>
	<th>BE</th>
	<th>Importer</th>
	<th>Credit Ledger</th>
	<th>Bill No</th>
	<th>Bill Date</th>
	<th>Bill Items</th>
	<th>Amount</th>
</tr>
</thead>

<tbody>
<?php 
$i = 1;
$previous = 0;
$total = array('amount' => 0);
$filter = array(
	'be_no'    => array(),
	'party'    => array(),
	'billitem' => array(),
	'credit'   => array(),
);
foreach ($rows as $r) {
	$filter['be_no'][$r['be_no']]             = 1;
	$filter['party'][$r['importer']]          = 1;
	$filter['billitem'][$r['bill_item_code']] = 1;
	$filter['credit'][$r['credit_ledger']]    = 1;

	$total['amount'] =  bcadd($total['amount'], $r['amount'], 2);

	echo '<tr>
	<td class="aligncenter">' . $i++ . '</td>
	<td>' . $r['job_no'] . '</td>
	<td>' . $r['be_no'] . '</td>
	<td>' . $r['importer'] . '</td>
	<td>' . $r['credit_ledger'] . '</td>
	<td class="aligncenter">' . $r['bill_no'] . '</td>
	<td class="aligncenter">' . $r['bill_date'] . '</td>
	<td>' . $r['bill_item'] . '</td>
	<td class="alignright">' . $r['amount'] . '</td>
</tr>';
} 
?>
</tbody>

<tfoot>
<tr>
	<th class="alignright" colspan="8">Total</th>
	<th class="alignright"><?php echo inr_format($total['amount']) ?></th>
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
if (count($filter['be_no']) > 0) {
	ksort($filter['be_no']);
	foreach ($filter['be_no'] as $k => $v) {
		echo '$("ul#FilterBE").append("<li><a href=\"javascript: filter(\'be_no:' . $k . '\')\">' . $k . '</a></li>");';
	}
	echo '$("ul#FilterBE").append("<li><a class=\"red\" href=\"javascript: filter(\'be_no:\')\">Clear Filter</a></li>");';
}
if (count($filter['party']) > 0) {
	ksort($filter['party']);
	foreach ($filter['party'] as $k => $v) {
		echo '$("ul#FilterParty").append("<li><a href=\"javascript: filter(\'party:' . $k . '\')\">' . $k . '</a></li>");';
	}
	echo '$("ul#FilterParty").append("<li><a class=\"red\" href=\"javascript: filter(\'party:\')\">Clear Filter</a></li>");';
}
if (count($filter['billitem']) > 0) {
	ksort($filter['billitem']);
	foreach ($filter['billitem'] as $k => $v) {
		echo '$("ul#FilterBillItem").append("<li><a href=\"javascript: filter(\'billitem:' . $k . '\')\">' . $k . '</a></li>");';
	}
	echo '$("ul#FilterBillItem").append("<li><a class=\"red\" href=\"javascript: filter(\'billitem:\')\">Clear Filter</a></li>");';
}
if (count($filter['credit']) > 0) {
	ksort($filter['credit']);
	foreach ($filter['credit'] as $k => $v) {
		echo '$("ul#FilterCredit").append("<li><a href=\"javascript: filter(\'credit:' . $k . '\')\">' . $k . '</a></li>");';
	}
	echo '$("ul#FilterCredit").append("<li><a class=\"red\" href=\"javascript: filter(\'credit:\')\">Clear Filter</a></li>");';
}
?>
});
</script>