<?php 
echo form_open($this->_clspath.$this->_class.'/ajaxEdit', 'id="MainForm" enctype="multipart/form-data"');
echo form_hidden(array('id' => 0));
echo form_hidden(array('job_id' => 0));
?>

<div class="card card-default">
	<div class="card-header">
		<h3 class="card-title"><span class=""><?php echo anchor($this->_clspath.$this->_class, '<i class="fa fa-list pr-1"></i>'. strtoupper($page_title)) ?></span></h3>
		<div class="card-tools w-50">
			<div class="form-group mb-0 row">
				<?php echo form_dropdown('job_list', [0 =>'Select Job'], '', 'class="form-control mb-0" id="exportSelect2" '); ?>
				<input type="hidden" class="form-control form-control-sm" name="job_id" id="job_id" placeholder="Enter Shipper">
			</div>
			
		</div>
	</div>
	<div class="card-header text-center">
		<h6 class="bold mb-0">BILL OF LADING FOR OCEAN TRANSPORT OR MULTI-MODAL TRANSPORT</h6>
	</div>
	<div class="card-body p-1 d-none" id="Maincard">

		<div class="row">
			<div class="col-md-6 pr-0">
				<table class="table table-borderless">
					<tbody>
						<tr>
							<td colspan="2" class="w-100 p-1 bold border-right border-bottom text-left align-top">
								<div class="form-group">
									<label class="control-label">Shipper</label>
									<input type="text" class="form-control form-control-sm" name="s_name" id="s_name" placeholder="Enter Shipper">
								</div>
								<div class="form-group">
									<textarea rows="2" class="form-control form-control-sm" name="s_address" id="s_address" placeholder="Enter Shipper Address"></textarea>
								</div>
							</td>
						</tr>
						<tr>
							<td  colspan="2" class="w-100 p-1 bold border-right border-bottom text-left align-top">
								<div class="form-group">
									<label class="control-label">Consingee</label>
									<input type="text" class="form-control form-control-sm" name="c_name" id="c_name" placeholder="Enter Consingee">
								</div>
								<div class="form-group">
									<textarea rows="2" class="form-control form-control-sm" name="c_address" id="c_address"placeholder="Enter Consingee Address"></textarea>
								</div>
							</td>
						</tr>
						<tr>
							<td colspan="2" class="w-100 p-1 border-right border-bottom text-left align-top">
								<div class="form-group">
									<label class="control-label">Notify</label>
									<input type="text" class="form-control form-control-sm" name="n_name" id="n_name" placeholder="Enter Notify">
								</div>
								<div class="form-group">
									<textarea rows="2" class="form-control form-control-sm" name="n_address" id="n_address" placeholder="Enter Notify Address"></textarea>
								</div>
							</td>
						</tr>
						<tr>
							<td class="w-50 p-1 border-right border-bottom text-left align-top">
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label class="control-label">Vessel</label>
											<input type="text" class="form-control form-control-sm" name="vessel_name" id="vessel_name" placeholder="Enter Vessel">
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label class="control-label">Voyage</label>
											<input type="text" class="form-control form-control-sm" name="voyage" id="voyage" placeholder="Enter Voyage">
										</div>
									</div>
								</div>
							</td>
							<td class="w-50 p-1 border-right border-bottom text-left align-top">
								<div class="form-group">
									<label class="control-label">Booking No</label>
									<input type="text" class="form-control form-control-sm" name="booking_no" id="booking_no" placeholder="Enter Notify">
								</div>
							</td>
						</tr>
						<tr>
							<td class="p-1 border-right border-bottom text-left align-top">
								<div class="form-group">
									<label class="control-label">Place Of Receipt</label>
									<input type="text" class="form-control form-control-sm" name="receipt" id="receipt" placeholder="Enter Place of Receipt">
								</div>
							</td>
							<td class="p-1 border-right border-bottom text-left align-top">
								<div class="form-group">
									<label class="control-label">Place Of Loading</label>
									<input type="text" class="form-control form-control-sm" name="loading" id="loading" placeholder="Enter Place of Loading">
								</div>
							</td>
						</tr>
						<tr>
							<td class="p-1 border-right border-bottom text-left align-top">
								<div class="form-group">
									<label class="control-label">Place Of Discharge</label>
									<input type="text" class="form-control form-control-sm" name="discharge" id="discharge" placeholder="Enter Place of Discharge">
								</div>
							</td>
							<td class="p-1 border-right border-bottom text-left align-top">
								<div class="form-group">
									<label class="control-label">Place Of Delivery</label>
									<input type="text" class="form-control form-control-sm" name="delivery" id="delivery" placeholder="Enter Place of Delivery">
								</div>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="col-md-6 pl-0">
				<table class="table table-borderless">
					<tbody>
						<tr>
							<td colspan="2" class="p-1 bold align-top">
								<div class="form-group row text-right">
									<div class="col-sm-2"></div>
									<label for="inputEmail3" class="col-sm-4 col-form-label-sm">Enter BL / MTD No</label>
									<div class="col-sm-6">
										<input type="text" class="form-control form-control-sm" name="bl_no" id="bl_no" placeholder="Enter BL / MTD No">
									</div>
								</div>
								<div class="form-group row text-right">
									<div class="col-sm-2"></div>
									<label for="inputEmail3" class="col-sm-4 col-form-label-sm">BL Type</label>
									<div class="col-sm-6">
										<input type="text" class="form-control form-control-sm" name="bl_type" id="bl_type" placeholder="Enter BL Type">
									</div>
								</div>
							</td>
						</tr>
						<tr>
							<td colspan="2" class="p-1 border-bottom text-center align-top">
								<img src="<?php echo base_url('assets/dist/img/logo.png') ?>" width="300"><br>
								<p class="tiny bold">REG OFF.: GF2,Ground Floor, Ridhi Siddhi Arcade 1, Plot No 13, Sector 8, <br>Nr B M Pump, Gandhidham - Gujarat(370201) - INDIA <br> www.traniso.in | +91 9727626474 | manish@traniso.in <br>REG No.: MTO/DGS/2553/JAN/2-25</p>
							</td>
						</tr>
						<tr>
							<td colspan="2" class="p-1 border-bottom text-left align-top">
								<p class="tiny">Taken in charge in apparently good condition herein at the place of receipt for transport and delivery as mentionedabove, unless otherwise stated. The MTO in accordance with the provisions contained in the MTD undertakes to perform or to procure the performanceof the multimodaltransport from the place st which the goods are taken in charge, to the place designated for delivery and assumes responsibility for such transport.</p> 
							</td>
						</tr>
						<tr>
							<td colspan="2" class="p-1 border-bottom text-left align-top">
								<p class="tiny">One of the MTD(s) must be surrendered, duty endorsed in exchange for the goods. In witness where of the original MTD all of this tenure and date have been signed in the number indicated below one of which being accomplished the other(s) to be void.</p> 
							</td>
						</tr>
						<tr>
							<td colspan="2" class="p-1 border-bottom text-left align-top">
								<div class="form-group">
									<label class="control-label">Delivery Agent</label>
									<textarea class="form-control form-control-sm" rows="3" name="delivery_agent" id="delivery_agent" placeholder="Enter Delivery Agent"></textarea>
								</div>
							</td>
						</tr>
						<tr>
							<td class="p-1 border-right border-bottom text-left align-top">
								<div class="form-group">
									<label class="control-label">Total Gross Weight</label>
									<input type="text" class="form-control form-control-sm" name="gross_weight" id="gross_weight" placeholder="Enter Total Gross Weight" onkeypress="return isNumber(event)">
								</div>
							</td>
							<td class="p-1 border-bottom text-left align-top">
								<div class="form-group">
									<label class="control-label">No of Containers</label>
									<input type="text" class="form-control form-control-sm" name="container" id="container" placeholder="Enter No of Containers">
								</div>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="col-md-12">
				<!-- <table class="table min-vh-100" > -->
				<table class="table" id="TemplateRow">
					<thead>
						<tr>
							<th width="15%" class="p-1 text-left align-top">Container Nos</th>
							<th width="20%" class="p-1 text-left align-top">Marks and Numbers</th>
							<th width="50%" class="p-1 text-left align-top">Number of Packages, Kinds of packages, general <br>description of goods. ( said to contain )</th>
							<th width="15%" class="p-1 text-left align-top">Gross Weight Measurement</th>
						</tr>
					</thead>
					<tbody>
						<tr class="TemplateRow" id="PartyContact">
							<td class="p-1 text-left align-top">
								<input type="text" class="form-control form-control-sm" name="number[]" id="number" placeholder="Containers">
							</td>
							<td class="p-1 text-left align-top">
								<input type="text" class="form-control form-control-sm" name="mark_number[]" id="mark_number" placeholder="Marks and Numbers">
							</td>
							<td class="p-1 text-left align-top">
								<input type="text" class="form-control form-control-sm" name="description[]" id="description" placeholder="Descriptions">
							</td>
							<td class="p-1 text-left align-top">
								<input type="text" class="form-control form-control-sm" name="measurement[]" id="gross_measure" placeholder="Gross Weight">
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="col-md-12">
				<!-- <table class="table min-vh-100" > -->
				<table class="table table-borderless" >
					<tbody>
						<tr>
							<td class="p-1 border-right border-bottom border-top text-left align-top">
								<div class="form-group">
									<label class="control-label">Freight & Charges Amount</label>
									<input type="text" class="form-control form-control-sm" name="charges_amount" id="charges_amount" placeholder="Enter Freight & Charges Amount" onkeypress="return isNumber(event)">
								</div>
							</td>
							<td class="p-1 border-right border-bottom border-top text-left align-top">
								<div class="form-group">
									<label class="control-label">Freight Payable at</label>
									<div class="input-group input-group-sm">
										<input type="text" class="form-control form-control-sm DatePicker" name="payable_at" id="payable_at">
										<div class="input-group-append">
											<span class="input-group-text"><i class="icon-calendar"></i></span>
										</div>
									</div>
								</div>
							</td>
							<td class="p-1 border-right border-bottom border-top text-left align-top">
								<div class="form-group">
									<label class="control-label">No of Original MTD(s)</label>
									<input type="text" class="form-control form-control-sm" name="no_of_original" id="no_of_original" placeholder="Enter No of Original MTD">
								</div>
							</td>
							<td class="p-1 border-bottom border-top align-top">
								<div class="form-group">
									<label class="control-label">Place & Date of issue</label>
									<div class="input-group input-group-sm">
										<input type="text" class="form-control form-control-sm DatePicker" name="date_issue" id="date_issue">
										<div class="input-group-append">
											<span class="input-group-text"><i class="icon-calendar"></i></span>
										</div>
									</div>
								</div>
							</td>
						</tr>
						<tr>
							<td colspan="4" class="p-1 border-right border-bottom align-top">
								<div class="form-group">
									<label class="control-label">Other Perticulars ( if any )</label>
									<textarea class="form-control form-control-sm" rows="3" name="remarks" id="remarks" placeholder="Enter Other Perticulars"></textarea>
								</div>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<div class="card-footer">
		<button type="button" class="btn btn-success" id="Update"><i class="fa fa-save"></i> Update</button>
		<button class="btn btn-danger" id="Delete"><i class="fa fa-trash-alt"></i> Clear</button>
		<button type="button" class="btn btn-info" id="Print"><i class="fa fa-print"></i> Print</button>
	</div>
</div>

<script type="text/javascript">
	
	$(document).ready(function() {

		$('#exportSelect2').select2({
			ajax: { 
				url: '<?php echo site_url($this->_clspath.$this->_class) ?>/jobsList/',
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
			//placeholder: 'Search for a repository',
	  		//minimumInputLength: 1,
	    }).on("change", function () {
	    	//console.log($(this).val())
	    	var job_id = $(this).val();
	    	// $('#updateCostsheet').html('');
	    	updateHbl(job_id);
	    	//window.location.href = '<?php //echo site_url($this->_clspath.$this->_class) ?>/edit/'+job_id;
			// var str = $("#s2id_search_code .select2-choice span").text();
		 //    DOSelectAjaxProd(e.val, str);
		});

	});

	function updateHbl(id = null)
    {   
    	// console.log(id);
    	if(! id){
        	return false;
        }
        
        if(id){
        	$.ajax({
                url: '<?php echo site_url() ?>export/Hbl/get/'+id,
                type: 'post',
                dataType: 'json',
                beforeSend: function(){
		            $("#loader").show();
		        },
                success:function(response) {
                	console.log(response);
                	if(response.success == false){
                		$("#loader").hide();
                		$('#MainForm')[0].reset();
                		$("input[name=id]").attr('value', '0');
                		$("input[name=job_id]").attr('value', '0');
   			    	}
                	else
                	{

                		$('#MainForm')[0].reset();
	                	document.querySelectorAll('#TemplateRow tbody tr').forEach((tr) => {
						    tr.remove();
						});

						
						$("input[name=id]").val(response.job.id);
						$("input[name=job_id]").val(response.job.job_id);
						$("#s_name").val(response.job.s_name);
	            		$("#s_address").val(response.job.s_address);
	            		$("#c_name").val(response.job.c_name);
	            		$("#c_address").val(response.job.c_address);
	            		$("#n_name").val(response.job.n_name);
	            		$("#n_address").val(response.job.n_address);
	            		$("#bl_no").val(response.job.bl_no);
	            		$("#bl_type").val(response.job.bl_type);
	            		$("#vessel_name").val(response.job.vessel);
	            		$("#voyage").val(response.job.voyage);
	            		$("#booking_no").val(response.job.booking_no);
	            		$("#delivery_agent").val(response.job.delivery_agent);
	            		$("#receipt").val(response.job.receipt);
	            		$("#loading").val(response.job.loading);
	            		$("#discharge").val(response.job.discharge);
	            		$("#delivery").val(response.job.delivery);
	            		$("#gross_weight").val(response.job.gross_weight);
	            		$("#container").val(response.job.no_containers + '* 40');
	            		$("#charges_amount").val(response.job.charges_amount);
	            		$("#payable_at").val(response.job.payable_at);
	            		$("#no_of_original").val(response.job.no_of_original);
	            		$("#date_issue").val(response.job.date_issue);
	            		$("#remarks").val(response.job.remarks);
		            	
		            	var containers = '';
		     				if(response.containers.length){
						    	var result = JSON.parse(JSON.stringify(response.containers));

						    	for (var i = 0; i < result.length; i++) {
						    		
						    		containers += '<tr>';
										containers += '<td class="p-1 text-left align-top">';
											containers += '<input type="text" class="form-control form-control-sm" name="number['+result[i].id+']" value="'+result[i].number+'" placeholder="Containers">';
										containers += '</td>';
										containers += '<td class="p-1 text-left align-top">';
											containers += '<input type="text" class="form-control form-control-sm" name="mark_number['+result[i].id+']" value="" placeholder="Marks and Numbers">';
										containers += '</td>';
										containers += '<td class="p-1 text-left align-top">';
											containers += '<input type="text" class="form-control form-control-sm" name="description['+result[i].id+']" value="'+result[i].description+'" placeholder="Descriptions">';
										containers += '</td>';
										containers += '<td class="p-1 text-left align-top">';
											containers += '<input type="text" class="form-control form-control-sm" name="gross_measure['+result[i].id+']" value="'+'-'+'" placeholder="Gross Weight">';
										containers += '</td>';
									containers += '</tr>';
								}
							}
						// $('#TemplateRow tbody tr').remove();	
						
						if(response.containers.length !== 0){
							$('#TemplateRow tbody').append(containers);
								
						}
						else{
							
							$('#TemplateRow tbody').html('<tr><td colspan="4" class="text-center">No Container details Found...</td></tr>').css('color', 'red').css('font-weight','Bold');
						}
						$("#Maincard").removeClass("d-none");

						$("#loader").hide();	
					}

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
						  			$('#MainForm')[0].reset();
			                		$("input[name=id]").attr('value', '0');
			                		$("input[name=job_id]").attr('value', '0');
			                		$("#Maincard").addClass("d-none");
			                		$("#loader").hide();
						  			Swal.fire({
								        title: 'Wow...!',
								        html:  ""+response.messages+"",
								        icon: "success",
								    });
								    updateHbl(id);
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



