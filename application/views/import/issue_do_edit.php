<?php
echo form_open($this->uri->uri_string(), 'id="MainForm"');
echo form_hidden($id);
echo form_hidden($job_id);
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
		<div class="row">
			<div class="col-md-2">
				<div class="form-group<?php echo (strlen(form_error('date')) > 0 ? ' has-error' : '') ?>">
					<label class="control-label">Date</label>
					<div class="input-group date DatePicker">
						<span class="input-group-addon"><i class="icon-calendar"></i></span>
						<input type="text" class="form-control form-control-sm Focus" name="date" value="<?php echo ($row['date'] == '00-00-0000' ? '' : $row['date']) ?>" id="Date" />
					</div>
				</div>
			</div>

			<div class="col-md-2">
				<div class="form-group">
					<label class="control-label">DO No</label>
					<input type="text" class="form-control form-control-sm" name="id2_format" value="<?php echo $row['id2_format'] ?>" readonly="readonly"/>
				</div>
			</div>

			<div class="col-md-2">
				<div class="form-group">
					<label class="control-label">Qty Delivered</label>
					<input type="text" class="form-control form-control-sm Numeric" name="qty_delivered" value="<?php echo $row['qty_delivered'] ?>" />
				</div>
			</div>
		</div>

		<div class="form-group">
			<label class="control-label">Remarks</label>
			<input type="text" class="form-control form-control-sm" name="remarks" value="<?php echo $row['remarks'] ?>" />
		</div>
	</div>

	<div class="card-footer">
		<button type="button" class="btn btn-success" id="Update">Update</button>
		<?php if($row['id'] > 0) {
			echo anchor($this->_clspath.$this->_class."/pdf/".$job_id['job_id'].'/'.$row['id'], '<i class="icon-file-pdf"></i> PDF', 'class="btn btn-default Popup"');
		}
		?>
	</div>
</div>

</form>

<script>
$(document).ready(function() {
	// $("#TOI").kaabar_typeahead({
	// 	name: 'tt_toi',
	// 	displayKey: 'toi',
	// 	url: '<?php echo site_url($this->_clspath.$this->_class.'/getJSON/job_invoices/toi') ?>',
	// 	suggestion: '<p>{{toi}}</p>',
	// });

	// $(".HSCode").kaabar_typeahead({
	// 	name: 'tt_hs_code',
	// 	displayKey: 'hs_code',
	// 	url: '<?php echo site_url($this->_clspath.$this->_class.'/getJSON/job_invoice_products/hs_code') ?>',
	// 	suggestion: '<p>{{hs_code}}</p>',
	// });

	// $(".Unit").kaabar_typeahead({
	// 	name: 'tt_quantity_unit',
	// 	displayKey: 'quantity_unit',
	// 	url: '<?php echo site_url($this->_clspath.$this->_class.'/getJSON/job_invoice_products/quantity_unit') ?>',
	// 	suggestion: '<p>{{quantity_unit}}</p>',
	// });

	// $(".Description").kaabar_typeahead({
	// 	name: 'tt_description',
	// 	displayKey: 'description',
	// 	url: '<?php echo site_url($this->_clspath.$this->_class.'/getJSON/job_invoice_products/description') ?>',
	// 	suggestion: '<p>{{description}}</p>',
	// });
});
</script>