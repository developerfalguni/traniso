<style>
.markChanged { background-color: #1BDB1A !important; }
</style>

<div id="modal-party" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><i class="icon-times"></i></button>
				<h3>Edit Party Details</h3>
			</div>
			<div class="modal-body">
				<div class="form-group">
					<label class="control-label">Party Name</label>
					<input type="text" class="form-control form-control-sm" name="name" value="" id="name" />
				</div>

				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label class="control-label">Address</label>
							<input type="text" class="form-control form-control-sm" name="address" value="" id="address" />
						</div>
					</div>

					<div class="col-md-6">
						<div class="form-group">
							<label class="control-label">City</label>
							<?php echo form_dropdown('city_id', $this->kaabar->getCities(), '1', 'class="form-control form-control-sm" id="city_id"'); ?>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label class="control-label">Contact</label>
							<input type="text" class="form-control form-control-sm" name="contact" value="" id="contact" />
						</div>
					</div>

					<div class="col-md-6">
						<div class="form-group">
							<label class="control-label">Email</label>
							<input type="text" class="form-control form-control-sm" name="email" value="" id="email" />
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-4">
						<div class="form-group">
							<label class="control-label">Pan No</label>
							<input type="text" class="form-control form-control-sm" name="pan_no" value="" id="pan_no" />
						</div>
					</div>

					<div class="col-md-4">
						<div class="form-group">
							<label class="control-label">Tan No</label>
							<input type="text" class="form-control form-control-sm" name="tan_no" value="" id="tan_no" />
						</div>
					</div>

					<div class="col-md-4">
						<div class="form-group">
							<label class="control-label">S.Tax No</label>
							<input type="text" class="form-control form-control-sm" name="service_tax_no" value="" id="service_tax_no" />
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label class="control-label">TIN No</label>
							<input type="text" class="form-control form-control-sm" name="tin_no" value="" id="tin_no" />
						</div>
					</div>

					<div class="col-md-6">
						<div class="form-group">
							<label class="control-label">CST No</label>
							<input type="text" class="form-control form-control-sm" name="cst_no" value="" id="cst_no" />
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label class="control-label">IEC No</label>
							<input type="text" class="form-control form-control-sm" name="iec_no" value="" id="iec_no" />
						</div>
					</div>

					<div class="col-md-6">
						<div class="form-group">
							<label class="control-label">Excise No</label>
							<input type="text" class="form-control form-control-sm" name="excise_no" value="" id="excise_no" />
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
			<div class="modal-body form-horizontal">
				<fieldset>
					<?php foreach($search_fields as $f => $n) : ?>
					<div class="form-group">
						<label class="control-label col-md-4"><?php echo humanize($f) ?></label>
						<div class="col-md-8">
							<input type="text" class="form-control form-control-sm AdvancedSearch" name="<?php echo $f ?>" />
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
					<button data-toggle="dropdown" class="btn btn-success dropdown-toggle"><?php echo (isset($parsed_search['account_group']) ? '<i class="icon-filter4"></i>' : '') ?> Account Group <span class="caret"></span></button>
						<ul class="dropdown-menu nav-menu-scroll pull-right" id="FilterAccountGroup"></ul>
				</div>
				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-info dropdown-toggle"><?php echo (isset($parsed_search['group']) ? '<i class="icon-filter4"></i>' : '') ?> Group <span class="caret"></span></button>
						<ul class="dropdown-menu nav-menu-scroll pull-right" id="FilterGroup"></ul>
				</div>
			</div>
		</td>
	</tr>
	</table>

	<table class="table table-condensed table-striped table-bordered hide" id="FixedHeader">
	<thead>
	<tr>
		<th>No</th>
		<th>A/c Group</th>
		<th>Party Group</th>
		<th>Party Name</th>
		<th>Address</th>
		<th>Contact</th>
		<th>Email</th>
		<th>PAN No</th>
		<th>TAN No</th>
		<th>S.Tax No</th>
		<th>IEC No</th>
		<th>TIN No</th>
		<th>CST No</th>
		<th>Excise No</th>
	</tr>
	</thead>
	</table>
</div>

<table class="table table-condensed table-striped table-bordered" id="Jobs">
<thead>
<tr>
	<th>No</th>
	<th>A/c Group</th>
	<th>Party Group</th>
	<th>Party Name</th>
	<th>Address</th>
	<th>Contact</th>
	<th>Email</th>
	<th>PAN No</th>
	<th>TAN No</th>
	<th>S.Tax No</th>
	<th>IEC No</th>
	<th>TIN No</th>
	<th>CST No</th>
	<th>Excise No</th>
</tr>
</thead>

<tbody>
<?php 
$i = 1;
$filter = array(
	'account_group'  => array(),
	'group'  => array(),
);
foreach ($rows as $r) {
	$filter['account_group'][$r['account_group']] = 1;
	$filter['group'][$r['group_name']]            = 1;

	echo '<tr id="' . $r['id'] . '">
	<td class="aligncenter">' . $i++ . '</td>
	<td>' . $r['account_group'] . '</td>
	<td>' . $r['group_name'] . '</td>
	<td>' . $r['name'] . '</td>
	<td class="tiny">' . $r['address'] . '<br />' . $r['city'] . '</td>
	<td>' . $r['contact'] . '</td>
	<td>' . $r['email'] . '</td>
	<td>' . $r['pan_no'] . '</td>
	<td>' . $r['tan_no'] . '</td>
	<td>' . $r['service_tax_no'] . '</td>
	<td class="aligncenter ' . (is_null($r['dgft_id']) ? 'red' : 'green') . '">' . $r['iec_no'] . '</td>
	<td>' . $r['tin_no'] . '</td>
	<td>' . $r['cst_no'] . '</td>
	<td>' . $r['excise_no'] . '</td>
</tr>';
} ?>
</tbody>
</table>

<script>
function combineSearch() {
	var txtData = [];
	$('input.AdvancedSearch').each(function() {
		if ($(this).val())
			txtData.push($(this).attr('name')+': '+$(this).val());
	});
	$('input#Search').val(txtData.join(" "));
	$("#SearchButton").click();
}

function clearSearch() {
	$('input#Search').val('');
	$("#SearchButton").click();
}

function filter(search) {
	var v = $('input#Search').val();
	$('input#Search').val(v+' '+search);
	$("#SearchButton").click();
}

function resizeFixedHeader() {
	$("#Jobs").find('thead tr').children().each(function(i, e) {
	    $($("#FixedHeader").find('thead tr').children()[i]).width($(e).width());
	});
	$("#FixedHeader").width($("#Jobs").width());
}

$(document).ready(function() {
	resizeFixedHeader();

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

	var availableTags = [
		<?php foreach($search_fields as $f => $n)
			echo "\"$f:\",";
		?>
	];
	function split( val ) {
		return val.split( / \s*/ );
	}
	function extractLast( term ) {
		return split( term ).pop();
	}

	$("#Search").on( "keydown", function( event ) {
		if ( event.keyCode === $.ui.keyCode.TAB &&
				$(this).data("ui-autocomplete").menu.active) {
			event.preventDefault();
		}
	})
	.autocomplete({
		minLength: 0,
		source: function( request, response ) {
			response( $.ui.autocomplete.filter(
				availableTags, extractLast(request.term)));
		},
		focus: function() {
			return false;
		},
		select: function( event, ui ) {
			var terms = split( this.value );
			terms.pop();
			terms.push( ui.item.value );
			terms.push("");
			this.value = terms.join(" ");
			return false;
		}
	});


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
		urlRoot: '/api/parties',
	});
	var party = new Party();

	var PartyView = Backbone.View.extend({
		el: "div#modal-party",
		initialize: function () {
			_.bindAll(this, "changed");
			this.save = $("#Save");
		},
		events : {
        	"change input": "changed",
        	"change select": "changed",
        	"click #Save": "saveModel"
		},
		changed:function(e) {
			var target = e.currentTarget,
      			data = {};
      		if (target.name != 'city_name')
				this.model.set(target.name, target.value);
		},
		render: function() {
			_.each(this.model.toJSON(), function(value, key) {
				$("#"+key).val(value);
			});
		},
		saveModel: function() {
			this.model.save(null, {
				success:function(model) {
					$("tr#"+model.get('id')+" td:eq(3)").text(model.get('name'));
					$("tr#"+model.get('id')+" td:eq(4)").html(model.get('address') + '<br />' + $("#city_id option:selected").text());
					$("tr#"+model.get('id')+" td:eq(5)").text(model.get('contact'));
					$("tr#"+model.get('id')+" td:eq(6)").text(model.get('email'));
					$("tr#"+model.get('id')+" td:eq(7)").text(model.get('pan_no'));
					$("tr#"+model.get('id')+" td:eq(8)").text(model.get('tan_no'));
					$("tr#"+model.get('id')+" td:eq(9)").text(model.get('service_tax_no'));
					$("tr#"+model.get('id')+" td:eq(10)").text(model.get('iec_no'));
					$("tr#"+model.get('id')+" td:eq(11)").text(model.get('tin_no'));
					$("tr#"+model.get('id')+" td:eq(12)").text(model.get('cst_no'));
					$("tr#"+model.get('id')+" td:eq(13)").text(model.get('excise_no'));
					resizeFixedHeader();
				}
			});
		}
	});
	var partyView = new PartyView({model: party});
	
	var fetchSuccess = function() {
		partyView.render();
	}

<?php 
if (count($filter['account_group']) > 0) {
	ksort($filter['account_group']);
	foreach ($filter['account_group'] as $k => $v) {
		echo '$("ul#FilterAccountGroup").append("<li><a href=\"javascript: filter(\'account_group: ' . $k . '\')\">' . $k . '</a></li>");';
	}
	echo '$("ul#FilterAccountGroup").append("<li><a class=\"red\" href=\"javascript: filter(\'account_group:\')\">Clear Filter</a></li>");';
}
if (count($filter['group']) > 0) {
	ksort($filter['group']);
	foreach ($filter['group'] as $k => $v) {
		echo '$("ul#FilterGroup").append("<li><a href=\"javascript: filter(\'group: ' . $k . '\')\">' . $k . '</a></li>");';
	}
	echo '$("ul#FilterGroup").append("<li><a class=\"red\" href=\"javascript: filter(\'group:\')\">Clear Filter</a></li>");';
}
?>
});
</script>