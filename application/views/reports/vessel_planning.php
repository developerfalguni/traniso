<style>
.EmailSent { background-color: #1BDB1A !important; }
tr.GateIn td { background-color: #FFA; }
tr.DocIn td { background-color: #66ccff; }
tr.Completed td { background-color: #6f6; }
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

<div id="modal-email" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
		<?php echo form_open($this->_clspath.$this->_class.'/preview/0/1', 'id="EmailForm"'); ?>
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><i class="icon-times"></i></button>
				<h3>eMail <?php echo $page_title ?></h3>
			</div>
			<div class="modal-body">
				<fieldset>
					<div class="form-group">
						<label class="control-label">To</label>
						<input type="text" class="form-control form-control-sm ajaxEmail" name="to" value="<?php echo isset($to_email) ? $to_email : '' ?>" />
					</div>

					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">CC</label>
								<input type="text" class="form-control form-control-sm ajaxEmail" name="cc" value="" />
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">BCC</label>
								<input type="text" class="form-control form-control-sm ajaxEmail" name="bcc" value="<?php echo Settings::get('smtp_user') ?>" />
							</div>
						</div>
					</div>

					<div class="form-group">
						<label class="control-label">Subject</label>
						<input type="text" class="form-control form-control-sm" name="subject" value="<?php echo $page_title . ' ' . $from_date . '-' . $to_date ?>" />
					</div>

					<div class="form-group">
						<label class="control-label">Message</label>
						<textarea class="form-control form-control-sm" name="message" rows="5"></textarea>
					</div>
				</fieldset>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-success" id="Update"><i class="icon-envelope-o"></i> Send</button>
			</div>
		</form>
		</div>
	</div>
</div>

<?php echo form_open($this->_clspath.$this->_class, 'target="_blank" id="FormPreview"'); ?>
<input type="hidden" name="stuffing_id" value="" id="ShipmentID" />
</form>

<div id="FixedToolbar">
	<?php echo form_open($this->uri->uri_string(), 'class="form-search" id="MainForm"'); ?>
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
					<?php echo anchor($this->_clspath.$this->_class.'/preview/0', '<i class="icon-file-o"></i>', 'class="btn btn-default Popup" ') .
					anchor($this->_clspath.$this->_class.'/preview/1', '<i class="icon-file-pdf"></i>', 'class="btn btn-default Popup" ');// .
					//anchor($this->_clspath.$this->_class."/excel/", '<i class="icon-file-excel"></i>', 'class="btn btn-warning Popup"'); ?>
					</div>
				</div>
			</div>
		</td>

		<td>
			<div class="nowrap">
				<button type="button" class="btn btn-group btn-primary" onclick="javascript: clearSearch()"><i class="icon-align-justify"></i></button>
				
				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-success dropdown-toggle"><?php echo (isset($parsed_search['vessel']) ? '<i class="icon-filter4"></i>' : '') ?> Vessel <span class="caret"></span></button>
					<ul class="dropdown-menu nav-menu-scroll pull-right" id="FilterVessel">
						<li><a class="red" href="javascript: filter('vessel:')">Clear Filter</a></li>
					</ul>
				</div>

				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-info dropdown-toggle"><?php echo (isset($parsed_search['port']) ? '<i class="icon-filter4"></i>' : '') ?> Port <span class="caret"></span></button>
					<ul class="dropdown-menu nav-menu-scroll pull-right" id="FilterPort">
						<li><a class="red" href="javascript: filter('port:')">Clear Filter</a></li>
					</ul>
				</div>

				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-warning dropdown-toggle"><?php echo (isset($parsed_search1['shipper']) ? '<i class="icon-filter4"></i>' : '') ?> Shipper <span class="caret"></span></button>
					<ul class="dropdown-menu nav-menu-scroll pull-right" id="FilterShipper">
						<li><a class="red" href="javascript: filter('shipper:')">Clear Filter</a></li>
					</ul>
				</div>
			</div>
		</td>
	</tr>
	</table>
	</form>
</div>

<?php 
$filter = array(
	'vessel'  => array(),
	'port'    => array(),
	'shipper' => array(),
);
foreach ($rows as $r) {
	$i = 1;
	$total = array(
		'p20' => 0,
		'p40' => 0,
		'c20' => 0,
		'c40' => 0,
		'g20' => 0,
		'g40' => 0,
		'd20' => 0,
		'd40' => 0,
		'v20' => 0,
		'v40' => 0,
	);
	$filter['vessel'][$r['vessel_name']] = 1;
	$filter['port'][$r['port_name']]     = 1;

	echo '<table class="header big">
<tr>
	<td width="40%" colspan="2"><span class="box_label">Vessel Name</span><br />' . anchor('/master/vessel/edit/'.$r['vessel_id'], $r['vessel_name'], 'target="_blank"') ;
	if ($r['terminal_id'] == 1) {
		echo anchor('tracking/mict/index', $r['terminal'], 'class="btn btn-primary btn-sm Popup pull-right"');
	}
	elseif ($r['terminal_id'] == 2) {
		echo anchor('tracking/adani/index/2', $r['terminal'], 'class="btn btn-primary btn-sm Popup pull-right"');
	}
	elseif ($r['terminal_id'] == 3) {
		echo anchor('tracking/adani/index/3', $r['terminal'], 'class="btn btn-primary btn-sm Popup pull-right"');
	}
	else {
		echo '<span class="pull-right"><button type="button" class="btn btn-sm" disabled="disabled">' . $r['terminal'] . '</button></span>';
	}
	echo '</td>
	<td colspan="2"><span class="box_label">Port Name</span><br />' . $r['port_name'] . '</td>
	<td width="20%"><span class="box_label">Load List</span><br />' . anchor($this->_clspath.$this->_class.'/loadlist/'.$r['vessel_id'], '<i class="icon-download-alt"></i> Excel', 'class="Popup"') . '</td>
</tr>

<tr>
	<td width="20%"><span class="box_label">ETA</span><br />' . $r['eta_date'] . '</td>
	<td width="20%"><span class="box_label">ETD</span><br />' . $r['etd_date'] . '</td>
	<td width="20%"><span class="box_label">Sailing Date</span><br />' . $r['sailing_date'] . '</td>
	<td width="20%"><span class="box_label">Doc Cutoff</span><br />' . $r['doc_cutoff_date'] . '</td>
	<td width="20%"><span class="box_label">Gate Cutoff</span><br />' . $r['gate_cutoff_date'] . '</td>
</tr>
</table>

<table class="details Result">
<thead>
<tr>
	<th width="24px" rowspan="2">No</th>
	<th width="60px" rowspan="2">Line</th>
	<th width="120px nowrap" rowspan="2">Booking</th>
	<th width="80px" rowspan="2">Shipper</th>
	<th rowspan="2">FPD</th>
	<th colspan="2">Intention</th>
	<th colspan="2">Pickup</th>
	<th colspan="2" class="GateIn">Gate In</th>
	<th colspan="2" class="DocIn">Doc</th>
	<th width="40px" rowspan="2">SI</th>
	<th colspan="2">OnBoard</th>
</tr>

<tr>
	<th width="40px">C.20</th>
	<th width="40px">C.40</th>
	<th width="40px">C.20</th>
	<th width="40px">C.40</th>
	<th width="40px">C.20</th>
	<th width="40px">C.40</th>
	<th width="40px">C.20</th>
	<th width="40px">C.40</th>
	<th width="40px">C.20</th>
	<th width="40px">C.40</th>
</tr>
</thead>

<tbody>
';
	foreach ($r['jobs'] as $j) {
		$filter['shipper'][$j['shipper']] = 1;

		$total['p20'] += $j['p20'];
		$total['p40'] += $j['p40'];
		$total['c20'] += $j['c20'];
		$total['c40'] += $j['c40'];
		$total['g20'] += $j['g20'];
		$total['g40'] += $j['g40'];
		$total['d20'] += $j['d20'];
		$total['d40'] += $j['d40'];
		$total['v20'] += $j['v20'];
		$total['v40'] += $j['v40'];

		$class = null;
		if (($j['g20'] > 0 && $j['g20'] == $j['c20']) OR 
			($j['g40'] > 0 && $j['g40'] == $j['c40'])) 
			$class = "GateIn";
		if (($j['d20'] > 0 && $j['d20'] == $j['c20']) OR 
			($j['d40'] > 0 && $j['d40'] == $j['c40']))
			$class = "DocIn";
		if (($j['d20'] > 0 OR $j['d40'] > 0) &&
			($j['g20'] > 0 OR $j['g40'] > 0) &&
			$j['c20'] == $j['g20'] && $j['c20'] == $j['d20'] && 
			$j['c40'] == $j['g40'] && $j['c40'] == $j['d40'] && 
			$j['si_submitted'] != 'No')
			$class = "Completed";

		echo '<tr class="' . $class . '">
	<td class="aligncenter">' . $i++ . '</td>
	<td>' . $j['line'] . '</td>
	<td>' . anchor('export/jobs/edit/'.$j['id'], (strlen($j['booking_no']) > 0 ? $j['booking_no'] : 'Missing Booking No'), 'target="_blank"') . '</td>
	<td>' . $j['shipper'] . '</td>
	<td>' . $j['fpod'] . '</td>
	<td class="aligncenter P20">' . anchor('export/stuffing/edit/'.$j['id'], $j['p20'], 'target="_blank"') . '</td>
	<td class="aligncenter P40">' . anchor('export/stuffing/edit/'.$j['id'], $j['p40'], 'target="_blank"') . '</td>
	<td class="aligncenter C20">' . anchor('export/stuffing/edit/'.$j['id'], $j['c20'], 'target="_blank"') . '</td>
	<td class="aligncenter C40">' . anchor('export/stuffing/edit/'.$j['id'], $j['c40'], 'target="_blank"') . '</td>
	<td class="aligncenter C20 ' . ($j['c20'] > $j['g20'] ? '' : 'G20') . '">' . anchor('export/gatein/edit/'.$j['id'], $j['g20'], 'target="_blank"') . '</td>
	<td class="aligncenter C40 ' . ($j['c40'] > $j['g40'] ? '' : 'G40') . '">' . anchor('export/gatein/edit/'.$j['id'], $j['g40'], 'target="_blank"') . '</td>
	<td class="aligncenter C20 ' . ($j['c20'] > $j['d20'] ? '' : 'D20') . '">' . anchor('export/docin/edit/'.$j['id'], $j['d20'], 'target="_blank"') . '</td>
	<td class="aligncenter C40 ' . ($j['c40'] > $j['d40'] ? '' : 'D40') . '">' . anchor('export/docin/edit/'.$j['id'], $j['d40'], 'target="_blank"') . '</td>
	<td class="aligncenter ' . ($j['si_submitted'] == 'No' ? 'red' : 'green') . '">'. $j['si_submitted'] . '</td>
	<td class="aligncenter C20 ' . ($j['c20'] > $j['v20'] ? '' : 'V20') . '">' . anchor('export/onboard/edit/'.$j['id'], $j['v20'], 'target="_blank"') . '</td>
	<td class="aligncenter C40 ' . ($j['c40'] > $j['v40'] ? '' : 'V40') . '">' . anchor('export/onboard/edit/'.$j['id'], $j['v40'], 'target="_blank"') . '</td>
</tr>';
	}
	echo '</tbody>

<tfoot>
<tr>
	<th class="alignright" colspan="5"><strong>Total</strong></th>
	<th class="aligncenter"><strong>' . $total['p20'] . '</strong></th>
	<th class="aligncenter"><strong>' . $total['p40'] . '</strong></th>
	<th class="aligncenter"><strong>' . $total['c20'] . '</strong></th>
	<th class="aligncenter"><strong>' . $total['c40'] . '</strong></th>
	<th class="aligncenter"><strong>' . $total['g20'] . '</strong></th>
	<th class="aligncenter"><strong>' . $total['g40'] . '</strong></th>
	<th class="aligncenter"><strong>' . $total['d20'] . '</strong></th>
	<th class="aligncenter"><strong>' . $total['d40'] . '</strong></th>
	<th></th>
	<th class="aligncenter"><strong>' . $total['v20'] . '</strong></th>
	<th class="aligncenter"><strong>' . $total['v40'] . '</strong></th>
</tr>
</tfoot>
</table>
<br />
<br />';
} 
?>

<script type="text/javascript">

$(document).ready(function() {
	$('#ShowHideCompleted').on('click', function() {
		$('.Completed').toggle();
	});

	<?php echo 'dateRangePicker({
		years:     ['.$years[0].', '.$years[1].'],
		from_date: \''.convDate($from_date).'\',
		to_date:   \''.convDate($to_date).'\'
	});' ?>

	$("#FixedToolbar").width($("table.Result:eq(0)").width());

	if (!($.browser == "msie" && $.browser.version < 7)) {
		var target = "div#FixedToolbar";
		$(window).scroll(function(event) {
			$(target).css({
				left: ($(".Result").offset().left - $(window).scrollLeft()) + 'px'
			});
			if ($(this).scrollTop() > 25) {
				$(target).addClass("fixedTop show");
			} else {
				$(target).removeClass("fixedTop show");
			}
		});
	}

<?php 
if (count($filter['vessel']) > 0) {
	ksort($filter['vessel']);
	foreach ($filter['vessel'] as $k => $v) {
		echo '$("ul#FilterVessel").append("<li><a href=\"javascript: filter(\'vessel:' . $k . '\')\">' . $k . '</a></li>");';
	}
}
if (count($filter['port']) > 0) {
	ksort($filter['port']);
	foreach ($filter['port'] as $k => $v) {
		echo '$("ul#FilterPort").append("<li><a href=\"javascript: filter(\'port:' . $k . '\')\">' . $k . '</a></li>");';
	}
}
if (count($filter['shipper']) > 0) {
	ksort($filter['shipper']);
	foreach ($filter['shipper'] as $k => $v) {
		echo '$("ul#FilterShipper").append("<li><a href=\"javascript: filter(\'shipper:' . $k . '\')\">' . $k . '</a></li>");';
	}
}
?>
});
</script>