
<div id="modal-add" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
		<?php 
			echo form_open($this->_clspath.$this->_class.'/create'); 
			echo form_hidden('party_id', $party_id);
			echo form_hidden('bill_template_id', $bill_template_id);
		?>
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><i class="icon-times"></i></button>
				<h3>Add New Rates</h3>
			</div>
			<div class="modal-body">
				<div class="form-group">
					<label class="control-label">Effective Date</label>
					<div class="input-group date DatePicker">
						<span class="input-group-addon"><i class="icon-calendar"></i></span>
						<input type="text" class="form-control form-control-sm AutoDate" name="wef_date" value="<?php echo date('d-m-Y') ?>" />
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-success">Create New Rate</button>
			</div>
		</form>
		</div>
	</div>
</div>

<div id="modal-copy" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
		<?php 
			echo form_open($this->_clspath.$this->_class.'/copy'); 
			echo form_hidden('party_id', $party_id);
			echo form_hidden('bill_template_id', $bill_template_id);
		?>
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><i class="icon-times"></i></button>
				<h3>Copy Rates</h3>
			</div>
			<div class="modal-body">
				<div class="form-group">
					<label class="control-label">Effective Date</label>
					<div class="input-group date DatePicker">
					<span class="input-group-addon"><i class="icon-calendar"></i></span>
					<input type="text" class="form-control form-control-sm AutoDate" name="new_wef_date" value="<?php echo date('d-m-Y') ?>" />
				</div>
				</div>

				<div class="form-group">
					<label class="control-label">Select Party &amp; WEF Date</label>
					<input type="hidden" name="copy_party_id" value="" id="LID" />
					<input type="hidden" name="wef_date" value="" id="WEFDate" />
					<input type="text" class="form-control form-control-sm" value="" id="ajaxPartyRate" />
				</div>
			</div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-success">Copy Rates</button>
			</div>
		</form>
		</div>
	</div>
</div>

<div class="card card-default">
	<div class="card-header">
		<h3 class="card-title"><span class=""><?php echo anchor($this->_clspath.$this->_class, '<i class="fa fa-list pr-1"></i>'. strtoupper($page_title)) ?></span></h3>
		<div class="card-tools">
  			<ol class="breadcrumb float-sm-right m-0">
      			<li class="breadcrumb-item"><a href="#"><?php echo anchor('main','Dashboard') ?></a></li>
      			<li class="breadcrumb-item"><?php echo humanize(clean($this->_clspath)) ?></li>
      			<li class="breadcrumb-item active mr-1"><?php echo humanize($this->_class) ?> edit</li>
    		</ol>
		</div>
	</div>
	
	<div class="card-body">

		<?php
		echo form_open($this->uri->uri_string(), 'id="MainForm"');
		echo form_hidden('type', $rows['type']);
		echo form_hidden('cargo_type', $rows['cargo_type']);
		echo form_hidden('product_id', $rows['product_id']);
		echo form_hidden('indian_port_id', $rows['indian_port_id']);
		echo form_hidden('berth_no', explode(',', $rows['berth_no']));
		?>
		<fieldset>
			<div class="row">
				<div class="col-md-12">
					<div class="form-group">
						<label class="control-label">Party Name</label>
						<h5 class="orange"><?php echo $party['name'] ?></h5>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-md-1">
					<div class="form-group">
						<label class="control-label">Type</label>
						<h5><?php echo $rows['type'] ?></h5>
					</div>
				</div>

				<div class="col-md-1">
					<div class="form-group">
						<label class="control-label">Cargo Type</label>
						<h5><?php echo $rows['cargo_type'] ?></h5>
					</div>
				</div>

				<div class="col-md-3">
					<div class="form-group">
						<label class="control-label">Product</label>
						<h5><?php echo $rows['product_name'] ?></h5>
					</div>
				</div>

				<div class="col-md-2">
					<div class="form-group">
						<label class="control-label">Indian Port</label>
						<h5><?php echo $rows['indian_port'] ?></h5>
					</div>
				</div>

				<div class="col-md-5">
					<div class="form-group">
						<label class="control-label">Berth No</label>
						<h5><?php echo $rows['berth_no'] ?></h5>
					</div>
				</div>
			</div>

			<ul class="nav nav-tabs" id="Rates">
				<?php 
				if (isset($rows['rates'])) {
					foreach ($rows['rates'] as $wef => $rates)
						echo '<li><a href="#' . $wef . '">' . $wef . '</a></li>';
				}
				?>
			</ul>

			<div class="tab-content">
			<?php 
			if (isset($rows['rates'])) :
				$i = 1;
				foreach ($rows['rates'] as $wef => $rates) : ?>
				<div class="tab-pane" id="<?php echo $wef ?>">
					<table class="table table-condensed table-striped DataEntry Sortable">
					<thead>
					<tr>
						<th width="24px"></th>
						<th width="64px">Sr No</th>
						<th width="100px">Code</th>
						<th>Particular</th>
						<th width="140px">Calc Type</th>
						<th width="140px">Unit Type</th>
						<th width="80px">Rate</th>
						<th width="24px" class="aligncenter"><a href="javascript: CheckAll(<?php echo $i ?>)"><i class="icon-trashcan"></i></a></th>
					</tr>
					</thead>

					<tbody>
					<?php 
					$sr_no = 1;
					foreach ($rates as $row) {
						echo '
					<tr>
						<td class="aligncenter grayLight SortHandle"><i class="icon-bars"></i></th>
						<td><input type="text" class="form-control form-control-sm Numeric" name="sr_no[' . $row['id'] . ']" value="' . $row['sr_no'] . '" /></td>
						<td><input type="hidden" name="bill_item_id[' . $row['id'] . ']" value="' . $row['bill_item_id'] . '" />
							' . $row['code'] . '</td>
						<td><input type="text" class="form-control form-control-sm" name="particulars[' . $row['id'] . ']" value="' . $row['particulars'] . '" /></td>
						<td>' . form_dropdown('calc_type[' . $row['id'] . ']', $this->office->getCalcType(), $row['calc_type'], 'class="form-control form-control-sm"') . '</td>
						<td>' . form_dropdown('unit_type[' . $row['id'] . ']', $this->office->getUnitType(), $row['unit_type'], 'class="form-control form-control-sm"') . '</td>
						<td class="aligncenter">' . ($row['calc_type'] == 'Vouchers' ? 'N/A<input type="hidden" name="rate[' . $row['id'] . ']" value="' . $row['rate'] . '" />' : '<input type="text" class="form-control form-control-sm Numeric" name="rate[' . $row['id'] . ']" value="' . $row['rate'] . '" />') . '</td>
						<td class="aligncenter">' . form_checkbox(array('name' => 'delete_id['.$row['id'].']', 'value' => $row['id'], 'checked' => false, 'class' => 'DeleteCheckbox_'.$i)) . '</td>
					</tr>';
						$sr_no = $row['sr_no'];
					}
					?>

					<tr class="TemplateRow">
						<td class="aligncenter grayLight SortHandle"><i class="icon-bars"></i></th>
						<td><input type="text" class="form-control form-control-sm Numeric Unchanged Increment Focus" name="new_sr_no[]" value="<?php echo ($sr_no + 1) ?>" />
							<input type="hidden" class="form-control form-control-sm Unchanged" name="new_wef_date[]" value="<?php echo $wef ?>" /></td>
						<td><input type="hidden" class="form-control form-control-sm BillItemID" name="new_bill_item_id[]" value="" />
							<input type="text" class="form-control form-control-sm ajaxBillItem" value="" /></td>
						<td><input type="text" class="form-control form-control-sm Particular" name="new_particulars[]" value="" /></td>
						<td><?php echo form_dropdown('new_calc_type[]', $this->office->getCalcType(), 'Fixed', 'class="form-control form-control-sm"') ?></td>
						<td><?php echo form_dropdown('new_unit_type[]', $this->office->getUnitType(), 'N/A', 'class="form-control form-control-sm"') ?></td>
						<td><input type="text" class="form-control form-control-sm Numeric" name="new_rate[]" value="" /></td>
						<td class="aligncenter"><button type="button" class="btn btn-success btn-sm AddButton" id="Add"><i class="icon-white icon-plus"></i></button></td>
					</tr>
					</tbody>
					</table>
				</div>
			<?php 
				$i++;
				endforeach;
			endif;
			?>
			</div>
		</fieldset>
	</div>

	<div class="card-footer">
		<button type="submit" class="btn btn-success" id="Update">Update</button>
		<button type="button" class="btn btn-danger" onclick="javascript: window.close()">Close</button>
	</div>
</div>

</form>

<script>
var checked = [];

function CheckAll(id) {
	if (checked[id]) {
		$(".DeleteCheckbox_"+id).attr("checked", "checked");
		checked[id] = 0;
	}
	else {
		$(".DeleteCheckbox_"+id).removeAttr("checked");
		checked[id] = 1;
	}
}

$(document).ready(function() {
	$("#Update").on('click', function() {
		$("form#MainForm").submit();
	});

	$('#Rates a:last').tab('show');

	$('#Rates a').click(function (e) {
		e.preventDefault();
		$(this).tab('show');
	})

	$("#ajaxPartyRate").autocomplete({
		appendTo: '#modal-copy',
		source: "<?php echo site_url($this->_clspath.$this->_class.'/ajaxPartyRates/'.$bill_template_id) ?>",
		minLength: 3,
		focus: function(event, ui) {
			$("#LID").val(ui.item.party_id);
			$("#WEFDate").val(ui.item.wef_date);
			$("#ajaxPartyRate").val(ui.item.name + ' - ' + ui.item.type);
			return false;
		},
		select: function(event, ui) {
			$("#LID").val(ui.item.party_id);
			$("#WEFDate").val(ui.item.wef_date);
			$("#ajaxPartyRate").val(ui.item.name + ' - ' + ui.item.type);
			return false;
		}
	})
	.data('ui-autocomplete')._renderItem = function(ul, item) {
		return $('<li></li>')
			.data('item.autocomplete', item)
			.append('<a><span class="tiny">' + item.name + ' <span class="orange">' + item.wef_date + '</span> ' + item.type + '</span></a>')
			.appendTo(ul);
	};

	$('.DataEntry').on('keydown.autocomplete', ".ajaxBillItem", function(event, items){
		var id = $(this).parent('td').parent('tr').find('.BillItemID');
		var pa = $(this).parent('td').parent('tr').find('.Particular');
		$(this).autocomplete({
			source: "<?php echo site_url('/accounting/ledger/ajaxLedgers/Bill Items') ?>",
			minLength: 1,
			focus: function(event, ui) {
				$(id).val(ui.item.id);
				$(this).val(ui.item.code);
				$(pa).val(ui.item.name);
				return false;
			},
			select: function(event, ui) {
				$(id).val(ui.item.id);
				$(this).val(ui.item.code);
				$(pa).val(ui.item.name);
				return false;
			}
		})
		.data('ui-autocomplete')._renderItem = function(ul, item) {
			return $('<li></li>')
				.data('item.autocomplete', item)
				.append('<a><strong class="blueDark">' + item.code + '</strong> ' + item.name + '</a>')
				.appendTo(ul);
		};
	});
});
</script>