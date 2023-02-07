<style>
.Date, .BLDate { width: 75px; }
.markRed   { background-color: #F33 !important; }
.markGreen { background-color: #46a546 !important; }
</style>

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
						<input type="text" class="form-control form-control-sm" name="subject" value="Container Pending List" />
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

<div id="modal-email-line" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
		<?php echo form_open($this->_clspath.$this->_class.'/email_line'); ?>
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><i class="icon-times"></i></button>
				<h3>Email</h3>
			</div>
			<div class="modal-body">
				<fieldset>
					<div class="form-group">
						<label class="control-label">To</label>
						<input type="text" class="form-control form-control-sm ajaxEmail To" name="to" value="" />
					</div>

					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">CC</label>
								<input type="text" class="form-control form-control-sm ajaxEmail CC" name="cc" value="" />
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">BCC</label>
								<input type="text" class="form-control form-control-sm ajaxEmail BCC" name="bcc" value="" />
							</div>
						</div>
					</div>

					<div class="form-group">
						<label class="control-label">Subject</label>
						<input type="text" class="form-control form-control-sm Subject" name="subject" value="" />
					</div>

					<div class="form-group">
						<label class="control-label">Message</label>
						<textarea class="form-control form-control-sm Message" name="message" rows="5"></textarea>
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

<div id="modal-status" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
		<?php echo form_open($this->_clspath.$this->_class.'/updateStatus', 'id="form-status"'); ?>
			<input type="hidden" name="row_id" value="0" id="RowID" />
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><i class="icon-times"></i></button>
				<h3>Change Job Status</h3>
			</div>
			<div class="modal-body">
				<div class="btn-group" data-toggle="buttons">
					<label class="btn btn-default">
						<input type="radio" name="status" value="Pending" id="option0" /><i class="icon-time2"></i> Pending
					</label>
					<label class="btn btn-warning">
						<input type="radio" name="status" value="Program" id="option1" /><i class="icon-thumbs-o-up"></i> Program
					</label>
					<label class="btn btn-success">
						<input type="radio" name="status" value="Delivery" id="option2" /><i class="icon-truck"></i> Delivery
					</label>
					<label class="btn btn-info">
						<input type="radio" name="status" value="Bills" id="option3" /><i class="icon-rupee"></i> Bills
					</label>
					<label class="btn btn-danger">
						<input type="radio" name="status" value="Completed" id="option4" /><i class="icon-check"></i> Completed
					</label>
				</div>
			</div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-success"><i class="fa fa-search"></i> Update</button>
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

<div id="modal-eta" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">Edit</h4>
			</div>
			<?php echo form_open($this->_clspath.$this->_class.'/updateETA', 'id="form-eta"'); ?>
				<input type="hidden" name="row_id" value="0" class="RowID" />
				<div class="modal-body">
					<div class="row">
						<div class="col-md-3">
							<div class="form-group">
								<label>Job No</label>
								<p id="ModalJob"></p>
							</div>
						</div>
						<div class="col-md-9">
							<div class="form-group">
								<label>Party Name</label>
								<p id="ModalParty"></p>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-3">
							<div class="form-group">
								<label>Cont.</label>
								<p id="ModalContainer"></p>
							</div>
						</div>
						<div class="col-md-5">
							<div class="form-group">
								<label>BL No &amp; Date</label>
								<p id="ModalBl"></p>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label>Shipping line/CHA</label>
								<p id="ModalCha"></p>
							</div>
						</div>
					</div>
					
					<div class="form-group">
						<label class="control-label">Temp Vessel</label>
						<input type="text" class="form-control form-control-sm" name="vessel" value="" id="Vessel" />
					</div>

					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								<label class="control-label">ETA</label>
								<div class="input-group date DatePicker">
									<span class="input-group-addon"><i class="icon-calendar"></i></span>
									<input type="text" class="form-control form-control-sm" name="eta" value="" id="ETA" />
								</div>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label class="control-label">Free Days</label>
								<input type="text" class="form-control form-control-sm" name="free_days" value="" id="FreeDays" />
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label class="control-label">BL Recieved</label>
								<div class="input-group date DateTimePicker">
									<span class="input-group-addon"><i class="icon-calendar"></i></span>
									<input type="text" name="original_bl_received" class="form-control form-control-sm" value="" id="BLRecieved" />
								</div>
							</div>
						</div>
					</div>
					
					<div class="form-group">
						<label class="control-label">Remarks</label>
						<textarea name="remarks" class="form-control form-control-sm" id="Remarks"></textarea>
					</div>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-success">Update</button>
				</div>
			</form>
		</div>
	</div>
</div>

<div id="FixedToolbar">
	<table class="table toolbar">
	<tr>
		<td class="nowrap">
			<?php echo form_open($this->uri->uri_string(), 'class="form-search" id="SearchForm"'); ?>
				<input type="hidden" name="search_form" value="1" />
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
					echo anchor($this->_clspath.$this->_class."/preview/0", '<i class="icon-file-o"></i>', 'class="btn btn-default Popup"') . 
					anchor($this->_clspath.$this->_class."/preview/1", '<i class="icon-file-pdf"></i>', 'class="btn btn-default Popup"') . 
					anchor($this->_clspath.$this->_class."/excel", '<i class="icon-file-excel"></i>', 'class="btn btn-warning Popup"'); ?>
					<a href="#modal-email" class="btn btn-info" data-toggle="modal"><i class="icon-envelope-o"></i></a>
					</div>
				</div>
			</div>
			</form>
		</td>

		<td class="nowrap">
			<div class="btn-group">
				<button type="button" class="btn btn-group btn-primary" onclick="javascript: clearSearch()"><i class="icon-align-justify"></i></button>
				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-success dropdown-toggle"><?php echo (isset($parsed_search['line']) ? '<i class="icon-filter4"></i>' : '') ?> Line <span class="caret"></span></button>
					<ul class="dropdown-menu nav-menu-scroll pull-right" id="FilterLine">
						<li><a class="red" href="javascript: filter('line:')">Clear Filter</a></li>
					</ul>
				</div>

				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-danger dropdown-toggle"><?php echo (isset($parsed_search['party_name']) ? '<i class="icon-filter4"></i>' : '') ?> Party <span class="caret"></span></button>
					<ul class="dropdown-menu nav-menu-scroll pull-right" id="FilterParty">
						<li><a class="red" href="javascript: filter('party:')">Clear Filter</a></li>
					</ul>
				</div>
			</div>
		</td>
		
		<td class="alignright nowrap">
			<?php echo anchor($this->uri->uri_string(), '<i class="icon-refresh"></i>', ' class="btn btn-primary" data-placement="bottom" rel="tooltip" data-original-title="Refresh"') . '&nbsp;' . 
				anchor("/import/jobs/edit/0/Container", '<i class="fa fa-plus"></i>', 'class="btn btn-success" data-placement="bottom" rel="tooltip" data-original-title="Add New Job"'); ?>
		</td>
	</tr>
	</table>

	<?php echo form_open($this->uri->uri_string(), array('class' => 'form-horizontal', 'id' => 'PendingForm')); ?>
	<input type="hidden" name="tracking_form" value="1" />

	<table class="table table-condensed table-striped table-bordered hide" id="FixedHeader">
	<thead>
	<tr>
		<th>S</th>
		<th>Job No</th>
		<th>Party Name / <span class="red">Shipper</span></th>
		<th>BL No / Date / BL Wt.</th>
		<th>Cont. / <span class="blue">CFS</span></th>
		<th>Shipping Line / <span class="orange">CHA</span> / <span class="red">CFS</span></th>
		<th>Vessel / POD</th>
		<th>ETA</th>
		<th>Free Days</th>
		<th>BL Rcvd.</th>
		<th>Remarks</th>
	</tr>
	</thead>
	</table>
</div>

<table class="table table-condensed table-striped table-bordered" id="Jobs">
<thead>
<tr>
	<th>S</th>
	<th>Job No</th>
	<th>Party Name / <span class="red">Shipper</span></th>
	<th>BL No / Date / BL Wt.</th>
	<th>Cont. / <span class="blue">CFS</span></th>
	<th>Shipping Line / <span class="orange">CHA</span> / <span class="red">CFS</span></th>
	<th>Vessel / POD</th>
	<th>ETA</th>
	<th>Free Days</th>
	<th>BL Rcvd.</th>
	<th>Remarks</th>
</tr>
</thead>

<tbody>
<?php
$filter = array(
	'party'  => array(),
	'vessel' => array(),
	'line'   => array(),
);
$prev_vessel = 0;
$alt_color = '#f0f0f0';
foreach ($rows as $p) {
	$filter['party'][$p['party_name']]   = 1;
	$filter['vessel'][$p['vessel_name']] = 1;
	$filter['line'][$p['line_name']]     = 1;

	if (intval($p['vessel_id']) > 0) {
		$vessel   = $p['vessel_name'] . ' ' . $p['voyage_no'];
		$eta_date = $p['eta_date'];
	}
	else {
		$vessel   = $p['temp_vessel_name'];
		$eta_date = $p['temp_eta'];
	}

	if ($prev_vessel != $p['vessel_id']) {
		$alt_color = ($alt_color == '#fff' ? '#f0f0f0' : '#fff');
	}

	echo '<tr id="'.$p['id'].'" style="background-color: ';
	if ($p['status'] == 'Pending')  echo $alt_color;
	if ($p['status'] == 'Program')  echo '#FFD5B0';
	if ($p['status'] == 'Delivery') echo '#7BD57B';
		echo ';">
	<td class="big aligncenter '.$p['status'].'"><input type="hidden" name="status[' . $p['id'] . ']" value="' . $p['status'] . '" id="Status_' . $p['id'] . '" /><a href="javascript: showStatus('.$p['id'].')"><i class="';
	if ($p['status'] == 'Pending') echo 'icon-time2';
	else if ($p['status'] == 'Program') echo 'icon-thumbs-o-up';
	else if ($p['status'] == 'Delivery') echo 'icon-truck';
	else if ($p['status'] == 'Bills') echo 'icon-rupee';
	else if ($p['status'] == 'Completed') echo 'icon-check';
	echo '"></i></a></td>
	<td class="aligncenter ClickJob pending-job">' . $p['id2_format'] . '</td>
	<td class="tiny ClickJob pending-party '.$p['status'].'">' . $p['party_name'] . ($p['high_seas'] == null ? '' : '<br /><span class="orange bold">' . $p['high_seas'] . '</span>') . '<br /><span class="red">' . $p['shipper_name'] . '</span></td>
	<td class="pending-bl">' . anchor('/import/import_detail/edit/' . $p['job_id'], $p['bl_no'], 'target="_blank"') . '<br /><span class="tiny orange">' . $p['bl_date'] . '</span> ' . 
		($p['house_bl'] == 'Yes' ? '<i class="icon-home"></i>' : '') . 
	'</td>
	<td class="tiny pending-container">' . $p['containers'];
		if (isset($p['delivery'])) {
			echo '<a href="' . site_url('/import/delivery/index/'.$p['job_id']) . '" target="_blank">';
			$is_container_20 = 0;
			if ($p['delivery']['container_20'] > 0) {
				$is_container_20 = 1;
		 		echo '<br />' . $p['delivery']['container_20'] . 'x20 ';
			}
		 	if ($p['delivery']['container_40'] > 0) {
		 		if (! $is_container_20) 
		 			echo '<br />';
		 		echo $p['delivery']['container_40'] . 'x40';
		 	}
		 	echo '</a>';
		}
	echo '</td>
	<td class="tiny pending-line"><a href="#" class="LineEmail">' . $p['line_name'] . '</a><br /><span class="tiny orange">' . $p['cha_name']  . '</span><br /><span class="tiny red">' . $p['cfs_name']  . '</span></td>
	<td class="tiny Vessel pending-vessel" vessel_name="' . $vessel . '">' . (intval($p['vessel_id']) == 0 ? 
			'<span class="VesselName">' . $vessel . '</span>' : 
			anchor('/master/vessel/edit/'.$p['vessel_id'], $vessel)
		) . '<br /><span class="tiny orange">' . $p['indian_port'] . (strlen($p['place_of_delivery']) > 0 ? ' >> ' . $p['place_of_delivery'] : '') . '</span></td>
	
	<!-- Mark entry RED if ETA is Less than or equal to Todays date -->
	<td class="ClickJob pending-eta' . (($eta_date != '00-00-0000' && daysDiff(date('d-m-Y'), $eta_date, 'd-m-Y') <= 1 && $p['status'] == 'Pending') ? ' markRed' : null) . '"><input type="hidden" class="form-control form-control-sm Numeric Date" name="vessel_id[' . $p['id'] . ']" value="' . intval($p['vessel_id']) . '" />' . $eta_date . '</td>

	<td class="ClickJob pending-freedays">'.$p['free_days_upto'].' <span class="badge badge-default daysCount">'.$p['free_days'].'</span></td>
	
	<!-- Mark entry GREEN if BL Date is Less not Zero -->
	<td class="ClickJob pending-bl-recieved " ' . ($p['original_bl_received'] != '00-00-0000' ? ' markGreen' : null) . '" >' . $p['original_bl_received'] . '</td>

	<td class="tiny ClickJob pending-remarks"><span class="Remarks">' . $p['remarks'] . '</span></td>

	</tr>';

	$prev_vessel = $p['vessel_id'];
}
?>
</tbody>
</table>
</form>

<script>
var payment_id = 0;
var pending_id = 0;


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

$(document).ready(function() {
	$("#Jobs").find('thead tr').children().each(function(i, e) {
	    $($("#FixedHeader").find('thead tr').children()[i]).width($(e).width());
	});
	$("#FixedHeader").width($("#Jobs").width());

	if (!($.browser == "msie" && $.browser.version < 7)) {
        var target = "div#FixedToolbar"; //, top = $(target).offset().top - parseFloat($(target).css("margin-top").replace(/auto/, 0));
        $(window).scroll(function(event) {
            $(target).css({
			 	left: ($("#Jobs").offset().left - $(window).scrollLeft()) + 'px'
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
					url: '<?php echo site_url('/master/party/ajaxEmail/0/'. (isset($parsed_search['party']) ? htmlentities($parsed_search['party']) : 
							(isset($parsed_search['website']) ? htmlentities($parsed_search['website']) : ''))) ?>',
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

	$('.LineEmail').on('click', function() {
		var id = $(this).parent('td').parent('tr').attr('id');
		$.ajax({
			type: "POST",
			url: '<?php echo site_url($this->_clspath.$this->_class.'/getLineEmail') ?>',
			dataType: "json",
			data: {
				job_id: id
			},
			success: function(data) {
				$('#modal-email-line .To').val(data.to);
				$('#modal-email-line .CC').val(data.cc);
				$('#modal-email-line .BCC').val(data.bcc);
				$('#modal-email-line .Subject').val(data.subject);
				$('#modal-email-line .Message').val(data.message);
			}
		});
		$('#modal-email-line').modal();
	});

<?php 
if (count($filter['party']) > 0) {
	ksort($filter['party']);
	foreach ($filter['party'] as $k => $v) {
		echo '$("ul#FilterParty").append("<li><a href=\"javascript: filter(\'party:' . $k . '\')\">' . $k . '</a></li>");';
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