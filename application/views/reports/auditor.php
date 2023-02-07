
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
						echo // anchor($this->_clspath.$this->_class.'/preview/0', '<i class="icon-file-o"></i>', 'class="btn btn-default Popup" ') .
							// anchor($this->_clspath.$this->_class.'/preview/1', '<i class="icon-file-pdf"></i>', 'class="btn btn-default Popup" ') .
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
					<button data-toggle="dropdown" class="btn btn-info dropdown-toggle"><?php echo (isset($search['status']) ? '<i class="icon-filter4"></i>' : '') ?> Status <span class="caret"></span></button>
					<ul class="dropdown-menu nav-menu-scroll pull-right" id="FilterStatus">
						<li><a class="red" href="javascript: filter('status:')">Clear Filter</a></li>
						<li><a href="javascript: filter('status:Pending')">Pending</a></li>
						<li><a href="javascript: filter('status:')"></a></li>
						<li><a href="javascript: filter('status:Completed')">Completed</a></li>
					</ul>
				</div>

				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-info dropdown-toggle"><?php echo (isset($search['party']) ? '<i class="icon-filter4"></i>' : '') ?> Party <span class="caret"></span></button>
					<ul class="dropdown-menu nav-menu-scroll pull-right" id="FilterParty">
						<li><a class="red" href="javascript: filter('party:')">Clear Filter</a></li>
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
		<th>Importer</th>
		<th>Line</th>
		<th class="orange">C.20</th>
		<th class="orange">C.40</th>
		<th>BE / SB No</th>
		<th>BE / SB Date</th>
		<th>Status</th>
		<th>Expenses</th>
		<th>Income</th>
		<th>Debit Note</th>
		<th>Transportation Details</th>
	</tr>
	</thead>
	</table>
</div>

<table class="table table-condensed table-striped table-bordered" id="Result">
<thead>
<tr>
	<th>No</th>
	<th>Job No</th>
	<th>Importer</th>
	<th>Line</th>
	<th class="orange">C.20</th>
	<th class="orange">C.40</th>
	<th>BE / SB No</th>
	<th>BE / SB Date</th>
	<th>Status</th>
	<th>Expenses</th>
	<th>Income</th>
	<th>Debit Note</th>
	<th>Transportation Details</th>
</tr>
</thead>

<tbody>
<?php 
$i = 1;
$filter = array(
	'party' => array(),
);
$total = array(
	'container_20' => 0,
	'container_40' => 0,
	'expense'      => 0,
	'income'       => 0,
	'debit'        => 0,
	'income'       => 0,
);
foreach ($rows as $r) {
	$filter['party'][$r['party_name']] = $r['party_name'];

	$total['container_20'] += $r['container_20'];
	$total['container_40'] += $r['container_40'];

	$job_link = $r['type'] == 'Import' ? anchor('/import/jobs/edit/' . $r['job_id'], $r['id2_format'], 'target="_blank"') : anchor('/export/jobs/edit/' . $r['job_id'], $r['id2_format'], 'target="_blank"');

	echo '<tr>
	<td class="aligntop aligncenter tiny">' . $i++ . '</td>
	<td class="aligntop tiny aligncenter">' . $job_link . '</td>
	<td class="aligntop tiny">' . $r['party_name'] . '</td>
	<td class="aligntop tiny">' . $r['line'] . '</td>
	<td class="aligntop big aligncenter">' . $r['container_20'] . '</td>
	<td class="aligntop big aligncenter">' . $r['container_40'] . '</td>
	<td class="aligntop tiny">' . $r['be_sb_no'] . '</td>
	<td class="aligntop tiny">' . $r['be_sb_date'] . '</td>
	<td class="aligntop tiny">' . $r['status'] . '</td>';

	if (isset($r['Expenses'])) {
		echo '<td class="aligntop">
		<table class="table table-bordered">
		<thead>
			<th>JV No</th>
			<th>Bill No</th>
			<th>Date</th>
			<th>Expense Name</th>
			<th>Expense Head</th>
			<th>Amount</th>
		</thead>

		<tbody>';
		$sub_total = array(
			'amount' => 0,
		);
		foreach ($r['Expenses'] as $i => $e) {
			$sub_total['amount'] += $e['amount'];
			$total['expense']        += $e['amount'];

			$audited = ($e['audited'] == 'Yes') ? 'style="background-color:#A1EF9B"' : '';
			

		echo '<tr>
			<td class="tiny aligntop" '.$audited.'>' . anchor('accounting/'.underscore($e['url']), $e['jv_no'], 'target="_blank"') . '</td>
			<td class="tiny aligntop" '.$audited.'>' . $e['bill_no'] . '</td>
			<td class="tiny aligntop" '.$audited.'>' . $e['date'] . '</td>
			<td class="tiny aligntop" '.$audited.'>' . $e['particulars'] . '</td>
			<td class="tiny aligntop" '.$audited.'>' . $e['party_name'] . '</td>
			<td class="tiny aligntop alignright" '.$audited.'>' . $e['amount'] . '</td>
		</tr>';
		}
		echo '</tbody>

		<tfoot>
		<tr>
			<th class="alignright" colspan="5">Total</th>
			<th class="alignright">' . $sub_total['amount'] . '</th>
		</tr>
		</tfoot>
		</table>
		</td>';
	}
	else {
		echo '<td></td>';
	}

	if (isset($r['Invoice'])) {
		echo '<td class="aligntop">
		<table class="table table-bordered">
		<thead>
			<th>Invoice No</th>
			<th>Date</th>
			<th>Particulars</th>
			<th>Amount</th>
		</thead>

		<tbody>';
		$sub_total = array(
			'amount'      => 0,
		);
		foreach ($r['Invoice'] as $i => $b) {
			$sub_total['amount'] += $b['amount'];
			$total['income']     += $b['amount'];

			$audited = ($b['audited'] == 'Yes') ? 'style="background-color:#A1EF9B"' : '';

		echo '<tr>
			<td class="tiny aligntop" '.$audited.'>' . anchor('accounting/'.underscore($b['url']), $b['invoice_no'], 'target="_blank"') . '</td>
			<td class="tiny aligntop" '.$audited.'>' . $b['date'] . '</td>
			<td class="tiny aligntop" '.$audited.'>' . $b['particulars'] . '</td>
			<td class="tiny aligntop alignright" '.$audited.'>' . $b['amount'] . '</td>
		</tr>';
		}
		echo '</tbody>

		<tfoot>
		<tr>
			<th class="alignright" colspan="3">Total</th>
			<th class="alignright">' . $sub_total['amount'] . '</th>
		</tr>
		</tfoot>
		</table>
		</td>';
	}
	else {
		echo '<td></td>';
	}

	if (isset($r['Debit Note'])) {
		echo '<td class="aligntop">
		<table class="table table-bordered">
		<thead>
			<th>Debit Note No</th>
			<th>Date</th>
			<th>Particulars</th>
			<th>Amount</th>
		</thead>

		<tbody>';
		$sub_total = array(
			'amount'      => 0,
		);
		foreach ($r['Debit Note'] as $i => $b) {
			$sub_total['amount'] += $b['amount'];
			$total['income']     += $b['amount'];

			$audited = ($b['audited'] == 'Yes') ? 'style="background-color:#A1EF9B"' : '';

		echo '<tr>
			<td class="tiny aligntop" '.$audited.'>' . anchor('accounting/'.underscore($b['url']), $b['invoice_no'], 'target="_blank"') . '</td>
			<td class="tiny aligntop" '.$audited.'>' . $b['date'] . '</td>
			<td class="tiny aligntop" '.$audited.'>' . $b['particulars'] . '</td>
			<td class="tiny aligntop alignright" '.$audited.'>' . $b['amount'] . '</td>
		</tr>';
		}
		echo '</tbody>

		<tfoot>
		<tr>
			<th class="alignright" colspan="3">Total</th>
			<th class="alignright">' . $sub_total['amount'] . '</th>
		</tr>
		</tfoot>
		</table>
		</td>';
	}
	else {
		echo '<td></td>';
	}

	if (isset($r['Transportation'])) {
		echo '<td class="aligntop">
		<table class="table table-bordered">
		<thead>
			<th>Date</th>
			<th>Container No</th>
			<th>Size</th>
			<th>Transporter</th>
			<th>Bill Date</th>
			<th>Software Date</th>
			<th>Processing Date</th>
			<th>Bill No</th>
			<th>Transporter Rate</th>
			<th>Party Name</th>
			<th>Invoice Date</th>
			<th>Invoice No</th>
			<th>Party Rate</th>
			<th>Invoice Amount</th>
			<th>Profit</th>
		</thead>

		<tbody>';
		$sub_total = array(
			'transporter_rate' => 0,
			'party_rate'       => 0,
			'profit'           => 0,
		);
		foreach ($r['Transportation'] as $i => $b) {
			$sub_total['transporter_rate'] += $b['transporter_rate'];
			$sub_total['party_rate']       += $b['party_rate'];
			$sub_total['profit']           += bcsub($b['party_rate'], $b['transporter_rate']);
			// $total['income']            += $b['amount'];

		echo '<tr>
			<td class="tiny aligntop">' . $b['date'] . '</td>
			<td class="tiny aligntop">' . $b['container_no'] . '</td>
			<td class="tiny aligntop">' . $b['container_size'] . '</td>
			<td class="tiny aligntop">' . $b['transporter_name'] . '</td>
			<td class="tiny aligntop">' . $b['transporter_bill_date'] . '</td>
			<td class="tiny aligntop">' . $b['software_bill_date'] . '</td>
			<td class="tiny aligntop">' . $b['processed_date'] . '</td>
			<td class="tiny aligntop">' . $b['transporter_bill_no'] . '</td>
			<td class="tiny aligntop alignright">' . $b['transporter_rate'] . '</td>
			<td class="tiny aligntop">' . $b['party_name'] . '</td>
			<td class="tiny aligntop">' . $b['transportation_invoice_date'] . '</td>
			<td class="tiny aligntop">' . $b['transportation_invoice_no'] . '</td>
			<td class="tiny aligntop alignright">' . $b['party_rate'] . '</td>
			<td class="tiny aligntop alignright">' . $b['transportation_invoice_amount'] . '</td>
			<td class="tiny aligntop alignright">' . bcsub($b['party_rate'], $b['transporter_rate']) . '</td>
		</tr>';
		}
		echo '</tbody>

		<tfoot>
		<tr>
			<th class="alignright" colspan="8">Total</th>
			<th class="alignright">' . $sub_total['transporter_rate'] . '</th>
			<th></th>
			<th></th>
			<th></th>
			<th class="alignright">' . $sub_total['party_rate'] . '</th>
			<th></th>
			<th class="alignright">' . $sub_total['profit'] . '</th>
		</tr>
		</tfoot>
		</table>
		</td>';
	}
	else {
		echo '<td></td>';
	}

echo '</tr>';
} 
?>
</tbody>
<tfoot>
	<tr>
		<th class="alignright" colspan="10">Total</th>
		<th class="alignright"><?php echo $total['container_20']; ?></th>
		<th class="alignright"><?php echo $total['container_40']; ?></th>
		<th></th>
		<!-- <th class="alignright"><?php echo inr_format($total['expense']); ?></th>
		<th class="alignright"><?php echo inr_format($total['income']); ?></th> -->
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