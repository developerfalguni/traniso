<?php
echo form_open($this->_clspath.$this->_class.'/ajaxEdit', 'id="MainForm" enctype="multipart/form-data"');
echo form_hidden($id);
?>

<div class="card card-default">
	<div class="card-header">
		<h3 class="card-title"><span class=""><?php echo anchor($this->_clspath.$this->_class, '<i class="fa fa-list pr-1"></i>'. strtoupper($page_title)) ?></span></h3>
		<div class="card-tools w-50">
			<div class="form-group mb-0 row">
				<label class="col-md-3 col-form-label col-form-label-sm">Select Agent : </label>
				<div class="col-sm-9">
					<?php echo form_dropdown('agentList', [], '', 'class="form-control mb-0" id="agentSelect2" '); ?>
				</div>
			</div>
		</div>
	</div>
	<div class="card-body">

		<label class="control-label h5 pt-1 text-center" >Total Count: <?php echo $count ?></label>
			
		<div class="row">
			<div class="col-md-3">
				<div class="form-group">
					<label class="control-label">Agent Company Name</label>
					<input type="text" class="form-control form-control-sm Focus" name="name" value="" id="name">
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group">
					<label class="control-label">Address Line 1</label>
					<input type="text" class="form-control form-control-sm" name="address1" value="" id="address1">
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group">
					<label class="control-label">Address Line 2</label>
					<input type="text" class="form-control form-control-sm" name="address2" value="" id="address2">
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group">
					<label class="control-label">Phone No</label>
					<input type="text" class="form-control form-control-sm" name="phone_no" value="" id="phone_no" maxlength="10" onkeypress="return isNumber(event)">
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-3">
				<div class="form-group">
					<label class="control-label">City</label>
					<input type="text" class="form-control form-control-sm" name="city" value="" id="city">
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group">
					<label class="control-label">State</label>
					<input type="text" class="form-control form-control-sm" name="state_name" value="" id="state_name">
					<input type="hidden" name="state" title="id" value="" id="state">
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
		</div>
		<div class="row">
			<div class="col-md-6">
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label class="control-label">Person Name</label>
							<input type="text" class="form-control form-control-sm Focus" name="person_name" value="" id="person_name">
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label class="control-label">TAX ID</label>
							<input type="text" class="form-control form-control-sm" name="tax_id" value="" id="tax_id">
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label class="control-label">Mobile No</label>
							<input type="text" class="form-control form-control-sm" name="mobile_no" value="" id="mobile_no" maxlength="10" onkeypress="return isNumber(event)">
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label class="control-label">Website</label>
							<input type="text" class="form-control form-control-sm" name="website" value="" id="website">
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label class="control-label">Email ID</label>
							<input type="text" class="form-control form-control-sm" name="email_id1" value="" id="email_id1">
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label class="control-label">Email ID 2</label>
							<input type="text" class="form-control form-control-sm" name="email_id2" value="" id="email_id2">
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label class="control-label">WCA Members</label>
							<select class="form-control form-control-sm" name="wca_member" id="wca_member">
                                <option value='Yes'>Yes</option>
                                <option value='No'>No</option>
		                    </select>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label class="control-label">WCA ID Number</label>
							<input type="text" class="form-control form-control-sm" name="wca_id_number" value="" id="wca_id_number">
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label class="control-label">WCA Expiry Date</label>
							<div class="input-group input-group-sm">
								<input type="text" class="form-control DatePicker" name="wca_expiry_date" value="<?php echo date('d-m-Y') ?>" id="wca_expiry_date" />
								<div class="input-group-append">
									<div class="input-group-text"><i class="icon-calendar"></i></div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="row">
					<div class="col-md-12">
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
					<button type="button" class="btn btn-success" id="Add" onclick="updateAgent('0')"><i class="fa fa-plus"></i> Add</button>
					<button type="button" class="btn btn-info" id="Update"><i class="fa fa-save"></i> Update</button>
					<button class="btn btn-danger" id="Delete"><i class="fa fa-trash-alt"></i> Delete</button>
				</div>
			</div>
		</div>
	</div>
</div>
</form>

<script type="text/javascript" language="JavaScript">
	$(function() {
	    updateAgent('0');
	});
	$(document).ready(function() {


		$('#state_name').kaabar_autocomplete({source: '<?php echo base_url('master/state/ajax') ?>', alertText: 'State name'});
		$('#country_name').kaabar_autocomplete({source: '<?php echo base_url('master/country/ajax') ?>', alertText: 'Country name'});

		$('#agentSelect2').select2({
			ajax: { 
				url: '<?php echo site_url($this->_clspath.$this->_class) ?>/agentList/',
				type: "GET",
				dataType: 'json',
				delay: 250,
				data: function (params) {
					return {
						searchTerm: params.term // search term
					};
				},
				beforeSend: function(){
		            $("#loader").show();
		        },
				processResults: function (response) {
					$("#loader").hide();
					// $('#countNos').text('Total Count : '+response.count);	
			  		return {
			     		results: response.data
			  		};
				},
				cache: true
			},
			
	    }).on("change", function () {

	    	//console.log($(this).val())
	    	var agent_id = $(this).val();
	    	// $('#updateParty').html();
	    	updateAgent(agent_id);

	    });
	});

	function updateAgent(id = null)
    {   
    	if(id){
        	$.ajax({
                url: '<?php echo base_url($this->_clspath.$this->_class) ?>/getAgent/'+id,
                type: 'post',
                dataType: 'json',
                beforeSend: function(){
		            $("#loader").show();
		        },
                success:function(response) {
                	if(response.success == false){
                		$("#loader").hide();
                		$('#MainForm')[0].reset();
                		$("input[name=id]").attr('value', '0');
                		document.querySelectorAll('table tbody tr:not(.TemplateRow)').forEach((tr) => {
						    tr.remove();
						});
   						
					}
                	else
                	{
                		$('#MainForm')[0].reset();
	                	document.querySelectorAll('table tbody tr:not(.TemplateRow)').forEach((tr) => {
						    tr.remove();
						});

	                	$("input[name=id]").val(response.row.id);
	            		$("#name").val(response.row.name);
	            		$("#address1").val(response.row.address1);
	            		$("#address2").val(response.row.address2);
	            		$("#city").val(response.row.city);
	            		$("#pincode").val(response.row.pincode);

	            		$("#state").val(response.row.state);
	            		$("#state_name").val(response.row.state_name);
	            		$("#country").val(response.row.country);
	            		$("#country_name").val(response.row.country_name);
	            		
	            		$("#person_name").val(response.row.person_name);
	            		$("#tax_id").val(response.row.tax_id);
	            		$("#phone_no").val(response.row.phone_no);
	            		$("#mobile_no").val(response.row.mobile_no);
	            		$("#website").val(response.row.website);
	            		$("#email_id1").val(response.row.email_id1);
	            		$("#email_id2").val(response.row.email_id2);
	            		$("#wca_member").val(response.row.wca_member);
	            		$("#wca_id_number").val(response.row.wca_id_number);
	            		$("#wca_expiry_date").val(response.row.wca_expiry_date);
	            		$("#business_started_date").val(response.row.business_started_date);
	    				
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
		                		if(tblType === 'uploadTbl')
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
								        html:  ""+response.messages+"",
								        icon: "success",
								    });

								    updateAgent(id);
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
									    $("#loader").hide();
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