<style>
.Date, .BLDate { width: 70px; }
td.markRed { background-color: #F33 !important;}
td.markYellow { background-color: #ffc !important;}

</style>

<div id="modal-search" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><i class="icon-times"></i></button>
				<h3>Advanced Search</h3>
			</div>
			<div class="modal-body form-horizontal">
				<table class="table table-condensed table-striped">
				<tbody>
				<?php foreach($search_fields as $f => $n) {
				echo "<tr>
					<td class=\"alignright alignmiddle\">$n :</td>
					<td><input type=\"text\" class=\"form-control AdvancedSearch\" name=\"$f\" /></td>
				</tr>";
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
		<!-- <td width="25%" class="nowrap"><h3><?php echo humanize($page_title) . ' <small>' . $page_desc . '</small>'; ?></h3></td> -->
		<td class="nowrap" width="30%">
			<?php echo form_open($this->uri->uri_string(), 'class="form-search" id="SearchForm"'); ?>
				<input type="hidden" name="search_form" value="1" />
				<input type="hidden" name="sortby" value="<?php echo $sortby ?>" id="SortBy" />
			<div class="input-group">
				<input type="text" class="form-control form-control-sm search-query" name="search" value="<?php echo (isset($search) ? $search : '') ?>" id="Search" />
				<button type="submit" class="btn btn-primary" id="SearchButton"><i class="fa fa-search"></i> Search</button>
			</div>
			<a href="#modal-search" class="btn btn-primary" data-toggle="modal"><i class="icon-search-plus"></i></a>
			<?php echo anchor($this->_clspath.$this->_class."/excel", 'Excel', 'class="btn btn-warning Popup"') ?>
			</form>
		</td>

		<td>
			<div class="nowrap">
				<button type="button" class="btn btn-group btn-primary" onclick="javascript: clearSearch()"><i class="icon-align-justify"></i></button>
				
				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-info dropdown-toggle"><?php echo (isset($parsed_search['party']) ? '<i class="icon-filter4"></i>' : '') ?> Party <span class="caret"></span></button>
					<div class="dropdown-menu" style="padding: 10px; width:500px;">
						<div class="row">
							<div class="col-md-6" id="FilterPartyLeft"></div>
							<div class="col-md-6" id="FilterPartyRight"></div>
						</div>
					</div>
				</div>
				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-success dropdown-toggle"><?php echo (isset($parsed_search['product']) ? '<i class="icon-filter4"></i>' : '') ?> Product <span class="caret"></span></button>
					<ul class="dropdown-menu" id="FilterProduct"></ul>
				</div>
				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-warning dropdown-toggle"><?php echo (isset($parsed_search['line']) ? '<i class="icon-filter4"></i>' : '') ?> Shipping Line <span class="caret"></span></button>
					<ul class="dropdown-menu" id="FilterLine"></ul>
				</div>
				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-danger dropdown-toggle"><?php echo (isset($parsed_search['port']) ? '<i class="icon-filter4"></i>' : '') ?> Loading Port <span class="caret"></span></button>
					<ul class="dropdown-menu" id="FilterPort"></ul>
				</div>
			</div>
		</td>

		<td width="25%" class="alignright nowrap">
			<?php echo anchor("/export/jobs/edit/0/Container", '<i class="fa fa-plus"></i> Add Shipment', 'class="btn btn-success"'); ?></td>
	</tr>
	</table>

	<table class="table table-condensed table-striped table-bordered hide" id="FixedHeader">
	<thead>
	<tr>
		<th>No</th>
		<th>Party Name</th>
		<th>Invoice No / Date</th>
		<th>Commodity</th>
		<th>SB No / Date</th>
		<th>Cont.</th>
		<th>Shipping Line</th>
		<th>Vessel</th>
		<th>POL</th>
		<th>POD</th>
		<th>Cutoff</th>
		<th>CFS</th>
		<th>Remarks</th>
	</tr>
	</thead>
	</table>
</div>

<?php echo form_open($this->uri->uri_string(), array('class' => 'form-horizontal', 'id' => 'PendingForm')); ?>
<table class="table table-condensed table-striped table-bordered" id="Jobs">
<thead>
<tr>
	<th>No</th>
	<th>Party Name</th>
	<th>Invoice No / Date</th>
	<th>Commodity</th>
	<th>SB No / Date</th>
	<th>Cont.</th>
	<th>Shipping Line</th>
	<th>Vessel</th>
	<th>POL</th>
	<th>POD</th>
	<th>Cutoff</th>
	<th>CFS</th>
	<th>Remarks</th>
</tr>
</thead>

<tbody>
<?php
	$i = 1;
	foreach ($export_details as $ed) {
		echo '<tr>
	<td class="aligncenter">' . $i++ . '</td>
	<td class="tiny">' . $ed['party_name'] . '</td>
	<td class="tiny">' . anchor('/export/jobs/edit/' . $ed['job_id'], $ed['invoice_no'] . ' / ' . $ed['invoice_date'], 'class="red"') . '</td>
	<td class="tiny">' . $ed['product_name'] . '</td>
	<td class="tiny blue">' . $ed['sb_no'] . ' / ' . $ed['sb_date'] . '</td>
	<td class="tiny">' . $ed['containers'] . '</td>
	<td class="tiny">' . $ed['line_name'] . '</td>
	<td class="tiny">' . $ed['vessel_name'] . '</td>
	<td class="tiny">' . $ed['pol'] . '</td>
	<td class="tiny">' . $ed['pod'] . '</td>
	<td class="tiny">' . $ed['cutoff_date'] . '</td>
	<td class="tiny">' . $ed['cfs_name'] . '</td>
	<td class="tiny">' . $ed['remarks'] . '</td>
	</tr>';
	}
?>
</tbody>
</table>
</form>

<script>

function Save() {
	$("#PendingForm").submit();
}

function Sort(sortby) {
	$("form#SearchForm input#SortBy").val(sortby);
	$("#SearchButton").click();
}

$(".Date").datepicker({
	duration: '',
	dateFormat: "dd-mm-yy",
	yearRange: "-5:+1",
	mandatory: true,
	showButtonPanel: true,
	changeMonth: true,
	changeYear: true,
	showOtherMonths: true,
	showStatus: true
});

$(".BLDate").datepicker({
	duration: '',
	dateFormat: "dd-mm-yy",
	minDate: '-2',
	maxDate: '0',
	mandatory: true,
	showButtonPanel: true,
	changeMonth: true,
	changeYear: true,
	showOtherMonths: true,
	showStatus: true,
	showOn: "focus",
	buttonImage: "<?php echo base_url('images/calendar.png') ?>",
	buttonImageOnly: true
});

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
});
</script>