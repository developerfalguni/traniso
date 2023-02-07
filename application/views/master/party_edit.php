<style>
.MarkYellow { background-color: #FFC; }
</style>

<div id="modal-document" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
		<?php echo form_open_multipart($this->_clspath.$this->_class.'/attach/', 'id="KycForm"'); ?>
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><i class="icon-times"></i></button>
				<h3>Attach KYC Document</h3>
			</div>
			<div class="modal-body">
				<input type="file" name="userfile" />
			</div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-success">Upload</button>
			</div>
		</form>
		</div>
	</div>
</div>

<div id="modal-document-delete" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><i class="icon-times"></i></button>
				<h3>Confirm Deletion</h3>
			</div>
			<div class="modal-body"><p>Are you sure, you want to DELETE...?</p></div>
			<div class="modal-footer">
				<?php echo anchor("#", 'Delete', 'class="btn btn-danger" id="DeleteUrl"') ?>
			</div>
		</div>
	</div>
</div>

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

<div id="modal-site" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
		<?php 
			echo form_open($this->_clspath.$this->_class.'/site/'.$id['id']);
			echo form_hidden('party_id', $id['id']);
		?>
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><i class="icon-times"></i></button>
				<h3>Add Party Address</h3>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-4">
						<div class="form-group">
							<label class="control-label">Site Code</label>
							<input type="text" class="form-control form-control-sm" name="code" value="" />
						</div>
					</div>

					<div class="col-md-8">
						<div class="form-group">
							<label class="control-label">Site Name</label>
							<input type="text" class="form-control form-control-sm" name="name" value="" />
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-success">Create New Site</button>
			</div>
			</form>
		</div>
	</div>
</div>

<?php
$site_link = '';
foreach ($sites as $s)
	$site_link .= '<li>' .  anchor($this->_clspath.$this->_class.'/site/'.$s['party_id'].'/'.$s['id'],  $s['code']) . '</li>';

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
					<div class="col-md-8">
						<div class="form-group">
							<label class="control-label">Name</label>
							<div class="form-group<?php echo (strlen(form_error('name')) > 0 ? ' has-error' : '') ?>">
								<input type="text" class="form-control form-control-sm Focus" name="name" value="<?php echo $row['name'] ?>" id="Name" />
							</div>
						</div>
					</div>

					<div class="col-md-4">
						<div class="form-group">
							<label class="control-label">Traces PAN Name</label>
							<div class="form-group<?php echo (strlen(form_error('traces_name')) > 0 ? ' has-error' : '') ?>">
								<input type="text" class="form-control form-control-sm" value="<?php echo $row['traces_name'] ?>" readonly="true" />
							</div>
						</div>
					</div>
				</div>
			
				<div class="form-group">
					<label class="control-label">Address</label>
					<input type="text" class="form-control form-control-sm" name="address" value="<?php echo $row['address'] ?>" />
				</div>
				
				<div class="form-group<?php echo (strlen(form_error('city_id')) > 0 ? ' has-error' : '') ?>">
					<label class="control-label">City</label>
					<?php echo form_dropdown('city_id', [0=>'--- Select City ---']+$this->kaabar->getCities(), $row['city_id'], 'class="form-control form-control-sm Selectize" data-placeholder="Choose City Name..."'); ?>
				</div>
				
				<div class="row">
					<div class="col-md-6">
						<div class="form-group<?php echo (strlen(form_error('contact')) > 0 ? ' has-error' : '') ?>">
							<label class="control-label">Contact</label>
							<input type="text" class="form-control form-control-sm" name="contact" value="<?php echo $row['contact'] ?>" />
						</div>
					</div>

					<div class="col-md-6">
						<div class="form-group<?php echo (strlen(form_error('fax')) > 0 ? ' has-error' : '') ?>">
							<label class="control-label">Fax</label>
							<input type="text" class="form-control form-control-sm" name="fax" value="<?php echo $row['fax'] ?>" />
						</div>
					</div>
				</div>


				<div class="form-group<?php echo (strlen(form_error('email')) > 0 ? ' has-error' : '') ?>">
					<label class="control-label">Email</label>
					<input type="text" class="form-control form-control-sm" name="email" value="<?php echo $row['email'] ?>" />
				</div>

				<div class="row">
					<div class="col-md-3">
						<div class="form-group">
							<label class="control-label">PAN No</label>
							<div class="input-group">
								<input type="text" class="form-control form-control-sm" name="pan_no" value="<?php echo $row['pan_no'] ?>" />
								<div class="input-group-addon">
									<label><input type="checkbox" name="pan_no_verified" value="1" <?php echo $row['pan_no_verified'] == 'Yes' ? 'checked="true"' : null ?> /> Verified</label>
								</div>
							</div>
						</div>
					</div>

					<div class="col-md-3">
						<div class="form-group">
							<label class="control-label">TAN No</label>
							<div class="input-group">
								<input type="text" class="form-control form-control-sm" name="tan_no" value="<?php echo $row['tan_no'] ?>" />
								<div class="input-group-addon">
									<label><input type="checkbox" name="tan_no_verified" value="1" <?php echo $row['tan_no_verified'] == 'Yes' ? 'checked="true"' : null ?> /> Verified</label>
								</div>
							</div>
						</div>
					</div>

					<div class="col-md-3">
						<div class="form-group">
							<label class="control-label">Service Tax No</label>
							<input type="text" class="form-control form-control-sm" name="service_tax_no" value="<?php echo $row['service_tax_no'] ?>" />
						</div>
					</div>

					<div class="col-md-3">
						<div class="form-group">
							<label class="control-label">GST No</label>
							<div class="form-group">
								<input type="text" class="form-control form-control-sm" name="gst_no" value="<?php echo $row['gst_no'] ?>" id="gst_no" />
							</div>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-3">
						<div class="form-group<?php echo (strlen(form_error('tin_no')) > 0 ? ' has-error' : '') ?>">
							<label class="control-label">TIN No</label>
							<input type="text" class="form-control form-control-sm" name="tin_no" value="<?php echo $row['tin_no'] ?>" />
						</div>
					</div>

					<div class="col-md-3">
						<div class="form-group<?php echo (strlen(form_error('cst_no')) > 0 ? ' has-error' : '') ?>">
							<label class="control-label">CST No</label>
							<input type="text" class="form-control form-control-sm" name="cst_no" value="<?php echo $row['cst_no'] ?>" />
						</div>
					</div>
				
					<div class="col-md-3">
						<div class="form-group<?php echo (strlen(form_error('excise_no')) > 0 ? ' has-error' : '') ?>">
							<label class="control-label">Excise No</label>
							<input type="text" class="form-control form-control-sm" name="excise_no" value="<?php echo $row['excise_no'] ?>" />
						</div>
					</div>

					<div class="col-md-3">
						<div class="form-group<?php echo (strlen(form_error('iec_no')) > 0 ? ' has-error' : '') ?>">
							<label class="control-label">Import Export Code</label>
							<input type="text" class="form-control form-control-sm" name="iec_no" value="<?php echo $row['iec_no'] ?>" />
						</div>
					</div>

					<!-- <div class="col-md-3">
						<div class="form-group">
							<label class="control-label">TDS Deductee Class</label>
							<?php echo form_dropdown('tds_class_id', array(0=>'')+getSelectOptions('tds_classes', 'id', 'name', 'WHERE type = "Deductee"'), $row['tds_class_id'], 'class="form-control form-control-sm"') ?>
						</div>
					</div> -->
				</div>
				
				<div class="form-group">
					<label class="control-label">Remarks</label>
					<div class="form-group<?php echo (strlen(form_error('remarks')) > 0 ? ' has-error' : '') ?>">
						<textarea class="form-control form-control-sm" name="remarks" rows="1"><?php echo $row['remarks'] ?></textarea>
					</div>
				</div>

				<div class="row">
					<div class="col-md-4">
						<div class="form-group">
							<label class="control-label">Username</label>
							<input type="text" class="form-control form-control-sm" name="username" value="<?php echo $row['username'] ?>" />
						</div>
					</div>

					<div class="col-md-4">
						<div class="form-group">
							<label class="control-label">Password</label>
							<input type="text" class="form-control form-control-sm" name="password" value="<?php echo $row['password'] ?>" />
						</div>
					</div>

					<div class="col-md-4">
						<div class="form-group">
							<label class="control-label">Active</label>
							<?php echo form_dropdown('active', getEnumSetOptions($this->_table, 'active'), $row['active'], 'class="form-control form-control-sm"');?>
						</div>
					</div>
				</div>

				<div class="card card-default">
					<div class="card-header">
						<h3 class="card-title">Address Details</h3>
					</div>
				
					<table class="table table-condensed table-striped DataEntry">
					<thead>
						<tr>
							<th width="20%">Branch Code</th>
							<th width="20%">Address 1</th>
							<th width="20%">Address 2</th>
							<th width="20%">GST No</th>
							<th width="20%">State</th>
							<th width="25%" class="aligncenter"><a class="CheckAll"><i class="icon-trashcan"></i></a></th>
						</tr>
					</thead>

					<tbody>
						<?php
						foreach($contacts as $r) {
							echo '<tr>
							<td><input type="text" class="form-control form-control-sm" name="person_name[' . $r['id'] . ']" value="' . $r['person_name'] . '" /></td>
							<td><textarea class="form-control form-control-sm" name="designation[' . $r['id'] . ']" value="' . $r['designation'] . '" ></textarea</td>
							<td><input type="text" class="form-control form-control-sm" name="mobile[' . $r['id'] . ']" value="' . $r['mobile'] . '" /></td>
							<td><textarea class="form-control form-control-sm Text ajaxEmail col-md-12" name="pc_email[' . $r['id'] . ']" value="' . $r['email'] . '" size=0" ></textarea></td>
							<td><input type="text" class="form-control form-control-sm" name="person_name[' . $r['id'] . ']" value="' . $r['person_name'] . '" /></td>
							<td class="aligncenter">' . form_checkbox(array('name' => 'delete_id['.$r['id'].']', 'value' => $r['id'], 'checked' => false, 'class' => 'DeleteCheckbox')) . '</td>
						</tr>';
						}
						?>

						<tr class="TemplateRow">
							<td><input type="text" class="form-control form-control-sm Focus" name="new_designation[]" value="" /></td>
							<td><textarea class="form-control form-control-sm Validate " rows="1" name="new_person_name[]" value="" size="10" ></textarea></td>
							<td><textarea class="form-control form-control-sm" rows="1" name="new_mobile[]" value="" size="10"></textarea></td>
							<td><input type="text" class="form-control form-control-sm Validate" name="new_email[]" value="" size="10" /></td>
							<td><input type="text" class="form-control form-control-sm Focus" name="new_designation[]" value="" /></td>
							<td><button type="submit" class="btn btn-success btn-sm AddButton"><i class="fa fa-plus"></i></button></div></td>
						</tr>
					</tbody>
					</table>
				</div>

				<div class="card card-default">
					<div class="card-header">
						<h3 class="card-title">Contacts</h3>
					</div>
				
					<table class="table table-condensed table-striped DataEntry">
					<thead>
						<tr>
							<th>Person Name</th>
							<th width="20%">Designation</th>
							<th width="20%">Mobile</th>
							<th>Email</th>
							<th width="24px" class="aligncenter"><a class="CheckAll"><i class="icon-trashcan"></i></a></th>
						</tr>
					</thead>

					<tbody>
						<?php
						foreach($contacts as $r) {
							echo '<tr>
							<td><input type="text" class="form-control form-control-sm" name="person_name[' . $r['id'] . ']" value="' . $r['person_name'] . '" /></td>
							<td><input type="text" class="form-control form-control-sm" name="designation[' . $r['id'] . ']" value="' . $r['designation'] . '" /></td>
							<td><input type="text" class="form-control form-control-sm" name="mobile[' . $r['id'] . ']" value="' . $r['mobile'] . '" /></td>
							<td><input type="text" class="form-control form-control-sm Text ajaxEmail col-md-12" name="pc_email[' . $r['id'] . ']" value="' . $r['email'] . '" size=0" /></td>
							<td class="aligncenter">' . form_checkbox(array('name' => 'delete_id['.$r['id'].']', 'value' => $r['id'], 'checked' => false, 'class' => 'DeleteCheckbox')) . '</td>
						</tr>';
						}
						?>

						<tr class="TemplateRow">
							<td><input type="text" class="form-control form-control-sm Focus" name="new_designation[]" value="" /></td>
							<td><input type="text" class="form-control form-control-sm Validate" name="new_person_name[]" value="" size="10" /></td>
							<td><input type="text" class="form-control form-control-sm" name="new_mobile[]" value="" size="10" /></td>
							<td><input type="text" class="form-control form-control-sm Validate" name="new_email[]" value="" size="10" /></td>
							<td><button type="submit" class="btn btn-success btn-sm AddButton"><i class="fa fa-plus"></i></button></div></td>
						</tr>
					</tbody>
					</table>
				</div>
			</div>

			<div class="col-md-4">
				<div class="card card-default">
					<div class="card-heading panel-tabs">
						<ul class="nav nav-tabs">
							<li class="active"><a href="#KYCDocument" data-toggle="tab">KYC Docs</a></li>
							<li><a href="#IECDetails" data-toggle="tab">DGFT IEC</a></li>
							<li><a href="#ImportRates" data-toggle="tab">Import Rates</a></li>
							<li><a href="#ExportRates" data-toggle="tab">Export Rates</a></li>
						</ul>
					</div>

					<div class="tab-content">
						<div class="tab-pane active" id="KYCDocument">
							<table class="table table-condensed table-striped">
							<tbody>
							<?php
								if (count($kyc_documents) == 0) {
									echo '<div class="alert alert-info">' . anchor('/accounting/ledger/edit/Party/'.$ledger_id, 'Select TDS Deductee Class in Party Ledger.', 'target="_blank"') . '</div>';
								}
								foreach($kyc_documents as $kyc) {
									echo '<tr>';
									if (strlen($kyc['file']) > 0)
										echo '<td>' . anchor($this->_clspath.'kyc/index/'.$kyc['party_id'].'/'.$kyc['id'], '<span class="green">' . $kyc['name'] . '</span>', 'class="Popup"') . '</td>';
									else
										echo '<td><span class="red">' . anchor('#modal-document', $kyc['name'], 'data-toggle="modal"') . '</span></td>';
									echo '</tr>';
								}
							?>
							</tbody>
							</table>
						</div>

						<div class="tab-pane" id="IECDetails">
							<table class="table table-condensed table-striped tiny">
							<tbody>
								<?php
								if ($iec_details) {
									foreach($iec_details as $field => $value) {
										if ($field == 'id' OR $field == 'party_id') continue;

										if ($field == 'party_name_address') {
											$address = stristr($value, "<BR />");
											echo '<tr>
											<td class="aligntop alignright nowrap muted">Party Name</td>
											<td>' . trim(substr($value, 0, (strlen($value) - strlen($address)))) . '</td>
										</tr>

										<tr>
											<td class="aligntop alignright nowrap muted">Address</td>
											<td>' . trim(str_replace('<BR />', ' ', $address)) . '</td>
										</tr>';
										}
										else {
											echo '<tr>
											<td class="aligntop alignright nowrap muted">' . humanize($field) . '</td>
											<td class="aligntop">' . $value . '</td>
										</tr>';
										}
									}
								}
							?>
							</tbody>
							</table>
						</div>

						<div class="tab-pane" id="ImportRates">
							<div class="list-group">
							<?php
							if ($row['id'] > 0) {
								foreach($bill_templates['Import'] as $bt) {
										echo anchor(
									$this->_clspath.'party_rate/index/'.$row['id'] . '/' . $bt['id'], 
									'<span class="pink">' . $bt['cargo_type'] . '</span> ' . $bt['indian_port'] . ' (<i>' . $bt['berth_no'] . '</i>) <span class="orange">' . $bt['product_name'] . '</span>', 
									'class="Popup tiny list-group-item' . ($bt['rate_exists'] ? ' MarkYellow' : '') . '"');
								}
							}
							?>
							</div>
						</div>
						
						<div class="tab-pane" id="ExportRates">
							<div class="list-group">
							<?php
							if ($row['id'] > 0) {
								foreach($bill_templates['Export'] as $bt) {
										echo anchor(
									$this->_clspath.'party_rate/index/'.$row['id'] . '/' . $bt['id'], 
									'<span class="pink">' . $bt['cargo_type'] . '</span> ' . $bt['indian_port'] . ' (<i>' . $bt['berth_no'] . '</i>) <span class="orange">' . $bt['product_name'] . '</span>', 
									'class="Popup tiny list-group-item' . ($bt['rate_exists'] ? ' MarkYellow' : '') . '"');
								}
							}
							?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="card-footer">
		<button type="submit" class="btn btn-success" id="Update">Update</button>
		<a href="#modal-delete" data-toggle="modal" class="btn btn-danger pull-right">Delete</a>
	</div>
</div>

</form>

<script>
function deleteDocument(id) {
	$("a#DeleteUrl").attr("href", '<?php echo base_url($this->_clspath.'kyc/detach/'.$party_id['party_id']) ?>/'+id);
	$("#modal-delete").modal();
}

$(document).ready(function() {
	$('.AddDocument').on('click', function() {
		var uid = $(this).attr('uid');
		$("#KycForm").attr('action', '<?php echo site_url($this->_clspath.'kyc/attach') ?>/'+uid);
		$("#modal-document").modal('show');
	});

});
</script>