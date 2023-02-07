<div id="modal-email" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
		<?php echo form_open($this->_clspath.$this->_class.'/preview/0/1', 'id="EmailForm"'); ?>
			<input type="hidden" name="stuffing_id" value="" id="EmailStuffingID" />
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
						<input type="text" class="form-control form-control-sm" name="subject" value="<?php echo $page_title . ' ' . $date ?>" />
					</div>

					<div class="form-group">
						<label class="control-label">Message</label>
						<textarea class="form-control form-control-sm" name="message" rows="5"></textarea>
					</div>
				</fieldset>
			</div>
			<div class="modal-footer">
				<div class="row">
					<div class="col-md-6 alignleft">
						<input type="radio" name="attachment_type" value="PDF" checked="true" /> PDF&nbsp;&nbsp;&nbsp;
						<input type="radio" name="attachment_type" value="Excel" /> Excel
					</div>
					<div class="col-md-6">
						<button type="button" class="btn btn-success" id="Update"><i class="icon-envelope"></i> Send</button>
					</div>
				</div>
			</div>
		</form>
		</div>
	</div>
</div>

<?php echo form_open($this->_clspath.$this->_class, 'target="_blank" id="FormPreview"'); ?>
<input type="hidden" name="stuffing_id" value="" id="StuffingID" />
</form>

<?php echo form_open($this->uri->uri_string(), 'class="form-search" id="MainForm"'); ?>
<input type="hidden" name="search_form" value="1" />
<input type="hidden" name="shipper_id" value="<?php echo $shipper_id ?>" id="ShipperID" />
<input type="hidden" name="shipper_site_id" value="<?php echo $shipper_site_id ?>" id="ShipperSiteID" />
<input type="hidden" name="stuffing_id" value="" id="StuffingID" />
<table class="table toolbar">
<tr>
	<td>
		<div class="input-group input-group-sm">
			<input type="text" class="form-control form-control-sm DatePicker" name="date" value="dd-mm-YY">
			<div class="input-group-append">
				<div class="input-group-text"><i class="icon-calendar"></i></div>
			</div>
		</div>
	</td>

	<td width="170px">
		<label><input type="checkbox" name="show_all" value="1" <?php echo ($show_all ? 'checked="true"' : '') ?> /> Show All</label>
	</td>

	<td class="nowrap">
		<button type="submit" class="btn btn-primary" id="SearchButton"><i class="fa fa-search"></i> Search</button>&nbsp;
		<div class="btn-group">
		<?php 
		if ($shipper_id > 0) {
			echo '<button type="button" class="btn btn-default" id="Preview"><i class="icon-file"></i></button>
				<button type="button" class="btn btn-default" id="PDF"><i class="icon-file-pdf"></i></button>';
			}
			echo '<button type="button" class="btn btn-warning" id="Excel"><i class="icon-file-excel"></i></button>'; 
			if ($shipper_id > 0) {
				echo '<a href="#modal-email" class="btn btn-info" data-toggle="modal"><i class="icon-envelope"></i></a>';
			}
		?>
		</div>
	</td>

	<td>
		<div class="nowrap">
			<button type="button" class="btn btn-group btn-primary" onclick="javascript: clearSearch()"><i class="icon-align-justify"></i></button>
			
			<div class="btn-group">
				<button data-toggle="dropdown" class="btn btn-info dropdown-toggle"><?php echo ($shipper_id > 0 ? '<i class="icon-filter4"></i>' : '') ?> Shipper <span class="caret"></span></button>
				<ul class="dropdown-menu nav-menu-scroll" id="FilterShipper"></ul>
			</div>

			<div class="btn-group">
				<button data-toggle="dropdown" class="btn btn-info dropdown-toggle"><?php echo ($shipper_site_id > 0 ? '<i class="icon-filter4"></i>' : '') ?> Shipper Site <span class="caret"></span></button>
				<ul class="dropdown-menu nav-menu-scroll" id="FilterSite"></ul>
			</div>
		</div>
	</td>
</tr>
</table>
</form>

<table class="table table-condensed table-striped table-bordered" id="Result">
<thead>
<tr>
	<th width="24px" class="aligncenter"><a href="#" class="CheckAll"><i class="icon-check"></i></a></th>
	<th width="24px" class="aligncenter"><i class="icon-envelope"></i></th>
	<th>No</th>
	<th>Job No</th>
	<th>Shipper</th>
	<th>Stuffing Location</th>
	<th>Cargo</th>
	<th>Unit</th>
	<th>LR No</th>
	<th>Vehicle No</th>
	<th>Container No</th>
	<th>Seal No</th>
	<th>FPD</th>
	<th>Stuffing Date</th>
	<th>Line</th>
	<th>Targeted Vessel</th>
	<th>ETA</th>
	<th>ETD</th>
	<th>Cutoff</th>
	<th>Party Ref</th>
	<th>Booking No</th>
</tr>
</thead>

<tbody>
<?php 
$i = 1;
$filter = array(
	'shipper_id'      => array(),
	'shipper_site_id' => array(),
);
foreach ($rows as $r) {
	$filter['shipper_id'][$r['shipper_name']]             = $r['shipper_id'];
	$filter['shipper_site_id'][$r['stuffing_location']] = $r['shipper_site_id'];

	echo '<tr>
	<td class="aligncenter">' . form_checkbox(array('name' => 'check_id['.$r['id'].']', 'value' => $r['id'], 'checked' => false, 'class' => 'DeleteCheckbox')) . '</td>
	<td class="aligncenter">' . ($r['email_sent_stuffing'] == 'Yes' ? '<i class="icon-envelope green"></i>' : '') . '</td>
	<td class="aligncenter tiny">' . $i++ . '</td>
	<td class="aligncenter tiny">' . anchor('/export/jobs/edit/' . $r['job_id'], $r['id2_format'], 'target="_blank"') . '</td>
	<td class="tiny">' . $r['shipper_name'] . '</td>
	<td class="tiny">' . $r['stuffing_location'] . '</td>
	<td class="tiny">' . $r['cargo_name'] . '</td>
	<td class="tiny">' . $r['unit_code'] . '</td>
	<td class="tiny aligncenter">' . $r['lr_no'] . '</td>
	<td class="tiny aligncenter">' . $r['vehicle_no'] . '</td>
	<td class="tiny aligncenter">' . $r['container_no'] . '</td>
	<td class="tiny aligncenter">' . $r['seal_no'] . '</td>
	<td class="tiny">' . $r['fpod'] . '</td>
	<td class="tiny">' . $r['stuffing_date'] . '</td>
	<td class="tiny">' . $r['line_code'] . '</td>
	<td class="tiny">' . $r['vessel_name'] . '</td>
	<td class="tiny">' . $r['eta_date'] . '</td>
	<td class="tiny">' . $r['etd_date'] . '</td>
	<td class="tiny">' . $r['gate_cutoff_date'] . '</td>
	<td class="tiny">' . $r['party_ref'] . '</td>
	<td class="tiny">' . $r['booking_no'] . '</td>
</tr>';
} 
?>
</tbody>
</table>

<script type="text/javascript">
var stuffing_ids = [];

function clearSearch() {
	$('input#ShipperID').val(0);
	$('input#ShipperSiteID').val(0);
	$("#SearchButton").click();
}

function filterShipper(id) {
	$('input#ShipperID').val(id);
	$("#SearchButton").click();
}

function filterSite(id) {
	$('input#ShipperSiteID').val(id);
	$("#SearchButton").click();
}

$(document).ready(function() {
	$('#ajaxShipperSite').kaabar_autocomplete({source: '<?php echo site_url('/master/party/ajaxSite') ?>/' + $('#ShipperID').val()});
	$('#ajaxShipper').on('change', function(event, items) {
		$('#ajaxShipperSite').val('');
		$('#ajaxShipperSite').kaabar_autocomplete({source: '<?php echo site_url('/master/party/ajaxSite') ?>/' + $('#ShipperID').val()});
	});
	$('#ajaxShipper').kaabar_autocomplete({source: '<?php echo site_url($this->_clspath.$this->_class.'/json/parties/id/name') ?>'});
	$('#ajaxGodown').kaabar_autocomplete({source: '<?php echo site_url($this->_clspath.$this->_class.'/json/godowns/id/name') ?>'});

	$("input.DeleteCheckbox").on("click", function() {
		var stuffing_id    = $(this).val();
		if (this.checked) {
			stuffing_ids.push(stuffing_id);
		}
		else {
			$.each(stuffing_ids, function(key, value) {
				if (value == stuffing_id) {
					stuffing_ids.splice(key, 1);
					return false;
				}
			});
		}
	});

	$("#Update").addClass("onEventAttached").on('click', function() {
		$("#EmailStuffingID").val(stuffing_ids);
		$("form#EmailForm").submit();
	});

	$("#Preview").on('click', function() {
		$("#StuffingID").val(stuffing_ids);
		$("form#FormPreview").attr('action', '<?php echo base_url($this->_clspath.$this->_class.'/preview/0') ?>');
		$("form#FormPreview").submit();
	});

	$("#PDF").on('click', function() {
		$("#StuffingID").val(stuffing_ids);
		$("form#FormPreview").attr('action', '<?php echo base_url($this->_clspath.$this->_class.'/preview/1') ?>');
		$("form#FormPreview").submit();
	});

	$("#Excel").on('click', function() {
		$("#StuffingID").val(stuffing_ids);
		$("form#FormPreview").attr('action', '<?php echo base_url($this->_clspath.$this->_class.'/excel/0') ?>');
		$("form#FormPreview").submit();
	});

	$('.ajaxEmail').on('keydown.autocomplete', function(event, items){
		$(this).autocomplete({
			appendTo: '#modal-email',
			source: function(request, response) {
				$.ajax({
					type: "POST",
					url: '<?php echo site_url('/master/party/ajaxEmail/'.$shipper_id) ?>',
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
});

<?php 
if (count($filter['shipper_id']) > 0) {
	ksort($filter['shipper_id']);
	foreach ($filter['shipper_id'] as $k => $v) {
		echo '$("ul#FilterShipper").append("<li><a href=\"javascript: filterShipper(' . $v . ')\">' . $k . '</a></li>");';
	}
	echo '$("ul#FilterShipper").append("<li><a class=\"red\" href=\"javascript: filterShipper(0)\">Clear Filter</a></li>");';
}
if (count($filter['shipper_site_id']) > 0) {
	ksort($filter['shipper_site_id']);
	foreach ($filter['shipper_site_id'] as $k => $v) {
		echo '$("ul#FilterSite").append("<li><a href=\"javascript: filterSite(' . $v . ')\">' . $k . '</a></li>");';
	}
	echo '$("ul#FilterSite").append("<li><a class=\"red\" href=\"javascript: filterSite(0)\">Clear Filter</a></li>");';
}
?>
</script>