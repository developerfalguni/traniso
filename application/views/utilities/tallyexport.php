<?php echo form_open($this->uri->uri_string()); ?>
<table class="table toolbar">
<tr>
	<td><input type="hidden" name="from_date" value="<?php echo $from_date ?>" id="FromDate" />
<input type="hidden" name="to_date"   value="<?php echo $to_date ?>" id="ToDate" />
<div id="ReportRange" class="pull-left" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc">
	<i class="icon-calendar icon-large"></i> <span></span> <b class="caret"></b>
</div>&nbsp;
Company : <?php echo form_dropdown('company_id', getSelectOptions('companies', 'id', 'CONCAT(code, " - ", name)'), $company_id, 'class="form-control form-control-sm"') ?></span>&nbsp;&nbsp;&nbsp;
<?php echo form_submit('Submit', 'Transfer To Tally', "class='btn btn-primary'") ?>
	</td>
</tr>
</table>
</form>

<script type="text/javascript">
$(document).ready(function() {
	<?php echo 'dateRangePicker({
		years:     ['.$years[0].', '.$years[1].'],
		from_date: \''.convDate($from_date).'\',
		to_date:   \''.convDate($to_date).'\'
	});' ?>
});
</script>
