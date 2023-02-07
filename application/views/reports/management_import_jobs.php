<style>
.Date, .BLDate { width: 75px; }
.markRed { 
	font-weight: bold;
	color: #fff;
	background-color: #F33 !important;
}
.markGreen { 
	font-weight: bold;
	color: #fff;
	background-color: #46a546 !important; 
}
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

<table class="table toolbar">
<tr>
	<td>
		<?php echo form_open($this->uri->uri_string(), 'class="form-search" id="SearchForm"'); ?>
			<input type="hidden" name="search_form" value="1" />
		<div class="input-group">
			<input type="text" class="form-control form-control-sm search-query" name="search" value="<?php echo (isset($search) ? $search : '') ?>" id="Search" />
			<span class="input-group-btn">
				<button type="submit" class="btn btn-primary" id="SearchButton"><i class="fa fa-search"></i> Search</button>
				<a href="#modal-search" class="btn btn-primary" data-toggle="modal"><i class="icon-search-plus"></i></a>
			</span>
		</div>
		</form>
	</td>

	<td>
		<button type="button" class="btn btn-group btn-primary" onclick="javascript: clearSearch()"><i class="icon-align-justify"></i></button>

		<div class="btn-group">
			<button data-toggle="dropdown" class="btn btn-info dropdown-toggle"><?php echo (isset($parsed_search['party']) ? '<i class="icon-filter4"></i>' : '') ?> Party <span class="caret"></span></button>
			<ul class="dropdown-menu nav-menu-scroll pull-right" id="FilterParty">
				<li><a class="red" href="javascript: filter('party:')">Clear Filter</a></li>
			</ul>
		</div>

		<div class="btn-group">
			<button data-toggle="dropdown" class="btn btn-info dropdown-toggle"><?php echo (isset($parsed_search['hss']) ? '<i class="icon-filter4"></i>' : '') ?> HSS <span class="caret"></span></button>
			<ul class="dropdown-menu nav-menu-scroll pull-right" id="FilterHSS">
				<li><a class="red" href="javascript: filter('hss:')">Clear Filter</a></li>
			</ul>
		</div>

		<div class="btn-group">
			<button data-toggle="dropdown" class="btn btn-info dropdown-toggle"><?php echo (isset($parsed_search['line']) ? '<i class="icon-filter4"></i>' : '') ?> Line <span class="caret"></span></button>
			<ul class="dropdown-menu nav-menu-scroll pull-right" id="FilterLine">
				<li><a class="red" href="javascript: filter('line:')">Clear Filter</a></li>
			</ul>
		</div>

		<div class="btn-group">
			<button data-toggle="dropdown" class="btn btn-info dropdown-toggle"><?php echo (isset($parsed_search['cha']) ? '<i class="icon-filter4"></i>' : '') ?> CHA <span class="caret"></span></button>
			<ul class="dropdown-menu nav-menu-scroll pull-right" id="FilterCHA">
				<li><a class="red" href="javascript: filter('cha:')">Clear Filter</a></li>
			</ul>
		</div>

		<div class="btn-group">
			<button data-toggle="dropdown" class="btn btn-info dropdown-toggle"><?php echo (isset($parsed_search['vessel']) ? '<i class="icon-filter4"></i>' : '') ?> Vessel <span class="caret"></span></button>
			<ul class="dropdown-menu nav-menu-scroll pull-right" id="FilterVessel">
				<li><a class="red" href="javascript: filter('vessel:')">Clear Filter</a></li>
			</ul>
		</div>
	</td>
</tr>
</table>

<?php echo form_open($this->uri->uri_string(), array('class' => 'form-horizontal', 'id' => 'PendingForm')); ?>
<input type="hidden" name="tracking_form" value="1" />

<table class="table table-condensed table-striped table-bordered" id="Jobs">
<thead>
<tr>
	<th>S</th>
	<th>Job No</th>
	<th>Party Name / <span class="orange">HSS</span></th>
	<th>BL No / Date / BL Wt.</th>
	<th>Cont.</th>
	<th>Shipping Line / <span class="orange">CHA</span></th>
	<th>Vessel / POD</th>
	<th>ETA</th>
	<th>Free Days</th>
	<th>BL Rcvd.</th>
	<th>Remarks</th>
</tr>
</thead>

<tbody>
<?php
$filter = array(
	'party'  => array(),
	'hss'    => array(),
	'line'   => array(),
	'cha'    => array(),
	'vessel' => array(),
);
$prev_vessel = 0;
$alt_color = '#f0f0f0';
foreach ($rows as $p) {
	$filter['party'][$p['party_name']]   = 1;
	$filter['hss'][$p['high_seas']]      = 1;
	$filter['line'][$p['line_name']]     = 1;
	$filter['cha'][$p['cha_name']]       = 1;
	$filter['vessel'][$p['vessel_name']] = 1;

	if (intval($p['vessel_id']) > 0) {
		$vessel   = $p['vessel_name'] . ' ' . $p['voyage_no'];
		$eta_date = $p['eta_date'];
	}
	else {
		$vessel   = $p['temp_vessel_name'];
		$eta_date = $p['temp_eta'];
	}

	echo '<tr id="'.$p['id'].'">
	<td class="big aligncenter '.$p['status'].'"><i class="icon-time2"></i>' .
		($p['house_bl'] == 'Yes' ? '<br /><i class="icon-home"></i>' : '') . '</td>
	<td class="aligncenter">' . anchor('/import/import_detail/edit/' . $p['job_id'], $p['id2_format'], 'target="_blank"') . '</td>
	<td class="tiny '.$p['status'].'">' . $p['party_name'] . ($p['high_seas'] == null ? '' : '<br /><span class="orange bold">' . $p['high_seas'] . '</span>') . '</td>
	<td>' . $p['bl_no'] . '<br /><span class="tiny orange">' . $p['bl_date'] . '</span></td>
	<td class="tiny">' . $p['containers'] . '</td>
	<td class="tiny">' . $p['line_name'] . '<br /><span class="tiny orange">' . $p['cha_name']  . '</span></td>
	<td class="tiny Vessel" vessel_name="' . $vessel . '">' . (intval($p['vessel_id']) == 0 ? 
			'<span class="VesselName">' . $vessel . '</span>' : 
			$vessel
		) . '<br /><span class="tiny orange">' . $p['indian_port'] . '</span></td>
	
	<!-- Mark entry RED if ETA is Less than or equal to Todays date -->
	<td class="' . (($eta_date != '00-00-0000' && daysDiff(date('d-m-Y'), $eta_date, 'd-m-Y') <= 1 && $p['status'] == 'Pending') ? 'markRed' : null) . '"><input type="hidden" class="form-control form-control-sm Numeric Date" name="vessel_id[' . $p['id'] . ']" value="' . intval($p['vessel_id']) . '" />' . $eta_date . '</td>

	<td>'.$p['free_days_upto'].' <span class="badge badge-default">'.$p['free_days'].'</span></td>
	
	<!-- Mark entry GREEN if BL Date is Less not Zero -->
	<td class=" ' . ($p['original_bl_received'] != '00-00-0000' ? ' markGreen' : null) . '" >' . $p['original_bl_received'] . '</td>

	<td class="tiny"><span class="Remarks">' . $p['remarks'] . '</span></td>

	</tr>';

	$prev_vessel = $p['vessel_id'];
}
?>
</tbody>
</table>
</form>

<script>
var payment_id = 0;
var pending_id = 0;

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

function showStatus(id) {
	$("#form-status #RowID").val(id);
	$('#modal-status').modal();
}

function setStatus(status) {
	switch(status) {
		case 0: $('#Status_'+pending_id).val('Pending');   break;
		case 1: $('#Status_'+pending_id).val('Program');   break;
		case 2: $('#Status_'+pending_id).val('Delivery');  break;
		case 3: $('#Status_'+pending_id).val('Bills');     break;
		case 4: $('#Status_'+pending_id).val('Completed'); break;
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
    $('a[rel=popover]').popover({html: true, trigger: 'click'});

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

	$("#Search")
		// don't navigate away from the field on tab when selecting an item
		.bind( "keydown", function( event ) {
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
	foreach ($filter['party'] as $k => $v) {
		echo '$("ul#FilterParty").append("<li><a href=\"javascript: filter(\'party: ' . $k . '\')\">' . $k . '</a></li>");';
	}
}
if (count($filter['hss']) > 0) {
	ksort($filter['hss']);
	foreach ($filter['hss'] as $k => $v) {
		echo '$("ul#FilterHSS").append("<li><a href=\"javascript: filter(\'hss: ' . $k . '\')\">' . $k . '</a></li>");';
	}
}
if (count($filter['line']) > 0) {
	ksort($filter['line']);
	foreach ($filter['line'] as $k => $v) {
		echo '$("ul#FilterLine").append("<li><a href=\"javascript: filter(\'line: ' . $k . '\')\">' . $k . '</a></li>");';
	}
}
if (count($filter['cha']) > 0) {
	ksort($filter['cha']);
	foreach ($filter['cha'] as $k => $v) {
		echo '$("ul#FilterCHA").append("<li><a href=\"javascript: filter(\'cha: ' . $k . '\')\">' . $k . '</a></li>");';
	}
}
if (count($filter['vessel']) > 0) {
	ksort($filter['vessel']);
	foreach ($filter['vessel'] as $k => $v) {
		echo '$("ul#FilterVessel").append("<li><a href=\"javascript: filter(\'vessel: ' . $k . '\')\">' . $k . '</a></li>");';
	}
}
?>
});
</script>