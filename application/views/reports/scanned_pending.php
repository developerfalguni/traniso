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
	<?php echo form_open($this->uri->uri_string(), 'class="form-search" id="SearchForm"'); ?>
	<table class="table toolbar">
	<tr>
		<td class="nowrap" width="400px">
			<div class="row">
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
				</div>
			</div>
		</td>		

		<td>
			<?php echo form_dropdown('hide_id[]', null, 0, 'multiple id="HideLedger" class="SelectizeKaabar" data-placeholder="Select ledgers to hide"'); ?>
		</td>
	</tr>
	</table>
	</form>

	<table class="table table-condensed table-striped table-bordered hide" id="FixedHeader">
	<thead>
	<tr>
		<th>Sr No</th>
		<th>Voucher No</th>
		<th>Date</th>
		<th>Debit Code</th>
		<th>Debit Account</th>
		<th>Credit Code</th>
		<th>Credit Account</th>
		<th>Amount</th>
		<th>Document</th>
	</tr>
	</thead>
	</table>
</div>

<table class="table table-condensed table-striped table-bordered" id="Result">
<thead>
<tr>
	<th>Sr No</th>
	<th>Voucher No</th>
	<th>Date</th>
	<th>Debit Code</th>
	<th>Debit Account</th>
	<th>Credit Code</th>
	<th>Credit Account</th>
	<th>Amount</th>
	<th>Document</th>
</tr>
</thead>

<tbody>
<?php 
$i = 1;
$hide_ids = array();
foreach ($rows as $r) {
	$hide_ids[$r['dr_ledger_id']] = $r['dr_code'] . ' - ' . $r['dr_name'];
	$hide_ids[$r['cr_ledger_id']] = $r['cr_code'] . ' - ' . $r['cr_name'];

	echo '<tr>
	<td class="aligncenter">' . $i++ . '</td>
	<td>' . anchor('/accounting/' . underscore($r['url']), $r['id2_format'], 'target="_blank"') . '</td>
	<td class="aligncenter">' . $r['date'] . '</td>
	<td>' . $r['dr_code'] . '</td>
	<td>' . $r['dr_name'] . '</td>
	<td>' . $r['cr_code'] . '</td>
	<td>' . $r['cr_name'] . '</td>
	<td class="alignright">' . $r['amount'] . '</td>
	<td class="aligncenter">' . $r['document'] . '</td>
</tr>';
} ?>
</tbody>
</table>

<script>
hid = [<?php
foreach($hide_ids as $i => $r) {
	echo "'<option value=\"" . $i . "\">" . $r . "</option>',";
}
?>];

$('#HideLedger').get(0).innerHTML = hid.join('');

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
});
</script>