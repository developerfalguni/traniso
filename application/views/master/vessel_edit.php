
<div id="modal-delete" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><i class="icon-times"></i></button>
				<h3>Confirm Deletion</h3>
			</div>
			<div class="modal-body"><p>Are you sure, you want to DELETE...?</p></div>
			<div class="modal-footer">
			<?php echo anchor($this->_clspath.$this->_class."/delete/".$id['id'], 'Delete', 'class="btn btn-danger"') ?>
			</div>
		</div>
	</div>
</div>


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
			<div class="col-md-2">
				<div class="form-group">
					<label class="control-label">Service</label>
					<div class="form-group<?php echo (strlen(form_error('service_id')) > 0 ? ' has-error' : '') ?>">
						<?php echo form_dropdown('service_id', array(0=>'')+getSelectOptions('services', 'id', 'code'), $row['service_id'], 'class="form-control form-control-sm" id="ServiceID"') ?>
					</div>
				</div>
			</div>

			<div class="col-md-10">
				<div class="form-group">
					<label class="control-label">Vessel Agent</label>
					<div class="form-group<?php echo (strlen(form_error('agent_id')) > 0 ? ' has-error' : '') ?>">
						<?php echo form_dropdown('agent_id', array(0=>'')+getSelectOptions('agents', 'id', 'name'), $row['agent_id'], 'class="form-control form-control-sm" id="VesselAgent"') ?>
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-2">
				<div class="form-group<?php echo (strlen(form_error('type')) > 0 ? ' has-error' : '') ?>">
					<label class="control-label">Vessel Type</label>
					<?php echo form_dropdown('type', $this->office->getVesselTypes(), $row['type'], 'class="form-control form-control-sm" id="Type"') ?>
				</div>
			</div>

			<div class="col-md-2">
				<div class="form-group">
					<label class="control-label">Prefix</label>
					<?php echo form_dropdown('prefix', $this->office->getVesselPrefix(), $row['prefix'], 'class="form-control form-control-sm" id="Prefix"') ?>
				</div>
			</div>

			<div class="col-md-4">
				<div class="form-group<?php echo (strlen(form_error('name')) > 0 ? ' has-error' : '') ?>">
					<label class="control-label">Name</label>
					<input type="text" class="form-control form-control-sm big col-md-12" name="name" value="<?php echo $row['name'] ?>" />
				</div>
			</div>

			<div class="col-md-2">
				<div class="form-group<?php echo (strlen(form_error('voyage_no')) > 0 ? ' has-error' : '') ?>">
					<label class="control-label">Voyage No</label>
					<input type="text" class="form-control form-control-sm big col-md-12" name="voyage_no" value="<?php echo $row['voyage_no'] ?>" />
				</div>
			</div>

			<div class="col-md-2">
				<div class="form-group<?php echo (strlen(form_error('imo_no')) > 0 ? ' has-error' : '') ?>">
					<label class="control-label">IMO No</label>
					<input type="text" class="form-control form-control-sm big col-md-12" name="imo_no" value="<?php echo $row['imo_no'] ?>" />
				</div>
			</div>
		</div>
		
		<div class="row">
			<div class="col-md-4">
				<div class="form-group<?php echo (strlen(form_error('indian_port_id')) > 0 ? ' has-error' : '') ?>">
					<label class="control-label">Arrival Port</label>
					<?php echo form_dropdown('indian_port_id', getSelectOptions('indian_ports', 'id', 'name'), $row['indian_port_id'], 'class="form-control form-control-sm"') ?>
				</div>
			</div>

			<div class="col-md-2">
				<div class="form-group<?php echo (strlen(form_error('berth_no')) > 0 ? ' has-error' : '') ?>">
					<label class="control-label">Berth No</label>
					<?php echo form_dropdown('berth_no', $this->office->getBerthNo(), $row['berth_no'], 'class="form-control form-control-sm" id="BerthNo"') ?>
				</div>
			</div>

			<div class="col-md-4">
				<div class="form-group">
					<label class="control-label">Berthing Terminal</label>
					<?php echo form_dropdown('terminal_id', array(0=>'-')+getSelectOptions('terminals', 'id', 'code'), $row['terminal_id'], 'class="form-control form-control-sm" id="TerminalID"') ?>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-2">
				<div class="form-group">
					<label class="control-label">Total BL Quantity</label>
					<input type="text" class="form-control form-control-sm Numeric" name="total_bl_quantity" value="<?php echo $row['total_bl_quantity'] ?>" readonly="true" />
				</div>
			</div>
			
			<div class="col-md-2">
				<div class="form-group">
					<label class="control-label">Draft Survey Quantity</label>
					<input type="text" class="form-control form-control-sm Numeric" name="draft_survey_quantity" value="<?php echo $row['draft_survey_quantity'] ?>" />
				</div>
			</div>

			<div class="col-md-2">
				<div class="form-group<?php echo (strlen(form_error('import_exchange_rate')) > 0 ? ' has-error' : '') ?>">
					<label class="control-label">Import Ex. Rate</label>
					<input type="text" class="form-control form-control-sm Numeric" name="import_exchange_rate" value="<?php echo $row['import_exchange_rate'] ?>" />
				</div>
			</div>

			<div class="col-md-2">
				<div class="form-group<?php echo (strlen(form_error('export_exchange_rate')) > 0 ? ' has-error' : '') ?>">
					<label class="control-label">Export Ex. Rate</label>
					<input type="text" class="form-control form-control-sm Numeric" name="export_exchange_rate" value="<?php echo $row['export_exchange_rate'] ?>" />
				</div>
			</div>

			<div class="col-md-2">
				<div class="form-group<?php echo (strlen(form_error('vcn_no')) > 0 ? ' has-error' : '') ?>">
					<label class="control-label">VCN No</label>
					<input type="text" class="form-control form-control-sm big col-md-12" name="vcn_no" value="<?php echo $row['vcn_no'] ?>" />
				</div>
			</div>

			<div class="col-md-2">
				<div class="form-group<?php echo (strlen(form_error('rotation_no')) > 0 ? ' has-error' : '') ?>">
					<label class="control-label">Rotation No</label>
					<input type="text" class="form-control form-control-sm big col-md-12" name="rotation_no" value="<?php echo $row['rotation_no'] ?>" />
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-3">
				<div class="form-group<?php echo (strlen(form_error('igm_no')) > 0 ? ' has-error' : '') ?>">
					<label class="control-label">IGM No</label>
					<input type="text" class="form-control form-control-sm" name="igm_no" value="<?php echo $row['igm_no'] ?>" />
				</div>
			</div>

			<div class="col-md-3">
				<div class="form-group<?php echo (strlen(form_error('igm_date')) > 0 ? ' has-error' : '') ?>">
					<label class="control-label">IGM Date</label>
					<div class="input-group input-group-sm">
						<input type="text" class="form-control form-control-sm DatePicker" name="igm_date" value="<?php echo $row['igm_date']; ?>">
						<div class="input-group-append">
							<div class="input-group-text"><i class="icon-calendar"></i></div>
						</div>
					</div>
				</div>
			</div>

			<div class="col-md-3">
				<div class="form-group<?php echo (strlen(form_error('egm_no')) > 0 ? ' has-error' : '') ?>">
					<label class="control-label">EGM No</label>
					<input type="text" class="form-control form-control-sm" name="egm_no" value="<?php echo $row['egm_no'] ?>" />
				</div>
			</div>

			<div class="col-md-3">
				<div class="form-group<?php echo (strlen(form_error('egm_date')) > 0 ? ' has-error' : '') ?>">
					<label class="control-label">EGM Date</label>
					<div class="input-group input-group-sm">
						<input type="text" class="form-control form-control-sm DatePicker" name="egm_date" value="<?php echo $row['egm_date']; ?>">
						<div class="input-group-append">
							<div class="input-group-text"><i class="icon-calendar"></i></div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-6">
				<table class="table table-condensed table-striped table-bordered">
				<thead>
				<tr>
					<th>ETA</th>
					<th>ETD</th>
				</tr>
				</thead>

				<tbody>
				<?php 
					// foreach ($dates as $d) {
					// 	echo '<tr>
					// 		<td>' . $d['eta_date'] . '</td>
					// 		<td>' . $d['etd_date'] . '</td>
					// 	</tr>';
					// }
				?>
				<tr>
					<td>
						<div class="input-group input-group-sm">
							<input type="text" class="form-control form-control-sm DatePicker" name="eta_date" value="<?php echo $row['eta_date']; ?>">
							<div class="input-group-append">
								<div class="input-group-text">
									<i class="icon-calendar"></i>
								</div>
							</div>
						</div>
					</td>
					<td>
						<div class="input-group input-group-sm">
							<input type="text" class="form-control form-control-sm DatePicker" name="etd_date" value="<?php echo $row['etd_date']; ?>">
							<div class="input-group-append">
								<div class="input-group-text">
									<i class="icon-calendar"></i>
								</div>
							</div>
						</div>
					</td>
				</tr>
				</tbody>
				</table>
			</div>

			<div class="col-md-6">
				<div class="row">
					<div class="col-md-4">
						<div class="form-group<?php echo (strlen(form_error('gld_date')) > 0 ? ' has-error' : '') ?>">
							<label class="control-label">GLD Date</label>
							<div class="input-group input-group-sm">
								<input type="text" class="form-control form-control-sm DatePicker" name="gld_date" value="<?php echo $row['gld_date']; ?>">
								<div class="input-group-append">
									<div class="input-group-text"><i class="icon-calendar"></i></div>
								</div>
							</div>
						</div>
					</div>

					<div class="col-md-4">
						<div class="form-group<?php echo (strlen(form_error('berthing_date')) > 0 ? ' has-error' : '') ?>">
							<label class="control-label">Berthing Date</label>
							<div class="input-group input-group-sm">
								<input type="text" class="form-control form-control-sm DatePicker" name="berthing_date" value="<?php echo $row['berthing_date']; ?>">
								<div class="input-group-append">
									<div class="input-group-text"><i class="icon-calendar"></i></div>
								</div>
							</div>
						</div>
					</div>
					
					<div class="col-md-4">
						<div class="form-group<?php echo (strlen(form_error('barging_date')) > 0 ? ' has-error' : '') ?>">
							<label class="control-label">Barging Date</label>
							<div class="input-group input-group-sm">
								<input type="text" class="form-control form-control-sm DatePicker" name="barging_date" value="<?php echo $row['barging_date']; ?>">
								<div class="input-group-append">
									<div class="input-group-text"><i class="icon-calendar"></i></div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-4">
						<div class="form-group<?php echo (strlen(form_error('sailing_date')) > 0 ? ' has-error' : '') ?>">
							<label class="control-label">Sailing Date</label>
							<div class="input-group input-group-sm">
								<input type="text" class="form-control form-control-sm DatePicker" name="sailing_date" value="<?php echo $row['sailing_date']; ?>">
								<div class="input-group-append">
									<div class="input-group-text"><i class="icon-calendar"></i></div>
								</div>
							</div>
						</div>
					</div>

					<div class="col-md-4">
						<div class="form-group<?php echo (strlen(form_error('pgr_begin_date')) > 0 ? ' has-error' : '') ?>">
							<label class="control-label">Port Ground Rent Begin</label>
							<div class="input-group input-group-sm">
								<input type="text" class="form-control form-control-sm DatePicker" name="pgr_begin_date" value="<?php echo $row['pgr_begin_date']; ?>">
								<div class="input-group-append">
									<div class="input-group-text"><i class="icon-calendar"></i></div>
								</div>
							</div>
						</div>
					</div>
				
					<div class="col-md-4">
						<div class="form-group<?php echo (strlen(form_error('doc_cutoff_date')) > 0 ? ' has-error' : '') ?>">
							<label class="control-label">Doc Cutoff Date</label>
							<div class="input-group input-group-sm">
								<input type="text" class="form-control form-control-sm DatePicker" name="doc_cutoff_date" value="<?php echo $row['doc_cutoff_date']; ?>">
								<div class="input-group-append">
									<div class="input-group-text"><i class="icon-calendar"></i></div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-4">
						<div class="form-group<?php echo (strlen(form_error('gate_cutoff_date')) > 0 ? ' has-error' : '') ?>">
							<label class="control-label">Gate Cutoff Date</label>
							<div class="input-group input-group-sm">
								<input type="text" class="form-control form-control-sm DatePicker" name="gate_cutoff_date" value="<?php echo $row['gate_cutoff_date']; ?>">
								<div class="input-group-append">
									<div class="input-group-text"><i class="icon-calendar"></i></div>
								</div>
							</div>
						</div>
					</div>
					
					<div class="col-md-4">
						<div class="form-group<?php echo (strlen(form_error('berthing_date')) > 0 ? ' has-error' : '') ?>">
							<label class="control-label">ENS Cutoff Date</label>
							<div class="input-group input-group-sm">
								<input type="text" class="form-control form-control-sm DatePicker" name="berthing_date" value="<?php echo $row['berthing_date']; ?>">
								<div class="input-group-append">
									<div class="input-group-text"><i class="icon-calendar"></i></div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="form-group">
			<label class="control-label">Remarks</label>
			<textarea class="form-control form-control-sm" name="remarks" rows="1"><?php echo $row['remarks'] ?></textarea>
		</div>
	
	<?php if ($id['id'] > 0) : ?>
		<table class="table table-condensed table-striped DataEntry">
		<thead>
		<tr>
			<th width="120px">Code</th>
			<th>Name</th>
			<th>Accounting Head</th>
			<th width="120px">Opening Balance</th>
			<th width="80px">Dr / Cr</th>
			<th width="24px" class="aligncenter"><a href="#" class="CheckAll"><i class="icon-trashcan"></i></a></th>
		</tr>
		</thead>

		<tbody>
		<?php
			foreach ($ledgers as $vl) {
				echo '<tr>
			<td><input type="hidden" class="form-control form-control-sm VLID" name="vlid[' . $vl['id'] . ']" value="' . $vl['id'] . '" />
				<input type="text" class="form-control form-control-sm VLCode" name="vcode[' . $vl['id'] . ']" value="' . $vl['code'] . '" /></td>
			<td><input type="text" class="form-control form-control-sm VLName" name="vname[' . $vl['id'] . ']" value="' . $vl['name'] . '" /></td>
			<td>' . form_dropdown("vagid[" . $vl['id'] . "]", getSelectOptions("account_groups", "id", "name"), $vl['account_group_id'], 'class="form-control form-control-sm"') . '</td>
			<td><input type="text" class="form-control form-control-sm Numeric VLOPBal" name="vopbal[' . $vl['id'] . ']" value="' . $vl['opening_balance'] . '" /></td>
			<td>' . form_dropdown("vdrcr[" . $vl['id'] . "]", getEnumSetOptions("ledgers", "dr_cr"), ' . $vl["dr_cr"] . ', 'class="form-control form-control-sm VLDRCR"') . '</td>
			<td></td>
		</tr>';
			}
		?>
		
		<tr class="TemplateRow">
			<td><input type="hidden" class="form-control form-control-sm VLID" name="new_vlid[]" value="" />
				<input type="text" class="form-control form-control-sm VLCode Validate Focus" name="new_code[]" value="" /></td>
			<td><input type="text" class="form-control form-control-sm VLName Validate" name="new_name[]" value="<?php echo $row['name'] . ' ' . $row['voyage_no'] ?>" /></td>
			<td><?php echo form_dropdown('new_agid[]', getSelectOptions('account_groups', 'id', 'name'), 202, 'class="form-control form-control-sm"') ?></td>
			<td><input type="text" class="form-control form-control-sm Numeric VLOPBal" name="new_opbal[]" value="0" /></td>
			<td><?php echo form_dropdown('new_drcr[]', getEnumSetOptions('ledgers', 'dr_cr'), 'Dr', 'class="form-control form-control-sm VLDRCR"') ?></td>
			<td><button type="submit" class="btn btn-success btn-sm AddButton"><i class="fa fa-plus"></i></button></td>
		</tr>
		</tbody>
		</table>
	<?php endif; ?>

	</div>

	<div class="card-footer">
		<button type="submit" class="btn btn-success" id="Update">Update</button>
		<a href="#modal-delete" data-toggle="modal" class="btn btn-danger">Delete</a>
	</div>
</div>
</form>

<script>

function onTypeChange(value) {
	if (value === 'Bulk') {
		$('#TerminalID').val('-');
		$('#TerminalID').attr('disabled', true);
		$('#BerthNo').removeAttr('disabled');
	}
	else {
		$('#BerthNo').val('-');
		$('#BerthNo').attr('disabled', true);
		$('#TerminalID').removeAttr('disabled');
	}
}

$(document).ready(function() {
	$('#Type').on('change', function(event) {
		onTypeChange($(this).val());
	});

	onTypeChange('<?php echo $row['type'] ?>');
});
</script>