<style>
.markChanged { background-color: #1BDB1A !important; }
</style>

<div id="modal-party" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><i class="icon-times"></i></button>
				<h3>Edit Vessel Details</h3>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-9">
						<div class="form-group">
							<label class="control-label">Vessel Name</label>
							<input type="text" class="form-control form-control-sm" name="name" value="" id="name" />
						</div>
					</div>

					<div class="col-md-3">
						<div class="form-group">
							<label class="control-label">Voyage No</label>
							<input type="text" class="form-control form-control-sm" name="voyage_no" value="" id="voyage_no" />
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-3">
						<div class="form-group">
							<label class="control-label">Type</label>
							<?php echo form_dropdown('type', $this->import->getCargoTypes(), 'Bulk', 'class="form-control form-control-sm" id="type"'); ?>
						</div>
					</div>

					<div class="col-md-6">
						<div class="form-group">
							<label class="control-label">Agent</label>
							<?php echo form_dropdown('agent_id', array(0=>'')+getSelectOptions('agents', 'id', 'name'), 0, 'class="form-control form-control-sm" id="agent_id"') ?>
						</div>
					</div>

					<div class="col-md-3">
						<div class="form-group">
							<label class="control-label">Port</label>
							<?php echo form_dropdown('indian_port_id', getSelectOptions('indian_ports', 'id', 'name'), 53, 'class="form-control form-control-sm" id="indian_port_id"'); ?>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label class="control-label">IGM No</label>
							<input type="text" class="form-control form-control-sm" name="igm_no" value="" id="igm_no" />
						</div>
					</div>

					<div class="col-md-3">
						<div class="form-group">
							<label class="control-label">IGM Date</label>
							<div class="input-group date DatePicker">
							<span class="input-group-addon"><i class="icon-calendar"></i></span>
							<input type="text" class="form-control form-control-sm AutoDate" name="igm_date" value="" id="igm_date" />
						</div>
					</div>
					</div>

					<div class="col-md-3">
						<div class="form-group">
							<label class="control-label">Berth No</label>
							<?php echo form_dropdown('berth_no', $this->office->getBerthNo(), $row['berth_no'], 'class="form-control form-control-sm" id="berth_no"') ?>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-3">
						<div class="form-group">
							<label class="control-label">GLD Date</label>
							<div class="input-group date DatePicker">
							<span class="input-group-addon"><i class="icon-calendar"></i></span>
							<input type="text" class="form-control form-control-sm AutoDate" name="gld_date" value="" id="gld_date" />
						</div>
					</div>
					</div>

					<div class="col-md-3">
						<div class="form-group">
							<label class="control-label">ETA Date</label>
							<div class="input-group date DatePicker">
							<span class="input-group-addon"><i class="icon-calendar"></i></span>
							<input type="text" class="form-control form-control-sm AutoDate" name="eta_date" value="" id="eta_date" />
						</div>
					</div>
					</div>

					<div class="col-md-3">
						<div class="form-group">
							<label class="control-label">ETD Date</label>
							<div class="input-group date DatePicker">
							<span class="input-group-addon"><i class="icon-calendar"></i></span>
							<input type="text" class="form-control form-control-sm AutoDate" name="etd_date" value="" id="etd_date" />
						</div>
					</div>
					</div>

					<div class="col-md-3">
						<div class="form-group">
							<label class="control-label">Sailing Date</label>
							<div class="input-group date DatePicker">
							<span class="input-group-addon"><i class="icon-calendar"></i></span>
							<input type="text" class="form-control form-control-sm AutoDate" name="sailing_date" value="" id="sailing_date" />
						</div>
					</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-3">
						<div class="form-group">
							<label class="control-label">Berthing Date</label>
							<div class="input-group date DatePicker">
							<span class="input-group-addon"><i class="icon-calendar"></i></span>
							<input type="text" class="form-control form-control-sm AutoDate" name="berthing_date" value="" id="berthing_date" />
						</div>
					</div>
					</div>

					<div class="col-md-3">
						<div class="form-group">
							<label class="control-label">Barging Date</label>
							<div class="input-group date DatePicker">
							<span class="input-group-addon"><i class="icon-calendar"></i></span>
							<input type="text" class="form-control form-control-sm AutoDate" name="barging_date" value="" id="barging_date" />
						</div>
					</div>
					</div>

					<div class="col-md-3">
						<div class="form-group">
							<label class="control-label">PGR Begin Date</label>
							<div class="input-group date DatePicker">
							<span class="input-group-addon"><i class="icon-calendar"></i></span>
							<input type="text" class="form-control form-control-sm AutoDate" name="pgr_begin_date" value="" id="pgr_begin_date" />
						</div>
					</div>
					</div>

					<div class="col-md-3">
						<div class="form-group">
							<label class="control-label">Exchange Rate</label>
							<input type="text" class="form-control form-control-sm Numeric" name="exchange_rate" value="" id="exchange_rate" />
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-success" data-dismiss="modal" id="Save">Save</button>
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
	<table class="table toolbar">
	<tr>
		<td class="nowrap">
			<div class="row">
				<?php echo form_open($this->uri->uri_string(), 'class="form-search" id="SearchForm"'); ?>
				<input type="hidden" name="search_form" value="1" />
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
					echo //anchor($this->_clspath.$this->_class."/preview", '<i class="icon-file-o"></i>', 'class="btn btn-default Popup"') .
					//anchor($this->_clspath.$this->_class."/preview/1", '<i class="icon-file-pdf"></i>', 'class="btn btn-default Popup"') .
					anchor($this->_clspath.$this->_class."/excel", '<i class="icon-file-excel"></i>', 'class="btn btn-warning"') 
					?>
					</div>
				</div>
			</div>
			</form>
		</td>

		<td>
			<div class="nowrap">
				<button type="button" class="btn btn-group btn-primary" onclick="javascript: clearSearch()"><i class="icon-align-justify"></i></button>

				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-success dropdown-toggle"><?php echo (isset($parsed_search['vessel']) ? '<i class="icon-filter4"></i>' : '') ?> Vessel <span class="caret"></span></button>
					<ul class="dropdown-menu nav-menu-scroll pull-right" id="FilterVessel"></ul>
				</div>
				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-info dropdown-toggle"><?php echo (isset($parsed_search['agent']) ? '<i class="icon-filter4"></i>' : '') ?> Agent <span class="caret"></span></button>
					<ul class="dropdown-menu nav-menu-scroll pull-right" id="FilterAgent"></ul>
				</div>
				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-warning dropdown-toggle"><?php echo (isset($parsed_search['port']) ? '<i class="icon-filter4"></i>' : '') ?> Port <span class="caret"></span></button>
					<ul class="dropdown-menu nav-menu-scroll pull-right" id="FilterPort"></ul>
				</div>
				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-danger dropdown-toggle"><?php echo (isset($parsed_search['type']) ? '<i class="icon-filter4"></i>' : '') ?> Type <span class="caret"></span></button>
					<ul class="dropdown-menu pull-right" id="FilterType">
						<li><a href="javascript: filter('type: Bulk')">Bulk</a></li>
						<li><a href="javascript: filter('type: Container')">Container</a></li>
						<li><a class="red" href="javascript: filter('type:')">Clear Filter</a></li>
					</ul>
				</div>
			</div>
		</td>
	</tr>
	</table>

	<table class="table table-condensed table-striped table-bordered hide" id="FixedHeader">
	<thead>
	<tr>
		<th>No</th>
		<th>Agent</th>
		<th>Type</th>
		<th>Vessel Name</th>
		<th>Voyage No</th>
		<th>Berth No</th>
		<th>Port</th>
		<th>IGM No</th>
		<th>IGM Date</th>
		<th>GLD Date</th>
		<th>ETA</th>
		<th>ETD</th>
		<th>Berthing</th>
		<th>Barging</th>
		<th>Sailing</th>
		<th>PGR Begin</th>
		<th>Ex. Rate</th>
	</tr>
	</thead>
	</table>
</div>

<table class="table table-condensed table-striped table-bordered" id="Jobs">
<thead>
<tr>
	<th>No</th>
	<th>Agent</th>
	<th>Type</th>
	<th>Vessel Name</th>
	<th>Voyage No</th>
	<th>Berth No</th>
	<th>Port</th>
	<th>IGM No</th>
	<th>IGM Date</th>
	<th>GLD Date</th>
	<th>ETA</th>
	<th>ETD</th>
	<th>Berthing</th>
	<th>Barging</th>
	<th>Sailing</th>
	<th>PGR Begin</th>
	<th>Ex. Rate</th>
</tr>
</thead>

<tbody>
<?php 
$i = 1;
$filter = array(
	'agent'  => array(),
	'vessel' => array(),
	'port'   => array(),
);
foreach ($rows as $r) {
	$filter['agent'][$r['agent_name']] = 1;
	$filter['vessel'][$r['name']]      = 1;
	$filter['port'][$r['port_name']]   = 1;

	echo '<tr id="' . $r['id'] . '">
	<td class="aligncenter">' . $i++ . '</td>
	<td>' . $r['agent_name'] . '</td>
	<td>' . $r['type'] . '</td>
	<td>' . $r['name'] . '</td>
	<td>' . $r['voyage_no'] . '</td>
	<td class="aligncenter">' . $r['berth_no'] . '</td>
	<td>' . $r['port_name'] . '</td>
	<td class="aligncenter">' . $r['igm_no'] . '</td>
	<td class="aligncenter">' . ($r['igm_date'] == '00-00-0000' ? '' : $r['igm_date']) . '</td>
	<td class="aligncenter">' . ($r['gld_date'] == '00-00-0000' ? '' : $r['gld_date']) . '</td>
	<td class="aligncenter">' . ($r['eta_date'] == '00-00-0000' ? '' : $r['eta_date']) . '</td>
	<td class="aligncenter">' . ($r['etd_date'] == '00-00-0000' ? '' : $r['etd_date']) . '</td>
	<td class="aligncenter">' . ($r['berthing_date'] == '00-00-0000' ? '' : $r['berthing_date']) . '</td>
	<td class="aligncenter">' . ($r['barging_date'] == '00-00-0000' ? '' : $r['barging_date']) . '</td>
	<td class="aligncenter">' . ($r['sailing_date'] == '00-00-0000' ? '' : $r['sailing_date']) . '</td>
	<td class="aligncenter">' . ($r['pgr_begin_date'] == '00-00-0000' ? '' : $r['pgr_begin_date']) . '</td>
	<td class="alignright">' . $r['exchange_rate'] . '</td>
</tr>';
} ?>
</tbody>
</table>

<script>

function resizeHeader() {
	$("#Jobs").find('thead tr').children().each(function(i, e) {
	    $($("#FixedHeader").find('thead tr').children()[i]).width($(e).width());
	});
	$("#FixedHeader").width($("#Jobs").width());
}

$(document).ready(function() {
	resizeHeader();

	if (!($.browser == "msie" && $.browser.version < 7)) {
        var target = "div#FixedToolbar";
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

	$("#Jobs td").on("click", function() {
		var id = $(this).parents('tr').attr("id");
		party.clear();
		party.set({id: id});
		party.fetch({success: fetchSuccess});
		$("#modal-party").modal();
	});


	// Backbone Code
	"use strict";
	var Party = Backbone.Model.extend({
		urlRoot: '/api/vessels',
	});
	var party = new Party();

	var PartyView = Backbone.View.extend({
		el: "div#modal-party",
		initialize: function () {
			_.bindAll(this, "changed");
			this.save = $("#Save");
		},
		events: {
			"dp.change .DatePicker": "changed",
			"change input": "changed",
        	"change select": "changed",
        	"click #Save": "saveModel"
		},
		changed:function(e) {
			var target = e.currentTarget,
      		data = {};
			if (target.name != 'agent_name' || target.name != 'port_name') {
				if ($(target).hasClass('DatePicker'))
					this.model.set($(target).find('input').attr('id'), $(target).find('input').val());
				else
					this.model.set(target.name, target.value);
			}
		},
		render: function() {
			_.each(this.model.toJSON(), function(value, key) {
				$("#"+key).val(value);
			});
		},
		saveModel: function() {
			this.model.save(null, {
				success:function(model) {
					$("tr#"+model.get('id')+" td:eq(1)").text($("#agent_id option:selected").text());
					$("tr#"+model.get('id')+" td:eq(2)").text(model.get('type'));
					$("tr#"+model.get('id')+" td:eq(3)").text(model.get('name'));
					$("tr#"+model.get('id')+" td:eq(4)").text(model.get('voyage_no'));
					$("tr#"+model.get('id')+" td:eq(5)").text(model.get('berth_no'));
					$("tr#"+model.get('id')+" td:eq(6)").text($("#indian_port_id option:selected").text());
					$("tr#"+model.get('id')+" td:eq(7)").text(model.get('igm_no'));
					$("tr#"+model.get('id')+" td:eq(8)").text(model.get('igm_date'));
					$("tr#"+model.get('id')+" td:eq(9)").text(model.get('gld_date'));
					$("tr#"+model.get('id')+" td:eq(10)").text(model.get('eta_date'));
					$("tr#"+model.get('id')+" td:eq(11)").text(model.get('etd_date'));
					$("tr#"+model.get('id')+" td:eq(12)").text(model.get('berthing_date'));
					$("tr#"+model.get('id')+" td:eq(13)").text(model.get('barging_date'));
					$("tr#"+model.get('id')+" td:eq(14)").text(model.get('sailing_date'));
					$("tr#"+model.get('id')+" td:eq(15)").text(model.get('pgr_begin_date'));
					$("tr#"+model.get('id')+" td:eq(16)").text(model.get('exchange_rate'));
					resizeHeader();
				}
			});
		}
	});
	var partyView = new PartyView({model: party});
	
	var fetchSuccess = function() {
		partyView.render();
	}

<?php 
if (count($filter['agent']) > 0) {
	ksort($filter['agent']);
	foreach ($filter['agent'] as $k => $v) {
		echo '$("ul#FilterAgent").append("<li><a href=\"javascript: filter(\'agent:' . $k . '\')\">' . $k . '</a></li>");';
	}
	echo '$("ul#FilterAgent").append("<li><a class=\"red\" href=\"javascript: filter(\'agent:\')\">Clear Filter</a></li>");';
}
if (count($filter['vessel']) > 0) {
	ksort($filter['vessel']);
	foreach ($filter['vessel'] as $k => $v) {
		echo '$("ul#FilterVessel").append("<li><a href=\"javascript: filter(\'vessel:' . $k . '\')\">' . $k . '</a></li>");';
	}
	echo '$("ul#FilterVessel").append("<li><a class=\"red\" href=\"javascript: filter(\'vessel:\')\">Clear Filter</a></li>");';
}
if (count($filter['port']) > 0) {
	ksort($filter['port']);
	foreach ($filter['port'] as $k => $v) {
		echo '$("ul#FilterPort").append("<li><a href=\"javascript: filter(\'port:' . $k . '\')\">' . $k . '</a></li>");';
	}
	echo '$("ul#FilterPort").append("<li><a class=\"red\" href=\"javascript: filter(\'port:\')\">Clear Filter</a></li>");';
}
?>
});
</script>