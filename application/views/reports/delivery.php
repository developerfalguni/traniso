
<div id="modal-email" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
		<?php echo form_open($this->_clspath.$this->_class.'/email', 'id="MainForm"'); ?>
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><i class="icon-times"></i></button>
				<h3>Email</h3>
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
								<input type="text" class="form-control form-control-sm ajaxEmail" name="cc" value="<?php echo Settings::get('default_cc') ?>" />
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
						<input type="text" class="form-control form-control-sm" name="subject" value="Daily Dispatch Report" />
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
						<div class="input-group-btn">
							<button type="submit" class="btn btn-primary" id="SearchButton"><i class="fa fa-search"></i> Search</button>
							<a href="#modal-search" class="btn btn-primary" data-toggle="modal"><i class="icon-search-plus"></i></a>
						</div>
					</div>
				</div>
				<div class="col-md-4">
					<div class="btn-group">
					<?php 
					echo anchor($this->_clspath.$this->_class.'/preview/0', '<i class="icon-file-o"></i>', 'class="btn btn-default Popup" ') .
						anchor($this->_clspath.$this->_class.'/preview/1', '<i class="icon-file-pdf"></i>', 'class="btn btn-default Popup" ') .
						anchor($this->_clspath.$this->_class.'/excel/', '<i class="icon-file-excel"></i>', 'class="btn btn-warning Popup"') .
						'<a href="#modal-email" class="btn btn-info" data-toggle="modal"><i class="icon-envelope-o"></i></a>';
					?>
					</div>
				</div>
			</div>
		</td>
		
		<td>
			<div class="nowrap">
				<button type="button" class="btn btn-group btn-primary" onclick="javascript: clearSearch()"><i class="icon-align-justify"></i></button>
				
				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-info dropdown-toggle"><?php echo (isset($parsed_search['party']) ? '<i class="icon-filter4"></i>' : '') ?> Party <span class="caret"></span></button>
					<ul class="dropdown-menu pull-right nav-menu-scroll" id="FilterParty">
						<li><a href="javascript: filter('party:')" class="red">Clear Filter</a></li>
					</ul>
				</div>

				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-success dropdown-toggle"><?php echo (isset($parsed_search['container']) ? '<i class="icon-filter4"></i>' : '') ?> Container <span class="caret"></span></button>
					<ul class="dropdown-menu pull-right nav-menu-scroll" id="FilterContainer">
						<li><a href="javascript: filter('container:')" class="red">Clear Filter</a></li>
					</ul>
				</div>

				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-warning dropdown-toggle"><?php echo (isset($parsed_search['bl_no']) ? '<i class="icon-filter4"></i>' : '') ?> BL <span class="caret"></span></button>
					<ul class="dropdown-menu pull-right nav-menu-scroll" id="FilterBL">
						<li><a href="javascript: filter('bl_no:')" class="red">Clear Filter</a></li>
					</ul>
				</div>

				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-danger dropdown-toggle"><?php echo (isset($parsed_search['be_no']) ? '<i class="icon-filter4"></i>' : '') ?> BE <span class="caret"></span></button>
					<ul class="dropdown-menu pull-right nav-menu-scroll" id="FilterBE">
						<li><a href="javascript: filter('be_no:')" class="red">Clear Filter</a></li>
					</ul>
				</div>

				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-info dropdown-toggle"><?php echo (isset($parsed_search['vehicle']) ? '<i class="icon-filter4"></i>' : '') ?> Vehicle <span class="caret"></span></button>
					<ul class="dropdown-menu pull-right nav-menu-scroll" id="FilterVehicle">
						<li><a href="javascript: filter('vehicle:')" class="red">Clear Filter</a></li>
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
		<th>Job No</th>
		<th>Gatepass Date</th>
		<th>Party Name</th>
		<th>Container No</th>
		<th>Size</th>
		<th>BL No</th>
		<th>BE No</th>
		<th>Vehicle No</th>
		<th>Disp. Wt.</th>
	</tr>
	</thead>
	</table>
</div>

<table class="table table-condensed table-striped table-bordered" id="Result">
<thead>
<tr>
	<th>No</th>
	<th>Job No</th>
	<th>Gatepass Date</th>
	<th>Party Name</th>
	<th>Container No</th>
	<th>Size</th>
	<th>BL No</th>
	<th>BE No</th>
	<th>Vehicle No</th>
	<th>Disp. Wt.</th>
</tr>
</thead>

<tbody>
<?php 
$i = 1;
$filter = array(
	'party'     => array(),
	'container' => array(),
	'bl_no'     => array(),
	'be_no'     => array(),
	'vehicle'   => array(),
);
$total = array(
	'dispatch_weight' => 0,
);
foreach ($rows as $r) {
	$filter['party'][$r['party_name']]   = 1;
	$filter['container'][$r['number']]   = 1;
	$filter['bl_no'][$r['bl_no']]        = 1;
	$filter['be_no'][$r['be_no']]        = 1;
	$filter['vehicle'][$r['vehicle_no']] = 1;

	$total['dispatch_weight'] = bcadd($total['dispatch_weight'], $r['dispatch_weight'], 2);
	
	echo '<tr id="' . $r['id'] . '">
	<td class="aligncenter">' . $i++ . '</td>
	<td class="aligncenter">' . anchor('import/delivery/index/'.$r['job_id'], $r['id2_format'], 'target="_blank"') . '</td>
	<td class="aligncenter">' . $r['gatepass_date'] . '</td>
	<td>' . $r['party_name'] . '</td>
	<td>' . $r['number'] . '</td>
	<td class="aligncenter">' . $r['size'] . '</td>
	<td>' . $r['bl_no'] . '</td>
	<td>' . $r['be_no'] . '</td>
	<td>' . $r['vehicle_no'] . '</td>
	<td class="alignright">' . $r['dispatch_weight'] . '</td>
</tr>';
} 
?>
</tbody>

<tfoot>
<tr>
	<th class="alignright" colspan="9">Total</th>
	<th class="alignright"><?php echo $total['dispatch_weight'] ?></th>
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

	$('.ajaxEmail').on('keydown.autocomplete', function(event, items){
		$(this).autocomplete({
			appendTo: '#modal-email',
			source: function(request, response) {
				$.ajax({
					type: "POST",
					// url: '<?php echo site_url('/master/party/ajaxEmail/0') ?>',
					url: '<?php echo site_url('/master/party/ajaxEmail/0/'.(isset($parsed_search['party']) ? htmlentities($parsed_search['party']) : '')) ?>',
					dataType: "json",
					data: {
						term: extractLast(request.term),
					},
					success: function(data) {
						response(data);
					}
				});
			},
			minLength: 1,
			focus: function(event, ui) {
				return false;
			},
			select: function(event, ui) {
				var terms = split(this.value);
				terms.pop();
				terms.push(ui.item.email);
				terms.push("");
				this.value = terms.join("; ");
				return false;
			}
		})
		.data('ui-autocomplete')._renderItem = function(ul, item) {
			return $('<li></li>')
				.data('item.autocomplete', item)
				.append('<a>' + item.name +  ' &lt;' + item.email +  '&gt;</a>')
				.appendTo(ul);
		}
	});

<?php 
if (count($filter['party']) > 0) {
	ksort($filter['party']);
	foreach ($filter['party'] as $k => $v) {
		echo '$("ul#FilterParty").append("<li><a href=\"javascript: filter(\'party:' . $k . '\')\">' . $k . '</a></li>");';
	}
}
if (count($filter['container']) > 0) {
	ksort($filter['container']);
	foreach ($filter['container'] as $k => $v) {
		echo '$("ul#FilterContainer").append("<li><a href=\"javascript: filter(\'container:' . $k . '\')\">' . $k . '</a></li>");';
	}
}
if (count($filter['bl_no']) > 0) {
	ksort($filter['bl_no']);
	foreach ($filter['bl_no'] as $k => $v) {
		echo '$("ul#FilterBL").append("<li><a href=\"javascript: filter(\'bl_no:' . $k . '\')\">' . $k . '</a></li>");';
	}
}
if (count($filter['be_no']) > 0) {
	ksort($filter['be_no']);
	foreach ($filter['be_no'] as $k => $v) {
		echo '$("ul#FilterBE").append("<li><a href=\"javascript: filter(\'be_no:' . $k . '\')\">' . $k . '</a></li>");';
	}
}
if (count($filter['vehicle']) > 0) {
	ksort($filter['vehicle']);
	foreach ($filter['vehicle'] as $k => $v) {
		echo '$("ul#FilterVehicle").append("<li><a href=\"javascript: filter(\'vehicle:' . $k . '\')\">' . $k . '</a></li>");';
	}
}
?>
});
</script>