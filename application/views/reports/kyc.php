<style>
.markChanged { background-color: #1BDB1A !important; }
</style>

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
			<div class="row">
				<?php echo form_open($this->uri->uri_string(), 'class="form-search" id="SearchForm"'); ?>
				<input type="hidden" name="search_form" value="1" />
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
		<th>Type</th>
		<th>Party Group</th>
		<th>Party Name</th>
		<th>Ledger A/c</th>
		<th>Attached KYC</th>
		<th>Pending KYC</th>
	</tr>
	</thead>
	</table>
</div>

<table class="table table-condensed table-striped table-bordered" id="Jobs">
<thead>
<tr>
	<th>No</th>
	<th>Type</th>
	<th>Party Group</th>
	<th>Party Name</th>
	<th>Ledger A/c</th>
	<th>Attached KYC</th>
	<th>Pending KYC</th>
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

	echo '<tr>
	<td class="aligncenter">' . $i++ . '</td>
	<td class="tiny">' . $r['type'] . '</td>
	<td class="tiny">' . $r['group_name'] . '</td>
	<td>' . $r['name'] . '</td>
	<td>' . (! is_null($r['ledger_id']) ? anchor('/accounting/ledger/edit/party/'.$r['ledger_id'], $r['ledger'], 'target="_blank"') : '') . '</td>
	<td class="tiny">' . $r['attached_docs'] . '</td>
	<td class="tiny">' . $r['pending_docs'] . '</td>
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

$(document).ready(function() {
	$("#Jobs").find('thead tr').children().each(function(i, e) {
	    $($("#FixedHeader").find('thead tr').children()[i]).width($(e).width());
	});
	$("#FixedHeader").width($("#Jobs").width());

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