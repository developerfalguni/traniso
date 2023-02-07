<div id="modal-copy" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<?php  echo form_open($this->_clspath.$this->_class.'/copy', 'class="form-horizontal"'); ?>
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><i class="icon-times"></i></button>
				<h3>Copy Rates</h3>
			</div>
			<div class="modal-body">
				<fieldset>
					<div class="form-group">
						<label class="control-label">Select New Agent</label>
						<input type="hidden" name="new_agent_id" value="" />
						<input type="text" class="form-control form-control-sm" value="" id="NewAgentName" />
					</div>

					<div class="form-group">
						<label class="control-label">Select Agent Rate</label>
						<input type="hidden" name="agent_rate_id" value="" />
						<input type="text" class="form-control form-control-sm" value="" id="ajaxAgentRate" />
					</div>
				</fieldset>
			</div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-success">Copy Rates</button>
			</div>
		</form>
		</div>
	</div>
</div>

<?php echo form_open($this->uri->uri_string(), 'id="MainForm"'); ?>

<div id="modal-tariff" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><i class="icon-times"></i></button>
				<h3>Tariff Chart</h3>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						<fieldset>
						<input type="hidden" name="agent_rate_id" value="0" id="AgentRateID" />
						<div class="form-group">
							<label class="control-label">Tariff Type</label>
							<?php echo form_dropdown('tariff_type', getEnumSetOptions('agent_tariffs', 'tariff_type'), 'Detention', 'class="form-control form-control-sm" id="TariffType"'); ?>
						</div>
						<br />
						
						<table class="table table-condensed table-striped">
						<thead>	
						<tr>
							<th>From</th>
							<th>To</th>
							<th>Price 20</th>
							<th>Price 40</th>
							<th width="24px" class="aligncenter"><a href="#" class="CheckAll"><i class="icon-trashcan"></i></a></th>
						</tr>
						</thead>

						<tbody id="TariffChart">
						</tbody>
						</table>
						</fieldset>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-success" id="AddTariff">Update</button>
			</div>
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
		<fieldset>
			<div class="row">
				<div class="col-md-2">
					<div class="form-group">
						<label class="control-label">Type</label>
						<?php echo form_dropdown('type', getEnumSetOptions('agent_rates', 'type'), $row['type'], 'class="form-control form-control-sm" id="CargoType"') ?>
					</div>
				</div>

				<div class="col-md-10">	
					<div class="form-group">
						<label class="control-label">Agent Name</label>
						<div class="form-group<?php echo (strlen(form_error('agent_id')) > 0 ? ' error' : '') ?>" >
							<input type="hidden" name="agent_id" value="<?php echo $row['agent_id'] ?>" />
							<input type="text" class="form-control form-control-sm" name="agent_name" value="<?php echo $row['agent_name'] ?>" id="AgentName" />
						</div>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-md-2">
					<div class="form-group">
						<label class="control-label">Destuffing Type</label>
						<?php echo form_dropdown('destuffing_type', getEnumSetOptions('agent_rates', 'destuffing_type'), $row['destuffing_type'], 'class="form-control form-control-sm"') ?>
					</div>
				</div>

				<div class="col-md-4">
					<div class="form-group">
						<label class="control-label">Port Name</label>
						<?php echo form_dropdown('indian_port_id', getSelectOptions('indian_ports', 'id', 'name'), $row['indian_port_id'], 'class="form-control form-control-sm"') ?>
					</div>
				</div>

				<div class="col-md-6">
					<div class="form-group">
						<label class="control-label">Porduct</label>
						<?php echo form_dropdown('product_id', getSelectOptions('products', 'id', 'name'), $row['product_id'], 'class="form-control form-control-sm"') ?>
					</div>
				</div>
			</div>
			<br />

			<table class="table table-condensed table-striped DataEntry">
			<thead>
				<tr>
					<th>Particular</th>
					<th width="120px">Calc. Type</th>
					<th width="100px">Currency</th>
					<th width="100px">Price 20</th>
					<th width="100px">Price 40</th>
					<th width="80px">Taxable</th>
					<th width="24px" class="aligncenter"><i class="icon-list5"></i></th>
					<th width="24px" class="aligncenter"><a href="#" class="CheckAll"><i class="icon-trashcan"></i></a></th>
				</tr>
			</thead>

			<tbody>
				<?php 
				foreach ($row['rates'] as $r) { 
					echo '<tr>
						<td><input type="text" class="form-control form-control-sm Particulars" name="particulars[' . $r['id'] . ']" value="' . $r['particulars'] . '" /></td>
						<td>' . form_dropdown('calc_type[' . $r['id'] . ']', getEnumSetOptions($this->_table, 'calc_type'), $r['calc_type'], 'class="form-control form-control-sm"') . '</td>
						<td>' . form_dropdown('currency_id[' . $r['id'] . ']', getSelectOptions('currencies', 'id', 'code'), $r['currency_id'], 'class="form-control form-control-sm"') . '</td>
						<td><input type="text" class="form-control form-control-sm Numeric" name="price_20[' . $r['id'] . ']" value="' . $r['price_20'] . '" /></td>
						<td><input type="text" class="form-control form-control-sm Numeric" name="price_40[' . $r['id'] . ']" value="' . $r['price_40'] . '" /></td>
						<td>' . form_dropdown('taxable[' . $r['id'] . ']', getEnumSetOptions($this->_table, 'taxable'), $r['taxable'], 'class="form-control form-control-sm"') . '</td>
						<td class="aligncenter"><a href="javascript: loadTariff(\'' . $r['id'] . '\')"><i class="icon-list5"></i></a></td>
						<td class="aligncenter">' . form_checkbox(array('name' => 'delete_id['.$r['id'].']', 'value' => $r['id'], 'checked' => false, 'class' => 'DeleteCheckbox')) . '</td>
					</tr>';
				}
				?>
				
				<tr class="TemplateRow">
					<td><input type="text" class="form-control form-control-sm Particulars Focus" name="new_particulars[]" /></td>
					<td><?php echo form_dropdown('new_calc_type[]', getEnumSetOptions($this->_table, 'calc_type'), $r['calc_type'], 'class="form-control form-control-sm"') ?></td>
					<td><?php echo form_dropdown('new_currency_id[]', getSelectOptions('currencies', 'id', 'code'), 1, 'class="form-control form-control-sm"') ?></td>
					<td><input type="text" class="form-control form-control-sm Numeric" name="new_price_20[]" /></td>
					<td><input type="text" class="form-control form-control-sm Numeric" name="new_price_40[]" /></td>
					<td><?php echo form_dropdown('new_taxable[]', getEnumSetOptions($this->_table, 'taxable'), $r['taxable'], 'class="form-control form-control-sm"') ?></td>
					<td></td>
					<td class="aligncenter"><button type="submit" class="btn btn-success btn-sm AddButton"><i class="fa fa-plus"></i></button></td>
				</tr>
			</tbody>
			</table>
		</fieldset>		
	</div>

	<div class="card-footer">
		<button type="submit" class="btn btn-success" id="Update">Update</button>
	</div>
</div>
</form>

<script type="text/javascript">
function copyRates() {
	$("#modal-copy").modal('hide');
	var id = $("#CopyRates").val();
	$.ajax({
		type: "GET",
		url: '<?php echo base_url($this->_clspath.$this->_class."/loadRates") ?>/'+id,
		dataType: "json",
		success: function(data) {
			$.each(data.containers, function(i, item) {
				$("tr.TemplateRow input:eq(0)").val(item.sr_no);
				$("tr.TemplateRow input:eq(1)").val(item.code);
				$("tr.TemplateRow input:eq(2)").val(item.name);
				$("tr.TemplateRow input:eq(3)").val('Yes');
				$("tr.TemplateRow input:eq(4)").val('Yes');
				if(item.is_pending == 'Yes') {
					$("tr.TemplateRow input:eq(3)").attr("checked", "checked");
				}
				if(item.is_compulsory == 'Yes') {
					$("tr.TemplateRow input:eq(4)").attr("checked", "checked");
				}
				$("tr.TemplateRow .AddButton").click();
			});
		}
	});
}

function loadTariff(id) {
	$.ajax({
		type: "GET",
		url: '<?php echo site_url($this->_clspath.$this->_class."/ajaxTariff") ?>/' + id,
		dataType: "json",
		success: function(data) {
			var index = 1;
			$('#TariffChart').empty();
			$.each(data, function(i, item) {
				$("#TariffType").val(item.tariff_type);
				$('#TariffChart').append('<tr>' +
					'<td><input type="text" class="form-control form-control-sm Numeric" name="from_day[' + item.id + ']" value="' + item.from_day + '" /></td>' +
					'<td><input type="text" class="form-control form-control-sm Numeric" name="to_day[' + item.id + ']" value="' + item.to_day + '" /></td>' +
					'<td><input type="text" class="form-control form-control-sm Numeric" name="price_20[' + item.id + ']" value="' + item.price_20 + '" /></td>' +
					'<td><input type="text" class="form-control form-control-sm Numeric" name="price_40[' + item.id + ']" value="' + item.price_40 + '" /></td>' +
					'<td class="aligncenter"><input type="checkbox" name="tariff_delete_id[' + item.id + ']" value="' + item.id + '" class="DeleteCheckbox" /></td>' +
				'</tr>');
				index++;
			});

			while(index < 10) {
				index++;
				$('#TariffChart').append('<tr>' +
					'<td><input type="text" class="form-control form-control-sm Numeric" name="new_from_day[]" value="" /></td>' +
					'<td><input type="text" class="form-control form-control-sm Numeric" name="new_to_day[]" value="" /></td>' +
					'<td><input type="text" class="form-control form-control-sm Numeric" name="new_price_20[]" value="" /></td>' +
					'<td><input type="text" class="form-control form-control-sm Numeric" name="new_price_40[]" value="" /></td>' +
					'<td></td>' +
				'</tr>');
			}
			$("#AgentRateID").val(id);
			$("#modal-tariff").modal();
		}
	});
}

$(document).ready(function() {
	$("#AddTariff").on('click', function() {
		$("form#MainForm").submit();
	});

	$("#AgentName").autocomplete({
		source: '<?php echo site_url($this->_clspath.$this->_class.'/ajax') ?>',
		minLength: 1,
		focus: function(event, ui) {
			$(this).prevAll('input').val(ui.item.id);
			$(this).val( ui.item.name);
			return false;
		},
		select: function(event, ui) {
			$(this).prevAll('input').val(ui.item.id);
			$(this).val(ui.item.name);
			return false;
		},
		response: function(event, ui) {
			if (ui.content.length === 0) {
				$(this).prevAll('input').val(0);
				$(this).val('');
			}
		}
	})
	.data('ui-autocomplete')._renderItem = function(ul, item) {
		return $('<li></li>')
			.data('item.autocomplete', item)
			.append('<a><span class="tiny">' + item.name + ' <span class="orange">' + item.type + '</span></span></a>')
			.appendTo(ul);
	};

	$("#NewAgentName").autocomplete({
		appendTo: '#modal-copy',
		source: '<?php echo site_url($this->_clspath.$this->_class.'/ajax') ?>',
		minLength: 1,
		focus: function(event, ui) {
			$(this).prevAll('input').val(ui.item.id);
			$(this).val( ui.item.name);
			return false;
		},
		select: function(event, ui) {
			$(this).prevAll('input').val(ui.item.id);
			$(this).val(ui.item.name);
			return false;
		},
		response: function(event, ui) {
			if (ui.content.length === 0) {
				$(this).prevAll('input').val(0);
				$(this).val('');
			}
		}
	})
	.data('ui-autocomplete')._renderItem = function(ul, item) {
		return $('<li></li>')
			.data('item.autocomplete', item)
			.append('<a><span class="tiny">' + item.name + ' <span class="orange">' + item.type + '</span></span></a>')
			.appendTo(ul);
	};

	$('.Particulars').autocomplete({
		source: '<?php echo site_url($this->_clspath.$this->_class.'/ajaxJson/agent_rates/particulars') ?>',
		minLength: 1
	});

	$("#ajaxAgentRate").autocomplete({
		appendTo: '#modal-copy',
		source: "<?php echo site_url($this->_clspath.$this->_class.'/ajaxAgentRates/') ?>",
		minLength: 1,
		focus: function(event, ui) {
			$(this).prevAll('input').val(ui.item.id);
			$(this).val(ui.item.name);
			return false;
		},
		select: function(event, ui) {
			$(this).prevAll('input').val(ui.item.id);
			$(this).val(ui.item.name);
			return false;
		}
	})
	.data('ui-autocomplete')._renderItem = function(ul, item) {
		return $('<li></li>')
			.data('item.autocomplete', item)
			.append('<a><span class="tiny">' + item.name + ' <span class="orange">' + item.port_name + '</span></span></a>')
			.appendTo(ul);
	};
});
</script>