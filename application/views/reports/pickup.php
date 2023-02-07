<div id="modal-email" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
		<?php echo form_open($this->_clspath.$this->_class.'/preview/0/1', 'id="MainForm"'); ?>
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
						<input type="text" class="form-control form-control-sm" name="subject" value="<?php echo $page_title ?>" />
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


<?php echo form_open($this->uri->uri_string(), 'class="form-search" id="SearchForm"'); ?>
<input type="hidden" name="search_form" value="1" />
<table class="table toolbar">
<tr>
	<td width="160px">
		<div class="input-group input-group-sm">
			<input type="text" class="form-control form-control-sm DatePicker" name="date" value="dd-mm-YY">
			<div class="input-group-append">
				<div class="input-group-text"><i class="icon-calendar"></i></div>
			</div>
		</div>
	</td>

	<td width="100px">
		<label><input type="checkbox" name="show_all" value="1" <?php echo ($show_all ? 'checked="true"' : '') ?> /> Show All</label>
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
					anchor($this->_clspath.$this->_class.'/preview/1', '<i class="icon-file-pdf"></i>', 'class="btn btn-default Popup" ') .
					anchor($this->_clspath.$this->_class."/excel/", '<i class="icon-file-excel"></i>', 'class="btn btn-warning Popup"'); ?>
				<a href="#modal-email" class="btn btn-info" data-toggle="modal"><i class="icon-envelope-o"></i></a>
				</div>
			</div>
		</div>
	</td>	

	<td>
		<div class="nowrap">
			<button type="button" class="btn btn-group btn-primary" onclick="javascript: clearSearch()"><i class="icon-align-justify"></i></button>
			
			<div class="btn-group">
				<button data-toggle="dropdown" class="btn btn-info dropdown-toggle"><?php echo (isset($parsed_search['shipper']) ? '<i class="icon-filter4"></i>' : '') ?> Party <span class="caret"></span></button>
				<ul class="dropdown-menu nav-menu-scroll pull-right" id="FilterParty">
					<li><a class="red" href="javascript: filter('shipper:')">Clear Filter</a></li>
				</ul>
			</div>
			
			<div class="btn-group">
				<button data-toggle="dropdown" class="btn btn-info dropdown-toggle"><?php echo (isset($parsed_search['location']) ? '<i class="icon-filter4"></i>' : '') ?> Location <span class="caret"></span></button>
				<ul class="dropdown-menu nav-menu-scroll pull-right" id="FilterLocation">
					<li><a class="red" href="javascript: filter('location:')">Clear Filter</a></li>
				</ul>
			</div>
		</div>
	</td>
</tr>
</table>
</form>

<table class="table table-condensed table-striped table-bordered" id="Result">
<thead>
<tr>
	<th>No</th>
	<th>Job No</th>
	<th>Party</th>
	<th>Party Ref</th>
	<th>Stuffing Location</th>
	<th>Cargo</th>
	<th>Unit</th>
	<th>Containers</th>
	<th>Gross Weight</th>
	<th>POL</th>
	<th>FPD</th>
	<th>Line</th>
	<th>Pickup Location</th>
	<th>Pickup Date</th>
	<th>Stuffing Date</th>
	<th>Targeted Vessel</th>
	<th>ETA</th>
	<th>Cutoff</th>
	<th>Booking No</th>
</tr>
</thead>

<tbody>
<?php 
$i = 1;
$filter = [
	'shipper'  => [],
	'location' => [],
];
$total = [];
foreach ($rows as $pickup_id => $r) {
	$filter['shipper'][$r['shipper_name']]     = 1;
	$filter['location'][$r['pickup_location']] = 1;

	if (isset($total[$r['size']]))
		$total[$r['size']] += $r['containers'];
	else
		$total[$r['size']] = $r['containers'];

	echo '<tr>
	<td class="aligncenter tiny">' . $i++ . '</td>
	<td class="aligncenter tiny nowrap">' . anchor('/export/jobs/edit/' . $r['job_id'], $r['id2_format'], 'target="_blank"') . '</td>
	<td class="tiny">' . $r['shipper_name'] . '</td>
	<td class="tiny">' . $r['party_ref'] . '</td>
	<td class="tiny">' . $r['stuffing_location'] . '</td>
	<td class="tiny">' . $r['cargo_name'] . '</td>
	<td class="tiny">' . $r['unit_code'] . '</td>
	<td class="tiny aligncenter">' . anchor('export/stuffing/pending/'.$r['job_id'] . '/'. $r['pickup_id'], $r['containers'] . ' x ' . $r['size'], 'target="_blank"') . '<br /><span class="green bold">' . $r['gate_out'] . '</span></td>
	<td class="tiny">' . $r['gross_weight'] . '</td>
	<td class="tiny">' . character_limiter($r['port_of_loading'], 10) . '</td>
	<td class="tiny">' . $r['fpod'] . '</td>
	<td class="tiny">' . character_limiter($r['line_name'], 10) . '</td>
	<td class="tiny">' . $r['pickup_location'] . '</td>
	<td class="tiny">' . $r['pickup_date'] . '</td>
	<td class="tiny">' . $r['stuffing_date'] . '</td>
	<td class="tiny">' . $r['vessel_name'] . '</td>
	<td class="tiny">' . $r['eta_date'] . '</td>
	<td class="tiny">' . $r['gate_cutoff_date'] . '</td>
	<td class="tiny">' . $r['booking_no'] . '</td>
</tr>';
}
?>
</tbody>
</table>

<p class="big">Total Containers: <?php foreach ($total as $size => $count) {
		echo ' (' . $count . ' x ' . $size . ') ';
	} ?>
</p>

<script type="text/javascript">

$(document).ready(function() {
	$('.ajaxEmail').on('keydown.autocomplete', function(event, items){
		$(this).autocomplete({
			appendTo: '#modal-email',
			source: function(request, response) {
				$.ajax({
					type: "POST",
					url: '<?php echo site_url('/master/party/ajaxEmail/0') ?>',
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
if (count($filter['shipper']) > 0) {
	ksort($filter['shipper']);
	foreach ($filter['shipper'] as $k => $v) {
		echo '$("ul#FilterParty").append("<li><a href=\"javascript: filter(\'shipper:' . $k . '\')\">' . $k . '</a></li>");';
	}
}
if (count($filter['location']) > 0) {
	ksort($filter['location']);
	foreach ($filter['location'] as $k => $v) {
		echo '$("ul#FilterLocation").append("<li><a href=\"javascript: filter(\'location:' . $k . '\')\">' . $k . '</a></li>");';
	}
}
?>
});
</script>