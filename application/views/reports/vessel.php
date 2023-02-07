<style>

.GroupTotal { background-color: #eee !important; }
.Demmurage { background-color: #ff6666 !important; }

</style>

<div id="modal-search" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><i class="icon-times"></i></button>
				<h3>Advanced Search</h3>
			</div>
			<div class="modal-body form-horizontal">
				<fieldset>
					<?php foreach($search_fields as $f => $n) : ?>
					<div class="form-group">
						<label class="control-label col-md-4"><?php echo humanize($f) ?></label>
						<div class="col-md-8">
							<input type="text" class="form-control form-control-sm AdvancedSearch" name="<?php echo $f ?>" />
						</div>
					</div>
					<?php endforeach; ?>
				</fieldset>
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
			<input type="hidden" name="from_date" value="<?php echo set_value('from_date', $from_date) ?>" id="FromDate" />
			<input type="hidden" name="to_date"   value="<?php echo set_value('to_date', $to_date) ?>" id="ToDate" />
			<div id="ReportRange" class="pull-left" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc">
				<i class="icon-calendar icon-large"></i> <span></span> <b class="caret"></b>
			</div>
		</td>

		<td class="nowrap">
			<div class="row">
				<div class="col-md-8">
					<div class="input-group">
						<input type="text" class="form-control form-control-sm search-query" name="search" value="<?php echo (isset($search) ? $search : '') ?>" id="Search" />
						<span class="input-group-btn">
							<button type="submit" class="btn btn-primary" id="SearchButton"><i class="icon-search icon-white"></i> Search</button>
				      	</span>
					</div>
				</div>
				<div class="col-md-4">
					<a href="#modal-search" class="btn btn-primary" data-toggle="modal"><i class="icon-search-plus"></i></a>
					<a href="#" class="btn btn-info" onclick="javascript: toggleDetails()" data-toggle="button"><i class="icon-eye-slash"></i> Show / Hide Details</a>					
				</div>
			</div>
		</td>

		<td>
			<div class="nowrap">
				<button type="button" class="btn btn-group btn-primary" onclick="javascript: clearSearch()"><i class="icon-align-justify"></i></button>

				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-info dropdown-toggle"><?php echo (isset($parsed_search['party']) ? '<i class="icon-filter4"></i>' : '') ?> Party <span class="caret"></span></button>
					<div class="dropdown-menu" style="padding: 10px; width:500px;">
						<div class="row">
							<div class="col-md-6" id="FilterPartyLeft"></div>
							<div class="col-md-6" id="FilterPartyRight"></div>
						</div>
					</div>
				</div>
				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-success dropdown-toggle"><?php echo (isset($parsed_search['vessel']) ? '<i class="icon-filter4"></i>' : '') ?> Vessel <span class="caret"></span></button>
					<ul class="dropdown-menu" id="FilterVessel"></ul>
				</div>
				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-primary dropdown-toggle"><?php echo (isset($parsed_search['port']) ? '<i class="icon-filter4"></i>' : '') ?> Port <span class="caret"></span></button>
					<ul class="dropdown-menu" id="FilterPort"></ul>
				</div>
				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-info dropdown-toggle"><?php echo (isset($parsed_search['category']) ? '<i class="icon-filter4"></i>' : '') ?> Category <span class="caret"></span></button>
					<ul class="dropdown-menu" id="FilterCategory"></ul>
				</div>
				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-warning dropdown-toggle"><?php echo (isset($parsed_search['product']) ? '<i class="icon-filter4"></i>' : '') ?> Product <span class="caret"></span></button>
					<ul class="dropdown-menu" id="FilterProduct"></ul>
				</div>
				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-danger dropdown-toggle"><?php echo (isset($parsed_search['cha_name']) ? '<i class="icon-filter4"></i>' : '') ?> CHA <span class="caret"></span></button>
					<ul class="dropdown-menu" id="FilterCHA"></ul>
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
		<th>Vessel / Party</th>
		<th>Port / CHA</th>
		<th>BL No</th>
		<th>Product</th>
		<th>Pieces</th>
		<th>CBM</th>
		<th>PGR Date</th>
		<th>OOC Date</th>
		<th>DO Date</th>
	</tr>
	</thead>
	</table>
</div>

<table class="table table-condensed table-striped table-bordered" id="Jobs">
<thead>
<tr>
	<th width="48px">No</th>
	<th>Vessel / Party</th>
	<th>Port / CHA</th>
	<th>BL No</th>
	<th>Product</th>
	<th width="100px">Pieces</th>
	<th width="100px">CBM</th>
	<th width="100px">PGR Date</th>
	<th width="100px">OOC Date</th>
	<th width="100px">DO Date</th>
</tr>
</thead>

<tbody>
<?php 
$i = 0;
$j = 1;
$prev_vessel = 0;
$group_total = array('packages' => 0, 'cbm' => 0);
$total = array('packages' => 0, 'cbm' => 0);
$filter = array(
	'party'    => array(),
	'vessel'   => array(),
	'cha'      => array(),
	'category' => array(),
	'product'  => array(),
	'port'     => array(),
);
$group_row = '';
foreach ($rows as $r) {
	$total['packages'] += $r['packages'];
	$total['cbm']    += $r['cbm'];

	$group_total['packages'] += $r['packages'];
	$group_total['cbm']    += $r['cbm'];

	$filter['party'][$r['party_name']]     = 1;
	$filter['vessel'][$r['vessel_voyage']] = 1;
	$filter['cha'][$r['cha_name']]         = 1;
	$filter['category'][$r['category']]    = 1;
	$filter['product'][$r['product_name']] = 1;
	$filter['port'][$r['indian_port']]     = 1;

	$mark = '';
	if (daysDiff($r['pgr_begin_date'], $r['ooc_date']) > 0)
		$mark = 'Demmurage';
	if (daysDiff($r['pgr_begin_date'], $r['do_date']) > 0)
		$mark = 'Demmurage';

	if ($prev_vessel == 0 OR $prev_vessel != $r['vessel_id']) {
		$i++;
		$j = 1;
		echo $group_row;
		$group_total['packages'] = $r['packages'];
		$group_total['cbm']      = $r['cbm'];

		echo '<tr class="Details hide">
	<td></td>
	<td class="bold blueDark">' . $r['vessel_voyage'] . '</td>
	<td class="bold blueDark">' . $r['indian_port'] . '</td>
	<td colspan="7"></td>
</tr>';
	}

	echo '<tr class="Details hide ' . $mark . '">
	<td class="aligncenter tiny ' . $mark . '">' . $j++ . '</td>
	<td class="tiny ' . $mark . '">' . $r['party_name'] . '</td>
	<td class="tiny ' . $mark . '">' . $r['cha_name'] . '</td>
	<td class="tiny ' . $mark . '">' . $r['bl_no'] . '</td>
	<td class="tiny ' . $mark . '">' . $r['product_name'] . '</td>
	<td class="alignright tiny ' . $mark . '">' . $r['packages'] . '</td>
	<td class="alignright tiny ' . $mark . '">' . $r['cbm'] . '</td>
	<td class="aligncenter tiny ' . $mark . '">' . $r['pgr_begin_date'] . '</td>
	<td class="aligncenter tiny ' . $mark . '">' . $r['ooc_date'] . '</td>
	<td class="aligncenter tiny ' . $mark . '">' . $r['do_date'] . '</td>
</tr>';

	$group_row = '<tr>
	<td class="aligncenter bold GroupTotal">' . $i . '</td>
	<td class="GroupTotal bold">' . $r['vessel_voyage'] . '</td>
	<td class="GroupTotal bold">' . $r['indian_port'] . '</td>
	<td class="GroupTotal"></td>
	<td class="GroupTotal"></td>
	<td class="alignright GroupTotal bold">' . $group_total['packages'] . '</td>
	<td class="alignright GroupTotal bold">' . $group_total['cbm'] . '</td>
	<td class="GroupTotal"></td>
	<td class="GroupTotal"></td>
	<td class="GroupTotal"></td>
</tr>';

	$prev_vessel = $r['vessel_id'];
} 
	echo $group_row;
?>
</tbody>

<tfoot>
<tr>
	<th class="alignright" colspan="5">Total</th>
	<th class="alignright"><?php echo $total['packages'] ?></th>
	<th class="alignright"><?php echo $total['cbm'] ?></th>
	<th></th>
	<th></th>
	<th></th>
</tr>
</tfoot>
</table>

<script type="text/javascript">
var details = 1;
function toggleDetails() {
	if(details) {
		$("tr.Details").removeClass("hide");
		details = 0;

		$("#Jobs").find('thead tr').children().each(function(i, e) {
		    $($("#FixedHeader").find('thead tr').children()[i]).width($(e).width());
		});
		$("#FixedHeader").width($("#Jobs").width());

	} else {
		$("tr.Details").addClass("hide");
		details = 1;
	}
}

function combineSearch() {
	var txtData = [];
	$('input.AdvancedSearch').each(function() {
		if ($(this).val())
			txtData.push($(this).attr('name')+': '+$(this).val());
	});
	$('input#Search').val(txtData.join(" "));
	$("#SearchButton").click();
}

function clearSearch() {
	$('input#Search').val('');
	$("#SearchButton").click();
}

function filter(search) {
	var v = $('input#Search').val();
	$('input#Search').val(v+' '+search);
	$("#SearchButton").click();
}

$(document).ready(function() {
	<?php echo dateRangePicker($years, $from_date, $to_date); ?>

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


	var availableTags = [
		<?php foreach($search_fields as $f => $n)
			echo "\"$f:\",";
		?>
	];
	function split( val ) {
		return val.split( / \s*/ );
	}
	function extractLast( term ) {
		return split( term ).pop();
	}

	$("#Search").on( "keydown", function( event ) {
		if ( event.keyCode === $.ui.keyCode.TAB &&
				$(this).data("ui-autocomplete").menu.active) {
			event.preventDefault();
		}
	})
	.autocomplete({
		minLength: 0,
		source: function( request, response ) {
			// delegate back to autocomplete, but extract the last term
			response( $.ui.autocomplete.filter(
				availableTags, extractLast(request.term)));
		},
		focus: function() {
			// prevent value inserted on focus
			return false;
		},
		select: function( event, ui ) {
			var terms = split( this.value );
			// remove the current input
			terms.pop();
			// add the selected item
			terms.push( ui.item.value );
			// add placeholder to get the comma-and-space at the end
			terms.push("");
			this.value = terms.join(" ");
			return false;
		}
	});

<?php 
if (count($filter['party']) > 0) {
	ksort($filter['party']);
	$i = 0;
	echo '$("#FilterPartyLeft").append("<a class=\"tiny red\" href=\"javascript: filter(\'party:\')\">Clear Filter</a><br />");';
	foreach ($filter['party'] as $k => $v) {
		if (($i++ % 2) == 0)
			echo '$("#FilterPartyRight").append("<a class=\"tiny\" href=\"javascript: filter(\'party: ' . $k . '\')\">' . $k . '</a><br />");';
		else
			echo '$("#FilterPartyLeft").append("<a class=\"tiny\" href=\"javascript: filter(\'party: ' . $k . '\')\">' . $k . '</a><br />");';
	}
}
if (count($filter['vessel']) > 0) {
	ksort($filter['vessel']);
	foreach ($filter['vessel'] as $k => $v) {
		echo '$("ul#FilterVessel").append("<li><a href=\"javascript: filter(\'vessel: ' . $k . '\')\">' . $k . '</a></li>");';
	}
	echo '$("ul#FilterVessel").append("<li><a class=\"red\" href=\"javascript: filter(\'vessel:\')\">Clear Filter</a></li>");';
}
if (count($filter['cha']) > 0) {
	ksort($filter['cha']);
	foreach ($filter['cha'] as $k => $v) {
		echo '$("ul#FilterCHA").append("<li><a href=\"javascript: filter(\'cha: ' . $k . '\')\">' . $k . '</a></li>");';
	}
	echo '$("ul#FilterCHA").append("<li><a class=\"red\" href=\"javascript: filter(\'cha:\')\">Clear Filter</a></li>");';
}
if (count($filter['category']) > 0) {
	ksort($filter['category']);
	foreach ($filter['category'] as $k => $v) {
		echo '$("ul#FilterCategory").append("<li><a href=\"javascript: filter(\'category: ' . $k . '\')\">' . $k . '</a></li>");';
	}
	echo '$("ul#FilterCategory").append("<li><a class=\"red\" href=\"javascript: filter(\'category:\')\">Clear Filter</a></li>");';
}
if (count($filter['product']) > 0) {
	ksort($filter['product']);
	foreach ($filter['product'] as $k => $v) {
		echo '$("ul#FilterProduct").append("<li><a href=\"javascript: filter(\'product: ' . $k . '\')\">' . $k . '</a></li>");';
	}
	echo '$("ul#FilterProduct").append("<li><a class=\"red\" href=\"javascript: filter(\'product:\')\">Clear Filter</a></li>");';
}
if (count($filter['port']) > 0) {
	ksort($filter['port']);
	foreach ($filter['port'] as $k => $v) {
		echo '$("ul#FilterPort").append("<li><a href=\"javascript: filter(\'port: ' . $k . '\')\">' . $k . '</a></li>");';
	}
	echo '$("ul#FilterPort").append("<li><a class=\"red\" href=\"javascript: filter(\'port:\')\">Clear Filter</a></li>");';
}
?>
});
</script>