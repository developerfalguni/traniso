<style>

.GroupTotal { background-color: #eee !important; }

</style>

<div id="modal-search" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><i class="icon-times"></i></button>
				<h3>Advanced Search</h3>
			</div>
			<div class="modal-body form-horizontal">
				<table class="table table-condensed table-striped">
				<tbody>
				<?php foreach($search_fields as $f => $n) {
				echo "<tr>
					<td class=\"alignright alignmiddle\">".humanize($f)." :</td>
					<td><input type=\"text\" class=\"form-control AdvancedSearch\" name=\"$f\" value=\"" . (isset($parsed_search[$f]) ? $parsed_search[$f] : '') . "\" /></td>
					</tr>";
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
			<div class="input-group">
				<input type="text" class="form-control form-control-sm search-query input-medium" name="search" value="<?php echo (isset($search) ? $search : '') ?>" id="Search" />
				<button type="submit" class="btn btn-primary" id="SearchButton"><i class="fa fa-search"></i> Search</button>
			</div>
			<a href="#modal-search" class="btn btn-primary" data-toggle="modal"><i class="icon-search-plus"></i></a> 
			<div class="btn-group">
				<?php 
				/*echo anchor($this->_clspath.$this->_class."/preview", 'Preview', 'class="btn btn-default Popup"') .
					anchor($this->_clspath.$this->_class."/preview/1", 'PDF', 'class="btn btn-default Popup"') .
					anchor($this->_clspath.$this->_class."/excelInvoice", 'Excel', 'class="btn btn-warning"') */
				?>
			</div>
			<a href="#" class="btn btn-info" onclick="javascript: toggleDetails()" data-toggle="button"><i class="icon-eye-slash"></i></a>
		</td>
		
		<td>
			<div class="nowrap">
				<button type="button" class="btn btn-group btn-primary" onclick="javascript: clearSearch()"><i class="icon-align-justify"></i></button>

				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-success dropdown-toggle"><?php echo (isset($parsed_search['vehicle']) ? '<i class="icon-filter4"></i>' : '') ?> Vehicle <span class="caret"></span></button>
					<ul class="dropdown-menu nav-menu-scroll" id="FilterVehicle"></ul>
				</div>

				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-info dropdown-toggle"><?php echo (isset($parsed_search['operator']) ? '<i class="icon-filter4"></i>' : '') ?> Operator <span class="caret"></span></button>
					<ul class="dropdown-menu pull-right nav-menu-scroll" id="FilterOperator"></ul>
				</div>

				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-warning dropdown-toggle"><?php echo (isset($parsed_search['location']) ? '<i class="icon-filter4"></i>' : '') ?> Location <span class="caret"></span></button>
					<ul class="dropdown-menu pull-right nav-menu-scroll" id="FilterLocation"></ul>
				</div>

				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-danger dropdown-toggle"><?php echo (isset($parsed_search['purpose']) ? '<i class="icon-filter4"></i>' : '') ?> Purpose <span class="caret"></span></button>
					<ul class="dropdown-menu pull-right nav-menu-scroll" id="FilterPurpose"></ul>
				</div>

				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-success dropdown-toggle"><?php echo (isset($parsed_search['fuel']) ? '<i class="icon-filter4"></i>' : '') ?> Fuel <span class="caret"></span></button>
					<ul class="dropdown-menu pull-right nav-menu-scroll" id="FilterType">
						<li><a href="javascript: filter('fuel: Tanker')">Tanker</a></li>
						<li><a href="javascript: filter('fuel: Pump')">Pump</a></li>
						<li><a class="red" href="javascript: filter('fuel:')">Clear Filter</a></li>
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
		<th>Date</th>
		<th>Vehicle No</th>
		<th>Total Running Hrs.</th>
		<th>Operator</th>
		<th>Operator 2</th>
		<th>Location</th>
		<th>Purpose</th>
		<th>Fuel Location</th>
		<th>Fuel Reading Supervisor</th>
		<th>Fuel Reading Sensor</th>
		<th>Difference</th>
		<th>Pilferage</th>
		<th>Avg. / Hr</th>
	</tr>
	</thead>
	</table>
</div>

<table class="table table-condensed table-striped table-bordered" id="Result">
<thead>
<tr>
	<th>No</th>
	<th>Date</th>
	<th>Vehicle No</th>
	<th>Total Running Hrs.</th>
	<th>Operator</th>
	<th>Operator 2</th>
	<th>Location</th>
	<th>Purpose</th>
	<th>Fuel Location</th>
	<th>Fuel Reading Supervisor</th>
	<th>Fuel Reading Sensor</th>
	<th>Difference</th>
	<th>Pilferage</th>
	<th>Avg. / Hr</th>
</tr>
</thead>

<tbody>
<?php 
$total_running_hrs        = 0;
$supervisor_reading_total = 0;
$sensor_reading_total     = 0;
$filter = array(
	'vehicle'  => array(),
	'operator' => array(),
	'location' => array(),
	'purpose'  => array(),
	'fuel'     => array(),
);
$i = 1;
foreach ($rows['summary'] as $r) {
	//$total_running_hrs        += $r['running_hrs'];
	// $supervisor_reading_total += $r['fuel_reading_supervisor'];
	// $sensor_reading_total     += $r['fuel_reading_sensor'];

	$location = explode(', ', $r['location']);
	foreach ($location as $l)
		$filter['location'][$l] = 1;

	$purpose = explode(', ', $r['purpose']);
	foreach ($purpose as $p)
		$filter['purpose'][$p] = 1;

	$operator = explode(', ', $r['operator']);
	foreach ($operator as $o)
		$filter['operator'][$o] = 1;

	$filter['vehicle'][$r['registration_no']] = 1;
	$filter['operator'][$r['operator2']]      = 1;
	$filter['fuel'][$r['fuel_location']]      = 1;
	
	echo '<tr>
	<td class="GroupTotal aligncenter">' . $i++ . '</td>
	<td class="GroupTotal aligncenter nowrap">' . $r['date'] . '</td>
	<td class="GroupTotal aligncenter nowrap">' . $r['registration_no'] . '</td>
	<td class="GroupTotal alignright">' . $r['running_hrs'] . '</td>
	<td class="GroupTotal">' . $r['operator'] . '</td>
	<td class="GroupTotal">' . $r['operator2'] . '</td>
	<td class="GroupTotal">' . $r['location'] . '</td>
	<td class="GroupTotal">' . $r['purpose'] . '</td>
	<td class="GroupTotal aligncenter">' . $r['fuel_location'] . '</td>
	<td class="GroupTotal alignright">' . $r['fuel_reading_supervisor'] . '</td>
	<td class="GroupTotal alignright">' . $r['fuel_reading_sensor'] . '</td>
	<td class="GroupTotal alignright">' . $r['difference'] . '</td>
	<td class="GroupTotal alignright">' . $r['pilferage'] . '</td>
	<td class="GroupTotal alignright">' . $r['average_hr'] . '</td>
</tr>';
	foreach ($rows['details'][$r['id']] as $d) {
		$total_running_hrs        += $d['running_hrs'];
		$supervisor_reading_total += $d['fuel_reading_supervisor'];
		$sensor_reading_total     += $d['fuel_reading_sensor'];

		echo '<tr class="hide tiny Details">
	<td class="aligncenter"></td>
	<td class="aligncenter nowrap">' . anchor('/master/vehicle_data/edit/'.$d['date'], $d['date'], 'target="_blank"') . '</td>
	<td class="aligncenter nowrap">"</td>
	<td class="alignright">' . $d['running_hrs'] . '</td>
	<td>' . $d['operator'] . '</td>
	<td>' . $d['operator2'] . '</td>
	<td>' . $d['location'] . '</td>
	<td>' . $d['purpose'] . '</td>
	<td class="aligncenter">' . $d['fuel_location'] . '</td>
	<td class="alignright">' . $d['fuel_reading_supervisor'] . '</td>
	<td class="alignright">' . $d['fuel_reading_sensor'] . '</td>
	<td class="alignright">' . $d['difference'] . '</td>
	<td class="alignright">' . $d['pilferage'] . '</td>
	<td class="alignright">' . $d['average_hr'] . '</td>
</tr>';
	}
}
?>
</tbody>

<tfoot>
<tr>
	<th colspan="3" class="alignright">Total</th>
	<th class="alignright"><?php echo $total_running_hrs ?></th>
	<th colspan="5" class="alignright"></th>
	<th class="alignright"><?php echo $supervisor_reading_total ?></th>
	<th class="alignright"><?php echo $sensor_reading_total ?></th>
	<th></th>
	<th></th>
	<th></th>
</tr>
</tfoot>
</table>

<script>
var details = 1;
function toggleDetails() {
	if(details) {
		$("tr.Details").removeClass("hide");
		details = 0;

		$("#Result").find('thead tr').children().each(function(i, e) {
		    $($("#FixedHeader").find('thead tr').children()[i]).width($(e).width());
		});
		$("#FixedHeader").width($("#Result").width());

	} else {
		$("tr.Details").addClass("hide");
		details = 1;
	}
}


$(document).ready(function() {
	<?php echo 'dateRangePicker({
		years:     ['.$years[0].', '.$years[1].'],
		from_date: \''.convDate($from_date).'\',
		to_date:   \''.convDate($to_date).'\'
	});' ?>

	$("#Result").find('thead tr').children().each(function(i, e) {
	    $($("#FixedHeader").find('thead tr').children()[i]).width($(e).width());
	});
	$("#FixedHeader").width($("#Result").width());

	if (!($.browser == "msie" && $.browser.version < 7)) {
        var target = "div#FixedToolbar";
        $(window).scroll(function(event) {
            $(target).css({
			 	left: ($("#Result").offset().left - $(window).scrollLeft()) + 'px'
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
if (count($filter['vehicle']) > 0) {
	ksort($filter['vehicle']);
	foreach ($filter['vehicle'] as $k => $v) {
		echo '$("ul#FilterVehicle").append("<li><a href=\"javascript: filter(\'vehicle:' . $k . '\')\">' . $k . '</a></li>");';
	}
	echo '$("ul#FilterVehicle").append("<li><a class=\"red\" href=\"javascript: filter(\'vehicle:\')\">Clear Filter</a></li>");';
}
if (count($filter['operator']) > 0) {
	ksort($filter['operator']);
	foreach ($filter['operator'] as $k => $v) {
		echo '$("ul#FilterOperator").append("<li><a href=\"javascript: filter(\'operator:' . $k . '\')\">' . $k . '</a></li>");';
	}
	echo '$("ul#FilterOperator").append("<li><a class=\"red\" href=\"javascript: filter(\'operator:\')\">Clear Filter</a></li>");';
}
if (count($filter['location']) > 0) {
	ksort($filter['location']);
	foreach ($filter['location'] as $k => $v) {
		echo '$("ul#FilterLocation").append("<li><a href=\"javascript: filter(\'location:' . $k . '\')\">' . $k . '</a></li>");';
	}
	echo '$("ul#FilterLocation").append("<li><a class=\"red\" href=\"javascript: filter(\'location:\')\">Clear Filter</a></li>");';
}
if (count($filter['purpose']) > 0) {
	ksort($filter['purpose']);
	foreach ($filter['purpose'] as $k => $v) {
		echo '$("ul#FilterPurpose").append("<li><a href=\"javascript: filter(\'purpose:' . $k . '\')\">' . $k . '</a></li>");';
	}
	echo '$("ul#FilterPurpose").append("<li><a class=\"red\" href=\"javascript: filter(\'purpose:\')\">Clear Filter</a></li>");';
}
?>
});
</script>