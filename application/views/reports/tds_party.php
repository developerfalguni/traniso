

<div id="FixedToolbar">
	<table class="table toolbar">
	<tr>
		<td>
	<?php echo form_open($this->uri->uri_string(), 'id="Report"'); ?>
	<input type="hidden" name="from_date" value="<?php echo $from_date ?>" id="FromDate" />
	<input type="hidden" name="to_date"   value="<?php echo $to_date ?>" id="ToDate" />
	<div id="ReportRange" class="pull-left" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc">
		<i class="icon-calendar icon-large"></i> <span></span> <b class="caret"></b>
	</div>&nbsp;
	<button type="submit" class="btn btn-primary" id="SearchButton"><i class="fa fa-search"></i> Search</button>&nbsp;
	<div class="btn-group">
	<?php echo anchor($this->_clspath.$this->_class."/preview", 'Preview', 'class="btn btn-default Popup"') ?>
	<?php echo anchor($this->_clspath.$this->_class."/preview/1", 'PDF', 'class="btn btn-default Popup"') ?>
	<?php echo anchor($this->_clspath.$this->_class."/excel", 'Excel', 'class="btn btn-warning Popup"') ?>
	</div>
	</form>
		</td>
	</tr>
	</table>

	<table class="table table-condensed table-striped table-bordered hide" id="FixedHeader">
	<thead>
	<tr>
		<th>No</th>
		<th>Party</th>
		<th>Address</th>
		<th>PAN No</th>
	</tr>
	</thead>
	</table>
</div>

<table class="table table-condensed table-striped table-bordered" id="Result">
<thead>
<tr>
	<th>No</th>
	<th>Party</th>
	<th>Address</th>
	<th>PAN No</th>
</tr>
</thead>

<tbody>
<?php 
$i = 1;
foreach ($rows as $r) {
	echo '<tr>
	<td class="aligncenter">' . $i++ . '</td>
	<td>' . $r['party_name'] . '</td>
	<td>' . $r['address'] . '</td>
	<td>' . $r['pan_no'] . '</td>
</tr>
';
}
?>
</tfoot>
</tbody>
</table>

<script type="text/javascript">
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
});
</script>
