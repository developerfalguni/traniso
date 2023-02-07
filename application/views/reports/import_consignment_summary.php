<div id="modal-email" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
		<?php echo form_open($this->_clspath.$this->_class.'/previewSummary/1/1', 'id="MainForm"'); ?>
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
					echo anchor($this->_clspath.$this->_class.'/previewSummary/0', '<i class="icon-file-o"></i>', 'class="btn btn-default Popup" ') .
						anchor($this->_clspath.$this->_class.'/previewSummary/1', '<i class="icon-file-pdf"></i>', 'class="btn btn-default Popup" ') .
						anchor($this->_clspath.$this->_class."/excelSummary/", '<i class="icon-file-excel"></i>', 'class="btn btn-warning Popup"') .
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
				<ul class="dropdown-menu nav-menu-scroll pull-right" id="FilterParty">
					<li><a class="red" href="javascript: filter('party:')">Clear Filter</a></li>
				</ul>
			</div>

			<div class="btn-group">
				<button data-toggle="dropdown" class="btn btn-success dropdown-toggle"><?php echo (isset($parsed_search['vessel']) ? '<i class="icon-filter4"></i>' : '') ?> Vessel <span class="caret"></span></button>
				<ul class="dropdown-menu nav-menu-scroll pull-right" id="FilterVessel">
					<li><a class="red" href="javascript: filter('vessel:')">Clear Filter</a></li>
				</ul>
			</div>

			<div class="btn-group">
				<button data-toggle="dropdown" class="btn btn-warning dropdown-toggle"><?php echo (isset($parsed_search['line']) ? '<i class="icon-filter4"></i>' : '') ?> Line <span class="caret"></span></button>
				<ul class="dropdown-menu nav-menu-scroll pull-right" id="FilterLine">
					<li><a class="red" href="javascript: filter('line:')">Clear Filter</a></li>
				</ul>
			</div>

			<div class="btn-group">
				<button data-toggle="dropdown" class="btn btn-danger dropdown-toggle"><?php echo (isset($parsed_search['product']) ? '<i class="icon-filter4"></i>' : '') ?> Product <span class="caret"></span></button>
				<ul class="dropdown-menu nav-menu-scroll pull-right" id="FilterProduct">
					<li><a class="red" href="javascript: filter('product:')">Clear Filter</a></li>
				</ul>
			</div>

			<div class="btn-group">
				<button data-toggle="dropdown" class="btn btn-info dropdown-toggle"><?php echo (isset($parsed_search['port']) ? '<i class="icon-filter4"></i>' : '') ?> Port <span class="caret"></span></button>
				<ul class="dropdown-menu nav-menu-scroll pull-right" id="FilterPort">
					<li><a class="red" href="javascript: filter('port:')">Clear Filter</a></li>
				</ul>
			</div>

			<div class="btn-group">
				<button data-toggle="dropdown" class="btn btn-success dropdown-toggle"><?php echo (isset($parsed_search['cfs']) ? '<i class="icon-filter4"></i>' : '') ?> CFS <span class="caret"></span></button>
				<ul class="dropdown-menu nav-menu-scroll pull-right" id="FilterCFS">
					<li><a class="red" href="javascript: filter('cfs:')">Clear Filter</a></li>
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
	<th>Importer</th>
	<th>Vessel</th>
	<th>Cargo</th>
	<th>POD</th>
	<th>BL No</th>
	<th>C.20</th>
	<th>C.40</th>
	<th>BL Qty</th>
	<th>Rcvd Wt.</th>
	<th>Diff/Bal</th>
	<th>Dispatch Wt.</th>
	<th>Diff/Bal</th>
</tr>
</thead>

<tbody>
<?php 
$i = 1;
$filter = array(
	'party'   => array(),
	'vessel'  => array(),
	'line'    => array(),
	'product' => array(),
	'port'    => array(),
	'cfs'     => array(),
);
$total = array(
	'container_20'    => 0,
	'container_40'    => 0,
	'net_weight'      => 0,
	'received_weight' => 0,
	'bl_rcvd_diff'   => 0,
	'dispatch_weight' => 0,
	'rcvd_disp_diff'  => 0,
	
);
foreach ($rows as $r) {
	$filter['party'][$r['party_name']]     = $r['party_name'];
	$filter['vessel'][$r['vessel_name']]   = $r['vessel_name'];
	$filter['line'][$r['line_name']]       = $r['line_name'];
	$filter['product'][$r['product_name']] = $r['product_name'];
	$filter['port'][$r['port_name']]       = $r['port_name'];
	$filter['cfs'][$r['cfs_name']]         = $r['cfs_name'];

	$total['container_20']    += $r['container_20'];
	$total['container_40']    += $r['container_40'];
	$total['net_weight']      += $r['net_weight'];
	$total['received_weight'] += $r['received_weight'];
	$total['bl_rcvd_diff']    += $r['bl_rcvd_diff'];
	$total['dispatch_weight'] += $r['dispatch_weight'];
	$total['rcvd_disp_diff']  += $r['rcvd_disp_diff'];
	
	echo '<tr>
	<td class="aligncenter tiny">' . $i++ . '</td>
	<td class="aligncenter big">' . anchor(underscore($r['type']).'/jobs/edit/' . $r['id'], $r['id2_format'], 'target="_blank"') . '</td>
	<td class="tiny">' . $r['party_name'] . '</td>
	<td class="tiny">' . $r['vessel_name'] . '</td>
	<td class="tiny">' . $r['product_name'] . '</td>
	<td class="tiny">' . $r['pod'] . '</td>
	<td class="tiny">' . $r['bl_no'] . '</td>
	<td class="tiny aligncenter">' . $r['container_20'] . '</td>
	<td class="tiny aligncenter">' . $r['container_40'] . '</td>
	<td class="tiny alignright">' . $r['net_weight'] . '</td>
	<td class="tiny alignright">' . $r['received_weight'] . '</td>
	<td class="tiny alignright">' . $r['bl_rcvd_diff']  . '</td>
	<td class="tiny alignright">' . $r['dispatch_weight'] . '</td>
	<td class="tiny alignright">' . $r['rcvd_disp_diff'] . '</td>
</tr>';
} 
?>
</tbody>

<tfoot>
	<tr>
		<th class="alignright" colspan="7">Total</th>
		<th class="alignright"><?php echo $total['container_20'] ?></th>
		<th class="alignright"><?php echo $total['container_40'] ?></th>
		<th class="alignright"><?php echo $total['net_weight'] ?></th>
		<th class="alignright"><?php echo $total['received_weight'] ?></th>
		<th class="alignright"><?php echo $total['bl_rcvd_diff'] ?></th>
		<th class="alignright"><?php echo $total['dispatch_weight'] ?></th>
		<th class="alignright"><?php echo $total['rcvd_disp_diff'] ?></th>
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
				.append('<a>' + item.name +  ' <span class="orange">&lt;' + item.email +  '&gt;</span></a>')
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
?>
});
</script>