
<div id="modal-email" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<?php echo form_open($this->_clspath.$this->_class.'/preview/1/1', 'id="MainForm"'); ?>
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><i class="icon-times"></i></button>
				<h3>eMail <?php echo $page_title ?></h3>
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

<?php echo form_open($this->uri->uri_string(), 'class="form-search" id="SearchForm"'); ?>
<input type="hidden" name="search_form" value="1" />
<input type="hidden" name="party_id" value="<?php echo $party_id ?>" id="PartyID" />
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
		<button type="submit" class="btn btn-primary" id="SearchButton"><i class="fa fa-search"></i> Search</button>&nbsp;
		<div class="btn-group">
		<?php 
			echo anchor($this->_clspath.$this->_class."/excel/", "Excel", 'class="btn btn-warning Popup"');
			if ($party_id > 0) {
				echo anchor($this->_clspath.$this->_class.'/preview/0', "HTML", 'class="btn btn-default Popup" ') .
					anchor($this->_clspath.$this->_class.'/preview/1', '<i class="icon-file-pdf"></i>', 'class="btn btn-default Popup" ') .
					'<a href="#modal-email" class="btn btn-info" ' . ($party_id == 0 ? 'disabled="true"' : 'data-toggle="modal"') . '><i class="icon-envelope-o"></i> Email</a>';
			}
		?>
		</div>
	</td>

	<td>
		<div class="nowrap">
			<button type="button" class="btn btn-group btn-primary" onclick="javascript: clearSearch()"><i class="icon-align-justify"></i></button>
			
			<div class="btn-group">
				<button data-toggle="dropdown" class="btn btn-info dropdown-toggle"><?php echo ($party_id > 0 ? '<i class="icon-filter4"></i>' : '') ?> Party <span class="caret"></span></button>
				<ul class="dropdown-menu nav-menu-scroll" id="FilterParty">
					<li><a class="red" href="javascript: filterParty('0')">Clear Filter</a></li>
				</ul>
			</div>
		</div>
	</td>
</tr>
</table>
</form>

<table class="table table-condensed table-striped table-bordered" id="Result">
<thead>
<tr>
	<th>No</th>
	<th>Job No</th>
	<th>Date</th>
	<th>Shipper</th>
	<th width="120px">Inward Amount</th>
	<th width="120px">Outward Amount</th>
	<th width="120px">Balance</th>
</tr>
</thead>

<tbody>
<?php 
$i = 1;
$filter = array(
	'party_id'      => array(),
);
$total = array(
	'inward_amount'  => 0,
	'outward_amount' => 0,
	'balance_amount' => 0,
);
foreach ($rows as $r) {
	$filter['party_id'][$r['party_name']] = $r['party_id'];

	$total['inward_amount']  = bcadd($total['inward_amount'], $r['inward_amount']);
	$total['outward_amount'] = bcadd($total['outward_amount'], $r['outward_amount']);
	$total['balance_amount'] = bcadd($total['balance_amount'], $r['balance_amount']);

	echo '<tr>
	<td class="aligncenter">' . $i++ . '</td>
	<td class="aligncenter big">' . anchor('/export/jobs/edit/' . $r['id'], $r['id2_format'], 'target="_blank"') . '</td>
	<td class="">' . $r['date'] . '</td>
	<td class="">' . $r['party_name'] . '</td>
	<td class="alignright red">' . $r['inward_amount'] . '</td>
	<td class="alignright green">' . $r['outward_amount'] . '</td>
	<td class="alignright ' . ($r['balance_amount'] > 0 ? 'green' : 'red') . '">' . $r['balance_amount'] . '</td>
</tr>';
} 
?>
</tbody>

<tfoot>
<tr>
	<th class="alignright" colspan="4">Total</th>
	<th class="alignright"><?php echo $total['inward_amount'] ?></th>
	<th class="alignright"><?php echo $total['outward_amount'] ?></th>
	<th class="alignright"><?php echo $total['balance_amount'] ?></th>
</tr>
</tfoot>
</table>

<script type="text/javascript">
function clearSearch() {
	$('input#PartyID').val(0);
	$('#SearchButton').click();
}

function filterParty(id) {
	$('input#PartyID').val(id);
	$('#SearchButton').click();
}

$(document).ready(function() {
	<?php echo 'dateRangePicker({
		years:     ['.$years[0].', '.$years[1].'],
		from_date: \''.convDate($from_date).'\',
		to_date:   \''.convDate($to_date).'\'
	});' ?>

	$('.ajaxEmail').on('keydown.autocomplete', function(event, items){
		$(this).autocomplete({
			appendTo: '#modal-email',
			source: function(request, response) {
				$.ajax({
					type: "POST",
					url: '<?php echo site_url('/master/party/ajaxEmail/'.$party_id) ?>',
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
				.append('<a>' + item.name +  ' &lt;' + item.email +  '&gt;</a>')
				.appendTo(ul);
		}
	});

<?php 
if (count($filter['party_id']) > 0) {
	ksort($filter['party_id']);
	foreach ($filter['party_id'] as $k => $v) {
		echo '$("ul#FilterParty").append("<li><a href=\"javascript: filterParty(' . $v . ')\">' . $k . '</a></li>");';
	}
}
?>
});
</script>