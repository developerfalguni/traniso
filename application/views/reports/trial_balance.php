<style>
.SubTotal { background-color: #eee !important; }
</style>

<div id="FixedToolbar">
	<?php echo form_open($this->uri->uri_string(), 'class="form-search" id="SearchForm"'); ?>
	<input type="hidden" name="search_form" value="1" />
	<table class="table toolbar">
	<tr>
		<td class="col-md-3">
			<div class="input-group date DatePicker">
				<span class="input-group-addon">Upto <i class="icon-calendar"></i></span>
				<input type="text" class="form-control form-control-sm AutoDate" name="upto" value="<?php echo ($upto != '00-00-0000' ? $upto : '') ?>" />
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
							<button type="submit" class="btn btn-primary" id="SearchButton"><i class="fa fa-search"></i> Search</button>
							<a href="#modal-search" class="btn btn-primary" data-toggle="modal"><i class="icon-search-plus"></i></a>
						</span>
					</div>
				</div>
				<div class="col-md-4">
					<div class="btn-group">
					<?php 
					echo anchor($this->_clspath.$this->_class."/preview", '<i class="icon-file-o"></i>', 'class="btn btn-default Preview Popup"') .
						anchor($this->_clspath.$this->_class."/preview/1", '<i class="icon-file-pdf"></i>', 'class="btn btn-default Preview Popup"') .
						anchor($this->_clspath.$this->_class."/excel", '<i class="icon-file-excel"></i>', 'class="btn btn-warning"')
					?>
					</div>

					<div class="btn-group">
					<?php 
					echo anchor($this->_clspath.$this->_class."/preview/0/1", '<i class="icon-file-o"></i>', 'class="btn btn-default Preview Popup"') .
						anchor($this->_clspath.$this->_class."/preview/1/1", '<i class="icon-file-pdf"></i>', 'class="btn btn-default Preview Popup"')
					?>
					</div>
				</div>
			</div>
		</td>
	</tr>
	</table>
	</form>

	<table class="table table-condensed table-striped table-bordered hide" id="FixedHeader">
	<thead>
	<tr>
		<th>Code</th>
		<th>Name</th>
		<th>Opening</th>
		<th>Debit</th>
		<th>Credit</th>
		<th>Closing</th>
	</tr>
	</thead>
	</table>
</div>

<table class="table table-condensed table-striped table-bordered" id="Result">
<thead>
<tr>
	<th width="80px">Code</th>
	<th>Name</th>
	<th>Opening</th>
	<th>Debit</th>
	<th>Credit</th>
	<th>Closing</th>
</tr>
</thead>

<tbody>
<?php 
$total = array('opening' => 0, 'debit' => 0, 'credit' => 0, 'closing' => 0);
foreach ($rows as $group_name => $groups) {
	$group = array('opening' => 0, 'debit' => 0, 'credit' => 0, 'closing' => 0);
	echo '<tr class="SubTotal bold hide"><td colspan="6">' . $group_name . '</td></tr>';
	foreach ($groups as $r) {
		$group['opening'] = bcadd($group['opening'], $r['opening'], 2);
		$group['debit']   = bcadd($group['debit'], $r['debit'], 2);
		$group['credit']  = bcadd($group['credit'], $r['credit'], 2);
		$group['closing'] = bcadd($group['closing'], $r['closing'], 2);

		$total['opening'] = bcadd($total['opening'], $r['opening'], 2);
		$total['debit']   = bcadd($total['debit'], $r['debit'], 2);
		$total['credit']  = bcadd($total['credit'], $r['credit'], 2);
		$total['closing'] = bcadd($total['closing'], $r['closing'], 2);

		echo '<tr>
<td>' . $r['code'] . '</td>
<td>' . anchor($this->_clspath.'account/index/'.$r['id'], $r['name'], 'target="_blank"') . '</td>
<td class="alignright"><span class="' . ($r['opening'] >= 0 ? '' : 'red') . '">' . inr_format(abs($r['opening'])) . ' ' . $this->accounting->getDrCr($r['opening']) . '</span></td>
<td class="alignright">' . inr_format($r['debit']) . '</td>
<td class="alignright">' . inr_format($r['credit']) . '</td>
<td class="alignright"><span class="' . ($r['closing'] >= 0 ? '' : 'red') . '">' . inr_format(abs($r['closing'])) . ' ' . $this->accounting->getDrCr($r['closing']) . '</span></td>
</tr>';
	}
	echo '<tr>
<td class="bold" colspan="2">' . $group_name . '</td>
<td class="alignright bold"><span class="' . ($group['opening'] >= 0 ? '' : 'red') . '">' . inr_format(abs($group['opening'])) . ' ' . $this->accounting->getDrCr($group['opening']) . '</span></td>
<td class="alignright bold">' . inr_format($group['debit']) . '</td>
<td class="alignright bold">' . inr_format($group['credit']) . '</td>
<td class="alignright bold"><span class="' . ($group['closing'] >= 0 ? '' : 'red') . '">' . inr_format(abs($group['closing'])) . ' ' . $this->accounting->getDrCr($group['closing']) . '</span></td>
</tr>';
} 
?>
</tbody>

<tfoot>
<tr>
	<th class="alignright" colspan="2">Grand Total</th>
	<th class="alignright"><span class="<?php echo ($total['opening'] >= 0 ? '' : 'red') ?>"><?php echo inr_format(abs($total['opening'])) . ' ' . $this->accounting->getDrCr($total['opening']) ?></th>
	<th class="alignright"><?php echo inr_format($total['debit']) ?></th>
	<th class="alignright"><?php echo inr_format($total['credit']) ?></th>
	<th class="alignright"><span class="<?php echo ($total['closing'] >= 0 ? '' : 'red') ?>"><?php echo inr_format(abs($total['closing'])) . ' ' . $this->accounting->getDrCr($total['closing']) ?></th>
</tr>
</tfoot>
</table>

<script>
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
});
</script>