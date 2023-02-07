<style>
.data-block input { margin-bottom: 0px; }
.SubTotal { background-color: #eee !important; }
.Opening  { background-color: #ffff00 !important; }
.Days     { background-color: #ff6666 !important; }
</style>

<div id="modal-email" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
		<?php echo form_open($this->_clspath.$this->_class.'/combined', 'id="EmailForm"'); ?>
			<input type="hidden" name="companies" value="" id="Companies" />
			<input type="hidden" name="ledgers" value="" id="Ledgers" />
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
						<textarea class="form-control form-control-sm" name="message" rows="5"><?php echo "Dear Sir / Ma'am,\n\nKindly find your Ledger Account Statement(s) in attachment."; ?></textarea>
					</div>
				</fieldset>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-success" onclick="javascript: sendSummaryEmail()"><i class="icon-envelope-o"></i> Summary</button>
				<button type="button" class="btn btn-success" onclick="javascript: sendCombinedEmail()"><i class="icon-envelope-o"></i> Combined</button>
			</div>
			</form>
		</div>
	</div>
</div>


<div id="FixedToolbar">
	<?php echo form_open($this->uri->uri_string(), 'id="Report"'); ?>
	<table class="table toolbar">
	<tr>
		<td width="160px">
			<div class="form-group">
				<label class="control-label">Upto</label>
				<div class="input-group input-group-sm">
					<input type="text" class="form-control form-control-sm DatePicker" name="upto" value="<?php echo $upto ?>">
					<div class="input-group-append">
						<div class="input-group-text"><i class="icon-calendar"></i></div>
					</div>
				</div>
			</div>
		</td>

		<td>
			<div class="form-group">
				<label class="control-label">Account</label>
				<?php if(Auth::isAdmin() OR Auth::get('username') == 'tejash') 
					echo form_dropdown('group_id', getSelectOptions('account_groups', 'id', 'name'), $group_id, 'class="form-control form-control-sm"');
				else 
					echo form_dropdown('group_id', getSelectOptions('account_groups', 'id', 'name', 'WHERE id IN (402, 406)'), $group_id, 'class="form-control form-control-sm"'); ?>
			</div>
		</td>

		<td width="110px">
			<div class="form-group">
				<label class="control-label">First Company</label>
				<?php echo form_dropdown('company_id', array(0=>'')+getSelectOptions('companies', 'id', 'code'), $company_id, 'class="form-control form-control-sm"'); ?>
			</div>
		</td>

		<td>
			<div class="form-group">
				<label class="control-label">Companies</label>
				<?php echo form_dropdown('companies[]', getSelectOptions('companies', 'id', 'code'), $companies, 'multiple class="SelectizeKaabar"'); ?>
			</div>
		</td>

		<td>
			<div class="form-group">
				<label class="control-label">Group</label>
				<div class="btn-group">
					<button type="button" data-toggle="dropdown" class="btn btn-primary dropdown-toggle"><?php echo (strlen($group) > 0 ? '<i class="icon-filter4"></i>' : '') ?> Group <span class="caret"></span></button>
					<ul class="dropdown-menu nav-menu-scroll" id="FilterGroup">
						<li><a class="red" href="javascript: filterGroup('')">Clear Filter</a></li>
					</ul>
				</div>
				<input type="text" class="form-control form-control-sm Text input-small" name="group" value="<?php echo $group ?>" id="ajaxGroup" />
			</div>
		</td>

		<td>
			<div class="form-group">
				<label class="control-label">Collection</label>
				<?php echo form_dropdown('staff_id', $collection_persons, $staff_id, 'class="form-control form-control-sm"'); ?>
			</div>
		</td>

		<td class="nowrap">
			<div class="form-group">
				<label class="control-label">&nbsp;</label><br />
				<button type="submit" class="btn btn-primary" id="SearchButton"><i class="fa fa-search"></i> Search</button>
				<div class="btn-group"><?php 
				echo anchor($this->_clspath.$this->_class."/preview", '<i class="icon-file-o"></i>', 'class="btn btn-default Popup"') .
					anchor($this->_clspath.$this->_class."/preview/1", '<i class="icon-file-pdf"></i>', 'class="btn btn-default Popup"') .
					anchor($this->_clspath.$this->_class."/excel", '<i class="icon-file-excel"></i>', 'class="btn btn-warning"') 
				?></div>
			</div>
		</td>

		<td>
			<div class="form-group">
				<label class="control-label">Days</label>
				<input type="text" class="form-control form-control-sm Numeric" name="days" value="<?php echo $days ?>" />
			</div>
		</td>

		<td class="nowrap">
			<div class="form-group">
				<label class="control-label">Email</label><br />
				<div class="btn-group">
					<a href="#modal-email" class="btn btn-info" data-toggle="modal"><i class="icon-envelope-o"></i></a>
				</div>
			</div>
		</td>
	</tr>
	</table>
	</form>

	<table class="table table-condensed table-bordered hide" id="FixedHeader">
	<thead>
	<tr>
		<th>Code</th>
		<th>Name</th>
	<?php 
		$json_companies = '[';
		$col_group_total[0] = 0;
		foreach ($rows['companies'] as $cid => $company_code) {
			$col_total[$cid] = 0;
			$col_group_total[$cid] = 0;
			echo '<th>' . $company_code . '</th>'; 
			$json_companies .= "{\"company_id\":\"$cid\",\"company_name\":\"$company_code\"},";
		}
		if (strlen($json_companies) > 1)
			$json_companies = substr($json_companies, 0, strlen($json_companies) - 1);
		$json_companies .= ']';
	?>
		<th>Total</th>
	</tr>
	</thead>
	</table>
</div>

<table class="table table-condensed table-bordered" id="Result">
<thead>
<tr>
	<th width="80px">Code</th>
	<th>Name</th>
<?php 
	$col_group_total[0] = 0;
	foreach ($rows['companies'] as $cid => $company_code) {
		$col_total[$cid] = 0;
		$col_group_total[$cid] = 0;
		echo '<th>' . $company_code . '</th>'; 
	}
?>
	<th>Total</th>
</tr>
</thead>

<tbody>
<?php 
$i = 1;
$j = 1;
$filter = array(
	'group'    => array()
);
foreach ($rows['ledgers'] as $group_name => $groups) {
	$filter['group'][$group_name] = 1;

	echo '<tr class="SubTotal">
	<td class="bold" colspan="2">' . $group_name . '<span class="pull-right"><input type="checkbox" class="form-control form-control-sm CheckSelector" id="G_'.$i.'" /></span></td>
	<td class="bold"><span class="pull-left"><input type="checkbox" class="CheckSelector" id="C_'.$company_id.'_'.$i.'" /></span></td>';
	foreach($companies as $index => $cid) {
		echo '<td class="bold"><span class="pull-left"><input type="checkbox" class="CheckSelector" id="C_'.$cid.'_'.$i.'" /></span></td>';
	}
echo '<td class="Bold"></td>
</tr>';
	foreach($groups as $ledger_code => $l) {

		$collection_responsibility = array();
		if (strlen($l['collection_m']) > 0)
			$collection_responsibility[] = '<span class="orange tiny">' . $l['collection_m'] . '</span>';
		if (strlen($l['collection_f1']) > 0)
			$collection_responsibility[] = '<span class="green tiny">' . $l['collection_f1'] . '</span>';
		if (strlen($l['collection_f2']) > 0)
			$collection_responsibility[] = '<span class="green tiny">' . $l['collection_f2'] . '</span>';

		echo '<tr>
	<td>' . $ledger_code . '</td>
	<td>' . $l['name'] . ' (' . implode(", ", $collection_responsibility) . ') 
		<span class="pull-right"><input type="checkbox" class="CheckSelector" id="R_'.$j.'" /></span></td>';
		$row_total = 0;
		foreach ($rows['companies'] as $cid => $company_code) {
			if (isset($l['closing'][$cid])) {
				$days_class = '';
				if ($l['closing'][$cid]['days'] >= $days && $days > 0)
					$days_class = 'Days';

				echo '<td class="alignright ' . 
					($l['closing'][$cid]['only_opening'] ? 'Opening' : $days_class) . 
					'"><span class="pull-left"><input type="checkbox" class="LedgerCheck G_'.$i.' R_'.$j.' C_'.$cid.'_'.$i.'" company_id="' . $cid.'" ledger_id="' . $l['closing'][$cid]['id'] . '" party_code="' . $ledger_code . '" party_name="' . $l['name'] . '" only_opening="' . $l['closing'][$cid]['only_opening'] . '" closing="' . $l['closing'][$cid]['closing'] . '" />&nbsp;&nbsp;&nbsp;' . 
				anchor('/accounting/ledger/edit/'.$l['category'] . '/' . $l['closing'][$cid]['id'] . '/' . $cid, '<i class="icon-pencil"></i>', 'class="" target="_blank"') . '
				</span>' . anchor($this->_clspath.'account/index/'.$l['closing'][$cid]['id'].'/'.$cid, 
					'<span class="' . ($l['closing'][$cid]['closing'] >= 0 ? '' : 'red') . '">' . inr_format($l['closing'][$cid]['closing']) . '</span>', 'target="_blank"') . '</td>';
				$row_total             = bcadd($row_total, $l['closing'][$cid]['closing'], 2);
				$col_total[$cid]       = bcadd($col_total[$cid], $l['closing'][$cid]['closing'], 2);
				$col_group_total[$cid] = bcadd($col_group_total[$cid], $l['closing'][$cid]['closing'], 2);
				$col_group_total[0]    = bcadd($col_group_total[0], $l['closing'][$cid]['closing'], 2);
			}
			else 
				echo '<td class="aligncenter">-</td>';
		}
		echo '<td class="alignright bold"><span class="' . ($row_total >= 0 ? '' : 'red') . '">' . inr_format(number_format($row_total, 2, '.', '')) . '</span></td>
</tr>
';
		$j++;
	}
	echo '<tr>
	<td></td>
	<td class="alignright bold">Group Total</td>';
		foreach ($rows['companies'] as $cid => $company_code) {
			echo '<td class="alignright bold"><span class="' . ($col_group_total[$cid] >= 0 ? '' : 'red') . '">' . inr_format($col_group_total[$cid]) . '</span></td>';
			$col_group_total[$cid] = 0;
		}
		echo '<td class="alignright bold"><span class="' . ($col_group_total[0] >= 0 ? '' : 'red') . '">' . inr_format($col_group_total[0]) . '</span></td>
	</tr>';
	$col_group_total[0] = 0;
	$i++;
}
?>

<tr>
	<td></td>
	<td class="alignright big">Grand Total</td>
<?php 
	$row_total = 0;
	foreach ($rows['companies'] as $cid => $company_code) {
		echo '<td class="alignright big"><span class="' . ($col_total[$cid] >= 0 ? '' : 'red') . '">' . inr_format($col_total[$cid]) . '</span></td>';
		$row_total = bcadd($row_total, $col_total[$cid], 2);
	} 
	echo '<td class="alignright big"><span class="' . ($row_total >= 0 ? '' : 'red') . '">' . inr_format($row_total) . '</span></td>';
?>
</tr>
</tbody>
</table>

<script>
var ledgerWidth = 0;
var ledger_ids = [];
var ledgers    = [];

function sendSummaryEmail() {
	$("#EmailForm").attr('action', '/<?php echo $this->_clspath.$this->_class.'/summary' ?>');
	$("#Companies").val('<?php echo $json_companies ?>');
	$("#Ledgers").val(JSON.stringify(ledgers));
	$("#EmailForm").submit();
}

function sendCombinedEmail() {
	$("#EmailForm").attr('action', '/<?php echo $this->_clspath.$this->_class.'/combined' ?>');
	$("#Companies").val('<?php echo $json_companies ?>');
	$("#Ledgers").val(JSON.stringify(ledgers));
	$("#EmailForm").submit();
}


function filterGroup(search) {
	$('#ajaxGroup').val(search);
	$("#SearchButton").click();
}

$(document).ready(function() {
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

    $("#ajaxGroup").on('focus', function () {
    	ledgerWidth = $(this).outerWidth();
		$(this).animate({ width: "200px" }, 500);
	});
	$("#ajaxGroup").on('blur', function () {
		$(this).animate({ width: ledgerWidth }, 500);
 	});

    $('td.Opening').each(function(i, e) {
    	$(this).parents('tr').children('td').each(function() {
    		$(this).addClass('Opening');
    	});
    });

    $('td.Days').each(function(i, e) {
    	$(this).parents('tr').children('td').each(function() {
    		$(this).addClass('Days');
    	});
    });

    $("input.CheckSelector").on("click", function() {
    	var checked = this.checked;
    	var id = $(this).attr('id');
    	$('.'+id).each(function(){
    		if ((checked && !this.checked) || (!checked && this.checked))
    			this.click();
    	});
    });

    $("input.LedgerCheck").on("click", function() {
    	var company_id   = $(this).attr('company_id');
    	var ledger_id    = $(this).attr('ledger_id');
    	var party_code   = $(this).attr('party_code');
    	var party_name   = $(this).attr('party_name');
    	var only_opening = $(this).attr('only_opening');
    	var closing      = $(this).attr('closing');
    	if (this.checked) {
    		ledger_ids.push(ledger_id);
			ledgers.push({company_id: company_id, ledger_id: ledger_id, code: party_code, name: party_name, only_opening:only_opening, closing: closing});
		}
		else {
			$.each(ledgers, function(key, value) {
			    if (value['ledger_id'] == ledger_id) {
			        ledgers.splice(key, 1);
			        return false;
			    }
			});
		}
	});

    /*$("#ajaxGroup").autocomplete({
		source: '<?php echo site_url($this->_clspath.$this->_class.'/ajaxJson/ledgers/group_name') ?>',
		minLength: 0
	});*/

	$('.ajaxEmail').on('keydown.autocomplete', function(event, items){
		$(this).autocomplete({
			appendTo: '#modal-email',
			source: function(request, response) {
				$.ajax({
					type: "POST",
					url: '<?php echo site_url($this->_clspath.$this->_class.'/ajaxEmail') ?>',
					dataType: "json",
					data: {
						term: extractLast(request.term),
						ledger_id: ledger_ids
					},
					success: function(data) {
						response(data);
					}
				});
	        },
			minLength: 0,
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
				.append('<a>' + item.name + ' <span class="tiny orange">' + item.email + '</span></a>')
				.appendTo(ul);
		};
	});

<?php 
if (count($filter['group']) > 0) {
	ksort($filter['group']);
	foreach ($filter['group'] as $k => $v)
		echo '$("ul#FilterGroup").append("<li><a href=\"javascript: filterGroup(\'' . $k . '\')\">' . $k . '</a></li>");';
}
?>
});
</script>