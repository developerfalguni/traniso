<?php
echo form_open($this->uri->uri_string(), 'id="MainForm"');
echo form_hidden($id);
?>

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
			<div class="form-group">
				<label class="control-label">Ledger Name</label>
				<div class="form-group<?php echo (strlen(form_error('ledger_id')) > 0 ? ' has-error' : '') ?>">
					<input type="hidden" name="ledger_id" value="<?php echo $row['ledger_id'] ?>" id="LedgerID" />
					<input type="text" class="form-control form-control-sm" value="<?php echo $row['ledger_name'] ?>" id="LedgerName" />
				</div>
			</div>

			<div class="card card-default">
				<div class="card-header">
					<h3 class="card-title">Rates</h3>
				</div>
				
				<!-- <div class="card-body"></div> -->
			
				<table class="table table-condensed table-striped DataEntry">
				<thead>
				<tr>
					<th>From Location</th>
					<th>To Location</th>
					<th>Product</th>
					<th width="80px">Price 20</th>
					<th width="80px">Price 40</th>
					<th width="80px">Price</th>
					<th width="80px">Weight</th>
					<th width="160px">Wef</th>
					<th width="24px" class="aligncenter"><a href="#" class="CheckAll"><i class="icon-trashcan"></i></a></th>
				</tr>
				</thead>

				<tbody>
				<?php
					foreach ($row['rates'] as $r) {
						echo '<tr>
						<td><input type="hidden" class="FromLocationID" name="from_location_id[' . $r['id'] . ']" value="' . $r['from_location_id'] . '" />
							<input type="text" class="form-control form-control-sm FromLocation" value="' . $r['from_location'] . '" /></td>
						<td><input type="hidden" class="ToLocationID" name="to_location_id[' . $r['id'] . ']" value="' . $r['to_location_id'] . '" />
							<input type="text" class="form-control form-control-sm ToLocation" value="' . $r['to_location'] . '" /></td>
						<td><input type="hidden" class="ProductID" name="product_id[' . $r['id'] . ']" value="' . $r['product_id'] . '" />
							<input type="text" class="form-control form-control-sm ProductName" value="' . $r['product_name'] . '" /></td>
						<td><input type="text" class="form-control form-control-sm Numeric" name="price_20[' . $r['id'] . ']" value="' . $r['price_20'] . '" /></td>
						<td><input type="text" class="form-control form-control-sm Numeric" name="price_40[' . $r['id'] . ']" value="' . $r['price_40'] . '" /></td>
						<td><input type="text" class="form-control form-control-sm Numeric" name="price[' . $r['id'] . ']" value="' . $r['price'] . '" /></td>
						<td><input type="text" class="form-control form-control-sm Numeric" name="weight[' . $r['id'] . ']" value="' . $r['weight'] . '" /></td>
						<td><div class="input-group date DatePicker"><span class="input-group-addon"><i class="icon-calendar"></i></span><input type="text" class="form-control form-control-sm AutoDate" name="wef_date[' . $r['id'] . ']" value="' . $r['wef_date'] . '" /></div></td>
						<td class="aligncenter">' . form_checkbox(array('name' => 'delete_id['.$r['id'].']', 'value' => $r['id'], 'checked' => false, 'class' => 'DeleteCheckbox')) . '</td>
					</tr>';
					}
				?>

				<tr class="TemplateRow">
					<td><input type="hidden" class="FromLocationID" name="new_from_location_id[]" value="" />
						<input type="text" class="form-control form-control-sm FromLocation Validate Focus" value="" /></td>
					<td><input type="hidden" class="ToLocationID" name="new_to_location_id[]" value="" />
						<input type="text" class="form-control form-control-sm ToLocation Validate" value="" /></td>
					<td><input type="hidden" class="ProductID" class="form-control form-control-sm Unchanged" name="new_product_id[]" value="" />
						<input type="text" class="form-control form-control-sm ProductName Unchanged" value="" /></td>
					<td><input type="text" class="form-control form-control-sm Numeric" name="new_price_20[]" value="" /></td>
					<td><input type="text" class="form-control form-control-sm Numeric" name="new_price_40[]" value="" /></td>
					<td><input type="text" class="form-control form-control-sm Numeric" name="new_price[]" value="" /></td>
					<td><input type="text" class="form-control form-control-sm Numeric" name="new_weight[]" value="" /></td>
					<td><div class="input-group date DatePicker"><span class="input-group-addon"><i class="icon-calendar"></i></span><input type="text" class="form-control form-control-sm AutoDate Unchanged" name="new_wef_date[]" value="" /></div></td>
					<td><button type="submit" class="btn btn-success btn-sm AddButton"><i class="fa fa-plus"></i></button></td>
				</tr>
				</tbody>
				</table>
			</div>
		</fieldset>
	</div>

	<div class="card-footer">
		<button type="submit" class="btn btn-success" id="Update">Update</button>
	</div>
</div>

</form>

<script>
$(document).ready(function() {
	$("#LedgerName").kaabar_typeahead_complex({
		name: 'tt_name',
		displayKey: 'name',
		url: '<?php echo site_url('/accounting/ledger/ajax') ?>',
		suggestion: '<p>{{name}}</p>',
		fields: [{id: '#LedgerID', field: 'id'}]
	});

	$('.FromLocation').typeahead({
		hint: false,
		highlight: true,
		minLength: 1
	}, {
		name: 'tt_name',
		displayKey: 'name',
		source: function(query, process) {
			return $.ajax({ 
				url: '<?php echo site_url($this->_clspath.$this->_class.'/ajaxLocation') ?>',
				type: 'POST',
				data: { term: query },
				dataType: 'json',
				success: function (result) {
					return process(result);
				}
			});
		},
		templates: {
			empty: ['<div class="tt-no-result">Unable to find any results that match the current query</div>'],
			suggestion: Handlebars.compile('<p>{{name}}</p>')
		}
	}).on('typeahead:selected', function(obj, datum) {
		$row = $(this).parent('span').parent('td').parent('tr');
		$(this).parent('span').parent('td').find('.FromLocationID').val(datum.id);
	});

	$('.ToLocation').typeahead({
		hint: false,
		highlight: true,
		minLength: 1
	}, {
		name: 'tt_name',
		displayKey: 'name',
		source: function(query, process) {
			return $.ajax({ 
				url: '<?php echo site_url($this->_clspath.$this->_class.'/ajaxLocation') ?>',
				type: 'POST',
				data: { term: query },
				dataType: 'json',
				success: function (result) {
					return process(result);
				}
			});
		},
		templates: {
			empty: ['<div class="tt-no-result">Unable to find any results that match the current query</div>'],
			suggestion: Handlebars.compile('<p>{{name}}</p>')
		}
	}).on('typeahead:selected', function(obj, datum) {
		$row = $(this).parent('span').parent('td').parent('tr');
		$(this).parent('span').parent('td').find('.ToLocationID').val(datum.id);
	});

	$('.ProductName').typeahead({
		hint: false,
		highlight: true,
		minLength: 1
	}, {
		name: 'tt_name',
		displayKey: 'name',
		source: function(query, process) {
			return $.ajax({ 
				url: '<?php echo site_url($this->_clspath.$this->_class.'/ajaxProduct') ?>',
				type: 'POST',
				data: { term: query },
				dataType: 'json',
				success: function (result) {
					return process(result);
				}
			});
		},
		templates: {
			empty: ['<div class="tt-no-result">Unable to find any results that match the current query</div>'],
			suggestion: Handlebars.compile('<p>{{name}}</p>')
		}
	}).on('typeahead:selected', function(obj, datum) {
		$row = $(this).parent('span').parent('td').parent('tr');
		$(this).parent('span').parent('td').find('.ProductID').val(datum.id);
	});

});
</script>
