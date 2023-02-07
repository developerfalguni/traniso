<style>
.Date, .BLDate { width: 70px; }
td.markRed { background-color: #F33 !important;}
td.markYellow { background-color: #ffc !important;}
td.Pending	 { }
td.Bills { background-color: #7BD57B !important; }
td.Completed { background-color: #FFD5B0 !important; }

</style>

<div id="modal-status" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><i class="icon-times"></i></button>
				<h3>Change Job Status</h3>
			</div>
			<div class="modal-body">
				<div class="btn-toolbar">
					<div class="btn-group" data-toggle="buttons-radio">
						<a class="btn btn-default" href="javascript: setStatus(0)"><i class="icon-clock"></i> Pending</a>
						<a class="btn btn-success" href="javascript: setStatus(1)"><i class="icon-thumbs-o-up"></i> Bills</a>
						<a class="btn btn-danger" href="javascript: setStatus(2)"><i class="icon-check"></i> Completed</a>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<!-- <button type="button" class="btn btn-primary" onclick="javascript: combineSearch()"><i class="fa fa-search"></i> Search</button> -->
			</div>
		</div>
	</div>
</div>


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
		<!-- <td width="25%" class="nowrap"><h3><?php echo humanize($page_title) . ' <small>' . $page_desc . '</small>'; ?></h3></td> -->
		<td class="nowrap">
			<?php echo form_open($this->uri->uri_string(), 'class="form-search" id="SearchForm"'); ?>
			<input type="hidden" name="search_form" value="1" />
			<input type="hidden" name="sortby" value="<?php echo $sortby ?>" id="SortBy" />
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

		<td width="25%" class="alignright nowrap">
			<?php echo anchor('tracking/icegate_sb/captcha', '<i class="icon-refresh"></i>', 'class="btn btn-info Popup" data-placement="bottom" rel="tooltip" data-original-title="IceGate Captcha"') . '&nbsp;' . 
				anchor($this->uri->uri_string(), '<i class="icon-refresh"></i>', ' class="btn btn-primary" data-placement="bottom" rel="tooltip" data-original-title="Refresh"') . '&nbsp;
				<button type="button" class="btn btn-success" id="Update" onclick="javascript: Save()">Update</button>&nbsp;' . 
				anchor("/export/jobs/edit/0/Container", '<i class="fa fa-plus"></i> Add Shipment', 'class="btn btn-success"'); ?>
		</td>
	</tr>
	</table>

	<table class="table table-condensed table-striped table-bordered hide" id="FixedHeader">
	<thead>
	<tr>
		<th>S</th>
		<th>No</th>
		<th>Job No</th>
		<th>Party Name</th>
		<th>Invoice No / Date</th>
		<th>SB No / Date</th>
		<th>Cont.</th>
		<th>Shipping Line</th>
		<th>Vessel / <span class="green">ETD</span></th>
		<th>BL No / Date</th>
		<th>POL</th>
		<th>POD</th>
		<th>CFS</th>
		<th>IceGate</th>
	</tr>
	</thead>
	</table>
</div>

<?php echo form_open($this->uri->uri_string(), array('class' => 'form-horizontal', 'id' => 'PendingForm')); ?>
<table class="table table-condensed table-striped table-bordered" id="Jobs">
<thead>
<tr>
	<th>S</th>
	<th>No</th>
	<th>Job No</th>
	<th>Party Name</th>
	<th>Invoice No / Date</th>
	<th>SB No / Date</th>
	<th>Cont.</th>
	<th>Shipping Line</th>
	<th>Vessel / <span class="green">ETD</span></th>
	<th>BL No / Date</th>
	<th>POL</th>
	<th>POD</th>
	<th>CFS</th>
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
	
	$filter['party'][$r['party_name']]     = 1;
	$filter['line'][$r['line_name']]       = 1;
	$filter['vessel'][$r['vessel_name']]   = 1;
	$filter['pol'][$r['pol']]              = 1;
	$filter['pod'][$r['pod']]              = 1;

	echo '<tr>
	<td class="big aligncenter ' . $r['job_status'] . '"><input type="hidden" name="job_status[' . $r['job_id'] . ']" value="' . $r['job_status'] . '" class="Status_' . $r['job_id'] . '" /><a href="javascript: showStatus('.$r['job_id'].')"><i class="';
	if ($r['job_status'] == 'Pending') echo 'icon-clock';
	else if ($r['job_status'] == 'Bills') echo 'icon-thumbs-o-up';
	else if ($r['job_status'] == 'Completed') echo 'icon-check';
	echo '"></i></td>
	<td class="' . $r['job_status'] . ' aligncenter">' . $i++ . '</td>
	<td class="' . $r['job_status'] . ' aligncenter">' . anchor('/export/jobs/edit/' . $r['job_id'], $r['id2_format'], 'target="_blank"') . '</td>
	<td class="' . $r['job_status'] . ' tiny">' . $r['party_name'] . '</td>
	<td class="' . $r['job_status'] . ' tiny red">' . $r['invoice_no'] . '<br />' . $r['invoice_date'] . '</td>
	<td class="' . $r['job_status'] . ' tiny blue">' . $r['sb_no'] . '<br />' . $r['sb_date'] . '</td>
	<td class="' . $r['job_status'] . ' tiny">' . $r['containers'] . '</td>
	<td class="' . $r['job_status'] . ' tiny">' . character_limiter($r['line_name'], 20) . '</td>
	<td class="' . $r['job_status'] . ' tiny">' . $r['vessel_name'] . '<br /><span class="green">' . $r['etd_date'] . '</span></td>
	<td class="' . $r['job_status'] . ' tiny blue">' . $r['bl_no'] . '<br />' . $r['bl_date'] . '</td>
	<td class="' . $r['job_status'] . ' tiny">' . $r['pol'] . '</td>
	<td class="' . $r['job_status'] . ' tiny">' . $r['pod'] . '</td>
	<td class="' . $r['job_status'] . ' tiny">' . character_limiter($r['cfs_name'], 20) . '</td>
	<td class="' . $r['job_status'] . ' nowrap">';
	if (strlen($r['leo_date']) > 0)
		echo anchor('/tracking/icegate_sb/index/'. $r['child_job_id'], '<span class="label label-success">' . $r['leo_date'] . '</span><span class="label label-warning">' . $r['ep_copy_print_status'] . '</span><span class="label label-info">' . $r['print_status'] . '</span>', 'target="_blank"') . '<br />';
	else
		echo '<span class="label label-red">N/A</span><br />';

	echo '<div class="pink tiny alignright">' . moment($r['last_fetched']) . '</div></td>
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

$(".Date").datepicker({
	duration: '',
	dateFormat: "dd-mm-yy",
	yearRange: "-5:+1",
	mandatory: true,
	showButtonPanel: true,
	changeMonth: true,
	changeYear: true,
	showOtherMonths: true,
	showStatus: true
});

$(".BLDate").datepicker({
	duration: '',
	dateFormat: "dd-mm-yy",
	minDate: '-2',
	maxDate: '0',
	mandatory: true,
	showButtonPanel: true,
	changeMonth: true,
	changeYear: true,
	showOtherMonths: true,
	showStatus: true,
	showOn: "focus",
	buttonImage: "<?php echo base_url('images/calendar.png') ?>",
	buttonImageOnly: true
});

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