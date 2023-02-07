
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
						</span>
					</div>
				</div>
				<div class="col-md-4">
					<a href="#modal-search" class="btn btn-primary" data-toggle="modal"><i class="icon-search-plus"></i></a>
					<div class="btn-group">
					<?php 
						echo '<button type="button" onclick="javascript: preview(0)" class="btn btn-default Preview"><i class="icon-file-o"></i></button>
							<button type="button" onclick="javascript: preview(1)" class="btn btn-default Preview"><i class="icon-file-pdf"></i></button>' .
							anchor($this->_clspath.$this->_class.'/excel/', '<i class="icon-file-excel"></i>', 'class="btn btn-warning Popup"');
					?>
					</div>
				</div>
			</div>
		</td>

		<td>
			<div class="nowrap">
				<button type="button" class="btn btn-group btn-primary" onclick="javascript: clearSearch()"><i class="icon-align-justify"></i></button>
				
				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-info dropdown-toggle"><?php echo (isset($search['type']) ? '<i class="icon-filter4"></i>' : '') ?> Type <span class="caret"></span></button>
					<ul class="dropdown-menu nav-menu-scroll pull-right" id="FilterType">
						<li><a class="red" href="javascript: filter('type:')">Clear Filter</a></li>
						<li><a href="javascript: filter('type:Import')">Import</a></li>
						<li><a href="javascript: filter('type:Export')">Export</a></li>
					</ul>
				</div>

				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-info dropdown-toggle"><?php echo (isset($search['party']) ? '<i class="icon-filter4"></i>' : '') ?> Party <span class="caret"></span></button>
					<ul class="dropdown-menu nav-menu-scroll pull-right" id="FilterParty">
						<li><a class="red" href="javascript: filter('party:')">Clear Filter</a></li>
					</ul>
				</div>

				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-info dropdown-toggle">Debit Note <span class="caret"></span></button>
					<ul class="dropdown-menu nav-menu-scroll pull-right">
						<li><a href="javascript: filter('debit_note:0')">All Entries</a></li>
						<li><a href="javascript: filter('debit_note:1')">Zero Only</a></li>
						<li><a href="javascript: filter('debit_note:2')">Non Zero Only</a></li>
					</ul>
				</div>

				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-info dropdown-toggle">Invoice <span class="caret"></span></button>
					<ul class="dropdown-menu nav-menu-scroll pull-right">
						<li><a href="javascript: filter('invoice:0')">All Entries</a></li>
						<li><a href="javascript: filter('invoice:1')">Zero Only</a></li>
						<li><a href="javascript: filter('invoice:2')">Non Zero Only</a></li>
					</ul>
				</div>

				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-info dropdown-toggle">Tpt Invoice <span class="caret"></span></button>
					<ul class="dropdown-menu nav-menu-scroll pull-right">
						<li><a href="javascript: filter('transportation:0')">All Entries</a></li>
						<li><a href="javascript: filter('transportation:1')">Zero Only</a></li>
						<li><a href="javascript: filter('transportation:2')">Non Zero Only</a></li>
					</ul>
				</div>

				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-info dropdown-toggle">Job Amount <span class="caret"></span></button>
					<ul class="dropdown-menu nav-menu-scroll pull-right">
						<li><a href="javascript: filter('job_amount:0')">All Entries</a></li>
						<li><a href="javascript: filter('job_amount:1')">Zero Only</a></li>
						<li><a href="javascript: filter('job_amount:2')">Non Zero Only</a></li>
					</ul>
				</div>

				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-info dropdown-toggle">Expenses <span class="caret"></span></button>
					<ul class="dropdown-menu nav-menu-scroll pull-right">
						<li><a href="javascript: filter('expenses:0')">All Entries</a></li>
						<li><a href="javascript: filter('expenses:1')">Zero Only</a></li>
						<li><a href="javascript: filter('expenses:2')">Non Zero Only</a></li>
					</ul>
				</div>

				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-info dropdown-toggle">Tpt Payment <span class="caret"></span></button>
					<ul class="dropdown-menu nav-menu-scroll pull-right">
						<li><a href="javascript: filter('payment:0')">All Entries</a></li>
						<li><a href="javascript: filter('payment:1')">Zero Only</a></li>
						<li><a href="javascript: filter('payment:2')">Non Zero Only</a></li>
					</ul>
				</div>
			</div>
		</td>
	</tr>
	</table>
	</form>

	<table class="table table-condensed table-striped table-bordered tiny hide" id="FixedHeader">
	<thead>
	<tr>
		<th>No</th>
		<th>Type</th>
		<th>Job No</th>
		<th>Importer</th>
		<th class="orange">C.20</th>
		<th class="orange">C.40</th>
		<th>D.Note Dt</th>
		<th>D.Note Amount</th>
		<th>Inv Date</th>
		<th>Inv Amount</th>
		<th>S.Tax</th>
		<th>Tpt Invoice </th>
		<th>Job Amount</th>
		<th>Expenses</th>
		<th>Tpt Payment</th>
		<th>Balance</th>
	</tr>
	</thead>
	</table>
</div>

<table class="table table-condensed table-striped table-bordered tiny" id="Result">
<thead>
<tr>
	<th>No</th>
	<th>Type</th>
	<th>Job No</th>
	<th>Importer</th>
	<th class="orange">C.20</th>
	<th class="orange">C.40</th>
	<th>D.Note Dt</th>
	<th>D.Note Amount</th>
	<th>Inv Date</th>
	<th>Inv Amount</th>
	<th>S.Tax</th>
	<th>Tpt Invoice </th>
	<th>Job Amount</th>
	<th>Expenses</th>
	<th>Tpt Payment</th>
	<th>Balance</th>
</tr>
</thead>

<tbody>
<?php 
$i = 1;
$filter = [
	'party' => [],
];
$total = array(
	'container_20' => 0,
	'container_40' => 0,
	// 'expense'   => 0,
	// 'income'    => 0,
);
foreach ($rows as $r) {
	$filter['party'][$r['party_name']] = $r['party_name'];

	$total['container_20'] += $r['container_20'];
	$total['container_40'] += $r['container_40'];

	if ($r['type'] == 'Import')
		$job_amount = ($r['debit_note']['amount'] + $r['invoice']['amount'] + $r['transportation']['amount']);
	else
		$job_amount = ($r['debit_note']['amount'] + $r['invoice']['amount'] + $r['invoice']['stax_amount'] + $r['transportation']['amount']);
	
	$net_profit = $job_amount - ($r['expense'] + $r['payment']);

	$job_link = $r['type'] == 'Import' ? anchor('/import/jobs/edit/' . $r['job_id'], $r['id2_format'], 'target="_blank"') : anchor('/export/jobs/edit/' . $r['job_id'], $r['id2_format'], 'target="_blank"');

	echo '<tr class="JobRow">
	<td class="aligncenter">' . $i++ . '</td>
	<td>' . $r['type'] . '</td>
	<td class="aligncenter">' . $job_link . '</td>
	<td>' . $r['party_name'] . '</td>
	<td class="aligncenter">' . $r['container_20'] . '</td>
	<td class="aligncenter">' . $r['container_40'] . '</td>
	<td>' . $r['debit_note']['no_date'] . '</td>
	<td class="alignright">' . $r['debit_note']['amount'] . '</td>
	<td>' . $r['invoice']['no_date'] . '</td>
	<td class="alignright">' . $r['invoice']['amount'] . '</td>
	<td class="alignright">' . $r['invoice']['stax_amount'] . '</td>
	<td class="alignright">' . $r['transportation']['amount'] . '</td>
	<td class="alignright">' . $job_amount . '</td>
	<td class="alignright">' . $r['expense'] . '</td>
	<td class="alignright">' . $r['payment'] . '</td>
	<td class="alignright">' . $net_profit . '</td>
</tr>';
} 
?>
</tbody>
<tfoot>
	<tr>
		<th class="alignright" colspan="4">Total</th>
		<th class="alignright"><?php echo $total['container_20']; ?></th>
		<th class="alignright"><?php echo $total['container_40']; ?></th>
		<th></th>
		<!-- <th class="alignright"><?php echo inr_format($total['expense']); ?></th>
		<th class="alignright"><?php echo inr_format($total['income']); ?></th> -->
	</tr>
</tfoot>
</table>

<script type="text/javascript">

var status_debit_note     = 0;
var status_invoice        = 0;
var status_transportation = 0;
var status_job_amount     = 0;
var status_expenses       = 0;
var status_payment        = 0;


function preview(pdf) {
	$('.Preview').popupWindow({
		menubar: 1,
		scrollbars: 1,
		height: 768,
		width: 1024,
		windowURL: '<?php echo site_url($this->_clspath.$this->_class.'/preview') ?>/'+pdf+'/'+status_debit_note+'/'+status_invoice+'/'+status_transportation+'/'+status_job_amount+'/'+status_expenses+'/'+status_payment 
	});
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
if (count($filter['party']) > 0) {
	ksort($filter['party']);
	foreach ($filter['party'] as $k => $v) {
		echo '$("ul#FilterParty").append("<li><a href=\"javascript: filter(\'party:' . $k . '\')\">' . $k . '</a></li>");';
	}
}
?>
});
</script>