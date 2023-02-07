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
		
		<td class="nowrap">
			<button type="button" class="btn btn-group btn-primary" onclick="javascript: clearSearch()"><i class="icon-align-justify"></i></button>

			<div class="btn-group">
				<button data-toggle="dropdown" class="btn btn-info dropdown-toggle"><?php echo (isset($parsed_search['transporter']) ? '<i class="icon-filter4"></i>' : '') ?> Transporter <span class="caret"></span></button>
				<ul class="dropdown-menu nav-menu-scroll pull-right" id="FilterTransporter">
					<li><a class="red" href="javascript: filter('transporter:')">Clear Filter</a></li>
				</ul>
			</div>

			<div class="btn-group">
				<button data-toggle="dropdown" class="btn btn-info dropdown-toggle"><?php echo (isset($parsed_search['cheque_no']) ? '<i class="icon-filter4"></i>' : '') ?> Cheque No <span class="caret"></span></button>
				<ul class="dropdown-menu nav-menu-scroll pull-right" id="FilterChequeNo">
					<li><a class="red" href="javascript: filter('cheque_no:')">Clear Filter</a></li>
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
		<th>Type</th>
		<th>Bill No</th>
		<th>Date</th>
		<th>Transporter Bill No</th>
		<th>Bill Date</th>
		<th>Transporter Name</th>
		<th>Trips</th>
		<th>Cheque No</th>
		<th>Cheque Date</th>
		<th>Processed On</th>
		<th>Amount</th>
	</tr>
	</thead>
	</table>
</div>

<table class="table table-condensed table-striped table-bordered tiny" id="Result">
<thead>
<tr>
	<th>No</th>
	<th>Type</th>
	<th>Bill No</th>
	<th>Date</th>
	<th>Transporter Bill No</th>
	<th>Bill Date</th>
	<th>Transporter Name</th>
	<th>Trips</th>
	<th>Cheque No</th>
	<th>Cheque Date</th>
	<th>Processed On</th>
	<th>Amount</th>
</tr>
</thead>

<tbody>
<?php 
$filter = array(
	'transporter' => array(),
	'cheque_no'   => array(),
);
$total = array(
	'amount' => 0,
);
$i = 1;
foreach ($rows as $bills) {
	$group = array(
		'amount' => 0,
	);
	foreach ($bills as $r) {
		$filter['transporter'][$r['transporter_name']] = 1;
		$filter['cheque_no'][$r['cheque_no']]          = 1;

		$total['amount'] = bcadd($total['amount'], $r['amount'], 2);
		$group['amount'] = bcadd($group['amount'], $r['amount'], 2);

		echo '<tr>
		<td class="aligncenter">' . anchor('transport/trip/edit/'.$r['cargo_type'].'/'.$r['id'], $i++, 'target="_blank"') . '</td>
		<td class="aligncenter">' . $r['type'] . '</td>
		<td class="aligncenter">' . $r['rishi_bill_no'] . '</td>
		<td class="aligncenter">' . $r['date'] . '</td>
		<td>' . $r['bill_no'] . '</td>
		<td>' . $r['bill_date'] . '</td>
		<td>' . $r['transporter_name'] . '</td>
		<td class="aligncenter">' . $r['trips'] . '</td>
		<td>' . $r['cheque_no'] . '</td>
		<td>' . $r['cheque_date'] . '</td>
		<td>' . $r['processed_date'] . '</td>
		<td class="alignright">' . $r['amount'] . '</td>
	</tr>';
	}
	echo '<tr>
	<th class="alignright" colspan="11">Grand Total</th>
	<th class="alignright">' . inr_format($group['amount']) . '</th>
</tr>';
}
?>
</tbody>

<tfoot>
<tr>
	<th class="alignright" colspan="11">Grand Total</th>
	<th class="alignright"><?php echo inr_format($total['amount']) ?></th>
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
if (count($filter['transporter']) > 0) {
	ksort($filter['transporter']);
	foreach ($filter['transporter'] as $k => $v) {
		echo '$("#FilterTransporter").append("<li><a href=\"javascript: filter(\'transporter:' . $k . '\')\">' . $k . '</a></li>");';
	}
}
if (count($filter['cheque_no']) > 0) {
	ksort($filter['cheque_no']);
	foreach ($filter['cheque_no'] as $k => $v) {
		echo '$("#FilterChequeNo").append("<li><a href=\"javascript: filter(\'cheque_no:' . $k . '\')\">' . $k . '</a></li>");';
	}
}
?>
});
</script>