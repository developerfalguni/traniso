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
		<div class="row">
			<div class="col-md-8">
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label class="control-label">Silica Gel Vendor</label>
							<input type="text" class="form-control form-control-sm" name="silica_gel_vendor" value="<?php echo $row['silica_gel_vendor'] ?>" id="SilicaGelVendor" />
						</div>
					</div>

					<div class="col-md-6">
						<div class="form-group">
							<label class="control-label">Silica Gel</label>
							<input type="text" class="form-control form-control-sm Numeric" name="silica_gel" value="<?php echo $row['silica_gel'] ?>" />
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label class="control-label">Craft Paper Vendor</label>
							<input type="text" class="form-control form-control-sm" name="craft_paper_vendor" value="<?php echo $row['craft_paper_vendor'] ?>" id="CraftPaperVendor" />
						</div>
					</div>

					<div class="col-md-6">
						<div class="form-group">
							<label class="control-label">Craft Paper Sides</label>
							<?php
							 echo form_dropdown('craft_paper_sides[]', getEnumSetOptions('job_expenses', 'craft_paper_sides'), explode(',', $row['craft_paper_sides']), 'multiple class="form-control form-control-sm Selectize"') ?>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label class="control-label">Wood Plank Vendor</label>
							<input type="text" class="form-control form-control-sm" name="wood_plank_vendor" value="<?php echo $row['wood_plank_vendor'] ?>" id="WoodPlankVendor" />
						</div>
					</div>

					<div class="col-md-3">
						<div class="form-group">
							<label class="control-label">Wood Plank</label>
							<input type="text" class="form-control form-control-sm Numeric" name="wood_plank" value="<?php echo $row['wood_plank'] ?>" />
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-3">
						<div class="form-group">
							<label class="control-label">Door Net</label>
							<?php echo form_dropdown('door_net', getEnumSetOptions('job_expenses', 'door_net'), $row['door_net'], 'class="form-control form-control-sm"'); ?>
						</div>
					</div>

					<div class="col-md-3">
						<div class="form-group">
							<label class="control-label">Lashing</label>
							<?php echo form_dropdown('lashing', getEnumSetOptions('job_expenses', 'lashing'), $row['lashing'], 'class="form-control form-control-sm"'); ?>
						</div>
					</div>

					<div class="col-md-3">
						<div class="form-group">
							<label class="control-label">Choking</label>
							<?php echo form_dropdown('choking', getEnumSetOptions('job_expenses', 'choking'), $row['choking'], 'class="form-control form-control-sm"'); ?>
						</div>
					</div>

					<div class="col-md-3">
						<div class="form-group">
							<label class="control-label">Rebagging</label>
							<?php echo form_dropdown('rebagging', getEnumSetOptions('job_expenses', 'rebagging'), $row['rebagging'], 'class="form-control form-control-sm"'); ?>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label class="control-label">Fumigation Vendor</label>
							<input type="text" class="form-control form-control-sm" name="fumigation_vendor" value="<?php echo $row['fumigation_vendor'] ?>" id="FumigationVendor" />
						</div>
					</div>

					<div class="col-md-3">
						<div class="form-group">
							<label class="control-label">Fumigation Dose</label>
							<input type="text" class="form-control form-control-sm Numeric" name="fumigation_dose" value="<?php echo $row['fumigation_dose'] ?>" />
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label class="control-label">Fumigant</label>
							<?php echo form_dropdown('fumigant', getEnumSetOptions('job_expenses', 'fumigant'), $row['fumigant'], 'class="form-control form-control-sm"'); ?>
						</div>
					</div>

					<div class="col-md-3">
						<div class="form-group">
							<label class="control-label">Fumigation Dose Unit</label>
							<?php echo form_dropdown('fumigation_dose_unit', getEnumSetOptions('job_expenses', 'fumigation_dose_unit'), $row['fumigation_dose_unit'], 'class="form-control form-control-sm"'); ?>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-3">
						<div class="form-group">
							<label class="control-label">COO Issuing Authority</label>
							<?php echo form_dropdown('coo_issuing_authority', getEnumSetOptions('job_expenses', 'coo_issuing_authority'), $row['coo_issuing_authority'], 'class="form-control form-control-sm"'); ?>
						</div>
					</div>

					<div class="col-md-3">
						<div class="form-group">
							<label class="control-label">EIA Cert. Type</label>
							<?php echo form_dropdown('eia_type', getEnumSetOptions('job_expenses', 'eia_type'), $row['eia_type'], 'class="form-control form-control-sm"'); ?>
						</div>
					</div>

					<div class="col-md-3">
						<div class="form-group">
							<label class="control-label">EIA Cert.</label>
							<?php echo form_dropdown('eia_certificate', getEnumSetOptions('job_expenses', 'eia_certificate'), $row['eia_certificate'], 'class="form-control form-control-sm"'); ?>
						</div>
					</div>

					<div class="col-md-3">
						<div class="form-group">
							<label class="control-label">Phytosanitary Issuing Authority</label>
							<?php echo form_dropdown('phytosanitary_issuing_authority', getEnumSetOptions('job_expenses', 'phytosanitary_issuing_authority'), $row['phytosanitary_issuing_authority'], 'class="form-control form-control-sm"'); ?>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-3">
						<div class="form-group">
							<label class="control-label">NonGMO</label>
							<?php echo form_dropdown('non_gmo', getEnumSetOptions('job_expenses', 'non_gmo'), $row['non_gmo'], 'class="form-control form-control-sm"'); ?>
						</div>
					</div>

					<div class="col-md-3">
						<div class="form-group">
							<label class="control-label">Health Cert.</label>
							<?php echo form_dropdown('health_certificate', getEnumSetOptions('job_expenses', 'health_certificate'), $row['health_certificate'], 'class="form-control form-control-sm"'); ?>
						</div>
					</div>

					<div class="col-md-3">
						<div class="form-group">
							<label class="control-label">Legalization of Certificates</label>
							<?php echo form_dropdown('legalization_of_certificates', getEnumSetOptions('job_expenses', 'legalization_of_certificates'), $row['legalization_of_certificates'], 'class="form-control form-control-sm"'); ?>
						</div>
					</div>

					<div class="col-md-3">
						<div class="form-group">
							<label class="control-label">Third Party Surveyor</label>
							<?php echo form_dropdown('third_party_surveyor', getEnumSetOptions('job_expenses', 'third_party_surveyor'), $row['third_party_surveyor'], 'class="form-control form-control-sm"'); ?>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-3">
						<div class="form-group">
							<label class="control-label">Special Weightment</label>
							<?php echo form_dropdown('special_weightment', getEnumSetOptions('job_expenses', 'special_weightment'), $row['special_weightment'], 'class="form-control form-control-sm"'); ?>
						</div>
					</div>

					<div class="col-md-3">
						<div class="form-group">
							<label class="control-label">Empty Lift On</label>
							<?php echo form_dropdown('empty_lift_on', getEnumSetOptions('job_expenses', 'empty_lift_on'), $row['empty_lift_on'], 'class="form-control form-control-sm"'); ?>
						</div>
					</div>
				</div>
			</div>

			<div class="col-md-4">
				<div class="card card-default">
					<div class="card-header">
						<h3 class="card-title">Vouchers Found</h3>
					</div>
				
					<table class="table table-condensed table-striped">
					<thead>
					<tr>
						<th width="70px">Voucher</th>
						<th width="70px">Bill No</th>
						<th>Particular</th>
						<th width="80px">Amount</th>
					</tr>
					</thead>

					<tbody class="tiny">
						<?php 
							$total = 0;
							foreach ($all_vouchers as $v) {
								$total = bcadd($total, $v['amount'], 0);
								echo '<tr>
						<td class="alignmiddle">' . anchor('/accounting/' . underscore($v['url']), $v['id2_format'], 'target="_blank"') . '</td>
						<td class="alignmiddle">' . $v['invoice_no'] . '</td>
						<td class="alignmiddle">' . $v['name'] . '</td>
						<td class="alignright">' . inr_format($v['amount'], 2) . '</td>
					</tr>';
							}
						?>
					</tbody>

					<tfoot>
					<tr>
						<th colspan="3" class="alignright">Total</th>
						<th><?php echo inr_format($total) ?></th>
					</tr>
					</tfoot>
					</table>
				</div>


				<div class="card card-default">
					<div class="card-header">
						<h3 class="card-title">Transportation Expenses</h3>
					</div>
				
					<table class="table table-condensed table-striped">
					<tbody class="tiny">
						<?php 
							$total = 0;
							foreach ($transportation as $f => $v) {
								echo '<tr>
						<td class="alignmiddle">' . humanize($f) . '</td>
						<td class="alignright">' . inr_format($v, 2) . '</td>
					</tr>';
							}
						?>
					</tbody>
					</table>
				</div>


				<div class="card card-default">
					<div class="card-header">
						<h3 class="card-title">Vouchers By Group</h3>
					</div>
				
					<table class="table table-condensed table-striped">
					<thead>
					<tr>
						<th width="70px">Voucher</th>
						<th>Particular</th>
						<th width="80px">Amount</th>
					</tr>
					</thead>

					<tbody class="tiny">
						<?php 
							foreach ($vouchers['vouchers'] as $v) {
								echo '<tr>
						<td class="alignmiddle">' . anchor('/accounting/' . underscore($v['url']), $v['id2_format'], 'target="_blank"') . '</td>
						<td class="alignmiddle">' . $v['name'] . '</td>
						<td class="alignright">';
							if (! isset($vouchers['bills'][$v['code']]))
								echo '<span class="red">' . inr_format($v['amount'], 2) . '</span>';
							else if ($v['amount'] <= $vouchers['bills'][$v['code']]['amount'])
								echo anchor('/accounting/' . underscore($vouchers['bills'][$v['code']]['url']), '<span class="green">' . inr_format($v['amount'], 2) . '</span>', 'target="_blank"');
							else
								echo anchor('/accounting/' . underscore($vouchers['bills'][$v['code']]['url']), '<span class="orange">' . inr_format($v['amount'], 2) . '</span>', 'target="_blank"');
						echo '</td>
					</tr>';
							}

							echo '<tr><td colspan="4">&nbsp;</td></tr>';
						?>
					</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

	<div class="card-footer">
		<button type="submit" class="btn btn-success" id="Update">Update</button>
	</div>
</div>

</form>

<script>
$(document).ready(function() {
	$('#SilicaGelVendor').autocomplete({
		source: '<?php echo site_url($this->_clspath.$this->_class.'/ajaxJson/job_expenses/silica_gel_vendor') ?>',
		minLength: 0
	});
	$('#CraftPaperVendor').autocomplete({
		source: '<?php echo site_url($this->_clspath.$this->_class.'/ajaxJson/job_expenses/craft_paper_vendor') ?>',
		minLength: 0
	});
	$('#WoodPlankVendor').autocomplete({
		source: '<?php echo site_url($this->_clspath.$this->_class.'/ajaxJson/job_expenses/wood_plank_vendor') ?>',
		minLength: 0
	});
	$('#FumigationVendor').autocomplete({
		source: '<?php echo site_url($this->_clspath.$this->_class.'/ajaxJson/job_expenses/fumigation_vendor') ?>',
		minLength: 0
	});
	$('#Fumigant').autocomplete({
		source: '<?php echo site_url($this->_clspath.$this->_class.'/ajaxFumigant') ?>',
		minLength: 0
	});
});
</script>