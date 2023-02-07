<style>
.markChanged { background-color: #1BDB1A !important; }
</style>

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


<?php echo form_open($this->uri->uri_string(), 'class="form-search" id="SearchForm"'); ?>
<input type="hidden" name="search_form" value="1" />
<input type="hidden" name="party_id" value="<?php echo $party_id ?>" id="PartyID" />
<table class="table toolbar">
<tr>
	<td width="160px">
		<div class="input-group input-group-sm">
			<input type="text" class="form-control form-control-sm DatePicker" name="date" value="dd-mm-YY">
			<div class="input-group-append">
				<div class="input-group-text"><i class="icon-calendar"></i></div>
			</div>
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
						<button type="submit" class="btn btn-primary" id="SearchButton"><i class="icon-search icon-white"></i> Search</button>
			      	</span>
				</div>
			</div>
			<div class="col-md-4">
				<a href="#modal-search" class="btn btn-primary" data-toggle="modal"><i class="icon-search-plus"></i></a>
				<div class="btn-group">
				<?php 
					// echo anchor($this->_clspath.$this->_class."/excel/", "Excel", 'class="btn btn-warning Popup"');
					if ($party_id > 0) {
					echo anchor($this->_clspath.$this->_class.'/preview/0', '<i class="icon-file-o"></i>', 'class="btn btn-default Popup" ') . 
						anchor($this->_clspath.$this->_class.'/preview/1', '<i class="icon-file-pdf"></i>', 'class="btn btn-default Popup" ') .
						'<a href="#modal-email" class="btn btn-info" ' . ($party_id == 0 ? 'disabled="true"' : 'data-toggle="modal"') . '><i class="icon-envelope-o"></i> Email</a>';
					}
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
				<button data-toggle="dropdown" class="btn btn-info dropdown-toggle"><?php echo ($party_id > 0 ? '<i class="icon-filter4"></i>' : '') ?> Party <span class="caret"></span></button>
				<ul class="dropdown-menu nav-menu-scroll pull-right" id="FilterParty">
					<li><a class="red" href="javascript: filterParty(0)">Clear Filter</a></li>
				</ul>
			</div>

			<div class="btn-group">
				<button data-toggle="dropdown" class="btn btn-info dropdown-toggle"><?php echo (isset($parsed_search['job_no']) ? '<i class="icon-filter4"></i>' : '') ?> Job No <span class="caret"></span></button>
				<ul class="dropdown-menu nav-menu-scroll pull-right" id="FilterJobNo">
					<li><a class="red" href="javascript: filter('job_no:')">Clear Filter</a></li>
				</ul>
			</div>

			<div class="btn-group">
				<button data-toggle="dropdown" class="btn btn-info dropdown-toggle"><?php echo (isset($parsed_search['invoice_no']) ? '<i class="icon-filter4"></i>' : '') ?> Invoice No <span class="caret"></span></button>
				<ul class="dropdown-menu nav-menu-scroll pull-right" id="FilterInvoiceNo">
					<li><a class="red" href="javascript: filter('invoice_no:')">Clear Filter</a></li>
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
	<th>Date</th>
	<th>Job No</th>
	<th>Party Name</th>
	<th>Invoice No</th>
	<th>Vehicle No</th>
	<th>Packaging Type</th>
	<th>Nos. Packages</th>
	<th>Dispatch Weight</th>
	<th>Received Weight</th>
	<th>Supplier Name</th>
	<th>Marks</th>
</tr>
</thead>

<tbody>
<?php 
$filter = array(
	'party_id'   => array(),
	'job_no'     => array(),
	'invoice_no' => array(),
);
$total = array(
	'units'           => 0,
	'dispatch_weight' => 0,
	'received_weight' => 0,
);
foreach ($rows as $invoice_no => $eis) {
	if (isset($eis['cargo_arrivals'])) {
		foreach ($eis['cargo_arrivals'] as $r) {
			$filter['party_id'][$r['party_id']]     = $r['party_name'];
			$filter['job_no'][$r['id2_format']]     = 1;
			$filter['invoice_no'][$r['invoice_no']] = 1;

			$total['units']           += $r['units'];
			$total['dispatch_weight'] += $r['dispatch_weight'];
			$total['received_weight'] += $r['received_weight'];

			echo '<tr>
		<td class="aligncenter">'.$r['date'].'</td>
		<td class="aligncenter big">' . anchor('/export/jobs/edit/' . $r['job_id'], $r['id2_format'], 'target="_blank"') . '</td>
		<td>' . $r['party_name'] . '</td>
		<td>' . $r['invoice_no'] . '</td>
		<td>' . $r['vehicle_no'] . '</td>
		<td class="alignright">'.$r['code'].'</td>
		<td class="alignright">'.$r['units'].'</td>
		<td class="alignright">'.$r['dispatch_weight'].'</td>
		<td class="alignright">'.$r['received_weight'].'</td>
		<td>'.$r['supplier_name'].'</td>
		<td>'.$r['remarks'].'</td>
	</tr>';
		}
	}
} 
?>
</tbody>

<tfoot>
	<tr>
		<th colspan="6" class="alignright">Total</th>
		<th class="alignright"><?php echo $total['units'] ?></th>
		<th class="alignright"><?php echo $total['dispatch_weight'] ?></th>
		<th class="alignright"><?php echo $total['received_weight'] ?></th>
		<th colspan="2"></th>
	</tr>
</tfoot>
</table>

<script type="text/javascript">
function clearSearch() {
	$('input#PartyID').val(0);
	$('input#Search').val('');
	$("#SearchButton").click();
}

function filterParty(id) {
	$('input#PartyID').val(id);
	$("#SearchButton").click();
}

$(document).ready(function() {
	$('#ajaxParty').kaabar_autocomplete({source: '<?php echo site_url($this->_clspath.$this->_class.'/json/parties/id/name') ?>'});

	function splitEmail(val) {
		return val.split(/;\s*/);
	}
	function extractLastEmail(term) {
		return split(term).pop();
	}

	$('.ajaxEmail').on('keydown.autocomplete', function(event, items){
		$(this).autocomplete({
			appendTo: '#modal-email',
			source: function(request, response) {
				$.ajax({
					type: "POST",
					url: '<?php echo site_url('/master/party/ajaxEmail/'.$party_id) ?>',
					dataType: "json",
					data: {
						term: extractLastEmail(request.term),
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
				var terms = splitEmail(this.value);
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
});

<?php 
if (count($filter['party_id']) > 0) {
	ksort($filter['party_id']);
	foreach ($filter['party_id'] as $k => $v) {
		echo '$("ul#FilterParty").append("<li><a href=\"javascript: filterParty(' . $k . ')\">' . trim($v) . '</a></li>");';
	}
}
if (count($filter['job_no']) > 0) {
	ksort($filter['job_no']);
	foreach ($filter['job_no'] as $k => $v) {
		echo '$("ul#FilterJobNo").append("<li><a href=\"javascript: filter(\'job_no: ' . trim($k) . '\')\">' . trim($k) . '</a></li>");';
	}
}
if (count($filter['invoice_no']) > 0) {
	ksort($filter['invoice_no']);
	foreach ($filter['invoice_no'] as $k => $v) {
		echo '$("ul#FilterInvoiceNo").append("<li><a href=\"javascript: filter(\'invoice_no: ' . trim($k) . '\')\">' . trim($k) . '</a></li>");';
	}
}
?>
</script>