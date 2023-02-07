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

<div id="modal-email" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
		<?php echo form_open($this->_clspath.$this->_class.'/preview/0/1', 'id="EmailForm"'); ?>
			<input type="hidden" name="stuffing_id" value="" id="EmailShipmentID" />
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><i class="icon-times"></i></button>
				<h3><?php echo $page_title ?></h3>
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
						<input type="text" class="form-control form-control-sm" name="subject" value="<?php echo $page_title . ' ' . $from_date . '-' . $to_date ?>" />
					</div>

					<div class="form-group">
						<label class="control-label">Message</label>
						<textarea class="form-control form-control-sm" name="message" rows="5">Dear Sir / Ma'am,

Please find enclosed Shipment Details cum Stuffing Report of Invoice Nos .

Thank you &amp; assure you our best services at all times.</textarea>
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
						<button type="button" class="btn btn-success" id="Update"><i class="icon-envelope-o"></i> Send</button>
					</div>
				</div>
			</div>
			</form>
		</div>
	</div>
</div>

<?php echo form_open($this->_clspath.$this->_class, 'target="_blank" id="FormPreview"'); ?>
<input type="hidden" name="stuffing_id" value="" id="ShipmentID" />
</form>

<div id="FixedToolbar">
	<?php echo form_open($this->uri->uri_string(), 'class="form-search" id="MainForm"'); ?>
	<input type="hidden" name="search_form" value="1" />
	<input type="hidden" name="shipper_id" value="<?php echo $shipper_id ?>" id="PartyID" />
	<input type="hidden" name="shipper_site_id" value="<?php echo $shipper_site_id ?>" id="PartySiteID" />
	<input type="hidden" name="stuffing_id" value="" id="ShipmentID" />
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
							<button type="submit" class="btn btn-primary" id="SearchButton"><i class="icon-search icon-white"></i> Search</button>
				      	</span>
					</div>
				</div>
				<div class="col-md-4">
					<a href="#modal-search" class="btn btn-primary" data-toggle="modal"><i class="icon-search-plus"></i></a>
					<div class="btn-group">
					<?php 
					if ($shipper_id > 0) {
						echo '<button type="button" class="btn btn-default" id="Preview"><i class="icon-file"></i></button>
							<button type="button" class="btn btn-default" id="PDF"><i class="icon-file-pdf"></i></button>';
						}
						echo '<button type="button" class="btn btn-warning" id="Excel"><i class="icon-file-excel"></i></button>'; 
						if ($shipper_id > 0) {
							echo '<a href="#modal-email" class="btn btn-info" data-toggle="modal"><i class="icon-envelope-o"></i></a>';
						}
					?>
					</div>
				</div>
			</div>
		</td>

		<td>
			<div class="nowrap">
				<button type="button" class="btn btn-group btn-primary" onclick="javascript: clearSearch()"><i class="icon-align-justify"></i></button>
				
				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-info dropdown-toggle"><?php echo ($shipper_id > 0 ? '<i class="icon-filter4"></i>' : '') ?> Party <span class="caret"></span></button>
					<ul class="dropdown-menu nav-menu-scroll pull-right" id="FilterParty">
						<li><a class="red" href="javascript: filterParty(0)">Clear Filter</a></li>
					</ul>
				</div>

				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-info dropdown-toggle"><?php echo ($shipper_site_id > 0 ? '<i class="icon-filter4"></i>' : '') ?> Party Site <span class="caret"></span></button>
					<ul class="dropdown-menu nav-menu-scroll pull-right" id="FilterSite">
						<li><a class="red" href="javascript: filterPartySite(0)">Clear Filter</a></li>
					</ul>
				</div>

				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-info dropdown-toggle"><?php echo (isset($parsed_search['job_no']) ? '<i class="icon-filter4"></i>' : '') ?> Job No <span class="caret"></span></button>
					<ul class="dropdown-menu nav-menu-scroll pull-right" id="FilterJobNo">
						<li><a class="red" href="javascript: filter('job_no:')">Clear Filter</a></li>
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
		<th class="aligncenter"><a href="#" class="CheckAll"><i class="icon-check"></i></a></th>
		<th class="aligncenter"><i class="icon-envelope-o"></i></th>
		<th>No</th>
		<th>Job No</th>
		<th>Party</th>
		<th>Consignee</th>
		<th>Invoice No</th>
		<th>Shipment Location</th>
		<th>Cargo</th>
		<th>Packages</th>
		<th>Vehicle No</th>
		<th>Container No</th>
		<th>Seal No</th>
		<th>FPD</th>
		<th>Shipment Date</th>
		<th>Line</th>
		<th>Targeted Vessel</th>
		<th>ETA</th>
		<th>ETD</th>
	</tr>
	</thead>
	</table>
</div>

<table class="table table-condensed table-striped table-bordered" id="Result">
<thead>
<tr>
	<th width="24px" class="aligncenter"><a href="#" class="CheckAll"><i class="icon-check"></i></a></th>
	<th width="24px" class="aligncenter"><i class="icon-envelope-o"></i></th>
	<th>No</th>
	<th>Job No</th>
	<th>Party</th>
	<th>Consignee</th>
	<th>Invoice No</th>
	<th>Shipment Location</th>
	<th>Cargo</th>
	<th>Packages</th>
	<th>Vehicle No</th>
	<th>Container No</th>
	<th>Seal No</th>
	<th>FPD</th>
	<th>Shipment Date</th>
	<th>Line</th>
	<th>Targeted Vessel</th>
	<th>ETA</th>
	<th>ETD</th>
</tr>
</thead>

<tbody>
<?php 
$i = 1;
$filter = array(
	'shipper_id'      => array(),
	'shipper_site_id' => array(),
	'job_no'        => array(),
);
foreach ($rows as $r) {
	$filter['shipper_id'][$r['shipper_name']]             = $r['shipper_id'];
	$filter['shipper_site_id'][$r['stuffing_location']] = $r['shipper_site_id'];
	$filter['job_no'][$r['id2_format']]               = 1;

	echo '<tr>
	<td class="aligncenter">' . form_checkbox(array('name' => 'check_id['.$r['id'].']', 'value' => $r['id'], 'checked' => false, 'class' => 'DeleteCheckbox')) . '</td>
	<td>' . ($r['email_sent_shipment'] == 'Yes' ? '<i class="icon-envelope-o green"></i>' : '') . '</td>
	<td class="aligncenter tiny">' . $i++ . '</td>
	<td class="aligncenter tiny">' . anchor('/export/jobs/edit/' . $r['job_id'], $r['id2_format'], 'target="_blank"') . '</td>
	<td class="tiny">' . $r['shipper_name'] . '</td>
	<td class="tiny">' . $r['consignee_name'] . '</td>
	<td class="aligncenter tiny">' . $r['invoice_no'] . '</td>
	<td class="tiny">' . $r['stuffing_location'] . '</td>
	<td class="tiny">' . $r['product_name'] . '</td>
	<td class="tiny">' . $r['units'] . ' ' . $r['unit_code'] . '</td>
	<td class="tiny aligncenter">' . $r['vehicle_no'] . '</td>
	<td class="tiny aligncenter">' . $r['container_no'] . '</td>
	<td class="tiny aligncenter">' . $r['seal_no'] . '</td>
	<td class="tiny">' . $r['fpod'] . '</td>
	<td class="tiny">' . $r['stuffing_date'] . '</td>
	<td class="tiny">' . $r['line_code'] . '</td>
	<td class="tiny">' . $r['vessel_name'] . '</td>
	<td class="tiny">' . $r['eta_date'] . '</td>
	<td class="tiny">' . $r['etd_date'] . '</td>
</tr>';
} 
?>
</tbody>
</table>

<script type="text/javascript">
var stuffing_ids = [];

function clearSearch() {
	$('input#PartyID').val(0);
	$('input#PartySiteID').val(0);
	$("#SearchButton").click();
}

function filterParty(id) {
	$('input#PartyID').val(id);
	$("#SearchButton").click();
}

function filterSite(id) {
	$('input#PartySiteID').val(id);
	$("#SearchButton").click();
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

	$('#ajaxPartySite').kaabar_autocomplete({source: '<?php echo site_url('/master/party/ajaxSite') ?>/' + $('#PartyID').val()});
	$('#ajaxParty').on('change', function(event, items) {
		$('#ajaxPartySite').val('');
		$('#ajaxPartySite').kaabar_autocomplete({source: '<?php echo site_url('/master/party/ajaxSite') ?>/' + $('#PartyID').val()});
	});
	$('#ajaxParty').kaabar_autocomplete({source: '<?php echo site_url($this->_clspath.$this->_class.'/json/parties/id/name') ?>'});
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
		$("#EmailShipmentID").val(stuffing_ids);
		$("form#EmailForm").submit();
	});

	$("#Preview").on('click', function() {
		$("#ShipmentID").val(stuffing_ids);
		$("form#FormPreview").attr('action', '<?php echo base_url($this->_clspath.$this->_class.'/preview/0') ?>');
		$("form#FormPreview").submit();
	});

	$("#PDF").on('click', function() {
		$("#ShipmentID").val(stuffing_ids);
		$("form#FormPreview").attr('action', '<?php echo base_url($this->_clspath.$this->_class.'/preview/1') ?>');
		$("form#FormPreview").submit();
	});

	$("#Excel").on('click', function() {
		$("#ShipmentID").val(stuffing_ids);
		$("form#FormPreview").attr('action', '<?php echo base_url($this->_clspath.$this->_class.'/excel/0') ?>');
		$("form#FormPreview").submit();
	});

	function splitEmail(val) {
		return val.split(/;\s*/);
	}
	function extractLastEmail(term) {
		return split(term).pop();
	}

	$('.ajaxEmail').on('keydown.autocomplete', function(event, items){
		$(this).autocomplete({
			appendTo: '#modal-email',
			source: function(request, response) {
				$.ajax({
					type: "POST",
					url: '<?php echo site_url('/master/party/ajaxEmail/'.$shipper_id) ?>',
					dataType: "json",
					data: {
						term: extractLastEmail(request.term),
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
				var terms = splitEmail(this.value);
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
		echo '$("ul#FilterParty").append("<li><a href=\"javascript: filterParty(' . $v . ')\">' . $k . '</a></li>");';
	}
}
if (count($filter['shipper_site_id']) > 0) {
	ksort($filter['shipper_site_id']);
	foreach ($filter['shipper_site_id'] as $k => $v) {
		echo '$("ul#FilterSite").append("<li><a href=\"javascript: filterSite(' . $v . ')\">' . $k . '</a></li>");';
	}
}
if (count($filter['job_no']) > 0) {
	ksort($filter['job_no']);
	foreach ($filter['job_no'] as $k => $v) {
		echo '$("ul#FilterJobNo").append("<li><a href=\"javascript: filter(\'job_no:' . $k . '\')\">' . $k . '</a></li>");';
	}
}
?>
</script>