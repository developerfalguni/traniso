<style>
.markChanged { background-color: #1BDB1A !important; }
</style>

<div id="modal-email" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<?php echo form_open($this->_clspath.$this->_class.'/email/' . $job_id, 'id="MainForm"'); ?>
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
						<input type="text" class="form-control form-control-sm" name="subject" value="<?php echo $page_title ?>" />
					</div>

					<div class="form-group">
						<label class="control-label">Message</label>
						<textarea class="form-control form-control-sm" name="message" rows="5"></textarea>
					</div>
				</fieldset>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-success" id="Update"><i class="icon-envelope-o"></i> Send</button>
			</div>
			</form>
		</div>
	</div>
</div>

<div id="modal-delivery" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><i class="icon-times"></i></button>
				<h3>Edit Delivery Details</h3>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label class="control-label">Container No</label>
							<input type="hidden" name="container_type_id" value="" id="container_type_id" />
							<input type="text" class="form-control form-control-sm" name="container_no" value="" id="container_no" />
						</div>
					</div>

					<div class="col-md-2">
						<div class="form-group">
							<label class="control-label">Size</label>
							<input type="text" class="form-control form-control-sm" value="" id="size" readonly="true" />
						</div>
					</div>

					<div class="col-md-4">
						<div class="form-group">
							<label class="control-label">BL No</label>
							<input type="text" class="form-control form-control-sm" value="" id="bl_no" readonly="true" />
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-3">
						<div class="form-group">
							<label class="control-label">Vehicle No</label>
							<input type="text" class="form-control form-control-sm" name="vehicle_no" value="" id="vehicle_no" />
						</div>
					</div>

					<div class="col-md-2">
						<div class="form-group">
							<label class="control-label">Disp. Weight</label>
							<input type="text" class="form-control form-control-sm" name="dispatch_weight" value="" id="dispatch_weight" />
						</div>
					</div>

					<div class="col-md-3">
						<div class="form-group">
							<label class="control-label">Dispatch Type</label>
							<?php echo form_dropdown('dispatch_type', getEnumSetOptions('deliveries_stuffings', 'dispatch_type'), NULL, 'class="form-control form-control-sm" id="dispatch_type"') ?>
						</div>
					</div>

					<div class="col-md-4">
						<div class="form-group">
							<label class="control-label">Unloading Location</label>
							<input type="text" class="form-control form-control-sm" name="unloading_location" id="unloading_location" />
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-4">
						<div class="form-group">
							<label class="control-label">GatePass No</label>
							<input type="text" class="form-control form-control-sm" name="gatepass_no" value="" id="gatepass_no" />
						</div>
					</div>

					<div class="col-md-4">
						<div class="form-group">
							<label class="control-label">GatePass Date</label>
							<div class="input-group date DatePicker">
								<span class="input-group-addon"><i class="icon-calendar"></i></span>
								<input type="text" class="form-control form-control-sm AutoDate" name="gatepass_date" value="" id="gatepass_date" />
							</div>
						</div>
					</div>

					<div class="col-md-4">
						<div class="form-group">
							<label class="control-label">Self LR No</label>
							<input type="text" class="form-control form-control-sm" name="lr_no" value="" id="lr_no" />
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-4">
						<div class="form-group">
							<label class="control-label">Return Date</label>
							<div class="input-group date DatePicker">
								<span class="input-group-addon"><i class="icon-calendar"></i></span>
								<input type="text" class="form-control form-control-sm AutoDate" name="return_date" value="" id="return_date" />
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-success" data-dismiss="modal" id="Save">Save</button>
				<button type="button" class="btn btn-danger" data-dismiss="modal" id="Delete">Delete</button>
			</div>
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
			<div class="modal-body form-horizontal">
				<fieldset>
					<?php foreach($search_fields as $f => $n) : ?>
					<div class="form-group">
						<label class="control-label col-md-4"><?php echo humanize($f) ?></label>
						<div class="col-md-8">
							<td><input type="text" class="form-control form-control-sm AdvancedSearch" name="$f" value="<?php echo (isset($parsed_search[$f]) ? $parsed_search[$f] : '') ?>" /></td>
						</div>
					</div>
					<?php endforeach; ?>
				</fieldset>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" onclick="javascript: combineSearch()"><i class="fa fa-search"></i> Search</button>
			</div>
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
							<a href="#modal-search" class="btn btn-primary" data-toggle="modal"><i class="icon-search-plus"></i></a>
						</span>
					</div>
				</div>

				<div class="col-md-4">
				<div class="btn-group">
				<?php
				if ($job_id > 0) {
					echo anchor($this->_clspath.$this->_class.'/preview/' . $job_id . '/0/0', '<i class="icon-file-o"></i>', 'class="btn btn-default Popup"') . 
						anchor($this->_clspath.$this->_class.'/preview/' . $job_id . '/1/0', '<i class="icon-file-pdf"></i>', 'class="btn btn-default Popup"') .
						anchor($this->_clspath.$this->_class.'/excel/' . $job_id, '<i class="icon-file-excel"></i>', 'class="btn btn-warning Popup"') .
						'<a href="#modal-email" class="btn btn-info" data-toggle="modal"><i class="icon-envelope-o"></i></a>
					</div>';
				}
				else {
					echo anchor($this->_clspath.$this->_class.'/previewAll/0/0', '<i class="icon-file-o"></i>', 'class="btn btn-default Popup"') . 
						anchor($this->_clspath.$this->_class.'/previewAll/1/0', '<i class="icon-file-pdf"></i>', 'class="btn btn-default Popup"') .
						anchor($this->_clspath.$this->_class.'/excel', '<i class="icon-file-excel"></i>', 'class="btn btn-warning Popup"');
				}
				?>
				</div>
			</div>
			</form>
		</td>

		<td class="nowrap">
			<button type="button" class="btn btn-group btn-primary" onclick="javascript: clearSearch()"><i class="icon-align-justify"></i></button>
			
			<div class="btn-group">
				<button data-toggle="dropdown" class="btn btn-info dropdown-toggle"><?php echo (isset($parsed_search['bl_no']) ? '<i class="icon-filter4"></i>' : '') ?> BL No <span class="caret"></span></button>
				<ul class="dropdown-menu nav-menu-scroll pull-right" id="FilterBLNo">
					<li><a class="red" href="javascript: filter('bl_no:')">Clear Filter</a></li>
				</ul>
			</div>

			<div class="btn-group">
				<button data-toggle="dropdown" class="btn btn-warning dropdown-toggle"><?php echo (isset($parsed_search['vehicle_no']) ? '<i class="icon-filter4"></i>' : '') ?> Vehicle No <span class="caret"></span></button>
				<ul class="dropdown-menu nav-menu-scroll pull-right" id="FilterVehicleNo">
					<li><a class="red" href="javascript: filter('vehicle_no:')">Clear Filter</a></li>
				</ul>
			</div>

			<div class="btn-group">
				<button data-toggle="dropdown" class="btn btn-success dropdown-toggle"><?php echo (isset($parsed_search['gatepass_date']) ? '<i class="icon-filter4"></i>' : '') ?> Gatepass Date <span class="caret"></span></button>
				<ul class="dropdown-menu nav-menu-scroll pull-right" id="FilterGatepass">
					<li><a class="red" href="javascript: filter('gatepass_date:')">Clear Filter</a></li>
				</ul>
			</div>
			
			<div class="btn-group">
				<button data-toggle="dropdown" class="btn btn-danger dropdown-toggle"><?php echo (isset($parsed_search['cfs_date']) ? '<i class="icon-filter4"></i>' : '') ?> CFS Date <span class="caret"></span></button>
				<ul class="dropdown-menu nav-menu-scroll pull-right" id="FilterCFS">
					<li><a class="red" href="javascript: filter('cfs_date:')">Clear Filter</a></li>
				</ul>
			</div>

			<div class="btn-group">
				<button data-toggle="dropdown" class="btn btn-success dropdown-toggle"><?php echo (isset($parsed_search['party']) ? '<i class="icon-filter4"></i>' : '') ?> Party <span class="caret"></span></button>
				<ul class="dropdown-menu pull-right nav-menu-scroll" id="FilterParty">
					<li><a href="javascript: filter('party:')" class="red">Clear Filter</a></li>
				</ul>
			</div>

			<div class="btn-group">
				<button data-toggle="dropdown" class="btn btn-info dropdown-toggle"><?php echo (isset($parsed_search['return_date']) ? '<i class="icon-filter4"></i>' : '') ?> Return Date <span class="caret"></span></button>
				<ul class="dropdown-menu nav-menu-scroll pull-right" id="FilterReturn">
					<li><a class="red" href="javascript: filter('return_date:')">Clear Filter</a></li>
				</ul>
			</div>

			<div class="btn-group">
				<button data-toggle="dropdown" class="btn btn-warning dropdown-toggle"><?php echo (isset($parsed_search['icegate']) ? '<i class="icon-filter4"></i>' : '') ?> Icegate <span class="caret"></span></button>
				<ul class="dropdown-menu nav-menu-scroll pull-right" id="FilterIcegate">
					<li><a class="red" href="javascript: filter('icegate:')">Clear Filter</a></li>
					<li><a href="javascript: filter('icegate:N.A.')">N.A.</a></li>
					<li><a href="javascript: filter('icegate:OOC')">OOC</a></li>
					<li><a href="javascript: filter('icegate:EXAM')">EXAM</a></li>
					<li><a href="javascript: filter('icegate:PAYMENT')">PAYMENT</a></li>
					<li><a href="javascript: filter('icegate:ASSESS')">ASSESS</a></li>
					<li><a href="javascript: filter('icegate:APPRA')">APPRA</a></li>
				</ul>
			</div>
		</td>
	</tr>
	</table>

	<table class="table table-condensed table-striped table-bordered hide" id="FixedHeader">
		<thead>
			<tr>
				<th>No</th>
				<th>Party</th>
				<th>Container No</th>
				<th>Size</th>
				<th>BE No</th>
				<th>BL No</th>
				<th>Vehicle No</th>
				<th>Disp. Wt.</th>
				<th>Unloading Location</th>
				<th>Unloading Date</th>
				<th>Fetched</th>
				<th>Location</th>
				<th>CFS In Date</th>
				<th>GatePass No</th>
				<th>GatePass Date</th>
				<th>LR No</th>
				<th>Return Date</th>
				<th>Icegate</th>
			</tr>
		</thead>
	</table>
</div>

<table class="table table-condensed table-striped table-bordered tiny" id="Result">
	<thead>
		<tr>
			<th>No</th>
			<th>Party</th>
			<th>Container No</th>
			<th>Size</th>
			<th>BE No</th>
			<th>BL No</th>
			<th>Vehicle No</th>
			<th>Disp. Wt.</th>
			<th>Unloading Location</th>
			<th>Unloading Date</th>
			<th>Fetched</th>
			<th>Location</th>
			<th>CFS In Date</th>
			<th>GatePass No</th>
			<th>GatePass Date</th>
			<th>LR No</th>
			<th>Return Date</th>
			<th>Icegate</th>
		</tr>
	</thead>

	<tbody>
	<?php 
	$i = 1;
	$filter = array(
		'bl_no'         => array(),
		'vehicle_no'    => array(),
		'party'         => array(),
		'cfs_date'      => array(),
		'gatepass_date' => array(),
		'return_date'   => array(),
	);
	$party_id = 0;
	foreach ($rows as $r) {
		$filter['party'][$r['party_name']]            = 1;
		$filter['bl_no'][$r['bl_no']]                 = 1;
		$filter['vehicle_no'][$r['vehicle_no']]       = 1;
		$filter['cfs_date'][$r['cfs_in_date_only']]   = 1;
		$filter['gatepass_date'][$r['gatepass_date']] = 1;
		$filter['return_date'][$r['return_date']]     = 1;

		$party_id = $r['party_id'];

		echo '<tr id="' . $r['id'] . '">
		<td class="aligncenter">' . $i++ . '</td>
		<td>' . $r['party_name'] . '</td>
		<td>' . $r['number'] . '</td>
		<td class="aligncenter">' . $r['size'] . '</td>
		<td>' . $r['be_no'] . '</td>
		<td>' . $r['bl_no'] . '</td>
		<td>' . $r['vehicle_no'] . '</td>
		<td>' . $r['dispatch_weight'] . '</td>
		<td>' . $r['unloading_location'] . '</td>
		<td class="aligncenter nowrap">' . $r['unloading_date'] . '</td>
		<td>' . $r['fetched_from'] . '</td>
		<td>' . $r['location'] . '</td>
		<td class="aligncenter nowrap">' . $r['cfs_in_date'] . '</td>
		<td>' . $r['gatepass_no'] . '</td>
		<td class="aligncenter nowrap">' . $r['gatepass_date'] . '</td>
		<td>' . $r['lr_no'] . '</td>
		<td>' . $r['return_date'] . '</td>
		<td>' . $r['icegate_status'] . '</td>
	</tr>';
	$party_name = $r['party_name'];
	} ?>
	</tbody>
</table>

<script>
$(document).ready(function() {
	$('#unloading_location').autocomplete({
		source: "<?php echo base_url($this->_clspath.$this->_class.'/ajaxJson/deliveries_stuffings/unloading_location') ?>",
		minLength: 0,
		appendTo: '#modal-delivery'
	});
});

function resizeFixedHeader() {
	$("#Result").find('thead tr').children().each(function(i, e) {
	    $($("#FixedHeader").find('thead tr').children()[i]).width($(e).width());
	});
	$("#FixedHeader").width($("#Result").width());
}

$(document).ready(function() {
	resizeFixedHeader();

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
					url: '<?php echo site_url('/master/party/ajaxEmail/0/'.htmlentities($party_name)) ?>',
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
				.append('<a>' + item.name +  ' <span class="orange">&lt;' + item.email +  '&gt;</span></a>')
				.appendTo(ul);
		}
	});

	$("#AddLoose").on("click", function() {
		delivery.clear();
		delivery.reset();
		deliveryView.render();
		$("#modal-delivery").modal();
	});

	$('#modal-delivery').on('change', '#dispatch_type', function(){
		if ($(this).val() == 'Loose') {
			$('#dispatch_weight').val('0');
			delivery.set('dispatch_weight', 0);
		}
	});

	$("#Result td").on("click", function() {
		var isLink = $(this).hasClass('VoucherLink');
		if (isLink) return true;

		var id = $(this).parents('tr').attr("id");
		delivery.clear();
		delivery.set({id: id});
		delivery.fetch({success: fetchSuccess});
		$("#modal-delivery").modal();
	});


	// Backbone Code
	"use strict";
	var Delivery = Backbone.Model.extend({
		urlRoot: '/api/import_deliveries',
		reset: function() {
			this.set({
				'container_id': 0,
				'container_type_id': 0,
				'job_id': <?php echo $job_id ?>,
				'container_no': 'LOOSE',
				'bl_no': '',
				'vehicle_no': '',
				'dispatch_weight': '',
				'unloading_location': '',
				'dispatch_type': '',
				'unloading_date': '',
				'gatepass_no': '',
				'gatepass_date': '',
				'lr_no': '',
				'return_date': ''
			})
		}
	});
	var delivery = new Delivery();

	var DeliveryView = Backbone.View.extend({
		el: "div#modal-delivery",
		initialize: function () {
			_.bindAll(this, "changed");
			this.save = $("#Save");
		},
		events: {
			"dp.change .DatePicker": "changed",
			"change input": "changed",
			"change select": "changed",
			"click #Save": "saveModel",
			"click #Delete": "deleteModel"
		},
		changed: function(e) {
			var target = e.currentTarget,
			data = {};
			if ($(target).hasClass('DatePicker'))
				this.model.set($(target).find('input').attr('id'), $(target).find('input').val());
			else
				this.model.set(target.name, target.value);
		},
		render: function() {
			if(this.model.get('container_id')!=0){
				$('#Delete').addClass('hide');
			}
			_.each(this.model.toJSON(), function(value, key) {
				$("#"+key).val(value);
			});
		},
		saveModel: function() {
			this.model.save(null, {
				success:function(model) {
					$("tr#"+model.get('id')+" td:eq(6)").text(model.get('vehicle_no'));
					$("tr#"+model.get('id')+" td:eq(7)").text(model.get('dispatch_weight'));
					$("tr#"+model.get('id')+" td:eq(8)").text(model.get('unloading_location'));
					$("tr#"+model.get('id')+" td:eq(9)").text(model.get('unloading_date'));
					$("tr#"+model.get('id')+" td:eq(13)").text(model.get('gatepass_no'));
					$("tr#"+model.get('id')+" td:eq(14)").text(model.get('gatepass_date'));
					$("tr#"+model.get('id')+" td:eq(15)").text(model.get('lr_no'));
					$("tr#"+model.get('id')+" td:eq(16)").text(model.get('return_date'));
					resizeFixedHeader();
				}
			});
		},
		deleteModel: function(){
			this.model.destroy({
				success: function(model, response) {
					$('tr#'+response).remove();
				}
			});
		}
	});
	var deliveryView = new DeliveryView({model: delivery});

	var fetchSuccess = function() {
		deliveryView.render();
	}

<?php 
if (count($filter['party']) > 0) {
	ksort($filter['party']);
	foreach ($filter['party'] as $k => $v) {
		echo '$("ul#FilterParty").append("<li><a href=\"javascript: filter(\'party:' . $k . '\')\">' . $k . '</a></li>");';
	}
}
if (count($filter['bl_no']) > 0) {
	ksort($filter['bl_no']);
	foreach ($filter['bl_no'] as $k => $v) {
		echo '$("ul#FilterBLNo").append("<li><a href=\"javascript: filter(\'bl_no:' . $k . '\')\">' . $k . '</a></li>");';
	}
}
if (count($filter['vehicle_no']) > 0) {
	ksort($filter['vehicle_no']);
	foreach ($filter['vehicle_no'] as $k => $v) {
		echo '$("ul#FilterVehicleNo").append("<li><a href=\"javascript: filter(\'vehicle_no:' . $k . '\')\">' . $k . '</a></li>");';
	}
}
if (count($filter['cfs_date']) > 0) {
	ksort($filter['cfs_date']);
	foreach ($filter['cfs_date'] as $k => $v) {
		echo '$("ul#FilterCFS").append("<li><a href=\"javascript: filter(\'cfs_date:' . $k . '\')\">' . $k . '</a></li>");';
	}
}
if (count($filter['gatepass_date']) > 0) {
	ksort($filter['gatepass_date']);
	foreach ($filter['gatepass_date'] as $k => $v) {
		echo '$("ul#FilterGatepass").append("<li><a href=\"javascript: filter(\'gatepass_date:' . $k . '\')\">' . $k . '</a></li>");';
	}
}
if (count($filter['return_date']) > 0) {
	ksort($filter['return_date']);
	foreach ($filter['return_date'] as $k => $v) {
		echo '$("ul#FilterReturn").append("<li><a href=\"javascript: filter(\'return_date:' . $k . '\')\">' . $k . '</a></li>");';
	}
}
?>
});
</script>