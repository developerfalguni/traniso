
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
					</div>
				</div>
			</div>
		</td>
		
		<td class="nowrap">
			<button type="button" class="btn btn-group btn-primary" onclick="javascript: clearSearch()"><i class="icon-align-justify"></i></button>

			<div class="btn-group">
				<button data-toggle="dropdown" class="btn btn-info dropdown-toggle"><?php echo (isset($parsed_search['from_location']) ? '<i class="icon-filter4"></i>' : '') ?> From <span class="caret"></span></button>
				<ul class="dropdown-menu nav-menu-scroll pull-right" id="FilterFrom">
					<li><a class="red" href="javascript: filter('from_location:')">Clear Filter</a></li>
				</ul>
			</div>

			<div class="btn-group">
				<button data-toggle="dropdown" class="btn btn-info dropdown-toggle"><?php echo (isset($parsed_search['to_location']) ? '<i class="icon-filter4"></i>' : '') ?> To <span class="caret"></span></button>
				<ul class="dropdown-menu nav-menu-scroll pull-right" id="FilterTo">
					<li><a class="red" href="javascript: filter('to_location:')">Clear Filter</a></li>
				</ul>
			</div>

			<div class="btn-group">
				<button data-toggle="dropdown" class="btn btn-info dropdown-toggle"><?php echo (isset($parsed_search['party']) ? '<i class="icon-filter4"></i>' : '') ?> Party <span class="caret"></span></button>
				<ul class="dropdown-menu nav-menu-scroll pull-right" id="FilterParty">
					<li><a class="red" href="javascript: filter('party:')">Clear Filter</a></li>
				</ul>
			</div>

			<div class="btn-group">
				<button data-toggle="dropdown" class="btn btn-info dropdown-toggle"><?php echo (isset($parsed_search['transporter']) ? '<i class="icon-filter4"></i>' : '') ?> Transporter <span class="caret"></span></button>
				<ul class="dropdown-menu nav-menu-scroll pull-right" id="FilterTransporter">
					<li><a class="red" href="javascript: filter('transporter:')">Clear Filter</a></li>
				</ul>
			</div>

			<div class="btn-group">
				<button data-toggle="dropdown" class="btn btn-info dropdown-toggle"><?php 
				echo (isset($parsed_search['registration_no']) ? '<i class="icon-filter4"></i>' : ''); ?> Vehicle No <span class="caret"></span></button>
				<ul class="dropdown-menu nav-menu-scroll pull-right" id="FilterVehicleNo">
					<li><a class="red" href="javascript: filter('registration_no:')">Clear Filter</a></li>
				</ul>
			</div>
		</td>
	</tr>
	</table>
	</form>

	<table class="table table-condensed table-striped table-bordered tiny hide" id="FixedHeader">
	<thead>
	<tr>
		<th>No</th>
		<th>Date</th>
		<th>Party Ref</th>
		<th>Bill No</th>
		<th>Bill Date</th>
		<th>Container</th>
		<th>Size</th>
		<th>From</th>
		<th>To</th>
		<th>Party</th>
		<th>Vehicle No</th>
		<th>LR No</th>
		<th>Party Rate</th>
		<th>Trans Rate</th>
		<th>Self Adv</th>
		<th>Party Adv</th>
		<th>Pump Adv</th>
		<th>Advance</th>
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
	<th>Party Ref</th>
	<th>Bill No</th>
	<th>Bill Date</th>
	<th>Container</th>
	<th>Size</th>
	<th>From</th>
	<th>To</th>
	<th>Party</th>
	<th>Vehicle No</th>
	<th>LR No</th>
	<th>Party Rate</th>
	<th>Trans Rate</th>
	<th>Self Adv</th>
	<th>Party Adv</th>
	<th>Pump Adv</th>
	<th>Advance</th>
	<th>Balance</th>
</tr>
</thead>

<tbody>
<?php 
$filter = [
	'from_location'   => [],
	'to_location'     => [],
	'product'         => [],
	'vessel'          => [],
	'party'           => [],
	'transporter'     => [],
	'pump'            => [],
	'registration_no' => [],
];
$total = [
	'transporter_rate' => 0,
	'party_rate'       => 0,
	'self_adv'         => 0,
	'party_adv'        => 0,
	'pump_adv'         => 0,
	'allowance'        => 0,
	'balance'          => 0,
	'expenses'         => 0,
];
foreach ($rows as $ledger_id => $vehicles) {
	$i = 1;
	$group = [
		'transporter_rate' => 0,
		'party_rate'       => 0,
		'self_adv'         => 0,
		'party_adv'        => 0,
		'pump_adv'         => 0,
		'allowance'        => 0,
		'balance'          => 0,
	];
	$registration_no = '';
	foreach ($vehicles['trips'] as $r) {
		$filter['from_location'][$r['from_location']]     = 1;
		$filter['to_location'][$r['to_location']]         = 1;
		$filter['product'][$r['product_name']]            = 1;
		$filter['party'][$r['party_name']]                = 1;
		$filter['transporter'][$r['transporter_name']]    = 1;
		$filter['registration_no'][$r['registration_no']] = 1;

		$total['transporter_rate'] = bcadd($total['transporter_rate'], $r['transporter_rate']);
		$total['party_rate']       = bcadd($total['party_rate'], $r['party_rate']);
		$total['self_adv']         = bcadd($total['self_adv'], $r['self_adv']);
		$total['party_adv']        = bcadd($total['party_adv'], $r['party_adv']);
		$total['pump_adv']         = bcadd($total['pump_adv'], $r['pump_adv']);
		$total['allowance']        = bcadd($total['allowance'], $r['allowance']);
		$total['balance']          = bcadd($total['balance'], $r['balance']);

		$group['transporter_rate'] = bcadd($group['transporter_rate'], $r['transporter_rate']);
		$group['party_rate']       = bcadd($group['party_rate'], $r['party_rate']);
		$group['self_adv']         = bcadd($group['self_adv'], $r['self_adv']);
		$group['party_adv']        = bcadd($group['party_adv'], $r['party_adv']);
		$group['pump_adv']         = bcadd($group['pump_adv'], $r['pump_adv']);
		$group['allowance']        = bcadd($group['allowance'], $r['allowance']);
		$group['balance']          = bcadd($group['balance'], $r['balance']);

		echo '<tr>
		<td class="aligncenter">' . anchor('transport/trip/edit/'.$r['cargo_type'].'/'.$r['id'], $i++, 'target="_blank"') . '</td>
		<td class="aligncenter">' . $r['date'] . '</td>
		<td>' . $r['party_reference_no'] . '</td>
		<td>' . $r['bill_no'] . '</td>
		<td>' . $r['bill_date'] . '</td>
		<td>' . $r['container_no'] . '</td>
		<td>' . $r['container_size'] . '</td>
		<td>' . $r['from_location'] . '</td>
		<td>' . $r['to_location'] . '</td>
		<td>' . $r['party_name'] . '</td>
		<td>' . $r['registration_no'] . '</td>
		<td>' . $r['lr_no'] . '</td>
		<td class="alignright">' . inr_format($r['party_rate']) . '</td>
		<td class="alignright">' . inr_format($r['transporter_rate']) . '</td>
		<td class="alignright">' . inr_format($r['self_adv']) . '</td>
		<td class="alignright">' . inr_format($r['party_adv']) . '</td>
		<td class="alignright">' . inr_format($r['pump_adv']) . '</td>
		<td class="alignright">' . inr_format($r['allowance']) . '</td>
		<td class="alignright">' . inr_format($r['balance']) . '</td>
	</tr>';
		$registration_no = $r['registration_no'];
	}

	$total['expenses'] = bcadd($total['expenses'], (isset($vehicles['closing']) ? $vehicles['closing'] : 0));

	echo '<tr>
		<td class="alignright bold" colspan="11">(' . $registration_no . ') Total</td>
		<td></td>
		<td class="alignright bold">' . inr_format($group['party_rate']) . '</td>
		<td class="alignright bold">' . inr_format($group['transporter_rate']) . '</td>
		<td class="alignright bold">' . inr_format($group['self_adv']) . '</td>
		<td class="alignright bold">' . inr_format($group['party_adv']) . '</td>
		<td class="alignright bold">' . inr_format($group['pump_adv']) . '</td>
		<td class="alignright bold">' . inr_format($group['allowance']) . '</td>
		<td class="alignright bold">' . inr_format($group['balance']) . '</td>
	</tr>

	<tr>
		<td class="alignright bold" colspan="18">(' . $registration_no . ') Expenses</td>
		<td class="alignright bold">' . inr_format((isset($vehicles['closing']) ? $vehicles['closing'] : 0)) . '</td>
	</tr>

	<tr>
		<td class="alignright bold" colspan="18">(' . $registration_no . ') Net Amount</td>
		<td class="alignright bold">' . inr_format($group['balance'] - (isset($vehicles['closing']) ? $vehicles['closing'] : 0)) . '</td>
	</tr>';
} 
?>
</tbody>

<tfoot>
<tr>
	<th class="alignright" colspan="11">Grand Total</th>
	<th></th>
	<th class="alignright"><?php echo inr_format($total['party_rate']) ?></th>
	<th class="alignright"><?php echo inr_format($total['transporter_rate']) ?></th>
	<th class="alignright"><?php echo inr_format($total['self_adv']) ?></th>
	<th class="alignright"><?php echo inr_format($total['party_adv']) ?></th>
	<th class="alignright"><?php echo inr_format($total['pump_adv']) ?></th>
	<th class="alignright"><?php echo inr_format($total['allowance']) ?></th>
	<th class="alignright"><?php echo inr_format($total['balance']) ?></th>
</tr>

<tr>
	<th class="alignright bold" colspan="18">Total Expenses</th>
	<th class="alignright bold"><?php echo inr_format($total['expenses']) ?></th>
</tr>

<tr>
	<th class="alignright bold" colspan="18">Net Amount</th>
	<th class="alignright bold"><?php echo inr_format($total['balance'] - $total['expenses']) ?></th>
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


<?php 
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

ksort($filter['transporter']);
foreach ($filter['transporter'] as $k => $v) {
	echo '$("#FilterTransporter").append("<li><a href=\"javascript: filter(\'transporter:' . $k . '\')\">' . $k . '</a></li>");';
}

ksort($filter['registration_no']);
foreach ($filter['registration_no'] as $k => $v) {
	echo '$("#FilterVehicleNo").append("<li><a href=\"javascript: filter(\'registration_no:' . $k . '\')\">' . $k . '</a></li>");';
}
?>
});
</script>