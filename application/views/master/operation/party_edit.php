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
// $site_link = '';
// foreach ($sites as $s)
// 	$site_link .= '<li>' .  anchor($this->_clspath.$this->_class.'/site/'.$s['party_id'].'/'.$s['id'],  $s['code']) . '</li>';
echo form_open($this->_clspath.$this->_class.'/ajaxEdit', 'id="MainForm"');
echo form_hidden($id);
?>

<div class="card card-default">
	<div class="card-header">
		<h3 class="card-title"><span class=""><?php echo anchor($this->_clspath.$this->_class, '<i class="fa fa-list pr-1"></i>'. strtoupper($page_title)) ?></span></h3>
		<div class="card-tools w-50">
			<div class="form-group mb-0 row">
				<label class="col-md-3 col-form-label col-form-label-sm">Select Customer : </label>
				<div class="col-sm-9">
					<?php echo form_dropdown('partyList', [], '', 'class="form-control mb-0" id="partySelect2" '); ?>
				</div>
			</div>
		</div>
	</div>
	
	<div class="card-body">
		<label class="control-label h5 pt-1 text-center" >Total Count: <?php echo $count ?></label>
		<div class="row">
			<div class="col-md-12">
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label class="control-label">Name</label>
							<div class="form-group">
								<input type="text" class="form-control form-control-sm Focus" name="name" value="" id="name" />
							</div>
						</div>
					</div>

					<div class="col-md-4">
						<div class="form-group">
							<label class="control-label">Traces PAN Name</label>
							<div class="form-group">
								<input type="text" class="form-control form-control-sm" name="traces_name" id="traces_name" value="" readonly="true" />
							</div>
						</div>
					</div>
				</div>
			
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label class="control-label">Address</label>
							<input type="text" class="form-control form-control-sm" name="main_address" value="" id="main_address"/>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<label class="control-label">City</label>
							<input type="text" class="form-control form-control-sm" name="city_id" id="ajaxCity" value="" />
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<label class="control-label">State</label>
							<input type="text" class="form-control form-control-sm" name="state_name" value="" id="state_name">
							<input type="hidden" name="state_id" title="id" value="" id="state_id">
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<label class="control-label">Pincode</label>
							<input type="text" class="form-control form-control-sm" name="pincode" value="" id="pincode">
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<label class="control-label">Country</label>
							<input type="text" class="form-control form-control-sm" name="country_name" value="" id="country_name">
							<input type="hidden" name="country" title="id" value="" id="country">
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<label class="control-label">Contact</label>
							<input type="text" class="form-control form-control-sm" name="contact" value="" id="contact" maxlength="10" onkeypress="return isNumber(event)"/>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<label class="control-label">Email</label>
							<input type="text" class="form-control form-control-sm" name="email" value="" id="email"/>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<label class="control-label">PAN No</label>
							<input type="text" class="form-control form-control-sm" name="pan_no" id="pan_no" value="" />
							<div class="input-group-addon d-none">
								<label><input type="checkbox" name="pan_no_verified" value="1" > Verified</label>
							</div>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<label class="control-label">TAN No</label>
							<input type="text" class="form-control form-control-sm" name="tan_no" id="tan_no" value="" />
							<div class="input-group-addon d-none">
								<label><input type="checkbox" name="tan_no_verified" value="1"> Verified</label>
							</div>
						</div>
					</div>

					<div class="col-md-3">
						<div class="form-group">
							<label class="control-label">GST No</label>
							<input type="text" class="form-control form-control-sm" name="gst_nos" value="" id="gst_nos" />
						</div>
					</div>

					<div class="col-md-3 d-none">
						<div class="form-group">
							<label class="control-label">TIN No</label>
							<input type="text" class="form-control form-control-sm" name="tin_no" id="tin_no" value="" />
						</div>
					</div>
				</div>

				<div class="row">
					
					<div class="col-md-3 d-none">
						<div class="form-group">
							<label class="control-label">Excise No</label>
							<input type="text" class="form-control form-control-sm" name="excise_no" id="excise_no" value="" />
						</div>
					</div>

					<div class="col-md-3">
						<div class="form-group">
							<label class="control-label">Import Export Code</label>
							<input type="text" class="form-control form-control-sm" name="iec_no" id="iec_no" value="" />
						</div>
					</div>

					<div class="col-md-3">
						<div class="form-group">
							<label class="control-label">Type of Customer</label>
							<input type="text" class="form-control form-control-sm" name="customer_type" id="customer_type" value="" />
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<label class="control-label">Commodity(Major)</label>
							<div class="form-group">
								<input type="text" class="form-control form-control-sm" name="commodity" id="commodity" value="" />
							</div>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<label class="control-label">Remarks \ Refference</label>
							<div class="form-group">
								<textarea class="form-control form-control-sm" name="remarks" id="remarks" rows="1"></textarea>
							</div>
						</div>
					</div>

					<!-- <div class="col-md-3">
						<div class="form-group">
							<label class="control-label">TDS Deductee Class</label>
							<?php //echo form_dropdown('tds_class_id', array(0=>'')+getSelectOptions('tds_classes', 'id', 'name', 'WHERE type = "Deductee"'), $row['tds_class_id'], 'class="form-control form-control-sm"') ?>
						</div>
					</div> -->
				</div>
				
				<div class="row">
					<div class="col-md-4 d-none">
						<div class="form-group">
							<label class="control-label">Username</label>
							<input type="text" class="form-control form-control-sm" name="username" id="username" value="" />
						</div>
					</div>

					<div class="col-md-4">
						<div class="form-group d-none">
							<label class="control-label">Password</label>
							<input type="text" class="form-control form-control-sm" name="password" value="" />
						</div>
					</div>

					<div class="col-md-4">
						<div class="form-group d-none">
							<label class="control-label">Active</label>
							<?php echo form_dropdown('active', getEnumSetOptions($this->_table, 'active'), '', 'class="form-control form-control-sm"');?>
						</div>
					</div>
				</div>

				<div class="card card-default">
					<div class="card-header">
						<h3 class="card-title">Address Details</h3>
					</div>
				
					<table class="table table-condensed table-striped DataEntry" id="branchTbl">
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
						foreach($addresses as $r) {
							echo '<tr>
							<td><input type="text" class="form-control form-control-sm" name="branch_code[' . $r['id'] . ']" value="' . $r['branch_code'] . '" /></td>
							<td><textarea class="form-control form-control-sm" name="address1[' . $r['id'] . ']" rows="1">'.$r["address1"].'</textarea></td>
							<td><textarea class="form-control form-control-sm Text ajaxEmail col-md-12" name="address2[' . $r['id'] . ']" rows="1" size="0" >'.$r["address2"].'</textarea></td>
							<td><input type="text" class="form-control form-control-sm" name="gst_no[' . $r['id'] . ']" value="' . $r['gst_no'] . '" /></td>							
							<td><input type="text" class="form-control form-control-sm" name="state[' . $r['id'] . ']" value="' . $r['state'] . '" /></td>
							<td class="aligncenter">' . form_checkbox(array('name' => 'delete_id['.$r['id'].']', 'value' => $r['id'], 'checked' => false, 'class' => 'DeleteCheckbox')) . '</td>
						</tr>';
						}
						?>

						<tr class="TemplateRow" id="PartyAddress">
							<td><input type="text" class="form-control form-control-sm Focus" name="new_branch_code[]" value="" /></td>
							<td><textarea class="form-control form-control-sm Validate " rows="1" name="new_address1[]" value="" size="10" ></textarea></td>
							<td><textarea class="form-control form-control-sm" rows="1" name="new_address2[]" value="" size="10"></textarea></td>
							<td><input type="text" class="form-control form-control-sm Validate" name="new_gst_no[]" value="" size="10" /></td>
							<td><input type="text" class="form-control form-control-sm Focus" name="new_state[]" value="" /></td>
							<td><button type="submit" class="btn btn-success btn-sm AddButton"><i class="fa fa-plus"></i></button></div></td>
						</tr>


					</tbody>
					</table>
				</div>

				<div class="card card-default">
					<div class="card-header">
						<h3 class="card-title">Contacts</h3>
					</div>
				
					<table class="table table-condensed table-striped DataEntry"  id="contactTbl">
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
							<td><input type="text" class="form-control form-control-sm" name="mobile[' . $r['id'] . ']" value="' . $r['mobile'] . '" maxlength="10" onkeypress="return isNumber(event)"/></td>
							<td><input type="text" class="form-control form-control-sm Text ajaxEmail col-md-12" name="con_email[' . $r['id'] . ']" value="' . $r['email'] . '" size=0" /></td>
							<td class="aligncenter">' . form_checkbox(array('name' => 'delete_id['.$r['id'].']', 'value' => $r['id'], 'checked' => false, 'class' => 'DeleteCheckbox')) . '</td>
						</tr>';
						}
						?>

						<tr class="TemplateRow" id="PartyContact">
							<td><input type="text" class="form-control form-control-sm Focus" name="new_designation[]" value="" /></td>
							<td><input type="text" class="form-control form-control-sm Validate" name="new_person_name[]" value="" size="10" /></td>
							<td><input type="text" class="form-control form-control-sm" name="new_mobile[]" value="" maxlength="10" onkeypress="return isNumber(event)"/></td>
							<td><input type="text" class="form-control form-control-sm Validate" name="new_con_email[]" value="" size="10" /></td>
							<td><button type="submit" class="btn btn-success btn-sm AddButton"><i class="fa fa-plus"></i></button></div></td>
						</tr>
					</tbody>
					</table>
				</div>


				<div class="card card-primary">
					<div class="card-header">
						<i class="fa fa-upload"></i> Documents Upload
					</div>
					<div class="card-body p-0">
						<div class="table-responsive table-sm">
							<table class="table table-bordered DataEntry" id="uploadTbl">
								<thead>
									<tr>
										<td class="">Doc Name</td>
										<td class="">Upload <span class="text-danger">(allowed : jpg, jpeg, pdf, zip, gif, png)</span></td>
										<td class="text-center">Action </td>
									</tr>
								</thead>
								<tbody>
									<tr class="TemplateRow" id="UploadDoc">
										<td><input type="text" class="form-control form-control-sm" name="userfilename[]" ></td>
										<td><input type="file" name="userfile[]"></td>
										<td class="text-center">
											<button type="button" class="btn btn-success btn-sm AddButton"><i class="fa fa-plus"></i></button>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				
				
			</div>

		</div>
	</div>

	<div class="card-footer">
		<div class="row">
			<div class="col-lg-3">
				<label class="text-danger">Business Started Date</label>
				<div class="input-group input-group-sm">
					<input type="text" class="form-control form-control-sm DatePicker" name="business_started_date" value="<?php echo date('d-m-Y') ?>" id="business_started_date">
					<div class="input-group-append">
						<span class="input-group-text"><i class="icon-calendar"></i></span>
					</div>
				</div>
			</div>
			<div class="col-lg-9">
				<div class="float-right pt-2">
					<button type="button" class="btn btn-success" id="Add" onclick="updateParty('0')"><i class="fa fa-plus"></i> Add</button>
					<button type="button" class="btn btn-info" id="Update"><i class="fa fa-save"></i> Update</button>
					<button class="btn btn-danger" id="Delete"><i class="fa fa-trash-alt"></i> Delete</button>
				</div>
			</div>
		</div>
	</div>
</div>

</form>

<script>
	$(document).ready(function() {
		$('#ajaxCity').kaabar_autocomplete_full({source: '<?php echo site_url($this->_clspath.$this->_class.'/json/parties/id/city_id') ?>'});
		$('#state_name').kaabar_autocomplete({source: '<?php echo base_url('master/state/ajax') ?>', alertText: 'State name'});
		$('#country_name').kaabar_autocomplete({source: '<?php echo base_url('master/country/ajax') ?>', alertText: 'Country name'});
	});
	
</script>

<script type="text/javascript" language="JavaScript">
	$(function() {
	    updateParty('0');
	});
	$(document).ready(function() {
		$('#partySelect2').select2({
			ajax: { 
				url: '<?php echo site_url($this->_clspath.$this->_class) ?>/partyList/',
				type: "GET",
				dataType: 'json',
				delay: 250,
				beforeSend: function(){
		            $("#loader").show();
		        },
				processResults: function (response) {
					$("#loader").hide();
			  		return {
			     		results: response
			  		};
				},
				cache: true
			},
			
	    }).on("change", function () {

	    	//console.log($(this).val())
	    	var party_id = $(this).val();
	    	// $('#updateParty').html();
	    	updateParty(party_id);

	    });
	});

	function updateParty(id = null)
    {   
    	if(id){
        	$.ajax({
                url: '<?php echo base_url($this->_clspath.$this->_class) ?>/getParty/'+id,
                type: 'post',
                dataType: 'json',
                beforeSend: function(){
		            $("#loader").show();
		        },
                success:function(response) {

                	if(response.success == false){

                		$('#MainForm')[0].reset();
                		$("input[name=id]").attr('value', '0');
   						document.querySelectorAll('table tbody tr:not(.TemplateRow)').forEach((tr) => {
						    tr.remove();
						});
						$("#loader").hide();
					}
                	else
                	{

                		$('#MainForm')[0].reset();
	                	document.querySelectorAll('table tbody tr:not(.TemplateRow)').forEach((tr) => {
						    tr.remove();
						});

					
						$("input[name=id]").val(response.party.id);
	            		$("#name").val(response.party.name);
	            		$("#traces_name").val(response.party.traces_name);
	            		$("#main_address").val(response.party.address);
	            		$("#ajaxCity").val(response.party.city_id);

	            		$("#pincode").val(response.party.pincode);

	            		$("#state_id").val(response.party.state);
	            		$("#state_name").val(response.party.state_name);
	            		$("#country").val(response.party.country);
	            		$("#country_name").val(response.party.country_name);

	            		$("#contact").val(response.party.contact);
	            		$("#fax").val(response.party.fax);
	            		$("#email").val(response.party.email);
	            		$("#pan_no").val(response.party.pan_no);
	            		$("#pan_no_verified").val(response.party.pan_no_verified);
	            		$("#tan_no").val(response.party.tan_no);
	            		$("#tan_no_verified").val(response.party.tan_no_verified);
	            		$("#service_tax_no").val(response.party.service_tax_no);
	            		$("#gst_nos").val(response.party.gst_nos);
	            		$("#tin_no").val(response.party.tin_no);
	    				$("#cst_no").val(response.party.cst_no);
	            		$("#excise_no").val(response.party.excise_no);
	            		$("#customer_type").val(response.party.customer_type);
	            		$("#commodity").val(response.party.commodity);
	            		$("#business_started_date").val(response.party.business_started_date);
	            		$("#iec_no").val(response.party.iec_no);
	            		$("#remarks").val(response.party.remarks);
	            		$("#username").val(response.party.username);
	            		$("#active").val(response.party.active);
	            		//$("#File").val(response.file.active);

	            		var partyAddress = '';
	     				if(response.parties_add.length){
					    	var result = JSON.parse(JSON.stringify(response.parties_add));

					    	for (var i = 0; i < result.length; i++) {
						        partyAddress += '<tr>';
									partyAddress += '<td>';
										partyAddress += '<input type="hidden" value="'+result[i].id+'" ><input type="text" class="form-control form-control-sm Focus" name="branch_code['+result[i].id+']" value="'+result[i].branch_code+'">';
									partyAddress += '</td>';
									partyAddress += '<td>';
										partyAddress += '<textarea class="form-control form-control-sm Validate " rows="1" name="address1['+result[i].id+']" value="" size="10">'+result[i].address1+'</textarea>';
									partyAddress += '</td>';
									partyAddress += '<td>';
										partyAddress += '<textarea class="form-control form-control-sm Validate " rows="1" name="address2['+result[i].id+']" value="" size="10">'+result[i].address2+'</textarea>';
									partyAddress += '</td>';
									partyAddress += '<td>';
										partyAddress += '<input type="text" class="form-control form-control-sm Validate" name="gst_no['+result[i].id+']" value="'+result[i].gst_no+'" size="10">';
									partyAddress += '</td>';
									partyAddress += '<td>';
										partyAddress += '<input type="text" class="form-control form-control-sm Focus" name="state['+result[i].id+']" value="'+result[i].state+'">';
									partyAddress += '</td>';								
									partyAddress += '<td>';
										partyAddress += '<button type="button" class="btn btn-danger btn-sm DelButton1"><i class="icon-minus fa fa-minus"></i></button>';
									partyAddress += '</td>';
								partyAddress += '</tr>';
							}
						}
						$('#PartyAddress:last-child').before(partyAddress);	
										
						var partyContact = '';
	     				if(response.parties_con.length){
					    	var result = JSON.parse(JSON.stringify(response.parties_con));

					    	for (var i = 0; i < result.length; i++) {
						        partyContact += '<tr>';
									partyContact += '<td>';
										partyContact += '<input type="hidden" value="'+result[i].id+'" ><input type="text" class="form-control form-control-sm Validate" name="person_name['+result[i].id+']" value="'+result[i].person_name+'" size="10">';
									partyContact += '</td>';
									partyContact += '<td>';
										partyContact += '<input type="text" class="form-control form-control-sm Focus" name="designation['+result[i].id+']" value="'+result[i].designation+'">';
									partyContact += '</td>';
									partyContact += '<td>';
										partyContact += '<input type="text" class="form-control form-control-sm" name="mobile['+result[i].id+']" value="'+result[i].mobile+'" maxlength="10" onkeypress="return isNumber(event)">';
									partyContact += '</td>';
									partyContact += '<td>';
										partyContact += '<input type="text" class="form-control form-control-sm Validate" name="con_email['+result[i].id+']" value="'+result[i].email+'" size="10">';
									partyContact += '</td>';
									partyContact += '<td>';
										partyContact += '<button type="button" class="btn btn-danger btn-sm DelButton1"><i class="icon-minus fa fa-minus"></i></button>';
									partyContact += '</td>';
								partyContact += '</tr>';
							}
						}
						$('#PartyContact:last-child').before(partyContact);	

						var uploadDoc = '';
	     				if(response.files.length){
					    	var result = JSON.parse(JSON.stringify(response.files));

					    	for (var i = 0; i < result.length; i++) {
						        uploadDoc += '<tr>';
									uploadDoc += '<td>';
										uploadDoc += '<input type="hidden" name="userfileid['+result[i].id+']" value="'+result[i].id+'" >';
										uploadDoc += '<input type="text" class="form-control form-control-sm" name="userfilename['+result[i].id+']" value="'+result[i].doc_name+'" >';
									uploadDoc += '</td>';
									uploadDoc += '<td>';
										uploadDoc += '<input type="file" class="d-none" name="userfile['+result[i].id+']">';
										uploadDoc += '<a href="'+result[i].filepath+'" download><i class="fa fa-download fa-lg text-success"></i> '+result[i].filename+'</a>';
									uploadDoc += '</td>';
									uploadDoc += '<td class="text-center">';
										uploadDoc += '<button type="button" class="btn btn-danger btn-sm DelButton1"><i class="icon-minus fa fa-minus"></i></button>';
									uploadDoc += '</td>';
								uploadDoc += '</tr>';
							}
						}
						$('#UploadDoc:last-child').before(uploadDoc);

						$("#loader").hide();
					}

					$('#state_name').kaabar_autocomplete({source: '<?php echo base_url('master/state/ajax') ?>', alertText: 'State name'});
					$('#country_name').kaabar_autocomplete({source: '<?php echo base_url('master/country/ajax') ?>', alertText: 'Country name'});

				    $('table.DataEntry').on('click', 'button.DelButton1', function() {
						const row = $(this).parents('tr');
						console.log(row)
						const that = this // here a is change
			            var output = true;

			            var tblType = $(this).parents('tr').parents('table').attr('id');

			            Swal.fire({
		                	title: "Are you sure?",
							text: "You won't be able to revert this!",
							icon: "warning",
							showCancelButton: true,
							confirmButtonColor: "#3085d6",
							cancelButtonColor: "#d33",
							confirmButtonText: "Yes, Delete it...!",
							cancelButtonText: "Cancel",
							buttonsStyling: true
		                }).then(function(result) {

		                	if (result.dismiss === Swal.DismissReason.cancel) {
		                		output = false;
		                        return false;
		                	}
		                	else
		                	{
		                		var rowId = row.find('input[type=hidden]').val();
		                		if(tblType === 'branchTbl')
		                			var deleteurl = 'party_addresses/'+id+'/'+rowId;
		                		else if(tblType === 'contactTbl')
		                			var deleteurl = 'party_contacts/'+id+'/'+rowId;
		                		else if(tblType === 'uploadTbl')
		                			var deleteurl = 'attachments/'+id+'/'+rowId;

	                			$.ajax({
					                url: '<?php echo base_url($this->_clspath.$this->_class) ?>/delDetails/'+deleteurl,
					                type: 'post',
					                dataType: 'json',
					                beforeSend: function(){
							            $("#loader").show();
							        },
					                success:function(response) {
					                	if(response.success == true){
					                		row.remove();
					                		$("#loader").hide();
					                		Swal.fire({
										        title: 'Wow...!',
										        html:  ""+response.messages+"",
										        icon: "success",
										    });

					                	}
					                	else
					                	{
					                		$("#loader").hide();
					                		Swal.fire({
										        title: 'Opps...!',
										        html:  ""+response.messages+"",
										        icon: "error",
										    });
					                	}
					                }
					            });
								
								
							}
		                });
			        });

			        $('#MainForm').unbind('submit').bind('submit', function(e) {

			        	e.preventDefault();
    					
                    	var formData = new FormData(this);
                    	// remove the text-danger
						$(".text-danger").remove();

						$.ajax({
						  	url: $(this).attr('action'),
						  	type: $(this).attr('method'),
						  	data: formData, // /converting the form data into array and sending it to server
						  	dataType: 'json',
						  	contentType: false,
    						processData: false,
    						beforeSend: function(){
    							$("#loader").show();
    						},
						  	success:function(response) {

						  		if(response.success === true) {
						  			$("#loader").hide();
						  			Swal.fire({
								        title: 'Wow...!',
								        html:  ""+response.messages+"",
								        icon: "success",
								    });

						  			updateParty(id);
								} 
								else 
								{

								  if(response.messages instanceof Object) {
								    $.each(response.messages, function(index, value) {
								      var id = $("#"+index);

								      id.removeClass('is-invalid')
								      .removeClass('is-valid')
								      .addClass(value.length > 0 ? 'is-invalid' : 'is-valid');
								      
								      id.after(value);

								    });
								    //$("#loader").hide();
								  } 
								  else 
								  {
								  	//$("#loader").hide();
								  	Swal.fire({
								        title: 'Opps...!',
								        html:  ""+response.messages+"",
								        icon: "error",
								    });
								  }
								}
								return false;
							}
						}); 
						return false;
					});

									}
            });
        }
    }
</script>