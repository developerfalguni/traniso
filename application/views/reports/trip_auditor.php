
<div id="modal-email" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
		<?php echo form_open($this->_clspath.$this->_class.'/email'); ?>
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
						<input type="text" class="form-control form-control-sm" name="subject" value="Trip Report" />
					</div>

					<div class="form-group">
						<label class="control-label">Message</label>
						<textarea class="form-control form-control-sm" name="message" rows="5"></textarea>
					</div>
				</fieldset>
			</div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-success"><i class="icon-envelope-o"></i> Send</button>
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
				<div class="col-md-7">
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
							<a href="#modal-search" class="btn btn-primary" data-toggle="modal"><i class="icon-search-plus"></i></a>
						</span>
					</div>
				</div>
				<div class="col-md-5">
					<div class="btn-group">
					<?php echo anchor($this->_clspath.$this->_class."/preview/0", '<i class="icon-file-o"></i>', 'class="btn btn-default Popup"') . 
						anchor($this->_clspath.$this->_class."/preview/1", '<i class="icon-file-pdf"></i>', 'class="btn btn-default Popup"') . 
						anchor($this->_clspath.$this->_class."/excel", '<i class="icon-file-excel"></i>', 'class="btn btn-warning Popup"'); ?>
						<a href="#modal-email" class="btn btn-info" data-toggle="modal"><i class="icon-envelope-o"></i></a>
					</div>
				</div>
			</div>
		</td>
	</tr>

	<tr>
		<td colspan="2" class="nowrap">
			<button type="button" class="btn btn-group btn-primary" onclick="javascript: clearSearch()"><i class="icon-align-justify"></i></button>

			<div class="btn-group">
				<button data-toggle="dropdown" class="btn btn-info dropdown-toggle"><?php echo (isset($parsed_search['job_no']) ? '<i class="icon-filter4"></i>' : '') ?> Job No <span class="caret"></span></button>
				<ul class="dropdown-menu nav-menu-scroll" id="FilterJobNo">
					<li><a class="red" href="javascript: filter('job_no:')">Clear Filter</a></li>
				</ul>
			</div>

			<div class="btn-group">
				<button data-toggle="dropdown" class="btn btn-info dropdown-toggle"><?php echo (isset($parsed_search['from_location']) ? '<i class="icon-filter4"></i>' : '') ?> From <span class="caret"></span></button>
				<ul class="dropdown-menu nav-menu-scroll" id="FilterFrom">
					<li><a class="red" href="javascript: filter('from_location:')">Clear Filter</a></li>
				</ul>
			</div>

			<div class="btn-group">
				<button data-toggle="dropdown" class="btn btn-info dropdown-toggle"><?php echo (isset($parsed_search['to_location']) ? '<i class="icon-filter4"></i>' : '') ?> To <span class="caret"></span></button>
				<ul class="dropdown-menu nav-menu-scroll" id="FilterTo">
					<li><a class="red" href="javascript: filter('to_location:')">Clear Filter</a></li>
				</ul>
			</div>

			<div class="btn-group">
				<button data-toggle="dropdown" class="btn btn-info dropdown-toggle"><?php echo (isset($parsed_search['party']) ? '<i class="icon-filter4"></i>' : '') ?> Party <span class="caret"></span></button>
				<ul class="dropdown-menu nav-menu-scroll" id="FilterParty">
					<li><a class="red" href="javascript: filter('party:')">Clear Filter</a></li>
				</ul>
			</div>

			<div class="btn-group">
				<button data-toggle="dropdown" class="btn btn-info dropdown-toggle"><?php echo (isset($parsed_search['rishi_bill_no']) ? '<i class="icon-filter4"></i>' : '') ?> Rishi Bill No <span class="caret"></span></button>
				<ul class="dropdown-menu nav-menu-scroll pull-right" id="FilterRishiBillNo">
					<li><a class="red" href="javascript: filter('rishi_bill_no:')">Clear Filter</a></li>
				</ul>
			</div>

			<div class="btn-group">
				<button data-toggle="dropdown" class="btn btn-info dropdown-toggle"><?php echo (isset($parsed_search['rishi_cheque_no']) ? '<i class="icon-filter4"></i>' : '') ?> Cheque No <span class="caret"></span></button>
				<ul class="dropdown-menu nav-menu-scroll pull-right" id="FilterChequeNo">
					<li><a class="red" href="javascript: filter('rishi_cheque_no:')">Clear Filter</a></li>
					<li><a href="javascript: filter('rishi_cheque_no: Paid')">Paid</a></li>
					<li><a href="javascript: filter('rishi_cheque_no: Processed')">Processed</a></li>
					<li><a href="javascript: filter('rishi_cheque_no: Pending')">Pending</a></li>
				</ul>
			</div>

			<div class="btn-group">
				<button data-toggle="dropdown" class="btn btn-info dropdown-toggle"><?php echo (isset($parsed_search['transporter']) ? '<i class="icon-filter4"></i>' : '') ?> Transporter <span class="caret"></span></button>
				<ul class="dropdown-menu nav-menu-scroll pull-right" id="FilterTransporter">
					<li><a class="red" href="javascript: filter('transporter:')">Clear Filter</a></li>
				</ul>
			</div>

			<div class="btn-group">
				<button data-toggle="dropdown" class="btn btn-info dropdown-toggle"><?php echo (isset($parsed_search['transporter_bill_no']) ? '<i class="icon-filter4"></i>' : '') ?> Trans. Bill No <span class="caret"></span></button>
				<ul class="dropdown-menu nav-menu-scroll pull-right" id="FilterTransporterBill">
					<li><a class="red" href="javascript: filter('transporter_bill_no:')">Clear Filter</a></li>
				</ul>
			</div>

			<div class="btn-group">
				<button data-toggle="dropdown" class="btn btn-info dropdown-toggle"><?php echo (isset($parsed_search['t_inward']) ? '<i class="icon-filter4"></i>' : '') ?> T. Inward <span class="caret"></span></button>
				<ul class="dropdown-menu nav-menu-scroll pull-right" id="FilterBillNo">
					<li><a class="red" href="javascript: filter('t_inward:')">Clear Filter</a></li>
					<li><a href="javascript: filter('t_inward:1')">Paid</a></li>
					<li><a href="javascript: filter('t_inward:0')">Pending</a></li>
				</ul>
			</div>

			<div class="btn-group">
				<button data-toggle="dropdown" class="btn btn-info dropdown-toggle"><?php echo (isset($parsed_search['pump']) ? '<i class="icon-filter4"></i>' : '') ?> Pump <span class="caret"></span></button>
				<ul class="dropdown-menu nav-menu-scroll pull-right" id="FilterPump">
					<li><a class="red" href="javascript: filter('pump:')">Clear Filter</a></li>
				</ul>
			</div>

			<div class="btn-group">
				<button data-toggle="dropdown" class="btn btn-info dropdown-toggle"><?php echo (isset($parsed_search['f_inward']) ? '<i class="icon-filter4"></i>' : '') ?> P. Inward <span class="caret"></span></button>
				<ul class="dropdown-menu nav-menu-scroll pull-right" id="FilterBillNo">
					<li><a class="red" href="javascript: filter('f_inward:')">Clear Filter</a></li>
					<li><a href="javascript: filter('f_inward:1')">Paid</a></li>
					<li><a href="javascript: filter('f_inward:0')">Pending</a></li>
				</ul>
			</div>

			<div class="btn-group">
				<button data-toggle="dropdown" class="btn btn-info dropdown-toggle"><?php 
				echo (isset($parsed_search['registration_no']) ? '<i class="icon-filter4"></i>' : ''); ?> Vehicle No <span class="caret"></span></button>
				<ul class="dropdown-menu nav-menu-scroll pull-right" id="FilterVehicleNo">
					<li><a class="red" href="javascript: filter('registration_no:')">Clear Filter</a></li>
				</ul>
			</div>

			<div class="btn-group">
				<button data-toggle="dropdown" class="btn btn-info dropdown-toggle"><?php echo (isset($parsed_search['owned']) ? '<i class="icon-filter4"></i>' : '') ?> Owned <span class="caret"></span></button>
				<ul class="dropdown-menu nav-menu-scroll pull-right" id="FilterTransporter">
					<li><a class="red" href="javascript: filter('owned:')">Clear Filter</a></li>
					<li><a href="javascript: filter('owned:1')">Yes</a></li>
					<li><a href="javascript: filter('owned:0')">No</a></li>
				</ul>
			</div>

			<div class="btn-group">
				<button data-toggle="dropdown" class="btn btn-info dropdown-toggle"><?php echo (isset($parsed_search['billed']) ? '<i class="icon-filter4"></i>' : '') ?> Bill No <span class="caret"></span></button>
				<ul class="dropdown-menu nav-menu-scroll pull-right" id="FilterBillNo">
					<li><a class="red" href="javascript: filter('billed:')">Clear Filter</a></li>
					<li><a href="javascript: filter('billed:1')">Billed</a></li>
					<li><a href="javascript: filter('billed:0')">Unbilled</a></li>
				</ul>
			</div>

			<div class="btn-group">
				<button data-toggle="dropdown" class="btn btn-info dropdown-toggle"><?php echo (isset($parsed_search['bill_date']) ? '<i class="icon-filter4"></i>' : '') ?> Bill Date <span class="caret"></span></button>
				<ul class="dropdown-menu nav-menu-scroll pull-right" id="FilterBillDate">
					<li><a class="red" href="javascript: filter('bill_date:')">Clear Filter</a></li>
				</ul>
			</div>
		</td>
	</tr>
	</table>
	</form>

	<table class="table table-condensed table-striped table-bordered tiny hide" id="FixedHeader">
	<thead>
	<tr>
		<th width="24px">No</th>
		<th>Date</th>
		<th>LR No</th>
		<th>Vehicle No</th>
		<th>Container</th>
		<th>Size</th>
		<th>Weight</th>
		<th>Job No</th>
		<th>Ref No</th>
		<th>From</th>
		<th>To</th>
		<th>Party Name</th>
		<th>Party Rate</th>
		<th>Invoice No</th>
		<th>Invoice Date</th>
		<th>Transporter</th>
		<th>Trans Rate</th>
		<th>T.Bill No</th>
		<th>T.Bill Date</th>
		<th>Self Adv</th>
		<th>Party Adv</th>
		<th>Pump Name</th>
		<th>Pump Adv</th>
		<th>Allowance</th>
		<th>Balance</th>
	</tr>
	</thead>
	</table>
</div>

<table class="table table-condensed table-striped table-bordered tiny" id="Result">
<thead>
<tr>
	<th width="24px">No</th>
	<th>Date</th>
	<th>LR No</th>
	<th>Vehicle No</th>
	<th>Container</th>
	<th>Size</th>
	<th>Weight</th>
	<th>Job No</th>
	<th>Ref No</th>
	<th>From</th>
	<th>To</th>
	<th>Party Name</th>
	<th>Party Rate</th>
	<th>Invoice No</th>
	<th>Invoice Date</th>
	<th>Transporter</th>
	<th>Trans Rate</th>
	<th>T.Bill No</th>
	<th>T.Bill Date</th>
	<th>Self Adv</th>
	<th>Party Adv</th>
	<th>Pump Name</th>
	<th>Pump Adv</th>
	<th>Allowance</th>
	<th>Balance</th>
</tr>
</thead>

<tbody>
<?php 
$filter = [
	'job_no'              => [],
	'from_location'       => [],
	'to_location'         => [],
	'vessel'              => [],
	'party'               => [],
	'rishi_bill_no'       => [],
	'transporter'         => [],
	'transporter_bill_no' => [],
	'pump'                => [],
	'registration_no'     => [],
	'bill_date'           => [],
];
$total = [
	'transporter_rate' => 0,
	'party_rate'       => 0,
	'self_adv'         => 0,
	'party_adv'        => 0,
	'pump_adv'         => 0,
	'allowance'        => 0,
	'balance'          => 0,
	'cheque_advance'   => 0,
];
$i = 1;
foreach ($rows as $r) {
	$filter['job_no'][$r['job_no']]                           = 1;
	$filter['from_location'][$r['from_location']]             = 1;
	$filter['to_location'][$r['to_location']]                 = 1;
	$filter['party'][$r['party_name']]                        = 1;
	$filter['rishi_bill_no'][$r['rishi_bill_no']]             = 1;
	$filter['transporter'][$r['transporter_name']]            = 1;
	$filter['transporter_bill_no'][$r['transporter_bill_no']] = 1;
	$filter['pump'][$r['pump_name']]                          = 1;
	$filter['registration_no'][$r['registration_no']]         = 1;
	$filter['bill_date'][$r['bill_date']]                     = 1;

	$total['transporter_rate'] = bcadd($total['transporter_rate'], $r['transporter_rate'], 2);
	$total['party_rate']       = bcadd($total['party_rate'], $r['party_rate'], 2);
	$total['self_adv']         = bcadd($total['self_adv'], $r['self_adv'], 2);
	$total['party_adv']        = bcadd($total['party_adv'], $r['party_adv'], 2);
	$total['pump_adv']         = bcadd($total['pump_adv'], $r['pump_adv'], 2);
	$total['allowance']        = bcadd($total['allowance'], $r['allowance'], 2);
	$total['balance']          = bcadd($total['balance'], $r['balance'], 2);
	$total['cheque_advance']   = bcadd($total['cheque_advance'], $r['cheque_advance'], 2);

	echo '<tr>
	<td class="aligncenter">' . anchor('transport/trip/edit/'.$r['cargo_type'].'/'.$r['id'], $i++, 'target="_blank"') . '</td>
	<td class="aligncenter">' . $r['date'] . '</td>
	<td>' . $r['lr_no'] . '</td>
	<td>' . ($r['self'] ? '<span class="label label-success">' . $r['registration_no'] . '</span>' : $r['registration_no']) . '</td>
	<td>' . $r['container_no'] . '</td>
	<td>' . $r['container_size'] . '</td>
	<td>' . $r['weight'] . '</td>
	<td>' . $r['job_no'] . '</td>
	<td>' . $r['party_reference_no'] . '</td>
	<td>' . $r['from_location'] . '</td>
	<td>' . $r['to_location'] . '</td>
	<td>' . $r['party_name'] . '</td>
	<td class="alignright">' . inr_format($r['party_rate']) . '</td>
	<td>' . $r['bill_no'] . '</td>
	<td>' . $r['bill_date'] . '</td>
	<td>' .  ($r['transporter_inward_id'] > 0 ? anchor('transport/trip_inward/edit/'.$r['transporter_inward_id'], $r['transporter_name'], 'target="_blank"') : $r['transporter_name']) . '</td>
	<td class="alignright">' . inr_format($r['transporter_rate']) . '</td>
	<td>' . $r['transporter_bill_no'] . '</td>
	<td>' . $r['transporter_bill_date'] . '</td>
	<td class="alignright">' . inr_format($r['self_adv']) . '</td>
	<td class="alignright">' . inr_format($r['party_adv']) . '</td>
	<td>' .  ($r['fuel_inward_id'] > 0 ? anchor('transport/trip_inward/fuel_edit/'.$r['fuel_inward_id'], $r['pump_name'], 'target="_blank"') : $r['pump_name']) . '</td>
	<td class="alignright">' . inr_format($r['pump_adv']) . '</td>
	<td class="alignright">' . inr_format($r['allowance']) . '</td>
	<td class="alignright">' . inr_format($r['balance']) . '</td>
</tr>';
} 
?>
</tbody>

<tfoot>
<tr>
	<th class="alignright" colspan="12">Total</th>
	<th class="alignright"><?php echo inr_format($total['party_rate']) ?></th>
	<th colspan="3"></th>
	<th class="alignright"><?php echo inr_format($total['transporter_rate']) ?></th>
	<th colspan="2"></th>
	<th class="alignright"><?php echo inr_format($total['self_adv']) ?></th>
	<th class="alignright"><?php echo inr_format($total['party_adv']) ?></th>
	<th></th>
	<th class="alignright"><?php echo inr_format($total['pump_adv']) ?></th>
	<th class="alignright"><?php echo inr_format($total['allowance']) ?></th>
	<th class="alignright"><?php echo inr_format(round($total['balance'], 0)) ?></th>
</tr>
</tfoot>
</table>

<script>

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
        var target = "div#FixedToolbar"; //, top = $(target).offset().top - parseFloat($(target).css("margin-top").replace(/auto/, 0));
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
					url: '<?php echo site_url('/master/party/ajaxUsersEmail') ?>',
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
ksort($filter['job_no']);
foreach ($filter['job_no'] as $k => $v) {
	echo '$("#FilterJobNo").append("<li><a href=\"javascript: filter(\'job_no:' . $k . '\')\">' . $k . '</a></li>");';
}

ksort($filter['from_location']);
foreach ($filter['from_location'] as $k => $v) {
	echo '$("#FilterFrom").append("<li><a href=\"javascript: filter(\'from_location:' . $k . '\')\">' . $k . '</a></li>");';
}

ksort($filter['to_location']);
foreach ($filter['to_location'] as $k => $v) {
	echo '$("#FilterTo").append("<li><a href=\"javascript: filter(\'to_location:' . $k . '\')\">' . $k . '</a></li>");';
}

ksort($filter['party']);
foreach ($filter['party'] as $k => $v) {
	echo '$("#FilterParty").append("<li><a href=\"javascript: filter(\'party:' . $k . '\')\">' . $k . '</a></li>");';
}

ksort($filter['rishi_bill_no']);
foreach ($filter['rishi_bill_no'] as $k => $v) {
	echo '$("#FilterRishiBillNo").append("<li><a href=\"javascript: filter(\'rishi_bill_no:' . $k . '\')\">' . $k . '</a></li>");';
}

ksort($filter['transporter']);
foreach ($filter['transporter'] as $k => $v) {
	echo '$("#FilterTransporter").append("<li><a href=\"javascript: filter(\'transporter:' . (strlen($k) == 0 ? 'Pending' : $k) . '\')\">' . (strlen($k) == 0 ? 'Pending' : $k) . '</a></li>");';
}

ksort($filter['transporter_bill_no']);
foreach ($filter['transporter_bill_no'] as $k => $v) {
	echo '$("#FilterTransporterBill").append("<li><a href=\"javascript: filter(\'transporter_bill_no:' . $k . '\')\">' . $k . '</a></li>");';
}

ksort($filter['pump']);
foreach ($filter['pump'] as $k => $v) {
	echo '$("#FilterPump").append("<li><a href=\"javascript: filter(\'pump:' . $k . '\')\">' . $k . '</a></li>");';
}

ksort($filter['registration_no']);
foreach ($filter['registration_no'] as $k => $v) {
	echo '$("#FilterVehicleNo").append("<li><a href=\"javascript: filter(\'registration_no:' . $k . '\')\">' . $k . '</a></li>");';
}

ksort($filter['bill_date']);
foreach ($filter['bill_date'] as $k => $v) {
	echo '$("#FilterBillDate").append("<li><a href=\"javascript: filter(\'bill_date:' . $k . '\')\">' . $k . '</a></li>");';
}
?>
});
</script>