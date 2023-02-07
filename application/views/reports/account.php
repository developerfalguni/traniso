<style>
.data-block input { margin-bottom: 0px; }
</style>

<div id="modal-email" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
		<?php echo form_open($this->_clspath.$this->_class.'/preview/1/1'); ?>
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><i class="icon-times"></i></button>
				<h3>Email Ledger in PDF</h3>
			</div>
			<div class="modal-body">
				<fieldset>
					<div class="form-group">
						<label class="control-label">To</label>
						<input type="text" class="form-control form-control-sm ajaxEmail" name="to" value="<?php echo isset($to_email) ? $to_email : '' ?>" />
					</div>

					<div class="form-group">
						<label class="control-label">CC</label>
						<input type="text" class="form-control form-control-sm ajaxEmail" name="cc" value="" />
					</div>

					<div class="form-group">
						<label class="control-label">BCC</label>
						<input type="text" class="form-control form-control-sm ajaxEmail" name="bcc" value="<?php echo Settings::get('smtp_user') ?>" />
					</div>

					<div class="form-group">
						<label class="control-label">Subject</label>
						<input type="text" class="form-control form-control-sm" name="subject" value="Ledger Account Statement" />
					</div>

					<div class="form-group">
						<label class="control-label">Message</label>
						<textarea class="form-control form-control-sm" name="message" rows="5"><?php echo "Dear Sir / Ma'am,\n\nKindly find your Ledger Account Statement in attachment."; ?></textarea>
					</div>
				</fieldset>
			</div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-success" id="Update"><i class="icon-envelope"></i> Send</button>
			</div>
			</form>
		</div>
	</div>
</div>


<?php echo form_open($this->uri->uri_string(), 'id="Report"'); ?>
<input type="hidden" name="child_id" value="<?php echo $child_id ?>" id="ChildID" />
<input type="hidden" name="filter_id" value="<?php echo $filter_id ?>" id="FilterID" />
<input type="hidden" name="filter2" value="<?php echo $filter2 ?>" id="Filter2" />

<table class="table toolbar">
<tr>
	<td class="nowrap" width="250px">
		<div class="form-group">
			<label class="control-label">From - To</label>
			<input type="hidden" name="from_date" value="<?php echo $from_date ?>" id="FromDate" />
			<input type="hidden" name="to_date"   value="<?php echo $to_date ?>" id="ToDate" />
			<div id="ReportRange" class="pull-left" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc">
				<i class="icon-calendar icon-large"></i> <span></span> <b class="caret"></b>
			</div>
		</div>
	</td>

	<td>
		<div class="form-group">
			<label class="control-label">Ledger</label>
			<input type="hidden" name="ledger_id" value="<?php echo $ledger_id ?>" id="LedgerID" />
			<input type="text" class="form-control form-control-sm" value="<?php echo (isset($ledger['name']) ? $ledger['name'] : '') ?>" id="ajaxLedger" />
		</div>
	</td>

	<td class="nowrap">
		<div class="form-group">
			<label class="control-label">Group Name / Filter</label>
			<div class="nowrap">
				<div class="btn-group">
					<button type="button" data-toggle="dropdown" class="btn btn-info dropdown-toggle">Group <span class="caret"></span></button>
					<ul class="dropdown-menu nav-menu-scroll" id="FilterVessel">
						<?php foreach ($childs as $c) {
							echo '<li><a class="red" href="javascript: filterChild(' . $c['id'] . ')">' . $c['code'] . ' - ' . $c['name'] . '</a></li>';
						} ?>
					</ul>
				</div>
				<div class="btn-group">
					<button type="button" data-toggle="dropdown" class="btn btn-info dropdown-toggle"><i class="icon-filter"></i> <span class="caret"></span></button>
					<ul class="dropdown-menu nav-menu-scroll" id="FilterFilter">
						<li><a class="tiny red" href="javascript: filterFilter('0')">Show All</a></li>
					</ul>
				</div>
				<div class="btn-group">
					<button type="button" data-toggle="dropdown" class="btn btn-info dropdown-toggle"><i class="icon-filter-2"></i> <span class="caret"></span></button>
					<ul class="dropdown-menu nav-menu-scroll" id="FilterFilter2">
						<li><a class="tiny red" href="javascript: filterFilter2('')">Show All</a></li>
						<li><a class="tiny" href="javascript: filterFilter2('Bulk')">Bulk Only</a></li>
						<li><a class="tiny" href="javascript: filterFilter2('Container')">Container Only</a></li>
					</ul>
				</div>
			</div>
		</div>
	</td>

	<td>
		<div class="form-group">
			<label class="control-label">Monthly</label>
			<input type="checkbox" name="monthly" value="1" <?php echo ($monthly ? 'checked="checked"' : '') ?> />
		</div>
	</td>

	<td>
		<div class="form-group">
			<label class="control-label">Description</label>
			<input type="checkbox" name="show_desc" value="1" <?php echo ($show_desc ? 'checked="checked"' : '') ?> />
		</div>
	</td>

	<td class="nowrap">
		<div class="form-group">
			<label class="control-label">&nbsp;</label>
			<button type="submit" class="btn btn-primary" id="SearchButton"><i class="fa fa-search"></i></button>
			<div class="btn-group">
			<?php 
			echo anchor($this->_clspath.$this->_class."/preview", 'Preview', 'class="btn btn-default Popup"') .
				anchor($this->_clspath.$this->_class."/preview/1", 'PDF', 'class="btn btn-default Popup"') .
				anchor($this->_clspath.$this->_class."/excel", '<i class="icon-file-excel"></i>', 'class="btn btn-warning"') . 
				'<a href="#modal-email" class="btn btn-info" data-toggle="modal">Email</a>'
			?>
			</div>
			<a href="#" class="btn btn-danger" onclick="javascript: window.close()"><i class="icon-times"></i></a> 
		</div>
	</td>
</tr>
</table>
</form>

<script type="text/javascript">
$(document).ready(function() {
	<?php echo 'dateRangePicker({
		years:     ['.$years[0].', '.$years[1].'],
		from_date: \''.convDate($from_date).'\',
		to_date:   \''.convDate($to_date).'\'
	});' ?>

	$("#ajaxLedger").autocomplete({
		source: '<?php echo site_url($this->_clspath.$this->_class.'/ajaxParent') ?>',
		minLength: 2,
		focus: function(event, ui) {
			$("#LedgerID").val(ui.item.id);
			$("#ajaxLedger").val(ui.item.code + ' - ' + ui.item.name);
			$("#ChildID").val(0);
			return false;
		},
		select: function(event, ui) {
			$("#LedgerID").val(ui.item.id);
			$("#ajaxLedger").val(ui.item.code + ' - ' + ui.item.name);
			$("#ChildID").val(0);
			$("#ajaxChild").val('');
			$("#ajaxChild").autocomplete('option','source', "<?php echo site_url($this->_clspath.$this->_class.'/ajaxChild') ?>/" + ui.item.id);
			return false;
		},
		response: function(event, ui) {
            if (ui.content.length === 0) {
				$("#LedgerID").val(0);
				$("#ChildID").val(0);
				$("#ajaxChild").val('');
				$("#ajaxChild").autocomplete('option','source', "<?php echo site_url($this->_clspath.$this->_class.'/ajaxChild') ?>/0");
            }
        }
	})
	.data('ui-autocomplete')._renderItem = function(ul, item) {
		return $('<li></li>')
			.data('item.autocomplete', item)
			.append('<a><span class="tiny"><strong class="blueDark">' + item.code + '</strong> ' + item.name + '</span></a>')
			.appendTo(ul);
	};

	/*$("#ajaxChild").autocomplete({
		source: '<?php echo site_url($this->_clspath.$this->_class.'/ajaxChild') ?>/'+$("#LedgerID").val(),
		minLength: 0,
		focus: function(event, ui) {
			$("#ChildID").val(ui.item.id);
			$("#ajaxChild").val(ui.item.code + ' - ' + ui.item.group_name);
			return false;
		},
		select: function(event, ui) {
			$("#ChildID").val(ui.item.id);
			$("#ajaxChild").val(ui.item.code + ' - ' + ui.item.group_name);
			return false;
		},
		response: function(event, ui) {
            if (ui.content.length === 0) {
				$("#ChildID").val(0);
            }
        }
	})
	.data('ui-autocomplete')._renderItem = function(ul, item) {
		return $('<li></li>')
			.data('item.autocomplete', item)
			.append('<a><span class="tiny"><strong class="blueDark">' + item.code + '</strong> ' + item.group_name + '</span></a>')
			.appendTo(ul);
	};*/
});
</script>


<h2 class="aligncenter"><?php echo $company['name'] ?></h2>
<h4 class="aligncenter"><?php echo $page_title ?></h4>

<?php if ($monthly) : ?>

<table class="table table-condensed table-bordered">
<thead>
<tr>
	<th>Month</th>
	<th>Debit</th>
	<th>Credit</th>
	<th>Balance</th>
</tr>
</thead>

<tbody>
<?php 
$total   = array('debit' => 0, 'credit' => 0, 'balance' => 0);
foreach ($rows['vouchers'] as $i => $e) {
	if ($i === 'pdc_cheques') continue;

	if ($i == 0) {
		$total['balance'] += $e['amount'];
		echo '<tr>
	<td class="big">Opening Balance</td>
	<td></td>
	<td></td>
	<td class="big alignright ' . ($total['balance'] >= 0 ? 'green' : 'red') . '">' . inr_format(abs($total['balance'])) . $this->accounting->getDrCr($total['balance']) . '</td>
</tr>';
		continue;
	}
	$total['debit']   += $e['debit'];
	$total['credit']  += $e['credit'];
	$total['balance'] += ($e['debit'] + $e['credit']);

	echo '<tr>
	<td>' . $e['month'] . '</td>
	<td class="alignright">' . ($e['debit'] >= 0 ? inr_format(number_format($e['debit'], 2, '.', '')) : '') . '</td>
	<td class="alignright">' . ($e['credit'] < 0 ? inr_format(number_format(abs($e['credit']), 2, '.', '')) : '') . '</td>
	<td class="alignright ' . ($total['balance'] >= 0 ? 'green' : 'red') . '">' . inr_format(number_format(abs($total['balance']), 2, '.', '')) . $this->accounting->getDrCr($total['balance']) . '</td>
</tr>';
} ?>

<tr>
	<td class="big">Closing Balance</td>
	<td class="big alignright"><?php echo inr_format(abs($total['debit'])) ?></td>
	<td class="big alignright"><?php echo inr_format(abs($total['credit'])) ?></td>
	<td class="big alignright <?php echo ($total['balance'] >= 0 ? 'green' : 'red') ?>"><?php echo inr_format(abs($total['balance'])) . $this->accounting->getDrCr($total['balance']) ?></td>
</tr>
</tbody>
</table>


<?php else : ?>

<table class="table table-condensed table-striped table-bordered hide" id="FixedHeader">
<thead>
<tr>
	<th>Date</th>
	<th>No</th>
	<th>Description</th>
	<th>Debit</th>
	<th>Credit</th>
	<th>Balance</th>
</tr>
</thead>
</table>

<table class="table table-condensed table-striped table-bordered" id="Ledgers">
<thead>
<tr>
	<th>Date</th>
	<th>No</th>
	<th>Description</th>
	<th>Debit</th>
	<th>Credit</th>
	<th>Balance</th>
</tr>
</thead>

<tbody>
<?php 
$filters = array();
$total   = array('debit' => 0, 'credit' => 0, 'balance' => 0);
foreach ($rows['vouchers'] as $i => $e) {
	if ($i < 0) {
		foreach ($e as $pdc) {
			$amount           = $pdc->amount;
			$total['debit']   = bcadd($total['debit'], $pdc->amount, 2);
			$total['balance'] = bcadd($total['balance'], $amount, 2);

			echo '<tr>
	<td class="purple aligncenter aligntop tiny">' . $pdc->cheque_date . '</td>
	<td class="purple aligntop tiny">' . anchor('/master/issued_cheque/edit/'.$pdc->id, $pdc->cheque_no, 'target="_blank"') . '</td>
	<td class="purple tiny">PDC Cheque</td>
	<td class="purple alignright">' . inr_format($pdc->amount) . '</td>
	<td></td>
	<td class="alignright aligntop ' . ($total['balance'] >= 0 ? 'green' : 'red') . '">' . inr_format(number_format(abs($total['balance']), 2, '.', '')) . $this->accounting->getDrCr($total['balance']) . '</td>
</tr>';
		}
		continue;
	}

	if ($i == 0) {
		$total['balance'] = bcadd($total['balance'], $e['amount'], 2);

		echo '<tr>
	<td></td>
	<td></td>
	<td class="big">Opening Balance</td>
	<td></td>
	<td></td>
	<td class="big alignright ' . ($total['balance'] >= 0 ? 'green' : 'red') . '">' . inr_format(abs($total['balance'])) . $this->accounting->getDrCr($total['balance']) . '</td>
</tr>';
		continue;
	}

	$amount           = bcadd($e['debit'], $e['credit'], 2);
	$total['debit']   = bcadd($total['debit'], $e['debit'], 2);
	$total['credit']  = bcadd($total['credit'], $e['credit'], 2);
	$total['balance'] = bcadd($total['balance'], $amount, 2);

	$filters[$e['ledger2_id']] = $e['ledger2'];

	echo '<tr>
	<td class="aligncenter aligntop tiny">' . $e['date'] . '</td>
	<td class="aligntop tiny">' . anchor('/accounting/' . underscore($e['url']), $e['no'].'-'.$e['id3'], 'target="_blank"') . '</td>
	<td class="aligntop tiny">' . $this->accounting->getToBy($amount) . ' ' . $e['ledger2'] . 
		((strlen($e['cheque_no_date']) == 0 OR $e['cheque_no_date'] == '0 / 00-00-0000') ? '' : '&nbsp;&nbsp;&nbsp; Chq No: ' . $e['cheque_no_date']) . 
		((strlen($e['invoice_no_date']) == 0 OR $e['invoice_no_date'] == '0 / 00-00-0000') ? '' : '&nbsp;&nbsp;&nbsp; Bill No: ' . $e['invoice_no_date']);

	if ($show_desc)
		 echo '<br />' . $e['narration'];

	if ($show_desc && isset($e['details']) && count($e['details']) > 0) {
		if ($e['voucher_type_id'] == 2 OR $e['voucher_type_id'] == 3 OR $e['voucher_type_id'] == 4) {
			if (isset($rows['jobs'][$e['job_id']]) && count($rows['jobs'][$e['job_id']]) > 0)
				echo $rows['jobs'][$e['job_id']]->party_name . ' / ' . $rows['jobs'][$e['job_id']]->bl_no . ' / ' . $rows['jobs'][$e['job_id']]->packages . ' ' . $rows['jobs'][$e['job_id']]->package_unit . ' / ' . $rows['jobs'][$e['job_id']]->net_weight . ' ' . $rows['jobs'][$e['job_id']]->net_weight_unit . ' / ' . $rows['jobs'][$e['job_id']]->containers;
		}
		echo '<table class="table table-condensed table-bordered">
		<thead>
		<tr>
			<th>Particulars</th>
			<th width="80px">Amount</th>
		</tr>
		</thead>
		<tbody>';
		foreach ($e['details'] as $d) {
			echo '<tr>';
			if ($e['voucher_type_id'] == 2 OR $e['voucher_type_id'] == 3 OR $e['voucher_type_id'] == 4)
				if ($d->category == "General")
					echo '<td class="tiny">' . $d->bill_item_name . '</td>';
				else
					echo '<td class="tiny">' . $d->particulars . '</td>';
			else 
				echo '<td class="tiny">' . $d->party_name . ' / ' . $d->bl_no . ' / ' . $d->packages . ' ' . $d->package_unit . ' / ' . $d->net_weight . ' ' . $d->net_weight_unit . ' / ' . $d->containers . '</td>';
			
			echo '<td class="alignright aligntop">' . number_format($d->amount, 2, '.', '') . '</td>
			</tr>';
		}
		echo '</tbody></table>';
	}

	echo '</td>
	<td class="alignright aligntop">' . ($e['debit'] > 0 ? inr_format(number_format($e['debit'], 2, '.', '')) : '') . '</td>
	<td class="alignright aligntop">' . ($e['credit'] < 0 ? inr_format(number_format(abs($e['credit']), 2, '.', '')) : '') . '</td>
	<td class="alignright aligntop ' . ($total['balance'] >= 0 ? 'green' : 'red') . '">' . inr_format(number_format(abs($total['balance']), 2, '.', '')) . $this->accounting->getDrCr($total['balance']) . '</td>
</tr>';
} ?>

<tr>
	<td></td>
	<td></td>
	<td class="big">Closing Balance</td>
	<td class="big alignright"><?php echo inr_format(abs($total['debit'])) ?></td>
	<td class="big alignright"><?php echo inr_format(abs($total['credit'])) ?></td>
	<td class="big alignright <?php echo ($total['balance'] >= 0 ? 'green' : 'red') ?>"><?php echo inr_format(abs($total['balance'])) . $this->accounting->getDrCr($total['balance']) ?></td>
</tr>
</tbody>
</table>

<script>
var ledgerWidth = 0;

function filterChild(search) {
	$('input#ChildID').val(search);
	$("#Report").submit();
}

function filterFilter(search) {
	$('input#FilterID').val(search);
	$("#Report").submit();
}

function filterFilter2(search) {
	$('input#Filter2').val(search);
	$("#Report").submit();
}

$(document).ready(function() {
	$("#Ledgers").find('thead tr').children().each(function(i, e) {
	    $($("#FixedHeader").find('thead tr').children()[i]).width($(e).width());
	});
	$("#FixedHeader").width($("#Ledgers").width());

	if (!($.browser == "msie" && $.browser.version < 7)) {
        var target = "table#FixedHeader"; //, top = $(target).offset().top - parseFloat($(target).css("margin-top").replace(/auto/, 0));
        $(window).scroll(function(event) {
            if ($(this).scrollTop() > 180) {
                $(target).addClass("fixedTop show");
                $(target).removeClass("hide");
            } else {
                $(target).removeClass("fixedTop show");
                $(target).addClass("hide");
            }
        });
    }

    $("#ajaxLedger").on('focus', function () {
    	ledgerWidth = $(this).outerWidth();
		$(this).animate({ width: "400px" }, 500);
	});
	$("#ajaxLedger").on('blur', function () {
		$(this).animate({ width: ledgerWidth }, 500);
 	});

 	function split(val) {
		return val.split(/;\s*/);
	}
	function extractLast(term) {
		return split(term).pop();
	}

	$('.ajaxEmail').on('keydown.autocomplete', function(event, items){
		$(this).autocomplete({
			appendTo: '#modal-email',
			source: function(request, response) {
				$.ajax({
					type: "POST",
					url: '<?php echo site_url('/master/party/ajaxEmail/'.$ledger_id) ?>',
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
if (count($filters) > 0) {
	ksort($filters);
	foreach ($filters as $k => $v) {
		echo '$("#FilterFilter").append("<li><a class=\"tiny\" href=\"javascript: filterFilter(\'' . $k . '\')\">' . $v . '</a></li>");';
	}
}
?>
});
</script>

<?php endif; ?>