<style>
.red-color   { color: #F00 !important; }
td.markRed   { background-color: #F33 !important; }
td.markChanged { background-color: #1BDB1A !important; }
td.Pending	 { }
td.Program	 { background-color: #FFD5B0 !important; }
td.Delivery	 { background-color: #7BD57B !important; }
td.Bills	 { background-color: #66CCFF !important; }
td.Completed { background-color: #7BD57B !important; }
</style>

<table class="table toolbar">
<tr>
	<td width="600px" class="nowrap">
		<?php echo form_open($this->uri->uri_string(), 'class="form-search" id="SearchForm"'); ?>
		<input type="hidden" name="search_form" value="1" />
		<div class="col-md-7">
			<div class="input-group">
				<input type="text" class="form-control form-control-sm search-query" name="search" value="<?php echo (isset($search) ? $search : '') ?>" id="Search" />
				<span class="input-group-btn">
					<button type="submit" class="btn btn-primary" id="SearchButton"><i class="fa fa-search"></i> Search</button>
					<a href="#modal-search" class="btn btn-primary" data-toggle="modal"><i class="icon-search-plus"></i></a>
				</span>
			</div>
		</div>
	</td>

	<td class="nowrap">
		<div>
			<button type="button" class="btn btn-group btn-primary" onclick="javascript: clearSearch()"><i class="icon-align-justify"></i></button>
			
			<div class="btn-group">
				<button data-toggle="dropdown" class="btn btn-success dropdown-toggle"><?php echo (isset($parsed_search['party']) ? '<i class="icon-filter4"></i>' : '') ?> Party <span class="caret"></span></button>
				<ul class="dropdown-menu nav-menu-scroll" id="FilterParty">
					<li><a class="red" href="javascript: filter('party:')">Clear Filter</a></li>
				</ul>
			</div>
			<div class="btn-group">
				<button data-toggle="dropdown" class="btn btn-info dropdown-toggle"><?php echo (isset($parsed_search['vessel']) ? '<i class="icon-filter4"></i>' : '') ?> Vessel <span class="caret"></span></button>
				<ul class="dropdown-menu" id="FilterVessel">
					<li><a class="red" href="javascript: filter('vessel:')">Clear Filter</a></li>
				</ul>
			</div>
			<div class="btn-group">
				<button data-toggle="dropdown" class="btn btn-warning dropdown-toggle"><?php echo (isset($parsed_search['line']) ? '<i class="icon-filter4"></i>' : '') ?> Line <span class="caret"></span></button>
				<ul class="dropdown-menu nav-menu-scroll" id="FilterLine">
					<li><a class="red" href="javascript: filter('line:')">Clear Filter</a></li>
				</ul>
			</div>
		
			<div class="btn-group">
				<button data-toggle="dropdown" class="btn btn-danger dropdown-toggle"><?php echo (isset($parsed_search['status']) ? '<i class="icon-filter4"></i>' : '') ?> Status <span class="caret"></span></button>
				<ul class="dropdown-menu">
					<li><a class="red" href="javascript: filterStatus('status:')">Clear Filter</a></li>
					<li><a href="javascript: filterStatus('status: Pending')">Pending</a></li>
					<li><a href="javascript: filterStatus('status: Program')">Program</a></li>
					<li><a href="javascript: filterStatus('status: Delivery')">Delivery</a></li>
					<li><a href="javascript: filterStatus('status: Bills')">Bills</a></li>
					<li><a href="javascript: filterStatus('status: Completed')">Completed</a></li>
				</ul>
			</div>

			<div class="btn-group">
				<button data-toggle="dropdown" class="btn btn-primary dropdown-toggle"><?php echo (isset($parsed_search['icegate']) ? '<i class="icon-filter4"></i>' : '') ?> Icegate <span class="caret"></span></button>
				<ul class="dropdown-menu" id="FilterIcegate">
					<?php foreach ($label_class as $k => $v)
						echo '<li><a href="javascript: filter(\'icegate: ' . $k . '\')">' . $k . '</a></li>';
					?>
					<li><a class="red" href="javascript: filter('icegate:')">Clear Filter</a></li>
				</ul>
			</div>
		</div>
	</td>
</tr>
</table>


<?php echo form_open($this->uri->uri_string(), 'id="PendingForm"'); ?>
<input type="hidden" name="container_form" value="1" />
<table class="table table-condensed table-striped table-bordered" id="Jobs">
<thead>
<tr>
	<th>S</th>
	<th>Job No</th>
	<th>Party Name / <span class="orange">HSS</span> / <span class="red">Shipper</span></th>
	<th>BL No / <span class="orange">BE No</span> / <span class="blue">BL Wt.</span></th>
	<th>Cont. / <span class="blue">CFS</span></th>
	<th class="red">Rcvd Wt</th>
	<th class="green">Dispd Wt</th>
	<th class="bold">Bal Wt</th>
	<th>Shipping Line / <span class="orange">CFS</span></th>
	<th>Vessel / <span class="orange">POD</span></th>
	<th>ETA<br />Free Days</th>
	<th>IceGate</th>
	<th>C. Duty</th>
	<th>Expenses</th>
	<th>Receipts</th>
</tr>
</thead>

<tbody>
<?php
$filter = array(
	'party'  => array(),
	'vessel' => array(),
	'line'   => array(),
);
$total = array(
	'containers'  => 0,
	'custom_duty' => 0,
	'expenses'    => 0,
	'receipts'    => 0,
);
$i = 1;
$dispatch_wt_total = $received_wt_total = $balabce_wt = 0;
foreach ($rows as $r) {
	$total['containers']  += $r['total_containers'];
	$total['custom_duty'] += $r['custom_duty'];
	$total['expenses']    += $r['expenses'];
	$total['receipts']    += $r['receipts'];

	$balabce_wt         = bcsub($r['delivery']['net_weight'], $r['delivery']['dispatch_weight'], 2);
	$dispatch_wt_total += $r['delivery']['net_weight'];
	$received_wt_total += $r['delivery']['dispatch_weight'];

	$filter['party'][$r['party_name']]   = 1;
	$filter['vessel'][$r['vessel_name']] = 1;
	$filter['line'][$r['line_name']]     = 1;
	
	if (intval($r['vessel_id']) > 0) {
		$vessel = $r['vessel_name'] . ' ' . $r['voyage_no'];
		$eta_date 	= $r['eta_date'];
	}
	else {
		$vessel = $r['temp_vessel_name'];
		$eta_date 	= $r['temp_eta'];
	}
	
	echo '<tr id="'.$r['id'].'">
	<td class="aligncenter big '.$r['status'].'"><input type="hidden" name="status[' . $r['id'] . ']" value="' . $r['status'] . '" id="Status_' . $r['id'] . '" /><a href="javascript: showStatus('.$r['id'].')"><i class="';
	if ($r['status'] == 'Pending') echo 'icon-time2';
	else if ($r['status'] == 'Program') echo 'icon-thumbs-o-up';
	else if ($r['status'] == 'Delivery') echo 'icon-truck';
	else if ($r['status'] == 'Bills') echo 'icon-rupee';
	else if ($r['status'] == 'Completed') echo 'icon-check';
	echo '"></i></a><br />' . ($r['house_bl'] == 'Yes' ? '<i class="icon-home"></i><br />' : '') .
	(daysDiff($r['validity'], date('d-m-Y'), 'd-m-Y') > 0 && $r['validity'] != '00-00-0000' ? ' <i class="icon-warning red-color"></i>' : null) . '</td>
	<td class="pending-job ClickJob '.$r['status'].'">' . str_replace('/', '/ ', $r['id2_format']) . '</td>
	<td class="tiny pending-party ClickJob '.$r['status'].'">' . $r['party_name'] . ($r['high_seas'] == null ? '' : '<br /><span class="orange bold">' . $r['high_seas'] . '</span>') . '<br /><span class="red">' . $r['shipper_name'] . '</span></td>
	<td class="tiny pending-bl '.$r['status'].' blue">' . anchor('/import/import_detail/edit/' . $r['job_id'], $r['bl_no'], 'target="_blank"') . '<br /><span class="orange bold">' . $r['be_no'] . '</span><br />' . $r['net_weight'] . ' ' . $r['net_weight_unit'] . '
	</td>
	<td class="aligncenter pending-container '.$r['status'].'">' . $r['containers'];
		if (isset($r['delivery'])) {
			echo '<span class="blueDark"><a href="' . site_url('/import/delivery/index/'.$r['job_id']) . '" target="_blank">';
			$is_container_20 = 0;
			if ($r['delivery']['container_20'] > 0) {
				$is_container_20 = 1;
		 		echo '<br />' . $r['delivery']['container_20'] . 'x20 ';
			}
		 	if ($r['delivery']['container_40'] > 0) {
		 		if (! $is_container_20) 
		 			echo '<br />';
		 		echo $r['delivery']['container_40'] . 'x40';
		 	}
		 	echo '</a></span>';
		}
	echo '</td>
	<td class="alignright bold red '.$r['status'].'">' . $r['delivery']['net_weight'] . '</td>
	<td class="alignright bold green '.$r['status'].'">' . $r['delivery']['dispatch_weight'] . '</td>
	<td class="alignright '.$r['status'].'"><span class="bold">' . $balabce_wt . '</span><br /><span class="tiny pink">' . moment($r['delivery']['last_delivery']) . '</span></td>

	<td class="tiny pending-line ClickJob '.$r['status'].'">' . character_limiter($r['line_name'], 10) . '<br /><span class="orange">' . character_limiter($r['cfs_name'], 10) . '</span></td>

	<td class="tiny pending-vessel '.$r['status'].'" vessel_name="' . $vessel . '">' . (intval($r['vessel_id']) == 0 ? 
			$vessel : anchor('/master/vessel/edit/'.$r['vessel_id'], $vessel)
		) . '<br /><span class="orange">' . $r['indian_port'] . '</span></td>

	<td class="tiny pending-eta ClickJob ' . ($eta_date != '00-00-0000' && daysDiff(date('d-m-Y'), $eta_date, 'd-m-Y') <= 1 && $r['status'] == 'Pending' ? 'markRed' : $r['status']) . '">' . $eta_date . '<br />' . 
		($eta_date == '00-00-0000' ? '' : '<span class="badge badge-default">' . $r['free_days'] . '</span>') . '</td>
	<td class="' . $r['status'] . ($r['last_status'] != $r['current_status'] ? ' markChanged' : '') . '">';
	if (strlen($r['be_no']) > 0) {
		echo anchor('/tracking/icegate_be/index/'. $r['job_id'], 
			($r['query_raised'] != 'N.A.' && strlen($r['query_raised']) > 0 ? '<span class="label label-red">' . (stripos($r['query_raised'], '#') !== false ? '??' : '?') . '</span>' : '') . 
			($r['section_48'] == 'Y' ? '<span class="label label-danger">SEC48</span> ' : '') . 
			'<span class="label ' . $label_class[$r['current_status']] . '">' . $r['current_status'] . '</span>', 
			'class="Popup"');
		}
	if (! is_null($r['challan_id']))
		echo '<span class="label label-default">C</span><br />';
	echo '<div class="tiny pink alignright">' . moment($r['last_fetched']) . '</div></td>

	<td class="bold alignright '.$r['status'] . (isset($r['vouchers']['CD']) ? ' markChanged' : null) . '">' . (isset($r['vouchers']['CD']) ? anchor('/accounting/' . $r['vouchers']['CD']['url'], $r['vouchers']['CD']['amount'], 'class="Popup color-black bold"') : inr_format($r['custom_duty'])) . '</td>
	
	<td class="bold alignright red ClickJob '.$r['status'].'">' . inr_format($r['expenses']) . '</td>
	<td class="bold alignright green ClickJob '.$r['status'].'">' . inr_format($r['receipts']) . '</td>
	</tr>';
}
?>
</tbody>

<tfoot>
<tr>
	<th class="alignright" colspan="4">Total</th>
	<th class="aligncenter"><?php echo $total['containers'] ?></th>
	<th class="aligncenter"><?php echo $dispatch_wt_total ?></th>
	<th class="aligncenter"><?php echo $received_wt_total ?></th>
	<th class="aligncenter"><?php echo ($dispatch_wt_total - $received_wt_total) ?></th>
	<th colspan="4"></th>
	<th class="aligncenter"><?php echo inr_format($total['custom_duty']) ?></th>
	<th class="aligncenter"><?php echo inr_format($total['expenses']) ?></th>
</tr>
</tfoot>
</table>

</form>

<script>
var payment_id = 0;
var pending_id = 0;

function filterStatus(status) {
	$('input#Search').val(status);
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

function showDeliveryOrder(id, job_id) {
	$("#form-delivery-order .RowID").val(id);
	$("#form-delivery-order .JobID").val(job_id);
	v0 = $("#DONo_" + id).val();
	v1 = $("#DODate_" + id).val();
	v2 = $("#DOUpto_" + id).val();
	v3 = $("#DOEmptyReturn_" + id).val();

	$("#DONo").val(v0);
	$("#DODate").val(v1);
	$("#DOUpto").val(v2);
	$("#DOEMptyReturn").val(v3);
	$("#modal-delivery-order").modal();
}

$(document).ready(function() {
	
	$("#CalcLine").on('click', function() {
		var job_ids = <?php echo json_encode(array_keys($rows)) ?>;
		$.ajax({
			type: "POST",
			url: '<?php echo site_url('/import/import_detail/calculate/Line') ?>',
			dataType: "json",
			data: {
				job_id: job_ids
			},
			complete: function(data) {
				$("#modal-refresh").modal();
			}
		});
	});

	$("#CalcCFS").on('click', function() {
		var job_ids = <?php echo json_encode(array_keys($rows)) ?>;
		$.ajax({
			type: "POST",
			url: '<?php echo site_url('/import/import_detail/calculate/CFS') ?>',
			dataType: "json",
			data: {
				job_id: job_ids
			},
			complete: function(data) {
				$("#modal-refresh").modal();
			}
		});
	});

	$("#MICT").on('click', function() {
		var job_ids = <?php echo json_encode(array_keys($rows)) ?>;
		$("#MICT").attr('disabled', true);
		$.ajax({
			type: "POST",
			url: '<?php echo site_url('/tracking/mict/index') ?>',
			dataType: "json",
			data: {
				job_id: job_ids
			},
			complete: function(data) {
				$("#modal-refresh").modal();
			}
		});
	});

	$("#AdaniCT2").on('click', function() {
		$("#AdaniCT2").attr('disabled', true);
		var job_ids = <?php echo json_encode(array_keys($rows)) ?>;
		$.ajax({
			type: "POST",
			url: '<?php echo site_url('/tracking/adani/index/2') ?>',
			dataType: "json",
			data: {
				job_id: job_ids
			},
			complete: function(data) {
				$("#modal-refresh").modal();
			}
		});
	});

	$("#AdaniCT3").on('click', function() {
		$("#AdaniCT3").attr('disabled', true);
		var job_ids = <?php echo json_encode(array_keys($rows)) ?>;
		$.ajax({
			type: "POST",
			url: '<?php echo site_url('/tracking/adani/index/3') ?>',
			dataType: "json",
			data: {
				job_id: job_ids
			},
			complete: function(data) {
				$("#modal-refresh").modal();
			}
		});
	});

	$('.ajaxEmail').on('keydown.autocomplete', function(event, items){
		$(this).autocomplete({
			appendTo: '#modal-email',
			source: function(request, response) {
				$.ajax({
					type: "POST",
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

	$("#Jobs td.ClickJob").on("click", function() {
		id = parseInt($(this).parent('tr').attr("id"));
		$('#ModalJob').html($(this).parent('tr').find('td.pending-job').html());
		$('#ModalParty').html($(this).parent('tr').find('td.pending-party').html());
		$('#ModalBl').html($(this).parent('tr').find('td.pending-bl').html());
		$('#ModalContainer').html($(this).parent('tr').find('td.pending-container').html());
		$('#ModalCha').html($(this).parent('tr').find('td.pending-line').html());

		$('#form-eta .RowID').val(id);
		$('#Vessel').val( $(this).parent('tr').find('td.pending-vessel').attr('vessel_name') );
		$('#ETA').val( $(this).parent('tr').find('td.pending-eta').text() );
		$('#FreeDays').val( $(this).parent('tr').find('td.pending-freedays span.daysCount').text() );
		$('#BLRecieved').val( $(this).parent('tr').find('td.pending-bl-recieved').text() );
		$('#Remarks').text( $(this).parent('tr').find('td.pending-remarks span.Remarks').text() );
		$("#modal-eta").modal();
	});

<?php 
if (count($filter['party']) > 0) {
	ksort($filter['party']);
	foreach ($filter['party'] as $k => $v) {
		echo '$("ul#FilterParty").append("<li><a href=\"javascript: filter(\'party:' . $k . '\')\">' . $k . '</a></li>");';
	}
}
if (count($filter['vessel']) > 0) {
	ksort($filter['vessel']);
	foreach ($filter['vessel'] as $k => $v) {
		echo '$("ul#FilterVessel").append("<li><a href=\"javascript: filter(\'vessel:' . $k . '\')\">' . $k . '</a></li>");';
	}
}
if (count($filter['line']) > 0) {
	ksort($filter['line']);
	foreach ($filter['line'] as $k => $v) {
		echo '$("ul#FilterLine").append("<li><a href=\"javascript: filter(\'line:' . $k . '\')\">' . $k . '</a></li>");';
	}
}
?>
});
</script>